<style>
    /* Styling khusus Sesi 1 */
    #section-carousel {
        position: relative;
        background-color: #d97706; /* Fallback color asli */
    }

    /* Horizontal Carousel Snapping (Hijacking kiri-kanan) */
    .carousel-container {
        display: flex;
        overflow-x: scroll;
        scroll-snap-type: x mandatory;
        width: 100%;
        height: 100%;
        scrollbar-width: none;
        scroll-behavior: smooth;
    }
    .carousel-container::-webkit-scrollbar { display: none; }

    /* Tiap Layar/Slide Carousel */
    .carousel-slide {
        flex: 0 0 100vw;
        height: 100%;
        scroll-snap-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 2;
        background-image: url('<?= base_url("assets/images/background.png") ?>');
        background-size: cover; 
        background-position: center;
    }

    /* Khusus Slide 1 background-nya fakultas.jpg tanpa filter */
    .carousel-slide.slide-1 {
        background-image: url('<?= base_url("assets/images/Fakultas.jpg") ?>');
    }

    /* Background Video Fullscreen */
    .background-video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: -1;
    }

    .scroll-hint {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        color: #fff;
        font-size: 0.9rem;
        letter-spacing: 2px;
        text-transform: uppercase;
        animation: bounce 2s infinite;
        z-index: 20;
        text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        font-weight: 700;
    }

    /* Carousel Indicators (Dots) - Modern Glassmorphism Hybrid */
    .carousel-indicators {
        position: absolute;
        bottom: 38px;
        left: 50%;
        transform: translateX(-50%);
        width: 86vw;
        max-width: 1350px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0;
        z-index: 30;
    }
    
    .dots-half {
        flex: 1 1 0;
        width: calc(50% - 42.5px);
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .dots-half.dots-left {
        justify-content: flex-end;
    }

    .dots-half.dots-right {
        justify-content: flex-start;
    }

    .dots-center-gap {
        width: 85px;
        min-width: 85px;
        max-width: 85px;
        flex: 0 0 85px;
        pointer-events: none;
    }

    .carousel-indicators .dot {
        flex: 1 1 0;
        max-width: 250px;
        min-width: 100px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        cursor: pointer;
        opacity: 0.7;
        transition: all 0.3s ease;
        background: rgba(0, 0, 0, 0.45);
        padding: 9px 12px;
        border-radius: 12px;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    /* Ketika sayap kanan hanya memiliki 1 item (Prestasi), buat lebarnya proporsional mengimbangi 2 item di sayap kiri */
    .dots-half.dots-right .dot:only-child {
        max-width: 512px;
        width: 100%;
    }
    
    .carousel-indicators .dot.active, 
    .carousel-indicators .dot:hover {
        opacity: 1;
        background: rgba(0, 0, 0, 0.75);
        border-color: rgba(234, 88, 12, 0.7);
        box-shadow: 0 4px 15px rgba(0,0,0,0.4);
        transform: translateY(-2px);
    }

    .carousel-indicators .dot .dot-label {
        font-size: 0.82rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #fff;
        letter-spacing: 1.2px;
        height: 18px;
        display: flex;
        align-items: center;
    }
    
    .carousel-indicators .dot .dot-track {
        height: 4px;
        width: 100%;
        border-radius: 4px;
        background: rgba(255, 255, 255, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .carousel-indicators .dot .progress {
        position: absolute;
        top: 0; left: 0; height: 100%;
        background: #fff;
        width: 0%;
        border-radius: 4px;
        box-shadow: 0 0 8px #fff, 0 0 15px rgba(255,255,255,0.8);
    }

    /* --- FASILITAS SEGMENTED SEEKBAR & CONTROLS GROUP --- */
    .dot-fasilitas {
        position: relative;
    }

    .dot-label-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        height: 18px;
    }

    .fasilitas-controls-group {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Tombol Tambah Ruangan Khusus Admin */
    .lab-add-room-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #ea580c;
        border: none;
        color: #ffffff;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(234, 88, 12, 0.5);
        flex-shrink: 0;
        text-decoration: none;
        padding: 0;
    }

    .lab-add-room-btn:hover {
        background: #ffffff;
        color: #ea580c;
        transform: scale(1.15);
        box-shadow: 0 4px 10px rgba(255, 255, 255, 0.6);
    }

    .lab-add-room-btn svg {
        width: 12px;
        height: 12px;
        fill: currentColor;
    }

    /* Badge Counter Nomor Ruangan Aktif */
    .fasilitas-counter {
        font-size: 0.68rem;
        font-weight: 800;
        color: rgba(255, 255, 255, 0.85);
        background: rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.18);
        padding: 1px 6px;
        border-radius: 10px;
        letter-spacing: 0.5px;
        font-variant-numeric: tabular-nums;
        transition: all 0.3s ease;
        line-height: 1.2;
    }

    .dot.active .fasilitas-counter {
        color: #ffffff;
        border-color: rgba(234, 88, 12, 0.6);
        background: rgba(234, 88, 12, 0.25);
        box-shadow: 0 0 8px rgba(234, 88, 12, 0.3);
    }

    .dot-track-continuous {
        height: 4px;
        width: 100%;
        background: rgba(255, 255, 255, 0.25);
        border-radius: 4px;
        position: relative;
        cursor: pointer;
        transition: height 0.2s cubic-bezier(0.25, 1, 0.5, 1), background 0.2s ease, box-shadow 0.2s ease;
        touch-action: none;
    }

    .dot-track-continuous::before {
        content: '';
        position: absolute;
        top: -8px;
        bottom: -8px;
        left: 0;
        right: 0;
    }

    .dot-track-continuous:hover,
    .dot-track-continuous.is-dragging {
        height: 6px;
        background: rgba(255, 255, 255, 0.38);
    }

    .dot-track-continuous .thumb {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 25%;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 4px;
        overflow: hidden;
        transition: left 0.4s cubic-bezier(0.25, 1, 0.5, 1), width 0.3s ease, opacity 0.3s ease;
        box-shadow: 0 0 8px rgba(255, 255, 255, 0.6), 0 0 12px rgba(234, 88, 12, 0.35);
    }

    .dot-track-continuous.is-dragging .thumb {
        transition: width 0.3s ease, opacity 0.3s ease; /* No lag during drag */
    }

    .dot-track-continuous .thumb .progress {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 0%;
        background: #ffffff;
        border-radius: 4px;
        box-shadow: 0 0 8px #ffffff, 0 0 14px rgba(255, 255, 255, 0.9);
    }

    /* Floating Scrub Tooltip Glassmorphism */
    .fasilitas-scrub-tooltip {
        position: absolute;
        bottom: calc(100% + 9px);
        left: 0;
        transform: translateX(-50%) translateY(4px);
        pointer-events: none;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.15s ease, transform 0.15s cubic-bezier(0.25, 1, 0.5, 1);
        z-index: 100;
        white-space: nowrap;
        font-size: 0.72rem;
        font-weight: 700;
        color: #ffffff;
        background: rgba(15, 23, 42, 0.92);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.22);
        padding: 3px 9px;
        border-radius: 7px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6), 0 0 12px rgba(234, 88, 12, 0.35);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .fasilitas-scrub-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border-width: 4px;
        border-style: solid;
        border-color: rgba(15, 23, 42, 0.92) transparent transparent transparent;
    }

    .fasilitas-scrub-tooltip.visible {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(0);
    }

    /* Tombol Play/Pause Kecil di Pinggir Label Fasilitas */
    .lab-play-pause-btn-side {
        width: 18px;
        height: 18px;
        min-width: 18px;
        border-radius: 50%;
        background: #ea580c;
        border: none;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(234, 88, 12, 0.5);
    }

    .lab-play-pause-btn-side:hover {
        background: #ffffff;
        color: #ea580c;
        transform: scale(1.2);
    }

    .lab-play-pause-btn-side svg {
        width: 9px;
        height: 9px;
        fill: currentColor;
    }

    /* --- SLIDE 1 LAYOUT DENGAN PAGINATION TEKS --- */
    .slide1-layout {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        padding: 0 80px;
        display: flex;
        align-items: center;
        z-index: 10;
        pointer-events: none;
    }
    .slide1-text-container {
        display: flex;
        flex-direction: column;
        gap: 15px;
        width: 500px;
        max-width: 90vw;
        z-index: 10;
        margin-top: -40px;
        margin-left: 20px;
    }
    .slide1-title-box {
        background: rgba(255, 255, 255, 0.95);
        padding: 15px 30px;
        border-radius: 14px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        backdrop-filter: blur(8px);
        pointer-events: auto;
        width: 100%;
        box-sizing: border-box;
    }
    .slide1-title-box h1 {
        color: #ea580c;
        font-size: 2.1rem;
        font-weight: 800;
        margin: 0;
    }
    .slide1-content-box {
        background: rgba(255, 255, 255, 0.95);
        padding: 22px 28px;
        border-radius: 14px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        color: #334155;
        font-size: 0.85rem;
        line-height: 1.6;
        text-align: justify;
        backdrop-filter: blur(8px);
        pointer-events: auto;
        width: 100%;
        box-sizing: border-box;
    }
    .dekanat-img-right {
        position: absolute;
        bottom: 0;
        right: 20px;
        max-height: 380px;
        z-index: 20;
        pointer-events: none;
    }
    
    /* ===== BACA SELENGKAPNYA BUTTON ===== */
    .read-more-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        pointer-events: auto;
        margin-top: 8px;
        padding: 9px 20px;
        background: #ea580c;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-weight: 800;
        font-size: 0.82rem;
        cursor: pointer;
        text-decoration: none;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 14px rgba(234,88,12,0.35);
        transition: all 0.25s ease;
        width: 100%;
        justify-content: center;
        white-space: normal;
        word-break: break-word;
        text-align: center;
    }
    .read-more-btn:hover {
        background: #c2410c;
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(234,88,12,0.45);
        color: #fff;
    }
    .read-more-btn svg { width: 14px; height: 14px; flex-shrink: 0; }

    /* ===== DOTS RIGHT: SCROLLABLE OVERFLOW ===== */
    .dots-half.dots-right {
        position: relative;
        overflow: hidden;
    }
    .dots-right-inner {
        display: flex;
        gap: 12px;
        align-items: center;
        transition: transform 0.35s cubic-bezier(0.25,1,0.5,1);
        will-change: transform;
    }
    /* Arrow nav buttons for dots-right */
    .dots-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: rgba(234,88,12,0.85);
        border: none;
        color: #fff;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        flex-shrink: 0;
    }
    .dots-nav-btn.visible { opacity: 1; pointer-events: auto; }
    .dots-nav-btn svg { width: 11px; height: 11px; }
    .dots-nav-btn.btn-prev { left: 0; }
    .dots-nav-btn.btn-next { right: 0; }
    .dots-nav-btn:hover { background: #ea580c; transform: translateY(-50%) scale(1.1); }
    
    @media (max-width: 900px) {
        .slide1-layout { padding: 0 20px; }
        .slide1-text-container { margin-top: 0; width: 100%; }
        .dekanat-img-right { max-height: 250px; opacity: 0.5; }
        .carousel-indicators { width: 90vw; bottom: 25px; }
    }
    
    @keyframes slideProgress {
        0% { width: 0%; }
        100% { width: 100%; }
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translate(-50%, 0); }
        40% { transform: translate(-50%, -10px); }
        60% { transform: translate(-50%, -5px); }
    }
</style>

<!-- Sesi 1: Carousel -->
<div class="section-wrapper" id="section-carousel">

    <!-- Carousel Horizontal (Scroll Hijacking) -->
    <div class="carousel-container" id="headerCarousel">
        <!-- Slide 1 (Overview - Fakultas Industri Kreatif) -->
        <div class="carousel-slide slide-1">
            <div class="slide1-layout">
                <div class="slide1-text-container">
                    <div class="slide1-title-box">
                        <h1><?= htmlspecialchars($header_settings->title ?? 'Fakultas Industri Kreatif') ?></h1>
                    </div>
                    <div class="slide1-content-box" id="headerDescBox">
                        <?php
                            $full_desc = $header_settings->description ?? 'Seiring dengan berkembangnya kebutuhan pelayanan untuk mahasiswa, dosen dan pegawai FIK maka diperlukan peningkatan layanan yang mengusung efisiensi dan efektifitas. Ifik lahir dari keresahan dan kesulitan mahasiswa maupun dosen dalam beberapa layanan, antara lain pendaftaran TA, bimbingan online, dokumen online, peminjaman ruangan dan lain sebagainya. Sejak dibuat tahun 2021 oleh tim unit lab FIK, aplikasi berbasis web ini telah digunakan hingga saat ini untuk mempermudah layanan untuk kalangan internal FIK, baik untuk mahasiswa, dosen maupun pegawai FIK.';
                            $plain_desc = strip_tags($full_desc);
                            $char_limit = 280;
                            if (mb_strlen($plain_desc) > $char_limit) {
                                $truncated = mb_substr($plain_desc, 0, $char_limit);
                                $last_space = mb_strrpos($truncated, ' ');
                                echo htmlspecialchars($last_space ? mb_substr($truncated, 0, $last_space) : $truncated) . '...';
                            } else {
                                echo htmlspecialchars($plain_desc);
                            }
                        ?>
                    </div>
                    <?php if (mb_strlen(strip_tags($header_settings->description ?? '')) > 280): ?>
                    <a href="<?= base_url('dashboard/about') ?>" class="read-more-btn">
                        Baca Selengkapnya
                        <svg fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php $dekanat_img = $header_settings->dekanat_image ?? 'dekanat2.png'; ?>
            <img src="<?= base_url('assets/images/' . $dekanat_img) ?>" alt="Dekanat" class="dekanat-img-right">
        </div>
        
        <!-- Slide 2 (Fasilitas: Lab Fakultas) -->
        <div class="carousel-slide slide-2" style="position: relative; width: 100vw; height: 100%;">
            <?php $this->load->view('dashboard/sections/lab'); ?>
        </div>
        
        <!-- Slide 3 (Prestasi & Inovasi) -->
        <div class="carousel-slide slide-3">
            <div class="slide1-layout">
                <div class="slide1-text-container">
                    <div class="slide1-title-box">
                        <h1>Prestasi &amp; Inovasi FIK</h1>
                    </div>
                    <div class="slide1-content-box">
                        Fakultas Industri Kreatif secara konsisten mengukir berbagai prestasi baik di tingkat nasional maupun internasional. Melalui fasilitas laboratorium yang canggih dan bimbingan dosen berpengalaman, mahasiswa FIK terus melahirkan karya-karya inovatif di bidang desain, seni, media interaktif, dan teknologi kreatif.
                    </div>
                </div>
            </div>
            <img src="<?= base_url('assets/images/' . $dekanat_img) ?>" alt="Dekanat" class="dekanat-img-right">
        </div>
        
        <!-- Custom Slides dari Database (Slide 4, 5, 6, dst) -->
        <?php if (!empty($header_slides) && count($header_slides) > 3): ?>
            <?php for ($i = 3; $i < count($header_slides); $i++): ?>
                <?php $s = $header_slides[$i]; ?>
                <div class="carousel-slide slide-custom slide-<?= $i + 1 ?>" style="position: relative; width: 100vw; height: 100%; <?= $s->media_type === 'image' && !empty($s->media_path) ? 'background-image: url(' . base_url('assets/images/' . $s->media_path) . '); background-size: cover; background-position: center;' : '' ?>">
                    <?php if ($s->media_type === 'video' && !empty($s->media_path)): ?>
                        <video autoplay muted loop playsinline style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                            <source src="<?= base_url('assets/videos/' . $s->media_path) ?>" type="video/mp4">
                        </video>
                    <?php endif; ?>
                    <div class="slide1-layout">
                        <div class="slide1-text-container">
                            <?php if (!empty($s->show_text) && $s->show_text == 1): ?>
                                <!-- Toggle ON: Tampilkan judul & deskripsi kustom dari TinyMCE -->
                                <?php if (!empty($s->overlay_title)): ?>
                                <div class="slide1-title-box">
                                    <h1><?= htmlspecialchars($s->overlay_title) ?></h1>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($s->overlay_description)): ?>
                                <div class="slide1-content-box">
                                    <?= $s->overlay_description /* HTML dari TinyMCE */ ?>
                                </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <!-- Toggle OFF: Gunakan judul & deskripsi default dari header_settings -->
                                <div class="slide1-title-box">
                                    <h1><?= htmlspecialchars($header_settings->title ?? 'Fakultas Industri Kreatif') ?></h1>
                                </div>
                                <div class="slide1-content-box">
                                    <?php
                                        $def_desc  = $header_settings->description ?? '';
                                        $def_plain = strip_tags($def_desc);
                                        $def_limit = 280;
                                        if (mb_strlen($def_plain) > $def_limit) {
                                            $def_cut   = mb_substr($def_plain, 0, $def_limit);
                                            $def_space = mb_strrpos($def_cut, ' ');
                                            echo htmlspecialchars($def_space ? mb_substr($def_cut, 0, $def_space) : $def_cut) . '...';
                                        } else {
                                            echo htmlspecialchars($def_plain);
                                        }
                                    ?>
                                </div>
                                <?php if (mb_strlen(strip_tags($header_settings->description ?? '')) > 280): ?>
                                <a href="<?= base_url('dashboard/about') ?>" class="read-more-btn">
                                    Baca Selengkapnya
                                    <svg fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <img src="<?= base_url('assets/images/' . $dekanat_img) ?>" alt="Dekanat" class="dekanat-img-right">
                </div>
            <?php endfor; ?>
        <?php endif; ?>
    </div>
    
    <!-- Indikator Dots Terbagi Kiri & Kanan (Simetris Mengelilingi Tombol Tengah) -->
    <?php 
        $all_rooms = [];
        $seen_keys = [];
        $featured_keys = ['multimedia', 'aula', 'cintiq', 'greenscreen', 'incubator', 'mac'];

        if (!empty($ruangan)) {
            foreach ($ruangan as $r) {
                $n = strtolower(trim(isset($r->nama_ruangan) ? $r->nama_ruangan : ''));
                $c_code = strtolower(trim(isset($r->kode_ruangan) ? $r->kode_ruangan : ''));
                if ($n !== 'ss' && $c_code !== 'ss' && strpos($n, 'test') === false && strpos($n, 'qqq') === false) {
                    $lab_code = '';
                    if (strpos($n, 'multimedia') !== false && !in_array('multimedia', $seen_keys)) $lab_code = 'multimedia';
                    elseif (strpos($n, 'aula') !== false && !in_array('aula', $seen_keys)) $lab_code = 'aula';
                    elseif ((strpos($n, 'cintiq') !== false || strpos($n, 'tablet') !== false || strpos($n, 'sablon') !== false) && !in_array('cintiq', $seen_keys)) $lab_code = 'cintiq';
                    elseif (strpos($n, 'green') !== false && !in_array('greenscreen', $seen_keys)) $lab_code = 'greenscreen';
                    elseif ((strpos($n, 'inkubator') !== false || strpos($n, 'incubator') !== false) && !in_array('incubator', $seen_keys)) $lab_code = 'incubator';
                    elseif (strpos($n, 'mac') !== false && !in_array('mac', $seen_keys)) $lab_code = 'mac';
                    else {
                        $lab_code = preg_replace('/[^a-z0-9]/', '', $c_code);
                        if (empty($lab_code)) $lab_code = 'room_' . $r->id;
                    }

                    if (!empty($lab_code) && !in_array($lab_code, $seen_keys)) {
                        $seen_keys[] = $lab_code;
                        $r->mapped_key = $lab_code;
                        $all_rooms[] = $r;
                    }
                }
            }
        }

        // Sort: 6 Lab Utama di depan, lalu diikuti seluruh ruangan lainnya
        usort($all_rooms, function($a, $b) use ($featured_keys) {
            $posA = array_search($a->mapped_key, $featured_keys);
            $posB = array_search($b->mapped_key, $featured_keys);
            if ($posA !== false && $posB !== false) return $posA - $posB;
            if ($posA !== false) return -1;
            if ($posB !== false) return 1;
            return $a->id - $b->id;
        });

        $total_rooms_count = count($all_rooms);

        // HIERARKI DISTRIBUSI SECTION / TAB
        // Sayap Kiri: Overview & Fasilitas (1 kesatuan utuh)
        // Sayap Kanan: Prestasi & Custom Slide lainnya
        $total_slides_count = !empty($header_slides) && count($header_slides) >= 3 ? count($header_slides) : 3;

        $tabs_left = [
            ['type' => 'overview', 'index' => 0, 'id' => 'dotOverview', 'label' => 'Overview'],
            ['type' => 'fasilitas_full', 'index' => 1, 'id' => 'dotFasilitas', 'label' => 'Fasilitas', 'rooms' => $all_rooms, 'has_play' => true, 'has_add' => true]
        ];

        $tabs_right = [
            ['type' => 'prestasi', 'index' => 2, 'id' => 'dotPrestasi', 'label' => 'Prestasi']
        ];

        for ($i = 3; $i < $total_slides_count; $i++) {
            $slide_label = $header_slides[$i]->label ?? ('Slide ' . ($i + 1));
            $tabs_right[] = ['type' => 'custom_slide', 'index' => $i, 'id' => 'dotSlide' . $i, 'label' => $slide_label];
        }
    ?>
    <div class="carousel-indicators" id="carouselDots">
        <!-- SISI KIRI (Sayap Kiri) -->
        <div class="dots-half dots-left">
            <?php foreach ($tabs_left as $idx => $tab): ?>
                <?php if ($tab['type'] === 'fasilitas_full'): ?>
                    <div class="dot dot-fasilitas <?= ($tab['index'] === 0 && $idx === 0) ? 'active' : '' ?>" data-index="<?= $tab['index'] ?>" id="<?= $tab['id'] ?>">
                        <div class="dot-label-row">
                            <span class="dot-label"><?= htmlspecialchars($tab['label']) ?></span>
                            <div class="fasilitas-controls-group">
                                <span class="fasilitas-counter" id="fasilitasCounterFull">01/<?= sprintf('%02d', count($tab['rooms'] ?? [])) ?></span>
                                <button class="lab-play-pause-btn-side" id="labAutoPlayBtn" title="Auto Play / Pause">
                                    <svg id="playPauseIcon" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </button>
                                <?php if ($this->session->userdata('role_id') == 1): ?>
                                    <a href="<?= base_url('kelolaruangan') ?>" class="lab-add-room-btn" title="Kelola Ruangan">
                                        <svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="dot-track-continuous" id="labIndicatorsFull">
                            <div class="fasilitas-scrub-tooltip" id="tooltipFasilitasFull"><span>Lab Multimedia</span></div>
                            <div class="thumb" id="thumbFasilitasFull">
                                <div class="progress"></div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="dot <?= ($tab['index'] === 0 && $idx === 0) ? 'active' : '' ?>" data-index="<?= $tab['index'] ?>" id="<?= $tab['id'] ?>">
                        <span class="dot-label"><?= htmlspecialchars($tab['label']) ?></span>
                        <div class="dot-track"><div class="progress"></div></div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- CELAH TENGAH: Tempat Tombol Bulat Oranye Scroll Down Bebas Terbuka -->
        <div class="dots-center-gap"></div>

        <!-- SISI KANAN (Sayap Kanan) — Scrollable jika > 2 tabs -->
        <div class="dots-half dots-right" id="dotsRightPanel">
            <!-- Tombol Prev (muncul jika overflow) -->
            <button class="dots-nav-btn btn-prev" id="dotsNavPrev" onclick="scrollDotsRight(-1)" title="Sebelumnya">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            </button>

            <div class="dots-right-inner" id="dotsRightInner">
                <?php foreach ($tabs_right as $idx => $tab): ?>
                    <div class="dot" data-index="<?= $tab['index'] ?>" id="<?= $tab['id'] ?>">
                        <span class="dot-label"><?= htmlspecialchars($tab['label']) ?></span>
                        <div class="dot-track"><div class="progress"></div></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Tombol Next (muncul jika overflow) -->
            <button class="dots-nav-btn btn-next" id="dotsNavNext" onclick="scrollDotsRight(1)" title="Berikutnya">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>
    </div>
</div>

<script>
    // Script Logika Carousel dan Sinkronisasi Indikator Header
    document.addEventListener('DOMContentLoaded', () => {
        const carousel = document.getElementById('headerCarousel');
        const slides = document.querySelectorAll('.carousel-slide');
        const dots = document.querySelectorAll('#carouselDots .dot');
        let currentIndex = 0;
        let activeProgEndListener = null;

        const updateDots = (index) => {
            dots.forEach((dot) => {
                const dotIdx = parseInt(dot.getAttribute('data-index') || '0');
                const fasPart = dot.getAttribute('data-fasilitas-part');
                
                // Active state
                if (dotIdx === index) {
                    if (dotIdx === 1) {
                        // Diatur oleh syncIndicators pada Lab sequence
                    } else {
                        dot.classList.add('active');
                    }
                } else {
                    dot.classList.remove('active');
                }

                // Normal Dot Progress Bar (Overview & Prestasi / Custom)
                const prog = dot.querySelector('.dot-track > .progress');
                if (prog) {
                    prog.style.animation = 'none';
                    prog.offsetHeight;
                    if (dotIdx < index) {
                        prog.style.width = '100%';
                    } else {
                        prog.style.width = '0%';
                    }
                }
            });

            // 3D Logo logic: tampil di Slide 0 dan Slide Terakhir
            const modelContainer = document.getElementById('global-model-container');
            const dashboardContainer = document.querySelector('.dashboard-container');
            if (modelContainer) {
                const currentScrollTop = dashboardContainer ? dashboardContainer.scrollTop : 0;
                const vh = window.innerHeight || 800;
                if (currentScrollTop < vh * 0.45) {
                    modelContainer.style.opacity = (index === 0 || index === (slides.length - 1)) ? '1' : '0';
                }
                modelContainer.style.pointerEvents = 'none';
            }

            if (index === 0) { // --- SLIDE 0: OVERVIEW ---
                const dotOverview = document.getElementById('dotOverview');
                if (dotOverview) dotOverview.classList.add('active');

                // Reset Fasilitas continuous thumbs
                document.querySelectorAll('.dot-track-continuous .thumb').forEach(thumb => {
                    thumb.style.left = '0%';
                    thumb.style.opacity = '0.35';
                    const p = thumb.querySelector('.progress');
                    if (p) { p.style.animation = 'none'; p.style.width = '0%'; }
                });

                if (typeof pauseAutoPlay === 'function') pauseAutoPlay();

                const overviewProg = dotOverview ? dotOverview.querySelector('.dot-track > .progress') : null;
                if (overviewProg) {
                    if (activeProgEndListener) {
                        overviewProg.removeEventListener('animationend', activeProgEndListener);
                    }
                    void overviewProg.offsetWidth;
                    overviewProg.style.animation = 'slideProgress 6.5s linear forwards';
                    overviewProg.style.animationPlayState = 'running';
                    
                    activeProgEndListener = () => {
                        overviewProg.removeEventListener('animationend', activeProgEndListener);
                        activeProgEndListener = null;
                        if (currentIndex === 0) {
                            goToSlide(1);
                        }
                    };
                    overviewProg.addEventListener('animationend', activeProgEndListener);
                }

            } else if (index === 1) { // --- SLIDE 1: FASILITAS (LABS) ---
                const dotOverview = document.getElementById('dotOverview');
                const overviewProg = dotOverview ? dotOverview.querySelector('.dot-track > .progress') : null;
                if (overviewProg) {
                    overviewProg.style.animation = 'none';
                    overviewProg.style.width = '100%';
                }

                if (typeof window.startLabSequence === 'function') {
                    window.startLabSequence();
                } else if (typeof startAutoPlay === 'function') {
                    startAutoPlay();
                }

            } else { // --- SLIDE LAINNYA (PRESTASI / CUSTOM) ---
                const dotOverview = document.getElementById('dotOverview');
                const overviewProg = dotOverview ? dotOverview.querySelector('.dot-track > .progress') : null;
                if (overviewProg) {
                    overviewProg.style.animation = 'none';
                    overviewProg.style.width = '100%';
                }

                // Completed state for Fasilitas continuous thumbs
                document.querySelectorAll('.dot-track-continuous .thumb').forEach(thumb => {
                    thumb.style.opacity = '0.35';
                    const p = thumb.querySelector('.progress');
                    if (p) { p.style.animation = 'none'; p.style.width = '100%'; }
                });

                if (typeof pauseAutoPlay === 'function') pauseAutoPlay();

                const curDot = document.querySelector(`#carouselDots .dot[data-index="${index}"]`);
                if (curDot) curDot.classList.add('active');
                const curProg = curDot ? curDot.querySelector('.dot-track > .progress') : null;
                if (curProg) {
                    if (activeProgEndListener) {
                        curProg.removeEventListener('animationend', activeProgEndListener);
                    }
                    void curProg.offsetWidth;
                    curProg.style.animation = 'slideProgress 6.5s linear forwards';
                    curProg.style.animationPlayState = 'running';
                    
                    activeProgEndListener = () => {
                        curProg.removeEventListener('animationend', activeProgEndListener);
                        activeProgEndListener = null;
                        if (currentIndex === index) {
                            const nextIndex = (currentIndex + 1) % slides.length;
                            goToSlide(nextIndex);
                        }
                    };
                    curProg.addEventListener('animationend', activeProgEndListener);
                }
            }
        };

        const goToSlide = (index) => {
            if (index < 0 || index >= slides.length) return;
            currentIndex = index;
            carousel.scrollTo({
                left: index * carousel.clientWidth,
                behavior: 'smooth'
            });
            updateDots(currentIndex);
        };

        window.goToSlide = goToSlide;

        goToSlide(0);

        // Click on dots
        dots.forEach((dot) => {
            dot.addEventListener('click', (e) => {
                if (e.target.closest('#labAutoPlayBtn') || e.target.closest('.lab-add-room-btn') || e.target.closest('.dot-track-continuous')) return;

                const slideIndex = parseInt(dot.getAttribute('data-index') || '0');
                goToSlide(slideIndex);
            });
        });

        // Scroll listener synchronization
        carousel.addEventListener('scroll', () => {
            const slideWidth = carousel.clientWidth;
            const newIndex = Math.round(carousel.scrollLeft / slideWidth);
            if (newIndex !== currentIndex && newIndex >= 0 && newIndex < slides.length) {
                currentIndex = newIndex;
                updateDots(currentIndex);
            }
        });
    });
</script>

<script>
    // ===== DOTS RIGHT: SCROLL OVERFLOW NAVIGATION =====
    (function() {
        document.addEventListener('DOMContentLoaded', () => {
            const panel     = document.getElementById('dotsRightPanel');
            const inner     = document.getElementById('dotsRightInner');
            const btnPrev   = document.getElementById('dotsNavPrev');
            const btnNext   = document.getElementById('dotsNavNext');

            if (!panel || !inner) return;

            let currentOffset = 0;

            function getDotWidth() {
                const firstDot = inner.querySelector('.dot');
                if (!firstDot) return 0;
                // width of dot + gap (12px)
                return firstDot.offsetWidth + 12;
            }

            function getVisibleCount() {
                const dw = getDotWidth();
                if (dw <= 0) return 99;
                return Math.floor(panel.offsetWidth / dw);
            }

            function getTotalDots() {
                return inner.querySelectorAll('.dot').length;
            }

            function updateNav() {
                const total   = getTotalDots();
                const visible = getVisibleCount();
                const needsScroll = total > visible;

                // Clamp offset
                const maxOffset = Math.max(0, total - visible);
                if (currentOffset > maxOffset) currentOffset = maxOffset;

                // Translate inner
                inner.style.transform = `translateX(-${currentOffset * getDotWidth()}px)`;

                // Show/hide buttons
                btnPrev.classList.toggle('visible', needsScroll && currentOffset > 0);
                btnNext.classList.toggle('visible', needsScroll && currentOffset < maxOffset);
            }

            window.scrollDotsRight = function(dir) {
                const visible  = getVisibleCount();
                const total    = getTotalDots();
                const maxOffset = Math.max(0, total - visible);
                currentOffset = Math.max(0, Math.min(currentOffset + dir, maxOffset));
                updateNav();
            };

            // Also expose so goToSlide can scroll to active dot
            window._syncDotsRightOffset = function(activeTabIndex) {
                // Find the position of this dot in the inner list
                const dots = Array.from(inner.querySelectorAll('.dot'));
                const pos = dots.findIndex(d => parseInt(d.dataset.index) === activeTabIndex);
                if (pos === -1) return;
                const visible = getVisibleCount();
                if (pos >= currentOffset + visible) {
                    currentOffset = pos - visible + 1;
                } else if (pos < currentOffset) {
                    currentOffset = pos;
                }
                updateNav();
            };

            updateNav();
            window.addEventListener('resize', updateNav);
        });
    })();
</script>
