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
        
        /* Spring Physics Card Hover */
        .clean-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.34, 1.4, 0.64, 1);
        }
        .clean-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 28px -5px rgba(0, 0, 0, 0.06);
        }

        /* Document Card Interactive Hover */
        .doc-card {
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .doc-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -4px rgba(249, 115, 22, 0.12);
        }
        .doc-card:hover .icon-box {
            transform: scale(1.15) rotate(6deg);
        }

        /* Preset Chips Hover Animation */
        .preset-chip {
            transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .preset-chip:hover {
            transform: translateY(-2px) scale(1.05);
            background-color: #ffedd5;
            color: #c2410c;
            border-color: #fdba74;
            box-shadow: 0 4px 10px -2px rgba(249, 115, 22, 0.2);
        }
        .preset-chip:active {
            transform: scale(0.95);
        }

        /* Action Buttons Hover Animation */
        .btn-action-animated {
            transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .btn-action-animated:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 8px 18px -2px rgba(0, 0, 0, 0.15);
        }
        .btn-action-animated:active {
            transform: scale(0.96);
        }

        /* Toast notification */
        #toastNotice {
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        #toastNotice.show {
            transform: translateY(0);
            opacity: 1;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col selection:bg-orange-600 selection:text-white">

    <!-- Header Navbar Partial -->
    <?php $this->load->view('partials/app_navbar', [
        'user_role_label'   => 'Layanan Akademik (LAA)',
        'user_display_name' => 'Admin Layanan FIK',
        'user_display_sub'  => 'Verifikasi Dokumen TA'
    ]); ?>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full space-y-6">

        <!-- Flash Messages -->
        <?php if($this->session->flashdata('error')): ?>
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl shadow-xs flex items-center justify-between text-xs animate-bounce">
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
                    <div class="w-14 h-14 rounded-2xl bg-orange-100 border border-orange-200 text-orange-600 font-extrabold text-xl flex items-center justify-center flex-shrink-0 shadow-xs">
                        <?= strtoupper(substr($detail['nama_depan'], 0, 1)); ?>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                                <?= htmlspecialchars($detail['nama_depan'] . ' ' . $detail['nama_belakang']); ?>
                            </h1>
                            <button type="button" onclick="copyNIM('<?= $detail['nim']; ?>')" class="px-2.5 py-0.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-md text-[11px] font-mono font-bold border border-slate-200 flex items-center gap-1.5 transition-colors" title="Klik untuk salin NIM">
                                <span><?= htmlspecialchars($detail['nim']); ?></span>
                                <i class="bi bi-clipboard text-slate-400 text-[10px]"></i>
                            </button>
                        </div>
                        <div class="text-xs text-slate-500 flex flex-wrap items-center gap-x-4 gap-y-1">
                            <span><i class="bi bi-mortarboard text-slate-400 mr-1"></i> <?= htmlspecialchars($detail['prodi'] ?? 'DKV'); ?></span>
                            <span><i class="bi bi-envelope text-slate-400 mr-1"></i> <?= htmlspecialchars($detail['email'] ?? '-'); ?></span>
                            <span><i class="bi bi-telephone text-slate-400 mr-1"></i> <?= htmlspecialchars($detail['no_hp'] ?? '-'); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Right Status Pill -->
                <div class="flex flex-col sm:items-end gap-1.5">
                    <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Status Saat Ini di LAA:</span>
                    <?php if($detail['status_approval_admin'] === 'Approved'): ?>
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl text-xs font-bold shadow-xs">
                            <i class="bi bi-check-circle-fill text-emerald-600"></i> Berkas Disetujui (Approved)
                        </span>
                    <?php elseif($detail['status_approval_admin'] === 'Rejected'): ?>
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-rose-50 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold shadow-xs">
                            <i class="bi bi-arrow-return-left text-rose-600"></i> Dikembalikan untuk Revisi
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-amber-50 text-amber-700 border border-amber-200 rounded-xl text-xs font-bold shadow-xs">
                            <i class="bi bi-clock text-amber-600"></i> Menunggu Verifikasi Berkas
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Kelompok Keahlian & Judul Section -->
            <div class="mt-6 pt-5 border-t border-slate-100 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block mb-1">Kelompok Keahlian (KK)</span>
                    <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        <i class="bi bi-diagram-3 text-orange-600"></i>
                        <?= htmlspecialchars($detail['nama_kk'] ?? 'Visual Communication & Multimedia'); ?>
                    </span>
                </div>
                <div class="md:col-span-2 bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block mb-1">Judul Rencana Tugas Akhir</span>
                    <p class="text-xs font-semibold text-slate-800 leading-relaxed"><?= htmlspecialchars($detail['judul_1']); ?></p>
                </div>
            </div>
        </div>

        <!-- Document Verification Form -->
        <form method="POST" action="<?= site_url('adminlayanan/submit_verifikasi/' . $detail['nim']); ?>" id="formVerifikasi">
            
            <div class="space-y-6">

                <!-- 4 Document Cards Grid -->
                <div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                                <i class="bi bi-files text-orange-600"></i> Pemeriksaan 4 Dokumen Persyaratan
                            </h2>
                            <span class="text-xs text-slate-500">Periksa dan validasi dokumen pendaftaran mahasiswa secara teliti.</span>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <!-- View Layout Mode Toggle (1-Column List vs 2x2 Grid) -->
                            <div class="flex items-center bg-slate-200/80 p-0.5 rounded-xl border border-slate-300">
                                <button type="button" onclick="setDocLayout('grid')" id="btnLayoutGrid" 
                                        class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all bg-white text-slate-900 shadow-xs flex items-center gap-1 cursor-pointer" title="Tampilan Grid 2x2">
                                    <i class="bi bi-grid-fill"></i> <span class="hidden sm:inline">2x2 Grid</span>
                                </button>
                                <button type="button" onclick="setDocLayout('list')" id="btnLayoutList" 
                                        class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all text-slate-500 hover:text-slate-900 flex items-center gap-1 cursor-pointer" title="Tampilan List 1 Kolom (Horizontal)">
                                    <i class="bi bi-view-list"></i> <span class="hidden sm:inline">1 Kolom</span>
                                </button>
                            </div>

                            <button type="button" onclick="markAllValid()" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-xl text-xs font-bold shadow-xs flex items-center gap-1.5 transition-all active:scale-95 cursor-pointer">
                                <i class="bi bi-check2-all"></i> Tandai Semua Valid
                            </button>
                        </div>
                    </div>

                    <?php
                        $resolve_pdf_url = function($filename) {
                            if (empty($filename)) {
                                return base_url('uploads/persyaratan_ta/Sertifikat_Massal_2026-07-07_(2).pdf');
                            }
                            if (strpos($filename, 'uploads/') === 0 && file_exists(FCPATH . $filename)) {
                                return base_url($filename);
                            }
                            $sub_path = 'uploads/persyaratan_ta/' . $filename;
                            if (file_exists(FCPATH . $sub_path)) {
                                return base_url($sub_path);
                            }
                            return base_url('uploads/persyaratan_ta/Sertifikat_Massal_2026-07-07_(2).pdf');
                        };

                        $berkas_items = array();
                        if (!empty($syarat_berkas)) {
                            $idx_b = 1;
                            foreach ($syarat_berkas as $sb) {
                                $kode = $sb['kode_berkas'];
                                $st_map = $student_berkas[$kode] ?? null;
                                $file_val = $st_map['file_name'] ?? ($detail['file_' . $kode] ?? '');
                                $status_val = $st_map['status_verifikasi'] ?? ($detail['status_' . $kode] ?? 'Pending');

                                $berkas_items[] = array(
                                    'key'       => $kode,
                                    'label'     => $idx_b . '. ' . $sb['nama_berkas'],
                                    'desc'      => $sb['deskripsi'] ?: 'Berkas persyaratan ' . $sb['nama_berkas'],
                                    'file'      => $file_val ?: ($kode . '_' . $detail['nim'] . '.pdf'),
                                    'status'    => $status_val,
                                    'icon'      => 'bi-file-earmark-pdf',
                                    'presets'   => array('File Buram / Tidak Jelas', 'Format File Tidak Sesuai', 'Berkas Tidak Lengkap')
                                );
                                $idx_b++;
                            }
                        } else {
                            $berkas_items = array(
                                array(
                                    'key'       => 'ksm',
                                    'label'     => '1. KSM (Kartu Studi Mahasiswa)',
                                    'desc'      => 'Bukti KRS semester aktif yang memuat mata kuliah Tugas Akhir.',
                                    'file'      => $detail['file_ksm'] ?? 'ksm_' . $detail['nim'] . '.pdf',
                                    'status'    => $detail['status_ksm'] ?? 'Pending',
                                    'icon'      => 'bi-card-checklist',
                                    'presets'   => array('Tanpa TTD Dosen Wali', 'Mata Kuliah TA Belum Ada', 'File Buram / Tidak Jelas')
                                ),
                                array(
                                    'key'       => 'transkrip',
                                    'label'     => '2. Transkrip Nilai Akademik Terakhir',
                                    'desc'      => 'Transkrip nilai resmi yang sudah divalidasi dan memenuhi syarat SKS kelulusan.',
                                    'file'      => $detail['file_transkrip'] ?? 'transkrip_' . $detail['nim'] . '.pdf',
                                    'status'    => $detail['status_transkrip'] ?? 'Pending',
                                    'icon'      => 'bi-file-earmark-spreadsheet',
                                    'presets'   => array('Belum Update Semester Terbaru', 'SKS Kelulusan Kurang', 'Belum Tervalidasi Resmi')
                                ),
                                array(
                                    'key'       => 'pernyataan',
                                    'label'     => '3. Surat Pernyataan Mahasiswa',
                                    'desc'      => 'Surat kesanggupan menyelesaikan TA bermaterai dan ditandatangani.',
                                    'file'      => $detail['file_pernyataan'] ?? 'pernyataan_' . $detail['nim'] . '.pdf',
                                    'status'    => $detail['status_pernyataan'] ?? 'Pending',
                                    'icon'      => 'bi-file-earmark-ruled',
                                    'presets'   => array('Tanpa Materai Rp 10.000', 'Belum Ditandatangani', 'Format Surat Salah')
                                ),
                                array(
                                    'key'       => 'bebas_lab',
                                    'label'     => '4. Surat Bebas Laboratorium & Perpustakaan',
                                    'desc'      => 'Surat keterangan bebas pinjaman alat lab FIK dan buku perpustakaan.',
                                    'file'      => $detail['file_bebas_lab'] ?? 'bebas_lab_' . $detail['nim'] . '.pdf',
                                    'status'    => $detail['status_bebas_lab'] ?? 'Pending',
                                    'icon'      => 'bi-building-check',
                                    'presets'   => array('Tanpa Stempel Resmi Lab', 'Pinjaman Alat Lab Belum Lunas', 'Buku Perpus Belum Kembali')
                                )
                            );
                        }
                        $raw_bk = $detail['berkas_kurang'] ?? '';
                        $berkas_kurang_selected = array();
                        if (!empty($raw_bk)) {
                            if (is_string($raw_bk) && (strpos(trim($raw_bk), '[') === 0 || strpos(trim($raw_bk), '{') === 0)) {
                                $decoded = json_decode($raw_bk, true);
                                if (is_array($decoded)) {
                                    $berkas_kurang_selected = array_map('trim', $decoded);
                                }
                            }
                            if (empty($berkas_kurang_selected)) {
                                $parts = explode(',', $raw_bk);
                                foreach ($parts as $p) {
                                    $p = trim($p, "[]\"' \t\n\r\0\x0B");
                                    if ($p !== '') $berkas_kurang_selected[] = $p;
                                }
                            }
                        }
                    ?>

                    <div id="docGridContainer" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php foreach($berkas_items as $b): ?>
                            <?php
                                $is_invalid = ($b['status'] === 'Invalid') || in_array($b['key'], $berkas_kurang_selected);
                                $is_valid = ($b['status'] === 'Valid') && !$is_invalid;
                                $card_border = $is_valid ? 'border-emerald-200 bg-emerald-50/20' : ($is_invalid ? 'border-rose-200 bg-rose-50/30' : 'border-slate-200 bg-white');
                            ?>
                            <div id="doc_card_<?= $b['key']; ?>" class="clean-card doc-card rounded-2xl p-5 border <?= $card_border; ?> flex flex-col justify-between space-y-4 transition-all duration-300" data-key="<?= $b['key']; ?>">
                                <div>
                                    <div class="flex items-start justify-between gap-3 mb-2">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-xl bg-orange-50 border border-orange-200 text-orange-600 flex items-center justify-center text-sm font-bold shadow-2xs">
                                                <i class="bi <?= $b['icon']; ?>"></i>
                                            </div>
                                            <div>
                                                <h3 class="font-black text-sm text-slate-900"><?= $b['label']; ?></h3>
                                                <span class="text-[10px] text-slate-400 font-mono"><?= htmlspecialchars($b['file']); ?></span>
                                            </div>
                                        </div>
                                        
                                        <!-- Document Status Badge -->
                                        <span class="doc-badge px-2.5 py-1 rounded-lg text-xs font-bold <?= $is_valid ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($is_invalid ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-600 border border-slate-200'); ?>">
                                            <?= $is_valid ? 'Valid' : ($is_invalid ? 'Kurang/Revisi' : 'Belum Dicek'); ?>
                                        </span>
                                    </div>

                                    <!-- Document Action Buttons (Dropdown Preview & New Tab) -->
                                    <div class="flex items-center gap-2 pt-1">
                                        <button type="button" onclick="toggleInlinePdfPreview('<?= $b['key']; ?>', '<?= $resolve_pdf_url($b['file']); ?>')" 
                                                id="btnTogglePdf_<?= $b['key']; ?>"
                                                class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold text-xs flex items-center gap-2 shadow-xs cursor-pointer transition-all">
                                            <i class="bi bi-file-earmark-pdf text-rose-400 text-sm"></i>
                                            <span>Pratinjau Dokumen</span>
                                            <i class="bi bi-chevron-down text-[10px] ml-1 transition-transform duration-300" id="iconPdf_<?= $b['key']; ?>"></i>
                                        </button>
                                        <a href="<?= $resolve_pdf_url($b['file']); ?>" target="_blank" 
                                           class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 rounded-xl font-bold text-xs flex items-center gap-1.5 transition-all">
                                            <i class="bi bi-box-arrow-up-right"></i> Unduh
                                        </a>
                                    </div>

                                    <!-- Inline Dropdown PDF Viewer Accordion (Buttery Smooth CSS Grid Animation) -->
                                    <div id="inlinePdfWrapper_<?= $b['key']; ?>" class="grid grid-rows-[0fr] transition-all duration-500 ease-in-out opacity-0 overflow-hidden">
                                        <div class="min-h-0 overflow-hidden">
                                            <div id="inlinePdfBox_<?= $b['key']; ?>" class="rounded-2xl border border-slate-200 shadow-inner bg-slate-100 mt-3 overflow-hidden">
                                                <div class="p-2 px-3.5 bg-slate-900 text-white flex items-center justify-between text-xs">
                                                    <div class="flex items-center gap-2">
                                                        <i class="bi bi-file-earmark-pdf text-rose-400"></i>
                                                        <span class="font-bold text-white text-[11px]"><?= htmlspecialchars($b['label']); ?></span>
                                                        <span class="text-[10px] text-slate-400 font-mono truncate max-w-[150px]">(<?= htmlspecialchars($b['file']); ?>)</span>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <button type="button" onclick="toggleInlinePdfPreview('<?= $b['key']; ?>')" class="text-slate-400 hover:text-white font-bold text-xs cursor-pointer">
                                                            <i class="bi bi-x-lg"></i> Tutup
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="w-full h-[450px] relative bg-slate-200">
                                                    <iframe id="inlinePdfFrame_<?= $b['key']; ?>" src="about:blank" class="w-full h-full border-none" loading="lazy"></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons & Validation Toggle for Document -->
                                <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2">
                                    <span class="text-[11px] font-bold text-slate-500 flex items-center gap-1">
                                        <i class="bi bi-check2-circle text-orange-600"></i> Status Verifikasi:
                                    </span>

                                     <!-- Mutually Exclusive Validation Checkboxes (Valid & Kurang) -->
                                     <div class="flex items-center gap-3">
                                         <!-- Checkbox Valid -->
                                         <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-semibold text-slate-700 select-none hover:text-emerald-700 transition-colors">
                                             <input type="checkbox" name="berkas_valid[]" value="<?= $b['key']; ?>" 
                                                    <?= $is_valid ? 'checked' : ''; ?>
                                                    onchange="handleValidCheck(this)"
                                                    class="w-3.5 h-3.5 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer">
                                             <span class="text-[11px] text-emerald-700 font-medium">Valid</span>
                                         </label>

                                         <!-- Checkbox Kurang -->
                                         <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-semibold text-slate-700 select-none hover:text-rose-700 transition-colors">
                                             <input type="checkbox" name="berkas_kurang[]" value="<?= $b['key']; ?>" 
                                                    <?= $is_invalid ? 'checked' : ''; ?>
                                                    onchange="handleKurangCheck(this)"
                                                    class="w-3.5 h-3.5 text-rose-600 rounded border-slate-300 focus:ring-rose-500 cursor-pointer">
                                             <span class="text-[11px] text-rose-600 font-medium">Kurang / Revisi</span>
                                         </label>
                                     </div>
                                 </div>

                                 <!-- Per-Document Revision Note Field (Tampil saat Kurang/Revisi dicentang) -->
                                 <div class="catatan-doc-box <?= $is_invalid ? '' : 'hidden'; ?> pt-2.5 border-t border-rose-200/80 space-y-1.5 transition-all">
                                     <label class="text-[10px] font-bold text-rose-700 uppercase tracking-wider flex items-center justify-between">
                                         <span><i class="bi bi-pencil-fill text-[9px] mr-1"></i> Catatan Revisi khusus <?= $b['label']; ?>:</span>
                                     </label>

                                     <!-- Per-Card Quick Preset Chips -->
                                     <div class="flex flex-wrap items-center gap-1.5 mb-1">
                                         <?php foreach(($b['presets'] ?? array()) as $ps): ?>
                                             <button type="button" onclick="setDocNote('<?= $b['key']; ?>', '<?= addslashes($ps); ?>')" 
                                                     class="px-2 py-0.5 rounded-md bg-rose-100/70 hover:bg-rose-200 text-rose-800 text-[10px] font-medium border border-rose-200 cursor-pointer transition-colors">
                                                 + <?= htmlspecialchars($ps); ?>
                                             </button>
                                         <?php endforeach; ?>
                                     </div>

                                     <input type="text" 
                                            name="catatan_berkas[<?= $b['key']; ?>]" 
                                            id="catatan_doc_<?= $b['key']; ?>"
                                            placeholder="Tuliskan catatan perbaikan spesifik berkas ini..."
                                            oninput="syncAllCatatanAdmin()"
                                            class="w-full px-3 py-1.5 bg-white border border-rose-300 rounded-xl text-xs font-medium text-slate-800 placeholder-rose-300 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all shadow-2xs">
                                 </div>
                             </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Decision Section: Live Summary Message Preview & Action Buttons -->
                <div class="clean-card rounded-2xl p-6 sm:p-7 space-y-5">
                    <div>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                            <label for="catatan_admin" class="block text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                                <i class="bi bi-chat-left-text text-orange-600"></i> Pratinjau Rangkuman Catatan (Pesan Dikirim ke Mahasiswa)
                            </label>
                            <span class="text-[10px] text-slate-400">Tersusun otomatis dari berkas revisi di atas. Anda dapat menambah instruksi umum di sini.</span>
                        </div>

                        <textarea id="catatan_admin" name="catatan_admin" rows="3" 
                                  placeholder="Tuliskan catatan perbaikan kepada mahasiswa..."
                                  oninput="updateActionButtonsUI()"
                                  class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 focus:bg-white transition-all leading-relaxed"><?= htmlspecialchars($detail['catatan_admin'] ?? ''); ?></textarea>
                    </div>

                    <!-- Actions Bar -->
                    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <a href="<?= site_url('adminlayanan'); ?>" class="text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors order-2 sm:order-1 flex items-center gap-1 hover:-translate-x-0.5">
                            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                        </a>

                        <div class="flex flex-wrap items-center gap-4 sm:gap-5 w-full sm:w-auto order-1 sm:order-2">
                            <!-- Button Reject / Kembalikan -->
                            <button type="submit" name="action" value="reject" id="btnActionReject" onclick="return confirmReject();" 
                                    class="w-full sm:w-auto px-4 py-2.5 rounded-xl text-xs font-semibold flex items-center justify-center gap-1.5 transition-all">
                                <i class="bi bi-arrow-return-left"></i> Kembalikan ke Mahasiswa (Revisi)
                            </button>

                            <!-- Button Approve -->
                            <button type="submit" name="action" value="approve" id="btnActionApprove" onclick="return confirmApprove();" 
                                    class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-all">
                                <i class="bi bi-check2-all text-sm"></i> Setujui Semua Berkas (Approve)
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>

    </main>

    <!-- Live PDF / Document Viewer Modal -->
    <div id="pdfModal" class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-xs hidden items-center justify-center p-3 sm:p-5">
        <div class="bg-white rounded-2xl max-w-5xl w-full h-[88vh] flex flex-col overflow-hidden shadow-2xl border border-slate-200">
            <!-- Modal Header -->
            <div class="p-3.5 px-5 bg-slate-900 text-white flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-rose-600/30 border border-rose-500/50 text-rose-400 flex items-center justify-center font-bold text-base shadow-2xs">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </div>
                    <div>
                        <h4 id="pdfModalTitle" class="font-bold text-sm leading-tight">Pratinjau Dokumen</h4>
                        <span id="pdfModalFilename" class="text-[11px] text-slate-400 font-mono">file.pdf</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 bg-amber-500/20 border border-amber-400/30 text-amber-300 text-[11px] font-semibold rounded-xl">
                        <i class="bi bi-eye-fill"></i> Baca &amp; Periksa Dokumen Langsung
                    </span>
                    <button type="button" onclick="closePdfModal()" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-lg flex items-center justify-center transition-colors">&times;</button>
                </div>
            </div>

            <!-- Live PDF Viewer Iframe Body -->
            <div class="flex-grow bg-slate-200 relative w-full h-full overflow-hidden">
                <iframe id="pdfIframeViewer" src="" class="w-full h-full border-0"></iframe>
            </div>

            <!-- Modal Action Footer -->
            <div class="p-3.5 px-5 bg-white border-t border-slate-200 flex flex-wrap items-center justify-between gap-3 flex-shrink-0">
                <div class="flex items-center gap-2">
                    <button type="button" onclick="simulateDownload()" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition-colors">
                        <i class="bi bi-box-arrow-up-right"></i> Buka di Tab Baru / Unduh
                    </button>
                </div>
                <div class="flex items-center gap-2.5">
                    <button type="button" id="modalBtnKurang" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition-all active:scale-95 flex items-center gap-1.5">
                        <i class="bi bi-exclamation-circle"></i> Tandai Kurang / Revisi
                    </button>
                    <button type="button" id="modalBtnValid" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition-all active:scale-95 flex items-center gap-1.5">
                        <i class="bi bi-check2-circle"></i> Tandai Valid &amp; Lolos
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Interactive Toast Notification -->
    <div id="toastNotice" class="fixed bottom-6 right-6 z-50 bg-slate-900 text-white px-4 py-3 rounded-2xl shadow-2xl flex items-center gap-3 text-xs border border-slate-700">
        <div class="w-6 h-6 rounded-lg bg-emerald-500 text-white flex items-center justify-center font-bold text-xs">
            <i class="bi bi-check-lg"></i>
        </div>
        <span id="toastMsg" class="font-medium">Pemberitahuan</span>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 mt-8 text-center text-xs text-slate-500">
        &copy; <?= date('Y'); ?> Fakultas Industri Kreatif - Telkom University. Modul Admin Layanan Akademik (LAA).
    </footer>

    <!-- Interactive Scripts -->
    <script>
        let currentModalKey = '';
        let currentModalFileUrl = '';

        function toggleInlinePdfPreview(key, fileUrl) {
            const wrapper = document.getElementById('inlinePdfWrapper_' + key);
            const icon = document.getElementById('iconPdf_' + key);
            const iframe = document.getElementById('inlinePdfFrame_' + key);
            const btn = document.getElementById('btnTogglePdf_' + key);

            if (!wrapper) return;

            const defaultSamplePdf = '<?= base_url("uploads/persyaratan_ta/Sertifikat_Massal_2026-07-07_(2).pdf"); ?>';
            const targetUrl = (fileUrl && fileUrl.match(/\.(pdf|png|jpg|jpeg)$/i)) ? fileUrl : defaultSamplePdf;

            const isOpen = wrapper.classList.contains('grid-rows-[1fr]');

            if (!isOpen) {
                // Close any other open dropdowns first for clean accordion UX
                document.querySelectorAll('[id^="inlinePdfWrapper_"]').forEach(el => {
                    el.classList.remove('grid-rows-[1fr]', 'opacity-100');
                    el.classList.add('grid-rows-[0fr]', 'opacity-0');
                });
                document.querySelectorAll('[id^="iconPdf_"]').forEach(el => el.classList.remove('rotate-180'));
                document.querySelectorAll('[id^="btnTogglePdf_"]').forEach(el => el.classList.remove('ring-2', 'ring-orange-500/40'));

                if (iframe && (iframe.src === 'about:blank' || !iframe.src)) {
                    iframe.src = targetUrl + '#view=FitH&zoom=100';
                }

                wrapper.classList.remove('grid-rows-[0fr]', 'opacity-0');
                wrapper.classList.add('grid-rows-[1fr]', 'opacity-100');
                if (icon) icon.classList.add('rotate-180');
                if (btn) btn.classList.add('ring-2', 'ring-orange-500/40');
            } else {
                wrapper.classList.remove('grid-rows-[1fr]', 'opacity-100');
                wrapper.classList.add('grid-rows-[0fr]', 'opacity-0');
                if (icon) icon.classList.remove('rotate-180');
                if (btn) btn.classList.remove('ring-2', 'ring-orange-500/40');
            }
        }

        function openPdfModal(title, filename, key, fileUrl) {
            toggleInlinePdfPreview(key, fileUrl);
        }

        function setDocLayout(mode) {
            const container = document.getElementById('docGridContainer');
            const btnGrid = document.getElementById('btnLayoutGrid');
            const btnList = document.getElementById('btnLayoutList');

            if (!container) return;

            if (mode === 'list') {
                container.classList.remove('md:grid-cols-2');
                container.classList.add('grid-cols-1');

                if (btnList && btnGrid) {
                    btnList.classList.add('bg-white', 'text-slate-900', 'shadow-xs');
                    btnList.classList.remove('text-slate-500');
                    btnGrid.classList.remove('bg-white', 'text-slate-900', 'shadow-xs');
                    btnGrid.classList.add('text-slate-500');
                }
                localStorage.setItem('laa_doc_layout', 'list');
            } else {
                container.classList.remove('grid-cols-1');
                container.classList.add('grid-cols-1', 'md:grid-cols-2');

                if (btnGrid && btnList) {
                    btnGrid.classList.add('bg-white', 'text-slate-900', 'shadow-xs');
                    btnGrid.classList.remove('text-slate-500');
                    btnList.classList.remove('bg-white', 'text-slate-900', 'shadow-xs');
                    btnList.classList.add('text-slate-500');
                }
                localStorage.setItem('laa_doc_layout', 'grid');
            }
        }

        // Restore saved layout choice on load
        document.addEventListener('DOMContentLoaded', () => {
            const savedLayout = localStorage.getItem('laa_doc_layout');
            if (savedLayout === 'list') {
                setDocLayout('list');
            }
        });

        // Close pdfModal when clicking dark backdrop outside content box
        document.addEventListener('DOMContentLoaded', () => {
            const pdfModal = document.getElementById('pdfModal');
            if (pdfModal) {
                pdfModal.addEventListener('click', (e) => {
                    if (e.target === pdfModal) {
                        closePdfModal();
                    }
                });
            }
        });

        function toggleDocStatus(key, isKurang) {
            const card = document.querySelector('.doc-card[data-key="' + key + '"]');
            if (card) {
                const cbValid = card.querySelector('input[name="berkas_valid[]"]');
                const cbKurang = card.querySelector('input[name="berkas_kurang[]"]');

                if (isKurang) {
                    if (cbValid) cbValid.checked = false;
                    if (cbKurang) cbKurang.checked = true;
                } else {
                    if (cbValid) cbValid.checked = true;
                    if (cbKurang) cbKurang.checked = false;
                }
                updateCardState(card);
            }
        }

        function handleValidCheck(cbValid) {
            const card = cbValid.closest('.doc-card');
            const cbKurang = card.querySelector('input[name="berkas_kurang[]"]');
            if (cbValid.checked && cbKurang) {
                cbKurang.checked = false;
            }
            updateCardState(card);
        }

        function handleKurangCheck(cbKurang) {
            const card = cbKurang.closest('.doc-card');
            const cbValid = card.querySelector('input[name="berkas_valid[]"]');
            if (cbKurang.checked && cbValid) {
                cbValid.checked = false;
            }
            updateCardState(card);
        }

        function updateCardState(card) {
            const cbValid = card.querySelector('input[name="berkas_valid[]"]');
            const cbKurang = card.querySelector('input[name="berkas_kurang[]"]');
            const badge = card.querySelector('.doc-badge');
            const noteBox = card.querySelector('.catatan-doc-box');
            const noteInput = noteBox ? noteBox.querySelector('input') : null;

            card.classList.remove('border-emerald-200', 'bg-emerald-50/20', 'border-rose-200', 'bg-rose-50/30', 'border-slate-200', 'bg-white');

            if (cbValid && cbValid.checked) {
                card.classList.add('border-emerald-200', 'bg-emerald-50/20');
                if (badge) {
                    badge.className = 'doc-badge px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200';
                    badge.textContent = 'Valid';
                }
                if (noteBox) noteBox.classList.add('hidden');
                if (noteInput) noteInput.value = '';
            } else if (cbKurang && cbKurang.checked) {
                card.classList.add('border-rose-200', 'bg-rose-50/30');
                if (badge) {
                    badge.className = 'doc-badge px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200';
                    badge.textContent = 'Kurang/Revisi';
                }
                if (noteBox) {
                    noteBox.classList.remove('hidden');
                    if (noteInput && !noteInput.value) noteInput.focus();
                }
            } else {
                card.classList.add('border-slate-200', 'bg-white');
                if (badge) {
                    badge.className = 'doc-badge px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200';
                    badge.textContent = 'Belum Dicek';
                }
                if (noteBox) noteBox.classList.add('hidden');
                if (noteInput) noteInput.value = '';
            }
            syncAllCatatanAdmin();
            updateActionButtonsUI();
        }

        function syncAllCatatanAdmin() {
            const compiledNotes = [];
            const labels = {
                'ksm': 'KSM',
                'transkrip': 'Transkrip Nilai',
                'pernyataan': 'Surat Pernyataan',
                'bebas_lab': 'Surat Bebas Lab & Perpustakaan'
            };

            document.querySelectorAll('.doc-card').forEach(card => {
                const key = card.getAttribute('data-key');
                const cbKurang = card.querySelector('input[name="berkas_kurang[]"]');
                const noteInput = card.querySelector('.catatan-doc-box input');

                if (cbKurang && cbKurang.checked) {
                    const text = noteInput ? noteInput.value.trim() : '';
                    const labelName = labels[key] || key;
                    if (text) {
                        compiledNotes.push('- ' + labelName + ': ' + text);
                    } else {
                        compiledNotes.push('- ' + labelName + ': Memerlukan perbaikan / revisi.');
                    }
                }
            });

            const mainTextarea = document.getElementById('catatan_admin');
            if (mainTextarea && compiledNotes.length > 0) {
                mainTextarea.value = compiledNotes.join('\n');
            }
        }

        function setDocNote(key, noteText) {
            const card = document.querySelector('.doc-card[data-key="' + key + '"]');
            if (!card) return;

            const cbKurang = card.querySelector('input[name="berkas_kurang[]"]');
            if (cbKurang) {
                cbKurang.checked = true;
                handleKurangCheck(cbKurang);
            }

            const noteInput = card.querySelector('.catatan-doc-box input');
            if (noteInput) {
                if (noteInput.value.trim().length > 0) {
                    if (!noteInput.value.includes(noteText)) {
                        noteInput.value += ', ' + noteText;
                    }
                } else {
                    noteInput.value = noteText;
                }
                noteInput.focus();
            }
            syncAllCatatanAdmin();
        }

        function updateActionButtonsUI() {
            const btnApprove = document.getElementById('btnActionApprove');
            const btnReject = document.getElementById('btnActionReject');
            if (!btnApprove || !btnReject) return;

            const checkedValidCount = document.querySelectorAll('input[name="berkas_valid[]"]:checked').length;
            const checkedKurangCount = document.querySelectorAll('input[name="berkas_kurang[]"]:checked').length;
            const mainCatatan = document.getElementById('catatan_admin') ? document.getElementById('catatan_admin').value.trim() : '';
            const currentAdminStatus = '<?= $detail['status_approval_admin'] ?? 'Pending'; ?>';

            // 1. Dynamic Reject / Batalkan Revisi Button Logic
            btnReject.type = 'submit';

            if (checkedKurangCount > 0) {
                btnReject.name = 'action';
                btnReject.value = 'reject';
                btnReject.innerHTML = '<i class="bi bi-arrow-return-left"></i> Kembalikan ke Mahasiswa (Revisi)';
                btnReject.className = 'btn-action-animated w-full sm:w-auto px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md shadow-rose-600/25 flex items-center justify-center gap-1.5 cursor-pointer transition-all';
                btnReject.title = 'Kembalikan pengajuan ke mahasiswa untuk perbaikan berkas';
                btnReject.onclick = function() { return confirmReject(); };
            } else if (currentAdminStatus === 'Rejected') {
                btnReject.name = 'action';
                btnReject.value = 'cancel_reject';
                btnReject.innerHTML = '<i class="bi bi-arrow-counterclockwise"></i> Batalkan Revisi (Kembali ke Pending)';
                btnReject.className = 'btn-action-animated w-full sm:w-auto px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold shadow-md shadow-amber-500/25 flex items-center justify-center gap-1.5 cursor-pointer transition-all';
                btnReject.title = 'Batalkan status revisi dan kembalikan mahasiswa ke status Pending';
                btnReject.onclick = function() { return confirm('Apakah Anda yakin ingin MEMBATALKAN status revisi dan mengembalikan mahasiswa ke status Pending?'); };
            } else if (mainCatatan.length > 0) {
                btnReject.name = 'action';
                btnReject.value = 'reject';
                btnReject.innerHTML = '<i class="bi bi-arrow-return-left"></i> Kembalikan ke Mahasiswa (Revisi)';
                btnReject.className = 'btn-action-animated w-full sm:w-auto px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md shadow-rose-600/25 flex items-center justify-center gap-1.5 cursor-pointer transition-all';
                btnReject.title = 'Kembalikan pengajuan ke mahasiswa dengan catatan perbaikan';
                btnReject.onclick = function() { return confirmReject(); };
            } else {
                btnReject.name = 'action';
                btnReject.value = 'reject';
                btnReject.innerHTML = '<i class="bi bi-arrow-return-left"></i> Kembalikan ke Mahasiswa (Revisi)';
                btnReject.className = 'w-full sm:w-auto px-4 py-2.5 bg-rose-50 text-rose-300 border border-rose-100 rounded-xl text-xs font-semibold flex items-center justify-center gap-1.5 opacity-60 cursor-not-allowed transition-all';
                btnReject.title = 'Centang minimal 1 berkas Kurang/Revisi atau tuliskan catatan perbaikan';
                btnReject.onclick = function() { return confirmReject(); };
            }

            // 2. Dynamic Approve Button Logic
            if (checkedKurangCount > 0) {
                btnApprove.className = 'w-full sm:w-auto px-5 py-2.5 bg-slate-100 text-slate-400 border border-slate-200 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 opacity-60 cursor-not-allowed transition-all';
                btnApprove.title = 'Tidak bisa disetujui karena terdapat berkas yang ditandai Kurang/Revisi!';
            } else {
                btnApprove.className = 'btn-action-animated w-full sm:w-auto px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-extrabold shadow-md shadow-emerald-600/25 flex items-center justify-center gap-1.5 cursor-pointer transition-all';
                btnApprove.title = 'Semua berkas valid! Klik untuk menyetujui pengajuan.';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateActionButtonsUI();
        });

        function resetAllChoice() {
            document.querySelectorAll('.doc-card').forEach(card => {
                const cbValid = card.querySelector('input[name="berkas_valid[]"]');
                const cbKurang = card.querySelector('input[name="berkas_kurang[]"]');
                if (cbValid) cbValid.checked = false;
                if (cbKurang) cbKurang.checked = false;
                updateCardState(card);
            });
            const mainTextarea = document.getElementById('catatan_admin');
            if (mainTextarea) {
                mainTextarea.value = '';
            }
            updateActionButtonsUI();
            showToast('Semua status berkas & catatan berhasil di-reset!');
        }

        function markAllValid() {
            document.querySelectorAll('.doc-card').forEach(card => {
                const cbValid = card.querySelector('input[name="berkas_valid[]"]');
                const cbKurang = card.querySelector('input[name="berkas_kurang[]"]');
                if (cbValid) cbValid.checked = true;
                if (cbKurang) cbKurang.checked = false;
                updateCardState(card);
            });
            showToast('Semua 4 berkas berhasil ditandai Valid!');
        }

        function appendNote(text) {
            const textarea = document.getElementById('catatan_admin');
            if (textarea.value.trim().length > 0) {
                textarea.value = textarea.value.trim() + '\n- ' + text;
            } else {
                textarea.value = '- ' + text;
            }
            textarea.focus();
            showToast('Catatan ditambahkan!');
        }

        function simulateDownload() {
            const defaultSamplePdf = '<?= base_url("uploads/persyaratan_ta/Sertifikat_Massal_2026-07-07_(2).pdf"); ?>';
            const targetUrl = (currentModalFileUrl && currentModalFileUrl.match(/\.(pdf|png|jpg|jpeg)$/i)) ? currentModalFileUrl : defaultSamplePdf;
            window.open(targetUrl, '_blank');
        }

        function copyNIM(nim) {
            navigator.clipboard.writeText(nim).then(() => {
                showToast('NIM ' + nim + ' berhasil disalin!');
            });
        }

        function showToast(message) {
            const toast = document.getElementById('toastNotice');
            document.getElementById('toastMsg').textContent = message;
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 2500);
        }

        function confirmReject() {
            const checkedCount = document.querySelectorAll('input[name="berkas_kurang[]"]:checked').length;
            const checkedValid = document.querySelectorAll('input[name="berkas_valid[]"]:checked').length;
            const catatan = document.getElementById('catatan_admin').value.trim();

            if (checkedCount === 0 && checkedValid === 4) {
                alert('Peringatan: Seluruh berkas telah dicentang Valid. Silakan gunakan tombol "Setujui & Teruskan ke Koordinator TA" jika semua berkas sudah sesuai!');
                return false;
            }

            if (checkedCount === 0 && !catatan) {
                alert('Peringatan: Silakan centang minimal 1 dokumen yang "Kurang / Revisi" atau tuliskan catatan instruksi revisi sebelum mengembalikan pengajuan ke mahasiswa!');
                return false;
            }

            return confirm('Yakin ingin mengembalikan pengajuan ini ke mahasiswa untuk revisi/perbaikan berkas?');
        }

        function confirmApprove() {
            const checkedKurang = document.querySelectorAll('input[name="berkas_kurang[]"]:checked').length;
            if (checkedKurang > 0) {
                alert('Peringatan: Terdapat ' + checkedKurang + ' berkas yang masih ditandai "Kurang / Revisi". Harap perbaiki centang atau gunakan tombol Kembalikan ke Mahasiswa!');
                return false;
            }

            const checkedValid = document.querySelectorAll('input[name="berkas_valid[]"]:checked').length;
            if (checkedValid < 4) {
                const setAll = confirm('Informasi: Baru ' + checkedValid + ' dari 4 berkas yang dicentang Valid.\n\nApakah Anda ingin otomatis menandai SELURUH 4 berkas sebagai VALID dan menyetujui pengajuan ini ke Koordinator TA?');
                if (setAll) {
                    markAllValid();
                    return true;
                }
                return false;
            }

            return confirm('Yakin seluruh 4 berkas mahasiswa ini telah lengkap dan valid? Pengajuan akan diteruskan ke Koordinator TA.');
        }
        function toggleDocGrid(cols) {
            const container = document.getElementById('docGridContainer');
            const btnSingle = document.getElementById('btnLayoutSingle');
            const btnGrid = document.getElementById('btnLayoutGrid');
            if (!container) return;

            if (cols === 1) {
                container.className = 'grid grid-cols-1 gap-6';
                if (btnSingle) btnSingle.className = 'px-2.5 py-1 rounded-lg font-bold bg-white text-orange-600 shadow-xs flex items-center gap-1 transition-all cursor-pointer';
        }
        function toggleDocGrid(cols) {
            const container = document.getElementById('docGridContainer');
            const btnSingle = document.getElementById('btnLayoutSingle');
            const btnGrid = document.getElementById('btnLayoutGrid');
            if (!container) return;

            if (cols === 1) {
                container.className = 'grid grid-cols-1 gap-6';
                if (btnSingle) btnSingle.className = 'px-2.5 py-1 rounded-lg font-bold bg-white text-orange-600 shadow-xs flex items-center gap-1 transition-all cursor-pointer';
                if (btnGrid) btnGrid.className = 'px-2.5 py-1 rounded-lg font-bold text-slate-600 hover:text-slate-900 flex items-center gap-1 transition-all cursor-pointer';
            } else {
                container.className = 'grid grid-cols-1 md:grid-cols-2 gap-6';
                if (btnGrid) btnGrid.className = 'px-2.5 py-1 rounded-lg font-bold bg-white text-orange-600 shadow-xs flex items-center gap-1 transition-all cursor-pointer';
                if (btnSingle) btnSingle.className = 'px-2.5 py-1 rounded-lg font-bold text-slate-600 hover:text-slate-900 flex items-center gap-1 transition-all cursor-pointer';
            }
        }

        // Focus & highlight specific document card if requested via hash or URL param
        document.addEventListener('DOMContentLoaded', () => {
            const hash = window.location.hash;
            const urlParams = new URLSearchParams(window.location.search);
            const focusKey = urlParams.get('focus') || (hash ? hash.replace('#doc_card_', '').replace('#', '') : null);

            if (focusKey) {
                const targetCard = document.getElementById('doc_card_' + focusKey) || document.querySelector(`[data-key="${focusKey}"]`);
                if (targetCard) {
                    setTimeout(() => {
                        targetCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        targetCard.classList.add('ring-4', 'ring-orange-500', 'ring-offset-2', 'shadow-2xl', 'scale-[1.02]');
                        setTimeout(() => {
                            targetCard.classList.remove('ring-4', 'ring-orange-500', 'ring-offset-2', 'shadow-2xl', 'scale-[1.02]');
                        }, 3000);
                    }, 300);
                }
            }
        });
    </script>

</body>
</html>
