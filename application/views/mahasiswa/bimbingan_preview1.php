<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Bimbingan & Evaluasi Preview TA — IFIK Portal'; ?></title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500;1,600&display=swap" rel="stylesheet">
    
    <!-- Global Styling -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>?v=<?= time(); ?>">
    <!-- TinyMCE -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
    <style>
        body, button, input, textarea, select {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }
        .orb-glow {
            position: absolute;
            border-radius: 50%;
            filter: blur(50px);
            pointer-events: none;
            z-index: 0;
        }

        /* Modal overlay */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(6px);
            z-index: 9998;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            animation: fadeIn 0.2s ease;
        }
        .modal-overlay.hidden {
            display: none !important;
        }
        .modal-content {
            background: white;
            border-radius: 1.5rem;
            max-width: 600px;
            width: 100%;
            max-height: 80vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.25);
            animation: slideUp 0.25s ease;
            overflow: hidden;
        }
        .modal-body-scroll {
            overflow-y: auto;
            flex: 1;
            padding: 1.25rem 1.5rem;
            scrollbar-width: thin;
            scrollbar-color: #fdba74 #fef3c7;
        }
        .modal-body-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .modal-body-scroll::-webkit-scrollbar-track {
            background: #fef3c7;
            border-radius: 3px;
        }
        .modal-body-scroll::-webkit-scrollbar-thumb {
            background: #fdba74;
            border-radius: 3px;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Active step highlight */
        .tab-card-active {
            border-color: #f97316 !important;
            box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.25), 0 10px 30px -5px rgba(249, 115, 22, 0.2) !important;
            background: linear-gradient(to bottom, #fff7ed, #ffffff) !important;
        }
        .tab-card-locked {
            opacity: 0.65;
            filter: grayscale(0.35);
            cursor: not-allowed !important;
        }
        .badge-active-step {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 10px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            background: #f97316;
            color: white;
            box-shadow: 0 2px 8px rgba(249, 115, 22, 0.4);
        }

        /* Drop zone for sidang files */
        .drop-zone-sidang {
            border: 2px dashed #d1d5db;
            background-color: #f9fafb;
            transition: all 0.2s;
            cursor: pointer;
        }
        .drop-zone-sidang:hover,
        .drop-zone-sidang.dragover {
            border-color: #10b981;
            background-color: #ecfdf5;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-amber-50/40 via-orange-50/25 to-slate-100 min-h-screen text-slate-800 antialiased flex flex-col justify-between selection:bg-orange-500 selection:text-white">

    <?php $this->load->view('partials/mahasiswa_navbar'); ?>

    <!-- Main Content -->
    <main class="w-full px-4 sm:px-6 lg:px-10 py-6 sm:py-8 flex-grow space-y-7">

        <!-- Flashdata Alert -->
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

        <!-- Hero Card & Supervisor Info -->
        <div class="card-3d-orange hover-card-elevate rounded-3xl p-7 sm:p-9 relative overflow-hidden transition-all duration-300 w-full shadow-2xl text-white">
            <div class="absolute inset-0 pointer-events-none z-0 overflow-hidden rounded-3xl">
                <img src="<?= base_url('assets/images/background.png'); ?>" alt="FIK Building Illustration" class="w-full h-full object-cover object-[85%_center] opacity-75 saturate-110 contrast-105">
                <div class="absolute inset-0 bg-gradient-to-r from-[#9a3412]/95 via-[#ea580c]/85 to-[#c2410c]/70"></div>
            </div>

            <div class="sph-3d w-28 h-28 -top-8 -right-8 bg-gradient-to-tr from-amber-300 to-orange-400 opacity-25 z-0"></div>
            <div class="sph-3d w-20 h-20 -bottom-6 left-1/3 bg-gradient-to-tr from-rose-300 to-amber-300 opacity-20 z-0" style="animation-duration: 8s;"></div>

            <div class="relative z-10 flex flex-col xl:flex-row items-start xl:items-center justify-between gap-8">
                <div class="space-y-4 max-w-3xl">
                    <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white text-xs font-bold badge-3d shadow-xs">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <i class="bi bi-mortarboard-fill text-amber-200 text-sm"></i> Hub Bimbingan &amp; Evaluasi Preview TA
                    </div>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-tight drop-shadow-sm">
                        Bimbingan Tugas Akhir
                    </h1>
                    
                    <p class="text-sm sm:text-base text-orange-100/95 font-normal leading-relaxed max-w-2xl">
                        Kelola seluruh proses bimbingan dan pengunggahan berkas checkpoint evaluasi (<strong class="text-white font-bold">Preview 1, Preview 2, dan Preview 3</strong>) hingga persiapan menuju Sidang Akhir Tugas Akhir.
                    </p>
                    
                    <!-- Judul TA Singkat -->
                    <?php if(!empty($pendaftaran['judul_1'])): ?>
                        <div class="pt-1">
                            <span class="text-xs font-bold uppercase tracking-wider text-amber-200 block mb-1.5">Judul Tugas Akhir Utama:</span>
                            <div class="p-4 sm:p-5 bg-black/25 backdrop-blur-md rounded-2xl border border-white/20 font-bold text-white text-sm sm:text-base shadow-inner flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-400 to-orange-500 text-white flex items-center justify-center text-lg shrink-0 box-3d shadow-sm">
                                    <i class="bi bi-bookmark-star-fill text-slate-900"></i>
                                </div>
                                <span class="leading-relaxed font-semibold"><?= htmlspecialchars($pendaftaran['judul_1']); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Right: Info Dosen Pembimbing & Penguji Card -->
                <div class="w-full xl:w-[460px] bg-black/25 backdrop-blur-xl rounded-3xl p-6 sm:p-7 border border-white/20 shadow-2xl space-y-4 shrink-0 text-white hover-card-elevate transition-all duration-300">
                    <div class="flex items-center justify-between border-b border-white/15 pb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-white/20 text-white flex items-center justify-center text-sm font-bold box-3d border border-white/20">
                                <i class="bi bi-people-fill text-amber-300"></i>
                            </div>
                            <span class="text-sm font-bold text-white uppercase tracking-wider">
                                Tim Pembimbing &amp; Penguji
                            </span>
                        </div>
                        <span class="text-xs font-bold px-3 py-1 rounded-full bg-emerald-500/25 text-emerald-300 border border-emerald-400/40 shadow-2xs backdrop-blur-md">
                            <i class="bi bi-patch-check-fill mr-1"></i> Aktif
                        </span>
                    </div>

                    <!-- Pembimbing 1 -->
                    <div class="flex items-start gap-3.5 p-3 rounded-2xl bg-white/10 hover:bg-white/15 transition-colors border border-white/10">
                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-amber-400 to-orange-500 text-slate-950 flex items-center justify-center font-extrabold text-base shrink-0 box-3d shadow-sm">
                            P1
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-amber-200 uppercase tracking-wider block">Pembimbing Utama (Penilai P1 &amp; P3)</span>
                            <h4 class="font-bold text-sm sm:text-base text-white truncate mt-0.5">
                                <?= !empty($pembimbing_1) ? htmlspecialchars($pembimbing_1) : '<span class="italic text-white/60 text-xs">Belum Di-assign</span>'; ?>
                            </h4>
                        </div>
                    </div>

                    <!-- Pembimbing 2 -->
                    <div class="flex items-start gap-3.5 p-3 rounded-2xl bg-white/5 hover:bg-white/10 transition-colors border border-white/10">
                        <div class="w-11 h-11 rounded-2xl bg-white/20 text-white flex items-center justify-center font-bold text-base shrink-0 box-3d border border-white/10">
                            P2
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-orange-200/80 uppercase tracking-wider block">Pembimbing Pendamping (Bimbingan Teknis)</span>
                            <h4 class="font-bold text-sm sm:text-base text-white/95 truncate mt-0.5">
                                <?= !empty($pembimbing_2) ? htmlspecialchars($pembimbing_2) : '<span class="italic text-white/50 text-xs">Belum Di-assign</span>'; ?>
                            </h4>
                        </div>
                    </div>

                    <!-- Dosen Penguji (Preview 2) -->
                    <div class="flex items-start gap-3.5 p-3 rounded-2xl bg-white/5 hover:bg-white/10 transition-colors border border-white/10">
                        <div class="w-11 h-11 rounded-2xl bg-purple-500/40 text-purple-200 border border-purple-400/30 flex items-center justify-center font-bold text-base shrink-0 box-3d">
                            U1
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-purple-200 uppercase tracking-wider block">Dosen Penguji (Penilai Preview 2)</span>
                            <h4 class="font-bold text-sm sm:text-base text-white/95 truncate mt-0.5">
                                <?= !empty($penguji_ta) ? htmlspecialchars($penguji_ta) : '<span class="italic text-white/50 text-xs">Belum Di-assign</span>'; ?>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Card Tracker -->
        <div id="statusCardContainer" class="card-3d-warm rounded-3xl p-6 sm:p-8 space-y-4 w-full shadow-lg border border-orange-100 mb-8">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2.5">
                <i class="bi bi-info-circle-fill text-orange-500"></i> Status Bimbingan Terkini
            </h3>
            <?php 
                $curr_status = 'Belum Memulai Bimbingan';
                $curr_color = 'bg-slate-50 border-slate-200 text-slate-700';
                $curr_icon = 'bi-dash-circle text-slate-400';
                $curr_catatan = 'Silakan mulai dengan mengunggah berkas Preview 1.';
                $curr_catatan2 = '';

                if ($latest_p3) {
                    if ($latest_p3['status_pembimbing'] == 'Approved') {
                        $curr_status = 'Preview 3 Disetujui (Siap Sidang)';
                        $curr_color = 'bg-emerald-50 border-emerald-200 text-emerald-800';
                        $curr_icon = 'bi-check-circle-fill text-emerald-500';
                        $curr_catatan = $latest_p3['catatan_pembimbing'];
                    } else if ($latest_p3['status_pembimbing'] == 'Revision') {
                        $curr_status = 'Preview 3 Revisi';
                        $curr_color = 'bg-rose-50 border-rose-200 text-rose-800';
                        $curr_icon = 'bi-x-circle-fill text-rose-500';
                        $curr_catatan = $latest_p3['catatan_pembimbing'];
                    } else {
                        $curr_status = 'Preview 3 Sedang Direview';
                        $curr_color = 'bg-amber-50 border-amber-200 text-amber-800';
                        $curr_icon = 'bi-clock-fill text-amber-500';
                        $curr_catatan = 'Menunggu review dari Pembimbing.';
                    }
                    $curr_catatan2 = $latest_p3['catatan_pembimbing_2'] ?? '';
                } else if ($latest_p2) {
                    if ($latest_p2['status_pembimbing'] == 'Approved') {
                        $curr_status = 'Preview 2 Disetujui (Lanjut Preview 3)';
                        $curr_color = 'bg-emerald-50 border-emerald-200 text-emerald-800';
                        $curr_icon = 'bi-check-circle-fill text-emerald-500';
                        $curr_catatan = $latest_p2['catatan_pembimbing'];
                    } else if ($latest_p2['status_pembimbing'] == 'Revision') {
                        $curr_status = 'Preview 2 Revisi';
                        $curr_color = 'bg-rose-50 border-rose-200 text-rose-800';
                        $curr_icon = 'bi-x-circle-fill text-rose-500';
                        $curr_catatan = $latest_p2['catatan_pembimbing'];
                    } else {
                        $curr_status = 'Preview 2 Sedang Direview';
                        $curr_color = 'bg-amber-50 border-amber-200 text-amber-800';
                        $curr_icon = 'bi-clock-fill text-amber-500';
                        $curr_catatan = 'Menunggu review dari Pembimbing.';
                    }
                    $curr_catatan2 = $latest_p2['catatan_pembimbing_2'] ?? '';
                } else if ($latest_p1) {
                    if ($latest_p1['status_pembimbing'] == 'Approved') {
                        $curr_status = 'Preview 1 Disetujui (Lanjut Preview 2)';
                        $curr_color = 'bg-emerald-50 border-emerald-200 text-emerald-800';
                        $curr_icon = 'bi-check-circle-fill text-emerald-500';
                        $curr_catatan = $latest_p1['catatan_pembimbing'];
                    } else if ($latest_p1['status_pembimbing'] == 'Revision') {
                        $curr_status = 'Preview 1 Revisi';
                        $curr_color = 'bg-rose-50 border-rose-200 text-rose-800';
                        $curr_icon = 'bi-x-circle-fill text-rose-500';
                        $curr_catatan = $latest_p1['catatan_pembimbing'];
                    } else {
                        $curr_status = 'Preview 1 Sedang Direview';
                        $curr_color = 'bg-amber-50 border-amber-200 text-amber-800';
                        $curr_icon = 'bi-clock-fill text-amber-500';
                        $curr_catatan = 'Menunggu review dari Pembimbing.';
                    }
                    $curr_catatan2 = $latest_p1['catatan_pembimbing_2'] ?? '';
                }
            ?>
            <div class="p-4 rounded-2xl border <?= $curr_color ?> flex items-start gap-4">
                <i class="bi <?= $curr_icon ?> text-2xl mt-1"></i>
                <div class="flex-1">
                    <h4 class="font-bold text-lg mb-1"><?= $curr_status ?></h4>
                    <?php if(!empty($curr_catatan)): ?>
                        <div class="text-sm mt-2 p-3 bg-white/50 rounded-lg border border-inherit">
                            <strong>Catatan Pembimbing 1:</strong><br>
                            <?= htmlspecialchars($curr_catatan) ?>
                        </div>
                    <?php endif; ?>
                    <?php if(!empty($curr_catatan2)): ?>
                        <div class="text-sm mt-2 p-3 bg-white/50 rounded-lg border border-inherit">
                            <strong>Catatan Pembimbing 2:</strong><br>
                            <?= htmlspecialchars($curr_catatan2) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Milestone Stepper Workflow / Tab Switcher -->
        <?php
            $is_p1_app = true; // Unlocked for direct access & testing
            $is_p2_app = true; // Unlocked for direct access & testing
            $is_p3_app = ($latest_p3 && $latest_p3['status_pembimbing'] === 'Approved');
            
            // Determine active step
            if (!$is_p1_app) {
                $active_step = 'preview1';
            } elseif (!$is_p2_app) {
                $active_step = 'preview2';
            } elseif (!$is_p3_app) {
                $active_step = 'preview3';
            } else {
                $active_step = 'sidang';
            }
        ?>

        <div class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-7 w-full shadow-lg shadow-orange-500/5">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-orange-100 pb-5">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-orange-600 block mb-1">PILIH TAHAPAN EVALUASI</span>
                    <h3 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                        <i class="bi bi-diagram-3-fill text-orange-500 text-xl"></i> Milestone Bimbingan &amp; Evaluasi TA
                    </h3>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button type="button" onclick="openRekomendasiModal('<?= $mahasiswa['nim'] ?? '1301210001' ?>', '<?= $latest_p3['id'] ?? '' ?>')" class="px-4 py-2 rounded-2xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-extrabold text-xs shadow-md transition flex items-center gap-2 cursor-pointer transform hover:scale-105 active:scale-95">
                        <i class="bi bi-check2-square text-sm"></i> Rekomendasi Sidang/Non Sidang
                    </button>
                    <span class="text-xs font-bold px-4 py-2 rounded-2xl bg-slate-100 text-slate-600 border border-slate-200 shadow-2xs">
                        <i class="bi bi-cursor-fill text-orange-500 mr-1.5"></i> Klik kartu tahapan untuk berganti tab
                    </span>
                </div>
            </div>

            <!-- 4 Milestone Tab Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                
                <!-- Tab 1: Preview 1 -->
                <div onclick="switchPreviewTab('preview1')" id="tabBtnPreview1" class="tab-card p-6 rounded-3xl border-2 transition-all duration-300 relative overflow-hidden hover-card-elevate cursor-pointer <?= $active_step === 'preview1' ? 'tab-card-active' : ($is_p1_app ? 'tab-card-locked border-slate-200 bg-slate-50/80' : 'tab-card-locked border-slate-200 bg-slate-50/80') ?>">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold tracking-wider uppercase <?= $active_step === 'preview1' ? 'text-orange-700' : 'text-slate-400' ?>">Tahap 01</span>
                        <div class="flex items-center gap-2">
                            <?php if ($active_step === 'preview1'): ?>
                                <span class="badge-active-step"><i class="bi bi-arrow-right-circle-fill"></i> Saat Ini</span>
                            <?php endif; ?>
                            <div class="w-10 h-10 rounded-2xl <?= $is_p1_app ? 'bg-emerald-500 shadow-md shadow-emerald-500/40' : 'bg-orange-500 shadow-md shadow-orange-500/40'; ?> text-white flex items-center justify-center font-bold text-lg box-3d">
                                <i class="bi <?= $is_p1_app ? 'bi-check-lg' : 'bi-file-earmark-text'; ?>"></i>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-bold text-base sm:text-lg <?= $active_step === 'preview1' ? 'text-slate-900' : 'text-slate-500' ?>">Preview 1</h4>
                        <p class="text-xs text-slate-500 font-medium mt-1 leading-snug">Proposal &amp; Bab 1–3 (Pembimbing 1)</p>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-bold <?= $is_p1_app ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : ($upload_count_p1 > 0 ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-orange-100 text-orange-800 border border-orange-300'); ?>">
                            <span class="w-2 h-2 rounded-full <?= $is_p1_app ? 'bg-emerald-500' : 'bg-orange-500'; ?>"></span>
                            <?= $is_p1_app ? 'Selesai (Terkunci)' : ($upload_count_p1 > 0 ? 'Menunggu Review' : 'Aktif / Sedang Berlangsung'); ?>
                        </span>
                    </div>
                </div>

                <!-- Tab 2: Preview 2 -->
                <div onclick="switchPreviewTab('preview2')" id="tabBtnPreview2" class="tab-card p-6 rounded-3xl border-2 transition-all duration-300 relative overflow-hidden hover-card-elevate cursor-pointer <?= $active_step === 'preview2' ? 'tab-card-active border-amber-300' : ($is_p2_app ? 'tab-card-locked border-slate-200 bg-slate-50/80' : (!$is_p1_app ? 'tab-card-locked border-slate-200 bg-slate-50/80' : 'border-amber-300 bg-amber-50/50')) ?>">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold tracking-wider uppercase <?= $active_step === 'preview2' ? 'text-amber-700' : 'text-slate-400' ?>">Tahap 02</span>
                        <div class="flex items-center gap-2">
                            <?php if ($active_step === 'preview2'): ?>
                                <span class="badge-active-step" style="background:#d97706;"><i class="bi bi-arrow-right-circle-fill"></i> Saat Ini</span>
                            <?php endif; ?>
                            <div class="w-10 h-10 rounded-2xl <?= $is_p2_app ? 'bg-emerald-500 text-white' : ($is_p1_app ? 'bg-amber-500 text-white' : 'bg-slate-200 text-slate-500'); ?> flex items-center justify-center font-bold text-lg box-3d">
                                <i class="bi <?= $is_p2_app ? 'bi-check-lg' : ($is_p1_app ? 'bi-hammer' : 'bi-lock-fill text-sm'); ?>"></i>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-bold text-base sm:text-lg <?= $active_step === 'preview2' ? 'text-slate-900' : 'text-slate-500' ?>">Preview 2</h4>
                        <p class="text-xs text-slate-500 font-medium mt-1 leading-snug">Progress Karya 50% (Dosen Penguji)</p>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-bold <?= $is_p2_app ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : ($is_p1_app ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-slate-100 text-slate-500 border border-slate-200'); ?>">
                            <i class="bi <?= $is_p2_app ? 'bi-check-circle-fill' : ($is_p1_app ? 'bi-unlock' : 'bi-lock-fill'); ?>"></i>
                            <?= $is_p2_app ? 'Selesai (Terkunci)' : ($is_p1_app ? 'Terbuka / Siap Upload' : 'Terkunci (Syarat P1)'); ?>
                        </span>
                    </div>
                </div>

                <!-- Tab 3: Preview 3 -->
                <div onclick="switchPreviewTab('preview3')" id="tabBtnPreview3" class="tab-card p-6 rounded-3xl border-2 transition-all duration-300 relative overflow-hidden hover-card-elevate cursor-pointer <?= $active_step === 'preview3' ? 'tab-card-active border-indigo-300' : ($is_p3_app ? 'tab-card-locked border-slate-200 bg-slate-50/80' : (!$is_p2_app ? 'tab-card-locked border-slate-200 bg-slate-50/80' : 'border-indigo-300 bg-indigo-50/50')) ?>">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold tracking-wider uppercase <?= $active_step === 'preview3' ? 'text-indigo-700' : 'text-slate-400' ?>">Tahap 03</span>
                        <div class="flex items-center gap-2">
                            <?php if ($active_step === 'preview3'): ?>
                                <span class="badge-active-step" style="background:#6366f1;"><i class="bi bi-arrow-right-circle-fill"></i> Saat Ini</span>
                            <?php endif; ?>
                            <div class="w-10 h-10 rounded-2xl <?= $is_p3_app ? 'bg-emerald-500 text-white' : ($is_p2_app ? 'bg-indigo-500 text-white' : 'bg-slate-200 text-slate-500'); ?> flex items-center justify-center font-bold text-lg box-3d">
                                <i class="bi <?= $is_p3_app ? 'bi-check-lg' : ($is_p2_app ? 'bi-journal-check' : 'bi-lock-fill text-sm'); ?>"></i>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-bold text-base sm:text-lg <?= $active_step === 'preview3' ? 'text-slate-900' : 'text-slate-500' ?>">Preview 3</h4>
                        <p class="text-xs text-slate-500 font-medium mt-1 leading-snug">Pra-Sidang Naskah 100% (Pembimbing 1)</p>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-bold <?= $is_p3_app ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : ($is_p2_app ? 'bg-indigo-100 text-indigo-800 border border-indigo-200' : 'bg-slate-100 text-slate-500 border border-slate-200'); ?>">
                            <i class="bi <?= $is_p3_app ? 'bi-check-circle-fill' : ($is_p2_app ? 'bi-unlock' : 'bi-lock-fill'); ?>"></i>
                            <?= $is_p3_app ? 'Selesai (Terkunci)' : ($is_p2_app ? 'Terbuka' : 'Terkunci (Syarat P2)'); ?>
                        </span>
                    </div>
                </div>

                <!-- Tab 4: Sidang Akhir -->
                <div onclick="switchPreviewTab('sidang')" id="tabBtnSidang" class="tab-card p-6 rounded-3xl border-2 transition-all duration-300 relative overflow-hidden hover-card-elevate cursor-pointer <?= $active_step === 'sidang' ? 'tab-card-active border-emerald-400' : ($is_p3_app ? 'border-emerald-400 bg-emerald-50/50' : 'tab-card-locked border-slate-200 bg-slate-50/80') ?>">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold tracking-wider uppercase <?= $active_step === 'sidang' ? 'text-emerald-700' : 'text-slate-400' ?>">Tahap 04</span>
                        <div class="flex items-center gap-2">
                            <?php if ($active_step === 'sidang'): ?>
                                <span class="badge-active-step" style="background:#059669;"><i class="bi bi-arrow-right-circle-fill"></i> Saat Ini</span>
                            <?php endif; ?>
                            <div class="w-10 h-10 rounded-2xl <?= $is_p3_app ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-500'; ?> flex items-center justify-center font-bold text-lg box-3d">
                                <i class="bi <?= $is_p3_app ? 'bi-mortarboard-fill' : 'bi-lock-fill text-sm'; ?>"></i>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-bold text-base sm:text-lg <?= $active_step === 'sidang' ? 'text-slate-900' : 'text-slate-500' ?>">Sidang Tugas Akhir</h4>
                        <p class="text-xs text-slate-500 font-medium mt-1 leading-snug">Pendaftaran &amp; Penjadwalan Sidang</p>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-bold <?= $is_p3_app ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-slate-100 text-slate-500 border border-slate-200'; ?>">
                            <i class="bi <?= $is_p3_app ? 'bi-check-circle-fill' : 'bi-lock-fill'; ?>"></i>
                            <?= $is_p3_app ? 'Siap Daftar Sidang' : 'Terkunci (Syarat P3)'; ?>
                        </span>
                    </div>
                </div>

            </div>
        </div>

        <!-- ================= TAB CONTENT PANEL: PREVIEW 1 ================= -->
        <div id="panelPreview1" class="tab-panel space-y-7 <?= $active_step === 'preview1' ? '' : 'hidden' ?>">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-7 w-full">
                <!-- Kolom Kiri: Form Upload Berkas Preview 1 (7 Kolom) -->
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
                        <?php else: ?>
                        <!-- Form Upload -->
                        <?= form_open_multipart('mahasiswa/upload_preview', ['id' => 'formUploadPreview1', 'class' => 'space-y-6']); ?>
                            <input type="hidden" name="tahap_preview" value="Preview 1">
                            
                            <!-- Drag & Drop Zone -->
                            <div>
                                <label class="block text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider mb-2.5">
                                    Berkas Draft Laporan / Proposal TA <span class="text-rose-500">*</span>
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

                            <!-- Catatan Progres Mahasiswa -->
                            <div>
                                <label for="catatanP1" class="block text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider mb-2.5">
                                    Catatan Progres Bimbingan <span class="text-slate-400 font-normal normal-case">(Opsional)</span>
                                </label>
                                <textarea name="catatan_mahasiswa" id="catatanP1" rows="3" class="w-full p-4 text-sm rounded-2xl border border-slate-200 focus:ring-4 focus:ring-orange-400/20 focus:border-orange-500 outline-none resize-none text-slate-900 placeholder:text-slate-400 transition bg-white font-medium" placeholder="Contoh: Mengunggah draft revisi Bab 1 s/d Bab 3 sesuai arahan Pembimbing 1..."></textarea>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-2">
                                <button type="submit" class="w-full py-4 px-8 rounded-2xl bg-gradient-to-r from-orange-600 via-orange-500 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white font-bold text-sm sm:text-base shadow-lg shadow-orange-500/25 transition flex items-center justify-center gap-3 cursor-pointer hover:scale-[1.01] active:scale-[0.99] box-3d">
                                    <i class="bi bi-cloud-arrow-up-fill text-lg"></i>
                                    <span>Kirim Berkas Draft Preview 1</span>
                                </button>
                            </div>
                        <?= form_close(); ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Kolom Kanan: Gatekeeper Validasi Syarat Lanjut ke Preview 2 (5 Kolom) -->
                <div class="lg:col-span-5 space-y-6">
                    <div class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-6 shadow-md shadow-orange-500/5">
                        <div class="border-b border-orange-100 pb-4">
                            <span class="text-xs font-bold uppercase tracking-wider text-orange-600 block mb-1">GATEKEEPER MILESTONE</span>
                            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2.5">
                                <i class="bi bi-shield-lock-fill text-orange-500 text-xl"></i> Syarat Melangkah ke Preview 2
                            </h3>
                        </div>

                        <!-- Checklist Syarat -->
                        <div class="space-y-4">
                            <!-- Syarat 1: Minimal 1x Upload -->
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

                            <!-- Syarat 2: ACC Pembimbing 1 -->
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

                        <!-- Tombol Aksi Preview 2 -->
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
                    <table class="w-full text-left border-collapse text-xs sm:text-sm">
                        <thead class="bg-slate-50 text-slate-700 font-bold uppercase py-3.5 border-b border-slate-200">
                            <tr>
                                <th class="py-4 px-6 w-14 text-center">#</th>
                                <th class="py-4 px-6">File Draft</th>
                                <th class="py-4 px-6">Catatan Anda</th>
                                <th class="py-4 px-6">Waktu Upload</th>
                                <th class="py-4 px-6 text-center">Status</th>
                                <th class="py-4 px-6 text-center">Catatan Pembimbing</th>
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
                <!-- Form Upload Preview 2 -->
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
                        <?php else: ?>
                        <?= form_open_multipart('mahasiswa/upload_preview', ['id' => 'formUploadPreview2', 'class' => 'space-y-6']); ?>
                            <input type="hidden" name="tahap_preview" value="Preview 2">
                            
                            <div>
                                <label class="block text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider mb-2.5">
                                    Berkas Progress Bab 4 &amp; Link Prototype / Demo <span class="text-rose-500">*</span>
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
                                    Catatan Progress Karya / Link Demo Video &amp; Figma
                                </label>
                                <textarea name="catatan_mahasiswa" rows="3" class="w-full p-4 text-sm rounded-2xl border border-slate-200 focus:ring-4 focus:ring-amber-400/20 focus:border-amber-500 outline-none resize-none text-slate-900 placeholder:text-slate-400 transition bg-white font-medium" placeholder="Tuliskan tautan prototype Figma, link repositori GitHub, atau ringkasan progres Bab 4..."></textarea>
                            </div>

                            <div class="pt-2">
                                <button type="submit" class="w-full py-4 px-8 rounded-2xl bg-gradient-to-r from-amber-600 via-amber-500 to-orange-500 hover:from-amber-700 hover:to-orange-600 text-white font-bold text-sm sm:text-base shadow-lg shadow-amber-500/25 transition flex items-center justify-center gap-3 cursor-pointer box-3d">
                                    <i class="bi bi-cloud-arrow-up-fill text-lg"></i>
                                    <span>Kirim Berkas Preview 2</span>
                                </button>
                            </div>
                        <?= form_close(); ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Info Dosen Penguji -->
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
                </div>
            </div>

            <!-- Log Preview 2 -->
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
                    <table class="w-full text-left border-collapse text-xs sm:text-sm">
                        <thead class="bg-slate-50 text-slate-700 font-bold uppercase py-3.5 border-b border-slate-200">
                            <tr>
                                <th class="py-4 px-6 w-14 text-center">#</th>
                                <th class="py-4 px-6">File Draft</th>
                                <th class="py-4 px-6">Catatan Anda</th>
                                <th class="py-4 px-6">Waktu Upload</th>
                                <th class="py-4 px-6 text-center">Status</th>
                                <th class="py-4 px-6 text-center">Catatan Penguji</th>
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
                            <i class="bi bi-journal-check text-indigo-600 text-xl"></i> Upload Naskah Lengkap TA (Bab 1 – 5) &amp; Karya Final
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 font-normal mt-1.5">
                            Persetujuan Preview 3 oleh Pembimbing 1 akan membuka akses pendaftaran Sidang Tugas Akhir Anda.
                        </p>
                    </div>
                    <button type="button" onclick="openRekomendasiModal('<?= $mahasiswa['nim'] ?? '1301210001' ?>', '<?= $latest_p3['id'] ?? '' ?>')" class="px-5 py-3 rounded-2xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-extrabold text-xs sm:text-sm shadow-lg shadow-orange-500/20 transition flex items-center gap-2 cursor-pointer transform hover:scale-105 active:scale-95">
                        <i class="bi bi-check2-square text-lg"></i> Rekomendasi Sidang/Non Sidang
                    </button>
                </div>

                <?php if(!$is_p2_app): ?>
                <div class="py-10 text-center bg-slate-50 border border-slate-200 rounded-3xl max-w-3xl">
                    <i class="bi bi-lock-fill text-4xl text-slate-400 mb-3 block"></i>
                    <h4 class="font-bold text-lg text-slate-700">Tahap Terkunci</h4>
                    <p class="text-slate-500 text-sm mt-2">Anda harus mendapatkan persetujuan (ACC) dari Penguji di tahap Preview 2 sebelum dapat mengunggah berkas Preview 3.</p>
                </div>
                <?php elseif($is_p3_app): ?>
                <div class="py-10 text-center bg-emerald-50 border border-emerald-200 rounded-3xl max-w-3xl">
                    <i class="bi bi-lock-fill text-4xl text-emerald-500 mb-3 block"></i>
                    <h4 class="font-bold text-lg text-emerald-800">Tahap Terkunci (Selesai)</h4>
                    <p class="text-emerald-700 text-sm mt-2">Tahap ini telah disetujui. Anda tidak dapat mengunggah ulang berkas. Anda sudah siap untuk mendaftar Sidang Akhir.</p>
                </div>
                <?php else: ?>
                <?= form_open_multipart('mahasiswa/upload_preview', ['id' => 'formUploadPreview3', 'class' => 'space-y-6 max-w-3xl']); ?>
                    <input type="hidden" name="tahap_preview" value="Preview 3">
                    <div>
                        <label class="block text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider mb-2.5">
                            Dokumen Lengkap Pra-Sidang (PDF / ZIP) <span class="text-rose-500">*</span>
                        </label>
                        
                        <div class="drop-zone relative border-2 border-dashed border-indigo-300 hover:border-indigo-500 bg-indigo-50/30 hover:bg-indigo-50/60 rounded-3xl p-8 sm:p-10 text-center transition-all cursor-pointer group" id="dropZoneP3">
                            <input type="file" name="file_draft" id="fileDraftP3" accept=".pdf,.docx,.zip" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="space-y-3.5 pointer-events-none">
                                <div class="w-16 h-16 sm:w-18 sm:h-18 rounded-3xl bg-gradient-to-tr from-indigo-500 to-purple-400 text-white group-hover:scale-105 flex items-center justify-center text-3xl mx-auto transition-transform box-3d shadow-md shadow-indigo-500/20">
                                    <i class="bi bi-file-earmark-text-fill"></i>
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
                        
                        <div id="fileBadgeP3" class="hidden mt-3 p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs sm:text-sm font-bold flex items-center justify-between shadow-xs">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-sm shrink-0 box-3d">
                                    <i class="bi bi-file-earmark-check-fill"></i>
                                </div>
                                <span id="fileNameP3" class="truncate font-mono">draft.pdf</span>
                            </div>
                            <span id="fileSizeP3" class="text-xs text-emerald-800 font-bold shrink-0 ml-3 bg-white px-3 py-1 rounded-xl border border-emerald-200">2.4 MB</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider mb-2.5">Catatan Kelayakan Pra-Sidang</label>
                        <textarea name="catatan_mahasiswa" rows="3" class="w-full p-4 border border-slate-200 rounded-2xl text-xs sm:text-sm font-medium" placeholder="Uraikan kelengkapan naskah dan karya yang siap disidangkan..."></textarea>
                    </div>
                    <button type="submit" class="py-4 px-8 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs sm:text-sm shadow-md transition cursor-pointer">
                        <i class="bi bi-send-check-fill mr-2"></i> Submit Berkas Pra-Sidang (Preview 3)
                    </button>
                <?= form_close(); ?>
                <?php endif; ?>
            </div>

            <!-- Log Preview 3 -->
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
                    <table class="w-full text-left border-collapse text-xs sm:text-sm">
                        <thead class="bg-slate-50 text-slate-700 font-bold uppercase py-3.5 border-b border-slate-200">
                            <tr>
                                <th class="py-4 px-6 w-14 text-center">#</th>
                                <th class="py-4 px-6">File Draft</th>
                                <th class="py-4 px-6">Catatan Anda</th>
                                <th class="py-4 px-6">Waktu Upload</th>
                                <th class="py-4 px-6 text-center">Status</th>
                                <th class="py-4 px-6 text-center">Catatan Pembimbing</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium" id="logTablePreview3">
                            <tr><td colspan="6" class="text-center py-8 text-slate-500"><i class="bi bi-arrow-repeat animate-spin mr-2 text-lg"></i> Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ================= TAB CONTENT PANEL: SIDANG AKHIR ================= -->
        <div id="panelSidang" class="tab-panel hidden space-y-7">
            <!-- Form Upload 4 Berkas Sidang -->
            <div class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-6 w-full shadow-md shadow-emerald-500/10">
                <div class="border-b border-emerald-100 pb-5">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 block mb-1">FORMULIR PENDAFTARAN SIDANG</span>
                    <h3 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                        <i class="bi bi-mortarboard-fill text-emerald-600 text-xl"></i> Upload Persyaratan Berkas Sidang Akhir
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 font-normal mt-1.5">
                        Unggah keempat berkas persyaratan sidang di bawah ini. Format yang diterima: <strong class="text-slate-900 font-semibold">PDF, DOC, DOCX</strong> (Maks. 10MB per file).
                    </p>
                </div>

                <?php
                // Tentukan kondisi untuk menampilkan form atau lock
                $sidang_locked = false;
                $lock_message = '';
                if (!$is_p3_app) {
                    $sidang_locked = true;
                    $lock_message = 'Anda harus menyelesaikan Preview 3 dan mendapatkan persetujuan Pembimbing 1 terlebih dahulu sebelum dapat mengunggah berkas sidang.';
                } elseif (empty($penguji_ta)) {
                    $sidang_locked = true;
                    $lock_message = 'Dosen Penguji belum di-assign. Silakan hubungi Koordinator TA untuk menetapkan Dosen Penguji sebelum Anda dapat mengunggah berkas sidang.';
                }
                ?>

                <?php if ($sidang_locked): ?>
                <div class="py-10 text-center bg-slate-50 border border-slate-200 rounded-3xl">
                    <i class="bi bi-lock-fill text-4xl text-slate-400 mb-3 block"></i>
                    <h4 class="font-bold text-lg text-slate-700">Tahap Terkunci</h4>
                    <p class="text-slate-500 text-sm mt-2"><?= $lock_message ?></p>
                </div>
                <?php else: ?>
                <?= form_open_multipart('mahasiswa/upload_sidang', ['id' => 'formUploadSidang', 'class' => 'space-y-6']); ?>

                <!-- File 1: File Bimbingan -->
                <div class="p-5 rounded-2xl border border-slate-200 bg-white/60 space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg shrink-0 box-3d">
                            <i class="bi bi-file-earmark-check-fill"></i>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-900">File Bimbingan <span class="text-rose-500">*</span></label>
                            <span class="text-xs text-slate-500 font-medium">Dokumen catatan bimbingan lengkap dari Pembimbing 1 &amp; 2</span>
                        </div>
                    </div>
                    <div class="drop-zone-sidang relative border-2 border-dashed rounded-2xl p-4 text-center transition-all cursor-pointer">
                        <input type="file" name="file_bimbingan" id="fileBimbingan" accept=".pdf,.doc,.docx" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="pointer-events-none">
                            <i class="bi bi-cloud-arrow-up text-2xl text-slate-400"></i>
                            <p class="text-sm text-slate-500 mt-1">Klik untuk pilih file atau seret ke sini</p>
                        </div>
                    </div>
                    <div id="fileBadgeBimbingan" class="hidden p-3 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs font-bold flex items-center justify-between">
                        <span id="fileNameBimbingan" class="truncate font-mono">file.pdf</span>
                        <span id="fileSizeBimbingan" class="text-emerald-700">0 MB</span>
                    </div>
                </div>

                <!-- File 2: File Sitasi -->
                <div class="p-5 rounded-2xl border border-slate-200 bg-white/60 space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg shrink-0 box-3d">
                            <i class="bi bi-quote"></i>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-900">File Sitasi <span class="text-rose-500">*</span></label>
                            <span class="text-xs text-slate-500 font-medium">Dokumen daftar sitasi / referensi (format APA / IEEE)</span>
                        </div>
                    </div>
                    <div class="drop-zone-sidang relative border-2 border-dashed rounded-2xl p-4 text-center transition-all cursor-pointer">
                        <input type="file" name="file_sitasi" id="fileSitasi" accept=".pdf,.doc,.docx" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="pointer-events-none">
                            <i class="bi bi-cloud-arrow-up text-2xl text-slate-400"></i>
                            <p class="text-sm text-slate-500 mt-1">Klik untuk pilih file atau seret ke sini</p>
                        </div>
                    </div>
                    <div id="fileBadgeSitasi" class="hidden p-3 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs font-bold flex items-center justify-between">
                        <span id="fileNameSitasi" class="truncate font-mono">file.pdf</span>
                        <span id="fileSizeSitasi" class="text-emerald-700">0 MB</span>
                    </div>
                </div>

                <!-- File 3: Persyaratan Jalur Tugas Akhir -->
                <div class="p-5 rounded-2xl border border-slate-200 bg-white/60 space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg shrink-0 box-3d">
                            <i class="bi bi-signpost-split-fill"></i>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-900">Persyaratan Jalur Tugas Akhir <span class="text-rose-500">*</span></label>
                            <span class="text-xs text-slate-500 font-medium">Dokumen persyaratan sesuai jalur TA yang ditempuh (Skripsi / Proyek / Jurnal)</span>
                        </div>
                    </div>
                    <div class="drop-zone-sidang relative border-2 border-dashed rounded-2xl p-4 text-center transition-all cursor-pointer">
                        <input type="file" name="file_persyaratan" id="filePersyaratan" accept=".pdf,.doc,.docx" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="pointer-events-none">
                            <i class="bi bi-cloud-arrow-up text-2xl text-slate-400"></i>
                            <p class="text-sm text-slate-500 mt-1">Klik untuk pilih file atau seret ke sini</p>
                        </div>
                    </div>
                    <div id="fileBadgePersyaratan" class="hidden p-3 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs font-bold flex items-center justify-between">
                        <span id="fileNamePersyaratan" class="truncate font-mono">file.pdf</span>
                        <span id="fileSizePersyaratan" class="text-emerald-700">0 MB</span>
                    </div>
                </div>

                <!-- File 4: File Pendukung Lainnya -->
                <div class="p-5 rounded-2xl border border-slate-200 bg-white/60 space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg shrink-0 box-3d">
                            <i class="bi bi-paperclip"></i>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-900">File Pendukung Lainnya <span class="text-rose-500">*</span></label>
                            <span class="text-xs text-slate-500 font-medium">Dokumen pendukung lain seperti surat pernyataan orisinalitas atau sertifikat</span>
                        </div>
                    </div>
                    <div class="drop-zone-sidang relative border-2 border-dashed rounded-2xl p-4 text-center transition-all cursor-pointer">
                        <input type="file" name="file_pendukung" id="filePendukung" accept=".pdf,.doc,.docx" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="pointer-events-none">
                            <i class="bi bi-cloud-arrow-up text-2xl text-slate-400"></i>
                            <p class="text-sm text-slate-500 mt-1">Klik untuk pilih file atau seret ke sini</p>
                        </div>
                    </div>
                    <div id="fileBadgePendukung" class="hidden p-3 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs font-bold flex items-center justify-between">
                        <span id="fileNamePendukung" class="truncate font-mono">file.pdf</span>
                        <span id="fileSizePendukung" class="text-emerald-700">0 MB</span>
                    </div>
                </div>

                <!-- Catatan -->
                <div>
                    <label class="block text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider mb-2.5">Catatan Pendaftaran Sidang</label>
                    <textarea name="catatan_sidang" rows="3" class="w-full p-4 text-sm rounded-2xl border border-slate-200 focus:ring-4 focus:ring-emerald-400/20 focus:border-emerald-500 outline-none resize-none text-slate-900 placeholder:text-slate-400 transition bg-white font-medium" placeholder="Tambahkan catatan penting terkait pendaftaran sidang Anda (opsional)..."></textarea>
                </div>

                <!-- Submit -->
                <div class="pt-2">
                    <button type="submit" class="w-full py-4 px-8 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-sm sm:text-base shadow-lg shadow-emerald-500/25 transition flex items-center justify-center gap-3 cursor-pointer hover:scale-[1.01] active:scale-[0.99] box-3d">
                        <i class="bi bi-cloud-arrow-up-fill text-lg"></i>
                        <span>Submit Berkas Sidang Akhir</span>
                    </button>
                </div>
                <?= form_close(); ?>
                <?php endif; ?>
            </div>

            <!-- Log Aktivitas Sidang -->
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
                    <table class="w-full text-left border-collapse text-xs sm:text-sm">
                        <thead class="bg-slate-50 text-slate-700 font-bold uppercase py-3.5 border-b border-slate-200">
                            <tr>
                                <th class="py-4 px-4 w-12 text-center">#</th>
                                <th class="py-4 px-4">File Bimbingan</th>
                                <th class="py-4 px-4">File Sitasi</th>
                                <th class="py-4 px-4">Persyaratan</th>
                                <th class="py-4 px-4">Pendukung</th>
                                <th class="py-4 px-4">Waktu Upload</th>
                                <th class="py-4 px-4 text-center">Status</th>
                                <th class="py-4 px-4 text-center">Catatan Pembimbing</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium" id="logTableSidang">
                            <tr><td colspan="8" class="text-center py-8 text-slate-500"><i class="bi bi-arrow-repeat animate-spin mr-2 text-lg"></i> Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white/90 border-t border-orange-100 py-6 text-center text-xs sm:text-sm text-slate-500 font-medium">
        &copy; <?= date('Y'); ?> IFIK Portal — Fakultas Industri Kreatif, Telkom University
    </footer>

    <!-- Modal Popup Catatan Pembimbing -->
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

    <script src="<?= base_url('assets/js/navbar_animated.js'); ?>?v=<?= time(); ?>"></script>
    <script>
        // ===================== MODAL CATATAN =====================
        function openCatatanModal(catatanText, label = 'Catatan Pembimbing') {
            const modal = document.getElementById('catatanModal');
            const body = document.getElementById('catatanModalBody');
            if (!modal || !body) return;

            if (!catatanText || catatanText.trim() === '') {
                body.innerHTML = '<p class="text-slate-400 italic text-center py-8">Belum ada catatan dari pembimbing.</p>';
            } else {
                body.innerHTML = `
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-orange-600 uppercase tracking-wider">
                            <i class="bi bi-person-badge-fill"></i> ${label}
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-sm text-slate-800 leading-relaxed font-medium whitespace-pre-wrap">
                            ${catatanText.replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>')}
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
            if (e.key === 'Escape') closeCatatanModal();
        });

        // ===================== TOAST =====================
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-5 right-5 z-[9999] p-4 rounded-xl text-white font-bold shadow-lg transition-opacity ${type === 'success' ? 'bg-emerald-500' : 'bg-rose-500'}`;
            toast.innerHTML = `<i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'} mr-2"></i> ${message}`;
            document.body.appendChild(toast);
            setTimeout(() => { toast.style.opacity = '0'; setTimeout(()=>toast.remove(), 300); }, 3000);
        }

        // ===================== FILE UPLOADER SETUP (PREVIEW 1-3) =====================
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
        setupFileUploader(3);

        // ===================== FILE UPLOADER SETUP (SIDANG) =====================
        function setupSidangFileUploader(fileInputId, badgeId, nameId, sizeId, dropZoneSelector) {
            const fileInput = document.getElementById(fileInputId);
            const fileBadge = document.getElementById(badgeId);
            const fileName = document.getElementById(nameId);
            const fileSize = document.getElementById(sizeId);
            const dropZone = document.querySelector(dropZoneSelector);

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
        setupSidangFileUploader('fileBimbingan', 'fileBadgeBimbingan', 'fileNameBimbingan', 'fileSizeBimbingan', '.drop-zone-sidang:has(#fileBimbingan)');
        setupSidangFileUploader('fileSitasi', 'fileBadgeSitasi', 'fileNameSitasi', 'fileSizeSitasi', '.drop-zone-sidang:has(#fileSitasi)');
        setupSidangFileUploader('filePersyaratan', 'fileBadgePersyaratan', 'fileNamePersyaratan', 'fileSizePersyaratan', '.drop-zone-sidang:has(#filePersyaratan)');
        setupSidangFileUploader('filePendukung', 'fileBadgePendukung', 'fileNamePendukung', 'fileSizePendukung', '.drop-zone-sidang:has(#filePendukung)');

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

        // ===================== AJAX FORM SUBMIT (PREVIEW 1-3) =====================
        ['formUploadPreview1', 'formUploadPreview2', 'formUploadPreview3'].forEach((formId, idx) => {
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
                            const fileBadge = document.getElementById('fileBadgeP' + (idx+1));
                            if(fileBadge) fileBadge.classList.add('hidden');
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

        // ===================== AJAX FORM SUBMIT SIDANG =====================
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
                        // Sembunyikan semua badge file sidang
                        ['Bimbingan', 'Sitasi', 'Persyaratan', 'Pendukung'].forEach(prefix => {
                            const badge = document.getElementById('fileBadge' + prefix);
                            if(badge) badge.classList.add('hidden');
                        });
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
                el.classList.remove('tab-card-active', 'border-orange-500', 'border-amber-300', 'border-indigo-300', 'border-emerald-400', 'ring-4', 'ring-orange-400/20', 'ring-amber-400/20', 'ring-indigo-400/20', 'ring-emerald-400/20');
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
            } else if (targetTab === 'sidang') {
                document.getElementById('panelSidang').classList.remove('hidden');
                document.getElementById('tabBtnSidang').classList.add('tab-card-active', 'border-emerald-400', 'ring-4', 'ring-emerald-400/20');
            }
        }

        // ===================== RENDER LOG TABLE =====================
        function renderLogTable(data, tbodyId, isSidang = false) {
            const tbody = document.getElementById(tbodyId);
            if(!tbody) return;
            if(!data || data.length === 0) {
                const colSpan = isSidang ? 8 : 6;
                tbody.innerHTML = `<tr><td colspan="${colSpan}" class="text-center py-12 px-4 text-slate-500 font-medium">Belum ada dokumen yang diunggah untuk tahap ini.</td></tr>`;
                return;
            }

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

                const catatanText = row.catatan_pembimbing || row.catatan_penguji || '';
                const reviewerLabel = isSidang ? 'Pembimbing' : (tbodyId === 'logTablePreview2' ? 'Penguji' : 'Pembimbing 1');

                const catatanBtn = catatanText ?
                    `<button type="button" onclick="openCatatanModal('${catatanText.replace(/'/g, "\\'")}', '${reviewerLabel}')" class="px-4 py-2 rounded-xl bg-orange-100 hover:bg-orange-200 text-orange-700 text-xs font-bold border border-orange-200 transition cursor-pointer flex items-center gap-1.5 mx-auto">
                        <i class="bi bi-chat-quote-fill"></i> Lihat Catatan
                    </button>` :
                    `<span class="text-slate-400 italic text-xs">Belum ada</span>`;

                if (isSidang) {
                    const fileBimbingan = row.file_bimbingan || '-';
                    const fileSitasi = row.file_sitasi || '-';
                    const filePersyaratan = row.file_persyaratan || '-';
                    const filePendukung = row.file_pendukung || '-';
                    html += `
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 px-4 text-center font-bold text-slate-700">${index + 1}</td>
                        <td class="py-4 px-4 text-xs font-mono max-w-[120px] truncate" title="${fileBimbingan}">${fileBimbingan}</td>
                        <td class="py-4 px-4 text-xs font-mono max-w-[120px] truncate" title="${fileSitasi}">${fileSitasi}</td>
                        <td class="py-4 px-4 text-xs font-mono max-w-[120px] truncate" title="${filePersyaratan}">${filePersyaratan}</td>
                        <td class="py-4 px-4 text-xs font-mono max-w-[120px] truncate" title="${filePendukung}">${filePendukung}</td>
                        <td class="py-4 px-4 whitespace-nowrap text-slate-600 font-medium text-xs">${timeHtml}</td>
                        <td class="py-4 px-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold border ${badgeCls} shadow-2xs">
                                <i class="bi ${badgeIcon}"></i> ${badgeText}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-center">${catatanBtn}</td>
                    </tr>`;
                } else {
                    html += `
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 px-6 text-center font-bold text-slate-700">${index + 1}</td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-lg shrink-0">
                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                </div>
                                <div class="min-w-0">
                                    <a href="<?= base_url('uploads/preview_ta/') ?>${row.file_draft}" target="_blank" class="font-bold text-slate-900 block truncate max-w-xs text-sm hover:text-orange-600 hover:underline">${row.file_draft}</a>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-slate-600 max-w-xs font-normal">
                            <p class="line-clamp-2 italic">${row.catatan_mahasiswa || '-'}</p>
                        </td>
                        <td class="py-4 px-6 whitespace-nowrap text-slate-600 font-medium text-xs">${timeHtml}</td>
                        <td class="py-4 px-6 text-center whitespace-nowrap">
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold border ${badgeCls} shadow-2xs">
                                <i class="bi ${badgeIcon}"></i> ${badgeText}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-center">${catatanBtn}</td>
                    </tr>`;
                }
            });
            tbody.innerHTML = html;
        }

        function renderStatusCard(data) {
            let statusHtml = '';
            if (data.is_p3_app) {
                statusHtml = `<div class="p-4 rounded-2xl border bg-emerald-50 border-emerald-200 text-emerald-800 flex items-start gap-4">
                    <i class="bi bi-check-circle-fill text-2xl mt-1 text-emerald-500"></i>
                    <div><h4 class="font-bold text-lg">Preview 3 Disetujui (Siap Sidang)</h4></div>
                </div>`;
            } else if (data.is_p2_app) {
                statusHtml = `<div class="p-4 rounded-2xl border bg-emerald-50 border-emerald-200 text-emerald-800 flex items-start gap-4">
                    <i class="bi bi-check-circle-fill text-2xl mt-1 text-emerald-500"></i>
                    <div><h4 class="font-bold text-lg">Preview 2 Disetujui (Lanjut Preview 3)</h4></div>
                </div>`;
            } else if (data.is_p1_app) {
                statusHtml = `<div class="p-4 rounded-2xl border bg-emerald-50 border-emerald-200 text-emerald-800 flex items-start gap-4">
                    <i class="bi bi-check-circle-fill text-2xl mt-1 text-emerald-500"></i>
                    <div><h4 class="font-bold text-lg">Preview 1 Disetujui (Lanjut Preview 2)</h4></div>
                </div>`;
            } else {
                let statusText = 'Preview 1 Sedang Direview';
                if (data.latest_p3) statusText = 'Preview 3 Sedang Direview';
                else if (data.latest_p2) statusText = 'Preview 2 Sedang Direview';
                statusHtml = `<div class="p-4 rounded-2xl border bg-amber-50 border-amber-200 text-amber-800 flex items-start gap-4">
                    <i class="bi bi-clock-fill text-2xl mt-1 text-amber-500"></i>
                    <div><h4 class="font-bold text-lg">${statusText}</h4></div>
                </div>`;
            }
            document.getElementById('statusCardContainer').innerHTML = `
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2.5">
                    <i class="bi bi-info-circle-fill text-orange-500"></i> Status Bimbingan Terkini
                </h3>` + statusHtml;
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
                        renderLogTable(data.riwayat_p1, 'logTablePreview1');
                        renderLogTable(data.riwayat_p2, 'logTablePreview2');
                        renderLogTable(data.riwayat_p3, 'logTablePreview3');
                        if (data.riwayat_sidang !== undefined) {
                            renderLogTable(data.riwayat_sidang, 'logTableSidang', true);
                        }
                        renderStatusCard(data);
                        
                        const orig_is_p1_app = <?php echo $is_p1_app ? 'true' : 'false'; ?>;
                        const orig_is_p2_app = <?php echo $is_p2_app ? 'true' : 'false'; ?>;
                        const orig_is_p3_app = <?php echo $is_p3_app ? 'true' : 'false'; ?>;
                        
                        if ((data.is_p1_app && !orig_is_p1_app) || (data.is_p2_app && !orig_is_p2_app) || (data.is_p3_app && !orig_is_p3_app)) {
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
            // Render initial data dari PHP
            renderLogTable(<?= json_encode($riwayat_preview1 ?? []) ?>, 'logTablePreview1');
            renderLogTable(<?= json_encode($riwayat_preview2 ?? []) ?>, 'logTablePreview2');
            renderLogTable(<?= json_encode($riwayat_preview3 ?? []) ?>, 'logTablePreview3');
            renderLogTable(<?= json_encode($riwayat_sidang ?? []) ?>, 'logTableSidang', true);
        });
    </script>
    <?php $this->load->view('partials/modal_rekomendasi_sidang'); ?>
    <?php $this->load->view('partials/custom_cursor'); ?>
</body>
</html>
