<style>
    /* Global Universal Cursor Override */
    @media (pointer: fine) and (min-width: 901px) {
        *, *::before, *::after, html, body, a, button, input, select, textarea, label, summary, model-viewer, model-viewer::part(default-canvas), [role="button"], [onclick] {
            cursor: none !important;
        }
    }

    /* Topbar Container - Normal Navbar */
    .dashboard-topbar {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        max-width: 100%;
        height: 70px;
        z-index: 100;
        display: flex;
        align-items: center;
        justify-content: center; /* Menu ada di tengah di desktop */
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-bottom: 2px solid #ea580c;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        padding: 0 16px;
        box-sizing: border-box;
    }

    /* ================= DESKTOP NAVIGATION ================= */
    .nav-list {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        align-items: center;
        list-style: none;
        margin: 0;
        padding: 0;
        max-width: 100%;
    }

    .nav-item {
        position: relative;
        flex-shrink: 0;
    }

    .nav-link {
        color: #1e293b;
        background: transparent;
        font-weight: 700;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        text-decoration: none;
        padding: 6px 5px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        position: relative;
        transition: color 0.3s ease;
        white-space: nowrap;
    }

    .nav-link::after {
        content: '';
        position: absolute;
        bottom: 2px;
        left: 0;
        width: 0%;
        height: 2px;
        background: #ea580c;
        transition: width 0.3s cubic-bezier(0.25, 1, 0.5, 1);
    }

    .nav-link:hover {
        color: #ea580c;
    }

    .nav-link:hover::after {
        width: 100%;
    }

    .nav-link .btn-box {
        width: 24px;
        height: 24px;
        background: transparent;
        color: inherit;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.3s ease;
    }

    .nav-link:hover .btn-box {
        transform: scale(1.1);
    }

    /* Nav Link Login (Desktop Orange Pill) */
    .nav-link-login {
        color: #ffffff;
        background: linear-gradient(90deg, #ea580c 0%, #ff7f50 50%, #ea580c 100%);
        background-size: 200% 100%;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        text-decoration: none;
        padding: 7px 16px 7px 9px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 999px;
        box-shadow: 0 4px 14px rgba(234, 88, 12, 0.3);
        transform-origin: center;
        animation: shine 3s linear infinite;
    }

    @keyframes shine {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    
    .nav-link-login:hover {
        background: #c2410c;
        color: #ffffff;
    }

    .nav-link-login .btn-box {
        width: 24px;
        height: 24px;
        background: #ffffff;
        color: #ea580c;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
    }

    .nav-link-login:hover .btn-box {
        transform: scale(1.22) rotate(18deg);
    }

    /* Desktop Dropdown */
    .nav-dropdown {
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(15px);
        margin-top: 10px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(234, 88, 12, 0.2);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(234, 88, 12, 0.15);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
        padding: 20px;
        z-index: 1000;
        pointer-events: none;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        white-space: nowrap;
    }

    .user-dropdown a {
        min-width: 130px;
    }

    .nav-dropdown::before {
        content: '';
        position: absolute;
        top: -15px;
        left: 0;
        width: 100%;
        height: 15px;
    }

    .nav-dropdown--right {
        left: auto;
        right: 0;
        transform: translateX(0) translateY(15px);
    }

    .nav-item:hover .nav-dropdown--right {
        transform: translateX(0) translateY(0) !important;
    }

    .nav-item:hover .nav-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(0);
        pointer-events: auto;
    }

    .nav-dropdown a {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
        padding: 15px 25px;
        background: #ffffff;
        border: 1px solid rgba(234, 88, 12, 0.1);
        border-radius: 12px;
        color: #475569;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 700;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        text-transform: capitalize;
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
    }

    .nav-dropdown a:hover {
        background: #ea580c;
        color: #ffffff;
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(234, 88, 12, 0.2);
    }

    .nav-dropdown a .btn-box {
        width: 38px;
        height: 38px;
        background: #fff7ed;
        color: #ea580c;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .nav-dropdown a:hover .btn-box {
        background: #ffffff;
        color: #ea580c;
        transform: scale(1.15) rotate(10deg);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    /* Desktop Responsive Breakpoints */
    @media (max-width: 1400px) {
        .nav-list { gap: 7px; }
        .nav-link { font-size: 0.74rem; letter-spacing: 0.2px; padding: 6px 3px; gap: 4px; }
        .nav-link-login { font-size: 0.75rem; padding: 6px 12px 6px 8px; }
    }

    @media (max-width: 1200px) {
        .nav-list { gap: 5px; }
        .nav-link { font-size: 0.70rem; letter-spacing: 0px; padding: 5px 2px; gap: 3px; }
        .nav-link .btn-box { display: none; }
        .nav-link-login { font-size: 0.72rem; padding: 6px 10px 6px 6px; }
        .nav-dropdown { padding: 12px; gap: 8px; }
        .nav-dropdown a { padding: 8px 12px; }
    }

    /* ================= MOBILE NAVIGATION & SIDEBAR ================= */
    .mobile-bar {
        display: none;
        width: 100%;
        height: 100%;
        align-items: center;
        justify-content: space-between;
    }

    .mobile-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        color: #0f172a;
    }

    .mobile-brand-icon {
        width: 34px;
        height: 34px;
        background: linear-gradient(135deg, #ea580c, #f97316);
        color: #ffffff;
        font-weight: 900;
        font-size: 1.05rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 3px 10px rgba(234, 88, 12, 0.35);
        letter-spacing: -0.5px;
    }

    .mobile-brand-text {
        font-weight: 800;
        font-size: 0.95rem;
        letter-spacing: 0.5px;
        color: #0f172a;
    }

    .mobile-brand-text span {
        color: #ea580c;
    }

    .mobile-right-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Hamburger Button di Kanan */
    .mobile-hamburger-btn {
        display: flex;
        background: #fff7ed;
        border: 1.5px solid rgba(234, 88, 12, 0.35);
        width: 40px;
        height: 40px;
        border-radius: 10px;
        padding: 0;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 5px;
        cursor: pointer;
        transition: all 0.25s ease;
        box-shadow: 0 2px 8px rgba(234, 88, 12, 0.15);
    }

    .mobile-hamburger-btn:hover, .mobile-hamburger-btn:active {
        background: #ea580c;
    }

    .mobile-hamburger-btn .bar {
        display: block;
        width: 20px;
        height: 2.2px;
        background-color: #ea580c;
        border-radius: 2px;
        transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
    }

    .mobile-hamburger-btn:hover .bar, .mobile-hamburger-btn:active .bar {
        background-color: #ffffff;
    }

    .mobile-hamburger-btn.active .bar:nth-child(1) {
        transform: translateY(7.2px) rotate(45deg);
        background-color: #ea580c;
    }
    .mobile-hamburger-btn.active:hover .bar:nth-child(1) {
        background-color: #ffffff;
    }
    .mobile-hamburger-btn.active .bar:nth-child(2) {
        opacity: 0;
        transform: scale(0.2);
    }
    .mobile-hamburger-btn.active .bar:nth-child(3) {
        transform: translateY(-7.2px) rotate(-45deg);
        background-color: #ea580c;
    }
    .mobile-hamburger-btn.active:hover .bar:nth-child(3) {
        background-color: #ffffff;
    }

    /* Backdrop Overlay */
    .mobile-sidebar-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 99998;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.35s ease;
    }

    .mobile-sidebar-backdrop.open {
        opacity: 1;
        pointer-events: auto;
    }

    /* Offcanvas Sidebar Drawer (Muncul dari KIRI) */
    .mobile-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 310px;
        max-width: 86vw;
        height: 100vh;
        height: 100dvh;
        max-height: 100vh;
        max-height: 100dvh;
        background: #ffffff;
        z-index: 99999;
        box-shadow: 10px 0 40px rgba(0, 0, 0, 0.3);
        transform: translateX(-100%); /* Sembunyi di kiri luar layar */
        transition: transform 0.38s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        touch-action: pan-y;
        pointer-events: auto;
    }

    .mobile-sidebar.open {
        transform: translateX(0); /* Meluncur keluar dari kiri */
    }

    /* Sidebar Header */
    .mobile-sidebar-header {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1.5px solid #f1f5f9;
        background: #ffffff;
    }

    .mobile-sidebar-close-btn {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 1.3rem;
        line-height: 1;
    }

    .mobile-sidebar-close-btn:hover {
        background: #fee2e2;
        color: #ef4444;
        border-color: #fca5a5;
    }

    /* Sidebar Body (Scrollable Container) */
    .mobile-sidebar-body {
        flex: 1 1 0;        /* flex-grow: 1, flex-shrink: 1, flex-basis: 0 — kunci agar body mengambil sisa ruang */
        min-height: 0;       /* WAJIB di flexbox agar overflow-y bisa aktif */
        overflow-y: scroll;  /* scroll selalu tampil, bukan auto */
        overflow-x: hidden;
        -webkit-overflow-scrolling: touch;
        overscroll-behavior: contain;
        touch-action: pan-y;
        padding: 14px 16px;
        display: flex;
        flex-direction: column;
        gap: 6px;
        scrollbar-width: thin;
        scrollbar-color: #ea580c #f1f5f9;
    }

    .mobile-sidebar-body::-webkit-scrollbar {
        width: 5px;
    }
    .mobile-sidebar-body::-webkit-scrollbar-track {
        background: #f8fafc;
    }
    .mobile-sidebar-body::-webkit-scrollbar-thumb {
        background: #fdba74;
        border-radius: 10px;
    }
    .mobile-sidebar-body::-webkit-scrollbar-thumb:hover {
        background: #ea580c;
    }

    /* Single Link Item */
    .mobile-menu-item {
        display: flex;
        flex-direction: column;
        border-radius: 12px;
        overflow: hidden;
    }

    .mobile-nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        text-decoration: none;
        color: #1e293b;
        font-weight: 700;
        font-size: 0.88rem;
        transition: all 0.2s ease;
    }

    .mobile-nav-link:hover, .mobile-nav-link:active {
        background: #fff7ed;
        color: #ea580c;
        border-color: rgba(234, 88, 12, 0.3);
    }

    .mobile-nav-link .icon-box {
        width: 32px;
        height: 32px;
        background: #f8fafc;
        color: #ea580c;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .mobile-nav-link:hover .icon-box {
        background: #ea580c;
        color: #ffffff;
    }

    /* Accordion Toggle Header */
    .mobile-accordion-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 12px 14px;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        text-decoration: none;
        color: #1e293b;
        font-weight: 700;
        font-size: 0.88rem;
        cursor: pointer;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .mobile-accordion-header:hover, 
    .mobile-accordion-header.active {
        background: #fff7ed;
        color: #ea580c;
        border-color: rgba(234, 88, 12, 0.3);
    }

    .mobile-accordion-header .left-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .mobile-accordion-header .icon-box {
        width: 32px;
        height: 32px;
        background: #f8fafc;
        color: #ea580c;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .mobile-accordion-header.active .icon-box,
    .mobile-accordion-header:hover .icon-box {
        background: #ea580c;
        color: #ffffff;
    }

    .mobile-accordion-header .chevron-icon {
        width: 18px;
        height: 18px;
        color: #94a3b8;
        transition: transform 0.3s cubic-bezier(0.25, 1, 0.5, 1), color 0.2s ease;
    }

    .mobile-accordion-header.active .chevron-icon {
        transform: rotate(180deg);
        color: #ea580c;
    }

    /* Accordion Content Submenu */
    .mobile-submenu {
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        transition: max-height 0.35s cubic-bezier(0.25, 1, 0.5, 1), opacity 0.3s ease;
        display: flex;
        flex-direction: column;
        gap: 4px;
        padding: 0 4px 0 16px;
        border-left: 2px solid #fdba74;
        margin-left: 18px;
        margin-top: 4px;
    }

    .mobile-submenu.open {
        max-height: 45vh;        /* Batas tinggi supaya tidak tumpah ke bawah */
        overflow-y: auto;         /* Scroll sendiri di dalam */
        -webkit-overflow-scrolling: touch;
        overscroll-behavior: contain;
        opacity: 1;
        padding-top: 6px;
        padding-bottom: 12px;
        scrollbar-width: thin;
        scrollbar-color: #fdba74 transparent;
    }

    .mobile-sub-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 12px;
        background: #f8fafc;
        border-radius: 8px;
        color: #475569;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .mobile-sub-link:hover, .mobile-sub-link:active {
        background: #ea580c;
        color: #ffffff;
        transform: translateX(4px);
    }

    .mobile-sub-link svg {
        flex-shrink: 0;
        color: #ea580c;
        transition: color 0.2s ease;
    }

    .mobile-sub-link:hover svg {
        color: #ffffff;
    }

    /* Sidebar Footer */
    .mobile-sidebar-footer {
        flex-shrink: 0; /* WAJIB: footer tidak boleh menyusut, selalu di bawah */
        padding: 16px;
        border-top: 1.5px solid #f1f5f9;
        background: #f8fafc;
    }

    .mobile-footer-btn-login {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #ea580c, #f97316);
        color: #ffffff;
        font-weight: 800;
        font-size: 0.9rem;
        letter-spacing: 0.5px;
        text-decoration: none;
        border-radius: 12px;
        box-shadow: 0 4px 14px rgba(234, 88, 12, 0.35);
        box-sizing: border-box;
        transition: all 0.2s ease;
    }

    .mobile-footer-btn-login:hover, .mobile-footer-btn-login:active {
        background: #c2410c;
        transform: translateY(-2px);
    }

    .mobile-footer-btn-logout {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 11px;
        background: #fee2e2;
        border: 1px solid #fca5a5;
        color: #dc2626;
        font-weight: 700;
        font-size: 0.85rem;
        text-decoration: none;
        border-radius: 10px;
        box-sizing: border-box;
        transition: all 0.2s ease;
    }

    .mobile-footer-btn-logout:hover {
        background: #dc2626;
        color: #ffffff;
    }

    /* SWITCH DARI DESKTOP KE MOBILE DI LAYAR <= 900px */
    @media (max-width: 900px) {
        .dashboard-topbar {
            height: 58px;
            padding: 0 16px;
            justify-content: space-between;
        }

        .desktop-nav-list {
            display: none !important;
        }

        .mobile-bar {
            display: flex;
        }
    }
</style>

<nav class="dashboard-topbar">
    <!-- ================= MOBILE HEADER TOPBAR (Kiri: Logo, Kanan: Hamburger) ================= -->
    <div class="mobile-bar">
        <!-- Brand Logo di Kiri -->
        <a href="<?= base_url() ?>" class="mobile-brand" onclick="scrollToDashboard(event)">
            <div class="mobile-brand-icon">iF</div>
            <div class="mobile-brand-text">PORTAL <span>FIK</span></div>
        </a>

        <!-- Aksi di Kanan (Login Pill & Tombol Hamburger) -->
        <div class="mobile-right-actions">
            <?php if ($this->session->userdata('logged_in')): ?>
                <a href="<?php echo base_url('mahasiswa'); ?>" class="nav-link-login" style="padding: 5px 10px; font-size: 0.72rem; gap: 5px;">
                    <span class="btn-box" style="width: 20px; height: 20px;">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="7" r="4"></circle><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path></svg>
                    </span>
                    <span><?= explode(' ', trim($this->session->userdata('name') ?? 'User'))[0] ?></span>
                </a>
            <?php else: ?>
                <a href="<?php echo base_url('login'); ?>" class="nav-link-login" style="padding: 5px 12px; font-size: 0.74rem;">
                    <span>Masuk</span>
                </a>
            <?php endif; ?>

            <!-- Tombol Hamburger di Kanan Atas -->
            <button type="button" class="mobile-hamburger-btn" id="mobileHamburgerBtn" onclick="toggleMobileSidebar()" aria-label="Buka Menu Navigasi">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
        </div>
    </div>

    <!-- ================= DESKTOP NAVIGATION BAR (> 900px) ================= -->
    <ul class="nav-list desktop-nav-list">
        <!-- 0. Tombol Dashboard -->
        <li class="nav-item">
            <a href="<?= base_url() ?>" class="nav-link" onclick="scrollToDashboard(event)">
                <span>Dashboard</span>
            </a>
        </li>

        <!-- 1. Layanan LAB -->
        <li class="nav-item">
            <a href="<?= site_url('welcome') ?>" class="nav-link">
                <span>Layanan LAB</span>
            </a>
            <div class="nav-dropdown">
                <a href="<?= base_url('ajukan-booking') ?>">
                    <span class="btn-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="9" y1="3" x2="9" y2="21"></line>
                        </svg>
                    </span>
                    <span>Peminjaman Ruang</span>
                </a>

                <a href="<?= site_url('welcome') ?>">
                    <span class="btn-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                        </svg>
                    </span>
                    <span>Peminjaman Barang</span>
                </a>
                <a href="<?= site_url('welcome') ?>">
                    <span class="btn-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </span>
                    <span>Pengajuan</span>
                </a>
                <a href="<?= site_url('welcome') ?>">
                    <span class="btn-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                    </span>
                    <span>Gallery Lab</span>
                </a>
            </div>
        </li>

        <!-- 2. Layanan LAA -->
        <li class="nav-item">
            <a href="<?= site_url('welcome') ?>" class="nav-link">
                <span>Layanan LAA</span>
            </a>
            <div class="nav-dropdown">
                <a href="<?= site_url('welcome') ?>">
                    <span class="btn-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </span>
                    <span>Tugas Akhir Online</span>
                </a>
                <a href="<?= site_url('welcome') ?>">
                    <span class="btn-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                        </svg>
                    </span>
                    <span>Kerja Praktek</span>
                </a>
                <a href="<?= site_url('welcome') ?>">
                    <span class="btn-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </span>
                    <span>Perwalian</span>
                </a>
            </div>
        </li>

        <!-- 3. Center of Excelent -->
        <li class="nav-item">
            <a href="<?= site_url('welcome') ?>" class="nav-link">
                <span>Center of Excelent</span>
            </a>
            <div class="nav-dropdown">
                <a href="<?= site_url('welcome') ?>">
                    <span class="btn-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                    </span>
                    <span>Mikro Credential</span>
                </a>
                <a href="<?= site_url('welcome') ?>">
                    <span class="btn-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </span>
                    <span>Sertifikasi</span>
                </a>
                <a href="<?= site_url('welcome') ?>">
                    <span class="btn-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </span>
                    <span>Pelatihan</span>
                </a>
                <a href="<?= site_url('welcome') ?>">
                    <span class="btn-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </span>
                    <span>Workshop</span>
                </a>
            </div>
        </li>

        <!-- 4. Ticketing -->
        <li class="nav-item">
            <a href="<?= site_url('welcome') ?>" class="nav-link">
                <span>Ticketing</span>
            </a>
            <div class="nav-dropdown">
                <a href="<?= site_url('welcome') ?>">
                    <span class="btn-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 20h9"></path>
                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                        </svg>
                    </span>
                    <span>Research Group</span>
                </a>
            </div>
        </li>

        <!-- 5. Galeri Karya FIK -->
        <li class="nav-item">
            <a href="<?= site_url('welcome') ?>" class="nav-link">
                <span>Galeri Karya FIK</span>
            </a>
        </li>

        <!-- 6. Admin Panel / Portal Mahasiswa -->
        <?php 
            $role_id = $this->session->userdata('role_id'); 
            $is_mahasiswa = ($role_id == 5 || strpos($this->session->userdata('email') ?? '', '@student.') !== false);
        ?>
        <?php if ($is_mahasiswa): ?>
            <li class="nav-item">
                <a href="<?= site_url('mahasiswa') ?>" class="nav-link">
                    <span>Portal Mahasiswa</span>
                </a>
            </li>
        <?php else: ?>
            <li class="nav-item">
                <a href="<?= site_url('admin') ?>" class="nav-link">
                    <span>Admin Panel</span>
                </a>
                <div class="nav-dropdown nav-dropdown--right">
                    <?php if ($role_id == 1): ?>
                    <a href="<?= site_url('admin') ?>">
                        <span class="btn-box">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                        </span>
                        <span>Pusat Kendali Admin</span>
                    </a>
                    <?php endif; ?>

                    <?php if (in_array($role_id, [1, 2])): ?>
                    <a href="<?= site_url('dosenwali') ?>">
                        <span class="btn-box">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg>
                        </span>
                        <span>Portal Dosen Wali</span>
                    </a>
                    <?php endif; ?>

                    <?php if (in_array($role_id, [1, 3])): ?>
                    <a href="<?= site_url('news/newsroom') ?>">
                        <span class="btn-box">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"></path><path d="M18 14h-8"></path><path d="M15 18h-5"></path><path d="M10 6h8v4h-8V6z"></path></svg>
                        </span>
                        <span>Kelola Berita</span>
                    </a>
                    <a href="<?= site_url('adminlayanan') ?>">
                        <span class="btn-box">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        </span>
                        <span>Admin Layanan (LAA)</span>
                    </a>
                    <?php endif; ?>

                    <?php if (in_array($role_id, [1, 5])): ?>
                    <a href="<?= site_url('ketuakk') ?>">
                        <span class="btn-box">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"></circle><path d="M6 20v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"></path></svg>
                        </span>
                        <span>Portal Ketua KK</span>
                    </a>
                    <?php endif; ?>

                    <?php if (in_array($role_id, [1, 4, 6])): ?>
                    <a href="<?= site_url('koordinatorta') ?>">
                        <span class="btn-box">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                        </span>
                        <span>Portal Koor TA</span>
                    </a>
                    <?php endif; ?>

                    <?php if ($role_id == 1): ?>
                    <a href="<?= site_url('import-email') ?>">
                        <span class="btn-box">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        </span>
                        <span>Import Email & Token</span>
                    </a>
                    <a href="<?= site_url('adminheader') ?>">
                        <span class="btn-box">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1 0-2.83 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                        </span>
                        <span>Pengaturan Header</span>
                    </a>
                    <?php endif; ?>
                </div>
            </li>
        <?php endif; ?>

        <!-- 7. Login / Logout -->
        <li class="nav-item">
            <?php if ($this->session->userdata('logged_in')): ?>
                <a href="#" class="nav-link-login user-link">
                    <span class="btn-box">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                            <polyline points="10 17 15 12 10 7"></polyline>
                            <line x1="15" y1="12" x2="3" y2="12"></line>
                        </svg>
                    </span>
                    <span><?php echo $this->session->userdata('name'); ?></span>
                </a>
                <div class="nav-dropdown user-dropdown">
                    <a href="<?php echo base_url('login/logout'); ?>">
                        <span class="btn-box">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                        </span>
                        <span>Logout</span>
                    </a>
                    <a href="<?php echo base_url('mahasiswa'); ?>">
                        <span class="btn-box">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                        </span>
                        <span>Mahasiswa</span>
                    </a>
                </div>
            <?php else: ?>
                <a href="<?php echo base_url('login'); ?>" class="nav-link-login">
                    <span class="btn-box">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                            <polyline points="10 17 15 12 10 7"></polyline>
                            <line x1="15" y1="12" x2="3" y2="12"></line>
                        </svg>
                    </span>
                    <span>Login</span>
                </a>
            <?php endif; ?>
        </li>
    </ul>
</nav>

<!-- ================= MOBILE BACKDROP & OFFCANVAS SIDEBAR (MUNCUL DARI KIRI) ================= -->
<div id="mobileSidebarBackdrop" class="mobile-sidebar-backdrop" onclick="closeMobileSidebar()"></div>

<aside id="mobileSidebar" class="mobile-sidebar" data-lenis-prevent="true" data-lenis-prevent aria-label="Sidebar Menu Mobile">
    <!-- Sidebar Header -->
    <div class="mobile-sidebar-header">
        <a href="<?= base_url() ?>" class="mobile-brand" onclick="closeMobileSidebar(); scrollToDashboard(event)">
            <div class="mobile-brand-icon">iF</div>
            <div class="mobile-brand-text">PORTAL <span>FIK</span></div>
        </a>
        <button type="button" class="mobile-sidebar-close-btn" onclick="closeMobileSidebar()" aria-label="Tutup Menu">
            &times;
        </button>
    </div>

    <!-- Sidebar Body Menu List -->
    <div class="mobile-sidebar-body" data-lenis-prevent="true" data-lenis-prevent>
        <!-- 0. Dashboard -->
        <div class="mobile-menu-item">
            <a href="<?= base_url() ?>" class="mobile-nav-link" onclick="closeMobileSidebar(); scrollToDashboard(event)">
                <div class="icon-box">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                </div>
                <span>Dashboard</span>
            </a>
        </div>

        <!-- 1. Layanan LAB (Dropdown Accordion) -->
        <div class="mobile-menu-item">
            <button type="button" class="mobile-accordion-header" onclick="toggleMobileAccordion('mobileSubLab', this)">
                <div class="left-wrap">
                    <div class="icon-box">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line></svg>
                    </div>
                    <span>Layanan LAB</span>
                </div>
                <svg class="chevron-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </button>
            <div id="mobileSubLab" class="mobile-submenu">
                <a href="<?= base_url('ajukan-booking') ?>" class="mobile-sub-link" onclick="closeMobileSidebar()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line></svg>
                    <span>Peminjaman Ruang</span>
                </a>
                <a href="<?= site_url('welcome') ?>" class="mobile-sub-link" onclick="closeMobileSidebar()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                    <span>Peminjaman Barang</span>
                </a>
                <a href="<?= site_url('welcome') ?>" class="mobile-sub-link" onclick="closeMobileSidebar()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    <span>Pengajuan</span>
                </a>
                <a href="<?= site_url('welcome') ?>" class="mobile-sub-link" onclick="closeMobileSidebar()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                    <span>Gallery Lab</span>
                </a>
            </div>
        </div>

        <!-- 2. Layanan LAA (Dropdown Accordion) -->
        <div class="mobile-menu-item">
            <button type="button" class="mobile-accordion-header" onclick="toggleMobileAccordion('mobileSubLaa', this)">
                <div class="left-wrap">
                    <div class="icon-box">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                    </div>
                    <span>Layanan LAA</span>
                </div>
                <svg class="chevron-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </button>
            <div id="mobileSubLaa" class="mobile-submenu">
                <a href="<?= site_url('welcome') ?>" class="mobile-sub-link" onclick="closeMobileSidebar()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                    <span>Tugas Akhir Online</span>
                </a>
                <a href="<?= site_url('welcome') ?>" class="mobile-sub-link" onclick="closeMobileSidebar()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                    <span>Kerja Praktek</span>
                </a>
                <a href="<?= site_url('welcome') ?>" class="mobile-sub-link" onclick="closeMobileSidebar()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path></svg>
                    <span>Perwalian</span>
                </a>
            </div>
        </div>

        <!-- 3. Center of Excellence (Dropdown Accordion) -->
        <div class="mobile-menu-item">
            <button type="button" class="mobile-accordion-header" onclick="toggleMobileAccordion('mobileSubCoe', this)">
                <div class="left-wrap">
                    <div class="icon-box">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    </div>
                    <span>Center of Excellence</span>
                </div>
                <svg class="chevron-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </button>
            <div id="mobileSubCoe" class="mobile-submenu">
                <a href="<?= site_url('welcome') ?>" class="mobile-sub-link" onclick="closeMobileSidebar()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    <span>Mikro Credential</span>
                </a>
                <a href="<?= site_url('welcome') ?>" class="mobile-sub-link" onclick="closeMobileSidebar()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    <span>Sertifikasi</span>
                </a>
                <a href="<?= site_url('welcome') ?>" class="mobile-sub-link" onclick="closeMobileSidebar()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <span>Pelatihan</span>
                </a>
                <a href="<?= site_url('welcome') ?>" class="mobile-sub-link" onclick="closeMobileSidebar()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                    <span>Workshop</span>
                </a>
            </div>
        </div>

        <!-- 4. Ticketing (Dropdown Accordion) -->
        <div class="mobile-menu-item">
            <button type="button" class="mobile-accordion-header" onclick="toggleMobileAccordion('mobileSubTicket', this)">
                <div class="left-wrap">
                    <div class="icon-box">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                    </div>
                    <span>Ticketing</span>
                </div>
                <svg class="chevron-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </button>
            <div id="mobileSubTicket" class="mobile-submenu">
                <a href="<?= site_url('welcome') ?>" class="mobile-sub-link" onclick="closeMobileSidebar()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                    <span>Research Group</span>
                </a>
            </div>
        </div>

        <!-- 5. Galeri Karya FIK -->
        <div class="mobile-menu-item">
            <a href="<?= site_url('welcome') ?>" class="mobile-nav-link" onclick="closeMobileSidebar()">
                <div class="icon-box">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                </div>
                <span>Galeri Karya FIK</span>
            </a>
        </div>

        <!-- 6. Admin Panel / Portal Mahasiswa (Dropdown Accordion) -->
        <?php if ($is_mahasiswa): ?>
            <div class="mobile-menu-item">
                <a href="<?= site_url('mahasiswa') ?>" class="mobile-nav-link" onclick="closeMobileSidebar()">
                    <div class="icon-box">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    </div>
                    <span>Portal Mahasiswa</span>
                </a>
            </div>
        <?php else: ?>
            <div class="mobile-menu-item">
                <button type="button" class="mobile-accordion-header" onclick="toggleMobileAccordion('mobileSubAdmin', this)">
                    <div class="left-wrap">
                        <div class="icon-box">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                        </div>
                        <span>Admin Panel</span>
                    </div>
                    <svg class="chevron-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>
                <div id="mobileSubAdmin" class="mobile-submenu">
                    <?php if ($role_id == 1): ?>
                    <a href="<?= site_url('admin') ?>" class="mobile-sub-link" onclick="closeMobileSidebar()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                        <span>Pusat Kendali Admin</span>
                    </a>
                    <?php endif; ?>

                    <?php if (in_array($role_id, [1, 2])): ?>
                    <a href="<?= site_url('dosenwali') ?>" class="mobile-sub-link" onclick="closeMobileSidebar()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle></svg>
                        <span>Portal Dosen Wali</span>
                    </a>
                    <?php endif; ?>

                    <?php if (in_array($role_id, [1, 3])): ?>
                    <a href="<?= site_url('news/newsroom') ?>" class="mobile-sub-link" onclick="closeMobileSidebar()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2z"></path></svg>
                        <span>Kelola Berita</span>
                    </a>
                    <a href="<?= site_url('adminlayanan') ?>" class="mobile-sub-link" onclick="closeMobileSidebar()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                        <span>Admin Layanan (LAA)</span>
                    </a>
                    <?php endif; ?>

                    <?php if (in_array($role_id, [1, 5])): ?>
                    <a href="<?= site_url('ketuakk') ?>" class="mobile-sub-link" onclick="closeMobileSidebar()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"></circle><path d="M6 20v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"></path></svg>
                        <span>Portal Ketua KK</span>
                    </a>
                    <?php endif; ?>

                    <?php if (in_array($role_id, [1, 4, 6])): ?>
                    <a href="<?= site_url('koordinatorta') ?>" class="mobile-sub-link" onclick="closeMobileSidebar()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path></svg>
                        <span>Portal Koor TA</span>
                    </a>
                    <?php endif; ?>

                    <?php if ($role_id == 1): ?>
                    <a href="<?= site_url('import-email') ?>" class="mobile-sub-link" onclick="closeMobileSidebar()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        <span>Import Email & Token</span>
                    </a>
                    <a href="<?= site_url('adminheader') ?>" class="mobile-sub-link" onclick="closeMobileSidebar()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1 0-2.83 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                        <span>Pengaturan Header</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar Footer (Login / Logout) -->
    <div class="mobile-sidebar-footer">
        <?php if ($this->session->userdata('logged_in')): ?>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <div style="font-size: 0.78rem; color: #64748b; font-weight: 600; text-align: center;">
                    Masuk sebagai: <strong style="color: #0f172a;"><?= $this->session->userdata('name') ?></strong>
                </div>
                <a href="<?php echo base_url('login/logout'); ?>" class="mobile-footer-btn-logout">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    <span>Keluar Akun (Logout)</span>
                </a>
            </div>
        <?php else: ?>
            <a href="<?php echo base_url('login'); ?>" class="mobile-footer-btn-login">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                <span>Masuk ke Akun (Login)</span>
            </a>
        <?php endif; ?>
    </div>
</aside>

<script>
    function scrollToDashboard(e) {
        if (e) e.preventDefault();
        if (window.lenis) {
            window.lenis.scrollTo(0, { duration: 1.2 });
        } else {
            const container = document.querySelector('.dashboard-container');
            if (container) {
                container.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        }
        if (typeof window.goToSlide === 'function') {
            window.goToSlide(0);
        }
    }

    // TOGGLE OFFCANVAS SIDEBAR (MUNCUL DARI KIRI)
    function toggleMobileSidebar() {
        const sidebar = document.getElementById('mobileSidebar');
        const backdrop = document.getElementById('mobileSidebarBackdrop');
        const btn = document.getElementById('mobileHamburgerBtn');

        if (!sidebar) return;

        const isOpen = sidebar.classList.contains('open');
        if (isOpen) {
            closeMobileSidebar();
        } else {
            sidebar.classList.add('open');
            if (backdrop) backdrop.classList.add('open');
            if (btn) btn.classList.add('active');
            document.body.style.overflow = 'hidden'; // Kunci scroll halaman saat sidebar terbuka
            if (window.lenis) window.lenis.stop();
        }
    }

    function closeMobileSidebar() {
        const sidebar = document.getElementById('mobileSidebar');
        const backdrop = document.getElementById('mobileSidebarBackdrop');
        const btn = document.getElementById('mobileHamburgerBtn');

        if (sidebar) sidebar.classList.remove('open');
        if (backdrop) backdrop.classList.remove('open');
        if (btn) btn.classList.remove('active');
        document.body.style.overflow = ''; // Buka kembali scroll halaman
        if (window.lenis) window.lenis.start();
    }

    // Intercept wheel di level window agar mengalahkan Lenis
    window.addEventListener('wheel', function(e) {
        const sidebar = document.getElementById('mobileSidebar');
        if (!sidebar || !sidebar.classList.contains('open')) return;

        const sidebarRect = sidebar.getBoundingClientRect();
        const isInsideSidebar = (
            e.clientX >= sidebarRect.left && e.clientX <= sidebarRect.right &&
            e.clientY >= sidebarRect.top && e.clientY <= sidebarRect.bottom
        );
        if (!isInsideSidebar) return;

        e.preventDefault();
        e.stopImmediatePropagation();

        // Cek apakah kursor ada di atas submenu yang terbuka
        const openSubmenus = sidebar.querySelectorAll('.mobile-submenu.open');
        let scrolledSubmenu = false;
        openSubmenus.forEach(function(sm) {
            const smRect = sm.getBoundingClientRect();
            if (e.clientX >= smRect.left && e.clientX <= smRect.right &&
                e.clientY >= smRect.top && e.clientY <= smRect.bottom) {
                sm.scrollTop += e.deltaY;
                scrolledSubmenu = true;
            }
        });

        // Kalau tidak di atas submenu, scroll body utama sidebar
        if (!scrolledSubmenu) {
            const sbBody = sidebar.querySelector('.mobile-sidebar-body');
            if (sbBody) sbBody.scrollTop += e.deltaY;
        }
    }, { passive: false, capture: true });

    // ACCORDION DROPDOWN SUBMENU DI DALAM SIDEBAR
    function toggleMobileAccordion(submenuId, headerBtn) {
        const submenu = document.getElementById(submenuId);
        if (!submenu) return;

        const isCurrentlyOpen = submenu.classList.contains('open');

        // Opsional: Tutup accordion lain jika ingin mode single-open
        document.querySelectorAll('.mobile-submenu').forEach(sm => {
            if (sm.id !== submenuId) {
                sm.classList.remove('open');
            }
        });
        document.querySelectorAll('.mobile-accordion-header').forEach(btn => {
            if (btn !== headerBtn) {
                btn.classList.remove('active');
            }
        });

        if (isCurrentlyOpen) {
            submenu.classList.remove('open');
            if (headerBtn) headerBtn.classList.remove('active');
        } else {
            submenu.classList.add('open');
            if (headerBtn) headerBtn.classList.add('active');
            
            // Auto scroll agar seluruh menu yang baru dibuka terlihat penuh
            setTimeout(() => {
                const sbBody = document.querySelector('.mobile-sidebar-body');
                if (sbBody && headerBtn) {
                    const topPos = headerBtn.offsetTop - sbBody.offsetTop;
                    sbBody.scrollTo({ top: topPos, behavior: 'smooth' });
                }
            }, 120);
        }
    }

    // Tutup sidebar saat tombol ESC ditekan
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeMobileSidebar();
        }
    });
</script>
