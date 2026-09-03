<style>
    /* Styling khusus Sesi 1 */
    #section-carousel {
        position: relative;
        background-color: #0f172a;
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

    /* Khusus Slide 2 (Fasilitas Lab) membentang penuh 100% tanpa centering gap */
    .carousel-slide.slide-2 {
        display: block !important;
        position: relative;
        width: 100vw;
        height: 100%;
        overflow: hidden;
        background: #0f172a !important;
        background-image: none !important;
    }
    /* CSS untuk Pop-up / Modal "Baca Selengkapnya" */
    .read-more-modal {
        position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
        background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px);
        z-index: 9999; display: flex; align-items: center; justify-content: center;
        opacity: 0; pointer-events: none; transition: opacity 0.3s ease;
    }
    .read-more-modal.active { opacity: 1; pointer-events: auto; }
    .read-more-modal-content {
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(234, 88, 12, 0.3);
        padding: 40px; border-radius: 20px;
        max-width: 650px; width: 90%; max-height: 80vh; overflow-y: auto;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2); position: relative;
        transform: translateY(20px); transition: transform 0.3s ease;
        font-family: 'Inter', sans-serif;
    }
    .read-more-modal.active .read-more-modal-content { transform: translateY(0); }
    .read-more-close {
        position: absolute; top: 20px; right: 20px;
        width: 36px; height: 36px; border-radius: 50%;
        background: #f1f5f9; border: none; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: background 0.3s;
    }
    .read-more-close:hover { background: #e2e8f0; }
    .read-more-close svg { width: 20px; height: 20px; stroke: #475569; stroke-width: 2; fill: none; }
    #readMoreTitle { font-size: 1.6rem; font-weight: 800; color: #1e293b; margin-bottom: 20px; line-height: 1.3; }

    /* [FIX #1] Scrollable description area inside the modal, independent of the close button */
    #readMoreDesc {
        font-size: 1.05rem;
        color: #334155;
        line-height: 1.7;
        text-align: justify;
        max-height: 60vh;
        overflow-y: auto;
        padding-right: 6px;
    }

    .multi-bg-fade { transition: background-image 1s ease-in-out; }

    /* Dashboard Header Styles */
    .dashboard-header {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
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

    /* [FIX #2 & #3 v2] Carousel Indicators — SATU grup tunggal (tidak lagi terpisah kiri/kanan).
       Container ini dibatasi dari sisi kiri card deskripsi (left:100px) sampai sisi kanan yang sama
       seperti sebelumnya (right:80px). Track di dalamnya berisi SEMUA tab (Overview, Fasilitas,
       Prestasi, dan seluruh custom slide) dalam satu baris flex yang bisa tumbuh mengisi ruang
       secara merata, menyusut sampai batas minimum lebar teksnya, lalu baru pakai pagination. */
    .carousel-indicators {
        position: absolute;
        bottom: 38px;
        left: 100px;
        right: 80px;
        z-index: 30;
        pointer-events: none;
    }

    /* [FIX v3-#2] Track selalu mengisi 100% lebar container (left:100px = sisi kiri card),
       tanpa ruang dan tanpa gap tersisa. Tombol panah TIDAK lagi ikut memakan ruang flex —
       sekarang mengambang (position: absolute) di atas track, jadi dot pertama selalu mulai
       tepat sejajar dengan sisi kiri card, baik saat panah kiri muncul maupun tidak. */
    .carousel-indicators-track {
        width: 100%;
        height: 100%;
        display: flex;
        gap: 12px;
        align-items: center;
        overflow-x: auto;
        scroll-behavior: smooth;
        scrollbar-width: none;
        -ms-overflow-style: none;
        pointer-events: auto;
        transition: padding 0.2s ease;
    }
    .carousel-indicators-track::-webkit-scrollbar { display: none; }
    /* Beri sedikit ruang di dalam track hanya ketika tombol panah sedang tampil, supaya
       tombol tidak menutupi dot yang sedang terlihat sebagian */
    .carousel-indicators-track.has-prev { padding-left: 30px; }
    .carousel-indicators-track.has-next { padding-right: 30px; }

    /* [FIX v3-#1] Celah tengah (dots-center-gap) DIHAPUS — sebelumnya menyisakan jarak kosong
       di tengah track setelah semua tab digabung. Semua dot sekarang berjejer rapat tanpa jarak. */

    /* [FIX #2] Tiap dot: flex-grow merata mengisi sisa ruang (flex-basis 0), tapi TIDAK PERNAH
       lebih kecil dari lebar teks label-nya sendiri (min-content) — atau minimal 140px, mana yang
       lebih besar. Begitu total lebar minimum semua dot melebihi ruang tersedia, track otomatis
       overflow dan panah pagination di sisi kiri/kanan container akan muncul. */
    .carousel-indicators .dot {
        flex: 1 1 0;
        min-width: max(140px, min-content);
        display: flex;
        flex-direction: column;
        gap: 8px;
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

    /* Dot Fasilitas butuh sedikit ruang ekstra untuk counter + tombol play/pause */
    .carousel-indicators .dot.dot-fasilitas {
        min-width: max(170px, min-content);
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
        white-space: nowrap;
    }
    
    /* [FIX v3-#3] Bulatan oranye kecil sebelum label HANYA muncul saat dot sedang aktif
       (sesi slide itu sedang berjalan), bukan tampil terus-menerus di semua tab. */
    .carousel-indicators .dot .dot-label::before {
        content: '';
        display: inline-block;
        width: 0;
        height: 8px;
        background-color: #ea580c;
        border-radius: 50%;
        margin-right: 0;
        flex-shrink: 0;
        opacity: 0;
        transition: width 0.25s ease, margin-right 0.25s ease, opacity 0.25s ease;
    }

    .carousel-indicators .dot.active .dot-label::before {
        width: 8px;
        margin-right: 8px;
        opacity: 1;
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
        gap: 0;
        width: 500px;
        max-width: 90vw;
        z-index: 10;
        margin-top: -40px;
        margin-left: 20px;
    }

    /* [FIX #4] Merged title + description into a single unified card */
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
        bottom: 0;
        right: 20px;
        max-height: 380px;
        z-index: 20;
        pointer-events: none;
    }
    
    /* ===== BACA SELENGKAPNYA BUTTON ===== */
    .read-more-container {
        display: flex;
        justify-content: flex-end;
        margin-top: 10px;
    }
    .read-more-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        pointer-events: auto;
        padding: 7px 18px;
        background: transparent;
        color: #ea580c;
        border: 2px solid #ea580c;
        border-radius: 10px;
        font-weight: 800;
        font-size: 0.82rem;
        cursor: pointer;
        text-decoration: none;
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

    /* [FIX v3-#2] Tombol panah kembali jadi overlay (position: absolute) di atas track, PIN ke
       tepi container `.carousel-indicators` — bukan flex sibling lagi. Ini memastikan dot
       pertama (leftmost) selalu mulai tepat sejajar dengan sisi kiri container (=sisi kiri card
       deskripsi), karena tombol tidak lagi mencuri ruang flex dari track saat sedang tersembunyi. */
    .dots-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 24px;
        height: 24px;
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
        transition: opacity 0.2s ease, transform 0.2s ease, background 0.2s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        flex-shrink: 0;
    }
    .dots-nav-btn.btn-prev { left: 0; }
    .dots-nav-btn.btn-next { right: 0; }
    .dots-nav-btn.visible { opacity: 1; pointer-events: auto; }
    .dots-nav-btn:hover { background: #ea580c; transform: translateY(-50%) scale(1.12); }
    .dots-nav-btn svg { width: 12px; height: 12px; }
    
    @media (max-width: 900px) {
        .slide1-layout { padding: 0 20px; top: 70px; }
        .slide1-text-container { margin-top: 0; width: 100%; margin-left: 0; }
        .slide1-title-box { padding: 12px 20px; border-radius: 12px; }
        .slide1-title-box h1 { font-size: 1.6rem; }
        .slide1-content-box { font-size: 0.82rem; padding: 16px 20px; border-radius: 12px; }
        .dekanat-img-right { max-height: 250px; opacity: 0.4; }

        /* Responsif Sempurna untuk Tab Bawah (Overview, Fasilitas, Prestasi) */
        .carousel-indicators {
            width: 96vw;
            max-width: 100%;
            bottom: 18px;
            gap: 0;
            padding: 0 4px;
            box-sizing: border-box;
            display: flex;
        }
        .dots-half {
            width: calc(50% - 28px);
            flex: 1 1 0;
            min-width: 0;
            gap: 6px;
        }
        .dots-half.dots-left {
            padding-right: 6px;
            box-sizing: border-box;
        }
        .dots-half.dots-right {
            padding-left: 6px;
            box-sizing: border-box;
        }
        .dots-center-gap {
            width: 54px;
            min-width: 54px;
            max-width: 54px;
            flex: 0 0 54px;
        }
        .carousel-indicators .dot {
            min-width: 0 !important;
            flex: 1 1 0;
            padding: 5px 6px;
            border-radius: 8px;
            gap: 3px;
            box-sizing: border-box;
        }
        .carousel-indicators .dot .dot-label {
            font-size: 0.62rem;
            letter-spacing: 0.2px;
            height: 13px;
        }
        .carousel-indicators .dot .dot-label::before {
            display: none;
        }
        .fasilitas-controls-group {
            gap: 2px;
        }
        .fasilitas-counter {
            font-size: 0.55rem;
            padding: 0 3px;
            letter-spacing: 0;
            border-radius: 4px;
        }
        .lab-play-pause-btn-side {
            display: none;
        }
        .dot .dot-track,
        .dot-track-continuous {
            height: 3px;
        }
    }

    @media (max-width: 480px) {
        .slide1-layout { padding: 0 14px; top: 65px; }
        .slide1-title-box h1 { font-size: 1.35rem; }
        .slide1-content-box { font-size: 0.78rem; padding: 12px 16px; }
        .read-more-btn { padding: 5px 12px; font-size: 0.75rem; }

        .carousel-indicators {
            width: 98vw;
            bottom: 12px;
            padding: 0 2px;
        }
        .dots-half {
            width: calc(50% - 24px);
            gap: 4px;
        }
        .dots-half.dots-left {
            padding-right: 4px;
        }
        .dots-half.dots-right {
            padding-left: 4px;
        }
        .dots-center-gap {
            width: 46px;
            min-width: 46px;
            max-width: 46px;
            flex: 0 0 46px;
        }
        .carousel-indicators .dot {
            padding: 4px 4px;
            border-radius: 7px;
            gap: 2px;
        }
        .carousel-indicators .dot .dot-label {
            font-size: 0.54rem;
            letter-spacing: 0px;
        }
        .fasilitas-counter {
            font-size: 0.48rem;
            padding: 0 2px;
        }
        .dot .dot-track,
        .dot-track-continuous {
            height: 2px;
        }
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
                    <!-- [FIX #4] Single unified card (title + description) -->
                    <div class="slide1-card" id="headerDescBox">
                        <h1 class="slide1-card-title"><?= htmlspecialchars($header_settings->title ?? 'Fakultas Industri Kreatif') ?></h1>
                        <div class="slide1-card-desc">
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
                            <?php
                                $modalTitle1 = htmlspecialchars($header_settings->title ?? 'Fakultas Industri Kreatif', ENT_QUOTES);
                                $modalDesc1 = htmlspecialchars(json_encode($header_settings->description ?? ''), ENT_QUOTES);
                            ?>
                            <div class="read-more-container">
                                <button class="read-more-btn" onclick='openReadMoreModal("<?= $modalTitle1 ?>", <?= $modalDesc1 ?>)'>
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
        
        <!-- Slide 2 (Fasilitas: Lab Fakultas) -->
        <div class="carousel-slide slide-2" style="position: relative; width: 100vw; height: 100%;">
            <?php $this->load->view('dashboard/sections/lab'); ?>
        </div>
        
        <!-- Slide 3 (Prestasi & Inovasi) -->
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
        
        <!-- Custom Slides dari Database (Slide 4, 5, 6, dst) -->
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
                                        <?php
                                            $modalTitle = htmlspecialchars($used_title, ENT_QUOTES);
                                            $modalDesc = htmlspecialchars(json_encode($used_desc), ENT_QUOTES);
                                        ?>
                                        <button class="read-more-btn" onclick='openReadMoreModal("<?= $modalTitle ?>", <?= $modalDesc ?>)'>
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

        // [FIX #1] SEMUA TAB DISATUKAN — Overview, Fasilitas, Prestasi, dan seluruh custom slide
        // sekarang berada dalam SATU array tunggal & dirender lewat SATU loop. Pagination
        // (panah kiri/kanan) jadi berlaku untuk seluruh tab, bukan hanya custom slide yang baru
        // ditambahkan dari web.
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
        <!-- Tombol Prev (muncul otomatis kalau track overflow) -->
        <button class="dots-nav-btn btn-prev" id="dotsNavPrev" onclick="scrollDotsTrack(-1)" title="Sebelumnya">
            <svg fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
        </button>

        <!-- [FIX v3-#1] Satu track tunggal berisi SEMUA tab, tanpa celah/jarak di tengah -->
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

        <!-- Tombol Next (muncul otomatis kalau track overflow) -->
        <button class="dots-nav-btn btn-next" id="dotsNavNext" onclick="scrollDotsTrack(1)" title="Berikutnya">
            <svg fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
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
                    prog.style.width = '0%';
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
                    overviewProg.style.width = '0%';
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
                    overviewProg.style.width = '0%';
                }

                // Completed state for Fasilitas continuous thumbs
                document.querySelectorAll('.dot-track-continuous .thumb').forEach(thumb => {
                    thumb.style.opacity = '0.35';
                    const p = thumb.querySelector('.progress');
                    if (p) { p.style.animation = 'none'; p.style.width = '0%'; }
                });

                if (typeof pauseAutoPlay === 'function') pauseAutoPlay();

                const curDot = document.querySelector(`#carouselDots .dot[data-index="${index}"]`);
                if (curDot) curDot.classList.add('active');
                const curProg = curDot ? curDot.querySelector('.dot-track > .progress') : null;
                
                // MULTI-IMAGE LOGIC
                let totalDuration = 6.5; // default 6.5s
                const slideEl = document.querySelector(`#customSlide_${index}`);
                let slideTimeouts = [];
                
                if (slideEl) {
                    const multiData = slideEl.getAttribute('data-multi');
                    if (multiData && multiData !== '[]') {
                        const items = JSON.parse(multiData);
                        if (items.length > 1) {
                            totalDuration = 0;
                            items.forEach(item => totalDuration += (item.duration || 3));
                            
                            // Queue image swaps
                            let accumulatedTime = 0;
                            slideEl.classList.add('multi-bg-fade');
                            items.forEach((item, idx) => {
                                if (idx > 0) {
                                    const t = setTimeout(() => {
                                        const ext = item.file.split('.').pop().toLowerCase();
                                        if (['mp4','webm','ogg'].includes(ext)) {
                                            slideEl.innerHTML = `<video autoplay muted playsinline style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: -1;"><source src="<?= base_url('assets/vids/') ?>${item.file}" type="video/mp4"></video>` + slideEl.innerHTML.replace(/<video.*?<\/video>/s, '');
                                        } else {
                                            slideEl.style.backgroundImage = `url('<?= base_url('assets/images/') ?>${item.file}')`;
                                        }
                                    }, accumulatedTime * 1000);
                                    slideTimeouts.push(t);
                                } else {
                                    // Set first instantly
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
                    if (activeProgEndListener) {
                        curProg.removeEventListener('animationend', activeProgEndListener);
                    }
                    void curProg.offsetWidth;
                    curProg.style.animation = `slideProgress ${totalDuration}s linear forwards`;
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
            // [FIX #1] Pastikan dot aktif selalu terlihat di dalam track gabungan (auto-scroll pagination)
            if (typeof window._syncActiveDotIntoView === 'function') {
                window._syncActiveDotIntoView(index);
            }
        };

        window.goToSlide = goToSlide;

        goToSlide(0);

        // Click on dots
        dots.forEach((dot) => {
            dot.addEventListener('click', (e) => {
                if (e.target.closest('#labAutoPlayBtn') || e.target.closest('.dot-track-continuous')) return;

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
    // [FIX #1 & #2] PAGINATION UNTUK SATU TRACK GABUNGAN (semua tab: Overview, Fasilitas,
    // Prestasi, dan seluruh custom slide). Dot melebar/mengecil merata lewat CSS flex; ketika
    // total lebar minimum tab melebihi ruang yang tersedia, track ini overflow secara native
    // (overflow-x) dan panah kiri/kanan dipakai untuk scroll — berlaku untuk semua tab, bukan
    // cuma slide yang baru ditambahkan.
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
                // [FIX v3-#2] Padding hanya ditambahkan saat tombol benar-benar tampil, supaya
                // saat tombol tersembunyi dot pertama tetap flush di sisi kiri card.
                track.classList.toggle('has-prev', showPrev);
                track.classList.toggle('has-next', showNext);
            }

            window.scrollDotsTrack = function(dir) {
                // Geser sejauh kira-kira lebar satu dot + gap
                const sampleDot = track.querySelector('.dot');
                const step = (sampleDot ? sampleDot.offsetWidth : 180) + 12;
                track.scrollBy({ left: dir * step, behavior: 'smooth' });
            };

            // Dipanggil dari goToSlide agar dot aktif selalu terlihat di dalam track
            window._syncActiveDotIntoView = function(activeTabIndex) {
                const activeDot = track.querySelector(`.dot[data-index="${activeTabIndex}"]`);
                if (activeDot) {
                    activeDot.scrollIntoView({ behavior: 'smooth', inline: 'nearest', block: 'nearest' });
                }
            };
            // Alias untuk kompatibilitas mundur, kalau ada view lain yang memanggil nama lama
            window._syncDotsRightOffset = window._syncActiveDotIntoView;

            track.addEventListener('scroll', updateNav);
            window.addEventListener('resize', updateNav);
            updateNav();
        });
    })();
</script>

<!-- [FIX #1] Modal Baca Selengkapnya — SATU-SATUNYA instance, dipindah ke luar #section-carousel,
     ID duplikat pada versi sebelumnya (dua elemen #readMoreModal) dihapus karena itulah
     penyebab tombol "Baca Selengkapnya" terasa tidak berfungsi. -->
<div class="read-more-modal" id="readMoreModal">
    <div class="read-more-modal-content">
        <button class="read-more-close" onclick="closeReadMoreModal()">
            <svg viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
        <h2 id="readMoreTitle"></h2>
        <div id="readMoreDesc"></div>
    </div>
</div>

<script>
    function openReadMoreModal(title, descHtml) {
        document.getElementById('readMoreTitle').innerText = title;
        document.getElementById('readMoreDesc').innerHTML = descHtml;
        document.getElementById('readMoreModal').classList.add('active');
    }
    
    function closeReadMoreModal() {
        document.getElementById('readMoreModal').classList.remove('active');
    }
</script>