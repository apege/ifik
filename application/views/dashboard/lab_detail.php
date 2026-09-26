<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$matched_room = null;
if (!empty($all_ruangan)) {
    foreach ($all_ruangan as $r) {
        $r_id = strtolower(trim((string)$r->id));
        $r_code = strtolower(trim((string)(isset($r->kode_ruangan) ? $r->kode_ruangan : '')));
        $r_name = strtolower(trim((string)$r->nama_ruangan));
        $target = strtolower(trim((string)$lab_key));

        $target_canon = preg_replace('/[^a-z0-9]/', '', $target);
        $r_id_canon   = preg_replace('/[^a-z0-9]/', '', $r_id);
        $r_code_canon = preg_replace('/[^a-z0-9]/', '', $r_code);

        if ($r_id === $target || $r_code === $target || $r_name === $target || 'room_' . $r_id === $target
            || (!empty($target_canon) && ($r_id_canon === $target_canon || $r_code_canon === $target_canon))) {
            $matched_room = $r;
            break;
        }
    }
    if (!$matched_room && !empty($all_ruangan)) {
        $matched_room = $all_ruangan[0];
    }
}

if (!$matched_room) {
    redirect('dashboard');
    exit;
}

$img_url = !empty($matched_room->foto) 
    ? (strpos($matched_room->foto, 'http') === 0 ? $matched_room->foto : base_url($matched_room->foto)) 
    : base_url('assets/images/multimedia.jpg');

$model_url = !empty($matched_room->model_3d) 
    ? (strpos($matched_room->model_3d, 'http') === 0 ? $matched_room->model_3d : base_url($matched_room->model_3d)) 
    : '';

$parsed_room_codes = [];
if (!empty($all_ruangan) && !empty($matched_room)) {
    foreach ($all_ruangan as $ar) {
        if (strcasecmp(trim((string)$ar->nama_ruangan), trim((string)$matched_room->nama_ruangan)) === 0) {
            $codeVal = !empty($ar->kode_ruangan) ? $ar->kode_ruangan : $ar->id;
            if (!empty($codeVal) && !in_array($codeVal, $parsed_room_codes)) {
                $parsed_room_codes[] = $codeVal;
            }
        }
    }
}
if (empty($parsed_room_codes)) {
    if (!empty($matched_room->kode_ruangan)) {
        $parsed_room_codes = array_filter(array_map('trim', explode(',', $matched_room->kode_ruangan)));
    } elseif (!empty($matched_room->id)) {
        $parsed_room_codes = [$matched_room->id];
    }
}

$specs = !empty($matched_room->spesifikasi_fasilitas)
    ? array_map(function($s) {
        return ['icon' => '⚙️', 'title' => 'Spesifikasi', 'desc' => trim($s)];
      }, array_filter(explode("\n", $matched_room->spesifikasi_fasilitas)))
    : [];

$rules = !empty($matched_room->tata_tertib)
    ? array_filter(array_map('trim', explode("\n", $matched_room->tata_tertib)))
    : [];

$category_name = !empty($matched_room->nama_kategori) ? $matched_room->nama_kategori : 'Laboratorium';

$lab = [
    'id_ruangan'     => $matched_room->id,
    'title'          => $matched_room->nama_ruangan,
    'subtitle'       => !empty($matched_room->tagline) ? $matched_room->tagline : ('Fasilitas ' . $category_name . ' Fakultas Industri Kreatif'),
    'badge'          => $category_name,
    'status'         => !empty($matched_room->status) ? $matched_room->status : (!empty($matched_room->akses) ? $matched_room->akses : 'Tersedia'),
    'status_class'   => 'status-open',
    'model'          => $model_url,
    'orbit'          => '45deg 75deg 85%',
    'fov'            => '22deg',
    'bg_gradient'    => 'radial-gradient(circle at 50% 60%, rgba(234, 88, 12, 0.18) 0%, rgba(255, 251, 245, 0.97) 100%)',
    'border_color'   => 'rgba(234, 88, 12, 0.3)',
    'glow_color'     => 'rgba(234, 88, 12, 0.4)',
    'photo'          => $img_url,
    'photo_fallback' => base_url('assets/images/multimedia.jpg'),
    'location'       => !empty($matched_room->lokasi) ? $matched_room->lokasi : 'Gedung Sebatik (FIK)',
    'room_codes'     => $parsed_room_codes,
    'capacity'       => !empty($matched_room->kapasitas) ? ($matched_room->kapasitas . ' Orang') : (!empty($matched_room->jumlah_unit) ? $matched_room->jumlah_unit : '-'),
    'hours'          => !empty($matched_room->jam_operasional) ? $matched_room->jam_operasional : '',
    'desc'           => !empty($matched_room->deskripsi) ? $matched_room->deskripsi : ('Fasilitas ' . $matched_room->nama_ruangan . ' di Fakultas Industri Kreatif Telkom University.'),
    'specs'          => $specs,
    'rules'          => $rules,
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lab['title'] ?> - Fakultas Industri Kreatif (IFIK)</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Model Viewer CDN -->
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.4.0/model-viewer.min.js"></script>

    <style>
        @media (pointer: fine) {
            *, *::before, *::after, html, body, a, button, input, select, textarea, label, summary, model-viewer, model-viewer::part(default-canvas), [role="button"], [onclick] {
                cursor: none !important;
            }
        }

        :root {
            --primary: #ea580c;
            --primary-hover: #c2410c;
            --bg-color: #090d16;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(234, 88, 12, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: #f8fafc;
            min-height: 100vh;
            overflow-x: hidden;
            background-image: 
                radial-gradient(at 0% 0%, rgba(234, 88, 12, 0.12) 0px, transparent 50%),
                radial-gradient(at 100% 50%, rgba(234, 88, 12, 0.08) 0px, transparent 50%);
        }

        /* Container Main Page */
        .page-container {
            max-width: 1240px;
            margin: 0 auto;
            padding: 120px 24px 60px 24px;
        }

        /* Breadcrumb Bar */
        .breadcrumb-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
            font-size: 0.9rem;
            color: #94a3b8;
        }

        .breadcrumb-bar a {
            color: #ea580c;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s ease;
        }

        .breadcrumb-bar a:hover {
            color: #f97316;
        }

        /* Main Glass Card Wrapper */
        .detail-card-main {
            background: #ffffff;
            border: 1px solid rgba(234, 88, 12, 0.18);
            border-radius: 28px;
            padding: 40px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
            color: var(--text-main);
            position: relative;
        }

        /* Layout Grid: 3D Showcase (Kiri) & Info Detail (Kanan) */
        .hero-grid {
            display: grid;
            grid-template-columns: 1fr 1.1fr;
            gap: 40px;
            align-items: center;
        }

        /* 3D Model Display Card */
        .lab-showcase-box {
            position: relative;
            width: 100%;
            height: 480px;
            border-radius: 24px;
            background: radial-gradient(circle at 50% 60%, rgba(234, 88, 12, 0.08) 0%, rgba(248, 250, 252, 0.95) 100%);
            border: 1px solid rgba(234, 88, 12, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: visible;
        }

        /* Backlight Glow di belakang model */
        .lab-showcase-box::before {
            content: '';
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 280px;
            height: 220px;
            background: radial-gradient(circle, rgba(234, 88, 12, 0.25) 0%, rgba(251, 146, 60, 0.08) 50%, transparent 75%);
            filter: blur(35px);
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
        }

        /* Indicators Garis Dempetan dengan 3 State: Passed (Border Oren), Active (Solid Lebar Tebal), Upcoming (Biasa) */
        .showcase-line-indicators {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 4px; /* Dempetan seperti di gambar */
            margin-top: 18px;
        }

        .detail-line {
            width: 36px;
            height: 5px;
            border-radius: 4px;
            background: rgba(0, 0, 0, 0.12);
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.35s cubic-bezier(0.25, 1, 0.5, 1);
            box-sizing: border-box;
        }

        .detail-line:hover {
            background: rgba(234, 88, 12, 0.3);
        }

        /* State 1: Line sebelum nya yang sudah dilewati (Hanya Border Oren) */
        .detail-line.passed {
            background: transparent;
            border: 1.5px solid #ea580c;
        }

        /* State 2: Line aktif saat ini (Solid Oren Lebar & Tebal) */
        .detail-line.active {
            width: 80px;
            height: 6px;
            background: #ea580c;
            border: 1px solid #ea580c;
            border-radius: 6px;
            box-shadow: 0 2px 10px rgba(234, 88, 12, 0.45);
        }

        .showcase-content-view {
            width: 100%;
            height: 100%;
            position: relative;
            z-index: 10;
            overflow: hidden;
            border-radius: 24px;
        }

        .showcase-content-view img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 24px;
            transition: transform 0.4s ease;
        }

        .showcase-content-view img:hover {
            transform: scale(1.03);
        }

        .lab-showcase-box model-viewer {
            width: 100%;
            height: 100%;
            position: relative;
            z-index: 10;
        }

        .badge-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: rgba(234, 88, 12, 0.1);
            color: #ea580c;
            border: 1px solid rgba(234, 88, 12, 0.25);
            margin-bottom: 12px;
        }

        .lab-title-main {
            font-size: 2.5rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            line-height: 1.2;
            margin-bottom: 10px;
            font-family: 'Outfit', sans-serif;
        }

        .lab-subtitle {
            font-size: 1.05rem;
            color: #64748b;
            font-weight: 500;
            line-height: 1.5;
            margin-bottom: 24px;
        }

        /* Quick Meta Badges */
        .meta-list {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 8px 16px;
            border-radius: 14px;
            font-size: 0.88rem;
            font-weight: 600;
            color: #334155;
        }

        .meta-item span.icon {
            font-size: 1.1rem;
        }

        .desc-paragraph {
            font-size: 0.96rem;
            color: #475569;
            line-height: 1.7;
            margin-bottom: 30px;
        }

        /* Action Buttons */
        .action-group {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }

        .btn-primary-action {
            background: var(--primary);
            color: #ffffff;
            padding: 14px 28px;
            border-radius: 16px;
            font-weight: 700;
            font-size: 0.95rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 10px 25px rgba(234, 88, 12, 0.3);
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary-action:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(234, 88, 12, 0.4);
        }

        .btn-secondary-action {
            background: #f1f5f9;
            color: #334155;
            padding: 14px 24px;
            border-radius: 16px;
            font-weight: 700;
            font-size: 0.95rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .btn-secondary-action:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        /* Section Title */
        .section-header-block {
            margin-top: 50px;
            margin-bottom: 24px;
        }

        .section-header-block h2 {
            font-size: 1.6rem;
            font-weight: 800;
            color: #0f172a;
            font-family: 'Outfit', sans-serif;
        }

        /* Specs Cards Grid */
        .specs-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .spec-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 24px;
            transition: all 0.3s ease;
        }

        .spec-card:hover {
            border-color: rgba(234, 88, 12, 0.3);
            background: #ffffff;
            box-shadow: 0 10px 25px rgba(234, 88, 12, 0.08);
            transform: translateY(-3px);
        }

        .spec-card-head {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
        }

        .spec-card-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(234, 88, 12, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .spec-card h3 {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1e293b;
        }

        .spec-card p {
            font-size: 0.88rem;
            color: #64748b;
            line-height: 1.6;
        }

        /* Rules Block */
        .rules-block {
            margin-top: 40px;
            background: #fff7ed;
            border: 1px solid rgba(234, 88, 12, 0.25);
            border-radius: 20px;
            padding: 28px;
        }

        .rules-block h3 {
            font-size: 1.15rem;
            font-weight: 800;
            color: #ea580c;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .rules-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .rules-list li {
            font-size: 0.92rem;
            color: #475569;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            line-height: 1.5;
        }

        .rules-list li::before {
            content: '✓';
            color: #ea580c;
            font-weight: 800;
        }

        /* Switch Lab Navigation */
        .nav-switch-labs {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
        }

        .switch-link {
            text-decoration: none;
            color: #ea580c;
            font-weight: 700;
            font-size: 0.92rem;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: transform 0.2s ease;
        }

        .switch-link:hover {
            transform: translateX(4px);
        }

        @media (max-width: 992px) {
            .hero-grid {
                grid-template-columns: 1fr;
            }
            .specs-grid {
                grid-template-columns: 1fr;
            }
            .detail-card-main {
                padding: 24px;
            }
            .lab-showcase-box {
                height: 340px;
            }
        }
    </style>
</head>
<body>

    <!-- Include Navbar Partial -->
    <?php $this->load->view('partials/navbar'); ?>

    <div class="page-container">
        
        <!-- Breadcrumb Navigation -->
        <div class="breadcrumb-bar">
            <a href="<?= site_url('dashboard') ?>">&larr; Kembali ke Beranda Dashboard</a>
            <span>/</span>
            <span>Detail Laboratorium &amp; Fasilitas</span>
            <span>/</span>
            <span style="color: #ffffff; font-weight: 600;"><?= $lab['title'] ?></span>
        </div>

        <!-- Main Detail Card -->
        <div class="detail-card-main">
            
            <!-- Hero Grid -->
            <div class="hero-grid">
                
                <!-- Left Column: 3D Interactive Model Showcase & Photo Slider -->
                <div>
                    <div class="lab-showcase-box" id="showcaseBox" style="background: <?= $lab['bg_gradient'] ?>; border-color: <?= $lab['border_color'] ?>;">
                        <style>
                            .lab-showcase-box::before {
                                background: radial-gradient(circle, <?= $lab['glow_color'] ?> 0%, rgba(251, 146, 60, 0.05) 50%, transparent 75%) !important;
                            }
                        </style>

                        <!-- Mode View 1: Foto Asli (Default Tampil Pertama) -->
                        <div class="showcase-content-view" id="viewPhoto" style="display: block;">
                            <img id="labRealPhotoImg" 
                                 src="<?= $lab['photo'] ?>" 
                                 alt="Foto Asli <?= $lab['title'] ?>" 
                                 onerror="this.onerror=null; this.src='<?= $lab['photo_fallback'] ?>';" />
                        </div>

                        <!-- Mode View 2: 3D Model (Tampil Kedua jika ada) -->
                        <?php if (!empty($lab['model'])): ?>
                        <div class="showcase-content-view" id="view3D" style="display: none;">
                            <model-viewer 
                                id="labDetailViewer"
                                src="<?= $lab['model'] ?>" 
                                alt="3D Visual <?= $lab['title'] ?>" 
                                bounds="tight"
                                camera-orbit="<?= $lab['orbit'] ?>"
                                camera-target="<?= isset($lab['target']) ? $lab['target'] : 'auto auto auto' ?>"
                                field-of-view="<?= $lab['fov'] ?>"
                                camera-controls 
                                touch-action="none"
                                shadow-intensity="1.5" 
                                shadow-softness="0.8"
                                exposure="1.2"
                                interaction-prompt="none"
                                style="background-color: transparent;">
                            </model-viewer>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Indicator Line Bar Murni (Line 1: Foto Asli, Line 2: Model 3D) -->
                    <?php if (!empty($lab['model'])): ?>
                    <div class="showcase-line-indicators">
                        <span class="detail-line active" id="detailLine0" onclick="switchShowcaseMode('photo')" title="Foto Dokumentasi Asli"></span>
                        <span class="detail-line" id="detailLine1" onclick="switchShowcaseMode('3d')" title="Model 3D Interaktif"></span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Right Column: Lab Info & Actions -->
                <div>
                    <div class="badge-tag">
                        <span>●</span> <?= $lab['badge'] ?>
                    </div>

                    <h1 class="lab-title-main"><?= $lab['title'] ?></h1>
                    <p class="lab-subtitle"><?= $lab['subtitle'] ?></p>

                    <!-- Meta Badges -->
                    <div class="meta-list">
                        <div class="meta-item">
                            <span class="icon">📍</span>
                            <span><?= $lab['location'] ?></span>
                        </div>
                        <?php if (!empty($lab['room_codes'])): ?>
                        <div class="meta-item" style="background: #fff7ed; border-color: #fed7aa; color: #9a3412;">
                            <span class="icon">🚪</span>
                            <span style="font-weight: 700; color: #c2410c;">Ruangan:</span>
                            <div style="display: flex; gap: 5px; flex-wrap: wrap; align-items: center;">
                                <?php foreach ($lab['room_codes'] as $rc): ?>
                                    <span style="background: #ea580c; color: #ffffff; padding: 2px 8px; border-radius: 6px; font-size: 0.78rem; font-weight: 800; letter-spacing: 0.02em;"><?= htmlspecialchars($rc) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="meta-item">
                            <span class="icon">💺</span>
                            <span><?= $lab['capacity'] ?></span>
                        </div>
                        <?php if (!empty($lab['hours'])): ?>
                        <div class="meta-item">
                            <span class="icon">⏰</span>
                            <span><?= htmlspecialchars($lab['hours']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <p class="desc-paragraph"><?= htmlspecialchars($lab['desc']) ?></p>

                </div>

            </div>

            <!-- Specs Grid Block (Hanya tampil jika ada data spesifikasi) -->
            <?php if (!empty($lab['specs'])): ?>
            <div class="section-header-block">
                <h2>Fasilitas &amp; Spesifikasi Perangkat</h2>
            </div>

            <div class="specs-grid">
                <?php foreach ($lab['specs'] as $spec): ?>
                    <div class="spec-card">
                        <div class="spec-card-head">
                            <div class="spec-card-icon"><?= $spec['icon'] ?></div>
                            <h3><?= htmlspecialchars($spec['title']) ?></h3>
                        </div>
                        <p><?= htmlspecialchars($spec['desc']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Rules Block (Hanya tampil jika ada tata tertib) -->
            <?php if (!empty($lab['rules'])): ?>
            <div class="rules-block">
                <h3>⚠️ Tata Tertib &amp; Ketentuan Pengguna</h3>
                <ul class="rules-list">
                    <?php foreach ($lab['rules'] as $rule): ?>
                        <li><?= htmlspecialchars($rule) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- Switch Labs Footer Links -->
            <?php
                $count_all = count($all_ruangan ?? []);
                $current_pos = -1;
                $prev_room = null;
                $next_room = null;

                if ($count_all > 1) {
                    foreach ($all_ruangan as $idx => $r) {
                        if ((string)$r->id === (string)$matched_room->id) {
                            $current_pos = $idx;
                            break;
                        }
                    }
                    if ($current_pos !== -1) {
                        $prev_room = $all_ruangan[($current_pos - 1 + $count_all) % $count_all];
                        $next_room = $all_ruangan[($current_pos + 1) % $count_all];
                    }
                }
            ?>
            <?php if ($count_all > 1): ?>
            <div class="nav-switch-labs">
                <?php if ($prev_room): ?>
                    <a href="<?= site_url('dashboard/lab_detail/' . $prev_room->id) ?>" class="switch-link">&larr; <?= htmlspecialchars($prev_room->nama_ruangan) ?></a>
                <?php else: ?>
                    <span></span>
                <?php endif; ?>

                <?php if ($next_room): ?>
                    <a href="<?= site_url('dashboard/lab_detail/' . $next_room->id) ?>" class="switch-link"><?= htmlspecialchars($next_room->nama_ruangan) ?> &rarr;</a>
                <?php else: ?>
                    <span></span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div>

    </div>

    <!-- Include Footer Partial -->
    <?php $this->load->view('partials/footer'); ?>

    <script>
        let currentShowcaseMode = 'photo';

        function toggleShowcasePrevNext() {
            if (currentShowcaseMode === 'photo') {
                switchShowcaseMode('3d');
            } else {
                switchShowcaseMode('photo');
            }
        }

        function switchShowcaseMode(mode) {
            currentShowcaseMode = mode;
            const view3D = document.getElementById('view3D');
            const viewPhoto = document.getElementById('viewPhoto');
            const line0 = document.getElementById('detailLine0');
            const line1 = document.getElementById('detailLine1');

            if (mode === 'photo') {
                if (viewPhoto) viewPhoto.style.display = 'block';
                if (view3D) view3D.style.display = 'none';
                if (line0) { line0.classList.add('active'); line0.classList.remove('passed'); }
                if (line1) { line1.classList.remove('active'); line1.classList.remove('passed'); }
            } else {
                if (viewPhoto) viewPhoto.style.display = 'none';
                if (view3D) view3D.style.display = 'block';
                if (line0) { line0.classList.remove('active'); line0.classList.add('passed'); }
                if (line1) { line1.classList.add('active'); line1.classList.remove('passed'); }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const viewer = document.getElementById('labDetailViewer');
            if (!viewer) return;

            const defaultOrbit = viewer.getAttribute('camera-orbit') || '45deg 75deg 44%';
            const defaultFov = viewer.getAttribute('field-of-view') || '17deg';
            let resetTimer = null;

            // 1. Double Click / Double Tap -> Toggle Zoom In / Zoom Out
            let isZoomedIn = false;
            viewer.addEventListener('dblclick', (e) => {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();

                viewer.interpolationDecay = 120;
                let currentFov = parseFloat(viewer.fieldOfView || viewer.getFieldOfView()) || 17;

                if (!isZoomedIn && currentFov > 13) {
                    viewer.fieldOfView = '10deg'; // Zoom in murni di posisi tengah
                    isZoomedIn = true;
                } else {
                    viewer.fieldOfView = defaultFov; // Zoom out kembali normal
                    isZoomedIn = false;
                }
            }, true);

            // 2. Touch Gestures 2 Jari
            let touchStartX = 0;
            let touchStartY = 0;

            viewer.addEventListener('touchstart', (e) => {
                viewer.interpolationDecay = 80;
                if (e.touches.length === 2) {
                    touchStartX = (e.touches[0].clientX + e.touches[1].clientX) / 2;
                    touchStartY = (e.touches[0].clientY + e.touches[1].clientY) / 2;
                }
            }, { passive: true });

            viewer.addEventListener('touchmove', (e) => {
                if (e.touches.length === 2) {
                    let currentX = (e.touches[0].clientX + e.touches[1].clientX) / 2;
                    let currentY = (e.touches[0].clientY + e.touches[1].clientY) / 2;
                    let diffX = currentX - touchStartX;
                    let diffY = currentY - touchStartY;

                    if (Math.abs(diffX) > 5) {
                        let currentFov = parseFloat(viewer.fieldOfView || viewer.getFieldOfView()) || 17;
                        let deltaFov = diffX > 0 ? -1.0 : 1.0;
                        let newFov = Math.max(6, Math.min(32, currentFov + deltaFov));
                        viewer.interpolationDecay = 60;
                        viewer.fieldOfView = `${newFov}deg`;

                        const curTarget = viewer.getCameraTarget();
                        if (curTarget) {
                            let nextX = curTarget.x - diffX * 0.005;
                            let nextY = curTarget.y + diffY * 0.005;
                            viewer.cameraTarget = `${nextX}m ${nextY}m ${curTarget.z}m`;
                        }

                        touchStartX = currentX;
                        touchStartY = currentY;
                    }
                }
            }, { passive: true });

            // 3. Mouse / Pointer Dragging (Pan ke Kiri & Kanan via Shift + Klik / Klik Kanan)
            let isShiftPressed = false;
            let prevX = 0, prevY = 0;

            window.addEventListener('keydown', (e) => {
                if (e.key === 'Shift') isShiftPressed = true;
            });
            window.addEventListener('keyup', (e) => {
                if (e.key === 'Shift') isShiftPressed = false;
            });

            viewer.addEventListener('pointerdown', (e) => {
                viewer.interpolationDecay = 80;
                prevX = e.clientX;
                prevY = e.clientY;
            });

            viewer.addEventListener('pointermove', (e) => {
                if ((e.buttons === 1 && (isShiftPressed || e.shiftKey)) || e.buttons === 2 || e.buttons === 4) {
                    let dx = e.clientX - prevX;
                    let dy = e.clientY - prevY;
                    prevX = e.clientX;
                    prevY = e.clientY;

                    const curTarget = viewer.getCameraTarget();
                    if (curTarget) {
                        let factor = 0.008;
                        let nextX = curTarget.x - dx * factor;
                        let nextY = curTarget.y + dy * factor;
                        viewer.cameraTarget = `${nextX}m ${nextY}m ${curTarget.z}m`;
                    }
                }
            });

            viewer.addEventListener('contextmenu', (e) => {
                e.preventDefault();
            });

            viewer.addEventListener('camera-change', (e) => {
                if (e.detail && e.detail.source === 'user-interaction') {
                    viewer.interpolationDecay = 80;
                    clearTimeout(resetTimer);
                    resetTimer = setTimeout(() => {
                        viewer.interpolationDecay = 400;
                        viewer.cameraOrbit = defaultOrbit;
                        viewer.cameraTarget = 'auto auto auto';
                    }, 5000);
                }
            });
        });

        function rotate3dModel(deltaDeg) {
            const viewer = document.getElementById('labDetailViewer');
            if (!viewer) return;
            viewer.interpolationDecay = 120;
            const orbit = viewer.getCameraOrbit();
            if (orbit) {
                let thetaDeg = (orbit.theta * 180) / Math.PI;
                let phiDeg = (orbit.phi * 180) / Math.PI;
                let newTheta = thetaDeg + deltaDeg;
                viewer.cameraOrbit = `${newTheta}deg ${phiDeg}deg ${orbit.radius}m`;
            }
        }

        function zoom3dModel(deltaFov) {
            const viewer = document.getElementById('labDetailViewer');
            if (!viewer) return;
            viewer.interpolationDecay = 120;
            let currentFov = parseFloat(viewer.fieldOfView || viewer.getFieldOfView()) || 17;
            let newFov = Math.max(6, Math.min(32, currentFov + deltaFov));
            viewer.fieldOfView = `${newFov}deg`;
        }

        function reset3dModel() {
            const viewer = document.getElementById('labDetailViewer');
            if (!viewer) return;
            viewer.interpolationDecay = 200;
            const defaultOrbit = viewer.getAttribute('camera-orbit') || '45deg 75deg 44%';
            const defaultFov = viewer.getAttribute('field-of-view') || '17deg';
            viewer.cameraOrbit = defaultOrbit;
            viewer.cameraTarget = 'auto auto auto';
            viewer.fieldOfView = defaultFov;
        }
    </script>
</body>
</html>
