<?php
/**
 * Komponen Berita Dashboard (3D Fan & Card Deck Mobile)
 */
?>
<style>
    /* ===== SESI 3: BERITA ===== */
    #section-contact {
        background-color: #fbf7f1;
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        align-items: center !important;
        overflow: hidden;
        position: relative;
        padding-top: 65px !important;
        padding-bottom: 65px !important;
        box-sizing: border-box;
        height: 100vh;
    }

    .news-header {
        margin-top: 0;
        margin-bottom: 12px;
        text-align: center;
        z-index: 10;
        position: relative;
        flex-shrink: 0;
    }

    .news-header h1 {
        font-size: 2rem;
        color: #1e293b;
        font-weight: 900;
        margin: 0 0 4px 0;
        line-height: 1.15;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    .news-header p {
        color: #64748b;
        font-size: 0.92rem;
        margin: 0;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    /* ===== WRAPPER UTAMA BERITA (Fan + Kontrol) ===== */
    .news-stage {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        position: relative;
    }

    /* ===== KONTAINER KARTU ===== */
    .news-fan-container {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 420px;
        position: relative;
        overflow: visible !important;
    }

    /* ===== KARTU BERITA ===== */
    .news-card {
        position: absolute;
        left: 50%;
        top: 50%;
        width: 280px;
        height: 400px;
        background: #fff;
        border-radius: 24px;
        box-shadow: -6px 10px 30px rgba(0, 0, 0, 0.12);
        overflow: hidden;
        cursor: pointer;
        transform-origin: bottom center;

        /* CSS Variables */
        --angle-per-card: 12deg;
        --center-index: calc((var(--total) - 1) / 2);
        --offset: calc(var(--index) - var(--center-index));
        --angle: calc(var(--offset) * var(--angle-per-card));

        /* DEFAULT: sudah terbuka/mekar, tidak rapat */
        --spread-x: calc(var(--offset) * 120px);
        --arc-y: calc(var(--offset) * var(--offset) * 7px);

        transform: translate(calc(-50% + var(--spread-x)), calc(-50% + var(--arc-y))) rotate(var(--angle));
        transition: transform 0.45s cubic-bezier(0.175, 0.885, 0.32, 1.275),
                    box-shadow 0.45s ease,
                    filter 0.45s ease,
                    opacity 0.45s ease;
        z-index: calc(10 + var(--index));
    }

    /* ===== VARIAN BINGKAI KARTU (PILIHAN ADMIN) ===== */
    /* 1. Polos / Standar */
    .news-card.frame-none {
        border: 1.5px solid rgba(234, 88, 12, 0.16);
    }
    .news-card.frame-none:hover {
        border-color: rgba(234, 88, 12, 0.45);
    }

    /* 2. Doodle Spiral (Artistik Swirl) */
    .news-card.frame-swirl::before {
        content: '';
        position: absolute;
        inset: 0;
        border: 12px solid #18181b;
        border-image: url('<?= base_url("assets/images/frame-border-swirl.svg") ?>') 20 round;
        pointer-events: none;
        z-index: 15;
        border-radius: 24px;
        box-shadow: inset 0 0 0 1px rgba(234, 88, 12, 0.3);
        transition: box-shadow 0.4s ease;
    }
    .news-card.frame-swirl:hover::before {
        box-shadow: inset 0 0 0 1px rgba(234, 88, 12, 0.8), 0 0 15px rgba(234, 88, 12, 0.35);
    }

    /* 3. Batik Geometrik */
    .news-card.frame-geometric::before {
        content: '';
        position: absolute;
        inset: 0;
        border: 12px solid #18181b;
        border-image: url('<?= base_url("assets/images/frame-border-geometric.svg") ?>') 20 round;
        pointer-events: none;
        z-index: 15;
        border-radius: 24px;
        box-shadow: inset 0 0 0 1px rgba(234, 88, 12, 0.3);
        transition: box-shadow 0.4s ease;
    }
    .news-card.frame-geometric:hover::before {
        box-shadow: inset 0 0 0 1px rgba(234, 88, 12, 0.8), 0 0 15px rgba(234, 88, 12, 0.35);
    }

    /* 4. Polaroid Vintage */
    .news-card.frame-polaroid {
        padding: 8px 8px 12px;
        border: 1.5px solid #e2e8f0;
    }
    .news-card.frame-polaroid .news-image {
        border-radius: 16px;
    }

    /* 5. Glow Neon */
    .news-card.frame-neon {
        border: 2px solid #ea580c;
        box-shadow: 0 0 15px rgba(234, 88, 12, 0.35), 0 12px 30px rgba(0, 0, 0, 0.15);
    }
    .news-card.frame-neon:hover {
        border-color: #f97316;
        box-shadow: 0 0 25px rgba(234, 88, 12, 0.6), 0 20px 45px rgba(234, 88, 12, 0.3);
    }

    /* Saat kartu di-hover: tegak, naik, geser menjauhi tumpukan */
    .news-card:hover {
        --hover-shift-x: calc(var(--offset) * 40px);
        transform: translate(calc(-50% + var(--spread-x) + var(--hover-shift-x)), calc(-50% - 55px)) rotate(0deg) scale(1.06) !important;
        box-shadow: 0 32px 70px rgba(234, 88, 12, 0.35);
        z-index: 100 !important;
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.4s ease, z-index 0s 0s;
    }

    /* Parting: kartu sesudah yang di-hover dorong ke kanan */
    .news-card:hover ~ .news-card {
        transform: translate(calc(-50% + var(--spread-x) + 150px), calc(-50% + var(--arc-y) + 25px)) rotate(calc(var(--angle) + 8deg)) !important;
    }

    /* Parting: kartu sebelum yang di-hover dorong ke kiri */
    .news-fan-container:has(.news-card:hover) .news-card:not(:hover):not(.news-card:hover ~ .news-card) {
        transform: translate(calc(-50% + var(--spread-x) - 150px), calc(-50% + var(--arc-y) + 25px)) rotate(calc(var(--angle) - 8deg)) !important;
    }

    /* Kartu lain meredup saat salah satu di-hover */
    .news-fan-container:has(.news-card:hover) .news-card:not(:hover) {
        opacity: 0.8;
        filter: blur(1px) brightness(0.85);
    }

    /* ===== ANIMASI KELUAR ke BAWAH (Next) ===== */
    .news-card.anim-exit {
        animation: cardExitDown 0.45s cubic-bezier(0.55, 0, 1, 0.45) forwards !important;
        pointer-events: none;
    }
    @keyframes cardExitDown {
        0%   { opacity: 1; transform: translate(calc(-50% + var(--spread-x)), calc(-50% + var(--arc-y))) rotate(var(--angle)) scale(1); }
        100% { opacity: 0; transform: translate(calc(-50% + var(--spread-x)), calc(-50% + var(--arc-y) + 200px)) rotate(var(--angle)) scale(0.85); }
    }

    /* ===== ANIMASI MASUK dari BAWAH (Next) ===== */
    .news-card.anim-enter {
        animation: cardEnterUp 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards !important;
        pointer-events: none;
    }
    @keyframes cardEnterUp {
        0%   { opacity: 0; transform: translate(calc(-50% + var(--spread-x)), calc(-50% + var(--arc-y) + 200px)) rotate(var(--angle)) scale(0.85); }
        100% { opacity: 1; transform: translate(calc(-50% + var(--spread-x)), calc(-50% + var(--arc-y))) rotate(var(--angle)) scale(1); }
    }

    /* ===== ANIMASI KELUAR ke ATAS (Prev) ===== */
    .news-card.anim-exit-up {
        animation: cardExitUp 0.45s cubic-bezier(0.55, 0, 1, 0.45) forwards !important;
        pointer-events: none;
    }
    @keyframes cardExitUp {
        0%   { opacity: 1; transform: translate(calc(-50% + var(--spread-x)), calc(-50% + var(--arc-y))) rotate(var(--angle)) scale(1); }
        100% { opacity: 0; transform: translate(calc(-50% + var(--spread-x)), calc(-50% + var(--arc-y) - 200px)) rotate(var(--angle)) scale(0.85); }
    }

    /* ===== ANIMASI MASUK dari ATAS (Prev) ===== */
    .news-card.anim-enter-down {
        animation: cardEnterDown 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards !important;
        pointer-events: none;
    }
    @keyframes cardEnterDown {
        0%   { opacity: 0; transform: translate(calc(-50% + var(--spread-x)), calc(-50% + var(--arc-y) - 200px)) rotate(var(--angle)) scale(0.85); }
        100% { opacity: 1; transform: translate(calc(-50% + var(--spread-x)), calc(-50% + var(--arc-y))) rotate(var(--angle)) scale(1); }
    }

    /* ===== KONTROL: MENEMPEL DI KANAN SECTION ===== */
    .news-controls {
        position: absolute;
        right: 28px;
        top: 50%;
        transform: translateY(-50%);
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: center;
        gap: 12px;
        z-index: 20;
        width: 56px;
        padding-right: 4px;
    }

    .news-arrow-btn {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        border: 2px solid #ea580c;
        background: #ffffff;
        color: #ea580c;
        font-size: 1.3rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(234, 88, 12, 0.08);
    }

    .news-arrow-btn:hover:not(:disabled) {
        background: #fff7ed;
        color: #ea580c;
        border-color: #f97316;
        transform: scale(1.1);
        box-shadow: 0 0 22px rgba(234, 88, 12, 0.45), 0 4px 14px rgba(234, 88, 12, 0.22);
    }

    .news-arrow-btn:active:not(:disabled) {
        transform: scale(0.96);
        background: #ffedd5;
        box-shadow: 0 0 12px rgba(234, 88, 12, 0.3);
    }

    .news-arrow-btn:disabled {
        border-color: #e2e8f0;
        color: #cbd5e1;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    /* Dots vertikal */
    .news-dots {
        display: flex;
        flex-direction: column;
        gap: 8px;
        align-items: center;
        align-self: center;
        margin: 4px 0;
        transition: opacity 0.3s ease;
    }

    .news-dot {
        width: 8px;
        height: 8px;
        border-radius: 99px;
        background: #cbd5e1;
        cursor: pointer;
        transition: height 0.35s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .news-dot:hover:not(.active) {
        background: #94a3b8;
    }

    .news-dot.active {
        background: rgba(234, 88, 12, 0.25);
        height: 32px; /* Memanjang ke bawah (vertikal pill) */
        border-radius: 99px;
        box-shadow: 0 2px 10px rgba(234, 88, 12, 0.2);
    }

    .news-dot-fill {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 0%;
        background: linear-gradient(180deg, #ea580c, #f97316);
        border-radius: 99px;
        pointer-events: none;
        box-shadow: 0 0 6px rgba(234, 88, 12, 0.6);
    }

    .news-page-info {
        font-size: 0.85rem;
        color: #94a3b8;
        font-weight: 600;
        text-align: center;
        align-self: center;
        line-height: 1.3;
    }

    /* ===== ISI KARTU ===== */
    .news-image {
        width: 100%;
        height: 45%;
        background-color: #ddd; 
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .news-image::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0; width: 100%; height: 30px;
        background: linear-gradient(to top, #ffffff, transparent);
    }

    /* ===== PLACEHOLDER FOTO BERITA (Bila Foto Kosong / Belum Ada) ===== */
    .news-image--placeholder {
        background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 50%, #fed7aa 100%) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .news-image-placeholder-badge {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        color: #ea580c;
        opacity: 0.85;
        transition: transform 0.3s ease, opacity 0.3s ease;
        pointer-events: none;
    }
    .news-card:hover .news-image-placeholder-badge {
        transform: scale(1.1);
        opacity: 1;
    }
    .news-image-placeholder-badge span {
        font-size: 0.65rem;
        font-weight: 800;
        letter-spacing: 1.2px;
        text-transform: uppercase;
    }

    /* Modal placeholder foto */
    .news-modal-card-img-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 50%, #fed7aa 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: #ea580c;
    }
    .news-modal-card-img-placeholder span {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 1.2px;
        text-transform: uppercase;
    }
    
    .news-content {
        padding: 20px 22px;
        height: 55%;
        display: flex;
        flex-direction: column;
    }
    
    .news-date {
        font-size: 0.75rem;
        color: #ea580c;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 10px;
        letter-spacing: 1px;
    }
    
    .news-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 10px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .news-excerpt {
        font-size: 0.88rem;
        color: #64748b;
        line-height: 1.6;
        flex-grow: 1;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }


    /* ===== RESPONSIVE ===== */
    /* ===== VIEW ALL BUTTON ===== */
    /* ===== EXPANDING CIRCULAR VIEW ALL BUTTON ===== */
    .news-view-all-btn {
        position: relative;
        right: auto;
        bottom: auto;
        margin-top: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: flex-start;
        height: 48px;
        width: 48px;
        padding: 0;
        border-radius: 9999px;
        border: 2px solid #ea580c;
        background: #ffffff;
        color: #ea580c;
        font-size: 0.84rem;
        font-weight: 700;
        cursor: pointer;
        transition: width 0.35s cubic-bezier(0.16, 1, 0.3, 1),
                    background-color 0.3s ease,
                    border-color 0.3s ease,
                    box-shadow 0.3s ease,
                    transform 0.25s ease;
        z-index: 25;
        white-space: nowrap;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(234, 88, 12, 0.08);
        flex-shrink: 0;
    }
    .news-view-all-btn .btn-icon-wrapper {
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .news-view-all-btn .btn-text-label {
        max-width: 0;
        opacity: 0;
        overflow: hidden;
        white-space: nowrap;
        transition: max-width 0.35s cubic-bezier(0.16, 1, 0.3, 1),
                    opacity 0.25s ease,
                    padding 0.35s ease;
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.3px;
    }
    .news-view-all-btn:hover,
    .news-view-all-btn:focus-visible {
        width: 155px;
        background: #ea580c;
        color: #ffffff;
        border-color: #ea580c;
        box-shadow: 0 8px 25px rgba(234, 88, 12, 0.4);
        transform: translateY(-2px);
    }
    .news-view-all-btn:hover .btn-text-label,
    .news-view-all-btn:focus-visible .btn-text-label {
        max-width: 100px;
        opacity: 1;
        padding-right: 14px;
    }
    .news-view-all-btn svg {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
        transition: transform 0.3s ease;
    }
    .news-view-all-btn:hover svg {
        transform: rotate(90deg);
    }

    /* ===== MODAL OVERLAY (LIGHT THEME) ===== */
    #newsModal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: center;
        background: rgba(251, 247, 241, 0.98); /* Light theme off-white */
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.35s ease;
        overflow-y: auto;
        overscroll-behavior: contain;
        max-height: 100vh;
        padding: 48px 40px 60px;
    }
    #newsModal.open {
        opacity: 1;
        pointer-events: all;
    }

    .news-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        max-width: 1300px;
        margin-bottom: 36px;
        flex-shrink: 0;
    }
    .news-modal-header h2 {
        font-size: 2rem;
        font-weight: 900;
        color: #1e293b;
    }
    .news-modal-header span {
        font-size: 0.95rem;
        color: #64748b;
        margin-top: 4px;
    }
    .news-modal-close {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        border: 2px solid rgba(30, 41, 59, 0.15);
        background: #ffffff;
        color: #1e293b;
        font-size: 1.4rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        flex-shrink: 0;
        box-shadow: 0 4px 14px rgba(0,0,0,0.06);
    }
    .news-modal-close:hover {
        background: #ea580c;
        border-color: #ea580c;
        color: #fff;
        transform: rotate(90deg);
    }

    .news-modal-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr); /* Selalu 3 kolom */
        gap: 24px;
        width: 100%;
        max-width: 1300px;
        align-items: start;
    }

    /* ===== MODAL CARD: Circular Corner Notch Design ===== */

    /* === Variant Alternatif: cutout di pojok KANAN BAWAH === */
    .news-modal-card--alt .news-modal-card-body {
        -webkit-mask-image: radial-gradient(
            circle at calc(100% - 36px) calc(100% - 36px),
            transparent var(--cutout-r),
            #000 calc(var(--cutout-r) + 1px)
        );
        mask-image: radial-gradient(
            circle at calc(100% - 36px) calc(100% - 36px),
            transparent var(--cutout-r),
            #000 calc(var(--cutout-r) + 1px)
        );
    }
    /* Button alt: terpasang di pojok kanan bawah */
    .news-modal-card--alt .news-modal-card-btn {
        top: auto;
        bottom: 14px;
        left: auto;
        right: 14px;
        animation-delay: 0.6s;
    }


    @property --cutout-r {
        syntax: '<length>';
        inherits: false;
        initial-value: 46px;
    }

    /* Outer wrapper: fixed full size 330px */
    .news-modal-card {
        position: relative;
        height: 330px;
        cursor: pointer;
        transition: transform 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275),
                    box-shadow 0.35s ease;
        opacity: 0;
        transform: translateY(30px);
        filter: drop-shadow(0 10px 24px rgba(0,0,0,0.4));
    }
    .news-modal-card.visible {
        opacity: 1;
        transform: translateY(0);
    }
    .news-modal-card:hover {
        transform: translateY(-8px);
        filter: drop-shadow(0 22px 40px rgba(234, 88, 12, 0.22));
    }

    /* Inner card body: circular notch mask (tetap ada saat hover) */
    .news-modal-card-body {
        position: absolute;
        inset: 0;
        border-radius: 24px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        background: #0f172a; /* Dark slate card base */

        /* Lingkaran transparan cutout (stay di 46px) */
        --cutout-r: 46px;
        -webkit-mask-image: radial-gradient(
            circle at calc(100% - 36px) 36px,
            transparent var(--cutout-r),
            #000 calc(var(--cutout-r) + 1px)
        );
        mask-image: radial-gradient(
            circle at calc(100% - 36px) 36px,
            transparent var(--cutout-r),
            #000 calc(var(--cutout-r) + 1px)
        );
    }

    .news-modal-card-bg {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 165px;
        overflow: hidden;
        background: #0f172a;
    }
    .news-modal-card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transition: transform 0.6s cubic-bezier(0.25, 1, 0.5, 1);
    }
    .news-modal-card:hover .news-modal-card-img {
        transform: scale(1.08);
    }
    .news-modal-card-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, transparent 50%, rgba(15, 23, 42, 0.9) 100%);
        pointer-events: none;
    }

    /* Date badge */
    .news-modal-card-badge {
        position: relative;
        z-index: 2;
        color: #34d399;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 0 22px;
        margin-bottom: 6px;
    }

    /* Text content (selalu tampil penuh) */
    .news-modal-card-content {
        position: relative;
        z-index: 2;
        padding: 0 22px 22px;
        width: 100%;
        box-sizing: border-box;
    }
    .news-modal-card-title {
        color: #fff;
        font-size: 1.05rem;
        font-weight: 800;
        line-height: 1.4;
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .news-modal-card-excerpt {
        color: #94a3b8;
        font-size: 0.83rem;
        line-height: 1.55;
        margin-top: 10px;
        opacity: 0.9;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Button: selalu visible di area cutout (posisi stay, lingkaran diam) */
    .news-modal-card-btn {
        position: absolute;
        top: 14px;
        right: 14px;
        z-index: 20;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: 2px solid rgba(15, 23, 42, 0.15);
        background: #1e293b;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        outline: none;
        padding: 0;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.18);
        transition: background 0.35s ease, border-color 0.35s ease, box-shadow 0.35s ease;
    }
    .news-modal-card--alt .news-modal-card-btn {
        top: auto;
        bottom: 14px;
        left: auto;
        right: 14px;
    }

    /* Animasi denyut halus KHUSUS untuk ikon arrow di dalam (lingkaran luar tidak ikut gerak) */
    .news-modal-card-btn-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        animation: arrowIdlePulse 2s ease-in-out infinite;
    }
    .news-modal-card--alt .news-modal-card-btn-icon {
        animation-delay: 0.6s;
    }

    /* Idle: maju-mundur diagonal ke kanan-atas ↗ */
    @keyframes arrowIdlePulse {
        0%, 100% {
            transform: translate(0, 0);
        }
        50% {
            transform: translate(3px, -3px);
        }
    }

    /* Hover: maju-mundur lurus ke kanan -> */
    @keyframes arrowHoverPulse {
        0%, 100% {
            transform: translate(0, 0);
        }
        50% {
            transform: translate(4px, 0);
        }
    }

    .news-modal-card:hover .news-modal-card-btn-icon {
        animation: arrowHoverPulse 1.2s ease-in-out infinite;
    }

    .news-modal-card-btn svg {
        width: 16px;
        height: 16px;
        transition: transform 0.35s ease;
    }
    .news-modal-card:hover .news-modal-card-btn {
        background: #ea580c;
        border-color: #ea580c;
        box-shadow: 0 4px 16px rgba(234, 88, 12, 0.5);
    }
    .news-modal-card:hover .news-modal-card-btn svg {
        transform: rotate(45deg); /* Berputar dari ↗ ke -> saat hover */
    }

    /* ===== SEARCH & AUTOCOMPLETE (LIGHT THEME) ===== */
    .news-search-wrapper {
        position: relative;
        flex: 1;
        max-width: 450px;
        margin: 0 40px;
        z-index: 50;
    }
    .news-search-input-container {
        position: relative;
        width: 100%;
        display: flex;
        align-items: center;
    }
    #newsSearchInput {
        width: 100%;
        padding: 12px 40px 12px 44px;
        border-radius: 30px;
        border: 2px solid rgba(234, 88, 12, 0.25);
        background: #ffffff;
        color: #1e293b;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        outline: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
    }
    #newsSearchInput::placeholder {
        color: #94a3b8;
    }
    #newsSearchInput:focus {
        border-color: #ea580c;
        background: #ffffff;
        box-shadow: 0 0 20px rgba(234, 88, 12, 0.2);
    }
    .news-search-icon {
        position: absolute;
        left: 16px;
        font-size: 1.1rem;
        color: #ea580c;
        pointer-events: none;
    }
    .news-search-clear {
        position: absolute;
        right: 16px;
        background: none;
        border: none;
        color: #64748b;
        font-size: 1.3rem;
        cursor: pointer;
        outline: none;
        padding: 0;
        line-height: 1;
        transition: color 0.2s ease;
    }
    .news-search-clear:hover {
        color: #ea580c;
    }
    
    .news-autocomplete-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        right: 0;
        background: #ffffff;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(234, 88, 12, 0.15);
        border-radius: 16px;
        max-height: 250px;
        overflow-y: auto;
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.12);
        display: none;
        scrollbar-width: thin;
        scrollbar-color: rgba(234, 88, 12, 0.3) transparent;
    }
    .news-autocomplete-dropdown.open {
        display: block;
    }
    .news-autocomplete-item {
        padding: 12px 18px;
        color: #334155;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
        border-bottom: 1px solid #f1f5f9;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-align: left;
    }
    .news-autocomplete-item:last-child {
        border-bottom: none;
    }
    .news-autocomplete-item:hover {
        background: rgba(234, 88, 12, 0.08);
        color: #ea580c;
    }
    .news-autocomplete-item strong {
        color: #ea580c;
    }
    .news-autocomplete-no-match {
        padding: 12px 18px;
        color: #64748b;
        font-size: 0.9rem;
        font-style: italic;
        text-align: left;
    }

    @media (max-width: 1200px) {
        .news-header h1 { font-size: 1.85rem; }
        .news-card { width: 250px; height: 360px; }
        .news-fan-container { height: 380px; }
        .news-title { font-size: 1.02rem; }
    }

    @media (max-width: 900px) {
        .news-header h1 { font-size: 1.65rem; }
        .news-header p { font-size: 0.86rem; }
        .news-card {
            width: 220px;
            height: 330px;
            --spread-x: calc(var(--offset) * 60px);
            --angle-per-card: 7deg;
        }
        .news-fan-container { height: 350px; }
        .news-title { font-size: 0.94rem; }
        .news-excerpt { font-size: 0.8rem; -webkit-line-clamp: 2; }
    }

    /* ===== RESPONSIVE MOBILE STACKED CARD DECK SWIPER MODE ===== */
    @media (max-width: 768px) {
        #section-contact {
            height: auto !important;
            min-height: 100vh;
            padding-top: 80px !important;
            padding-bottom: 35px !important;
            justify-content: center !important;
            overflow-x: hidden !important;
        }
        .news-header {
            margin-top: 0;
            margin-bottom: 12px;
            padding: 0 16px;
        }
        .news-header h1 {
            font-size: 1.5rem;
            line-height: 1.2;
        }
        .news-header p {
            font-size: 0.82rem;
            padding: 0 12px;
        }
        .news-fan-container {
            height: 355px;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: visible !important;
            touch-action: pan-y;
        }
        .news-card {
            width: 245px;
            max-width: 82vw;
            height: 350px;
            position: absolute !important;
            left: 50% !important;
            top: 50% !important;
            border-radius: 22px;
            box-shadow: -6px 12px 35px rgba(0, 0, 0, 0.16) !important;
            transition: transform 0.45s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.45s ease, opacity 0.45s ease;
        }

        /* Position Stack Card Numpuk (keluar dikit di kanan-bawah) khusus Mobile */
        .news-card[data-stack-idx="0"] {
            transform: translate(-50%, -50%) rotate(0deg) scale(1) !important;
            z-index: 50 !important;
            opacity: 1 !important;
            pointer-events: auto !important;
        }
        .news-card[data-stack-idx="1"] {
            transform: translate(calc(-50% + 14px), calc(-50% + 8px)) rotate(3.5deg) scale(0.96) !important;
            z-index: 40 !important;
            opacity: 0.94 !important;
            pointer-events: none !important;
        }
        .news-card[data-stack-idx="2"] {
            transform: translate(calc(-50% + 28px), calc(-50% + 16px)) rotate(7deg) scale(0.92) !important;
            z-index: 30 !important;
            opacity: 0.86 !important;
            pointer-events: none !important;
        }
        .news-card[data-stack-idx="3"] {
            transform: translate(calc(-50% + 42px), calc(-50% + 24px)) rotate(10.5deg) scale(0.88) !important;
            z-index: 20 !important;
            opacity: 0.74 !important;
            pointer-events: none !important;
        }
        .news-card[data-stack-idx="4"] {
            transform: translate(calc(-50% + 56px), calc(-50% + 32px)) rotate(14deg) scale(0.84) !important;
            z-index: 10 !important;
            opacity: 0.60 !important;
            pointer-events: none !important;
        }
        .news-card[data-stack-idx="shuffle"] {
            transform: translate(calc(-50% - 250px), calc(-50% + 15px)) rotate(-18deg) scale(0.92) !important;
            z-index: 60 !important;
            opacity: 0.8 !important;
            pointer-events: none !important;
        }
        .news-card[data-stack-idx="shuffle-right"] {
            transform: translate(calc(-50% + 250px), calc(-50% + 15px)) rotate(18deg) scale(0.92) !important;
            z-index: 60 !important;
            opacity: 0.8 !important;
            pointer-events: none !important;
        }

        /* Dimatikan fanning hover di mobile agar murni stack deck shuffle */
        .news-card:hover {
            box-shadow: -6px 12px 35px rgba(0, 0, 0, 0.16) !important;
        }
        .news-fan-container:has(.news-card:hover) .news-card:not(:hover) {
            opacity: 1 !important;
            filter: none !important;
        }

        /* Controls Row */
        .news-controls {
            position: relative;
            right: auto;
            top: auto;
            transform: none;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: center !important;
            width: auto;
            gap: 10px;
            margin-top: 14px;
            margin-bottom: 0;
            padding: 0;
            z-index: 40;
        }
        .news-arrow-btn {
            display: none !important;
        }
        .news-dots {
            flex-direction: row !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px;
            margin: 0 2px;
            height: 40px;
        }
        .news-dot {
            width: 8px;
            height: 8px;
        }
        .news-dot.active {
            width: 24px;
            height: 8px;
            border-radius: 99px;
        }
        .news-page-info {
            line-height: 1;
            margin: 0 2px;
            display: inline-flex;
            align-items: center;
            height: 40px;
            font-size: 0.82rem;
            font-weight: 700;
            color: #ea580c;
            background: #fff7ed;
            padding: 0 12px;
            border-radius: 99px;
            border: 1px solid rgba(234, 88, 12, 0.25);
        }
        .news-view-all-btn {
            position: relative;
            right: auto;
            bottom: auto;
            width: 40px;
            height: 40px;
            margin: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .news-view-all-btn .btn-icon-wrapper {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .news-view-all-btn:hover,
        .news-view-all-btn:focus-visible {
            width: 135px;
        }
        .news-dot-fill {
            top: 0;
            left: 0;
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, #ea580c, #f97316);
        }
        .news-modal-header {
            flex-direction: column;
            align-items: stretch;
            gap: 14px;
            position: relative;
        }
        .news-search-wrapper {
            margin: 0;
            max-width: 100%;
        }
        .news-modal-close {
            position: absolute;
            top: 0;
            right: 0;
            width: 38px;
            height: 38px;
            font-size: 1.15rem;
        }
        .news-modal-grid {
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 14px;
        }
        #newsModal {
            padding: 24px 14px 36px;
        }
    }

    @media (max-width: 480px) {
        #section-contact {
            padding-top: 80px !important;
            padding-bottom: 30px !important;
            justify-content: center !important;
        }
        .news-header h1 {
            font-size: 1.4rem;
        }
        .news-header p {
            font-size: 0.8rem;
        }
        .news-fan-container {
            height: 335px;
        }
        .news-card {
            width: 215px;
            height: 315px;
            border-radius: 18px;
            --spread-x: calc(var(--offset) * 32px);
            --arc-y: calc(var(--offset) * var(--offset) * 3px);
            --angle-per-card: 5deg;
        }
        .news-content {
            padding: 14px 16px;
        }
        .news-title {
            font-size: 0.92rem;
            margin-bottom: 4px;
            line-height: 1.35;
        }
        .news-date {
            font-size: 0.7rem;
            margin-bottom: 4px;
        }
        .news-excerpt {
            font-size: 0.78rem;
            line-height: 1.45;
            -webkit-line-clamp: 2;
        }
        .news-view-all-btn {
            height: 42px;
            width: 42px;
            font-size: 0.78rem;
        }
        .news-view-all-btn .btn-icon-wrapper {
            width: 38px;
            height: 38px;
        }
        .news-view-all-btn:hover,
        .news-view-all-btn:focus-visible {
            width: 140px;
        }
        .news-modal-grid {
            grid-template-columns: 1fr;
        }
        .news-modal-card {
            height: 270px;
        }
        .news-modal-header h2 {
            font-size: 1.25rem;
        }
    }

    @media (max-width: 360px) {
        .news-header h1 {
            font-size: 1.25rem;
        }
        .news-fan-container {
            height: 310px;
        }
        .news-card {
            width: 195px;
            height: 290px;
            border-radius: 16px;
            --spread-x: calc(var(--offset) * 22px);
            --angle-per-card: 4deg;
        }
        .news-content {
            padding: 12px 14px;
        }
        .news-title {
            font-size: 0.86rem;
        }
        .news-excerpt {
            display: none; /* Sembunyikan ringkasan di HP sangat kecil agar judul muat penuh */
        }
    }
</style>

<!-- Sesi 3: Berita dengan Paging Fan -->
<!-- ID dipertahankan sebagai 'section-contact' agar IntersectionObserver di index.php tetap berfungsi -->
<div class="section-wrapper" id="section-contact">
    
    <div class="news-header">
        <h1>Berita &amp; Informasi Terkini</h1>
        <p>Tetap terhubung dengan kabar terbaru dari Fakultas Industri Kreatif</p>
    </div>

    <div class="news-stage">

        <!-- Container Fan Kartu -->
        <div class="news-fan-container" id="newsFanContainer">
            <!-- Kartu akan di-render oleh JavaScript -->
        </div>

        <!-- Kontrol Navigasi Vertikal Kanan -->
        <div class="news-controls">
            <button class="news-arrow-btn" id="newsPrevBtn" title="Halaman Sebelumnya">&#8593;</button>
            <div class="news-dots" id="newsDots"></div>
            <span class="news-page-info" id="newsPageInfo"></span>
            <button class="news-arrow-btn" id="newsNextBtn" title="Halaman Berikutnya">&#8595;</button>
            
            <!-- Tombol View All (Circular Expanding Button) -->
            <button class="news-view-all-btn" id="newsViewAllBtn" title="Buka Semua Berita" aria-label="Lihat Semua Berita">
                <span class="btn-icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                        <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>
                    </svg>
                </span>
                <span class="btn-text-label">Semua Berita</span>
            </button>
        </div>

    </div>
</div>

<!-- ===== MODAL VIEW ALL ===== -->
<div id="newsModal" data-lenis-prevent>
    <div class="news-modal-header">
        <div>
            <h2>Semua Berita &amp; Informasi</h2>
            <span id="newsModalCount"></span>
        </div>
        <div class="news-search-wrapper">
            <div class="news-search-input-container">
                <input type="text" id="newsSearchInput" placeholder="Cari judul berita..." autocomplete="off">
                <button id="newsSearchClear" class="news-search-clear" style="display:none;">&times;</button>
            </div>
            <div id="newsAutocomplete" class="news-autocomplete-dropdown"></div>
        </div>
        <button class="news-modal-close" id="newsModalClose" title="Tutup">&times;</button>
    </div>
    <div class="news-modal-grid" id="newsModalGrid"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    // ===== DATA SEMUA BERITA (dinamis dari database & model) =====
    <?php
        $CI =& get_instance();
        if (!isset($CI->News_model)) {
            $CI->load->model('News_model');
        }
        $db_berita = isset($berita) && !empty($berita) ? $berita : ($CI->News_model ? $CI->News_model->get_published() : array());

        $bulan_map = array('01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember');
        $news_json = array();

        if (!empty($db_berita)) {
            foreach ($db_berita as $b) {
                $tgl_p = explode('-', $b->tanggal);
                $tgl_id = (count($tgl_p) === 3) ? ((int)$tgl_p[2].' '.($bulan_map[$tgl_p[1]]??'').' '.$tgl_p[0]) : $b->tanggal;
                
                $img_path = $b->gambar ? trim($b->gambar) : '';
                $has_image = !empty($img_path) && file_exists(FCPATH . ltrim($img_path, '/\\')) && strpos($img_path, 'background.png') === false;
                $gambar_url = $has_image ? base_url($img_path) : '';

                $news_json[] = array(
                    'id'            => $b->id,
                    'date'          => $tgl_id,
                    'title'         => $b->judul,
                    'excerpt'       => $b->excerpt ?? '',
                    'image'         => $gambar_url,
                    'isPlaceholder' => !$has_image,
                    'url'           => site_url('news/detail/' . $b->id),
                    'border_style'  => $b->border_style ?? 'none',
                );
            }
        } else {
            // Sample fallback news items if DB is empty, with real photos + placeholder examples
            $sample_news = array(
                array(
                    'id' => 1,
                    'date' => '12 Agustus 2026',
                    'title' => 'Pameran Karya Mahasiswa FIK 2026 Sukses Digelar',
                    'excerpt' => 'Ratusan karya inovatif dari mahasiswa dipamerkan dalam ajang tahunan yang dihadiri oleh praktisi industri kreatif terkemuka.',
                    'image' => base_url('assets/images/Fakultas.jpg'),
                    'isPlaceholder' => false,
                    'border_style' => 'swirl'
                ),
                array(
                    'id' => 2,
                    'date' => '05 Agustus 2026',
                    'title' => 'Workshop Desain Interaktif Bersama Pakar UI/UX',
                    'excerpt' => 'Mahasiswa diajak untuk mendalami tren UI/UX dan interaksi 3D web modern dalam workshop intensif selama dua hari.',
                    'image' => '',
                    'isPlaceholder' => true,
                    'border_style' => 'neon'
                ),
                array(
                    'id' => 3,
                    'date' => '28 Juli 2026',
                    'title' => 'Peluncuran Sistem Layanan Terpadu IFIK Versi Baru',
                    'excerpt' => 'Sistem IFIK kini hadir dengan wajah baru yang lebih premium, responsif, dan interaktif untuk memudahkan seluruh civitas akademika.',
                    'image' => base_url('assets/images/multimedia.jpg'),
                    'isPlaceholder' => false,
                    'border_style' => 'geometric'
                ),
                array(
                    'id' => 4,
                    'date' => '15 Juli 2026',
                    'title' => 'Prestasi Gemilang Tim Riset FIK di Tingkat Nasional',
                    'excerpt' => 'Penelitian kolaboratif dosen dan mahasiswa tentang pemanfaatan AI dalam desain komunikasi visual berhasil memenangkan hibah.',
                    'image' => '',
                    'isPlaceholder' => true,
                    'border_style' => 'polaroid'
                ),
                array(
                    'id' => 5,
                    'date' => '02 Juli 2026',
                    'title' => 'Kunjungan Studi Industri Kreatif ke Studio Animasi',
                    'excerpt' => 'Mahasiswa semester akhir berkesempatan melihat langsung alur kerja produksi animasi 3D kelas dunia dan berdiskusi.',
                    'image' => base_url('assets/images/Aula1.jpg'),
                    'isPlaceholder' => false,
                    'border_style' => 'none'
                )
            );

            foreach ($sample_news as $sn) {
                $news_json[] = array(
                    'id'           => $sn['id'],
                    'date'         => $sn['date'],
                    'title'        => $sn['title'],
                    'excerpt'      => $sn['excerpt'],
                    'image'        => $sn['image'],
                    'isPlaceholder'=> $sn['isPlaceholder'],
                    'url'          => site_url('news/detail/' . $sn['id']),
                    'border_style' => $sn['border_style'],
                );
            }
        }
    ?>
    const allNews = <?= json_encode($news_json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

    // ===== KONFIGURASI =====
    function getCardsPerPage() {
        return window.innerWidth <= 768 ? 1 : 5; // Mobile: 1 fokus aktif per slide (memungkinkan swipe per kartu)
    }

    let CARDS_PER_PAGE = getCardsPerPage();
    const container      = document.getElementById('newsFanContainer');
    const prevBtn        = document.getElementById('newsPrevBtn');
    const nextBtn        = document.getElementById('newsNextBtn');
    const dotsEl         = document.getElementById('newsDots');
    const pageInfoEl     = document.getElementById('newsPageInfo');

    let totalPages   = Math.ceil(allNews.length / CARDS_PER_PAGE);
    let currentPage  = 0;
    let isAnimating  = false;

    // ===== BUILD DOTS =====
    function buildDots() {
        dotsEl.innerHTML = '';
        const dotsCount = window.innerWidth <= 768 ? Math.ceil(allNews.length / 5) : totalPages;
        const activeDotIndex = window.innerWidth <= 768 ? Math.floor(currentPage / 5) : currentPage;

        for (let i = 0; i < dotsCount; i++) {
            const dot = document.createElement('div');
            dot.className = 'news-dot' + (i === activeDotIndex ? ' active' : '');
            
            const fill = document.createElement('div');
            fill.className = 'news-dot-fill';
            dot.appendChild(fill);

            dot.addEventListener('click', () => {
                const targetPage = window.innerWidth <= 768 ? i * 5 : i;
                goToPage(targetPage);
            });
            dotsEl.appendChild(dot);
        }
    }

    // ===== UPDATE UI =====
    function updateControls() {
        if (window.innerWidth <= 768) {
            prevBtn.disabled = false;
            nextBtn.disabled = false;
            const displayTotal = Math.ceil(allNews.length / 5);
            const displayCurrent = Math.min(displayTotal, Math.floor(currentPage / 5) + 1);
            pageInfoEl.textContent = `${displayCurrent} / ${displayTotal}`;
            const activeDotIndex = Math.floor(currentPage / 5);
            dotsEl.querySelectorAll('.news-dot').forEach((d, i) => {
                const isActive = i === activeDotIndex;
                d.classList.toggle('active', isActive);
                const fill = d.querySelector('.news-dot-fill');
                if (fill) fill.style.height = '0%';
            });
        } else {
            prevBtn.disabled = currentPage === 0;
            nextBtn.disabled = currentPage === totalPages - 1;
            pageInfoEl.textContent = `${currentPage + 1} / ${totalPages}`;
            dotsEl.querySelectorAll('.news-dot').forEach((d, i) => {
                const isActive = i === currentPage;
                d.classList.toggle('active', isActive);
                const fill = d.querySelector('.news-dot-fill');
                if (fill) fill.style.height = '0%';
            });
        }
    }

    // ===== RENDER KARTU =====
    function renderPage(pageIndex) {
        container.innerHTML = '';
        const isMobile = window.innerWidth <= 768;

        if (isMobile) {
            const totalNews = allNews.length;
            const maxStack = Math.min(5, totalNews);
            for (let i = 0; i < maxStack; i++) {
                let newsIndex = (pageIndex + i) % totalNews;
                const news = allNews[newsIndex];
                if (news) {
                    const card = createCard(news, i, maxStack);
                    card.setAttribute('data-stack-idx', i); // 0 (paling atas), 1, 2, 3, 4 (paling bawah di deck)
                    container.appendChild(card);
                }
            }
        } else {
            const start  = pageIndex * CARDS_PER_PAGE;
            const slice  = allNews.slice(start, start + CARDS_PER_PAGE);
            const total  = slice.length;

            slice.forEach((news, i) => {
                const card = createCard(news, i, total);
                container.appendChild(card);
            });
        }
    }

    // ===== BUAT ELEMEN KARTU =====
    function createCard(news, index, total) {
        const card = document.createElement('div');
        card.className = 'news-card frame-' + (news.border_style || 'none');
        card.style.setProperty('--index', index);
        card.style.setProperty('--total', total);

        const isPlaceholder = !news.image || news.isPlaceholder;
        const imgStyle = isPlaceholder 
            ? '' 
            : (news.imageFit
                ? `background-image:url('${news.image}'); background-size:contain; background-repeat:no-repeat; background-color:${news.imageBg || '#f1f5f9'};`
                : `background-image:url('${news.image}');`);

        const placeholderHtml = isPlaceholder ? `
            <div class="news-image-placeholder-badge">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                  <path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/>
                </svg>
                <span>IFIK Portal</span>
            </div>
        ` : '';

        card.innerHTML = `
            <div class="news-image ${isPlaceholder ? 'news-image--placeholder' : ''}" style="${imgStyle}">
                ${placeholderHtml}
            </div>
            <div class="news-content">
                <span class="news-date">${news.date}</span>
                <h3 class="news-title">${news.title}</h3>
                <p class="news-excerpt">${news.excerpt}</p>
            </div>`;

        if (news.url) {
            card.addEventListener('click', (e) => {
                if (Math.abs(touchCurrentX - touchStartX) > 10 || Math.abs(touchCurrentY - touchStartY) > 10) {
                    e.preventDefault();
                    return;
                }
                window.location.href = news.url;
            });
        }
        return card;
    }

    // ===== ANIMASI PINDAH HALAMAN =====
    function goToPage(targetPage, direction = null) {
        if (isAnimating || targetPage === currentPage) return;
        if (targetPage < 0 || targetPage >= totalPages) return;

        isAnimating = true;
        resetAutoScroll();

        const goingForward = direction !== null ? direction === 'next' : targetPage > currentPage;
        const exitClass   = goingForward ? 'anim-exit'       : 'anim-exit-up';
        const enterClass  = goingForward ? 'anim-enter'      : 'anim-enter-down';

        const currentCards = Array.from(container.querySelectorAll('.news-card'));

        currentCards.forEach(card => {
            if (window.innerWidth > 768) {
                card.classList.add(exitClass);
            }
        });

        setTimeout(() => {
            currentPage = targetPage;
            renderPage(currentPage);
            updateControls();

            const newCards = Array.from(container.querySelectorAll('.news-card'));
            if (window.innerWidth > 768) {
                newCards.forEach((card, i) => {
                    card.style.animationDelay = `${i * 50}ms`;
                    card.classList.add(enterClass);
                });
            }

            setTimeout(() => {
                newCards.forEach(card => {
                    card.classList.remove(enterClass);
                    card.style.animationDelay = '';
                });
                isAnimating = false;
            }, window.innerWidth <= 768 ? 50 : 550);

        }, window.innerWidth <= 768 ? 10 : 480);
    }

    // ===== AUTO SCROLL (OTOMATIS PINDAH HALAMAN DENGAN PROGRESS DOT) =====
    const AUTO_SCROLL_DELAY = 10000;
    let autoScrollRaf = null;
    let lastTimestamp = null;
    let elapsedMs = 0;
    let isHovered = false;
    let isSectionVisible = true;

    function updateProgressBar(pct) {
        const activeDotFill = dotsEl.querySelector('.news-dot.active .news-dot-fill');
        if (activeDotFill) {
            const isMobile = window.innerWidth <= 768;
            const cappedPct = Math.min(100, Math.max(0, pct));
            if (isMobile) {
                activeDotFill.style.width = `${cappedPct}%`;
                activeDotFill.style.height = '100%';
            } else {
                activeDotFill.style.height = `${cappedPct}%`;
                activeDotFill.style.width = '100%';
            }
        }
    }

    function autoScrollLoop(timestamp) {
        if (lastTimestamp === null) lastTimestamp = timestamp;
        const delta = timestamp - lastTimestamp;
        lastTimestamp = timestamp;

        if (totalPages > 1 && !isHovered && isSectionVisible && !document.hidden && !newsModal.classList.contains('open')) {
            elapsedMs += delta;
            const pct = (elapsedMs / AUTO_SCROLL_DELAY) * 100;
            updateProgressBar(pct);

            if (elapsedMs >= AUTO_SCROLL_DELAY) {
                elapsedMs = 0;
                updateProgressBar(0);
                nextAutoPage();
            }
        }

        autoScrollRaf = requestAnimationFrame(autoScrollLoop);
    }

    function nextAutoPage() {
        if (totalPages <= 1 || isAnimating) return;
        const nextPage = (currentPage + 1) % totalPages;
        goToPage(nextPage, 'next');
    }

    function startAutoScroll() {
        stopAutoScroll();
        if (totalPages <= 1) return;
        lastTimestamp = null;
        autoScrollRaf = requestAnimationFrame(autoScrollLoop);
    }

    function stopAutoScroll() {
        if (autoScrollRaf) {
            cancelAnimationFrame(autoScrollRaf);
            autoScrollRaf = null;
        }
        lastTimestamp = null;
    }

    function resetAutoScroll() {
        elapsedMs = 0;
        updateProgressBar(0);
        lastTimestamp = null;
    }

    // Dynamic resize handler for cards per page
    window.addEventListener('resize', () => {
        const newPerPage = getCardsPerPage();
        if (newPerPage !== CARDS_PER_PAGE) {
            CARDS_PER_PAGE = newPerPage;
            totalPages = Math.ceil(allNews.length / CARDS_PER_PAGE);
            currentPage = 0;
            buildDots();
            renderPage(0);
            updateControls();
        }
    });

    // ===== TOUCH SWIPE INTERAKSI STACK DECK SHUFFLE (KHUSUS MOBILE) =====
    let touchStartX = 0;
    let touchStartY = 0;
    let touchCurrentX = 0;
    let touchCurrentY = 0;
    let isTouchDragging = false;
    let isHorizontalSwipe = false;

    container.addEventListener('touchstart', (e) => {
        if (window.innerWidth > 768) return;
        if (!e.touches || e.touches.length === 0) return;

        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
        touchCurrentX = touchStartX;
        touchCurrentY = touchStartY;
        isTouchDragging = true;
        isHorizontalSwipe = false;

        const topCard = container.querySelector('.news-card[data-stack-idx="0"]');
        if (topCard) {
            topCard.style.transition = 'none';
        }
    }, { passive: true });

    container.addEventListener('touchmove', (e) => {
        if (!isTouchDragging || window.innerWidth > 768) return;
        if (!e.touches || e.touches.length === 0) return;

        touchCurrentX = e.touches[0].clientX;
        touchCurrentY = e.touches[0].clientY;

        const diffX = touchCurrentX - touchStartX;
        const diffY = touchCurrentY - touchStartY;

        if (!isHorizontalSwipe && Math.abs(diffX) > 8 && Math.abs(diffX) > Math.abs(diffY)) {
            isHorizontalSwipe = true;
        }

        if (isHorizontalSwipe) {
            if (e.cancelable) e.preventDefault();

            const topCard = container.querySelector('.news-card[data-stack-idx="0"]');
            const card2   = container.querySelector('.news-card[data-stack-idx="1"]');

            if (topCard) {
                const rotation = diffX * 0.08;
                topCard.style.transform = `translate(calc(-50% + ${diffX}px), calc(-50% + ${diffY * 0.15}px)) rotate(${rotation}deg)`;
            }

            if (card2) {
                const progress = Math.min(Math.abs(diffX) / 200, 1);
                const scaleVal = 0.96 + (progress * 0.04);
                const rotVal   = 3.5 - (progress * 3.5);
                const posXVal  = 14 - (progress * 14);
                const posYVal  = 8 - (progress * 8);
                card2.style.transform = `translate(calc(-50% + ${posXVal}px), calc(-50% + ${posYVal}px)) rotate(${rotVal}deg) scale(${scaleVal})`;
            }
        }
    }, { passive: false });

    function finishTouchSwipe() {
        if (!isTouchDragging || isAnimating || window.innerWidth > 768) return;
        isTouchDragging = false;

        const diffX = touchCurrentX - touchStartX;
        const topCard = container.querySelector('.news-card[data-stack-idx="0"]');
        const card2   = container.querySelector('.news-card[data-stack-idx="1"]');

        if (isHorizontalSwipe && Math.abs(diffX) > 40) {
            isAnimating = true;

            const cards = Array.from(container.querySelectorAll('.news-card'));
            const shuffleState = diffX > 0 ? 'shuffle-right' : 'shuffle';

            if (topCard) {
                topCard.style.transition = 'transform 0.42s cubic-bezier(0.2, 0.8, 0.2, 1), opacity 0.42s ease';
                topCard.style.transform = ''; // clear drag inline transform
                topCard.setAttribute('data-stack-idx', shuffleState);
            }

            // Move lower cards forward smoothly
            cards.forEach(card => {
                if (card !== topCard) {
                    card.style.transition = 'transform 0.45s cubic-bezier(0.175, 0.885, 0.32, 1.275), opacity 0.45s ease';
                    card.style.transform = '';
                    card.style.opacity = '';
                    const idx = parseInt(card.getAttribute('data-stack-idx'), 10);
                    if (!isNaN(idx) && idx > 0) {
                        card.setAttribute('data-stack-idx', idx - 1);
                    }
                }
            });

            setTimeout(() => {
                if (topCard) {
                    topCard.style.transition = 'none';
                    topCard.style.transform = '';
                    topCard.style.opacity = '';
                    topCard.setAttribute('data-stack-idx', cards.length - 1);
                    container.appendChild(topCard);

                    void topCard.offsetWidth; // Force reflow
                    topCard.style.transition = 'transform 0.45s cubic-bezier(0.175, 0.885, 0.32, 1.275), opacity 0.45s ease';
                }

                if (diffX < 0) {
                    currentPage = (currentPage + 1) % allNews.length;
                } else {
                    currentPage = (currentPage - 1 + allNews.length) % allNews.length;
                }
                updateControls();
                isAnimating = false;
            }, 400);
        } else {
            const allCards = container.querySelectorAll('.news-card');
            allCards.forEach(card => {
                card.style.transition = 'transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), opacity 0.3s ease';
                card.style.transform = '';
                card.style.opacity = '';
            });
        }

        isHorizontalSwipe = false;
    }

    container.addEventListener('touchend', finishTouchSwipe, { passive: true });
    container.addEventListener('touchcancel', finishTouchSwipe, { passive: true });

    // Pause auto-scroll HANYA saat kursor berada langsung di atas kartu (.news-card)
    container.addEventListener('mouseover', (e) => { 
        if (e.target.closest('.news-card')) {
            isHovered = true; 
            dotsEl.style.opacity = '0.7';
        }
    });
    container.addEventListener('mouseout', (e) => { 
        if (!e.relatedTarget || !e.relatedTarget.closest('.news-card')) {
            isHovered = false; 
            dotsEl.style.opacity = '1';
        }
    });

    // IntersectionObserver agar auto-scroll hanya aktif saat section berita terlihat
    const sectionContact = document.getElementById('section-contact');
    if (sectionContact && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                isSectionVisible = entry.isIntersecting;
            });
        }, { threshold: 0.2 });
        observer.observe(sectionContact);
    }

    // Pause saat tab browser tidak aktif (pindah tab)
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopAutoScroll();
        } else {
            startAutoScroll();
        }
    });

    // ===== EVENT LISTENER TOMBOL =====
    prevBtn.addEventListener('click', () => goToPage(currentPage - 1, 'prev'));
    nextBtn.addEventListener('click', () => goToPage(currentPage + 1, 'next'));

    // ===== MODAL VIEW ALL LOGIC =====
    const newsModal      = document.getElementById('newsModal');
    const newsModalClose = document.getElementById('newsModalClose');
    const newsViewAllBtn = document.getElementById('newsViewAllBtn');
    const newsModalGrid  = document.getElementById('newsModalGrid');
    const newsModalCount = document.getElementById('newsModalCount');
    const newsSearchInput  = document.getElementById('newsSearchInput');
    const newsSearchClear  = document.getElementById('newsSearchClear');
    const newsAutocomplete = document.getElementById('newsAutocomplete');

    // Populate modal cards
    function populateModal(newsList = allNews, animate = false) {
        newsModalGrid.innerHTML = '';
        newsModalCount.textContent = `Total: ${newsList.length} berita`;

        if (newsList.length === 0) {
            newsModalGrid.innerHTML = '<div class="news-autocomplete-no-match" style="grid-column: 1/-1; text-align: center; color: #94a3b8; font-size: 1.1rem; padding: 40px 0; width: 100%;">Tidak ada berita yang cocok dengan pencarian Anda.</div>';
            return;
        }

        newsList.forEach((news, i) => {
            const card = document.createElement('div');
            // Selang-seling per baris (3 kolom): atas, bawah, atas
            card.className = 'news-modal-card' + (i % 3 === 1 ? ' news-modal-card--alt' : '');
            if (!animate) {
                card.classList.add('visible');
            }
            
            const isPlaceholder = !news.image || news.isPlaceholder;
            const isContain = news.imageFit === 'contain';
            const imgStyle = isContain
                ? `object-fit: contain; padding: 12px; background: ${news.imageBg || '#1e293b'};`
                : `object-fit: cover;`;

            const imgHtml = isPlaceholder ? `
                <div class="news-modal-card-img-placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" fill="currentColor" viewBox="0 0 16 16">
                      <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                      <path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/>
                    </svg>
                    <span>IFIK News</span>
                </div>
            ` : `<img class="news-modal-card-img" src="${news.image}" alt="${news.title}" loading="lazy" style="${imgStyle}" />`;

            card.innerHTML = `
                <div class="news-modal-card-body">
                    <div class="news-modal-card-bg" style="${isContain ? 'background:' + (news.imageBg || '#1e293b') : ''}">
                        ${imgHtml}
                    </div>
                    <div class="news-modal-card-overlay"></div>
                    <div class="news-modal-card-badge">${news.date}</div>
                    <div class="news-modal-card-content">
                        <h3 class="news-modal-card-title">${news.title}</h3>
                        <p class="news-modal-card-excerpt">${news.excerpt}</p>
                    </div>
                </div>
                <button class="news-modal-card-btn" title="Baca Selengkapnya">
                    <div class="news-modal-card-btn-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="7" y1="17" x2="17" y2="7"></line>
                            <polyline points="7 7 17 7 17 17"></polyline>
                        </svg>
                    </div>
                </button>
            `;

            if (news.url) {
                card.addEventListener('click', () => window.location.href = news.url);
            }
            newsModalGrid.appendChild(card);

            if (animate) {
                setTimeout(() => {
                    card.classList.add('visible');
                }, i * 30);
            }
        });
    }

    // Open Modal
    newsViewAllBtn.addEventListener('click', () => {
        populateModal(allNews, true);
        newsModal.classList.add('open');
        if (window.lenis) window.lenis.stop();
        document.body.style.overflow = 'hidden';
    });

    // Close Modal
    function closeModal() {
        newsModal.classList.remove('open');
        if (window.lenis) window.lenis.start();
        document.body.style.overflow = '';
        const modalCards = newsModalGrid.querySelectorAll('.news-modal-card');
        modalCards.forEach(card => card.classList.remove('visible'));
        
        // Reset search input and search results
        newsSearchInput.value = '';
        newsSearchClear.style.display = 'none';
        newsAutocomplete.classList.remove('open');
    }

    newsModalClose.addEventListener('click', closeModal);
    newsModal.addEventListener('click', (e) => {
        if (e.target === newsModal) {
            closeModal();
        }
    });

    // Handle escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && newsModal.classList.contains('open')) {
            closeModal();
        }
    });

    // Search and Autocomplete functionality
    function performSearch(query) {
        const filtered = allNews.filter(news => 
            news.title.toLowerCase().includes(query.toLowerCase()) || 
            news.excerpt.toLowerCase().includes(query.toLowerCase())
        );
        populateModal(filtered, false);
    }

    function showAutocomplete(query) {
        if (!query) {
            newsAutocomplete.classList.remove('open');
            newsAutocomplete.innerHTML = '';
            return;
        }

        const matches = allNews.filter(news => 
            news.title.toLowerCase().includes(query.toLowerCase())
        ).slice(0, 5); // Limit suggestions to 5

        if (matches.length === 0) {
            newsAutocomplete.innerHTML = '<div class="news-autocomplete-no-match">Tidak ada kecocokan</div>';
        } else {
            newsAutocomplete.innerHTML = '';
            matches.forEach(news => {
                const item = document.createElement('div');
                item.className = 'news-autocomplete-item';
                
                // Highlight matching characters
                const regex = new RegExp(`(${query.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&')})`, 'gi');
                const highlighted = news.title.replace(regex, `<strong>$1</strong>`);
                
                item.innerHTML = highlighted;
                item.addEventListener('click', (e) => {
                    e.stopPropagation();
                    newsSearchInput.value = news.title;
                    performSearch(news.title);
                    newsAutocomplete.classList.remove('open');
                    newsSearchClear.style.display = 'block';
                });
                newsAutocomplete.appendChild(item);
            });
        }
        newsAutocomplete.classList.add('open');
    }

    newsSearchInput.addEventListener('input', (e) => {
        const query = e.target.value.trim();
        performSearch(query);
        showAutocomplete(query);
        newsSearchClear.style.display = query ? 'block' : 'none';
    });

    newsSearchInput.addEventListener('focus', (e) => {
        const query = e.target.value.trim();
        if (query) {
            showAutocomplete(query);
        }
    });

    // Hide dropdown on blur with delay to allow clicks to register
    newsSearchInput.addEventListener('blur', () => {
        setTimeout(() => {
            newsAutocomplete.classList.remove('open');
        }, 200);
    });

    newsSearchClear.addEventListener('click', () => {
        newsSearchInput.value = '';
        performSearch('');
        newsAutocomplete.classList.remove('open');
        newsSearchClear.style.display = 'none';
        newsSearchInput.focus();
    });

    // ===== INISIALISASI =====
    buildDots();
    renderPage(0);
    updateControls();
    startAutoScroll();
});
</script>
