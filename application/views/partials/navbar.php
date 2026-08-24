<style>
    /* Global Universal Cursor Override */
    @media (pointer: fine) {
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
        height: 70px;
        z-index: 100;
        display: flex;
        align-items: center;
        justify-content: center; /* Menu ada di tengah */
        background: rgba(255, 255, 255, 0.95); /* Putih solid tapi sedikit kaca */
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-bottom: 2px solid #ea580c; /* Highlight oranye jelas tapi simple */
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); /* Soft shadow */
        transition: all 0.3s ease;
    }

    .nav-list {
        display: flex;
        flex-direction: row; /* Menu menyamping (horizontal) */
        gap: 15px;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .nav-item {
        position: relative;
    }

    .nav-link {
        color: #1e293b;
        background: transparent;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        text-decoration: none;
        padding: 7px 5px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        position: relative;
        transition: color 0.3s ease;
    }

    /* Orange line from left to right on hover */
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

    /* Minimalist btn-box for transparent links */
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

    /* Nav Link Login (Preserve Old Orange Pill Style) */
    .nav-link-login {
        color: #ffffff;
        background: #ea580c;
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
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), 
                    box-shadow 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), 
                    background 0.3s ease;
        box-shadow: 0 4px 14px rgba(234, 88, 12, 0.3);
        transform-origin: center;
    }
    
    .nav-link-login:hover {
        background: #c2410c;
        transform: scale(1.1) rotate(-4deg);
        box-shadow: 0 8px 22px rgba(234, 88, 12, 0.5);
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

    /* Dropdown Menjadi Pop-down Horizontal */
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
        display: flex;
        flex-direction: row;
        gap: 15px;
        white-space: nowrap;
    }

    .nav-dropdown::before {
        content: '';
        position: absolute;
        top: -15px;
        left: 0;
        width: 100%;
        height: 15px;
    }

    /* Right-aligned dropdown (untuk item di ujung kanan navbar) */
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

    /* Dropdown Items (Card Style) */
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
        min-width: 130px;
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
        background: #fff7ed; /* orange-50 */
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

    /* Responsiveness */
    @media (max-width: 1200px) {
        .nav-list { gap: 10px; }
        .nav-link { font-size: 0.75rem; padding: 7px 2px; }
        .nav-link-login { font-size: 0.75rem; }
        .nav-dropdown { padding: 15px; gap: 10px; }
        .nav-dropdown a { padding: 10px 15px; min-width: 110px; }
    }

    @media (max-width: 992px) {
        .nav-list { gap: 5px; }
        .nav-link .btn-box { display: none; } /* Hide icons to save space */
        .nav-link { font-size: 0.7rem; gap: 4px; }
        
        .nav-link-login span:last-child { display: none; } /* Hide "Login" text */
        .nav-link-login { padding: 6px; }
        .nav-link-login .btn-box { margin: 0; }
        
        .nav-dropdown {
            flex-direction: column; /* Stack dropdown items vertically */
            left: 0;
            transform: translateX(-20px) translateY(15px);
        }
        .nav-item:hover .nav-dropdown {
            transform: translateX(-20px) translateY(0);
        }
    }
</style>

<nav class="dashboard-topbar">
    <ul class="nav-list">
        <!-- 0. Tombol Dashboard -->
        <li class="nav-item">
            <a href="<?= base_url() ?>" class="nav-link" onclick="scrollToDashboard(event)">
                <span class="btn-box">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                        <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                        <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                    </svg>
                </span>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- 1. Layanan LAB -->
        <li class="nav-item">
            <a href="<?= site_url('welcome') ?>" class="nav-link">
                <span class="btn-box">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                        <line x1="8" y1="21" x2="16" y2="21"></line>
                        <line x1="12" y1="17" x2="12" y2="21"></line>
                    </svg>
                </span>
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
                <span class="btn-box">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                </span>
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
                <span class="btn-box">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <circle cx="12" cy="12" r="6"></circle>
                        <circle cx="12" cy="12" r="2"></circle>
                    </svg>
                </span>
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
                <span class="btn-box">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"></path>
                        <path d="M13 5v2"></path>
                        <path d="M13 17v2"></path>
                        <path d="M13 11v2"></path>
                    </svg>
                </span>
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
                <span class="btn-box">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                </span>
                <span>Galeri Karya FIK</span>
            </a>
        </li>

        <!-- 6. Admin Panel -->
        <li class="nav-item">
            <a href="<?= site_url('admin') ?>" class="nav-link">
                <span class="btn-box">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                </span>
                <span>Admin Panel</span>
            </a>
            <div class="nav-dropdown nav-dropdown--right">
                <a href="<?= site_url('admin') ?>">
                    <span class="btn-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    </span>
                    <span>Pusat Kendali Admin</span>
                </a>
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
                <a href="<?= site_url('ketuakk') ?>">
                    <span class="btn-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"></circle><path d="M6 20v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"></path></svg>
                    </span>
                    <span>Portal Ketua KK</span>
                </a>
                <a href="<?= site_url('import-email') ?>">
                    <span class="btn-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    </span>
                    <span>Import Email & Token</span>
                </a>
                <a href="<?= site_url('adminheader') ?>">
                    <span class="btn-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                    </span>
                    <span>Pengaturan Header</span>
                </a>
            </div>
        </li>

        <!-- 7. Login / Logout -->
        <li class="nav-item">
            <?php if ($this->session->userdata('logged_in')): ?>
                <a href="<?= base_url('login/logout') ?>" class="nav-link-login">
                    <span class="btn-box">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                    </span>
                    <span>Logout</span>
                </a>
            <?php else: ?>
                <a href="<?= base_url('login') ?>" class="nav-link-login">
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
</script>
