<!-- =========================================================================================
 * VIEW: PORTAL BIMBINGAN & EVALUASI PREVIEW 1 MAHASISWA
 * Path: application/views/mahasiswa/bimbingan_preview1.php
 * Pengembang: Nazril Rizki
 * =========================================================================================
 * 
 * Deskripsi:
 * Antarmuka bimbingan akademik Tugas Akhir untuk mahasiswa pada tahapan Preview 1 (Bab 1, 2, dan 3).
 * Memfasilitasi mahasiswa untuk mengunggah naskah naskah TA, mencatat logbook konsultasi bimbingan,
 * memantau status persetujuan (ACC / Revisi) dari Dosen Pembimbing 1 dan Dosen Pembimbing 2,
 * serta melihat riwayat catatan evaluasi dosen pembimbing secara interaktif.
 * ========================================================================================= -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Bimbingan & Evaluasi Preview TA — IFIK Portal'; ?></title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500;1,600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>?v=<?= time(); ?>">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
    <style>
        body, button, input, textarea, select {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }
        .orb-glow { position: absolute; border-radius: 50%; filter: blur(50px); pointer-events: none; z-index: 0; }

        .modal-overlay {
            position: fixed; inset: 0; background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(6px); z-index: 9998;
            display: flex; align-items: center; justify-content: center;
            padding: 1rem; animation: fadeIn 0.2s ease;
        }
        .modal-overlay.hidden { display: none !important; }
        .modal-content {
            background: white; border-radius: 1.5rem; max-width: 600px; width: 100%;
            max-height: 80vh; display: flex; flex-direction: column;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.25);
            animation: slideUp 0.25s ease; overflow: hidden;
        }
        .modal-body-scroll {
            overflow-y: auto; flex: 1; padding: 1.25rem 1.5rem;
            scrollbar-width: thin; scrollbar-color: #fdba74 #fef3c7;
        }
        .modal-body-scroll::-webkit-scrollbar { width: 6px; }
        .modal-body-scroll::-webkit-scrollbar-track { background: #fef3c7; border-radius: 3px; }
        .modal-body-scroll::-webkit-scrollbar-thumb { background: #fdba74; border-radius: 3px; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        .tab-card-active {
            border-color: #f97316 !important;
            box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.25), 0 10px 30px -5px rgba(249, 115, 22, 0.2) !important;
            background: linear-gradient(to bottom, #fff7ed, #ffffff) !important;
        }
        .tab-card-locked { opacity: 0.65; filter: grayscale(0.35); cursor: not-allowed !important; }
        .badge-active-step {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 2px 10px; border-radius: 9999px; font-size: 10px; font-weight: 800;
            letter-spacing: 0.05em; text-transform: uppercase;
            background: #f97316; color: white; box-shadow: 0 2px 8px rgba(249, 115, 22, 0.4);
        }

        .drop-zone-sidang {
            border: 2px dashed #d1d5db; background-color: #f9fafb;
            transition: all 0.2s; cursor: pointer;
        }
        .drop-zone-sidang:hover, .drop-zone-sidang.dragover {
            border-color: #10b981; background-color: #ecfdf5;
        }

        /* ===== Dropzone Persyaratan Sidang (baru) ===== */
        .ps-dropzone {
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .ps-dropzone.dragover {
            border-color: #ea580c !important;
            background-color: #fff7ed !important;
            transform: scale(1.01);
            box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.15);
        }

        @media (max-width: 768px) {
            .responsive-log-table { border: none !important; background: transparent !important; box-shadow: none !important; }
            .responsive-log-table thead { display: none !important; }
            .responsive-log-table, .responsive-log-table tbody { display: block !important; width: 100% !important; }
            .responsive-log-table tr {
                display: block !important; margin: 0 0 1rem 0 !important;
                border: 1.5px solid #e2e8f0 !important; border-radius: 18px !important;
                padding: 0.9rem 0.85rem 0.7rem !important; background: #ffffff !important;
                box-shadow: 0 4px 14px -4px rgba(15,23,42,0.09) !important; position: relative !important;
            }
            .responsive-log-table tbody tr:last-child { margin-bottom: 0 !important; }
            .responsive-log-table tbody tr:hover { background: #fff !important; }
            .responsive-log-table td {
                display: block !important; width: 100% !important;
                padding: 0.5rem 0.35rem !important; text-align: left !important;
                border-bottom: 1px dashed #f1f5f9 !important; position: relative !important;
                padding-left: 42% !important; min-height: 34px !important;
                font-size: 0.8rem !important; vertical-align: top !important;
            }
            .responsive-log-table td:last-child { border-bottom: none !important; padding-bottom: 0.2rem !important; }
            .responsive-log-table td::before {
                content: attr(data-label); position: absolute; left: 0.35rem; top: 0.55rem;
                width: 38%; font-weight: 800; font-size: 0.6rem; text-transform: uppercase;
                color: #64748b; letter-spacing: 0.05em; line-height: 1.2;
            }
            .responsive-log-table td.cell-no {
                position: absolute !important; top: 0.65rem !important; right: 0.65rem !important;
                width: auto !important; padding: 0 !important; border: none !important;
                padding-left: 0 !important; min-height: 0 !important; z-index: 2 !important;
            }
            .responsive-log-table td.cell-no::before { display: none !important; }
            .responsive-log-table td.cell-no span {
                display: inline-flex !important; align-items: center !important;
                justify-content: center !important; width: 28px !important; height: 28px !important;
                border-radius: 10px !important;
                background: linear-gradient(135deg, #ea580c, #f97316) !important;
                color: #fff !important; font-weight: 800 !important; font-size: 0.7rem !important;
                box-shadow: 0 3px 8px rgba(234,88,12,0.3) !important;
            }
            .responsive-log-table td.cell-file {
                padding: 0.15rem 2.5rem 0.6rem 0.35rem !important;
                border-bottom: 1px solid #e2e8f0 !important; margin-bottom: 0.35rem;
            }
            .responsive-log-table td.cell-file::before { display: none !important; }
            .responsive-log-table td.cell-center { text-align: right !important; padding-right: 0.35rem !important; }
            .responsive-log-table td.cell-center::before { width: 40%; text-align: left; }
            .responsive-log-table td[colspan] {
                display: block !important; padding: 2rem 1rem !important;
                padding-left: 1rem !important; text-align: center !important;
                border: none !important; min-height: 0 !important;
            }
            .responsive-log-table td[colspan]::before { display: none !important; }
            .responsive-log-table tbody tr:has(td[colspan]) {
                padding: 0 !important; border: none !important;
                box-shadow: none !important; background: transparent !important;
            }
            .responsive-log-table .log-file-link {
                max-width: 100% !important; white-space: normal !important; word-break: break-all;
            }
            .responsive-log-table .inline-flex { font-size: 0.68rem !important; }
        }

        #unifiedCommentModal {
            position: fixed; inset: 0; z-index: 10000; display: none;
            align-items: center; justify-content: center;
            background: rgba(15, 23, 42, 0.55); backdrop-filter: blur(6px); padding: 1rem;
        }
        #unifiedCommentModal.active { display: flex; }
        #unifiedCommentModal .uc-modal-box {
            background: #fff; border-radius: 1.75rem; max-width: 640px; width: 100%;
            max-height: 88vh; overflow: hidden; box-shadow: 0 40px 80px -20px rgba(0,0,0,0.45);
            display: flex; flex-direction: column; animation: slideUp 0.25s ease;
        }
        #unifiedCommentModal .uc-header {
            padding: 1.1rem 1.4rem; background: #1e293b; color: white;
            display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;
        }
        #unifiedCommentModal .uc-body { padding: 1.25rem 1.4rem; overflow-y: auto; flex: 1; }
        .uc-tab-btn {
            padding: 6px 12px; border-radius: 10px; font-size: 0.7rem; font-weight: 800;
            border: 1.5px solid transparent; cursor: pointer; transition: all 0.15s ease;
            display: inline-flex; align-items: center; gap: 5px;
        }
        .uc-tab-btn.p1-active { background: #ffedd5; color: #c2410c; border-color: #fdba74; }
        .uc-tab-btn.p1-inactive { background: #f1f5f9; color: #64748b; border-color: #e2e8f0; }
        .uc-tab-btn.p1-inactive:hover { background: #e2e8f0; }
        .uc-tab-btn.p2-active { background: #e0e7ff; color: #4338ca; border-color: #a5b4fc; }
        .uc-tab-btn.p2-inactive { background: #f1f5f9; color: #64748b; border-color: #e2e8f0; }
        .uc-tab-btn.p2-inactive:hover { background: #e2e8f0; }
        .uc-tab-btn.u1-active { background: #d1fae5; color: #065f46; border-color: #6ee7b7; }
        .uc-tab-btn.u1-inactive { background: #f1f5f9; color: #64748b; border-color: #e2e8f0; }
        .uc-tab-btn.u1-inactive:hover { background: #e2e8f0; }
        .uc-tab-btn.u2-active { background: #ede9fe; color: #6d28d9; border-color: #c4b5fd; }
        .uc-tab-btn.u2-inactive { background: #f1f5f9; color: #64748b; border-color: #e2e8f0; }
        .uc-tab-btn.u2-inactive:hover { background: #e2e8f0; }
        .uc-comment-content {
            padding: 1rem 1.1rem; background: #f8fafc; border: 1px solid #e2e8f0;
            border-radius: 14px; font-size: 0.85rem; line-height: 1.6;
            color: #1e293b; font-weight: 500; white-space: pre-wrap; word-break: break-word;
        }
        .uc-comment-content p { margin: 0 0 0.6rem 0; }
        .uc-comment-content p:last-child { margin-bottom: 0; }
        .uc-comment-content ul { list-style: disc; padding-left: 1.4rem; margin: 0.5rem 0; }
        .uc-comment-content ol { list-style: decimal; padding-left: 1.4rem; margin: 0.5rem 0; }
        .uc-comment-content li { margin-bottom: 0.25rem; }
        .uc-comment-content a { color: #c2410c; text-decoration: underline; font-weight: 600; }
        .uc-comment-content strong, .uc-comment-content b { font-weight: 800; color: #0f172a; }
        .uc-comment-content em, .uc-comment-content i { font-style: italic; }
        .uc-comment-content u { text-decoration: underline; }
        .uc-comment-content br { line-height: 1.6; }
        .uc-comment-content h1, .uc-comment-content h2, .uc-comment-content h3,
        .uc-comment-content h4, .uc-comment-content h5, .uc-comment-content h6 {
            font-weight: 800; color: #0f172a; margin: 0.5rem 0 0.35rem;
        }
        .uc-comment-content blockquote {
            border-left: 3px solid #fdba74; padding-left: 0.75rem; color: #475569;
            margin: 0.5rem 0; font-style: italic;
        }

        /* ===== Milestone chip (glass, menyatu dengan hero) ===== */
        .ms-chip {
            display: flex; align-items: center; gap: .55rem; padding: .5rem .7rem; min-width: 0;
            border-radius: 1rem; border: 1.5px solid rgba(255,255,255,.22);
            background: rgba(255,255,255,.10); color: #fff; cursor: pointer;
            backdrop-filter: blur(6px); transition: all .2s ease;
        }
        .ms-chip:hover { background: rgba(255,255,255,.18); }
        .ms-chip .ms-sub { color: rgba(255,255,255,.75); }
        .ms-chip.ms-locked { opacity: .6; }
        .ms-chip.tab-card-active {
            background: #ffffff !important; border-color: #ffffff !important; color: #0f172a !important; opacity: 1;
            box-shadow: 0 0 0 3px rgba(251,191,36,.5), 0 8px 18px -6px rgba(0,0,0,.35) !important;
        }
        .ms-chip.tab-card-active .ms-sub { color: #64748b; }
    </style>
</head>
<body class="bg-gradient-to-br from-amber-50/40 via-orange-50/25 to-slate-100 min-h-screen text-slate-800 antialiased flex flex-col justify-between selection:bg-orange-500 selection:text-white">

    <?php $this->load->view('components/curved_sidebar'); ?>

    <div id="mainPageContent" class="page-wrapper-for-sidebar min-h-screen flex flex-col flex-grow">

    <?php $this->load->view('partials/mahasiswa_navbar'); ?>

    <main class="w-full px-4 sm:px-6 lg:px-10 py-6 sm:py-8 flex-grow space-y-6">

        <?php if ($this->session->flashdata('success')): ?>
            <div class="p-5 rounded-3xl bg-emerald-50 border-2 border-emerald-300 text-emerald-900 text-sm font-semibold flex items-center justify-between shadow-md shadow-emerald-500/10">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg shrink-0 box-3d">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <span><?= $this->session->flashdata('success'); ?></span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold text-2xl leading-none cursor-pointer">&times;</button>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="p-5 rounded-3xl bg-rose-50 border-2 border-rose-300 text-rose-900 text-sm font-semibold flex items-center justify-between shadow-md shadow-rose-500/10">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-rose-500 text-white flex items-center justify-center text-lg shrink-0 box-3d">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <span><?= $this->session->flashdata('error'); ?></span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 font-bold text-2xl leading-none cursor-pointer">&times;</button>
            </div>
        <?php endif; ?>

        <?php
            // ---------- Status milestone (dipakai hero + panel) ----------
            $is_p1_app = (!empty($latest_p1['lulus_preview1']) && (string)$latest_p1['lulus_preview1'] === '1') || (!empty($pendaftaran['lulus_preview1']) && (string)$pendaftaran['lulus_preview1'] === '1');
            $is_p2_app = (!empty($latest_p2['lulus_preview2']) && (string)$latest_p2['lulus_preview2'] === '1') || (!empty($pendaftaran['lulus_preview2']) && (string)$pendaftaran['lulus_preview2'] === '1');
            $is_p3_app = (!empty($latest_p3['lulus_preview3']) && (string)$latest_p3['lulus_preview3'] === '1') || (!empty($pendaftaran['lulus_preview3']) && (string)$pendaftaran['lulus_preview3'] === '1');

            // Per-file approval flags untuk Preview 3 (didefinisikan di sini supaya
            // selalu tersedia di SSE handler JS, berapapun step yang aktif)
            $has_app_bimbingan   = false;
            $has_app_sitasi      = false;
            $has_app_persyaratan = false;
            $latest_p3_bim       = null;
            $latest_p3_sit       = null;
            $latest_p3_per       = null;
            if (!empty($riwayat_preview3)) {
                foreach ($riwayat_preview3 as $_r3) {
                    if (!empty($_r3['file_bimbingan']) && $latest_p3_bim === null) {
                        $latest_p3_bim = $_r3;
                        $st_b_eff = (!empty($_r3['status_file_bimbingan']) && $_r3['status_file_bimbingan'] !== 'Pending')
                            ? $_r3['status_file_bimbingan']
                            : ($_r3['status_pembimbing'] ?? 'Pending');
                        if ($st_b_eff === 'Approved') $has_app_bimbingan = true;
                    }
                    if (!empty($_r3['file_sitasi']) && $latest_p3_sit === null) {
                        $latest_p3_sit = $_r3;
                        $st_s_eff = (!empty($_r3['status_file_sitasi']) && $_r3['status_file_sitasi'] !== 'Pending')
                            ? $_r3['status_file_sitasi']
                            : ($_r3['status_pembimbing'] ?? 'Pending');
                        if ($st_s_eff === 'Approved') $has_app_sitasi = true;
                    }
                    if (!empty($_r3['file_persyaratan']) && $latest_p3_per === null) {
                        $latest_p3_per = $_r3;
                        $st_p_eff = (!empty($_r3['status_file_persyaratan']) && $_r3['status_file_persyaratan'] !== 'Pending')
                            ? $_r3['status_file_persyaratan']
                            : ($_r3['status_pembimbing'] ?? 'Pending');
                        if ($st_p_eff === 'Approved') $has_app_persyaratan = true;
                    }
                }
            }

            $rekomen_type  = strtolower(trim($rekomendasi['recommendation_type'] ?? 'sidang'));
            $is_non_sidang = ($rekomen_type === 'non_sidang');

            // Flag status blokir & revisi Preview 1
            $st_p1_raw      = !empty($latest_p1) ? ($latest_p1['status_pembimbing'] ?? 'Pending') : null;
            $st_p1_clean    = $st_p1_raw ? strtolower($st_p1_raw) : null;
            $is_p1_pending  = ($st_p1_clean === 'pending');
            $is_p1_approved = ($st_p1_clean === 'approved');
            $is_p1_revision = in_array($st_p1_clean, ['revision', 'rejected', 'revisi']);

            // Flag status blokir & revisi Preview 2
            $st_p2_raw      = !empty($latest_p2) ? ($latest_p2['status_pembimbing'] ?? 'Pending') : null;
            $st_p2_clean    = $st_p2_raw ? strtolower($st_p2_raw) : null;
            $is_p2_pending  = ($st_p2_clean === 'pending');
            $is_p2_approved = ($st_p2_clean === 'approved');
            $is_p2_revision = in_array($st_p2_clean, ['revision', 'rejected', 'revisi']);

            // Flag status blokir & revisi Preview 3 (Per Berkas)
            $st_p3_bim_clean = $latest_p3_bim ? strtolower($latest_p3_bim['status_pembimbing'] ?? 'pending') : null;
            $is_p3_bim_pending  = ($st_p3_bim_clean === 'pending');
            $is_p3_bim_revision = in_array($st_p3_bim_clean, ['revision', 'rejected', 'revisi']);

            $st_p3_sit_clean = $latest_p3_sit ? strtolower($latest_p3_sit['status_pembimbing'] ?? 'pending') : null;
            $is_p3_sit_pending  = ($st_p3_sit_clean === 'pending');
            $is_p3_sit_revision = in_array($st_p3_sit_clean, ['revision', 'rejected', 'revisi']);

            $st_p3_per_clean = $latest_p3_per ? strtolower($latest_p3_per['status_pembimbing'] ?? 'pending') : null;
            $is_p3_per_pending  = ($st_p3_per_clean === 'pending');
            $is_p3_per_revision = in_array($st_p3_per_clean, ['revision', 'rejected', 'revisi']);

            // Flag status blokir & revisi Sidang
            $st_sidang_raw   = !empty($latest_sidang) ? ($latest_sidang['status_pembimbing'] ?? 'Pending') : null;
            $st_sidang_clean = $st_sidang_raw ? strtolower($st_sidang_raw) : null;
            $is_sidang_pending  = ($st_sidang_clean === 'pending');
            $is_sidang_approved = ($st_sidang_clean === 'approved');
            $is_sidang_revision = in_array($st_sidang_clean, ['revision', 'rejected', 'revisi']);

            $is_ps_app      = !empty($is_ps_app);
            $is_ps_revision = !empty($is_ps_revision);
            $upload_count_ps = (int)($upload_count_ps ?? 0);

            if (!$is_p1_app) { $active_step = 'preview1'; }
            elseif (!$is_p2_app) { $active_step = 'preview2'; }
            elseif (!$is_p3_app) { $active_step = 'preview3'; }
            elseif (!$is_ps_app && empty($is_nilai_published)) { $active_step = 'persyaratan_sidang'; }
            else { $active_step = 'sidang'; }

            // ---------- Tim penguji (Penguji 1 & Penguji 2) ----------
            $hero_u1 = !empty($penguji_1) ? $penguji_1 : (!empty($penguji_ta) ? $penguji_ta : '');
            $hero_u2 = !empty($penguji_2) ? $penguji_2 : '';

            // ---------- Status bimbingan terkini ----------
            $stage_no  = $latest_p3 ? 3 : ($latest_p2 ? 2 : ($latest_p1 ? 1 : 0));
            $stage_row = [1 => $latest_p1, 2 => $latest_p2, 3 => $latest_p3][$stage_no] ?? null;
            $next_label = [1 => ' (Lanjut Preview 2)', 2 => ' (Lanjut Preview 3)', 3 => ' (Lanjut Persyaratan Sidang)'];

            $curr_status = 'Belum Memulai Bimbingan';
            $curr_tone   = 'text-white/70';
            $curr_icon   = 'bi-dash-circle';
            $curr_n1 = '';
            $curr_n2 = '';

            if ($is_p3_app) {
                if ($is_ps_app) {
                    $curr_status = 'Lulus Persyaratan Sidang (Siap Sidang TA)';
                    $curr_tone = 'text-emerald-300'; $curr_icon = 'bi-check-circle-fill';
                } elseif ($is_ps_revision) {
                    $curr_status = 'Persyaratan Sidang Perlu Revisi (Admin LAA)';
                    $curr_tone = 'text-rose-300'; $curr_icon = 'bi-x-circle-fill';
                } elseif ($upload_count_ps > 0) {
                    $curr_status = 'Persyaratan Sidang Diverifikasi Admin LAA';
                    $curr_tone = 'text-amber-200'; $curr_icon = 'bi-clock-fill';
                } else {
                    $curr_status = 'Lulus Tahap Preview 3 (Lanjut Persyaratan Sidang)';
                    $curr_tone = 'text-emerald-300'; $curr_icon = 'bi-check-circle-fill';
                }
            } elseif ($stage_row) {
                $is_curr_lulus = (!empty($stage_row['lulus_preview' . $stage_no]) && (string)$stage_row['lulus_preview' . $stage_no] === '1')
                              || (!empty($pendaftaran['lulus_preview' . $stage_no]) && (string)$pendaftaran['lulus_preview' . $stage_no] === '1');
                $st = $stage_row['status_pembimbing'] ?? '';
                if ($is_curr_lulus) {
                    $curr_status = 'Lulus Tahap Preview ' . $stage_no . $next_label[$stage_no];
                    $curr_tone = 'text-emerald-300'; $curr_icon = 'bi-check-circle-fill';
                } elseif ($st === 'Approved') {
                    $curr_status = 'Berkas Preview ' . $stage_no . ' Disetujui (Menunggu Lulus Tahap)';
                    $curr_tone = 'text-amber-200'; $curr_icon = 'bi-clock-fill';
                } elseif ($st === 'Revision') {
                    $curr_status = 'Preview ' . $stage_no . ' Revisi';
                    $curr_tone = 'text-rose-300'; $curr_icon = 'bi-x-circle-fill';
                } else {
                    $curr_status = 'Preview ' . $stage_no . ' Sedang Direview';
                    $curr_tone = 'text-amber-200'; $curr_icon = 'bi-clock-fill';
                }
                if ($st === 'Approved' || $st === 'Revision') {
                    $curr_n1 = trim(strip_tags($stage_row['catatan_pembimbing'] ?? '')) !== '' ? $stage_row['catatan_pembimbing'] : '';
                }
                $curr_n2 = trim(strip_tags($stage_row['catatan_pembimbing_2'] ?? '')) !== '' ? $stage_row['catatan_pembimbing_2'] : '';
            } elseif ($is_p2_app) {
                $curr_status = 'Lulus Tahap Preview 2 (Lanjut Preview 3)';
                $curr_tone = 'text-emerald-300'; $curr_icon = 'bi-check-circle-fill';
            } elseif ($is_p1_app) {
                $curr_status = 'Lulus Tahap Preview 1 (Lanjut Preview 2)';
                $curr_tone = 'text-emerald-300'; $curr_icon = 'bi-check-circle-fill';
            }

            // ---------- Sub-label & state milestone chip ----------
            $ms1_sub    = $is_p1_app ? 'Selesai' : ($upload_count_p1 > 0 ? 'Menunggu review' : 'Aktif');
            $ms2_sub    = $is_p2_app ? 'Selesai' : ($is_p1_app ? 'Terbuka' : 'Terkunci');
            $ms3_sub    = $is_p3_app ? 'Selesai' : ($is_p2_app ? 'Terbuka' : 'Terkunci');
            $ms_ps_sub  = $is_ps_app ? 'Selesai' : ($is_ps_revision ? 'Perlu revisi' : ($upload_count_ps > 0 ? 'Verifikasi LAA' : ($is_p3_app ? 'Terbuka' : 'Terkunci')));
            $ms_ps_open = ($is_p3_app || $is_ps_app);
            $ms4_sub    = !empty($is_nilai_published) ? 'Hasil terbit' : ($is_ps_app ? 'Siap daftar' : 'Terkunci');
            $ms4_open   = ($is_ps_app || !empty($is_nilai_published));
        ?>
        <script>window._statusNotes = <?= json_encode(['p1' => $curr_n1, 'p2' => $curr_n2]); ?>;</script>

        <!-- ==========================================================
             HERO CARD (COMPACT): Judul + Tim + Status + Milestone
             ========================================================== -->
        <div class="card-3d-orange rounded-3xl p-4 sm:p-5 relative overflow-hidden w-full shadow-xl text-white">
            <div class="absolute inset-0 pointer-events-none z-0 overflow-hidden rounded-3xl">
                <img src="<?= base_url('assets/images/background.png'); ?>" alt="FIK Building Illustration" class="w-full h-full object-cover object-[85%_center] opacity-75 saturate-110 contrast-105">
                <div class="absolute inset-0 bg-gradient-to-r from-[#9a3412]/95 via-[#ea580c]/85 to-[#c2410c]/70"></div>
            </div>

            <div class="relative z-10 space-y-3">

                <!-- ROW 1: Judul (kiri) + Tim (kanan) -->
                <div class="flex flex-col lg:flex-row lg:items-stretch gap-3">

                    <div class="lg:w-[44%] space-y-2.5 min-w-0">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white text-[11px] font-bold">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <i class="bi bi-mortarboard-fill text-amber-200"></i> Hub Bimbingan &amp; Evaluasi Preview TA
                        </div>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight leading-tight">Bimbingan Tugas Akhir</h1>

                        <?php if(!empty($pendaftaran['judul_1'])): ?>
                            <div class="px-3 py-2 bg-black/25 backdrop-blur-md rounded-xl border border-white/20 flex items-center gap-2.5" title="<?= htmlspecialchars($pendaftaran['judul_1']); ?>">
                                <i class="bi bi-bookmark-star-fill text-amber-300 text-base shrink-0"></i>
                                <div class="min-w-0">
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-amber-200 block">Judul Tugas Akhir Utama</span>
                                    <span class="text-xs font-semibold text-white leading-snug line-clamp-2 block"><?= htmlspecialchars($pendaftaran['judul_1']); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TIM PEMBIMBING & PENGUJI -->
                    <div class="flex-1 bg-black/25 backdrop-blur-xl rounded-2xl p-3 border border-white/20 space-y-2 min-w-0">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-white uppercase tracking-wider flex items-center gap-2">
                                <i class="bi bi-people-fill text-amber-300"></i> Tim Pembimbing &amp; Penguji
                            </span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/25 text-emerald-300 border border-emerald-400/40">
                                <i class="bi bi-patch-check-fill mr-0.5"></i> Aktif
                            </span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <div class="flex items-center gap-2 p-2 rounded-xl bg-white/10 border border-white/10 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-amber-400 to-orange-500 text-slate-950 flex items-center justify-center font-extrabold text-xs shrink-0">P1</div>
                                <div class="min-w-0">
                                    <span class="text-[9px] font-bold text-amber-200 uppercase tracking-wider block truncate">Pembimbing Utama</span>
                                    <h4 class="font-bold text-xs text-white truncate" title="<?= !empty($pembimbing_1) ? htmlspecialchars($pembimbing_1) : ''; ?>"><?= !empty($pembimbing_1) ? htmlspecialchars($pembimbing_1) : '<span class="italic text-white/60 font-medium">Belum Di-assign</span>'; ?></h4>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 p-2 rounded-xl bg-white/10 border border-white/10 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-white/20 text-white flex items-center justify-center font-bold text-xs shrink-0">P2</div>
                                <div class="min-w-0">
                                    <span class="text-[9px] font-bold text-orange-200 uppercase tracking-wider block truncate">Pembimbing Pendamping</span>
                                    <h4 class="font-bold text-xs text-white truncate" title="<?= !empty($pembimbing_2) ? htmlspecialchars($pembimbing_2) : ''; ?>"><?= !empty($pembimbing_2) ? htmlspecialchars($pembimbing_2) : '<span class="italic text-white/60 font-medium">Belum Di-assign</span>'; ?></h4>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 p-2 rounded-xl bg-white/10 border border-white/10 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-purple-500/40 text-purple-100 border border-purple-300/30 flex items-center justify-center font-bold text-xs shrink-0">U1</div>
                                <div class="min-w-0">
                                    <span class="text-[9px] font-bold text-purple-200 uppercase tracking-wider block truncate">Penguji 1 (Penilai P2)</span>
                                    <h4 class="font-bold text-xs text-white truncate" title="<?= !empty($hero_u1) ? htmlspecialchars($hero_u1) : ''; ?>"><?= !empty($hero_u1) ? htmlspecialchars($hero_u1) : '<span class="italic text-white/60 font-medium">Belum Di-assign</span>'; ?></h4>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 p-2 rounded-xl bg-white/10 border border-white/10 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-fuchsia-500/40 text-fuchsia-100 border border-fuchsia-300/30 flex items-center justify-center font-bold text-xs shrink-0">U2</div>
                                <div class="min-w-0">
                                    <span class="text-[9px] font-bold text-fuchsia-200 uppercase tracking-wider block truncate">Penguji 2</span>
                                    <h4 class="font-bold text-xs text-white truncate" title="<?= !empty($hero_u2) ? htmlspecialchars($hero_u2) : ''; ?>"><?= !empty($hero_u2) ? htmlspecialchars($hero_u2) : '<span class="italic text-white/60 font-medium">Belum Di-assign</span>'; ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ROW 2: Status Bimbingan Terkini + Milestone (satu baris, glass) -->
                <div class="flex flex-col lg:flex-row lg:items-stretch gap-3 pt-3 border-t border-white/15">

                    <!-- STATUS BIMBINGAN TERKINI -->
                    <div id="statusCardContainer" class="lg:w-[300px] shrink-0 flex items-center gap-2.5 px-3 py-2 rounded-2xl bg-black/25 backdrop-blur-md border border-white/20">
                        <i class="bi <?= $curr_icon ?> <?= $curr_tone ?> text-xl"></i>
                        <div class="min-w-0 flex-1">
                            <span class="text-[9px] font-bold uppercase tracking-wider text-amber-200 block">Status Bimbingan Terkini</span>
                            <h4 class="text-xs font-bold text-white leading-snug"><?= $curr_status ?></h4>
                            <?php if ($curr_n1 !== '' || $curr_n2 !== ''): ?>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    <?php if ($curr_n1 !== ''): ?>
                                        <button type="button" onclick="openStatusNote('p1')" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white/15 hover:bg-white/25 border border-white/20 text-[10px] font-bold text-white cursor-pointer"><i class="bi bi-chat-quote-fill"></i> Catatan P1</button>
                                    <?php endif; ?>
                                    <?php if ($curr_n2 !== ''): ?>
                                        <button type="button" onclick="openStatusNote('p2')" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white/15 hover:bg-white/25 border border-white/20 text-[10px] font-bold text-white cursor-pointer"><i class="bi bi-chat-quote-fill"></i> Catatan P2</button>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- MILESTONE BIMBINGAN & EVALUASI TA -->
                    <div class="flex-1 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2 min-w-0">

                        <div onclick="switchPreviewTab('preview1')" id="tabBtnPreview1" title="Proposal &amp; Bab 1–3 (Pembimbing 1)"
                             class="tab-card ms-chip <?= $active_step === 'preview1' ? 'tab-card-active' : '' ?>">
                            <div class="ms-ico w-8 h-8 rounded-lg <?= $is_p1_app ? 'bg-emerald-500' : 'bg-orange-500'; ?> text-white flex items-center justify-center text-sm shrink-0">
                                <i class="bi <?= $is_p1_app ? 'bi-check-lg' : 'bi-file-earmark-text'; ?>"></i>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold leading-tight truncate">Preview 1</h4>
                                <span class="ms-sub text-[10px] font-semibold block truncate"><?= $ms1_sub ?></span>
                            </div>
                        </div>

                        <div onclick="switchPreviewTab('preview2')" id="tabBtnPreview2" title="Progress Karya 50% (Dosen Penguji)"
                             class="tab-card ms-chip <?= !$is_p1_app ? 'ms-locked' : '' ?> <?= $active_step === 'preview2' ? 'tab-card-active' : '' ?>">
                            <div class="ms-ico w-8 h-8 rounded-lg <?= $is_p2_app ? 'bg-emerald-500' : ($is_p1_app ? 'bg-amber-500' : 'bg-white/20'); ?> text-white flex items-center justify-center text-sm shrink-0">
                                <i class="bi <?= $is_p2_app ? 'bi-check-lg' : ($is_p1_app ? 'bi-hammer' : 'bi-lock-fill'); ?>"></i>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold leading-tight truncate">Preview 2</h4>
                                <span class="ms-sub text-[10px] font-semibold block truncate"><?= $ms2_sub ?></span>
                            </div>
                        </div>

                        <div onclick="switchPreviewTab('preview3')" id="tabBtnPreview3" title="Pra-Sidang Naskah 100% (Pembimbing 1)"
                             class="tab-card ms-chip <?= !$is_p2_app ? 'ms-locked' : '' ?> <?= $active_step === 'preview3' ? 'tab-card-active' : '' ?>">
                            <div class="ms-ico w-8 h-8 rounded-lg <?= $is_p3_app ? 'bg-emerald-500' : ($is_p2_app ? 'bg-indigo-500' : 'bg-white/20'); ?> text-white flex items-center justify-center text-sm shrink-0">
                                <i class="bi <?= $is_p3_app ? 'bi-check-lg' : ($is_p2_app ? 'bi-journal-check' : 'bi-lock-fill'); ?>"></i>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold leading-tight truncate">Preview 3</h4>
                                <span class="ms-sub text-[10px] font-semibold block truncate"><?= $ms3_sub ?></span>
                            </div>
                        </div>

                        <div onclick="switchPreviewTab('persyaratan_sidang')" id="tabBtnPersyaratanSidang" title="Kelengkapan Berkas Persyaratan Sidang (Verifikasi Admin LAA)"
                             class="tab-card ms-chip <?= !$ms_ps_open ? 'ms-locked' : '' ?> <?= $active_step === 'persyaratan_sidang' ? 'tab-card-active' : '' ?>">
                            <div class="ms-ico w-8 h-8 rounded-lg <?= $is_ps_app ? 'bg-emerald-500' : ($ms_ps_open ? 'bg-teal-500' : 'bg-white/20'); ?> text-white flex items-center justify-center text-sm shrink-0">
                                <i class="bi <?= $is_ps_app ? 'bi-check-lg' : ($ms_ps_open ? 'bi-folder-check' : 'bi-lock-fill'); ?>"></i>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold leading-tight truncate">Persyaratan Sidang</h4>
                                <span class="ms-sub text-[10px] font-semibold block truncate"><?= $ms_ps_sub ?></span>
                            </div>
                        </div>

                        <div onclick="switchPreviewTab('sidang')" id="tabBtnSidang" title="Pendaftaran, Jadwal &amp; Hasil Sidang"
                             class="tab-card ms-chip <?= !$ms4_open ? 'ms-locked' : '' ?> <?= $active_step === 'sidang' ? 'tab-card-active' : '' ?>">
                            <div class="ms-ico w-8 h-8 rounded-lg <?= $ms4_open ? 'bg-emerald-500' : 'bg-white/20'; ?> text-white flex items-center justify-center text-sm shrink-0">
                                <i class="bi <?= !empty($is_nilai_published) ? 'bi-award-fill' : ($ms4_open ? 'bi-mortarboard-fill' : 'bi-lock-fill'); ?>"></i>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold leading-tight truncate">Sidang TA</h4>
                                <span class="ms-sub text-[10px] font-semibold block truncate"><?= $ms4_sub ?></span>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <!-- ================= TAB CONTENT PANEL: PREVIEW 1 ================= -->
        <div id="panelPreview1" class="tab-panel space-y-7 <?= $active_step === 'preview1' ? '' : 'hidden' ?>">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-7 w-full">
                <div class="lg:col-span-7 space-y-6">
                    <div class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-6 shadow-md shadow-orange-500/5">
                        <div class="border-b border-orange-100 pb-5">
                            <span class="text-xs font-bold uppercase tracking-wider text-orange-600 block mb-1">FORMULIR UNGGAH PREVIEW 1</span>
                            <h3 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                                <i class="bi bi-cloud-arrow-up-fill text-orange-500 text-2xl"></i> Upload Draft Proposal &amp; Bab 1–3
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600 font-normal mt-1.5">
                                Berkas akan ditinjau oleh <strong class="text-slate-900 font-semibold">Pembimbing 1 (<?= htmlspecialchars($pembimbing_1); ?>)</strong>. Format: <strong class="text-slate-900 font-semibold">PDF, DOCX, ZIP</strong> (Maks. 10MB).
                            </p>
                        </div>

                        <?php if(!$is_pembimbing_assigned): ?>
                        <div class="py-10 text-center bg-slate-50 border border-slate-200 rounded-3xl">
                            <i class="bi bi-person-fill-lock text-4xl text-slate-400 mb-3 block"></i>
                            <h4 class="font-bold text-lg text-slate-700">Tahap Bimbingan Belum Tersedia</h4>
                            <p class="text-slate-500 text-sm mt-2">Dosen Pembimbing 1 dan Pembimbing 2 Anda belum di-assign oleh Koordinator TA. Harap menunggu hingga pembimbing ditetapkan sebelum Anda dapat mulai mengunggah berkas.</p>
                        </div>
                        <?php elseif($is_p1_app): ?>
                        <div class="py-10 text-center bg-emerald-50 border border-emerald-200 rounded-3xl">
                            <i class="bi bi-lock-fill text-4xl text-emerald-500 mb-3 block"></i>
                            <h4 class="font-bold text-lg text-emerald-800">Tahap Terkunci (Selesai)</h4>
                            <p class="text-emerald-700 text-sm mt-2">Tahap ini telah disetujui oleh Pembimbing 1. Anda tidak dapat mengunggah ulang berkas. Silakan lanjut ke Preview 2.</p>
                        </div>
                        <?php elseif($is_p1_approved): ?>
                        <!-- Visual Cue: Berkas Telah Di-ACC (Menunggu Lulus Tahap) -->
                        <div class="card-3d-warm rounded-3xl p-6 sm:p-7 space-y-4 border-2 border-emerald-300 bg-emerald-50/60 shadow-md shadow-emerald-500/10">
                            <div class="flex items-center justify-between gap-3 border-b border-emerald-200 pb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center text-2xl shrink-0 box-3d shadow-md shadow-emerald-500/20">
                                        <i class="bi bi-patch-check-fill"></i>
                                    </div>
                                    <div>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider bg-emerald-200 text-emerald-800 mb-1">
                                            <i class="bi bi-check-circle-fill"></i> Berkas Disetujui (ACC)
                                        </span>
                                        <h4 class="font-black text-lg text-emerald-950">Draft Preview 1 Telah Disetujui</h4>
                                        <p class="text-xs text-emerald-800">Dosen Pembimbing 1 telah memberikan persetujuan (ACC). Menunggu pengesahan tahap untuk membuka Preview 2.</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1.5 rounded-xl bg-white border border-emerald-300 text-emerald-800 text-xs font-bold shrink-0 flex items-center gap-1.5 shadow-2xs">
                                    <i class="bi bi-lock-fill text-emerald-600"></i> Upload Selesai
                                </span>
                            </div>
                            <div class="p-3.5 bg-white rounded-2xl border border-emerald-200/80 flex items-center justify-between gap-3 text-xs">
                                <div class="flex items-center gap-2.5 truncate">
                                    <i class="bi bi-file-earmark-check-fill text-emerald-600 text-xl"></i>
                                    <span class="font-mono font-bold text-slate-800 truncate"><?= htmlspecialchars($latest_p1['file_draft'] ?? '-'); ?></span>
                                </div>
                                <a href="<?= base_url('uploads/preview_ta/' . rawurlencode($latest_p1['file_draft'] ?? '')); ?>" target="_blank" class="px-3 py-1 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-bold shrink-0 transition">
                                    Lihat Berkas
                                </a>
                            </div>
                        </div>
                        <?php elseif($is_p1_pending): ?>
                        <!-- Visual Cue: Berkas Sedang Ditinjau (Upload Terblokir) -->
                        <div class="card-3d-warm rounded-3xl p-6 sm:p-7 space-y-5 border-2 border-amber-300 bg-amber-50/50 shadow-md shadow-amber-500/10">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-amber-200 pb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-2xl shrink-0 box-3d shadow-md shadow-amber-500/20">
                                        <i class="bi bi-hourglass-split animate-pulse"></i>
                                    </div>
                                    <div>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-amber-200 text-amber-900 mb-1">
                                            <span class="w-2 h-2 rounded-full bg-amber-600 animate-ping"></span> Menunggu Peninjauan Dosen
                                        </span>
                                        <h4 class="font-black text-lg text-slate-900">Berkas Preview 1 Sedang Ditinjau</h4>
                                        <p class="text-xs text-slate-600">Ditinjau oleh: <strong class="text-slate-900"><?= htmlspecialchars($pembimbing_1); ?></strong></p>
                                    </div>
                                </div>
                                <span class="px-3 py-1.5 rounded-xl bg-white border border-amber-300 text-amber-900 text-xs font-bold shrink-0 self-start sm:self-auto flex items-center gap-1.5 shadow-2xs">
                                    <i class="bi bi-lock-fill text-amber-600"></i> Upload Terkunci
                                </span>
                            </div>

                            <div class="p-4 rounded-2xl bg-white border border-amber-200/80 space-y-2.5">
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Dokumen yang Telah Diunggah</span>
                                <div class="flex items-center justify-between gap-3 min-w-0">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-lg shrink-0">
                                            <i class="bi bi-file-earmark-pdf-fill"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <a href="<?= base_url('uploads/preview_ta/' . rawurlencode($latest_p1['file_draft'] ?? '')); ?>" target="_blank" class="font-bold text-slate-900 hover:text-orange-600 hover:underline text-sm truncate block">
                                                <?= htmlspecialchars($latest_p1['file_draft'] ?? '-'); ?>
                                            </a>
                                            <span class="text-[11px] text-slate-500">
                                                <i class="bi bi-clock mr-1"></i> Diunggah pada: <?= !empty($latest_p1['created_at']) ? date('d M Y, H:i', strtotime($latest_p1['created_at'])) . ' WIB' : '-'; ?>
                                            </span>
                                        </div>
                                    </div>
                                    <a href="<?= base_url('uploads/preview_ta/' . rawurlencode($latest_p1['file_draft'] ?? '')); ?>" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs shrink-0 transition flex items-center gap-1.5">
                                        <i class="bi bi-download"></i> Unduh
                                    </a>
                                </div>
                                <?php if (!empty($latest_p1['catatan_mahasiswa'])): ?>
                                    <div class="pt-2 border-t border-slate-100 text-xs text-slate-600">
                                        <span class="font-semibold text-slate-700">Catatan Anda:</span>
                                        <p class="italic text-slate-500 mt-0.5"><?= htmlspecialchars($latest_p1['catatan_mahasiswa']); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="p-4 rounded-2xl bg-amber-100/70 border border-amber-300 text-amber-900 text-xs leading-relaxed flex items-start gap-2.5">
                                <i class="bi bi-info-circle-fill text-amber-600 text-base shrink-0 mt-0.5"></i>
                                <div>
                                    <strong class="font-bold block mb-0.5">Pengunggahan Berkas Dikunci Sementara:</strong>
                                    Anda telah mengunggah berkas untuk tahap Preview 1. Pengunggahan baru diblokir sementara hingga Pembimbing 1 memberikan persetujuan (ACC) atau catatan revisi.
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <?php if ($is_p1_revision): ?>
                        <!-- Visual Cue: Dosen Meminta Revisi — Upload Ulang Dibuka -->
                        <div class="p-5 rounded-2xl bg-rose-50 border-2 border-rose-300 text-rose-950 space-y-3 mb-6">
                            <div class="flex items-center justify-between gap-3">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-rose-200 text-rose-800">
                                    <i class="bi bi-arrow-repeat text-rose-600"></i> Perlu Revisi — Unggah Ulang Dibuka
                                </span>
                                <span class="text-[11px] text-rose-700 font-semibold">
                                    Status: Perbaikan Naskah
                                </span>
                            </div>
                            <div class="space-y-1">
                                <h4 class="font-black text-sm text-rose-900">Catatan Revisi dari Pembimbing 1:</h4>
                                <div class="p-3 bg-white rounded-xl border border-rose-200 text-xs leading-relaxed text-slate-800 italic">
                                    <?= !empty($latest_p1['catatan_pembimbing']) ? nl2br(htmlspecialchars(strip_tags($latest_p1['catatan_pembimbing']))) : 'Silakan lakukan revisi sesuai arahan pembimbing.'; ?>
                                </div>
                            </div>
                            <p class="text-xs text-rose-800 font-medium">
                                Silakan perbaiki naskah draft Bab 1–3 Anda, lalu unggah dokumen perbaikan terbaru melalui formulir di bawah ini.
                            </p>
                        </div>
                        <?php endif; ?>

                        <?= form_open_multipart('mahasiswa/upload_preview', ['id' => 'formUploadPreview1', 'class' => 'space-y-6']); ?>
                            <input type="hidden" name="tahap_preview" value="Preview 1">
                            
                            <div>
                                <label class="block text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider mb-2.5">
                                    Berkas Draft Laporan / Proposal TA <?= $is_p1_revision ? '(Revisi)' : ''; ?> <span class="text-rose-500">*</span>
                                </label>
                                
                                <div class="drop-zone relative border-2 border-dashed border-orange-300 hover:border-orange-500 bg-orange-50/30 hover:bg-orange-50/60 rounded-3xl p-8 sm:p-10 text-center transition-all cursor-pointer group" id="dropZoneP1">
                                    <input type="file" name="file_draft" id="fileDraftP1" accept=".pdf,.docx,.zip" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    
                                    <div class="space-y-3.5 pointer-events-none">
                                        <div class="w-16 h-16 sm:w-18 sm:h-18 rounded-3xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white group-hover:scale-105 flex items-center justify-center text-3xl mx-auto transition-transform box-3d shadow-md shadow-orange-500/20">
                                            <i class="bi bi-file-earmark-arrow-up-fill"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm sm:text-base font-bold text-slate-900">
                                                Klik untuk memilih file atau seret &amp; lepas ke sini
                                            </p>
                                            <p class="text-xs text-slate-500 font-medium mt-1">
                                                PDF, DOCX, atau ZIP (Maksimal ukuran: 10MB)
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="fileBadgeP1" class="hidden mt-3 p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs sm:text-sm font-bold flex items-center justify-between shadow-xs">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-sm shrink-0 box-3d">
                                            <i class="bi bi-file-earmark-check-fill"></i>
                                        </div>
                                        <span id="fileNameP1" class="truncate font-mono">draft.pdf</span>
                                    </div>
                                    <span id="fileSizeP1" class="text-xs text-emerald-800 font-bold shrink-0 ml-3 bg-white px-3 py-1 rounded-xl border border-emerald-200">2.4 MB</span>
                                </div>
                            </div>

                            <div>
                                <label for="catatanP1" class="block text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider mb-2.5">
                                    Catatan Progres Bimbingan <?= $is_p1_revision ? '(Perbaikan Revisi)' : ''; ?> <span class="text-slate-400 font-normal normal-case">(Opsional)</span>
                                </label>
                                <textarea name="catatan_mahasiswa" id="catatanP1" rows="3" class="w-full p-4 text-sm rounded-2xl border border-slate-200 focus:ring-4 focus:ring-orange-400/20 focus:border-orange-500 outline-none resize-none text-slate-900 placeholder:text-slate-400 transition bg-white font-medium" placeholder="Contoh: Mengunggah draft revisi Bab 1 s/d Bab 3 sesuai arahan Pembimbing 1..."></textarea>
                            </div>

                            <div class="pt-2">
                                <button type="submit" class="w-full py-4 px-8 rounded-2xl bg-gradient-to-r from-orange-600 via-orange-500 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white font-bold text-sm sm:text-base shadow-lg shadow-orange-500/25 transition flex items-center justify-center gap-3 cursor-pointer hover:scale-[1.01] active:scale-[0.99] box-3d">
                                    <i class="bi bi-cloud-arrow-up-fill text-lg"></i>
                                    <span><?= $is_p1_revision ? 'Kirim Berkas Revisi Preview 1' : 'Kirim Berkas Draft Preview 1'; ?></span>
                                </button>
                            </div>
                        <?= form_close(); ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="lg:col-span-5 space-y-6">
                    <div class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-6 shadow-md shadow-orange-500/5">
                        <div class="border-b border-orange-100 pb-4">
                            <span class="text-xs font-bold uppercase tracking-wider text-orange-600 block mb-1">GATEKEEPER MILESTONE</span>
                            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2.5">
                                <i class="bi bi-shield-lock-fill text-orange-500 text-xl"></i> Syarat Melangkah ke Preview 2
                            </h3>
                        </div>

                        <div class="space-y-4">
                            <?php $req1_passed = ($upload_count_p1 > 0); ?>
                            <div class="p-4 rounded-2xl border <?= $req1_passed ? 'border-emerald-300 bg-emerald-50/60' : 'border-slate-200 bg-slate-50/60'; ?> flex items-start gap-3.5 transition-colors">
                                <div class="w-9 h-9 rounded-xl <?= $req1_passed ? 'bg-emerald-500 text-white shadow-xs' : 'bg-slate-200 text-slate-400'; ?> flex items-center justify-center text-base font-bold shrink-0 mt-0.5 box-3d">
                                    <i class="bi <?= $req1_passed ? 'bi-check-lg' : 'bi-dash'; ?>"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-bold text-sm text-slate-900">Minimal 1 Kali Upload Berkas</h4>
                                    <p class="text-xs text-slate-500 font-medium mt-0.5">
                                        <?= $req1_passed ? "Sudah mengunggah {$upload_count_p1} kali berkas draft." : "Wajib mengunggah minimal 1 kali berkas draft."; ?>
                                    </p>
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl border <?= $is_p1_app ? 'border-emerald-300 bg-emerald-50/60' : 'border-slate-200 bg-slate-50/60'; ?> flex items-start gap-3.5 transition-colors">
                                <div class="w-9 h-9 rounded-xl <?= $is_p1_app ? 'bg-emerald-500 text-white shadow-xs' : 'bg-slate-200 text-slate-400'; ?> flex items-center justify-center text-base font-bold shrink-0 mt-0.5 box-3d">
                                    <i class="bi <?= $is_p1_app ? 'bi-check-lg' : 'bi-dash'; ?>"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-bold text-sm text-slate-900">Persetujuan Pembimbing 1</h4>
                                    <p class="text-xs text-slate-500 font-medium mt-0.5">
                                        <?= $is_p1_app ? "Draft disetujui untuk melangkah ke Preview 2." : "Menunggu peninjauan &amp; persetujuan Pembimbing 1."; ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <?php if($req1_passed && $is_p1_app): ?>
                                <button type="button" onclick="switchPreviewTab('preview2')" class="w-full py-4 px-6 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md transition flex items-center justify-center gap-2.5 box-3d hover:scale-105 active:scale-95 cursor-pointer">
                                    <i class="bi bi-arrow-right-circle-fill text-base"></i>
                                    <span>Buka Tahap Preview 2</span>
                                </button>
                            <?php else: ?>
                                <div class="w-full py-4 px-6 rounded-2xl bg-slate-100 text-slate-400 font-bold text-xs sm:text-sm border border-slate-200 flex items-center justify-center gap-2.5 cursor-not-allowed opacity-80" title="Selesaikan syarat di atas untuk membuka tahap Preview 2">
                                    <i class="bi bi-lock-fill text-sm"></i>
                                    <span>Tahap Preview 2 Masih Terkunci</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Riwayat Upload Berkas Preview 1 -->
            <div class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-6 w-full shadow-md shadow-orange-500/5">
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-orange-100 pb-5">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-orange-600 block mb-1">LOG AKTIVITAS PREVIEW 1</span>
                        <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2.5">
                            <i class="bi bi-clock-history text-orange-500 text-xl"></i> Riwayat Pengajuan Berkas Preview 1
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-500 font-normal mt-0.5">Daftar draft yang diajukan ke Pembimbing 1 beserta catatan review.</p>
                    </div>
                    <span class="text-xs font-bold px-4 py-2 rounded-2xl bg-slate-100 text-slate-700 border border-slate-200 shadow-2xs">
                        Total: <?= count($riwayat_preview1); ?> Dokumen
                    </span>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-2xs mt-4">
                    <table class="responsive-log-table w-full text-left border-collapse text-xs sm:text-sm">
                        <thead class="bg-slate-50 text-slate-700 font-bold uppercase py-3.5 border-b border-slate-200">
                            <tr>
                                <th class="py-4 px-6 w-14 text-center">#</th>
                                <th class="py-4 px-6">File Draft</th>
                                <th class="py-4 px-6">Catatan Anda</th>
                                <th class="py-4 px-6">Waktu Upload</th>
                                <th class="py-4 px-6 text-center">Status</th>
                                <th class="py-4 px-6 text-center">Komentar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium" id="logTablePreview1">
                            <tr><td colspan="6" class="text-center py-8 text-slate-500"><i class="bi bi-arrow-repeat animate-spin mr-2 text-lg"></i> Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- ================= TAB CONTENT PANEL: PREVIEW 2 ================= -->
        <div id="panelPreview2" class="tab-panel hidden space-y-7">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-7 w-full">
                <div class="lg:col-span-7 space-y-6">
                    <div class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-6 shadow-md shadow-amber-500/5">
                        <div class="border-b border-amber-100 pb-5">
                            <span class="text-xs font-bold uppercase tracking-wider text-amber-700 block mb-1">FORMULIR UNGGAH PREVIEW 2</span>
                            <h3 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                                <i class="bi bi-hammer text-amber-500 text-xl"></i> Upload Progress Produk &amp; Prototype 50%
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600 font-normal mt-1.5">
                                Berkas akan dievaluasi oleh <strong class="text-slate-900 font-semibold">Dosen Penguji (<?= htmlspecialchars($penguji_ta); ?>)</strong>.
                            </p>
                        </div>

                        <?php if(!$is_p1_app): ?>
                        <div class="py-10 text-center bg-slate-50 border border-slate-200 rounded-3xl">
                            <i class="bi bi-lock-fill text-4xl text-slate-400 mb-3 block"></i>
                            <h4 class="font-bold text-lg text-slate-700">Tahap Terkunci</h4>
                            <p class="text-slate-500 text-sm mt-2">Anda harus mendapatkan persetujuan (ACC) dari Pembimbing 1 di tahap Preview 1 sebelum dapat mengunggah berkas di tahap ini.</p>
                        </div>
                        <?php elseif($is_p2_app): ?>
                        <div class="py-10 text-center bg-emerald-50 border border-emerald-200 rounded-3xl">
                            <i class="bi bi-lock-fill text-4xl text-emerald-500 mb-3 block"></i>
                            <h4 class="font-bold text-lg text-emerald-800">Tahap Terkunci (Selesai)</h4>
                            <p class="text-emerald-700 text-sm mt-2">Tahap ini telah disetujui oleh Penguji. Anda tidak dapat mengunggah ulang berkas. Silakan lanjut ke Preview 3.</p>
                        </div>
                        <?php elseif($is_p2_approved): ?>
                        <!-- Visual Cue: Berkas Telah Di-ACC (Menunggu Lulus Tahap) -->
                        <div class="card-3d-warm rounded-3xl p-6 sm:p-7 space-y-4 border-2 border-emerald-300 bg-emerald-50/60 shadow-md shadow-emerald-500/10">
                            <div class="flex items-center justify-between gap-3 border-b border-emerald-200 pb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center text-2xl shrink-0 box-3d shadow-md shadow-emerald-500/20">
                                        <i class="bi bi-patch-check-fill"></i>
                                    </div>
                                    <div>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider bg-emerald-200 text-emerald-800 mb-1">
                                            <i class="bi bi-check-circle-fill"></i> Berkas Disetujui (ACC)
                                        </span>
                                        <h4 class="font-black text-lg text-emerald-950">Berkas Preview 2 Telah Disetujui</h4>
                                        <p class="text-xs text-emerald-800">Dosen Penguji telah menyetujui berkas progress Anda. Menunggu evaluasi kelulusan tahap untuk membuka Preview 3.</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1.5 rounded-xl bg-white border border-emerald-300 text-emerald-800 text-xs font-bold shrink-0 flex items-center gap-1.5 shadow-2xs">
                                    <i class="bi bi-lock-fill text-emerald-600"></i> Upload Selesai
                                </span>
                            </div>
                            <div class="p-3.5 bg-white rounded-2xl border border-emerald-200/80 flex items-center justify-between gap-3 text-xs">
                                <div class="flex items-center gap-2.5 truncate">
                                    <i class="bi bi-file-earmark-check-fill text-emerald-600 text-xl"></i>
                                    <span class="font-mono font-bold text-slate-800 truncate"><?= htmlspecialchars($latest_p2['file_draft'] ?? '-'); ?></span>
                                </div>
                                <a href="<?= base_url('uploads/preview_ta/' . rawurlencode($latest_p2['file_draft'] ?? '')); ?>" target="_blank" class="px-3 py-1 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-bold shrink-0 transition">
                                    Lihat Berkas
                                </a>
                            </div>
                        </div>
                        <?php elseif($is_p2_pending): ?>
                        <!-- Visual Cue: Berkas Sedang Ditinjau (Upload Terblokir) -->
                        <div class="card-3d-warm rounded-3xl p-6 sm:p-7 space-y-5 border-2 border-amber-300 bg-amber-50/50 shadow-md shadow-amber-500/10">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-amber-200 pb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-2xl shrink-0 box-3d shadow-md shadow-amber-500/20">
                                        <i class="bi bi-hourglass-split animate-pulse"></i>
                                    </div>
                                    <div>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-amber-200 text-amber-900 mb-1">
                                            <span class="w-2 h-2 rounded-full bg-amber-600 animate-ping"></span> Sedang Dievaluasi Penguji
                                        </span>
                                        <h4 class="font-black text-lg text-slate-900">Berkas Preview 2 Sedang Dievaluasi</h4>
                                        <p class="text-xs text-slate-600">Dievaluasi oleh: <strong class="text-slate-900"><?= htmlspecialchars($penguji_ta); ?></strong></p>
                                    </div>
                                </div>
                                <span class="px-3 py-1.5 rounded-xl bg-white border border-amber-300 text-amber-900 text-xs font-bold shrink-0 self-start sm:self-auto flex items-center gap-1.5 shadow-2xs">
                                    <i class="bi bi-lock-fill text-amber-600"></i> Upload Terkunci
                                </span>
                            </div>

                            <div class="p-4 rounded-2xl bg-white border border-amber-200/80 space-y-2.5">
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Dokumen Progress yang Telah Diunggah</span>
                                <div class="flex items-center justify-between gap-3 min-w-0">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg shrink-0">
                                            <i class="bi bi-file-earmark-zip-fill"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <a href="<?= base_url('uploads/preview_ta/' . rawurlencode($latest_p2['file_draft'] ?? '')); ?>" target="_blank" class="font-bold text-slate-900 hover:text-amber-700 hover:underline text-sm truncate block">
                                                <?= htmlspecialchars($latest_p2['file_draft'] ?? '-'); ?>
                                            </a>
                                            <span class="text-[11px] text-slate-500">
                                                <i class="bi bi-clock mr-1"></i> Diunggah pada: <?= !empty($latest_p2['created_at']) ? date('d M Y, H:i', strtotime($latest_p2['created_at'])) . ' WIB' : '-'; ?>
                                            </span>
                                        </div>
                                    </div>
                                    <a href="<?= base_url('uploads/preview_ta/' . rawurlencode($latest_p2['file_draft'] ?? '')); ?>" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs shrink-0 transition flex items-center gap-1.5">
                                        <i class="bi bi-download"></i> Unduh
                                    </a>
                                </div>
                                <?php if (!empty($latest_p2['catatan_mahasiswa'])): ?>
                                    <div class="pt-2 border-t border-slate-100 text-xs text-slate-600">
                                        <span class="font-semibold text-slate-700">Catatan / Link Karya:</span>
                                        <p class="italic text-slate-500 mt-0.5"><?= htmlspecialchars($latest_p2['catatan_mahasiswa']); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="p-4 rounded-2xl bg-amber-100/70 border border-amber-300 text-amber-900 text-xs leading-relaxed flex items-start gap-2.5">
                                <i class="bi bi-info-circle-fill text-amber-600 text-base shrink-0 mt-0.5"></i>
                                <div>
                                    <strong class="font-bold block mb-0.5">Pengunggahan Berkas Dikunci Sementara:</strong>
                                    Anda telah mengunggah berkas untuk tahap Preview 2. Pengunggahan baru diblokir sementara sampai Dosen Penguji menyelesaikan evaluasi berkas ini (ACC atau Revisi).
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <?php if ($is_p2_revision): ?>
                        <!-- Visual Cue: Dosen Penguji Meminta Revisi — Upload Ulang Dibuka -->
                        <div class="p-5 rounded-2xl bg-rose-50 border-2 border-rose-300 text-rose-950 space-y-3 mb-6">
                            <div class="flex items-center justify-between gap-3">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-rose-200 text-rose-800">
                                    <i class="bi bi-arrow-repeat text-rose-600"></i> Perlu Revisi Progress — Unggah Ulang Dibuka
                                </span>
                                <span class="text-[11px] text-rose-700 font-semibold">
                                    Status: Perbaikan Berkas Preview 2
                                </span>
                            </div>
                            <div class="space-y-1">
                                <h4 class="font-black text-sm text-rose-900">Catatan Evaluasi Penguji:</h4>
                                <div class="p-3 bg-white rounded-xl border border-rose-200 text-xs leading-relaxed text-slate-800 italic">
                                    <?= !empty($latest_p2['catatan_penguji_1']) ? nl2br(htmlspecialchars(strip_tags($latest_p2['catatan_penguji_1']))) : (!empty($latest_p2['catatan_pembimbing']) ? nl2br(htmlspecialchars(strip_tags($latest_p2['catatan_pembimbing']))) : 'Silakan lakukan revisi sesuai catatan evaluasi.'); ?>
                                </div>
                            </div>
                            <p class="text-xs text-rose-800 font-medium">
                                Silakan perbaiki dokumen progress Bab 4 atau karya/prototype Anda, lalu unggah dokumen perbaikan terbaru melalui formulir di bawah ini.
                            </p>
                        </div>
                        <?php endif; ?>

                        <?= form_open_multipart('mahasiswa/upload_preview', ['id' => 'formUploadPreview2', 'class' => 'space-y-6']); ?>
                            <input type="hidden" name="tahap_preview" value="Preview 2">
                            
                            <div>
                                <label class="block text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider mb-2.5">
                                    Berkas Progress Bab 4 &amp; Link Prototype / Demo <?= $is_p2_revision ? '(Revisi)' : ''; ?> <span class="text-rose-500">*</span>
                                </label>
                                
                                <div class="drop-zone relative border-2 border-dashed border-amber-300 hover:border-amber-500 bg-amber-50/30 hover:bg-amber-50/60 rounded-3xl p-8 sm:p-10 text-center transition-all cursor-pointer group" id="dropZoneP2">
                                    <input type="file" name="file_draft" id="fileDraftP2" accept=".pdf,.docx,.zip" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <div class="space-y-3.5 pointer-events-none">
                                        <div class="w-16 h-16 sm:w-18 sm:h-18 rounded-3xl bg-gradient-to-tr from-amber-500 to-orange-400 text-white group-hover:scale-105 flex items-center justify-center text-3xl mx-auto transition-transform box-3d shadow-md shadow-amber-500/20">
                                            <i class="bi bi-file-earmark-zip-fill"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm sm:text-base font-bold text-slate-900">
                                                Pilih file dokumen / laporan progress Preview 2
                                            </p>
                                            <p class="text-xs text-slate-500 font-medium mt-1">PDF, DOCX, atau ZIP (Maksimal 10MB)</p>
                                        </div>
                                    </div>
                                </div>
                                <div id="fileBadgeP2" class="hidden mt-3 p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs sm:text-sm font-bold flex items-center justify-between shadow-xs">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-sm shrink-0 box-3d">
                                            <i class="bi bi-file-earmark-check-fill"></i>
                                        </div>
                                        <span id="fileNameP2" class="truncate font-mono">draft.pdf</span>
                                    </div>
                                    <span id="fileSizeP2" class="text-xs text-emerald-800 font-bold shrink-0 ml-3 bg-white px-3 py-1 rounded-xl border border-emerald-200">2.4 MB</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider mb-2.5">
                                    Catatan Progress Karya / Link Demo Video &amp; Figma <?= $is_p2_revision ? '(Perbaikan Revisi)' : ''; ?>
                                </label>
                                <textarea name="catatan_mahasiswa" rows="3" class="w-full p-4 text-sm rounded-2xl border border-slate-200 focus:ring-4 focus:ring-amber-400/20 focus:border-amber-500 outline-none resize-none text-slate-900 placeholder:text-slate-400 transition bg-white font-medium" placeholder="Tuliskan tautan prototype Figma, link repositori GitHub, atau ringkasan progres Bab 4..."></textarea>
                            </div>

                            <div class="pt-2">
                                <button type="submit" class="w-full py-4 px-8 rounded-2xl bg-gradient-to-r from-amber-600 via-amber-500 to-orange-500 hover:from-amber-700 hover:to-orange-600 text-white font-bold text-sm sm:text-base shadow-lg shadow-amber-500/25 transition flex items-center justify-center gap-3 cursor-pointer box-3d">
                                    <i class="bi bi-cloud-arrow-up-fill text-lg"></i>
                                    <span><?= $is_p2_revision ? 'Kirim Berkas Revisi Preview 2' : 'Kirim Berkas Preview 2'; ?></span>
                                </button>
                            </div>
                        <?= form_close(); ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="lg:col-span-5 space-y-6">
                    <div class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-5 shadow-md shadow-amber-500/5">
                        <div class="border-b border-amber-100 pb-4">
                            <span class="text-xs font-bold uppercase tracking-wider text-amber-700 block mb-1">PENGUJI EVALUASI</span>
                            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2.5">
                                <i class="bi bi-person-check-fill text-amber-600 text-xl"></i> Dosen Penguji Preview 2
                            </h3>
                        </div>
                        <div class="p-5 rounded-2xl bg-amber-50/70 border border-amber-200 space-y-2">
                            <h4 class="font-bold text-base text-slate-900"><?= htmlspecialchars($penguji_ta); ?></h4>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-normal">
                                Dosen penguji bertugas mengevaluasi kelayakan teknis rancangan produk dan metodologi sebelum Anda diperbolehkan menyusun draft laporan lengkap di Preview 3.
                            </p>
                        </div>
                    </div>

                    <!-- ================= CONTAINER CARD DE PREVIEW 2 ================= -->
                    <div id="deContainerPreview2" class="space-y-6 <?= (!empty($de_detail['is_de']) ? '' : 'hidden'); ?>">
                        <!-- Card Jadwal Desk Evaluation (DE) -->
                        <div class="card-3d-warm rounded-3xl p-7 sm:p-8 space-y-4 shadow-md shadow-indigo-500/5 border-2 border-indigo-200/80 bg-gradient-to-b from-indigo-50/40 to-white">
                            <div class="border-b border-indigo-100 pb-3 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-700 block mb-0.5">JADWAL PELAKSANAAN</span>
                                    <h3 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
                                        <i class="bi bi-calendar-event-fill text-indigo-600"></i> Desk Evaluation (DE)
                                    </h3>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-indigo-100 text-indigo-800 border border-indigo-200 uppercase">Tahap DE</span>
                            </div>

                            <div class="space-y-3 text-xs sm:text-sm">
                                <div class="flex items-start gap-3 p-3 bg-white/90 rounded-2xl border border-indigo-100">
                                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 text-base">
                                        <i class="bi bi-calendar3"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider block">Tanggal & Waktu</span>
                                        <span id="deJadwalWaktuText" class="font-bold text-slate-800">
                                            <?php
                                                $tgl_de = !empty($de_detail['tanggal_presentasi']) ? date('d M Y', strtotime($de_detail['tanggal_presentasi'])) : null;
                                                $jam_de = !empty($de_detail['waktu_presentasi']) ? substr($de_detail['waktu_presentasi'], 0, 5) . ' WIB' : null;
                                                if ($tgl_de && $jam_de) echo htmlspecialchars($tgl_de . ' • ' . $jam_de);
                                                elseif ($tgl_de) echo htmlspecialchars($tgl_de);
                                                else echo '<span class="text-slate-400 italic">Belum Dijadwalkan</span>';
                                            ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3 p-3 bg-white/90 rounded-2xl border border-indigo-100">
                                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 text-base">
                                        <i class="bi bi-geo-alt-fill"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider block">Ruangan / Tempat</span>
                                        <span id="deJadwalRuangText" class="font-bold text-slate-800">
                                            <?= !empty($de_detail['ruang_sidang']) ? htmlspecialchars($de_detail['ruang_sidang']) : '<span class="text-slate-400 italic">Belum Ditentukan</span>'; ?>
                                        </span>
                                    </div>
                                </div>

                                <?php if (!empty($de_detail['link_presentasi'])): ?>
                                <div id="deJadwalLinkWrap" class="p-3 bg-indigo-50/70 rounded-2xl border border-indigo-200 flex items-center justify-between gap-2">
                                    <div class="min-w-0 flex-1 truncate text-xs text-indigo-900 font-semibold">
                                        <i class="bi bi-camera-video-fill text-indigo-600 mr-1.5"></i>
                                        <span id="deJadwalLinkText" class="truncate"><?= htmlspecialchars($de_detail['link_presentasi']); ?></span>
                                    </div>
                                    <a id="deJadwalLinkBtn" href="<?= htmlspecialchars($de_detail['link_presentasi']); ?>" target="_blank" rel="noopener noreferrer"
                                        class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shrink-0 inline-flex items-center gap-1 shadow-xs transition">
                                        <i class="bi bi-box-arrow-up-right"></i> Buka Link
                                    </a>
                                </div>
                                <?php else: ?>
                                <div id="deJadwalLinkWrap" class="hidden p-3 bg-indigo-50/70 rounded-2xl border border-indigo-200 items-center justify-between gap-2">
                                    <div class="min-w-0 flex-1 truncate text-xs text-indigo-900 font-semibold">
                                        <i class="bi bi-camera-video-fill text-indigo-600 mr-1.5"></i>
                                        <span id="deJadwalLinkText" class="truncate">-</span>
                                    </div>
                                    <a id="deJadwalLinkBtn" href="#" target="_blank" rel="noopener noreferrer"
                                        class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shrink-0 inline-flex items-center gap-1 shadow-xs transition">
                                        <i class="bi bi-box-arrow-up-right"></i> Buka Link
                                    </a>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($de_detail['komentar_kelayakan2'])): ?>
                                <div id="deJadwalCatatanWrap" class="p-3 bg-slate-50 rounded-2xl border border-slate-200">
                                    <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider block mb-0.5">Catatan Penguji</span>
                                    <p id="deJadwalCatatanText" class="text-xs text-slate-700 leading-relaxed italic"><?= nl2br(htmlspecialchars($de_detail['komentar_kelayakan2'])); ?></p>
                                </div>
                                <?php else: ?>
                                <div id="deJadwalCatatanWrap" class="hidden p-3 bg-slate-50 rounded-2xl border border-slate-200">
                                    <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider block mb-0.5">Catatan Penguji</span>
                                    <p id="deJadwalCatatanText" class="text-xs text-slate-700 leading-relaxed italic">-</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Card Nilai Desk Evaluation (DE) -->
                        <div class="card-3d-warm rounded-3xl p-7 sm:p-8 space-y-4 shadow-md shadow-orange-500/5 border-2 border-amber-200/80 bg-gradient-to-b from-amber-50/30 to-white">
                            <div class="border-b border-amber-100 pb-3 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-700 block mb-0.5">EVALUASI 4 DOSEN</span>
                                    <h3 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
                                        <i class="bi bi-award-fill text-amber-600"></i> Nilai Desk Evaluation
                                    </h3>
                                </div>
                                <span class="text-[10px] font-bold text-slate-500">Syarat: Rata-rata &gt; 60</span>
                            </div>

                            <div class="grid grid-cols-2 gap-2.5">
                                <div class="p-3 bg-white rounded-2xl border border-slate-200 shadow-2xs">
                                    <span class="text-[10px] text-slate-500 font-extrabold uppercase tracking-wider block truncate" title="Pembimbing 1">P1 (<?= htmlspecialchars($pembimbing_1 ?: 'Pembimbing 1'); ?>)</span>
                                    <span id="deNilaiP1" class="text-lg font-black text-slate-900 block mt-1">
                                        <?= (isset($de_detail['nilai_pembimbing1']) && $de_detail['nilai_pembimbing1'] !== null) ? htmlspecialchars($de_detail['nilai_pembimbing1']) : '<span class="text-slate-300 text-xs font-bold italic">Belum</span>'; ?>
                                    </span>
                                </div>
                                <div class="p-3 bg-white rounded-2xl border border-slate-200 shadow-2xs">
                                    <span class="text-[10px] text-slate-500 font-extrabold uppercase tracking-wider block truncate" title="Pembimbing 2">P2 (<?= htmlspecialchars($pembimbing_2 ?: 'Pembimbing 2'); ?>)</span>
                                    <span id="deNilaiP2" class="text-lg font-black text-slate-900 block mt-1">
                                        <?= (isset($de_detail['nilai_pembimbing2']) && $de_detail['nilai_pembimbing2'] !== null) ? htmlspecialchars($de_detail['nilai_pembimbing2']) : '<span class="text-slate-300 text-xs font-bold italic">Belum</span>'; ?>
                                    </span>
                                </div>
                                <div class="p-3 bg-white rounded-2xl border border-slate-200 shadow-2xs">
                                    <span class="text-[10px] text-slate-500 font-extrabold uppercase tracking-wider block truncate" title="Penguji 1">U1 (<?= htmlspecialchars($penguji_1 ?: 'Penguji 1'); ?>)</span>
                                    <span id="deNilaiU1" class="text-lg font-black text-slate-900 block mt-1">
                                        <?= (isset($de_detail['nilai_penguji1']) && $de_detail['nilai_penguji1'] !== null) ? htmlspecialchars($de_detail['nilai_penguji1']) : '<span class="text-slate-300 text-xs font-bold italic">Belum</span>'; ?>
                                    </span>
                                </div>
                                <div class="p-3 bg-white rounded-2xl border border-slate-200 shadow-2xs">
                                    <span class="text-[10px] text-slate-500 font-extrabold uppercase tracking-wider block truncate" title="Penguji 2">U2 (<?= htmlspecialchars($penguji_2 ?: 'Penguji 2'); ?>)</span>
                                    <span id="deNilaiU2" class="text-lg font-black text-slate-900 block mt-1">
                                        <?= (isset($de_detail['nilai_penguji2']) && $de_detail['nilai_penguji2'] !== null) ? htmlspecialchars($de_detail['nilai_penguji2']) : '<span class="text-slate-300 text-xs font-bold italic">Belum</span>'; ?>
                                    </span>
                                </div>
                            </div>

                            <div class="p-4 bg-gradient-to-r from-amber-500/10 via-orange-500/10 to-transparent rounded-2xl border border-amber-300/80 flex items-center justify-between gap-3">
                                <div>
                                    <span class="text-[10px] text-slate-500 font-extrabold uppercase tracking-wider block">Nilai Rata-rata</span>
                                    <span id="deNilaiAvg" class="text-2xl font-black text-slate-900">
                                        <?= (!empty($de_detail['rata_rata']) && $de_detail['rata_rata'] > 0) ? htmlspecialchars($de_detail['rata_rata']) : '-'; ?>
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span id="deStatusLulusBadge" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black <?= (!empty($de_detail['is_lulus_syarat']) ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : (!empty($de_detail['semua_dinilai']) ? 'bg-rose-100 text-rose-800 border border-rose-300' : 'bg-amber-100 text-amber-800 border border-amber-300')); ?>">
                                        <?php
                                            if (!empty($de_detail['is_lulus_syarat'])) {
                                                echo '<i class="bi bi-check-circle-fill text-emerald-600"></i> Memenuhi Syarat (&gt; 60)';
                                            } elseif (!empty($de_detail['semua_dinilai'])) {
                                                echo '<i class="bi bi-x-circle-fill text-rose-600"></i> Rata-rata ≤ 60';
                                            } else {
                                                $cnt = $de_detail['count_graded'] ?? 0;
                                                echo '<i class="bi bi-hourglass-split text-amber-600"></i> ' . $cnt . '/4 Dinilai';
                                            }
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-6 w-full shadow-md shadow-amber-500/5">
                <div class="flex items-center justify-between border-b border-amber-100 pb-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-700 block mb-1">LOG AKTIVITAS PREVIEW 2</span>
                        <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2.5">
                            <i class="bi bi-clock-history text-amber-600 text-xl"></i> Riwayat Pengajuan Berkas Preview 2
                        </h3>
                    </div>
                    <span class="text-xs font-bold px-4 py-2 rounded-2xl bg-slate-100 text-slate-700 border border-slate-200">
                        Total: <?= count($riwayat_preview2); ?> Dokumen
                    </span>
                </div>
                <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-2xs mt-4">
                    <table class="responsive-log-table w-full text-left border-collapse text-xs sm:text-sm">
                        <thead class="bg-slate-50 text-slate-700 font-bold uppercase py-3.5 border-b border-slate-200">
                            <tr>
                                <th class="py-4 px-6 w-14 text-center">#</th>
                                <th class="py-4 px-6">File Draft</th>
                                <th class="py-4 px-6">Catatan Anda</th>
                                <th class="py-4 px-6">Waktu Upload</th>
                                <th class="py-4 px-6 text-center">Status</th>
                                <th class="py-4 px-6 text-center">Komentar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium" id="logTablePreview2">
                            <tr><td colspan="6" class="text-center py-8 text-slate-500"><i class="bi bi-arrow-repeat animate-spin mr-2 text-lg"></i> Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ================= TAB CONTENT PANEL: PREVIEW 3 ================= -->
        <div id="panelPreview3" class="tab-panel hidden space-y-7">
            <div class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-6 w-full shadow-md shadow-indigo-500/5">
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-indigo-100 pb-5">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-700 block mb-1">FORMULIR PRA-SIDANG (PREVIEW 3)</span>
                        <h3 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                            <i class="bi bi-journal-check text-indigo-600 text-xl"></i> Upload Berkas Pra-Sidang (3 Dokumen)
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 font-normal mt-1.5">
                            Unggah file sitasi, file bimbingan, dan persyaratan jalur TA. Persetujuan Preview 3 akan membuka akses Sidang Akhir.
                        </p>
                    </div>
                </div>

                <?php if(!$is_p2_app): ?>
                <div class="py-10 text-center bg-slate-50 border border-slate-200 rounded-3xl max-w-3xl">
                    <i class="bi bi-lock-fill text-4xl text-slate-400 mb-3 block"></i>
                    <h4 class="font-bold text-lg text-slate-700">Tahap Terkunci</h4>
                    <p class="text-slate-500 text-sm mt-2">Anda harus mendapatkan persetujuan (ACC) dari Penguji di tahap Preview 2 sebelum dapat mengunggah berkas Preview 3.</p>
                </div>
                <?php elseif(empty($penguji_ta)): ?>
                <div class="py-10 text-center bg-slate-50 border border-slate-200 rounded-3xl max-w-3xl">
                    <i class="bi bi-person-fill-lock text-4xl text-slate-400 mb-3 block"></i>
                    <h4 class="font-bold text-lg text-slate-700">Dosen Penguji Belum Di-assign</h4>
                    <p class="text-slate-500 text-sm mt-2">Anda tidak dapat mengunggah berkas Preview 3 sampai Dosen Penguji ditetapkan oleh Koordinator TA.</p>
                </div>
                <?php elseif($is_p3_app): ?>
                <div class="py-10 text-center bg-emerald-50 border border-emerald-200 rounded-3xl max-w-3xl">
                    <i class="bi bi-lock-fill text-4xl text-emerald-500 mb-3 block"></i>
                    <h4 class="font-bold text-lg text-emerald-800">Tahap Terkunci (Selesai)</h4>
                    <p class="text-emerald-700 text-sm mt-2">Tahap ini telah disetujui. Anda tidak dapat mengunggah ulang berkas. Anda sudah siap untuk mendaftar Sidang Akhir.</p>
                </div>
                <?php else: ?>
                    <div class="space-y-6 max-w-3xl">
                        <!-- FORM FILE BIMBINGAN -->
                        <?= form_open_multipart('mahasiswa/upload_preview3_ajax', ['id' => 'formUploadP3Bimbingan', 'class' => 'space-y-3']); ?>
                        <input type="hidden" name="jenis_file" value="bimbingan">
                        <div class="p-5 rounded-2xl border border-slate-200 <?= $has_app_bimbingan ? 'bg-emerald-50' : ($is_p3_bim_pending ? 'bg-amber-50/60 border-amber-300' : 'bg-white/60') ?> space-y-3" id="containerBimbinganP3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl <?= $has_app_bimbingan ? 'bg-emerald-500' : ($is_p3_bim_pending ? 'bg-amber-500' : 'bg-indigo-500') ?> text-white flex items-center justify-center text-lg shrink-0 box-3d">
                                        <i class="bi <?= $has_app_bimbingan ? 'bi-check-lg' : ($is_p3_bim_pending ? 'bi-hourglass-split' : 'bi-file-earmark-check-fill') ?>"></i>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-900">File Bimbingan <span class="text-rose-500">*</span></label>
                                        <span class="text-xs text-slate-500 font-medium">Dokumen catatan bimbingan lengkap dari Pembimbing 1 &amp; 2</span>
                                    </div>
                                </div>
                                <?php if($has_app_bimbingan): ?>
                                    <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-300">Telah Disetujui (ACC)</span>
                                <?php elseif($is_p3_bim_pending): ?>
                                    <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold border border-amber-300 flex items-center gap-1.5"><i class="bi bi-clock-history"></i> Sedang Ditinjau</span>
                                <?php endif; ?>
                            </div>

                            <?php if($has_app_bimbingan): ?>
                                <?php if(!empty($latest_p3_bim['file_bimbingan'])): ?>
                                <div class="p-3 bg-white rounded-xl border border-emerald-200 flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2 truncate">
                                        <i class="bi bi-file-earmark-check-fill text-emerald-600 text-lg"></i>
                                        <span class="font-mono font-bold text-slate-800 truncate"><?= htmlspecialchars($latest_p3_bim['file_bimbingan']); ?></span>
                                    </div>
                                    <a href="<?= base_url('uploads/preview_ta/' . rawurlencode($latest_p3_bim['file_bimbingan'])); ?>" target="_blank" class="px-3 py-1 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-bold shrink-0 transition">
                                        Lihat Berkas
                                    </a>
                                </div>
                                <?php endif; ?>

                            <?php elseif($is_p3_bim_pending): ?>
                                <!-- Visual Cue: Pending Review -->
                                <div class="p-4 rounded-xl bg-amber-50/80 border border-amber-200 space-y-3">
                                    <div class="flex items-center justify-between gap-2 min-w-0">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <i class="bi bi-file-earmark-pdf-fill text-amber-600 text-xl shrink-0"></i>
                                            <div class="min-w-0">
                                                <a href="<?= base_url('uploads/preview_ta/' . rawurlencode($latest_p3_bim['file_bimbingan'] ?? '')); ?>" target="_blank" class="font-bold text-slate-900 hover:text-amber-700 hover:underline text-xs truncate block">
                                                    <?= htmlspecialchars($latest_p3_bim['file_bimbingan'] ?? '-'); ?>
                                                </a>
                                                <span class="text-[11px] text-slate-500">
                                                    <i class="bi bi-clock mr-1"></i> Diunggah: <?= !empty($latest_p3_bim['created_at']) ? date('d M Y, H:i', strtotime($latest_p3_bim['created_at'])) . ' WIB' : '-'; ?>
                                                </span>
                                            </div>
                                        </div>
                                        <a href="<?= base_url('uploads/preview_ta/' . rawurlencode($latest_p3_bim['file_bimbingan'] ?? '')); ?>" target="_blank" class="px-3 py-1 rounded-lg bg-white border border-amber-300 text-amber-900 font-bold text-xs hover:bg-amber-100 transition shrink-0 flex items-center gap-1">
                                            <i class="bi bi-download"></i> Unduh
                                        </a>
                                    </div>
                                    <?php if (!empty($latest_p3_bim['catatan_mahasiswa'])): ?>
                                    <div class="pt-2 border-t border-amber-200/60 text-xs text-slate-600 italic">
                                        "<?= htmlspecialchars($latest_p3_bim['catatan_mahasiswa']); ?>"
                                    </div>
                                    <?php endif; ?>
                                    <div class="p-3 rounded-lg bg-amber-100/70 border border-amber-300 text-amber-950 text-xs flex items-center gap-2">
                                        <i class="bi bi-lock-fill text-amber-600 shrink-0"></i>
                                        <span><strong>Upload Terkunci:</strong> Berkas sedang ditinjau Pembimbing 1. Harap menunggu keputusan ACC atau revisi sebelum dapat mengunggah kembali.</span>
                                    </div>
                                </div>

                            <?php else: ?>
                                <?php if($is_p3_bim_revision): ?>
                                <!-- Visual Cue: Revision Note -->
                                <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-300 text-rose-950 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-rose-200 text-rose-800">
                                            <i class="bi bi-arrow-repeat text-rose-600"></i> Perlu Revisi File Bimbingan
                                        </span>
                                        <span class="text-[11px] text-rose-700 font-semibold">Unggah Ulang Dibuka</span>
                                    </div>
                                    <?php if (!empty($latest_p3_bim['catatan_pembimbing'])): ?>
                                    <div class="p-2.5 bg-white rounded-lg border border-rose-200 text-xs italic text-slate-800">
                                        <?= nl2br(htmlspecialchars(strip_tags($latest_p3_bim['catatan_pembimbing']))); ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>

                                <div class="drop-zone-sidang relative border-2 border-dashed rounded-2xl p-4 text-center transition-all cursor-pointer" id="dropZoneBimbinganP3">
                                    <input type="file" name="file_upload" id="fileBimbinganP3" accept=".pdf,.doc,.docx" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <div class="pointer-events-none">
                                        <i class="bi bi-cloud-arrow-up text-2xl text-slate-400"></i>
                                        <p class="text-sm text-slate-500 mt-1">Klik untuk pilih file atau seret ke sini</p>
                                    </div>
                                </div>
                                <div id="fileBadgeBimbinganP3" class="hidden p-3 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs font-bold flex items-center justify-between">
                                    <span id="fileNameBimbinganP3" class="truncate font-mono">file.pdf</span>
                                    <span id="fileSizeBimbinganP3" class="text-emerald-700">0 MB</span>
                                </div>
                                <div class="pt-2 hidden" id="submitWrapBimbinganP3">
                                    <input type="text" name="catatan_mahasiswa" placeholder="Catatan kelayakan (opsional)..." class="w-full p-3 mb-3 border border-slate-200 rounded-xl text-xs bg-white focus:outline-none focus:border-indigo-400">
                                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-indigo-600 text-white font-bold text-xs shadow-md transition hover:bg-indigo-700">
                                        <?= $is_p3_bim_revision ? 'Submit Revisi File Bimbingan' : 'Submit File Bimbingan'; ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?= form_close(); ?>

                        <!-- FORM FILE SITASI -->
                        <?= form_open_multipart('mahasiswa/upload_preview3_ajax', ['id' => 'formUploadP3Sitasi', 'class' => 'space-y-3']); ?>
                        <input type="hidden" name="jenis_file" value="sitasi">
                        <div class="p-5 rounded-2xl border border-slate-200 <?= $has_app_sitasi ? 'bg-emerald-50' : (!$has_app_bimbingan ? 'bg-slate-50 opacity-50' : ($is_p3_sit_pending ? 'bg-amber-50/60 border-amber-300' : 'bg-white/60')) ?> space-y-3 transition-all duration-300" id="containerSitasiP3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl <?= $has_app_sitasi ? 'bg-emerald-500' : (!$has_app_bimbingan ? 'bg-slate-400' : ($is_p3_sit_pending ? 'bg-amber-500' : 'bg-indigo-500')) ?> text-white flex items-center justify-center text-lg shrink-0 box-3d transition-colors" id="iconSitasiP3">
                                        <i class="bi <?= $has_app_sitasi ? 'bi-check-lg' : ($is_p3_sit_pending ? 'bi-hourglass-split' : 'bi-quote') ?>"></i>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-900">File Sitasi <span class="text-rose-500">*</span></label>
                                        <span class="text-xs text-slate-500 font-medium">Dokumen daftar sitasi / referensi (format APA / IEEE)</span>
                                    </div>
                                </div>
                                <?php if($has_app_sitasi): ?>
                                    <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-300">Telah Disetujui (ACC)</span>
                                <?php elseif($is_p3_sit_pending): ?>
                                    <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold border border-amber-300 flex items-center gap-1.5"><i class="bi bi-clock-history"></i> Sedang Ditinjau</span>
                                <?php endif; ?>
                            </div>

                            <?php if($has_app_sitasi): ?>
                                <?php if(!empty($latest_p3_sit['file_sitasi'])): ?>
                                <div class="p-3 bg-white rounded-xl border border-emerald-200 flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2 truncate">
                                        <i class="bi bi-file-earmark-check-fill text-emerald-600 text-lg"></i>
                                        <span class="font-mono font-bold text-slate-800 truncate"><?= htmlspecialchars($latest_p3_sit['file_sitasi']); ?></span>
                                    </div>
                                    <a href="<?= base_url('uploads/preview_ta/' . rawurlencode($latest_p3_sit['file_sitasi'])); ?>" target="_blank" class="px-3 py-1 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-bold shrink-0 transition">
                                        Lihat Berkas
                                    </a>
                                </div>
                                <?php endif; ?>

                            <?php elseif(!$has_app_bimbingan): ?>
                                <div class="drop-zone-sidang relative border-2 border-dashed rounded-2xl p-4 text-center transition-all cursor-not-allowed bg-slate-100" id="dropZoneSitasiP3">
                                    <input type="file" name="file_upload" id="fileSitasiP3" accept=".pdf,.doc,.docx" required disabled class="absolute inset-0 w-full h-full opacity-0 cursor-not-allowed z-10">
                                    <div class="pointer-events-none">
                                        <i class="bi bi-lock-fill text-2xl text-slate-400" id="iconLockSitasiP3"></i>
                                        <p class="text-sm text-slate-500 mt-1" id="textLockSitasiP3">Terkunci (Tunggu File Bimbingan di-ACC)</p>
                                    </div>
                                </div>

                            <?php elseif($is_p3_sit_pending): ?>
                                <!-- Visual Cue: Pending Review -->
                                <div class="p-4 rounded-xl bg-amber-50/80 border border-amber-200 space-y-3">
                                    <div class="flex items-center justify-between gap-2 min-w-0">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <i class="bi bi-file-earmark-pdf-fill text-amber-600 text-xl shrink-0"></i>
                                            <div class="min-w-0">
                                                <a href="<?= base_url('uploads/preview_ta/' . rawurlencode($latest_p3_sit['file_sitasi'] ?? '')); ?>" target="_blank" class="font-bold text-slate-900 hover:text-amber-700 hover:underline text-xs truncate block">
                                                    <?= htmlspecialchars($latest_p3_sit['file_sitasi'] ?? '-'); ?>
                                                </a>
                                                <span class="text-[11px] text-slate-500">
                                                    <i class="bi bi-clock mr-1"></i> Diunggah: <?= !empty($latest_p3_sit['created_at']) ? date('d M Y, H:i', strtotime($latest_p3_sit['created_at'])) . ' WIB' : '-'; ?>
                                                </span>
                                            </div>
                                        </div>
                                        <a href="<?= base_url('uploads/preview_ta/' . rawurlencode($latest_p3_sit['file_sitasi'] ?? '')); ?>" target="_blank" class="px-3 py-1 rounded-lg bg-white border border-amber-300 text-amber-900 font-bold text-xs hover:bg-amber-100 transition shrink-0 flex items-center gap-1">
                                            <i class="bi bi-download"></i> Unduh
                                        </a>
                                    </div>
                                    <?php if (!empty($latest_p3_sit['catatan_mahasiswa'])): ?>
                                    <div class="pt-2 border-t border-amber-200/60 text-xs text-slate-600 italic">
                                        "<?= htmlspecialchars($latest_p3_sit['catatan_mahasiswa']); ?>"
                                    </div>
                                    <?php endif; ?>
                                    <div class="p-3 rounded-lg bg-amber-100/70 border border-amber-300 text-amber-950 text-xs flex items-center gap-2">
                                        <i class="bi bi-lock-fill text-amber-600 shrink-0"></i>
                                        <span><strong>Upload Terkunci:</strong> Berkas sedang ditinjau Pembimbing 1. Harap menunggu keputusan ACC atau revisi sebelum dapat mengunggah kembali.</span>
                                    </div>
                                </div>

                            <?php else: ?>
                                <?php if($is_p3_sit_revision): ?>
                                <!-- Visual Cue: Revision Note -->
                                <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-300 text-rose-950 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-rose-200 text-rose-800">
                                            <i class="bi bi-arrow-repeat text-rose-600"></i> Perlu Revisi File Sitasi
                                        </span>
                                        <span class="text-[11px] text-rose-700 font-semibold">Unggah Ulang Dibuka</span>
                                    </div>
                                    <?php if (!empty($latest_p3_sit['catatan_pembimbing'])): ?>
                                    <div class="p-2.5 bg-white rounded-lg border border-rose-200 text-xs italic text-slate-800">
                                        <?= nl2br(htmlspecialchars(strip_tags($latest_p3_sit['catatan_pembimbing']))); ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>

                                <div class="drop-zone-sidang relative border-2 border-dashed rounded-2xl p-4 text-center transition-all cursor-pointer" id="dropZoneSitasiP3">
                                    <input type="file" name="file_upload" id="fileSitasiP3" accept=".pdf,.doc,.docx" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <div class="pointer-events-none">
                                        <i class="bi bi-cloud-arrow-up text-2xl text-slate-400" id="iconLockSitasiP3"></i>
                                        <p class="text-sm text-slate-500 mt-1" id="textLockSitasiP3">Klik untuk pilih file atau seret ke sini</p>
                                    </div>
                                </div>
                                <div id="fileBadgeSitasiP3" class="hidden p-3 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs font-bold flex items-center justify-between">
                                    <span id="fileNameSitasiP3" class="truncate font-mono">file.pdf</span>
                                    <span id="fileSizeSitasiP3" class="text-emerald-700">0 MB</span>
                                </div>
                                <div class="pt-2 hidden" id="submitWrapSitasiP3">
                                    <input type="text" name="catatan_mahasiswa" placeholder="Catatan kelayakan (opsional)..." class="w-full p-3 mb-3 border border-slate-200 rounded-xl text-xs bg-white focus:outline-none focus:border-indigo-400">
                                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-indigo-600 text-white font-bold text-xs shadow-md transition hover:bg-indigo-700">
                                        <?= $is_p3_sit_revision ? 'Submit Revisi File Sitasi' : 'Submit File Sitasi'; ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?= form_close(); ?>

                        <!-- FORM FILE PERSYARATAN -->
                        <?= form_open_multipart('mahasiswa/upload_preview3_ajax', ['id' => 'formUploadP3Persyaratan', 'class' => 'space-y-3']); ?>
                        <input type="hidden" name="jenis_file" value="persyaratan">
                        <div class="p-5 rounded-2xl border border-slate-200 <?= $has_app_persyaratan ? 'bg-emerald-50' : (!$has_app_sitasi ? 'bg-slate-50 opacity-50' : ($is_p3_per_pending ? 'bg-amber-50/60 border-amber-300' : 'bg-white/60')) ?> space-y-3 transition-all duration-300" id="containerPersyaratanP3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl <?= $has_app_persyaratan ? 'bg-emerald-500' : (!$has_app_sitasi ? 'bg-slate-400' : ($is_p3_per_pending ? 'bg-amber-500' : 'bg-indigo-500')) ?> text-white flex items-center justify-center text-lg shrink-0 box-3d transition-colors" id="iconPersyaratanP3">
                                        <i class="bi <?= $has_app_persyaratan ? 'bi-check-lg' : ($is_p3_per_pending ? 'bi-hourglass-split' : 'bi-signpost-split-fill') ?>"></i>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-900">Persyaratan Jalur Tugas Akhir <span class="text-rose-500">*</span></label>
                                        <span class="text-xs text-slate-500 font-medium">Dokumen persyaratan sesuai jalur TA yang ditempuh</span>
                                    </div>
                                </div>
                                <?php if($has_app_persyaratan): ?>
                                    <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-300">Telah Disetujui (ACC)</span>
                                <?php elseif($is_p3_per_pending): ?>
                                    <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold border border-amber-300 flex items-center gap-1.5"><i class="bi bi-clock-history"></i> Sedang Ditinjau</span>
                                <?php endif; ?>
                            </div>

                            <?php if($has_app_persyaratan): ?>
                                <?php if(!empty($latest_p3_per['file_persyaratan'])): ?>
                                <div class="p-3 bg-white rounded-xl border border-emerald-200 flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2 truncate">
                                        <i class="bi bi-file-earmark-check-fill text-emerald-600 text-lg"></i>
                                        <span class="font-mono font-bold text-slate-800 truncate"><?= htmlspecialchars($latest_p3_per['file_persyaratan']); ?></span>
                                    </div>
                                    <a href="<?= base_url('uploads/preview_ta/' . rawurlencode($latest_p3_per['file_persyaratan'])); ?>" target="_blank" class="px-3 py-1 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-bold shrink-0 transition">
                                        Lihat Berkas
                                    </a>
                                </div>
                                <?php endif; ?>

                            <?php elseif(!$has_app_sitasi): ?>
                                <div class="drop-zone-sidang relative border-2 border-dashed rounded-2xl p-4 text-center transition-all cursor-not-allowed bg-slate-100" id="dropZonePersyaratanP3">
                                    <input type="file" name="file_upload" id="filePersyaratanP3" accept=".pdf,.doc,.docx" required disabled class="absolute inset-0 w-full h-full opacity-0 cursor-not-allowed z-10">
                                    <div class="pointer-events-none">
                                        <i class="bi bi-lock-fill text-2xl text-slate-400" id="iconLockPersyaratanP3"></i>
                                        <p class="text-sm text-slate-500 mt-1" id="textLockPersyaratanP3">Terkunci (Tunggu File Sitasi di-ACC)</p>
                                    </div>
                                </div>

                            <?php elseif($is_p3_per_pending): ?>
                                <!-- Visual Cue: Pending Review -->
                                <div class="p-4 rounded-xl bg-amber-50/80 border border-amber-200 space-y-3">
                                    <div class="flex items-center justify-between gap-2 min-w-0">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <i class="bi bi-file-earmark-pdf-fill text-amber-600 text-xl shrink-0"></i>
                                            <div class="min-w-0">
                                                <a href="<?= base_url('uploads/preview_ta/' . rawurlencode($latest_p3_per['file_persyaratan'] ?? '')); ?>" target="_blank" class="font-bold text-slate-900 hover:text-amber-700 hover:underline text-xs truncate block">
                                                    <?= htmlspecialchars($latest_p3_per['file_persyaratan'] ?? '-'); ?>
                                                </a>
                                                <span class="text-[11px] text-slate-500">
                                                    <i class="bi bi-clock mr-1"></i> Diunggah: <?= !empty($latest_p3_per['created_at']) ? date('d M Y, H:i', strtotime($latest_p3_per['created_at'])) . ' WIB' : '-'; ?>
                                                </span>
                                            </div>
                                        </div>
                                        <a href="<?= base_url('uploads/preview_ta/' . rawurlencode($latest_p3_per['file_persyaratan'] ?? '')); ?>" target="_blank" class="px-3 py-1 rounded-lg bg-white border border-amber-300 text-amber-900 font-bold text-xs hover:bg-amber-100 transition shrink-0 flex items-center gap-1">
                                            <i class="bi bi-download"></i> Unduh
                                        </a>
                                    </div>
                                    <?php if (!empty($latest_p3_per['catatan_mahasiswa'])): ?>
                                    <div class="pt-2 border-t border-amber-200/60 text-xs text-slate-600 italic">
                                        "<?= htmlspecialchars($latest_p3_per['catatan_mahasiswa']); ?>"
                                    </div>
                                    <?php endif; ?>
                                    <div class="p-3 rounded-lg bg-amber-100/70 border border-amber-300 text-amber-950 text-xs flex items-center gap-2">
                                        <i class="bi bi-lock-fill text-amber-600 shrink-0"></i>
                                        <span><strong>Upload Terkunci:</strong> Berkas sedang ditinjau Pembimbing 1. Harap menunggu keputusan ACC atau revisi sebelum dapat mengunggah kembali.</span>
                                    </div>
                                </div>

                            <?php else: ?>
                                <?php if($is_p3_per_revision): ?>
                                <!-- Visual Cue: Revision Note -->
                                <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-300 text-rose-950 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-rose-200 text-rose-800">
                                            <i class="bi bi-arrow-repeat text-rose-600"></i> Perlu Revisi File Persyaratan
                                        </span>
                                        <span class="text-[11px] text-rose-700 font-semibold">Unggah Ulang Dibuka</span>
                                    </div>
                                    <?php if (!empty($latest_p3_per['catatan_pembimbing'])): ?>
                                    <div class="p-2.5 bg-white rounded-lg border border-rose-200 text-xs italic text-slate-800">
                                        <?= nl2br(htmlspecialchars(strip_tags($latest_p3_per['catatan_pembimbing']))); ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>

                                <div class="drop-zone-sidang relative border-2 border-dashed rounded-2xl p-4 text-center transition-all cursor-pointer" id="dropZonePersyaratanP3">
                                    <input type="file" name="file_upload" id="filePersyaratanP3" accept=".pdf,.doc,.docx" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <div class="pointer-events-none">
                                        <i class="bi bi-cloud-arrow-up text-2xl text-slate-400" id="iconLockPersyaratanP3"></i>
                                        <p class="text-sm text-slate-500 mt-1" id="textLockPersyaratanP3">Klik untuk pilih file atau seret ke sini</p>
                                    </div>
                                </div>
                                <div id="fileBadgePersyaratanP3" class="hidden p-3 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs font-bold flex items-center justify-between">
                                    <span id="fileNamePersyaratanP3" class="truncate font-mono">file.pdf</span>
                                    <span id="fileSizePersyaratanP3" class="text-emerald-700">0 MB</span>
                                </div>
                                <div class="pt-2 hidden" id="submitWrapPersyaratanP3">
                                    <input type="text" name="catatan_mahasiswa" placeholder="Catatan kelayakan (opsional)..." class="w-full p-3 mb-3 border border-slate-200 rounded-xl text-xs bg-white focus:outline-none focus:border-indigo-400">
                                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-indigo-600 text-white font-bold text-xs shadow-md transition hover:bg-indigo-700">
                                        <?= $is_p3_per_revision ? 'Submit Revisi File Persyaratan' : 'Submit File Persyaratan'; ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?= form_close(); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-6 w-full shadow-md shadow-indigo-500/5">
                <div class="flex items-center justify-between border-b border-indigo-100 pb-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-700 block mb-1">LOG AKTIVITAS PREVIEW 3</span>
                        <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2.5">
                            <i class="bi bi-clock-history text-indigo-600 text-xl"></i> Riwayat Pengajuan Berkas Preview 3
                        </h3>
                    </div>
                </div>
                <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-2xs mt-4">
                    <table class="responsive-log-table w-full text-left border-collapse text-xs sm:text-sm">
                        <thead class="bg-slate-50 text-slate-700 font-bold uppercase py-3.5 border-b border-slate-200">
                            <tr>
                                <th class="py-4 px-4 w-12 text-center">#</th>
                                <th class="py-4 px-4">File Bimbingan</th>
                                <th class="py-4 px-4">File Sitasi</th>
                                <th class="py-4 px-4">Persyaratan</th>
                                <th class="py-4 px-4">Catatan Anda</th>
                                <th class="py-4 px-4">Waktu Upload</th>
                                <th class="py-4 px-4 text-center">Status</th>
                                <th class="py-4 px-4 text-center">Komentar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium" id="logTablePreview3">
                            <tr><td colspan="8" class="text-center py-8 text-slate-500"><i class="bi bi-arrow-repeat animate-spin mr-2 text-lg"></i> Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ================= TAB CONTENT PANEL: PERSYARATAN SIDANG ================= -->
        <div id="panelPersyaratanSidang" class="tab-panel space-y-7 <?= $active_step === 'persyaratan_sidang' ? '' : 'hidden' ?>">

            <div class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-6 w-full shadow-md shadow-teal-500/5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-teal-100 pb-5">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-teal-700 block mb-1">TAHAP 4 — VERIFIKASI BERKAS OLEH ADMIN LAYANAN (LAA)</span>
                        <h3 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                            <i class="bi bi-folder-check text-teal-600 text-xl"></i> Unggah Berkas Persyaratan Sidang Tugas Akhir
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 font-normal mt-1.5">
                            Unggah dokumen kelengkapan administrasi pendaftaran Sidang Tugas Akhir sesuai daftar dari Admin LAA. Berkas pada tahap ini diverifikasi oleh <strong class="text-slate-900 font-semibold">Admin Layanan (LAA)</strong>.
                        </p>
                    </div>
                    <div class="shrink-0">
                        <?php if ($is_ps_app): ?>
                            <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-2xs">
                                <i class="bi bi-check-circle-fill text-emerald-600"></i> Disetujui Admin LAA
                            </span>
                        <?php elseif ($is_ps_revision): ?>
                            <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300 shadow-2xs">
                                <i class="bi bi-x-circle-fill text-rose-600"></i> Perlu Revisi LAA
                            </span>
                        <?php elseif ($upload_count_ps > 0): ?>
                            <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300 shadow-2xs">
                                <i class="bi bi-clock-fill text-amber-600"></i> Menunggu Verifikasi LAA
                            </span>
                        <?php elseif ($is_p3_app): ?>
                            <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold bg-teal-100 text-teal-800 border border-teal-300 shadow-2xs">
                                <i class="bi bi-unlock-fill text-teal-600"></i> Tahap Terbuka
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200 shadow-2xs">
                                <i class="bi bi-lock-fill text-slate-400"></i> Tahap Terkunci
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!$is_p3_app): ?>
                <div class="py-10 text-center bg-slate-50 border border-slate-200 rounded-3xl max-w-3xl">
                    <i class="bi bi-lock-fill text-4xl text-slate-400 mb-3 block"></i>
                    <h4 class="font-bold text-lg text-slate-700">Tahap Persyaratan Sidang Terkunci</h4>
                    <p class="text-slate-500 text-sm mt-2">Anda harus menyelesaikan dan dinyatakan lulus pada tahap <strong>Preview 3</strong> oleh Pembimbing 1 terlebih dahulu sebelum dapat mengunggah berkas Persyaratan Sidang.</p>
                </div>
                <?php else: ?>
                    <?php if ($is_ps_app): ?>
                    <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-start gap-3.5 max-w-3xl">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg shrink-0 box-3d">
                            <i class="bi bi-patch-check-fill"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm sm:text-base text-emerald-900">Seluruh Berkas Persyaratan Sidang Telah Disetujui Admin LAA</h4>
                            <p class="text-emerald-700 text-xs sm:text-sm mt-1">Verifikasi administrasi persyaratan sidang telah selesai. Anda sekarang dapat melanjutkan ke tahap <strong>Sidang TA</strong>.</p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="space-y-4 max-w-3xl" id="psBodyContent">
                        <div class="text-center py-10 text-slate-400">
                            <i class="bi bi-arrow-repeat animate-spin text-2xl inline-block"></i>
                            <p class="mt-2 text-xs font-semibold">Memuat daftar persyaratan sidang...</p>
                        </div>
                    </div>

                    <?php if (!$is_ps_app): ?>
                    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 max-w-3xl">
                        <span class="text-xs text-slate-500 font-medium">
                            <i class="bi bi-info-circle mr-1 text-teal-600"></i> Format: PDF / DOC / DOCX / JPG / PNG — Maks. 5MB per berkas
                        </span>
                        <button type="button" id="psBtnSubmit" onclick="submitPersyaratanSidang()"
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white text-xs sm:text-sm font-bold shadow-md shadow-teal-500/25 transition cursor-pointer">
                            <i class="bi bi-cloud-arrow-up-fill text-base"></i>
                            <span>Upload &amp; Simpan Berkas Persyaratan</span>
                        </button>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-6 w-full shadow-md shadow-teal-500/5">
                <div class="flex items-center justify-between border-b border-teal-100 pb-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-teal-700 block mb-1">LOG AKTIVITAS PERSYARATAN SIDANG</span>
                        <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2.5">
                            <i class="bi bi-clock-history text-teal-600 text-xl"></i> Status &amp; Riwayat Berkas Persyaratan Sidang
                        </h3>
                    </div>
                </div>
                <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-2xs mt-4">
                    <table class="responsive-log-table w-full text-left border-collapse text-xs sm:text-sm">
                        <thead class="bg-slate-50 text-slate-700 font-bold uppercase py-3.5 border-b border-slate-200">
                            <tr>
                                <th class="py-4 px-4 w-12 text-center">#</th>
                                <th class="py-4 px-4">Nama Berkas Persyaratan</th>
                                <th class="py-4 px-4">Dokumen Terunggah</th>
                                <th class="py-4 px-4 text-center">Status Verifikasi LAA</th>
                                <th class="py-4 px-4">Catatan Admin LAA</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium" id="logTablePersyaratanSidang">
                            <tr><td colspan="5" class="text-center py-8 text-slate-500"><i class="bi bi-arrow-repeat animate-spin mr-2 text-lg"></i> Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ================= TAB CONTENT PANEL: SIDANG AKHIR ================= -->
        <div id="panelSidang" class="tab-panel <?= $active_step === 'sidang' ? '' : 'hidden' ?> space-y-7">
            <?php
                $tgl_sidang_raw = $tgl_sidang ?? '';
                $tgl_sidang_fmt = '-';
                if (!empty($tgl_sidang_raw)) {
                    $hari_arr  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                    $bulan_arr = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                    $ts = strtotime($tgl_sidang_raw);
                    if ($ts) $tgl_sidang_fmt = $hari_arr[date('w',$ts)].', '.date('j',$ts).' '.$bulan_arr[(int)date('n',$ts)].' '.date('Y',$ts);
                }
                $jam_sidang_fmt = '-';
                if (!empty($jam_mulai_sidang)) {
                    $jam_sidang_fmt = substr($jam_mulai_sidang,0,5) . (!empty($jam_selesai_sidang) ? ' – '.substr($jam_selesai_sidang,0,5) : '') . ' WIB';
                }
                $ruangan_sidang_fmt = !empty($ruangan_sidang) ? $ruangan_sidang : 'Belum Ditentukan';

                $tgl_pub_fmt = 'Resmi Diterbitkan';
                if (!empty($tgl_publish_sidang)) {
                    $ts_pub = strtotime($tgl_publish_sidang);
                    if ($ts_pub) {
                        $bulan_arr2 = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                        $tgl_pub_fmt = date('j',$ts_pub).' '.$bulan_arr2[(int)date('n',$ts_pub)].' '.date('Y, H:i',$ts_pub).' WIB';
                    }
                }

                $dosen_p1 = !empty($pembimbing_1) ? $pembimbing_1 : 'Dosen Pembimbing 1';
                $dosen_p2 = !empty($pembimbing_2) ? $pembimbing_2 : 'Dosen Pembimbing 2';
                $dosen_u1 = !empty($penguji_1)    ? $penguji_1    : (!empty($penguji_ta) ? $penguji_ta : 'Dosen Penguji 1');
                $dosen_u2 = !empty($penguji_2)    ? $penguji_2    : 'Dosen Penguji 2';
            ?>

            <?php if (!empty($is_nilai_published)): ?>
            <?php
                $is_lulus_murni = (stripos($status_kelulusan,'revisi')===false && stripos($status_kelulusan,'tidak')===false && stripos($status_kelulusan,'lulus')!==false);
                $is_revisi      = (stripos($status_kelulusan,'revisi')!==false || stripos($status_kelulusan,'bersyarat')!==false);

                if ($is_lulus_murni) {
                    $card_theme_border   = 'border-emerald-300';
                    $card_header_bg      = 'bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700';
                    $card_badge_kelulusan= 'bg-emerald-500 text-white';
                    $icon_hero           = 'bi-trophy-fill';
                    $headline_title      = 'SELAMAT! ANDA DINYATAKAN LULUS SIDANG TUGAS AKHIR';
                    $headline_sub        = 'Hasil evaluasi sidang Tugas Akhir Anda telah disahkan dan dipublikasikan secara resmi oleh Koordinator TA.';
                } elseif ($is_revisi) {
                    $card_theme_border   = 'border-amber-300';
                    $card_header_bg      = 'bg-gradient-to-r from-amber-600 via-orange-600 to-amber-700';
                    $card_badge_kelulusan= 'bg-amber-500 text-white';
                    $icon_hero           = 'bi-exclamation-diamond-fill';
                    $headline_title      = 'LULUS DENGAN REVISI';
                    $headline_sub        = 'Anda dinyatakan lulus dengan kewajiban menyelesaikan revisi naskah/karya sesuai catatan dari dewan penguji.';
                } else {
                    $card_theme_border   = 'border-rose-300';
                    $card_header_bg      = 'bg-gradient-to-r from-rose-600 via-red-600 to-rose-700';
                    $card_badge_kelulusan= 'bg-rose-500 text-white';
                    $icon_hero           = 'bi-x-octagon-fill';
                    $headline_title      = 'TIDAK LULUS SIDANG TUGAS AKHIR';
                    $headline_sub        = 'Silakan berkonsultasi dengan Dosen Pembimbing untuk arahan perbaikan dan pengajuan sidang ulang.';
                }
            ?>
            <div class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-7 w-full shadow-xl border-2 <?= $card_theme_border; ?> bg-white relative overflow-hidden">
                <div class="<?= $card_header_bg; ?> rounded-2xl p-6 sm:p-7 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5 relative overflow-hidden shadow-lg">
                    <div class="relative z-10 flex items-start gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-md text-white flex items-center justify-center text-3xl shrink-0 box-3d border border-white/30">
                            <i class="bi <?= $icon_hero; ?>"></i>
                        </div>
                        <div class="space-y-1">
                            <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-[11px] font-extrabold uppercase tracking-wider text-white border border-white/30">
                                <span class="w-2 h-2 rounded-full bg-white animate-ping"></span> Pengumuman Resmi Hasil Sidang TA
                            </div>
                            <h2 class="text-xl sm:text-2xl font-black tracking-tight text-white leading-snug"><?= $headline_title; ?></h2>
                            <p class="text-xs sm:text-sm text-white/90 font-medium max-w-2xl leading-relaxed"><?= $headline_sub; ?></p>
                        </div>
                    </div>
                    <div class="relative z-10 sm:text-right shrink-0">
                        <span class="text-[10px] uppercase font-bold text-white/80 block tracking-wider">Tanggal Publikasi:</span>
                        <span class="text-xs font-bold text-white mt-0.5 block bg-black/25 px-3 py-1.5 rounded-xl border border-white/20">
                            <i class="bi bi-calendar2-check mr-1.5"></i> <?= $tgl_pub_fmt; ?>
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-5 rounded-2xl bg-gradient-to-br from-slate-50 to-orange-50/40 border border-slate-200/80 shadow-2xs flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">Nilai Akhir Sidang</span>
                            <div class="flex items-baseline gap-1">
                                <span class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight"><?= $nilai_akhir; ?></span>
                                <span class="text-xs font-bold text-slate-400">/ 100</span>
                            </div>
                            <span class="text-[11px] font-semibold text-emerald-700 mt-1 block">Rata-rata 4 Penilai</span>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-orange-500 text-white flex items-center justify-center text-xl font-bold box-3d shadow-md shadow-orange-500/30">
                            <i class="bi bi-speedometer2"></i>
                        </div>
                    </div>
                    <div class="p-5 rounded-2xl bg-gradient-to-br from-slate-50 to-indigo-50/40 border border-slate-200/80 shadow-2xs flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">Grade Mutu</span>
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl sm:text-4xl font-black text-indigo-700 tracking-tight"><?= htmlspecialchars($grade_sidang); ?></span>
                                <span class="text-xs font-bold text-indigo-600 bg-indigo-100 px-2 py-0.5 rounded-md">Huruf Mutu</span>
                            </div>
                            <span class="text-[11px] font-semibold text-slate-500 mt-1 block">Standar Skala Akademik</span>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-xl font-bold box-3d shadow-md shadow-indigo-600/30">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                    </div>
                    <div class="p-5 rounded-2xl bg-gradient-to-br from-slate-50 to-emerald-50/40 border border-slate-200/80 shadow-2xs flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">Keputusan Dewan Sidang</span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-black uppercase tracking-wide <?= $card_badge_kelulusan; ?> shadow-xs">
                                <i class="bi bi-patch-check-fill"></i> <?= htmlspecialchars($status_kelulusan); ?>
                            </span>
                            <span class="text-[11px] font-semibold text-slate-500 mt-2 block">Keputusan Final Disahkan</span>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-xl font-bold box-3d shadow-md shadow-emerald-600/30">
                            <i class="bi bi-shield-check"></i>
                        </div>
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-200/80 pb-3">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                            <i class="bi bi-info-circle-fill text-orange-500"></i> Informasi Berita Acara &amp; Pelaksanaan Sidang
                        </span>
                        <span class="text-xs font-bold px-3 py-1 rounded-lg bg-white border border-slate-200 text-slate-600">
                            <i class="bi bi-door-open-fill text-cyan-600 mr-1"></i> Ruangan: <?= htmlspecialchars($ruangan_sidang_fmt); ?>
                        </span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                        <div class="p-3 bg-white rounded-xl border border-slate-200/80 space-y-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Waktu Sidang:</span>
                            <p class="font-bold text-slate-900"><?= $tgl_sidang_fmt; ?></p>
                            <p class="text-slate-600"><?= $jam_sidang_fmt; ?></p>
                        </div>
                        <div class="p-3 bg-white rounded-xl border border-slate-200/80 space-y-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tempat / Ruangan:</span>
                            <p class="font-bold text-slate-900"><?= htmlspecialchars($ruangan_sidang_fmt); ?></p>
                            <?php if (!empty($link_sidang)): ?>
                                <a href="<?= htmlspecialchars($link_sidang); ?>" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-indigo-600 hover:text-indigo-800 font-bold hover:underline">
                                    <i class="bi bi-camera-video-fill"></i> Link Video Conference
                                </a>
                            <?php elseif (!empty($ruangan_sidang) && $ruangan_sidang !== 'Belum Ditentukan'): ?>
                                <p class="text-slate-500 text-[11px]">Ruang Sidang FIK</p>
                            <?php else: ?>
                                <p class="text-slate-400 text-[11px] italic">Lokasi belum ditetapkan</p>
                            <?php endif; ?>
                        </div>
                        <div class="p-3 bg-white rounded-xl border border-slate-200/80 space-y-1">
                            <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider block">Dewan Dosen Penguji:</span>
                            <p class="font-bold text-slate-900 truncate">1. <?= htmlspecialchars($dosen_u1); ?></p>
                            <p class="font-bold text-slate-900 truncate">2. <?= htmlspecialchars($dosen_u2); ?></p>
                        </div>
                        <div class="p-3 bg-white rounded-xl border border-slate-200/80 space-y-1">
                            <span class="text-[10px] font-bold text-orange-600 uppercase tracking-wider block">Dosen Pembimbing:</span>
                            <p class="font-bold text-slate-900 truncate">1. <?= htmlspecialchars($dosen_p1); ?></p>
                            <p class="font-bold text-slate-900 truncate">2. <?= htmlspecialchars($dosen_p2); ?></p>
                        </div>
                    </div>

                    <?php if (!empty($catatan_sidang)): ?>
                        <div class="p-4 rounded-xl bg-amber-50/80 border border-amber-200 space-y-1">
                            <span class="text-[11px] font-extrabold uppercase tracking-wider text-amber-800 flex items-center gap-1.5">
                                <i class="bi bi-chat-quote-fill text-amber-600"></i> Catatan &amp; Arahan Revisi dari Dewan Sidang:
                            </span>
                            <p class="text-xs text-amber-950 font-medium leading-relaxed italic pl-4 border-l-2 border-amber-400 my-1 whitespace-pre-wrap"><?= htmlspecialchars($catatan_sidang); ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="pt-2 flex flex-wrap items-center justify-between gap-3 border-t border-slate-200/80">
                        <span class="text-xs text-slate-500 font-medium">Transparansi penilaian: Anda dapat melihat rincian bobot dan poin per aspek kriteria evaluasi.</span>
                        <button type="button" onclick="openModalRubrikNilai()" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition flex items-center gap-2 box-3d hover:scale-105 active:scale-95 cursor-pointer">
                            <i class="bi bi-card-checklist text-base"></i> Lihat Rincian Rubrik Penilaian
                        </button>
                    </div>
                </div>
            </div>

            <?php elseif (!empty($is_nilai_draft)): ?>
            <div class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-5 w-full shadow-lg border-2 border-amber-300 bg-amber-50/40">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-2xl shrink-0 box-3d shadow-md shadow-amber-500/30">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-800 block">EVALUASI DALAM PROSES VERIFIKASI</span>
                        <h3 class="text-lg sm:text-xl font-extrabold text-slate-900">Sidang Telah Selesai — Penilaian Sedang Direkapitulasi</h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                            Sidang Tugas Akhir Anda telah selesai dilaksanakan. Saat ini dewan penguji sedang melengkapi penilaian. Baru <strong><?= (int)($rekap_nilai_sidang['jumlah_terisi'] ?? 0); ?> dari 4</strong> penilai yang telah mengisi nilai. Nilai akhir, grade mutu, dan status kelulusan akan ditampilkan otomatis setelah keempat penilai selesai.
                        </p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs pt-3 border-t border-amber-200">
                    <div class="p-3 bg-white rounded-xl border border-amber-200 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Waktu Sidang:</span>
                        <p class="font-bold text-slate-900"><?= $tgl_sidang_fmt; ?></p>
                        <p class="text-slate-600"><?= $jam_sidang_fmt; ?></p>
                    </div>
                    <div class="p-3 bg-white rounded-xl border border-amber-200 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Ruangan:</span>
                        <p class="font-bold text-slate-900"><?= htmlspecialchars($ruangan_sidang_fmt); ?></p>
                        <?php if (!empty($link_sidang)): ?>
                            <a href="<?= htmlspecialchars($link_sidang); ?>" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-indigo-600 hover:text-indigo-800 font-bold hover:underline">
                                <i class="bi bi-camera-video-fill"></i> Link Video Conference
                            </a>
                        <?php elseif (!empty($ruangan_sidang) && $ruangan_sidang !== 'Belum Ditentukan'): ?>
                            <p class="text-slate-500 text-[11px]">Ruang Sidang FIK</p>
                        <?php else: ?>
                            <p class="text-slate-400 text-[11px] italic">Lokasi belum ditetapkan</p>
                        <?php endif; ?>
                    </div>
                    <div class="p-3 bg-white rounded-xl border border-amber-200 space-y-1">
                        <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider block">Dewan Penguji:</span>
                        <p class="font-bold text-slate-900 truncate">1. <?= htmlspecialchars($dosen_u1); ?></p>
                        <p class="font-bold text-slate-900 truncate">2. <?= htmlspecialchars($dosen_u2); ?></p>
                    </div>
                    <div class="p-3 bg-white rounded-xl border border-amber-200 space-y-1">
                        <span class="text-[10px] font-bold text-orange-600 uppercase tracking-wider block">Dosen Pembimbing:</span>
                        <p class="font-bold text-slate-900 truncate">1. <?= htmlspecialchars($dosen_p1); ?></p>
                        <p class="font-bold text-slate-900 truncate">2. <?= htmlspecialchars($dosen_p2); ?></p>
                    </div>
                </div>
            </div>

            <?php elseif (!$is_non_sidang && !empty($is_sidang_scheduled)): ?>
            <div class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-5 w-full shadow-lg border-2 border-sky-300 bg-sky-50/40">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-sky-600 text-white flex items-center justify-center text-2xl shrink-0 box-3d shadow-md shadow-sky-600/30">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-sky-800 block">JADWAL SIDANG TERBIT</span>
                        <h3 class="text-lg sm:text-xl font-extrabold text-slate-900">Jadwal Pelaksanaan Sidang Tugas Akhir</h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                            Koordinator TA telah menetapkan jadwal dan dewan penguji sidang Tugas Akhir Anda. Harap hadir 15 menit sebelum waktu pelaksanaan dan mempersiapkan naskah cetak, slide presentasi, serta prototype karya.
                        </p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs pt-3 border-t border-sky-200">
                    <div class="p-3.5 bg-white rounded-xl border border-sky-200 space-y-1 shadow-2xs">
                        <span class="text-[10px] font-bold text-sky-700 uppercase tracking-wider block">Hari &amp; Tanggal:</span>
                        <p class="font-extrabold text-slate-900 text-sm"><?= $tgl_sidang_fmt; ?></p>
                        <p class="text-slate-600 font-semibold"><?= $jam_sidang_fmt; ?></p>
                    </div>
                    <div class="p-3.5 bg-white rounded-xl border border-sky-200 space-y-1 shadow-2xs">
                        <span class="text-[10px] font-bold text-sky-700 uppercase tracking-wider block">Ruangan Sidang:</span>
                        <p class="font-extrabold text-slate-900 text-sm"><?= htmlspecialchars($ruangan_sidang_fmt); ?></p>
                        <?php if (!empty($link_sidang)): ?>
                            <a href="<?= htmlspecialchars($link_sidang); ?>" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-indigo-600 hover:text-indigo-800 font-bold hover:underline">
                                <i class="bi bi-camera-video-fill"></i> Buka Link Sidang Online
                            </a>
                        <?php elseif (!empty($ruangan_sidang) && $ruangan_sidang !== 'Belum Ditentukan'): ?>
                            <p class="text-slate-500 text-xs">Ruang Sidang FIK</p>
                        <?php else: ?>
                            <p class="text-slate-400 text-xs italic">Menunggu penetapan ruang</p>
                        <?php endif; ?>
                    </div>
                    <div class="p-3.5 bg-white rounded-xl border border-sky-200 space-y-1 shadow-2xs">
                        <span class="text-[10px] font-bold text-indigo-700 uppercase tracking-wider block">Dewan Dosen Penguji:</span>
                        <p class="font-bold text-slate-900 truncate">1. <?= htmlspecialchars($dosen_u1); ?></p>
                        <p class="font-bold text-slate-900 truncate">2. <?= htmlspecialchars($dosen_u2); ?></p>
                    </div>
                    <div class="p-3.5 bg-white rounded-xl border border-sky-200 space-y-1 shadow-2xs">
                        <span class="text-[10px] font-bold text-orange-700 uppercase tracking-wider block">Dosen Pembimbing:</span>
                        <p class="font-bold text-slate-900 truncate">1. <?= htmlspecialchars($dosen_p1); ?></p>
                        <p class="font-bold text-slate-900 truncate">2. <?= htmlspecialchars($dosen_p2); ?></p>
                    </div>
                </div>
            </div>

            <?php elseif (!$is_non_sidang): ?>
            <div class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-5 w-full shadow-md border-2 border-amber-200 bg-amber-50/30">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-2xl shrink-0 box-3d border border-amber-300">
                        <i class="bi bi-door-closed-fill"></i>
                    </div>
                    <div class="space-y-1">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-amber-200/80 text-amber-900 mb-1">
                            <i class="bi bi-clock-history"></i> BELUM DI-PLOTTING
                        </span>
                        <h3 class="text-lg sm:text-xl font-extrabold text-slate-900">Belum Ada Ruangan &amp; Jadwal Sidang</h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                            Tanggal dan ruangan pelaksanaan sidang Tugas Akhir Anda <strong>belum di-plotting</strong> oleh Koordinator Tugas Akhir. Anda baru dapat mengunggah dokumen sidang akhir setelah tanggal dan ruangan sidang ditetapkan.
                        </p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs pt-3 border-t border-amber-200/70">
                    <div class="p-3.5 bg-white rounded-xl border border-amber-200/80 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Hari &amp; Tanggal:</span>
                        <p class="font-bold text-amber-800 italic">Belum Di-plotting</p>
                        <p class="text-slate-400 text-[11px]">Menunggu Koordinator TA</p>
                    </div>
                    <div class="p-3.5 bg-white rounded-xl border border-amber-200/80 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Ruangan / Tempat:</span>
                        <p class="font-bold text-amber-800 italic">Belum Ada Ruangan</p>
                        <p class="text-slate-400 text-[11px]">Menunggu Koordinator TA</p>
                    </div>
                    <div class="p-3.5 bg-white rounded-xl border border-amber-200/80 space-y-1">
                        <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider block">Dewan Penguji:</span>
                        <p class="font-bold text-slate-700 truncate">1. <?= htmlspecialchars($dosen_u1); ?></p>
                        <p class="font-bold text-slate-700 truncate">2. <?= htmlspecialchars($dosen_u2); ?></p>
                    </div>
                    <div class="p-3.5 bg-white rounded-xl border border-amber-200/80 space-y-1">
                        <span class="text-[10px] font-bold text-orange-600 uppercase tracking-wider block">Dosen Pembimbing:</span>
                        <p class="font-bold text-slate-700 truncate">1. <?= htmlspecialchars($dosen_p1); ?></p>
                        <p class="font-bold text-slate-700 truncate">2. <?= htmlspecialchars($dosen_p2); ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- CARD: BAP SIDANG -->
            <?php if (!empty($pendaftaran['bap_published'])): ?>
            <div class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-5 w-full shadow-lg border-2 border-purple-300 bg-purple-50/40">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-purple-600 text-white flex items-center justify-center text-2xl shrink-0 box-3d shadow-md shadow-purple-600/30">
                            <i class="bi bi-file-earmark-check-fill"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-purple-800 block">BERITA ACARA SIDANG (BAP)</span>
                            <h3 class="text-lg font-extrabold text-slate-900">Dokumen BAP Sidang Telah Terbit</h3>
                            <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                                Penguji 1 telah mempublikasikan dokumen <strong>Berita Acara Sidang Tugas Akhir</strong> Anda.
                                Silakan lihat atau cetak dokumen BAP untuk keperluan arsip pribadi.
                            </p>
                            <?php if (!empty($pendaftaran['bap_published_at'])): ?>
                                <p class="text-[11px] text-slate-500 font-medium mt-2">
                                    <i class="bi bi-clock-history mr-1"></i>
                                    Dipublikasikan: <?= date('d M Y, H:i', strtotime($pendaftaran['bap_published_at'])); ?> WIB
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <button type="button" onclick="openStudentBapPopup()"
                            class="px-5 py-3 rounded-2xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs sm:text-sm shadow-md transition flex items-center gap-2 box-3d hover:scale-[1.02] active:scale-95 cursor-pointer shrink-0">
                        <i class="bi bi-eye-fill text-base"></i> Lihat BAP Sidang
                    </button>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($is_nilai_published)): ?>
            <div class="p-5 rounded-2xl bg-emerald-50 border-2 border-emerald-300 text-emerald-900 text-xs font-semibold flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-base shrink-0 box-3d">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <span>Pendaftaran dan pelaksanaan sidang telah selesai dilalui. Berkas persyaratan yang telah diunggah dapat ditinjau pada riwayat pengajuan di bawah.</span>
                </div>
                <button type="button" onclick="toggleFormUploadSidang()" class="px-3.5 py-2 rounded-xl bg-white border border-emerald-300 text-emerald-800 font-bold hover:bg-emerald-100 transition shrink-0 cursor-pointer flex items-center gap-1.5 shadow-2xs">
                    <i class="bi bi-file-earmark-text"></i> <span id="toggleUploadFormText">Tampilkan Form Berkas</span>
                </button>
            </div>
            <?php endif; ?>

            <!-- Form Upload Berkas Sidang -->
            <div id="wrapperFormUploadSidang" class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-6 w-full shadow-md shadow-emerald-500/10 <?= !empty($is_nilai_published) ? 'hidden' : ''; ?>">
                <div class="border-b border-emerald-100 pb-5">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 block mb-1">FORMULIR PENDAFTARAN SIDANG</span>
                    <h3 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                        <i class="bi bi-mortarboard-fill text-emerald-600 text-xl"></i> Upload Dokumen Sidang Akhir
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 font-normal mt-1.5">
                        Unggah satu file dokumen final untuk persyaratan sidang. Format: <strong class="text-slate-900 font-semibold">PDF, DOC, DOCX</strong> (Maks. 10MB).
                    </p>
                </div>

                <?php
                $sidang_locked = false;
                $lock_message  = '';
                if (!$is_p3_app) {
                    $sidang_locked = true;
                    $lock_message  = 'Anda harus menyelesaikan Preview 3 dan mendapatkan persetujuan Pembimbing 1 terlebih dahulu sebelum dapat mengunggah berkas sidang.';
                } elseif (!$is_ps_app) {
                    $sidang_locked = true;
                    $lock_message  = 'Anda harus melengkapi dan mendapatkan persetujuan Admin Layanan (LAA) pada tahap Persyaratan Sidang terlebih dahulu sebelum dapat mengunggah berkas Sidang TA.';
                } elseif (!$is_non_sidang && empty($is_sidang_scheduled)) {
                    $sidang_locked = true;
                    $lock_message  = 'Belum ada ruangan dan jadwal sidang (belum di-plotting oleh Koordinator TA). Anda baru dapat mengunggah berkas sidang setelah tanggal dan ruangan sidang ditetapkan.';
                } elseif (!$is_non_sidang && empty($penguji_ta)) {
                    $sidang_locked = true;
                    $lock_message  = 'Dosen Penguji belum di-assign. Silakan hubungi Koordinator TA untuk menetapkan Dosen Penguji sebelum Anda dapat mengunggah berkas sidang.';
                }
                ?>

                <?php if ($sidang_locked): ?>
                <div class="py-10 text-center bg-slate-50 border border-slate-200 rounded-3xl">
                    <i class="bi bi-lock-fill text-4xl text-slate-400 mb-3 block"></i>
                    <h4 class="font-bold text-lg text-slate-700">Tahap Terkunci</h4>
                    <p class="text-slate-500 text-sm mt-2"><?= $lock_message ?></p>
                </div>
                <?php elseif ($is_sidang_approved): ?>
                <!-- Visual Cue: Berkas Sidang Disetujui (ACC) -->
                <div class="card-3d-warm rounded-3xl p-6 sm:p-7 space-y-4 border-2 border-emerald-300 bg-emerald-50/60 shadow-md shadow-emerald-500/10">
                    <div class="flex items-center justify-between gap-3 border-b border-emerald-200 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center text-2xl shrink-0 box-3d shadow-md shadow-emerald-500/20">
                                <i class="bi bi-patch-check-fill"></i>
                            </div>
                            <div>
                                <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider bg-emerald-200 text-emerald-800 mb-1">
                                    <i class="bi bi-check-circle-fill"></i> Berkas Disetujui (ACC)
                                </span>
                                <h4 class="font-black text-lg text-emerald-950">Berkas Pendaftaran Sidang Diterima</h4>
                                <p class="text-xs text-emerald-800">Berkas sidang Anda telah diverifikasi dan disetujui. Silakan menunggu jadwal sidang yang ditetapkan oleh Koordinator TA.</p>
                            </div>
                        </div>
                        <span class="px-3 py-1.5 rounded-xl bg-white border border-emerald-300 text-emerald-800 text-xs font-bold shrink-0 flex items-center gap-1.5 shadow-2xs">
                            <i class="bi bi-lock-fill text-emerald-600"></i> Pendaftaran Selesai
                        </span>
                    </div>
                    <?php $file_sidang_name = $latest_sidang['file_sidang'] ?? ($latest_sidang['file_draft'] ?? '-'); ?>
                    <div class="p-3.5 bg-white rounded-2xl border border-emerald-200/80 flex items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2.5 truncate">
                            <i class="bi bi-file-earmark-check-fill text-emerald-600 text-xl"></i>
                            <span class="font-mono font-bold text-slate-800 truncate"><?= htmlspecialchars($file_sidang_name); ?></span>
                        </div>
                        <a href="<?= base_url('uploads/sidang/' . rawurlencode($file_sidang_name)); ?>" target="_blank" class="px-3 py-1 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-bold shrink-0 transition">
                            Lihat Berkas
                        </a>
                    </div>
                </div>
                <?php elseif ($is_sidang_pending): ?>
                <!-- Visual Cue: Berkas Sedang Diverifikasi (Upload Terblokir) -->
                <div class="card-3d-warm rounded-3xl p-6 sm:p-7 space-y-5 border-2 border-amber-300 bg-amber-50/50 shadow-md shadow-amber-500/10">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-amber-200 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-2xl shrink-0 box-3d shadow-md shadow-amber-500/20">
                                <i class="bi bi-hourglass-split animate-pulse"></i>
                            </div>
                            <div>
                                <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-amber-200 text-amber-900 mb-1">
                                    <span class="w-2 h-2 rounded-full bg-amber-600 animate-ping"></span> Sedang Ditinjau / Diverifikasi
                                </span>
                                <h4 class="font-black text-lg text-slate-900">Berkas Sidang Dalam Proses Peninjauan</h4>
                                <p class="text-xs text-slate-600">Dokumen pendaftaran sidang Anda sedang diverifikasi oleh Dosen Pembimbing dan Koordinator TA.</p>
                            </div>
                        </div>
                        <span class="px-3 py-1.5 rounded-xl bg-white border border-amber-300 text-amber-900 text-xs font-bold shrink-0 self-start sm:self-auto flex items-center gap-1.5 shadow-2xs">
                            <i class="bi bi-lock-fill text-amber-600"></i> Upload Terkunci
                        </span>
                    </div>

                    <?php $file_sidang_name = $latest_sidang['file_sidang'] ?? ($latest_sidang['file_draft'] ?? '-'); ?>
                    <div class="p-4 rounded-2xl bg-white border border-amber-200/80 space-y-2.5">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Dokumen yang Telah Diunggah</span>
                        <div class="flex items-center justify-between gap-3 min-w-0">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg shrink-0">
                                    <i class="bi bi-file-earmark-text-fill"></i>
                                </div>
                                <div class="min-w-0">
                                    <a href="<?= base_url('uploads/sidang/' . rawurlencode($file_sidang_name)); ?>" target="_blank" class="font-bold text-slate-900 hover:text-amber-700 hover:underline text-sm truncate block">
                                        <?= htmlspecialchars($file_sidang_name); ?>
                                    </a>
                                    <span class="text-[11px] text-slate-500">
                                        <i class="bi bi-clock mr-1"></i> Diunggah pada: <?= !empty($latest_sidang['created_at']) ? date('d M Y, H:i', strtotime($latest_sidang['created_at'])) . ' WIB' : '-'; ?>
                                    </span>
                                </div>
                            </div>
                            <a href="<?= base_url('uploads/sidang/' . rawurlencode($file_sidang_name)); ?>" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs shrink-0 transition flex items-center gap-1.5">
                                <i class="bi bi-download"></i> Unduh
                            </a>
                        </div>
                        <?php if (!empty($latest_sidang['catatan_mahasiswa'])): ?>
                            <div class="pt-2 border-t border-slate-100 text-xs text-slate-600">
                                <span class="font-semibold text-slate-700">Catatan Pendaftaran:</span>
                                <p class="italic text-slate-500 mt-0.5"><?= htmlspecialchars($latest_sidang['catatan_mahasiswa']); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="p-4 rounded-2xl bg-amber-100/70 border border-amber-300 text-amber-900 text-xs leading-relaxed flex items-start gap-2.5">
                        <i class="bi bi-info-circle-fill text-amber-600 text-base shrink-0 mt-0.5"></i>
                        <div>
                            <strong class="font-bold block mb-0.5">Pengunggahan Berkas Dikunci Sementara:</strong>
                            Anda telah mengunggah berkas pendaftaran sidang. Pengunggahan baru diblokir sementara sampai berkas ini diverifikasi dan disetujui (ACC) atau diberikan arahan revisi.
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <?php if ($is_sidang_revision): ?>
                <!-- Visual Cue: Dosen Meminta Revisi Berkas Sidang — Upload Ulang Dibuka -->
                <div class="p-5 rounded-2xl bg-rose-50 border-2 border-rose-300 text-rose-950 space-y-3 mb-6">
                    <div class="flex items-center justify-between gap-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-rose-200 text-rose-800">
                            <i class="bi bi-arrow-repeat text-rose-600"></i> Perlu Revisi Berkas Sidang — Unggah Ulang Dibuka
                        </span>
                        <span class="text-[11px] text-rose-700 font-semibold">
                            Status: Perbaikan Dokumen Sidang
                        </span>
                    </div>
                    <div class="space-y-1">
                        <h4 class="font-black text-sm text-rose-900">Catatan Evaluasi / Arahan Revisi:</h4>
                        <div class="p-3 bg-white rounded-xl border border-rose-200 text-xs leading-relaxed text-slate-800 italic">
                            <?= !empty($latest_sidang['catatan_pembimbing']) ? nl2br(htmlspecialchars(strip_tags($latest_sidang['catatan_pembimbing']))) : (!empty($latest_sidang['catatan_sidang']) ? nl2br(htmlspecialchars(strip_tags($latest_sidang['catatan_sidang']))) : 'Silakan lakukan revisi berkas sidang sesuai catatan dosen.'); ?>
                        </div>
                    </div>
                    <p class="text-xs text-rose-800 font-medium">
                        Silakan perbaiki dokumen naskah/berkas sidang Anda, lalu unggah dokumen perbaikan terbaru melalui formulir di bawah ini.
                    </p>
                </div>
                <?php endif; ?>

                <?= form_open_multipart('mahasiswa/upload_sidang', ['id' => 'formUploadSidang', 'class' => 'space-y-6']); ?>

                <div>
                    <label class="block text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider mb-2.5">
                        File Dokumen Sidang <?= $is_sidang_revision ? '(Revisi)' : ''; ?> <span class="text-rose-500">*</span>
                    </label>
                    
                    <div class="drop-zone relative border-2 border-dashed border-emerald-300 hover:border-emerald-500 bg-emerald-50/30 hover:bg-emerald-50/60 rounded-3xl p-8 sm:p-10 text-center transition-all cursor-pointer group" id="dropZoneSidangSingle">
                        <input type="file" name="file_sidang" id="fileSidangSingle" accept=".pdf,.doc,.docx" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="space-y-3.5 pointer-events-none">
                            <div class="w-16 h-16 sm:w-18 sm:h-18 rounded-3xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-white group-hover:scale-105 flex items-center justify-center text-3xl mx-auto transition-transform box-3d shadow-md shadow-emerald-500/20">
                                <i class="bi bi-file-earmark-text-fill"></i>
                            </div>
                            <div>
                                <p class="text-sm sm:text-base font-bold text-slate-900">
                                    Klik untuk memilih file atau seret &amp; lepas ke sini
                                </p>
                                <p class="text-xs text-slate-500 font-medium mt-1">
                                    PDF, DOC, atau DOCX (Maksimal ukuran: 10MB)
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div id="fileBadgeSidangSingle" class="hidden mt-3 p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs sm:text-sm font-bold flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-sm shrink-0 box-3d">
                                <i class="bi bi-file-earmark-check-fill"></i>
                            </div>
                            <span id="fileNameSidangSingle" class="truncate font-mono">dokumen.pdf</span>
                        </div>
                        <span id="fileSizeSidangSingle" class="text-xs text-emerald-800 font-bold shrink-0 ml-3 bg-white px-3 py-1 rounded-xl border border-emerald-200">2.4 MB</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider mb-2.5">Catatan Pendaftaran Sidang</label>
                    <textarea name="catatan_sidang" rows="3" class="w-full p-4 text-sm rounded-2xl border border-slate-200 focus:ring-4 focus:ring-emerald-400/20 focus:border-emerald-500 outline-none resize-none text-slate-900 placeholder:text-slate-400 transition bg-white font-medium" placeholder="Tambahkan catatan penting terkait pendaftaran sidang Anda (opsional)..."></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-4 px-8 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-sm sm:text-base shadow-lg shadow-emerald-500/25 transition flex items-center justify-center gap-3 cursor-pointer hover:scale-[1.01] active:scale-[0.99] box-3d">
                        <i class="bi bi-cloud-arrow-up-fill text-lg"></i>
                        <span><?= $is_sidang_revision ? 'Submit Revisi Berkas Sidang Akhir' : 'Submit Berkas Sidang Akhir'; ?></span>
                    </button>
                </div>
                <?= form_close(); ?>
                <?php endif; ?>
            </div>

            <div class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-6 w-full shadow-md shadow-emerald-500/10">
                <div class="flex items-center justify-between border-b border-emerald-100 pb-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 block mb-1">LOG AKTIVITAS SIDANG</span>
                        <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2.5">
                            <i class="bi bi-clock-history text-emerald-600 text-xl"></i> Riwayat Pengajuan Berkas Sidang
                        </h3>
                    </div>
                    <span class="text-xs font-bold px-4 py-2 rounded-2xl bg-slate-100 text-slate-700 border border-slate-200">
                        Total: <?= count($riwayat_sidang ?? []); ?> Pengajuan
                    </span>
                </div>
                <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-2xs mt-4">
                    <table class="responsive-log-table w-full text-left border-collapse text-xs sm:text-sm">
                        <thead class="bg-slate-50 text-slate-700 font-bold uppercase py-3.5 border-b border-slate-200">
                            <tr>
                                <th class="py-4 px-4 w-12 text-center">#</th>
                                <th class="py-4 px-4">File Sidang</th>
                                <th class="py-4 px-4">Catatan Anda</th>
                                <th class="py-4 px-4">Waktu Upload</th>
                                <th class="py-4 px-4 text-center">Status</th>
                                <th class="py-4 px-4 text-center">Komentar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium" id="logTableSidang">
                            <tr><td colspan="6" class="text-center py-8 text-slate-500"><i class="bi bi-arrow-repeat animate-spin mr-2 text-lg"></i> Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>

    <footer class="bg-white/90 border-t border-orange-100 py-6 text-center text-xs sm:text-sm text-slate-500 font-medium">
        &copy; <?= date('Y'); ?> IFIK Portal — Fakultas Industri Kreatif, Telkom University
    </footer>
    </div>

    <!-- ==========================================================
         UNIFIED COMMENT MODAL (P1, P2, U1, U2)
         ========================================================== -->
    <div id="unifiedCommentModal">
        <div class="absolute inset-0" onclick="closeUnifiedCommentModal(event)"></div>
        <div class="uc-modal-box relative" onclick="event.stopPropagation()">
            <div class="uc-header">
                <h3 class="text-sm font-extrabold flex items-center gap-2">
                    <i class="bi bi-chat-quote-fill text-orange-400"></i>
                    <span>Komentar Dosen</span>
                </h3>
                <button type="button" onclick="closeUnifiedCommentModal()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition cursor-pointer">
                    <i class="bi bi-x-lg text-sm"></i>
                </button>
            </div>
            <div class="px-5 pt-4">
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1"></p>
                <p class="text-sm font-bold text-slate-900 mb-3" id="ucStudentName"></p>
                <p class="text-[11px] text-slate-500 font-semibold mb-4" id="ucStageInfo"></p>
            </div>
            <div class="px-5 pb-5">
                <div class="flex flex-wrap gap-1.5 mb-4 border-b border-slate-200 pb-3" id="ucTabsContainer">
                    <button onclick="ucSwitchTab('p1')" id="ucTabP1" class="uc-tab-btn p1-active">Pembimbing 1</button>
                    <button onclick="ucSwitchTab('p2')" id="ucTabP2" class="uc-tab-btn p1-inactive">Pembimbing 2</button>
                    <button onclick="ucSwitchTab('u1')" id="ucTabU1" class="uc-tab-btn p1-inactive">Penguji 1</button>
                    <button onclick="ucSwitchTab('u2')" id="ucTabU2" class="uc-tab-btn p1-inactive">Penguji 2</button>
                </div>
                <div id="ucCommentContent" class="uc-comment-content">
                    <em class="text-slate-400">Belum ada komentar.</em>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Popup Catatan Pembimbing (Legacy) -->
    <div id="catatanModal" class="modal-overlay hidden" onclick="closeCatatanModal(event)">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between p-5 border-b border-slate-200 bg-gradient-to-r from-orange-50 to-amber-50">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="bi bi-chat-quote-fill text-orange-500 text-xl"></i> Catatan Pembimbing
                </h3>
                <button type="button" onclick="closeCatatanModal()" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-rose-600 hover:border-rose-300 flex items-center justify-center text-lg transition cursor-pointer">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body-scroll" id="catatanModalBody">
                <p class="text-slate-500 italic">Tidak ada catatan.</p>
            </div>
            <div class="p-4 border-t border-slate-200 flex justify-end bg-slate-50">
                <button type="button" onclick="closeCatatanModal()" class="px-5 py-2.5 rounded-xl bg-slate-700 hover:bg-slate-800 text-white text-sm font-bold transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- ==========================================================
         MODAL RINCIAN RUBRIK PENILAIAN SIDANG
         ========================================================== -->
    <div id="modalRubrikNilai" class="modal-overlay hidden" onclick="closeModalRubrikNilai(event)">
        <div class="modal-content !max-w-3xl" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between p-5 border-b border-slate-200 bg-gradient-to-r from-indigo-50 via-slate-50 to-orange-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-lg box-3d shadow-md shadow-indigo-600/30">
                        <i class="bi bi-card-checklist"></i>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-extrabold text-slate-900">Rincian Rubrik Penilaian Sidang</h3>
                        <p class="text-xs text-slate-500 font-medium">Program Studi: <?= htmlspecialchars($pendaftaran['prodi'] ?? 'FIK'); ?></p>
                    </div>
                </div>
                <button type="button" onclick="closeModalRubrikNilai()" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-rose-600 hover:border-rose-300 flex items-center justify-center text-lg transition cursor-pointer">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="modal-body-scroll space-y-4">
                <div class="grid grid-cols-3 gap-3 p-4 rounded-xl bg-slate-50 border border-slate-200 text-center">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nilai Akhir</span>
                        <span class="text-2xl font-black text-slate-900"><?= $nilai_akhir; ?></span>
                    </div>
                    <div class="border-x border-slate-200">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Grade</span>
                        <span class="text-2xl font-black text-indigo-700"><?= htmlspecialchars($grade_sidang); ?></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Status</span>
                        <span class="text-sm font-black text-emerald-700 uppercase"><?= htmlspecialchars($status_kelulusan); ?></span>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead class="bg-slate-50 text-slate-700 font-bold uppercase border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-3.5 w-10 text-center">No</th>
                                <th class="py-3 px-3.5">Aspek / Kriteria Penilaian</th>
                                <th class="py-3 px-3.5 text-center w-24">Bobot</th>
                                <th class="py-3 px-3.5 text-center w-24">Nilai Rata-rata</th>
                                <th class="py-3 px-3.5 text-center w-28">Poin Terbobot</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-800">
                            <?php if (!empty($criteria_items)): ?>
                                <?php
                                    $total_bobot     = 0;
                                    $total_terbobot  = 0;
                                    $posisi_list     = [1, 2, 3, 4];

                                    foreach ($criteria_items as $k_idx => $crit):
                                        $crit_title = $crit['title'] ?? ('Kriteria ' . ($k_idx + 1));
                                        $crit_desc  = $crit['desc']  ?? '';
                                        $crit_bobot = isset($crit['bobot']) ? (float)$crit['bobot'] : 0;
                                        $crit_id    = $crit['id'] ?? null;

                                        $scores = [];
                                        foreach ($posisi_list as $pos) {
                                            $detail_pos = $rekap_nilai_sidang['detail_per_posisi'][$pos]['detail'] ?? [];
                                            if (!is_array($detail_pos)) continue;
                                            foreach ($detail_pos as $dp) {
                                                if (($dp['id'] ?? null) === $crit_id && isset($dp['nilai'])) {
                                                    $scores[] = (float)$dp['nilai'];
                                                    break;
                                                }
                                            }
                                        }
                                        $crit_score = !empty($scores) ? (array_sum($scores) / count($scores)) : 0;
                                        $terbobot   = ($crit_bobot * $crit_score) / 100;
                                        $total_bobot    += $crit_bobot;
                                        $total_terbobot += $terbobot;
                                ?>
                                    <tr class="hover:bg-slate-50/70">
                                        <td class="py-3 px-3.5 text-center font-bold text-slate-400"><?= $k_idx + 1; ?></td>
                                        <td class="py-3 px-3.5">
                                            <div class="font-bold text-slate-900"><?= htmlspecialchars($crit_title); ?></div>
                                            <?php if (!empty($crit_desc)): ?>
                                                <div class="text-[11px] text-slate-500 font-normal leading-relaxed mt-0.5"><?= htmlspecialchars($crit_desc); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3 px-3.5 text-center font-bold text-slate-700 bg-slate-50/50"><?= $crit_bobot; ?>%</td>
                                        <td class="py-3 px-3.5 text-center font-bold text-slate-900"><?= number_format($crit_score, 1); ?></td>
                                        <td class="py-3 px-3.5 text-center font-black text-indigo-700 bg-indigo-50/30"><?= number_format($terbobot, 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="bg-slate-100/80 font-bold border-t-2 border-slate-200">
                                    <td colspan="2" class="py-3 px-3.5 text-right uppercase tracking-wider text-slate-700">Total Akumulasi Terbobot:</td>
                                    <td class="py-3 px-3.5 text-center text-slate-800"><?= $total_bobot; ?>%</td>
                                    <td class="py-3 px-3.5 text-center text-slate-400">-</td>
                                    <td class="py-3 px-3.5 text-center font-black text-emerald-700 text-sm"><?= number_format($total_terbobot, 2); ?></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400">
                                        <i class="bi bi-info-circle text-2xl mb-1 block"></i>
                                        Belum ada detail rubrik yang tersimpan. Nilai akhir akan terisi setelah semua penilai selesai mengisi.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (!empty($catatan_sidang)): ?>
                    <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs">
                        <span class="font-bold text-amber-800 uppercase tracking-wider text-[10px] block mb-1">Catatan Tambahan:</span>
                        <p class="text-slate-700 leading-relaxed font-medium italic">"<?= htmlspecialchars($catatan_sidang); ?>"</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="p-4 border-t border-slate-200 flex justify-end bg-slate-50">
                <button type="button" onclick="closeModalRubrikNilai()" class="px-5 py-2 rounded-xl bg-slate-700 hover:bg-slate-800 text-white text-xs font-bold transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/js/navbar_animated.js'); ?>?v=<?= time(); ?>"></script>
    <script>
        // ===================== MODAL RUBRIK PENILAIAN =====================
        function openModalRubrikNilai() {
            const modal = document.getElementById('modalRubrikNilai');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModalRubrikNilai(event) {
            const modal = document.getElementById('modalRubrikNilai');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        function toggleFormUploadSidang() {
            const formWrap = document.getElementById('wrapperFormUploadSidang');
            const txt = document.getElementById('toggleUploadFormText');
            if (!formWrap) return;

            if (formWrap.classList.contains('hidden')) {
                formWrap.classList.remove('hidden');
                if (txt) txt.textContent = 'Sembunyikan Form Berkas';
            } else {
                formWrap.classList.add('hidden');
                if (txt) txt.textContent = 'Tampilkan Form Berkas';
            }
        }

        // ============================================================
        // HELPERS: HTML ↔ PLAIN TEXT
        // ============================================================

        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function stripHtml(html) {
            if (!html) return '';
            const tmp = document.createElement('div');
            tmp.innerHTML = String(html);
            tmp.querySelectorAll('br').forEach(el => el.replaceWith(' '));
            tmp.querySelectorAll('p, div, li, h1, h2, h3, h4, h5, h6, blockquote')
                .forEach(el => { el.appendChild(document.createTextNode(' ')); });
            return (tmp.textContent || tmp.innerText || '').replace(/\s+/g, ' ').trim();
        }

        function sanitizeHtml(html) {
            if (!html) return '';
            const allowed = ['P','DIV','BR','B','STRONG','I','EM','U','S','UL','OL','LI','A','SPAN','H1','H2','H3','H4','H5','H6','BLOCKQUOTE','CODE','PRE'];
            const tmp = document.createElement('div');
            tmp.innerHTML = String(html);

            (function clean(node) {
                Array.from(node.childNodes).forEach(child => {
                    if (child.nodeType === 1) {
                        if (!allowed.includes(child.tagName)) {
                            const text = document.createTextNode(child.textContent || '');
                            child.parentNode.replaceChild(text, child);
                        } else {
                            Array.from(child.attributes).forEach(attr => {
                                const ok = child.tagName === 'A' &&
                                    ['href','target','rel'].includes(attr.name);
                                if (!ok) child.removeAttribute(attr.name);
                            });
                            if (child.tagName === 'A') {
                                child.setAttribute('target', '_blank');
                                child.setAttribute('rel', 'noopener noreferrer');
                            }
                            clean(child);
                        }
                    }
                });
            })(tmp);

            return tmp.innerHTML;
        }

        window._commentRegistry = window._commentRegistry || {};
        window._commentCounter = window._commentCounter || 0;

        function registerComment(data) {
            const id = 'cmt_' + (++window._commentCounter);
            window._commentRegistry[id] = data;
            return id;
        }
        function openCommentById(id) {
            const d = window._commentRegistry[id];
            if (!d) return;
            showUnifiedCommentModal(d.name, d.p1, d.p2, d.u1, d.u2, d.stage);
        }

        // ===================== UNIFIED COMMENT MODAL =====================
        let _ucData = { p1: '', p2: '', u1: '', u2: '' };
        let _ucActiveTab = 'p1';

        function ucRenderContent() {
            const content = document.getElementById('ucCommentContent');
            const labels = { p1: 'Pembimbing 1', p2: 'Pembimbing 2', u1: 'Penguji 1', u2: 'Penguji 2' };
            const comment = _ucData[_ucActiveTab] || '';
            const plain = stripHtml(comment);

            if (plain && plain.length > 0) {
                content.innerHTML = sanitizeHtml(comment);
            } else {
                content.innerHTML = `<em class="text-slate-400">Belum ada komentar dari ${labels[_ucActiveTab]}.</em>`;
            }
        }

        function ucSwitchTab(tab) {
            _ucActiveTab = tab;
            const map = { p1: 'ucTabP1', p2: 'ucTabP2', u1: 'ucTabU1', u2: 'ucTabU2' };
            const colorPrefix = { p1: 'p1', p2: 'p2', u1: 'u1', u2: 'u2' };

            for (const [key, elId] of Object.entries(map)) {
                const el = document.getElementById(elId);
                if (el) {
                    const prefix = colorPrefix[key];
                    el.className = 'uc-tab-btn ' + (key === tab ? `${prefix}-active` : `${prefix}-inactive`);
                }
            }
            ucRenderContent();
        }

        function showUnifiedCommentModal(studentName, p1, p2, u1, u2, stageInfo) {
            _ucData.p1 = p1 || '';
            _ucData.p2 = p2 || '';
            _ucData.u1 = u1 || '';
            _ucData.u2 = u2 || '';

            document.getElementById('ucStudentName').textContent = studentName || '-';
            document.getElementById('ucStageInfo').textContent = stageInfo || '';

            if (stripHtml(_ucData.p1)) _ucActiveTab = 'p1';
            else if (stripHtml(_ucData.p2)) _ucActiveTab = 'p2';
            else if (stripHtml(_ucData.u1)) _ucActiveTab = 'u1';
            else if (stripHtml(_ucData.u2)) _ucActiveTab = 'u2';
            else _ucActiveTab = 'p1';

            ucSwitchTab(_ucActiveTab);

            const modal = document.getElementById('unifiedCommentModal');
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeUnifiedCommentModal(event) {
            if (event) event.stopPropagation();
            const modal = document.getElementById('unifiedCommentModal');
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }

        // ===================== MODAL CATATAN (Legacy) =====================
        function openCatatanModal(catatanText, label = 'Catatan Pembimbing') {
            const modal = document.getElementById('catatanModal');
            const body = document.getElementById('catatanModalBody');
            if (!modal || !body) return;

            const plain = stripHtml(catatanText);
            if (!plain) {
                body.innerHTML = '<p class="text-slate-400 italic text-center py-8">Belum ada catatan dari pembimbing.</p>';
            } else {
                body.innerHTML = `
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-orange-600 uppercase tracking-wider">
                            <i class="bi bi-person-badge-fill"></i> ${escapeHtml(label)}
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-sm text-slate-800 leading-relaxed font-medium">
                            ${sanitizeHtml(catatanText)}
                        </div>
                    </div>
                `;
            }
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeCatatanModal(event) {
            const modal = document.getElementById('catatanModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCatatanModal();
                closeUnifiedCommentModal();
                closeModalRubrikNilai();
                closeModalPersyaratanSidang();
            }
        });

        // ===================== TOAST =====================
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-5 right-5 z-[99999] p-4 rounded-xl text-white font-bold shadow-lg transition-opacity ${type === 'success' ? 'bg-emerald-500' : 'bg-rose-500'}`;
            toast.innerHTML = `<i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'} mr-2"></i> ${escapeHtml(message)}`;
            document.body.appendChild(toast);
            setTimeout(() => { toast.style.opacity = '0'; setTimeout(()=>toast.remove(), 300); }, 3000);
        }

        // ===================== FILE UPLOADER SETUP =====================
        function setupFileUploader(tahapId) {
            const fileInput = document.getElementById('fileDraftP' + tahapId);
            const fileBadge = document.getElementById('fileBadgeP' + tahapId);
            const fileName = document.getElementById('fileNameP' + tahapId);
            const fileSize = document.getElementById('fileSizeP' + tahapId);
            const dropZone = document.getElementById('dropZoneP' + tahapId);

            if (fileInput && fileBadge) {
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const file = this.files[0];
                        fileName.textContent = file.name;
                        fileSize.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                        fileBadge.classList.remove('hidden');
                    }
                });
            }
            if (dropZone && fileInput) {
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, preventDefaults, false);
                });

                function preventDefaults(e) { e.preventDefault(); e.stopPropagation(); }

                ['dragenter', 'dragover'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => dropZone.classList.add('opacity-50'), false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => dropZone.classList.remove('opacity-50'), false);
                });

                dropZone.addEventListener('drop', (e) => {
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    if(files.length > 0) {
                        fileInput.files = files;
                        const event = new Event('change');
                        fileInput.dispatchEvent(event);
                    }
                }, false);
            }
        }
        setupFileUploader(1);
        setupFileUploader(2);

        function setupGenericFileUploader(fileInputId, badgeId, nameId, sizeId, dropZoneId) {
            const fileInput = document.getElementById(fileInputId);
            const fileBadge = document.getElementById(badgeId);
            const fileName = document.getElementById(nameId);
            const fileSize = document.getElementById(sizeId);
            const dropZone = document.getElementById(dropZoneId);

            if (fileInput && fileBadge) {
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const file = this.files[0];
                        fileName.textContent = file.name;
                        fileSize.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                        fileBadge.classList.remove('hidden');
                    }
                });
            }
            if (dropZone && fileInput) {
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, preventDefaults, false);
                });

                function preventDefaults(e) { e.preventDefault(); e.stopPropagation(); }

                ['dragenter', 'dragover'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => dropZone.classList.add('dragover'), false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => dropZone.classList.remove('dragover'), false);
                });

                dropZone.addEventListener('drop', (e) => {
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    if(files.length > 0) {
                        fileInput.files = files;
                        const event = new Event('change');
                        fileInput.dispatchEvent(event);
                    }
                }, false);
            }
        }

        setupGenericFileUploader('fileBimbinganP3', 'fileBadgeBimbinganP3', 'fileNameBimbinganP3', 'fileSizeBimbinganP3', 'dropZoneBimbinganP3');
        setupGenericFileUploader('fileSitasiP3', 'fileBadgeSitasiP3', 'fileNameSitasiP3', 'fileSizeSitasiP3', 'dropZoneSitasiP3');
        setupGenericFileUploader('filePersyaratanP3', 'fileBadgePersyaratanP3', 'fileNamePersyaratanP3', 'fileSizePersyaratanP3', 'dropZonePersyaratanP3');

        const fbBimbingan = document.getElementById('fileBimbinganP3');
        const fbSitasi = document.getElementById('fileSitasiP3');
        const fbPersyaratan = document.getElementById('filePersyaratanP3');
        
        if (fbBimbingan) {
            fbBimbingan.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    document.getElementById('submitWrapBimbinganP3').classList.remove('hidden');
                } else {
                    document.getElementById('submitWrapBimbinganP3').classList.add('hidden');
                }
            });
        }
        if (fbSitasi) {
            fbSitasi.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    document.getElementById('submitWrapSitasiP3').classList.remove('hidden');
                } else {
                    document.getElementById('submitWrapSitasiP3').classList.add('hidden');
                }
            });
        }
        if (fbPersyaratan) {
            fbPersyaratan.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    document.getElementById('submitWrapPersyaratanP3').classList.remove('hidden');
                } else {
                    document.getElementById('submitWrapPersyaratanP3').classList.add('hidden');
                }
            });
        }
        setupGenericFileUploader('fileSidangSingle', 'fileBadgeSidangSingle', 'fileNameSidangSingle', 'fileSizeSidangSingle', 'dropZoneSidangSingle');

        // ===================== TINYMCE INIT =====================
        tinymce.init({
            selector: 'textarea[name="catatan_mahasiswa"]',
            menubar: false,
            statusbar: false,
            plugins: 'lists link',
            toolbar: 'bold italic underline | bullist numlist | link',
            height: 200,
            skin: 'oxide',
            setup: function (editor) {
                editor.on('change', function () {
                    tinymce.triggerSave();
                });
            }
        });

        // ===================== AJAX FORM SUBMIT =====================
        ['formUploadPreview1', 'formUploadPreview2'].forEach((formId) => {
            const form = document.getElementById(formId);
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    tinymce.triggerSave();
                    const formData = new FormData(this);
                    const btn = this.querySelector('button[type="submit"]');
                    const originalBtnContent = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin mr-2"></i> Mengunggah...';

                    fetch('<?= site_url('mahasiswa/upload_preview_ajax') ?>', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.status) {
                            showToast(data.message, 'success');
                            this.reset();
                            const fileBadge = document.getElementById('fileBadgeP' + (formId === 'formUploadPreview1' ? '1' : '2'));
                            if(fileBadge) fileBadge.classList.add('hidden');
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            showToast(data.message || 'Terjadi kesalahan saat mengunggah', 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        showToast('Kesalahan koneksi', 'error');
                    })
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = originalBtnContent;
                    });
                });
            }
        });

        ['formUploadP3Bimbingan', 'formUploadP3Sitasi', 'formUploadP3Persyaratan'].forEach((formId) => {
            const form = document.getElementById(formId);
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    // tinymce is not on this form anymore, we just have a normal text input
                    const formData = new FormData(this);
                    const btn = this.querySelector('button[type="submit"]');
                    const originalBtnContent = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin mr-2"></i> Mengunggah...';

                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.status) {
                            showToast(data.message, 'success');
                            this.reset();
                            let suffix = formId.replace('formUploadP3', '');
                            const badge = document.getElementById('fileBadge' + suffix + 'P3');
                            if(badge) badge.classList.add('hidden');
                            document.getElementById('submitWrap' + suffix + 'P3').classList.add('hidden');
                            // Reload page to reflect approved status if possible, or wait for refresh
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            showToast(data.message || 'Terjadi kesalahan saat mengunggah', 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        showToast('Kesalahan koneksi', 'error');
                    })
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = originalBtnContent;
                    });
                });
            }
        });

        const formSidang = document.getElementById('formUploadSidang');
        if (formSidang) {
            formSidang.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const btn = this.querySelector('button[type="submit"]');
                const originalBtnContent = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin mr-2"></i> Mengunggah Berkas...';

                fetch('<?= site_url('mahasiswa/upload_sidang_ajax') ?>', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    if(data.status) {
                        showToast(data.message, 'success');
                        this.reset();
                        const badge = document.getElementById('fileBadgeSidangSingle');
                        if(badge) badge.classList.add('hidden');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showToast(data.message || 'Terjadi kesalahan saat mengunggah berkas sidang', 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Kesalahan koneksi', 'error');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalBtnContent;
                });
            });
        }

        // ===================== TAB SWITCHER =====================
        function switchPreviewTab(targetTab) {
            document.querySelectorAll('.tab-panel').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-card').forEach(el => {
                el.classList.remove('tab-card-active', 'border-orange-500', 'border-amber-300', 'border-indigo-300', 'border-teal-300', 'border-emerald-400', 'ring-4', 'ring-orange-400/20', 'ring-amber-400/20', 'ring-indigo-400/20', 'ring-teal-400/20', 'ring-emerald-400/20');
            });

            if (targetTab === 'preview1') {
                document.getElementById('panelPreview1').classList.remove('hidden');
                document.getElementById('tabBtnPreview1').classList.add('tab-card-active', 'border-orange-500', 'ring-4', 'ring-orange-400/20');
            } else if (targetTab === 'preview2') {
                document.getElementById('panelPreview2').classList.remove('hidden');
                document.getElementById('tabBtnPreview2').classList.add('tab-card-active', 'border-amber-300', 'ring-4', 'ring-amber-400/20');
            } else if (targetTab === 'preview3') {
                document.getElementById('panelPreview3').classList.remove('hidden');
                document.getElementById('tabBtnPreview3').classList.add('tab-card-active', 'border-indigo-300', 'ring-4', 'ring-indigo-400/20');
            } else if (targetTab === 'persyaratan_sidang') {
                const panelPs = document.getElementById('panelPersyaratanSidang');
                const btnPs = document.getElementById('tabBtnPersyaratanSidang');
                if (panelPs) panelPs.classList.remove('hidden');
                if (btnPs) btnPs.classList.add('tab-card-active', 'border-teal-300', 'ring-4', 'ring-teal-400/20');
                if (typeof loadMasterSyaratSidang === 'function' && (!_psMasterData || !_psMasterData.length)) {
                    loadMasterSyaratSidang();
                }
            } else if (targetTab === 'sidang') {
                document.getElementById('panelSidang').classList.remove('hidden');
                document.getElementById('tabBtnSidang').classList.add('tab-card-active', 'border-emerald-400', 'ring-4', 'ring-emerald-400/20');
            }
        }

        // ===================== RENDER LOG TABLE =====================
        function renderLogTable(data, tbodyId, type = 'preview1') {
            const tbody = document.getElementById(tbodyId);
            if(!tbody) return;
            if(!data || data.length === 0) {
                const colSpan = (type === 'preview3') ? 8 : 6;
                tbody.innerHTML = `<tr><td colspan="${colSpan}" class="text-center py-12 px-4 text-slate-500 font-medium">Belum ada dokumen yang diunggah untuk tahap ini.</td></tr>`;
                return;
            }

            const stageLabel = type === 'preview1' ? 'Preview 1' : type === 'preview2' ? 'Preview 2' : type === 'preview3' ? 'Preview 3' : 'Sidang TA';

            let html = '';
            data.forEach((row, index) => {
                let st = row.status_pembimbing || row.status || 'Pending';
                let badgeCls = 'bg-amber-100 text-amber-900 border-amber-300';
                let badgeIcon = 'bi-clock-fill text-amber-600';
                let badgeText = 'Menunggu Review';

                if (st === 'Approved') {
                    badgeCls = 'bg-emerald-100 text-emerald-800 border-emerald-300';
                    badgeIcon = 'bi-check-circle-fill text-emerald-600';
                    badgeText = 'Disetujui (ACC)';
                } else if (st === 'Revision') {
                    badgeCls = 'bg-rose-100 text-rose-800 border-rose-300';
                    badgeIcon = 'bi-x-circle-fill text-rose-600';
                    badgeText = 'Perlu Revisi';
                }

                const dt = new Date(row.created_at || row.uploaded_at);
                const timeHtml = isNaN(dt.getTime()) ? '-' : `${dt.toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'})} ${dt.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'})} WIB`;

                const p1 = row.catatan_pembimbing || '';
                const p2 = row.catatan_pembimbing_2 || '';
                const u1 = row.catatan_penguji_1 || '';
                const u2 = row.catatan_penguji_2 || '';

                const hasAnyComment = !!stripHtml(p1) || !!stripHtml(p2) || !!stripHtml(u1) || !!stripHtml(u2);

                let commentParts = [];
                if (stripHtml(p1)) commentParts.push('P1');
                if (stripHtml(p2)) commentParts.push('P2');
                if (stripHtml(u1)) commentParts.push('U1');
                if (stripHtml(u2)) commentParts.push('U2');

                let commentBtn = `<span class="text-slate-400 text-xs italic">-</span>`;
                if (hasAnyComment) {
                    const commentId = registerComment({
                        name: row.nama_mahasiswa || '',
                        p1: p1,
                        p2: p2,
                        u1: u1,
                        u2: u2,
                        stage: stageLabel
                    });
                    commentBtn = `<button type="button"
                            onclick="openCommentById('${commentId}')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-violet-100 hover:bg-violet-200 text-violet-700 border border-violet-200 rounded-lg text-[11px] font-bold transition cursor-pointer shadow-2xs">
                            <i class="bi bi-chat-quote-fill"></i>
                            <span>${commentParts.join(', ')}</span>
                        </button>`;
                }

                if (type === 'preview1' || type === 'preview2') {
                    const fileName = escapeHtml(row.file_draft || '');
                    const fileUrl = '<?= base_url('uploads/preview_ta/') ?>' + encodeURIComponent(row.file_draft || '');
                    const catatanTxt = escapeHtml(stripHtml(row.catatan_mahasiswa || '')) || '-';
                    html += `
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="cell-no py-4 px-6 text-center font-bold text-slate-700"><span>${index + 1}</span></td>
                        <td class="cell-file py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-lg shrink-0">
                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                </div>
                                <div class="min-w-0">
                                    <a href="${fileUrl}" target="_blank" class="log-file-link font-bold text-slate-900 block truncate max-w-xs text-sm hover:text-orange-600 hover:underline">${fileName}</a>
                                </div>
                            </div>
                        </td>
                        <td data-label="Catatan Anda" class="py-4 px-6 text-slate-600 max-w-xs font-normal">
                            <p class="line-clamp-2 italic">${catatanTxt}</p>
                        </td>
                        <td data-label="Waktu Upload" class="py-4 px-6 whitespace-nowrap text-slate-600 font-medium text-xs">${timeHtml}</td>
                        <td data-label="Status" class="cell-center py-4 px-6 text-center whitespace-nowrap">
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold border ${badgeCls} shadow-2xs">
                                <i class="bi ${badgeIcon}"></i> ${badgeText}
                            </span>
                        </td>
                        <td data-label="Komentar" class="cell-center py-4 px-6 text-center">${commentBtn}</td>
                    </tr>`;
                }
                else if (type === 'preview3') {
                    const fileBimbingan = escapeHtml(row.file_bimbingan || '-');
                    const fileSitasi = escapeHtml(row.file_sitasi || '-');
                    const filePersyaratan = escapeHtml(row.file_persyaratan || '-');
                    const catatanTxt = escapeHtml(stripHtml(row.catatan_mahasiswa || '')) || '-';
                    html += `
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="cell-no py-4 px-4 text-center font-bold text-slate-700"><span>${index + 1}</span></td>
                        <td data-label="File Bimbingan" class="cell-file py-4 px-4 text-xs font-mono max-w-[120px] truncate" title="${fileBimbingan}">${fileBimbingan}</td>
                        <td data-label="File Sitasi" class="py-4 px-4 text-xs font-mono max-w-[120px] truncate" title="${fileSitasi}">${fileSitasi}</td>
                        <td data-label="Persyaratan" class="py-4 px-4 text-xs font-mono max-w-[120px] truncate" title="${filePersyaratan}">${filePersyaratan}</td>
                        <td data-label="Catatan Anda" class="py-4 px-4 text-slate-600 max-w-xs font-normal">
                            <p class="line-clamp-2 italic">${catatanTxt}</p>
                        </td>
                        <td data-label="Waktu Upload" class="py-4 px-4 whitespace-nowrap text-slate-600 font-medium text-xs">${timeHtml}</td>
                        <td data-label="Status" class="cell-center py-4 px-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold border ${badgeCls} shadow-2xs">
                                <i class="bi ${badgeIcon}"></i> ${badgeText}
                            </span>
                        </td>
                        <td data-label="Komentar" class="cell-center py-4 px-4 text-center">${commentBtn}</td>
                    </tr>`;
                }
                else if (type === 'sidang') {
                    const fileSidangRaw = row.file_sidang || row.file_draft || '';
                    const fileSidang = escapeHtml(fileSidangRaw) || '-';
                    const fileUrl = '<?= base_url('uploads/sidang/') ?>' + encodeURIComponent(fileSidangRaw);
                    const catatanTxt = escapeHtml(stripHtml(row.catatan_sidang || row.catatan_mahasiswa || '')) || '-';
                    html += `
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="cell-no py-4 px-4 text-center font-bold text-slate-700"><span>${index + 1}</span></td>
                        <td class="cell-file py-4 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-lg shrink-0">
                                    <i class="bi bi-file-earmark-text-fill"></i>
                                </div>
                                <div class="min-w-0">
                                    <a href="${fileUrl}" target="_blank" class="log-file-link font-bold text-slate-900 block truncate max-w-xs text-sm hover:text-emerald-600 hover:underline">${fileSidang}</a>
                                </div>
                            </div>
                        </td>
                        <td data-label="Catatan Anda" class="py-4 px-4 text-slate-600 max-w-xs font-normal">
                            <p class="line-clamp-2 italic">${catatanTxt}</p>
                        </td>
                        <td data-label="Waktu Upload" class="py-4 px-4 whitespace-nowrap text-slate-600 font-medium text-xs">${timeHtml}</td>
                        <td data-label="Status" class="cell-center py-4 px-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold border ${badgeCls} shadow-2xs">
                                <i class="bi ${badgeIcon}"></i> ${badgeText}
                            </span>
                        </td>
                        <td data-label="Komentar" class="cell-center py-4 px-4 text-center">${commentBtn}</td>
                    </tr>`;
                }
            });
            tbody.innerHTML = html;
        }

        function openStatusNote(key) {
            const notes = window._statusNotes || {};
            openCatatanModal(notes[key] || '', key === 'p1' ? 'Catatan Pembimbing 1' : 'Catatan Pembimbing 2');
        }

        function renderStatusCard(data) {
            const box = document.getElementById('statusCardContainer');
            if (!box) return;

            const stageNo = data.latest_p3 ? 3 : (data.latest_p2 ? 2 : (data.latest_p1 ? 1 : 0));
            const latest = stageNo ? data['latest_p' + stageNo] : null;
            let text = 'Belum Memulai Bimbingan', icon = 'bi-dash-circle', tone = 'text-white/70';

            if (data.is_p3_app) {
                if (data.is_ps_app) {
                    text = 'Lulus Persyaratan Sidang (Siap Sidang TA)'; icon = 'bi-check-circle-fill'; tone = 'text-emerald-300';
                } else if (data.is_ps_revision) {
                    text = 'Persyaratan Sidang Perlu Revisi (Admin LAA)'; icon = 'bi-x-circle-fill'; tone = 'text-rose-300';
                } else if (data.upload_count_ps > 0) {
                    text = 'Persyaratan Sidang Diverifikasi Admin LAA'; icon = 'bi-clock-fill'; tone = 'text-amber-200';
                } else {
                    text = 'Lulus Preview 3 (Lanjut Persyaratan Sidang)'; icon = 'bi-check-circle-fill'; tone = 'text-emerald-300';
                }
            }
            else if (data.is_p2_app) { text = 'Lulus Preview 2 (Lanjut Preview 3)'; icon = 'bi-check-circle-fill'; tone = 'text-emerald-300'; }
            else if (data.is_p1_app) { text = 'Lulus Preview 1 (Lanjut Preview 2)'; icon = 'bi-check-circle-fill'; tone = 'text-emerald-300'; }
            else if (stageNo) {
                if (latest && latest.status_pembimbing === 'Revision') {
                    text = 'Preview ' + stageNo + ' Perlu Revisi'; icon = 'bi-x-circle-fill'; tone = 'text-rose-300';
                } else if (latest && latest.status_pembimbing === 'Approved') {
                    text = 'Berkas Preview ' + stageNo + ' Disetujui (Menunggu Lulus Tahap)'; icon = 'bi-clock-fill'; tone = 'text-amber-200';
                } else {
                    text = 'Preview ' + stageNo + ' Sedang Direview'; icon = 'bi-clock-fill'; tone = 'text-amber-200';
                }
            }

            const n1 = latest && stripHtml(latest.catatan_pembimbing) ? latest.catatan_pembimbing : '';
            const n2 = latest && stripHtml(latest.catatan_pembimbing_2) ? latest.catatan_pembimbing_2 : '';
            window._statusNotes = { p1: n1, p2: n2 };

            const btn = (key, label) => `<button type="button" onclick="openStatusNote('${key}')" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white/15 hover:bg-white/25 border border-white/20 text-[10px] font-bold text-white cursor-pointer"><i class="bi bi-chat-quote-fill"></i> ${label}</button>`;
            const notesHtml = (n1 || n2) ? `<div class="flex flex-wrap gap-1 mt-1">${n1 ? btn('p1', 'Catatan P1') : ''}${n2 ? btn('p2', 'Catatan P2') : ''}</div>` : '';

            box.innerHTML = `
                <i class="bi ${icon} ${tone} text-xl"></i>
                <div class="min-w-0 flex-1">
                    <span class="text-[9px] font-bold uppercase tracking-wider text-amber-200 block">Status Bimbingan Terkini</span>
                    <h4 class="text-xs font-bold text-white leading-snug">${text}</h4>
                    ${notesHtml}
                </div>`;
        }

        function renderDeCardPreview2(d) {
            const container = document.getElementById('deContainerPreview2');
            if (!container) return;

            if (!d || !d.is_de) {
                container.classList.add('hidden');
                return;
            }

            container.classList.remove('hidden');

            // Jadwal DE
            const waktuEl = document.getElementById('deJadwalWaktuText');
            if (waktuEl) {
                let tglText = 'Belum Dijadwalkan';
                if (d.tanggal_presentasi) {
                    try {
                        const dt = new Date(d.tanggal_presentasi + (d.waktu_presentasi ? 'T' + d.waktu_presentasi : ''));
                        tglText = dt.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
                            + (d.waktu_presentasi ? ' • ' + d.waktu_presentasi.substring(0, 5) + ' WIB' : '');
                    } catch(e) { tglText = d.tanggal_presentasi; }
                }
                waktuEl.textContent = tglText;
            }

            const ruangEl = document.getElementById('deJadwalRuangText');
            if (ruangEl) ruangEl.textContent = d.ruang_sidang || 'Belum Ditentukan';

            const linkWrap = document.getElementById('deJadwalLinkWrap');
            const linkText = document.getElementById('deJadwalLinkText');
            const linkBtn  = document.getElementById('deJadwalLinkBtn');
            if (linkWrap && linkText && linkBtn) {
                if (d.link_presentasi) {
                    linkWrap.classList.remove('hidden');
                    linkWrap.style.display = 'flex';
                    linkText.textContent = d.link_presentasi;
                    linkBtn.href = d.link_presentasi;
                } else {
                    linkWrap.classList.add('hidden');
                    linkWrap.style.display = 'none';
                }
            }

            const catWrap = document.getElementById('deJadwalCatatanWrap');
            const catText = document.getElementById('deJadwalCatatanText');
            if (catWrap && catText) {
                if (d.komentar_kelayakan2) {
                    catWrap.classList.remove('hidden');
                    catText.textContent = d.komentar_kelayakan2;
                } else {
                    catWrap.classList.add('hidden');
                }
            }

            // Nilai DE
            const p1El = document.getElementById('deNilaiP1');
            const p2El = document.getElementById('deNilaiP2');
            const u1El = document.getElementById('deNilaiU1');
            const u2El = document.getElementById('deNilaiU2');
            if (p1El) p1El.innerHTML = (d.nilai_pembimbing1 !== null && d.nilai_pembimbing1 !== '') ? d.nilai_pembimbing1 : '<span class="text-slate-300 text-xs font-bold italic">Belum</span>';
            if (p2El) p2El.innerHTML = (d.nilai_pembimbing2 !== null && d.nilai_pembimbing2 !== '') ? d.nilai_pembimbing2 : '<span class="text-slate-300 text-xs font-bold italic">Belum</span>';
            if (u1El) u1El.innerHTML = (d.nilai_penguji1 !== null && d.nilai_penguji1 !== '') ? d.nilai_penguji1 : '<span class="text-slate-300 text-xs font-bold italic">Belum</span>';
            if (u2El) u2El.innerHTML = (d.nilai_penguji2 !== null && d.nilai_penguji2 !== '') ? d.nilai_penguji2 : '<span class="text-slate-300 text-xs font-bold italic">Belum</span>';

            const avgEl = document.getElementById('deNilaiAvg');
            if (avgEl) avgEl.textContent = (d.rata_rata && d.rata_rata > 0) ? d.rata_rata : '-';

            const badgeEl = document.getElementById('deStatusLulusBadge');
            if (badgeEl) {
                if (d.is_lulus_syarat) {
                    badgeEl.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300';
                    badgeEl.innerHTML = '<i class="bi bi-check-circle-fill text-emerald-600"></i> Memenuhi Syarat (> 60)';
                } else if (d.semua_dinilai) {
                    badgeEl.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black bg-rose-100 text-rose-800 border border-rose-300';
                    badgeEl.innerHTML = '<i class="bi bi-x-circle-fill text-rose-600"></i> Rata-rata ≤ 60';
                } else {
                    badgeEl.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black bg-amber-100 text-amber-800 border border-amber-300';
                    badgeEl.innerHTML = `<i class="bi bi-hourglass-split text-amber-600"></i> ${d.count_graded || 0}/4 Dinilai`;
                }
            }
        }

        // ===================== SSE REALTIME =====================
        let mahasiswaEventSource = null;
        function startMahasiswaSSE() {
            if (mahasiswaEventSource) mahasiswaEventSource.close();
            mahasiswaEventSource = new EventSource('<?= site_url('mahasiswa/sse_mahasiswa_bimbingan') ?>');
            mahasiswaEventSource.onmessage = function(event) {
                try {
                    const data = JSON.parse(event.data);
                    if(data) {
                        renderLogTable(data.riwayat_p1, 'logTablePreview1', 'preview1');
                        renderLogTable(data.riwayat_p2, 'logTablePreview2', 'preview2');
                        renderLogTable(data.riwayat_p3, 'logTablePreview3', 'preview3');
                        if (data.riwayat_sidang !== undefined) {
                            renderLogTable(data.riwayat_sidang, 'logTableSidang', 'sidang');
                        }
                        renderStatusCard(data);
                        if (data.de_detail) {
                            renderDeCardPreview2(data.de_detail);
                        }
                        
                        const orig_is_p1_app = <?php echo $is_p1_app ? 'true' : 'false'; ?>;
                        const orig_is_p2_app = <?php echo $is_p2_app ? 'true' : 'false'; ?>;
                        const orig_is_p3_app = <?php echo $is_p3_app ? 'true' : 'false'; ?>;
                        const orig_is_ps_app = <?php echo $is_ps_app ? 'true' : 'false'; ?>;
                        const orig_is_ps_rev = <?php echo $is_ps_revision ? 'true' : 'false'; ?>;

                        // Deteksi perubahan status per-file Preview 3
                        const orig_has_app_bimbingan   = <?php echo (!empty($has_app_bimbingan)   ? 'true' : 'false'); ?>;
                        const orig_has_app_sitasi      = <?php echo (!empty($has_app_sitasi)      ? 'true' : 'false'); ?>;
                        const orig_has_app_persyaratan = <?php echo (!empty($has_app_persyaratan) ? 'true' : 'false'); ?>;

                        // Deteksi perubahan status review (Pending -> Revision / Approved)
                        const orig_st_p1              = <?= json_encode($latest_p1['status_pembimbing'] ?? '') ?>;
                        const orig_st_p2              = <?= json_encode($latest_p2['status_pembimbing'] ?? '') ?>;
                        const orig_st_sidang          = <?= json_encode($latest_sidang['status_pembimbing'] ?? '') ?>;
                        const orig_is_sidang_sched    = <?= !empty($is_sidang_scheduled) ? 'true' : 'false' ?>;
                        
                        const fileStatusChanged =
                            (data.has_app_bimbingan   && !orig_has_app_bimbingan)   ||
                            (data.has_app_sitasi       && !orig_has_app_sitasi)      ||
                            (data.has_app_persyaratan  && !orig_has_app_persyaratan);

                        const curr_st_p1           = data.latest_p1 ? (data.latest_p1.status_pembimbing || '') : '';
                        const curr_st_p2           = data.latest_p2 ? (data.latest_p2.status_pembimbing || '') : '';
                        const curr_st_sidang       = data.latest_sidang ? (data.latest_sidang.status_pembimbing || '') : '';
                        const curr_is_sidang_sched = !!data.is_sidang_scheduled;

                        const reviewStatusChanged =
                            (orig_st_p1 && curr_st_p1 && orig_st_p1 !== curr_st_p1) ||
                            (orig_st_p2 && curr_st_p2 && orig_st_p2 !== curr_st_p2) ||
                            (orig_st_sidang && curr_st_sidang && orig_st_sidang !== curr_st_sidang) ||
                            (curr_is_sidang_sched !== orig_is_sidang_sched) ||
                            (!!data.is_ps_revision !== orig_is_ps_rev);

                        if ((!!data.is_p1_app !== orig_is_p1_app) || (!!data.is_p2_app !== orig_is_p2_app) || (!!data.is_p3_app !== orig_is_p3_app) || (!!data.is_ps_app !== orig_is_ps_app) || fileStatusChanged || reviewStatusChanged) {
                            window.location.reload();
                        }
                    }
                } catch(e) { console.error('SSE Error:', e); }
            };
            mahasiswaEventSource.onerror = function() {
                console.log('SSE connection lost, retrying...');
            };
        }

        document.addEventListener('DOMContentLoaded', () => {
            startMahasiswaSSE();
            renderLogTable(<?= json_encode($riwayat_preview1 ?? []) ?>, 'logTablePreview1', 'preview1');
            renderLogTable(<?= json_encode($riwayat_preview2 ?? []) ?>, 'logTablePreview2', 'preview2');
            renderLogTable(<?= json_encode($riwayat_preview3 ?? []) ?>, 'logTablePreview3', 'preview3');
            renderLogTable(<?= json_encode($riwayat_sidang ?? []) ?>, 'logTableSidang', 'sidang');
            if (typeof loadMasterSyaratSidang === 'function') {
                loadMasterSyaratSidang();
            }
        });
    </script>

    <?php $this->load->view('partials/modal_rekomendasi_sidang'); ?>

    <!-- ========================================================== -->
    <!-- FLOATING POPUP: BAP SIDANG UNTUK MAHASISWA (kondisional)   -->
    <!-- ========================================================== -->
    <?php if (!empty($pendaftaran['bap_published'])): ?>
    <div id="studentBapContainer" class="fixed inset-0 z-[100000] pointer-events-none p-4 sm:p-6 flex items-center justify-center" style="display:none;">
        <div class="absolute inset-0 bg-slate-900/55 backdrop-blur-sm pointer-events-auto" onclick="closeStudentBapPopup()"></div>
        <div class="relative pointer-events-auto bg-white rounded-3xl shadow-2xl border border-slate-300 flex flex-col overflow-hidden w-[92vw] sm:w-[600px] xl:w-[720px] h-[85vh] max-h-[92vh] animate-pop-in">
            <div class="p-3 px-4 bg-slate-900 text-white flex items-center justify-between gap-2 shrink-0 border-b border-slate-800">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-lg bg-purple-500/25 border border-purple-400/40 text-purple-300 flex items-center justify-center font-bold text-sm shrink-0">
                        <i class="bi bi-file-earmark-check-fill"></i>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-sm font-extrabold text-white truncate">Berita Acara Sidang Tugas Akhir</h4>
                        <p class="text-[10px] text-slate-300 truncate">Dokumen BAP resmi dari dewan sidang</p>
                    </div>
                </div>
                <button type="button" onclick="closeStudentBapPopup()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-rose-600 text-slate-300 hover:text-white flex items-center justify-center transition cursor-pointer">
                    <i class="bi bi-x-lg text-sm"></i>
                </button>
            </div>

            <div class="bg-slate-950 p-2 px-3 flex items-center gap-2 border-b border-slate-800 shrink-0">
                <button type="button" onclick="studentBapSwitchTab('igracias')" id="stBtnBapIgracias"
                        class="flex-1 py-2 px-3 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 cursor-pointer bg-orange-600 text-white shadow-xs transition">
                    <i class="bi bi-file-earmark-text-fill"></i><span>1. BAP (IGRACIAS)</span>
                </button>
                <button type="button" onclick="studentBapSwitchTab('fakultas')" id="stBtnBapFakultas"
                        class="flex-1 py-2 px-3 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 cursor-pointer bg-slate-800 text-slate-400 hover:bg-slate-700 hover:text-white transition">
                    <i class="bi bi-award-fill"></i><span>2. BAP FAKULTAS</span>
                </button>
            </div>

            <div class="flex-1 bg-slate-100 p-2 overflow-hidden flex flex-col relative">
                <iframe id="stBapIframe"
                        src=""
                        class="w-full h-full bg-white rounded-2xl shadow-inner border border-slate-200"
                        frameborder="0"></iframe>
            </div>

            <div class="p-2.5 px-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs shrink-0 flex-wrap gap-2">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="font-mono text-[11px] font-bold text-orange-600"><?= htmlspecialchars($pendaftaran['nim'] ?? $nim); ?></span>
                    <span class="text-slate-400 text-[11px]">|</span>
                    <span class="text-[11px] text-slate-600 truncate font-semibold">
                        <?= htmlspecialchars(trim(($mahasiswa['nama_depan'] ?? 'Mahasiswa') . ' ' . ($mahasiswa['nama_belakang'] ?? ''))); ?>
                    </span>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <a id="stBtnCetakBap" href="#" target="_blank"
                       class="px-3.5 py-2 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-bold text-[11px] transition flex items-center gap-1.5 cursor-pointer shadow-2xs">
                        <i class="bi bi-printer-fill text-[11px]"></i> Cetak PDF
                    </a>
                    <button type="button" onclick="closeStudentBapPopup()"
                            class="px-3 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-600 border border-slate-200 font-bold text-[11px] transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const ST_NIM = <?= json_encode($pendaftaran['nim'] ?? $nim); ?>;
        const ST_URL_BAP_IGRACIAS_PREVIEW = '<?= site_url('adminlayanan/preview_bap_igracias/'); ?>' + ST_NIM;
        const ST_URL_BAP_FAKULTAS_PREVIEW = '<?= site_url('adminlayanan/preview_bap_fakultas/'); ?>' + ST_NIM;
        const ST_URL_BAP_IGRACIAS_PRINT   = '<?= site_url('adminlayanan/cetak_bap_igracias/'); ?>'   + ST_NIM;
        const ST_URL_BAP_FAKULTAS_PRINT   = '<?= site_url('adminlayanan/cetak_bap_fakultas/'); ?>'   + ST_NIM;

        let stBapActiveTab = 'igracias';

        function studentBapSwitchTab(type) {
            stBapActiveTab = type;
            const btnIgracias = document.getElementById('stBtnBapIgracias');
            const btnFakultas = document.getElementById('stBtnBapFakultas');
            const iframe      = document.getElementById('stBapIframe');
            const btnCetak    = document.getElementById('stBtnCetakBap');
            if (!iframe) return;

            const ACTIVE_ORANGE = 'flex-1 py-2 px-3 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 cursor-pointer bg-orange-600 text-white shadow-xs transition';
            const ACTIVE_INDIGO = 'flex-1 py-2 px-3 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 cursor-pointer bg-indigo-600 text-white shadow-xs transition';
            const INACTIVE      = 'flex-1 py-2 px-3 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 cursor-pointer bg-slate-800 text-slate-400 hover:bg-slate-700 hover:text-white transition';

            if (type === 'igracias') {
                iframe.src = ST_URL_BAP_IGRACIAS_PREVIEW;
                if (btnCetak) btnCetak.href = ST_URL_BAP_IGRACIAS_PRINT;
                if (btnIgracias) btnIgracias.className = ACTIVE_ORANGE;
                if (btnFakultas) btnFakultas.className = INACTIVE;
            } else {
                iframe.src = ST_URL_BAP_FAKULTAS_PREVIEW;
                if (btnCetak) btnCetak.href = ST_URL_BAP_FAKULTAS_PRINT;
                if (btnFakultas) btnFakultas.className = ACTIVE_INDIGO;
                if (btnIgracias) btnIgracias.className = INACTIVE;
            }
        }

        function openStudentBapPopup() {
            const container = document.getElementById('studentBapContainer');
            if (!container) return;
            container.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            studentBapSwitchTab(stBapActiveTab || 'igracias');
        }

        function closeStudentBapPopup() {
            const container = document.getElementById('studentBapContainer');
            if (!container) return;
            container.style.display = 'none';
            document.body.style.overflow = '';
        }

        window.openStudentBapPopup  = openStudentBapPopup;
        window.closeStudentBapPopup = closeStudentBapPopup;
        window.studentBapSwitchTab  = studentBapSwitchTab;
    </script>
    <?php endif; ?>

    <script>
        // ============================================================
        // TAHAP PERSYARATAN SIDANG — DINAMIS DARI MASTER LAA
        // ============================================================
        let _psMasterData = [];
        let _psUploadedData = [];

        function openModalPersyaratanSidang() {
            switchPreviewTab('persyaratan_sidang');
        }

        function closeModalPersyaratanSidang() {}

        function loadMasterSyaratSidang() {
            const body = document.getElementById('psBodyContent');
            if (body) {
                body.innerHTML = `<div class="text-center py-10 text-slate-400">
                    <i class="bi bi-arrow-repeat animate-spin text-2xl inline-block"></i>
                    <p class="mt-2 text-xs font-semibold">Memuat daftar persyaratan sidang...</p>
                </div>`;
            }

            fetch('<?= site_url("mahasiswa/get_master_syarat_sidang_ajax") ?>')
                .then(r => r.json())
                .then(res => {
                    if (!res.status) throw new Error(res.message || 'Gagal memuat data.');

                    const uploaded = (res.uploaded || []).filter(b => {
                        if (!b || !b.is_uploaded || !b.file_name) return false;
                        if (b.file_name.startsWith('berkas_')) return false;
                        if (/^[a-z0-9_]+_\d+\.pdf$/i.test(b.file_name)) return false;
                        return true;
                    });

                    _psMasterData   = res.master || [];
                    _psUploadedData = uploaded;
                    renderSyaratSidangForm();
                    renderLogTablePersyaratanSidang();
                })
                .catch(err => {
                    console.error('[PS] fetch error:', err);
                    if (body) body.innerHTML = `<div class="text-center py-10 text-rose-500 text-xs font-bold">${err.message}</div>`;
                });
        }

        function renderLogTablePersyaratanSidang() {
            const tbody = document.getElementById('logTablePersyaratanSidang');
            if (!tbody) return;
            if (!_psMasterData || !_psMasterData.length) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center py-12 px-4 text-slate-500 font-medium">Belum ada daftar persyaratan sidang yang dikonfigurasi.</td></tr>`;
                return;
            }
            let html = '';
            _psMasterData.forEach((item, idx) => {
                const kode = item.kode_berkas || '';
                const uploaded = _psUploadedData.find(u => u.kode === kode);
                const hasFile = uploaded && uploaded.file_name;
                const statusSt = hasFile ? (uploaded.status || 'Pending Verifikasi') : 'Belum Diunggah';
                const catatan = hasFile && uploaded.catatan ? escapeHtml(uploaded.catatan) : '-';

                let badgeHtml = `<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200"><i class="bi bi-dash-circle"></i> Belum Diunggah</span>`;
                if (hasFile) {
                    const s = statusSt.toLowerCase();
                    if (s.includes('setuju') || s.includes('valid') || s.includes('approved')) {
                        badgeHtml = `<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-2xs"><i class="bi bi-check-circle-fill text-emerald-600"></i> Disetujui Admin LAA</span>`;
                    } else if (s.includes('revisi') || s.includes('tolak') || s.includes('invalid') || s.includes('rejected')) {
                        badgeHtml = `<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300 shadow-2xs"><i class="bi bi-x-circle-fill text-rose-600"></i> Perlu Revisi LAA</span>`;
                    } else {
                        badgeHtml = `<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300 shadow-2xs"><i class="bi bi-clock-fill text-amber-600"></i> Menunggu Verifikasi LAA</span>`;
                    }
                }

                const fileCell = hasFile
                    ? `<div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 border border-teal-200 flex items-center justify-center text-base shrink-0">
                            <i class="bi bi-file-earmark-check-fill"></i>
                        </div>
                        <a href="${uploaded.file_url}" target="_blank" class="font-bold text-slate-900 hover:text-teal-600 hover:underline truncate max-w-xs block text-xs sm:text-sm">${escapeHtml(uploaded.file_name)}</a>
                       </div>`
                    : `<span class="text-slate-400 italic text-xs">Belum ada berkas</span>`;

                html += `
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="cell-no py-4 px-4 text-center font-bold text-slate-700"><span>${idx + 1}</span></td>
                    <td class="py-4 px-4">
                        <div class="font-bold text-slate-900 text-xs sm:text-sm">${escapeHtml(item.nama_berkas || '')} ${parseInt(item.is_required) === 1 ? '<span class="text-rose-500">*</span>' : ''}</div>
                        ${item.deskripsi ? `<div class="text-xs text-slate-500 font-normal mt-0.5">${escapeHtml(item.deskripsi)}</div>` : ''}
                    </td>
                    <td data-label="Dokumen Terunggah" class="py-4 px-4">${fileCell}</td>
                    <td data-label="Status Verifikasi LAA" class="cell-center py-4 px-4 text-center whitespace-nowrap">${badgeHtml}</td>
                    <td data-label="Catatan Admin LAA" class="py-4 px-4 text-slate-600 text-xs italic">${catatan}</td>
                </tr>`;
            });
            tbody.innerHTML = html;
        }

        function _psStatusBadge(status) {
            const s = (status || '').toLowerCase();
            if (s.includes('setuju') || s.includes('valid') || s.includes('approved')) {
                return '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200"><i class="bi bi-check-circle-fill"></i> Disetujui LAA</span>';
            }
            if (s.includes('revisi') || s.includes('tolak') || s.includes('invalid') || s.includes('rejected')) {
                return '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200"><i class="bi bi-x-circle-fill"></i> Perlu Revisi</span>';
            }
            return '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200"><i class="bi bi-clock-fill"></i> Menunggu</span>';
        }

        function renderSyaratSidangForm() {
            const body = document.getElementById('psBodyContent');
            if (!body) return;

            if (!_psMasterData.length) {
                body.innerHTML = `<div class="text-center py-10 text-slate-400 text-xs font-semibold">
                    <i class="bi bi-inbox text-3xl block mb-2"></i>
                    Belum ada berkas persyaratan yang dikonfigurasi oleh Admin LAA.
                </div>`;
                return;
            }

            const totalReq = _psMasterData.filter(m => parseInt(m.is_required) === 1).length;
            const uploadedReq = _psMasterData.filter(m => {
                if (parseInt(m.is_required) !== 1) return false;
                const u = _psUploadedData.find(x => x.kode === m.kode_berkas);
                return u && u.file_name;
            }).length;

            let html = `
                <div class="p-3.5 rounded-2xl bg-gradient-to-r from-orange-50 to-amber-50 border border-orange-200 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-orange-500 text-white flex items-center justify-center text-sm"><i class="bi bi-info-circle-fill"></i></div>
                        <div>
                            <span class="text-xs font-extrabold text-slate-800 block">Progres Berkas Wajib</span>
                            <span class="text-[11px] text-slate-500">Unggah semua berkas bertanda <span class="text-rose-500 font-bold">*</span></span>
                        </div>
                    </div>
                    <span class="text-xs font-black text-orange-700 bg-white px-3 py-1.5 rounded-xl border border-orange-200">
                        ${uploadedReq} / ${totalReq}
                    </span>
                </div>
            `;

            _psMasterData.forEach((item, idx) => {
                const kode      = item.kode_berkas || '';
                const uploaded  = _psUploadedData.find(u => u.kode === kode);
                const isReq     = parseInt(item.is_required) === 1;
                const hasFile   = uploaded && uploaded.file_name;
                const statusSt  = uploaded ? uploaded.status : '';

                html += `
                    <div class="p-4 rounded-2xl border ${hasFile ? 'border-emerald-200 bg-emerald-50/30' : 'border-slate-200 bg-white'} space-y-3 transition">
                        
                        <!-- HEADER: NAMA + STATUS -->
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <label class="font-bold text-slate-800 text-xs sm:text-sm block">
                                    ${idx + 1}. ${item.nama_berkas}
                                    ${isReq ? '<span class="text-rose-500">*</span>' : '<span class="text-[10px] text-slate-400 font-medium ml-1">(opsional)</span>'}
                                </label>
                                ${item.deskripsi ? `<p class="text-[11px] text-slate-500 mt-0.5 leading-snug">${item.deskripsi}</p>` : ''}
                            </div>
                            ${hasFile ? _psStatusBadge(statusSt) : ''}
                        </div>

                        <!-- DROPZONE (DI ATAS) -->
                        <div class="ps-dropzone relative border-2 border-dashed ${hasFile ? 'border-emerald-300 bg-emerald-50/40' : 'border-slate-300 hover:border-orange-400 bg-slate-50/50 hover:bg-orange-50/40'} rounded-2xl p-5 text-center transition-all cursor-pointer group"
                             data-kode="${kode}"
                             ondragover="psDragOver(event, this)"
                             ondragleave="psDragLeave(event, this)"
                             ondrop="psDrop(event, this)">
                            
                            <input type="file" name="berkas[${kode}]" data-kode="${kode}"
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   class="ps-file-input absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            
                            <div class="pointer-events-none space-y-2">
                                <div class="ps-dz-icon w-12 h-12 rounded-2xl ${hasFile ? 'bg-gradient-to-tr from-emerald-500 to-teal-400' : 'bg-gradient-to-tr from-orange-500 to-amber-400'} text-white flex items-center justify-center text-xl mx-auto transition-transform group-hover:scale-110 shadow-md ${hasFile ? 'shadow-emerald-500/25' : 'shadow-orange-500/25'}">
                                    <i class="bi ${hasFile ? 'bi-arrow-repeat' : 'bi-cloud-arrow-up-fill'}"></i>
                                </div>
                                <div>
                                    <p class="ps-dz-title text-xs sm:text-sm font-bold ${hasFile ? 'text-emerald-700' : 'text-slate-800'}">
                                        ${hasFile ? 'Ganti Berkas — Klik atau seret file baru' : 'Klik untuk pilih file atau seret & lepas ke sini'}
                                    </p>
                                    <p class="ps-dz-sub text-[10px] sm:text-[11px] text-slate-500 font-medium mt-0.5">
                                        PDF / DOC / DOCX / JPG / PNG — Maks. 5MB
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- CURRENT FILE (DI BAWAH DROPZONE) -->
                        ${hasFile ? `
                            <div class="ps-current-file flex items-center gap-2.5 p-3 rounded-xl bg-white border border-emerald-200 shadow-2xs">
                                <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-base shrink-0">
                                    <i class="bi bi-file-earmark-check-fill"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="text-[9px] font-extrabold text-emerald-700 uppercase tracking-wider block">Current File</span>
                                    <a href="${uploaded.file_url}" target="_blank"
                                       class="text-[11px] font-bold text-slate-800 hover:text-emerald-700 underline truncate block">
                                        ${uploaded.file_name}
                                    </a>
                                </div>
                                <span class="text-[10px] text-slate-400 font-semibold shrink-0 bg-slate-100 px-2 py-1 rounded-md">Terupload</span>
                            </div>
                        ` : ''}

                    </div>
                `;
            });

            body.innerHTML = html;

            // Attach event listener ke semua input file
            document.querySelectorAll('.ps-file-input').forEach(inp => {
                inp.addEventListener('change', function() {
                    const dz = this.closest('.ps-dropzone');
                    if (this.files && this.files[0]) {
                        psShowPickedFile(dz, this.files[0]);
                    }
                });
            });
        }

        // ============================================================
        // DRAG & DROP HELPERS
        // ============================================================
        function psDragOver(e, el) {
            e.preventDefault(); e.stopPropagation();
            el.classList.add('dragover');
        }
        function psDragLeave(e, el) {
            e.preventDefault(); e.stopPropagation();
            el.classList.remove('dragover');
        }
        function psDrop(e, el) {
            e.preventDefault(); e.stopPropagation();
            el.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files && files.length > 0) {
                const input = el.querySelector('.ps-file-input');
                // Set file via DataTransfer
                const dt = new DataTransfer();
                dt.items.add(files[0]);
                input.files = dt.files;
                psShowPickedFile(el, files[0]);
            }
        }
        function psShowPickedFile(dropzoneEl, file) {
            const title = dropzoneEl.querySelector('.ps-dz-title');
            const sub   = dropzoneEl.querySelector('.ps-dz-sub');
            const icon  = dropzoneEl.querySelector('.ps-dz-icon');
            if (!title || !sub || !icon) return;

            // Reset styling → tandai sebagai "siap upload"
            dropzoneEl.classList.remove('border-slate-300', 'bg-slate-50/50', 'border-emerald-300', 'bg-emerald-50/40');
            dropzoneEl.classList.add('border-orange-500', 'bg-orange-50');
            title.classList.remove('text-slate-800', 'text-emerald-700');
            title.classList.add('text-orange-800');
            icon.classList.remove('from-orange-500', 'to-amber-400', 'from-emerald-500', 'to-teal-400');
            icon.classList.add('from-orange-600', 'to-orange-500');
            icon.innerHTML = '<i class="bi bi-file-earmark-check-fill"></i>';

            const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
            title.textContent = '📎 ' + file.name;
            sub.textContent   = 'Siap diunggah — ' + sizeMB + ' MB';
            sub.classList.remove('text-slate-500');
            sub.classList.add('text-orange-700', 'font-bold');
        }

        function submitPersyaratanSidang() {
            const inputs = document.querySelectorAll('.ps-file-input');
            const formData = new FormData();
            let fileCount = 0;

            inputs.forEach(input => {
                if (input.files && input.files[0]) {
                    formData.append('berkas[' + input.dataset.kode + ']', input.files[0]);
                    fileCount++;
                }
            });

            if (fileCount === 0) {
                if (typeof showToast === 'function') showToast('Pilih minimal satu berkas untuk diunggah.', 'error');
                else alert('Pilih minimal satu berkas untuk diunggah.');
                return;
            }

            const btn = document.getElementById('psBtnSubmit');
            const orig = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin text-sm"></i> Mengunggah...';

            fetch('<?= site_url("mahasiswa/upload_berkas_sidang_ajax") ?>', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(res => {
                if (res.status) {
                    if (typeof showToast === 'function') showToast(res.message, 'success');
                    else alert(res.message);
                    setTimeout(() => loadMasterSyaratSidang(), 600);
                } else {
                    if (typeof showToast === 'function') showToast(res.message || 'Gagal mengunggah berkas.', 'error');
                    else alert(res.message || 'Gagal mengunggah berkas.');
                }
            })
            .catch(err => {
                console.error('[PS] upload error:', err);
                if (typeof showToast === 'function') showToast('Kesalahan koneksi ke server.', 'error');
                else alert('Kesalahan koneksi ke server.');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = orig;
            });
        }

        // Expose ke window agar aman
        window.openModalPersyaratanSidang  = openModalPersyaratanSidang;
        window.closeModalPersyaratanSidang = closeModalPersyaratanSidang;
        window.submitPersyaratanSidang     = submitPersyaratanSidang;
        window.psDragOver                  = psDragOver;
        window.psDragLeave                 = psDragLeave;
        window.psDrop                      = psDrop;
    </script>

    <?php $this->load->view('partials/custom_cursor'); ?>
</body>
</html>