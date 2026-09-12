<style>
    #section-carousel {
        position: relative;
        background-color: #0f172a;
    }

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
    .carousel-slide.slide-1 {
        background-image: url('<?= base_url("assets/images/Fakultas.jpg") ?>');
    }
    .carousel-slide.slide-2 {
        display: block !important;
        position: relative;
        width: 100vw;
        height: 100%;
        overflow: hidden;
        background: #0f172a !important;
        background-image: none !important;
    }

    /* ============ MODAL ============ */
    .read-more-modal {
        position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(8px);
        z-index: 950;                    /* di bawah sidebar (2.147.483.646) */
        display: flex; align-items: center; justify-content: center;
        opacity: 0; pointer-events: none;
        transition: opacity 0.3s ease;
    }
    .read-more-modal.active { opacity: 1; pointer-events: auto; }
    .read-more-modal-content {
        background: rgba(255, 255, 255, 0.97);
        border: 1px solid rgba(234, 88, 12, 0.3);
        border-radius: 20px;
        max-width: 650px; width: 90%; max-height: 82vh;
        overflow: hidden;
        display: flex; flex-direction: column;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        position: relative;
        transform: translateY(20px);
        transition: transform 0.3s ease;
        font-family: 'Inter', sans-serif;
    }
    .read-more-modal.active .read-more-modal-content { transform: translateY(0); }
    .read-more-modal-header {
        padding: 28px 40px 16px 40px;
        flex-shrink: 0;
        position: relative;
        border-bottom: 1px solid #f1f5f9;
    }
    .read-more-modal-body {
        flex: 1;
        overflow-y: auto;
        padding: 20px 40px 32px 40px;
        scrollbar-width: thin;
        scrollbar-color: #ea580c #f1f5f9;
    }
    .read-more-modal-body::-webkit-scrollbar { width: 5px; }
    .read-more-modal-body::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 3px; }
    .read-more-modal-body::-webkit-scrollbar-thumb { background: #ea580c; border-radius: 3px; }
    .read-more-close {
        position: absolute; top: 16px; right: 20px;
        width: 36px; height: 36px; border-radius: 50%;
        background: #f1f5f9; border: none; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: background 0.3s;
    }
    .read-more-close:hover { background: #e2e8f0; }
    .read-more-close svg { width: 20px; height: 20px; stroke: #475569; stroke-width: 2; fill: none; }
    #readMoreTitle { font-size: 1.6rem; font-weight: 800; color: #1e293b; margin: 0; line-height: 1.3; padding-right: 40px; }
    #readMoreDesc { font-size: 1.05rem; color: #334155; line-height: 1.8; text-align: justify; white-space: pre-line; }

    .multi-bg-fade { transition: background-image 1s ease-in-out; }

    .dashboard-header { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
    .background-video {
        position: absolute; top: 0; left: 0;
        width: 100%; height: 100%;
        object-fit: cover; z-index: -1;
    }

    .scroll-hint {
        position: absolute; bottom: 30px; left: 50%;
        transform: translateX(-50%);
        color: #fff; font-size: 0.9rem;
        letter-spacing: 2px; text-transform: uppercase;
        animation: bounce 2s infinite;
        z-index: 20;
        text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        font-weight: 700;
    }

    /* ============ CAROUSEL INDICATORS ============ */
    .carousel-indicators {
        position: absolute;
        bottom: 38px; left: 100px; right: 80px;
        z-index: 30;
        pointer-events: none;
    }
    .carousel-indicators-track {
        width: 100%; height: 100%;
        display: flex; gap: 12px; align-items: center;
        overflow-x: auto;
        scroll-behavior: smooth;
        scrollbar-width: none;
        -ms-overflow-style: none;
        pointer-events: auto;
        transition: padding 0.2s ease;
    }
    .carousel-indicators-track::-webkit-scrollbar { display: none; }
    .carousel-indicators-track.has-prev { padding-left: 30px; }
    .carousel-indicators-track.has-next { padding-right: 30px; }

    .carousel-indicators .dot {
        flex: 1 1 0;
        min-width: max(140px, min-content);
        display: flex; flex-direction: column; gap: 8px;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.3s ease, background 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease;
        background: rgba(0, 0, 0, 0.45);
        padding: 9px 12px;
        border-radius: 12px;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        pointer-events: auto;
        scroll-snap-align: start;
        flex-shrink: 0;
    }
    .carousel-indicators .dot.dot-fasilitas { min-width: max(170px, min-content); }

    .carousel-indicators .dot.active,
    .carousel-indicators .dot:hover {
        opacity: 1;
        background: rgba(0, 0, 0, 0.75);
        border-color: rgba(234, 88, 12, 0.7);
        box-shadow: 0 4px 15px rgba(0,0,0,0.4);
        transform: translateY(-2px);
    }
    .carousel-indicators .dot .dot-label {
        font-size: 0.82rem; font-weight: 800;
        text-transform: uppercase; color: #fff;
        letter-spacing: 1.2px; height: 18px;
        display: flex; align-items: center;
        white-space: nowrap;
    }
    .carousel-indicators .dot .dot-label::before {
        content: ''; display: inline-block;
        width: 0; height: 8px;
        background-color: #ea580c; border-radius: 50%;
        margin-right: 0; flex-shrink: 0;
        opacity: 0;
        transition: width 0.25s ease, margin-right 0.25s ease, opacity 0.25s ease;
    }
    .carousel-indicators .dot.active .dot-label::before {
        width: 8px; margin-right: 8px; opacity: 1;
    }
    .carousel-indicators .dot .dot-track {
        height: 4px; width: 100%; border-radius: 4px;
        background: rgba(255, 255, 255, 0.3);
        position: relative; overflow: hidden;
    }
    .carousel-indicators .dot .progress {
        position: absolute; top: 0; left: 0; height: 100%;
        background: #fff; width: 0%; border-radius: 4px;
        box-shadow: 0 0 8px #fff, 0 0 15px rgba(255,255,255,0.8);
    }

    .dot-fasilitas { position: relative; }
    .dot-label-row {
        display: flex; align-items: center; justify-content: space-between;
        width: 100%; height: 18px;
    }
    .fasilitas-controls-group { display: flex; align-items: center; gap: 6px; }
    .lab-add-room-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 18px; height: 18px; border-radius: 50%;
        background: #ea580c; border: none; color: #ffffff;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(234, 88, 12, 0.5);
        flex-shrink: 0; text-decoration: none; padding: 0;
    }
    .lab-add-room-btn:hover {
        background: #ffffff; color: #ea580c;
        transform: scale(1.15);
        box-shadow: 0 4px 10px rgba(255, 255, 255, 0.6);
    }
    .lab-add-room-btn svg { width: 12px; height: 12px; fill: currentColor; }
    .fasilitas-counter {
        font-size: 0.68rem; font-weight: 800;
        color: rgba(255, 255, 255, 0.85);
        background: rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.18);
        padding: 1px 6px; border-radius: 10px;
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
        height: 4px; width: 100%;
        background: rgba(255, 255, 255, 0.25);
        border-radius: 4px;
        position: relative; cursor: pointer;
        transition: height 0.2s cubic-bezier(0.25, 1, 0.5, 1), background 0.2s ease, box-shadow 0.2s ease;
        touch-action: none;
    }
    .dot-track-continuous::before {
        content: ''; position: absolute;
        top: -8px; bottom: -8px; left: 0; right: 0;
    }
    .dot-track-continuous:hover,
    .dot-track-continuous.is-dragging {
        height: 6px; background: rgba(255, 255, 255, 0.38);
    }
    .dot-track-continuous .thumb {
        position: absolute; top: 0; left: 0;
        height: 100%; width: 25%;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 4px; overflow: hidden;
        transition: left 0.4s cubic-bezier(0.25, 1, 0.5, 1), width 0.3s ease, opacity 0.3s ease;
        box-shadow: 0 0 8px rgba(255, 255, 255, 0.6), 0 0 12px rgba(234, 88, 12, 0.35);
    }
    .dot-track-continuous.is-dragging .thumb {
        transition: width 0.3s ease, opacity 0.3s ease;
    }
    .dot-track-continuous .thumb .progress {
        position: absolute; top: 0; left: 0;
        height: 100%; width: 0%;
        background: #ffffff; border-radius: 4px;
        box-shadow: 0 0 8px #ffffff, 0 0 14px rgba(255, 255, 255, 0.9);
    }
    .fasilitas-scrub-tooltip {
        position: absolute; bottom: calc(100% + 9px); left: 0;
        transform: translateX(-50%) translateY(4px);
        pointer-events: none; opacity: 0; visibility: hidden;
        transition: opacity 0.15s ease, transform 0.15s cubic-bezier(0.25, 1, 0.5, 1);
        z-index: 100; white-space: nowrap;
        font-size: 0.72rem; font-weight: 700;
        color: #ffffff;
        background: rgba(15, 23, 42, 0.92);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.22);
        padding: 3px 9px; border-radius: 7px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6), 0 0 12px rgba(234, 88, 12, 0.35);
        display: flex; align-items: center; gap: 6px;
    }
    .fasilitas-scrub-tooltip::after {
        content: ''; position: absolute;
        top: 100%; left: 50%;
        transform: translateX(-50%);
        border-width: 4px; border-style: solid;
        border-color: rgba(15, 23, 42, 0.92) transparent transparent transparent;
    }
    .fasilitas-scrub-tooltip.visible {
        opacity: 1; visibility: visible;
        transform: translateX(-50%) translateY(0);
    }
    .lab-play-pause-btn-side {
        width: 18px; height: 18px; min-width: 18px;
        border-radius: 50%;
        background: #ea580c; border: none; color: #ffffff;
        display: inline-flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(234, 88, 12, 0.5);
    }
    .lab-play-pause-btn-side:hover {
        background: #ffffff; color: #ea580c; transform: scale(1.2);
    }
    .lab-play-pause-btn-side svg { width: 9px; height: 9px; fill: currentColor; }

    /* ============ SLIDE 1 LAYOUT ============ */
    /* [CENTER FIX] - padding atas/bawah mengakomodasi topbar (70px) & indicators
       (bottom:38px + tinggi ~60px). align-items:center -> card CENTER VERTIKAL. */
    .slide1-layout {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        padding: 90px 80px 140px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        z-index: 10;
        pointer-events: none;
        box-sizing: border-box;
    }
    .slide1-text-container {
        display: flex;
        flex-direction: column;
        gap: 0;
        width: 500px;
        max-width: 90vw;
        z-index: 10;
        margin-left: 20px;
        /* [CENTER FIX] margin-top:-40px DIHAPUS agar card benar-benar center */
    }

    .slide1-card {
        background: rgba(255, 255, 255, 0.95);
        padding: 22px 28px;
        border-radius: 14px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        backdrop-filter: blur(8px);
        pointer-events: auto;
        width: 100%;
        box-sizing: border-box;
        position: relative;
    }
    .slide1-card-title {
        color: #ea580c;
        font-size: 2.1rem;
        font-weight: 800;
        margin: 0 0 14px 0;
    }
    .slide1-card-desc {
        color: #334155;
        font-size: 0.85rem;
        line-height: 1.6;
        text-align: justify;
    }

    .dekanat-img-right {
        position: absolute;
        bottom: 0; right: 20px;
        max-height: 380px;
        z-index: 20;
        pointer-events: none;
    }

    .read-more-container {
        display: flex; justify-content: flex-end;
        margin-top: 10px;
    }
    .read-more-btn {
        display: flex; align-items: center; gap: 8px;
        pointer-events: auto;
        padding: 7px 18px;
        background: transparent;
        color: #ea580c;
        border: 2px solid #ea580c;
        border-radius: 10px;
        font-weight: 800; font-size: 0.82rem;
        cursor: pointer; text-decoration: none;
        letter-spacing: 0.5px;
        transition: all 0.25s ease;
    }
    .read-more-btn:hover {
        background: #ea580c;
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(234,88,12,0.45);
        color: #fff;
    }
    .read-more-btn svg { width: 14px; height: 14px; flex-shrink: 0; }

    .dots-nav-btn {
        position: absolute; top: 50%;
        transform: translateY(-50%);
        width: 24px; height: 24px; border-radius: 50%;
        background: rgba(234,88,12,0.85);
        border: none; color: #fff;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        z-index: 10;
        opacity: 0; pointer-events: none;
        transition: opacity 0.2s ease, transform 0.2s ease, background 0.2s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        flex-shrink: 0;
    }
    .dots-nav-btn.btn-prev { left: 0; }
    .dots-nav-btn.btn-next { right: 0; }
    .dots-nav-btn.visible { opacity: 1; pointer-events: auto; }
    .dots-nav-btn:hover { background: #ea580c; transform: translateY(-50%) scale(1.12); }
    .dots-nav-btn svg { width: 12px; height: 12px; }

    /* ============ RESPONSIVE MOBILE ============ */
    /* [CENTER FIX] & [SAFE PADDING]:
       - top:0 (bukan 70px) + padding-top: 90px  -> ruang utk topbar
       - padding-bottom: 130px                    -> ruang utk indicators di bawah
       - align-items: center                      -> card CENTER VERTIKAL
       - box-sizing: border-box                   -> padding tidak menambah lebar
    */
    @media (max-width: 900px) {
        .slide1-layout {
            top: 0;
            padding: 90px 20px 130px;
            align-items: center;
            justify-content: center;
        }
        .slide1-text-container {
            margin-top: 0;
            width: 100%;
            max-width: 100%;
            margin-left: 0;
        }
        .slide1-card { padding: 16px 18px; border-radius: 12px; }
        .slide1-card-title { font-size: 1.4rem; margin-bottom: 10px; }
        .slide1-card-desc { font-size: 0.78rem; line-height: 1.55; }
        .read-more-btn { padding: 6px 14px; font-size: 0.75rem; }
        .dekanat-img-right { max-height: 220px; opacity: 1; right: 10px; }

        .scroll-hint { display: none; }

        .carousel-indicators {
            left: 12px; right: 12px; bottom: 16px;
        }
        .carousel-indicators-track { gap: 8px; }
        .carousel-indicators-track.has-prev { padding-left: 26px; }
        .carousel-indicators-track.has-next { padding-right: 26px; }

        .carousel-indicators .dot {
            min-width: max(96px, min-content);
            padding: 7px 9px;
            border-radius: 9px;
            gap: 5px;
        }
        .carousel-indicators .dot.dot-fasilitas { min-width: max(120px, min-content); }
        .carousel-indicators .dot .dot-label {
            font-size: 0.65rem; letter-spacing: 0.4px; height: 14px;
        }
        .carousel-indicators .dot .dot-label::before { height: 6px; }
        .carousel-indicators .dot.active .dot-label::before { width: 6px; margin-right: 5px; }

        .fasilitas-controls-group { gap: 4px; }
        .fasilitas-counter { font-size: 0.58rem; padding: 1px 4px; }
        .lab-play-pause-btn-side { width: 15px; height: 15px; min-width: 15px; }
        .lab-play-pause-btn-side svg { width: 7px; height: 7px; }

        .dot .dot-track,
        .dot-track-continuous { height: 3px; }

        .dots-nav-btn { width: 20px; height: 20px; }
        .dots-nav-btn svg { width: 10px; height: 10px; }

        .read-more-modal-content { border-radius: 16px; max-height: 85vh; }
        .read-more-modal-header { padding: 20px 22px 12px 22px; }
        .read-more-modal-body { padding: 14px 22px 24px 22px; }
        #readMoreTitle { font-size: 1.25rem; padding-right: 34px; }
        #readMoreDesc { font-size: 0.92rem; line-height: 1.7; }
    }

    @media (max-width: 480px) {
        .slide1-layout { padding: 85px 14px 120px; }
        .slide1-card { padding: 13px 15px; }
        .slide1-card-title { font-size: 1.15rem; margin-bottom: 8px; }
        .slide1-card-desc { font-size: 0.72rem; }
        .read-more-btn { padding: 5px 11px; font-size: 0.7rem; }
        .dekanat-img-right { max-height: 150px; opacity: 1; }

        .carousel-indicators { left: 8px; right: 8px; bottom: 10px; }
        .carousel-indicators-track { gap: 6px; }

        .carousel-indicators .dot {
            min-width: max(78px, min-content);
            padding: 6px 7px;
            border-radius: 8px;
        }
        .carousel-indicators .dot.dot-fasilitas { min-width: max(98px, min-content); }
        .carousel-indicators .dot .dot-label { font-size: 0.56rem; letter-spacing: 0.1px; }
        .fasilitas-counter { font-size: 0.5rem; padding: 0 3px; }
        .lab-play-pause-btn-side { display: none; }

        .dot .dot-track,
        .dot-track-continuous { height: 2px; }

        #readMoreTitle { font-size: 1.05rem; }
        #readMoreDesc { font-size: 0.85rem; }
    }
    .nav-link, .nav-link-login {
    width: 100%;
    justify-content: space-between;
    padding: 14px 6px;
    font-size: 0.85rem;
}
.nav-link-login {
    justify-content: center;
    gap: 10px;
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

<!-- ============ Sesi 1: Carousel ============ -->
<div class="section-wrapper" id="section-carousel">
    <div class="carousel-container" id="headerCarousel">
        <!-- Slide 1 -->
        <div class="carousel-slide slide-1">
            <div class="slide1-layout">
                <div class="slide1-text-container">
                    <div class="slide1-card" id="headerDescBox">
                        <h1 class="slide1-card-title"><?= htmlspecialchars($header_settings->title ?? 'Fakultas Industri Kreatif') ?></h1>
                        <div class="slide1-card-desc">
                            <?php
                                $default_desc = 'Seiring dengan berkembangnya kebutuhan pelayanan untuk mahasiswa, dosen dan pegawai FIK maka diperlukan peningkatan layanan yang mengusung efisiensi dan efektifitas. Ifik lahir dari keresahan dan kesulitan mahasiswa maupun dosen dalam beberapa layanan, antara lain pendaftaran TA, bimbingan online, dokumen online, peminjaman ruangan dan lain sebagainya. Sejak dibuat tahun 2021 oleh tim unit lab FIK, aplikasi berbasis web ini telah digunakan hingga saat ini untuk mempermudah layanan untuk kalangan internal FIK, baik untuk mahasiswa, dosen maupun pegawai FIK.';
                                $full_desc = (!empty($header_settings->description)) ? $header_settings->description : $default_desc;
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
                            <?php
                                $modalTitle1 = (!empty($header_settings->title)) ? $header_settings->title : 'Fakultas Industri Kreatif';
                                $modalDesc1 = $full_desc;
                            ?>
                            <div class="read-more-container">
                                <button class="read-more-btn"
                                    data-modal-title="<?= htmlspecialchars($modalTitle1, ENT_QUOTES) ?>"
                                    data-modal-desc="<?= htmlspecialchars($modalDesc1, ENT_QUOTES) ?>"
                                    onclick="openReadMoreModal(this)">
                                    Baca Selengkapnya
                                    <svg fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php $dekanat_img = $header_settings->dekanat_image ?? 'dekanat2.png'; ?>
            <img src="<?= base_url('assets/images/' . $dekanat_img) ?>" alt="Dekanat" class="dekanat-img-right">
        </div>

        <!-- Slide 2 -->
        <div class="carousel-slide slide-2" style="position: relative; width: 100vw; height: 100%;">
            <?php $this->load->view('dashboard/sections/lab'); ?>
        </div>

        <!-- Slide 3 -->
        <div class="carousel-slide slide-3">
            <div class="slide1-layout">
                <div class="slide1-text-container">
                    <div class="slide1-card">
                        <h1 class="slide1-card-title">Prestasi &amp; Inovasi FIK</h1>
                        <div class="slide1-card-desc">
                            Fakultas Industri Kreatif secara konsisten mengukir berbagai prestasi baik di tingkat nasional maupun internasional. Melalui fasilitas laboratorium yang canggih dan bimbingan dosen berpengalaman, mahasiswa FIK terus melahirkan karya-karya inovatif di bidang desain, seni, media interaktif, dan teknologi kreatif.
                        </div>
                    </div>
                </div>
            </div>
            <img src="<?= base_url('assets/images/' . $dekanat_img) ?>" alt="Dekanat" class="dekanat-img-right">
        </div>

        <!-- Custom Slides -->
        <?php if (!empty($header_slides) && count($header_slides) > 3): ?>
            <?php for ($i = 3; $i < count($header_slides); $i++): ?>
                <?php
                    $s = $header_slides[$i];
                    $media_json = json_decode($s->media_path, true);
                    $is_multi = (is_array($media_json) && isset($media_json[0]['file']));
                    $first_image = $is_multi ? $media_json[0]['file'] : $s->media_path;
                    $multi_data = $is_multi ? htmlspecialchars(json_encode($media_json)) : '[]';
                ?>
                <div class="carousel-slide slide-custom slide-<?= $i + 1 ?>" id="customSlide_<?= $i ?>" data-multi="<?= $multi_data ?>" style="position: relative; width: 100vw; height: 100%; <?= ($s->media_type === 'image' || $s->media_type === 'multi') && !empty($first_image) ? 'background-image: url(' . base_url('assets/images/' . $first_image) . '); background-size: cover; background-position: center;' : '' ?>">
                    <?php if ($s->media_type === 'video' && !empty($s->media_path) && !$is_multi): ?>
                        <video autoplay muted loop playsinline style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: -1;">
                            <source src="<?= base_url('assets/vids/' . $s->media_path) ?>" type="video/mp4">
                        </video>
                    <?php endif; ?>
                    <div class="slide1-layout">
                        <div class="slide1-text-container">
                            <?php
                                $used_title = !empty($s->overlay_title) ? $s->overlay_title : ($header_settings->title ?? 'Fakultas Industri Kreatif');
                                $used_desc = !empty($s->overlay_description) ? $s->overlay_description : ($header_settings->description ?? '');
                            ?>
                            <div class="slide1-card">
                                <h1 class="slide1-card-title"><?= htmlspecialchars($used_title) ?></h1>
                                <div class="slide1-card-desc">
                                    <?php
                                        $def_plain = strip_tags($used_desc);
                                        $def_limit = 280;
                                        if (mb_strlen($def_plain) > $def_limit) {
                                            $def_cut   = mb_substr($def_plain, 0, $def_limit);
                                            $def_space = mb_strrpos($def_cut, ' ');
                                            echo htmlspecialchars($def_space ? mb_substr($def_cut, 0, $def_space) : $def_cut) . '...';
                                        } else {
                                            echo htmlspecialchars($def_plain);
                                        }
                                    ?>
                                    <?php if (mb_strlen($def_plain) > 280): ?>
                                    <div class="read-more-container">
                                        <button class="read-more-btn"
                                            data-modal-title="<?= htmlspecialchars($used_title, ENT_QUOTES) ?>"
                                            data-modal-desc="<?= htmlspecialchars($used_desc, ENT_QUOTES) ?>"
                                            onclick="openReadMoreModal(this)">
                                            Baca Selengkapnya
                                            <svg fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                        </button>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <img src="<?= base_url('assets/images/' . $dekanat_img) ?>" alt="Dekanat" class="dekanat-img-right">
                </div>
            <?php endfor; ?>
        <?php endif; ?>
    </div>

    <!-- Indicators -->
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
        usort($all_rooms, function($a, $b) use ($featured_keys) {
            $posA = array_search($a->mapped_key, $featured_keys);
            $posB = array_search($b->mapped_key, $featured_keys);
            if ($posA !== false && $posB !== false) return $posA - $posB;
            if ($posA !== false) return -1;
            if ($posB !== false) return 1;
            return $a->id - $b->id;
        });
        $total_slides_count = !empty($header_slides) && count($header_slides) >= 3 ? count($header_slides) : 3;
        $tabs_all = [
            ['type' => 'overview', 'index' => 0, 'id' => 'dotOverview', 'label' => 'Overview'],
            ['type' => 'fasilitas_full', 'index' => 1, 'id' => 'dotFasilitas', 'label' => 'Fasilitas', 'rooms' => $all_rooms, 'has_play' => true, 'has_add' => true],
            ['type' => 'prestasi', 'index' => 2, 'id' => 'dotPrestasi', 'label' => 'Prestasi']
        ];
        for ($i = 3; $i < $total_slides_count; $i++) {
            $slide_label = $header_slides[$i]->label ?? ('Slide ' . ($i + 1));
            $tabs_all[] = ['type' => 'custom_slide', 'index' => $i, 'id' => 'dotSlide' . $i, 'label' => $slide_label];
        }
    ?>
    <div class="carousel-indicators" id="carouselDots">
        <button class="dots-nav-btn btn-prev" id="dotsNavPrev" onclick="scrollDotsTrack(-1)" title="Sebelumnya">
            <svg fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
        </button>

        <div class="carousel-indicators-track" id="dotsTrack">
            <?php foreach ($tabs_all as $pos => $tab): ?>
                <?php if ($tab['type'] === 'fasilitas_full'): ?>
                    <div class="dot dot-fasilitas <?= ($tab['index'] === 0) ? 'active' : '' ?>" data-index="<?= $tab['index'] ?>" id="<?= $tab['id'] ?>">
                        <div class="dot-label-row">
                            <span class="dot-label"><?= htmlspecialchars($tab['label']) ?></span>
                            <div class="fasilitas-controls-group">
                                <span class="fasilitas-counter" id="fasilitasCounterFull">01/<?= sprintf('%02d', count($tab['rooms'] ?? [])) ?></span>
                                <button class="lab-play-pause-btn-side" id="labAutoPlayBtn" title="Auto Play / Pause">
                                    <svg id="playPauseIcon" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </button>
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
                    <div class="dot <?= ($tab['index'] === 0) ? 'active' : '' ?>" data-index="<?= $tab['index'] ?>" id="<?= $tab['id'] ?>">
                        <span class="dot-label"><?= htmlspecialchars($tab['label']) ?></span>
                        <div class="dot-track"><div class="progress"></div></div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <button class="dots-nav-btn btn-next" id="dotsNavNext" onclick="scrollDotsTrack(1)" title="Berikutnya">
            <svg fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
    </div>
</div>

<script>
    /* ============ CAROUSEL LOGIC (TIDAK ADA PERUBAHAN) ============ */
    document.addEventListener('DOMContentLoaded', () => {
        const carousel = document.getElementById('headerCarousel');
        const slides = document.querySelectorAll('.carousel-slide');
        const dots = document.querySelectorAll('#carouselDots .dot');
        let currentIndex = 0;
        let activeProgEndListener = null;

        const updateDots = (index) => {
            dots.forEach((dot) => {
                const dotIdx = parseInt(dot.getAttribute('data-index') || '0');
                if (dotIdx === index) {
                    if (dotIdx !== 1) dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
                const prog = dot.querySelector('.dot-track > .progress');
                if (prog) {
                    prog.style.animation = 'none';
                    prog.offsetHeight;
                    prog.style.width = '0%';
                }
            });

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

            if (index === 0) {
                const dotOverview = document.getElementById('dotOverview');
                if (dotOverview) dotOverview.classList.add('active');
                document.querySelectorAll('.dot-track-continuous .thumb').forEach(thumb => {
                    thumb.style.left = '0%'; thumb.style.opacity = '0.35';
                    const p = thumb.querySelector('.progress');
                    if (p) { p.style.animation = 'none'; p.style.width = '0%'; }
                });
                if (typeof pauseAutoPlay === 'function') pauseAutoPlay();
                const overviewProg = dotOverview ? dotOverview.querySelector('.dot-track > .progress') : null;
                if (overviewProg) {
                    if (activeProgEndListener) overviewProg.removeEventListener('animationend', activeProgEndListener);
                    void overviewProg.offsetWidth;
                    overviewProg.style.animation = 'slideProgress 6.5s linear forwards';
                    overviewProg.style.animationPlayState = 'running';
                    activeProgEndListener = () => {
                        overviewProg.removeEventListener('animationend', activeProgEndListener);
                        activeProgEndListener = null;
                        if (currentIndex === 0) goToSlide(1);
                    };
                    overviewProg.addEventListener('animationend', activeProgEndListener);
                }
            } else if (index === 1) {
                const dotOverview = document.getElementById('dotOverview');
                const overviewProg = dotOverview ? dotOverview.querySelector('.dot-track > .progress') : null;
                if (overviewProg) { overviewProg.style.animation = 'none'; overviewProg.style.width = '0%'; }
                if (typeof window.startLabSequence === 'function') window.startLabSequence();
                else if (typeof startAutoPlay === 'function') startAutoPlay();
            } else {
                const dotOverview = document.getElementById('dotOverview');
                const overviewProg = dotOverview ? dotOverview.querySelector('.dot-track > .progress') : null;
                if (overviewProg) { overviewProg.style.animation = 'none'; overviewProg.style.width = '0%'; }
                document.querySelectorAll('.dot-track-continuous .thumb').forEach(thumb => {
                    thumb.style.opacity = '0.35';
                    const p = thumb.querySelector('.progress');
                    if (p) { p.style.animation = 'none'; p.style.width = '0%'; }
                });
                if (typeof pauseAutoPlay === 'function') pauseAutoPlay();

                const curDot = document.querySelector(`#carouselDots .dot[data-index="${index}"]`);
                if (curDot) curDot.classList.add('active');
                const curProg = curDot ? curDot.querySelector('.dot-track > .progress') : null;

                let totalDuration = 6.5;
                const slideEl = document.querySelector(`#customSlide_${index}`);
                if (slideEl) {
                    const multiData = slideEl.getAttribute('data-multi');
                    if (multiData && multiData !== '[]') {
                        const items = JSON.parse(multiData);
                        if (items.length > 1) {
                            totalDuration = 0;
                            items.forEach(item => totalDuration += (item.duration || 3));
                            let accumulatedTime = 0;
                            slideEl.classList.add('multi-bg-fade');
                            items.forEach((item, idx) => {
                                if (idx > 0) {
                                    setTimeout(() => {
                                        const ext = item.file.split('.').pop().toLowerCase();
                                        if (['mp4','webm','ogg'].includes(ext)) {
                                            slideEl.innerHTML = `<video autoplay muted playsinline style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: -1;"><source src="<?= base_url('assets/vids/') ?>${item.file}" type="video/mp4"></video>` + slideEl.innerHTML.replace(/<video.*?<\/video>/s, '');
                                        } else {
                                            slideEl.style.backgroundImage = `url('<?= base_url('assets/images/') ?>${item.file}')`;
                                        }
                                    }, accumulatedTime * 1000);
                                } else {
                                    const ext = item.file.split('.').pop().toLowerCase();
                                    if (!['mp4','webm','ogg'].includes(ext)) {
                                        slideEl.style.backgroundImage = `url('<?= base_url('assets/images/') ?>${item.file}')`;
                                    }
                                }
                                accumulatedTime += (item.duration || 3);
                            });
                        }
                    }
                }

                if (curProg) {
                    if (activeProgEndListener) curProg.removeEventListener('animationend', activeProgEndListener);
                    void curProg.offsetWidth;
                    curProg.style.animation = `slideProgress ${totalDuration}s linear forwards`;
                    curProg.style.animationPlayState = 'running';
                    activeProgEndListener = () => {
                        curProg.removeEventListener('animationend', activeProgEndListener);
                        activeProgEndListener = null;
                        if (currentIndex === index) {
                            goToSlide((currentIndex + 1) % slides.length);
                        }
                    };
                    curProg.addEventListener('animationend', activeProgEndListener);
                }
            }
        };

        const goToSlide = (index) => {
            if (index < 0 || index >= slides.length) return;
            currentIndex = index;
            carousel.scrollTo({ left: index * carousel.clientWidth, behavior: 'smooth' });
            updateDots(currentIndex);
            if (typeof window._syncActiveDotIntoView === 'function') window._syncActiveDotIntoView(index);
        };

        window.goToSlide = goToSlide;
        goToSlide(0);

        dots.forEach((dot) => {
            dot.addEventListener('click', (e) => {
                if (e.target.closest('#labAutoPlayBtn') || e.target.closest('.dot-track-continuous')) return;
                goToSlide(parseInt(dot.getAttribute('data-index') || '0'));
            });
        });

        carousel.addEventListener('scroll', () => {
            const slideWidth = carousel.clientWidth;
            const newIndex = Math.round(carousel.scrollLeft / slideWidth);
            if (newIndex !== currentIndex && newIndex >= 0 && newIndex < slides.length) {
                currentIndex = newIndex;
                updateDots(currentIndex);
            }
        });
    });

    /* ============ PAGINATION TRACK ============ */
    (function() {
        document.addEventListener('DOMContentLoaded', () => {
            const track   = document.getElementById('dotsTrack');
            const btnPrev = document.getElementById('dotsNavPrev');
            const btnNext = document.getElementById('dotsNavNext');
            if (!track || !btnPrev || !btnNext) return;

            function updateNav() {
                const needsScroll = track.scrollWidth > track.clientWidth + 1;
                const atStart = track.scrollLeft <= 1;
                const atEnd = track.scrollLeft >= (track.scrollWidth - track.clientWidth - 1);
                const showPrev = needsScroll && !atStart;
                const showNext = needsScroll && !atEnd;
                btnPrev.classList.toggle('visible', showPrev);
                btnNext.classList.toggle('visible', showNext);
                track.classList.toggle('has-prev', showPrev);
                track.classList.toggle('has-next', showNext);
            }

            window.scrollDotsTrack = function(dir) {
                const sampleDot = track.querySelector('.dot');
                const step = (sampleDot ? sampleDot.offsetWidth : 180) + 12;
                track.scrollBy({ left: dir * step, behavior: 'smooth' });
            };

            window._syncActiveDotIntoView = function(activeTabIndex) {
                const activeDot = track.querySelector(`.dot[data-index="${activeTabIndex}"]`);
                if (activeDot) activeDot.scrollIntoView({ behavior: 'smooth', inline: 'nearest', block: 'nearest' });
            };
            window._syncDotsRightOffset = window._syncActiveDotIntoView;

            track.addEventListener('scroll', updateNav);
            window.addEventListener('resize', updateNav);
            updateNav();
        });
    })();
</script>

<!-- Modal -->
<div class="read-more-modal" id="readMoreModal" onclick="closeReadMoreModalOnBackdrop(event)">
    <div class="read-more-modal-content">
        <div class="read-more-modal-header">
            <button class="read-more-close" onclick="closeReadMoreModal()" title="Tutup">
                <svg viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
            <h2 id="readMoreTitle"></h2>
        </div>
        <div class="read-more-modal-body">
            <div id="readMoreDesc"></div>
        </div>
    </div>
</div>

<script>
    function openReadMoreModal(btnEl) {
        const title = btnEl.getAttribute('data-modal-title') || '';
        const desc  = btnEl.getAttribute('data-modal-desc')  || '';
        document.getElementById('readMoreTitle').textContent = title;
        document.getElementById('readMoreDesc').textContent  = desc;
        const body = document.querySelector('.read-more-modal-body');
        if (body) body.scrollTop = 0;
        document.getElementById('readMoreModal').classList.add('active');
    }
    function closeReadMoreModal() {
        document.getElementById('readMoreModal').classList.remove('active');
    }
    function closeReadMoreModalOnBackdrop(e) {
        if (e.target === document.getElementById('readMoreModal')) closeReadMoreModal();
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeReadMoreModal();
    });
</script>