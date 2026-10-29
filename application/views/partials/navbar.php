<!-- =========================================================================================
 * PARTIAL VIEW: MAIN PUBLIC NAVBAR (BILAH NAVIGASI UTAMA PUBLIK & DASHBOARD)
 * Path: application/views/partials/navbar.php
 * Pengembang: Nazril Rizki
 * =========================================================================================
 * 
 * Deskripsi:
 * Komponen bilah navigasi utama website IFIK yang tampil pada halaman depan (Beranda Publik).
 * Dirancang dengan desain modern Glassmorphism (efek kaca kabur / blur), responsivitas penuh
 * (desktop horizontal bar dan mobile frosted glass sidebar drawer), serta adaptasi menu berbasis role sesi pengguna.
 * 
 * Fitur & Komponen Utama:
 * 1. Desktop Navigation Bar: Melayang di bagian atas dengan efek backdrop-blur dan border oranye.
 * 2. Mobile Responsive Drawer: Hamburger menu yang membuka sidebar drawer kaca dengan backdrop overlay.
 * 3. Smooth Scroll Navigation: Integrasi dengan Lenis Smooth Scroll untuk navigasi ke seksi Beranda & Berita.
 * 4. Dropdown Layanan Interaktif: Center of Excellent (Mikro Credential, Study Club, Workshop) dengan animasi hover.
 * 5. Dynamic Role Redirection: Menghubungkan pengguna login langsung ke panel kerjanya masing-masing.
 * 6. User Profile & Authentication Pill: Menampilkan profil dan opsi logout untuk pengguna terotentikasi, atau tombol login untuk tamu.
 * ========================================================================================= -->
    <!-- ==================== NAVBAR (FULL) ==================== -->
    <style>
        @media (pointer: fine) {
            *, *::before, *::after, html, body, a, button, input, select, textarea, label, summary, model-viewer, model-viewer::part(default-canvas), [role="button"], [onclick] {
                cursor: none !important;
            }
        }

        /* Topbar: hanya wadah burger di mobile */
        .dashboard-topbar {
            position: fixed;
            top: 0; left: 0;
            width: 100vw; max-width: 100%;
            height: 70px;
            z-index: 100;
            display: none;              /* tampil hanya di mobile */
            align-items: center;
            justify-content: flex-start;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 2px solid #ea580c;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 0 24px;
            box-sizing: border-box;
        }

        /* Nav list: bar atas di desktop, sidebar di mobile */
        .nav-list {
            position: fixed;
            top: 0; left: 0;
            width: 100vw; max-width: 100%;
            height: 70px;
            z-index: 101;
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            align-items: center;
            justify-content: center;
            list-style: none;
            margin: 0;
            padding: 0 150px;
            box-sizing: border-box;
            gap: 28px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 2px solid #ea580c;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        /* Burger (mobile) */
        .navbar-burger {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px; height: 40px;
            flex-shrink: 0;
            border: none;
            background: transparent;
            color: #1e293b;
            padding: 0;
            z-index: 101;
        }

        .nav-item { position: relative; flex-shrink: 0; }

        /* Dorong login ke kanan (fallback untuk semua breakpoint) */
        .nav-item:has(.nav-link-login) { margin-left: auto; }

        .nav-link {
            color: #1e293b;
            background: transparent;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: none;
            padding: 8px 14px;
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
            bottom: 2px; left: 0;
            width: 0%; height: 2px;
            background: #ea580c;
            transition: width 0.3s cubic-bezier(0.25, 1, 0.5, 1);
        }
        .nav-link:hover { color: #ea580c; }
        .nav-link:hover::after { width: 100%; }

        .nav-link .btn-box {
            width: 24px; height: 24px;
            background: transparent;
            color: inherit;
            display: flex; align-items: center; justify-content: center;
            transition: transform 0.3s ease;
        }
        .nav-link:hover .btn-box { transform: scale(1.1); }

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
            animation: shine 3s linear infinite;
        }
        @keyframes shine { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
        .nav-link-login:hover { background: #c2410c; color: #ffffff; }

        .nav-link-login .btn-box {
            width: 24px; height: 24px;
            background: #ffffff; color: #ea580c;
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
        }
        .nav-link-login:hover .btn-box { transform: scale(1.22) rotate(18deg); }

        .nav-dropdown {
            position: absolute; top: 100%; left: 50%;
            transform: translateX(-50%) translateY(15px);
            margin-top: 10px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(234, 88, 12, 0.2);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(234, 88, 12, 0.15);
            opacity: 0; visibility: hidden;
            transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
            padding: 20px; z-index: 1000;
            pointer-events: none;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            white-space: nowrap;
        }

        @media (min-width: 993px) {
            .nav-list { padding: 0 150px; }   /* seimbang kiri & kanan agar menu tepat di tengah */

            .nav-item:has(.nav-link-login) {
                position: absolute;
                top: 50%; right: 24px;
                transform: translateY(-50%);
                margin-left: 0;
            }
            .nav-item:has(.nav-link-login) .nav-dropdown {
                left: auto; right: 0;
                transform: translateX(0) translateY(15px);
            }
            .nav-item:has(.nav-link-login):hover .nav-dropdown {
                transform: translateX(0) translateY(0);
            }
        }

        .user-dropdown a { min-width: 130px; }
        .nav-dropdown::before { content: ''; position: absolute; top: -15px; left: 0; width: 100%; height: 15px; }
        .nav-dropdown--right { left: auto; right: 0; transform: translateX(0) translateY(15px); }
        .nav-item:hover .nav-dropdown--right { transform: translateX(0) translateY(0) !important; }
        .nav-item:hover .nav-dropdown { opacity: 1; visibility: visible; transform: translateX(-50%) translateY(0); pointer-events: auto; }

        .nav-dropdown a {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            gap: 12px; padding: 15px 25px;
            background: #ffffff;
            border: 1px solid rgba(234, 88, 12, 0.1);
            border-radius: 12px;
            color: #475569; text-decoration: none;
            font-size: 0.85rem; font-weight: 700;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-transform: capitalize;
            box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        }
        .nav-dropdown a:hover {
            background: #ea580c; color: #ffffff;
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(234, 88, 12, 0.2);
        }
        .nav-dropdown a .btn-box {
            width: 38px; height: 38px;
            background: #fff7ed; color: #ea580c;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .nav-dropdown a:hover .btn-box {
            background: #ffffff; color: #ea580c;
            transform: scale(1.15) rotate(10deg);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        @media (max-width: 1400px) and (min-width: 993px) {
            .nav-list { gap: 24px; padding: 0 140px; }
            .nav-link { font-size: 0.78rem; letter-spacing: 0.3px; padding: 8px 12px; gap: 4px; }
            .nav-link-login { font-size: 0.8rem; padding: 7px 16px 7px 9px; }
        }
        @media (max-width: 1200px) and (min-width: 993px) {
            .nav-list { gap: 20px; padding: 0 130px; }
            .nav-link { font-size: 0.74rem; letter-spacing: 0.2px; padding: 7px 10px; gap: 3px; }
            .nav-link .btn-box { display: none; }
            .nav-link-login { font-size: 0.76rem; padding: 6px 14px 6px 8px; }
            .nav-dropdown { padding: 12px; gap: 8px; }
            .nav-dropdown a { padding: 8px 12px; }
        }
        @media (max-width: 1080px) and (min-width: 993px) {
            .nav-list { gap: 18px; padding: 0 120px; }
            .nav-link { font-size: 0.72rem; padding: 6px 8px; gap: 2px; }
            .nav-link-login { font-size: 0.72rem; padding: 6px 12px 6px 7px; }
        }

        /* ===== MOBILE: sidebar ===== */
        @media (max-width: 992px) {
            .dashboard-topbar {
                display: flex;
                padding: 0 16px;
            }

            .nav-overlay {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.45);
                z-index: 2147483645;      /* di bawah sidebar, di atas SEMUA elemen */
                opacity: 0; visibility: hidden;
                transition: opacity 0.3s ease;
            }
            .nav-overlay.active { opacity: 1; visibility: visible; }

            .nav-list {
                top: 0; left: 0;
                height: 100vh;
                width: 300px; max-width: 85vw;
                margin: 0;
                /* ✅ Background lebih transparan + efek frosted glass */
                background: rgba(255, 255, 255, 0.55);
                backdrop-filter: blur(18px) saturate(160%);
                -webkit-backdrop-filter: blur(18px) saturate(160%);
                flex-direction: column;
                align-items: stretch;
                justify-content: flex-start;
                flex-wrap: nowrap;
                gap: 2px;
                padding: 90px 20px 24px;
                overflow-y: auto;
                box-shadow: 6px 0 30px rgba(0, 0, 0, 0.18);
                border-right: 1px solid rgba(255, 255, 255, 0.4);
                border-bottom: none;
                transform: translateX(-100%);
                transition: transform 0.35s cubic-bezier(0.25, 1, 0.5, 1);
                z-index: 2147483646;      /* LAPISAN PALING ATAS */
            }
            .nav-list.nav-open { transform: translateX(0); }

            .nav-item { width: 100%; }
            .nav-item:has(.nav-link-login) {
                margin-left: 0;
                margin-top: auto;
                padding-top: 16px;
                border-top: 1px solid rgba(148, 163, 184, 0.35);
            }

            .nav-link, .nav-link-login {
                width: 100%;
                justify-content: space-between;
                padding: 14px 6px;
                font-size: 0.85rem;
            }
            .nav-link-login span:last-child { display: inline; }

            .nav-link::after { content: none; }
            .nav-item:has(> .nav-dropdown) > .nav-link::after,
            .nav-item:has(> .nav-dropdown) > .nav-link-login::after {
                content: '\203A';
                position: static;
                background: none;
                width: auto; height: auto;
                font-size: 1.2rem;
                line-height: 1;
                transform: rotate(90deg);
                transition: transform 0.3s ease;
            }
            .nav-item.dropdown-open > .nav-link::after,
            .nav-item.dropdown-open > .nav-link-login::after { transform: rotate(-90deg); }

            .nav-dropdown, .nav-dropdown--right {
                position: static;
                transform: none !important;
                opacity: 1; visibility: visible; pointer-events: auto;
                display: none;
                grid-template-columns: 1fr;
                gap: 2px;
                background: transparent;
                backdrop-filter: none;
                border: none;
                box-shadow: none;
                border-radius: 0;
                padding: 4px 0 8px 14px;
                margin-top: 0;
            }
            .nav-item.dropdown-open > .nav-dropdown { display: grid; }

            .nav-dropdown a, .user-dropdown a {
                flex-direction: row;
                justify-content: flex-start;
                padding: 10px 12px;
                box-shadow: none;
                min-width: 0;
                border-radius: 8px;
                /* sedikit transparan agar menyatu dgn sidebar */
                background: rgba(255, 255, 255, 0.6);
                border: 1px solid rgba(255, 255, 255, 0.5);
            }
            .nav-dropdown a .btn-box { width: 30px; height: 30px; }
        }

        body.no-scroll { overflow: hidden; }
    </style>

    <nav class="dashboard-topbar">
        <button type="button" class="navbar-burger" id="navbarBurger" onclick="toggleMobileNav()" aria-label="Buka menu" aria-expanded="false" aria-controls="navList">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
    </nav>

    <!-- NAV LIST di LUAR topbar -> z-index-nya berlaku di root -->
    <ul class="nav-list" id="navList">
        <li class="nav-item">
            <a href="<?= base_url() ?>" class="nav-link" onclick="scrollToDashboard(event)">
                <span>Dashboard</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="<?= base_url('#berita') ?>" class="nav-link" onclick="scrollToBeritaNav(event)">
                <span>Berita</span>
            </a>
        </li>

        <!-- <li class="nav-item">
            <a href="<?= site_url('welcome') ?>" class="nav-link"><span>Layanan LAB</span></a>
            <div class="nav-dropdown">
                <a href="<?= base_url('ajukan-booking') ?>">
                    <span class="btn-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line></svg>
                    </span>
                    <span>Peminjaman Ruang</span>
                </a>
                <a href="<?= site_url('welcome') ?>">
                    <span class="btn-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                    </span>
                    <span>Peminjaman Barang</span>
                </a>
                <a href="<?= site_url('welcome') ?>">
                    <span class="btn-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    </span>
                    <span>Pengajuan</span>
                </a>
                <a href="<?= site_url('welcome') ?>">
                    <span class="btn-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                    </span>
                    <span>Gallery Lab</span>
                </a>
            </div>
        </li>

        <li class="nav-item">
            <a href="<?= site_url('welcome') ?>" class="nav-link"><span>Layanan LAA</span></a>
            <div class="nav-dropdown">
                <a href="<?= site_url('welcome') ?>">
                    <span class="btn-box"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg></span>
                    <span>Tugas Akhir Online</span>
                </a>
                <a href="<?= site_url('welcome') ?>">
                    <span class="btn-box"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg></span>
                    <span>Kerja Praktek</span>
                </a>
                <a href="<?= site_url('welcome') ?>">
                    <span class="btn-box"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg></span>
                    <span>Perwalian</span>
                </a>
            </div>
        </li> -->

        <li class="nav-item">
            <a href="<?= site_url('welcome') ?>" class="nav-link"><span>Center of Excelent</span></a>
            <div class="nav-dropdown">
                <a href="<?= site_url('welcome') ?>">
                    <span class="btn-box"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg></span>
                    <span>Mikro Credential</span>
                </a>
                <a href="<?= site_url('welcome') ?>">
                    <span class="btn-box"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg></span>
                    <span>Sertifikasi</span>
                </a>
                <a href="<?= site_url('welcome') ?>">
                    <span class="btn-box"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg></span>
                    <span>Pelatihan</span>
                </a>
                <a href="<?= site_url('welcome') ?>">
                    <span class="btn-box"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg></span>
                    <span>Workshop</span>
                </a>
            </div>
        </li>
    <!-- 
        <li class="nav-item">
            <a href="<?= site_url('welcome') ?>" class="nav-link"><span>Ticketing</span></a>
            <div class="nav-dropdown">
                <a href="<?= site_url('welcome') ?>">
                    <span class="btn-box"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg></span>
                    <span>Research Group</span>
                </a>
            </div>
        </li> -->

        <!-- =========================================================================================
             DETEKSI PERAN (ROLE) & STATUS AUTENTIKASI PENGGUNA DARI SESI CODEIGNITER
             Mengidentifikasi apakah pengguna adalah Mahasiswa atau Dosen/Staf untuk mengatur
             visibilitas menu khusus seperti 'Peminjaman Barang' serta rute tujuan panel kerja.
             ========================================================================================= -->
        <?php
            $role_id       = (int)($this->session->userdata('role_id') ?? 0);
            $user_email    = $this->session->userdata('email') ?? '';
            $user_name_top = $this->session->userdata('name') ?? '';
            $is_mahasiswa  = ($role_id === 4 || strpos($user_email, '@student.') !== false);
            $is_role_dosen = ($role_id === 3 || in_array($role_id, [2, 6, 7, 9, 16], true));
        ?>

        <?php if ($is_role_dosen): ?>
        <li class="nav-item">
            <a href="<?= site_url('peminjaman_barang') ?>" class="nav-link"><span>Peminjaman Barang</span></a>
        </li>
        <?php endif; ?>

        <li class="nav-item">
            <a href="<?= site_url('welcome') ?>" class="nav-link"><span>Galeri Karya FIK</span></a>
        </li>

        <?php
            // =========================================================================================
            // PEMETAAN LABEL & URL TUJUAN PANEL KERJA SIVITAS AKADEMIKA BERDASARKAN ROLE ID
            // Role 1  : Pusat Kendali Admin (Admin System)
            // Role 2  : Panel Kaur (Kepala Urusan Laboratorium)
            // Role 3  : Panel Dosen / Ka. Ur / Kaprodi / Wadek (Berdasarkan Jabatan Struktural)
            // Role 5  : Panel Admin Layanan Akademik (LAA)
            // Role 6  : Panel Koordinator Tugas Akhir (Koor TA)
            // Role 9  : Panel Ketua Kelompok Keahlian (Ketua KK)
            // Role 11 : Panel Sekretariat
            // Role 15 : Panel SDM & Keuangan
            // Role 16 : Panel Program Studi
            // Role 17 : Panel Kemahasiswaan
            // Role 21 : Panel Laboran
            // Role 22 : Panel Super Administrator
            // =========================================================================================
            // ====== Tentukan label & URL panel berdasarkan role ======
            $panel_label = 'Panel Admin';
            $panel_url   = base_url('adminheader');

            if ($role_id == 22) {
                $panel_label = 'Panel Super Admin';
                $panel_url   = base_url('import-email');
            } elseif ($role_id == 21) {
                $panel_label = 'Panel Laboran';
                $panel_url   = base_url('laboran');
            } elseif ($role_id == 2) {
                $panel_label = 'Panel Kaur';
                $panel_url   = base_url('kaur');
            } elseif ($role_id == 3) {
                $user_jabatan = strtolower((string)($this->session->userdata('jabatan') ?? ''));
                if ($user_jabatan === 'kaur') {
                    $panel_label = 'Panel Ka. Ur';
                    $panel_url   = base_url('kaur');
                } elseif ($user_jabatan === 'kaprodi') {
                    $panel_label = 'Panel Kaprodi';
                    $panel_url   = base_url('kaprodi/peminjaman');
                } elseif ($user_jabatan === 'wadek') {
                    $panel_label = 'Panel Wadek';
                    $panel_url   = base_url('wadek/peminjaman');
                } else {
                    $panel_label = 'Panel Dosen';
                    $panel_url   = base_url('dosen/wali');
                }
            } elseif ($role_id == 6) {
                $panel_label = 'Panel Koordinator TA';
                $panel_url   = base_url('koordinatorta');
            } elseif ($role_id == 5) {
                $panel_label = 'Panel LAA';
                $panel_url   = base_url('adminlayanan');
            } elseif ($role_id == 9) {
                $panel_label = 'Panel Ketua KK';
                $panel_url   = base_url('ketuakk');
            } elseif ($role_id == 16) {
                $user_p = $this->session->userdata('prodi');
                $panel_label = !empty($user_p) ? 'Panel ' . $user_p : 'Panel Program Studi';
                $panel_url   = base_url('prodi/respon-ticketing');
            } elseif ($role_id == 17) {
                $panel_label = 'Panel Kemahasiswaan';
                $panel_url   = base_url('kemahasiswaan/respon-ticketing');
            } elseif ($role_id == 11) {
                $panel_label = 'Panel Sekretariat';
                $panel_url   = base_url('sekretariat/respon-ticketing');
            } elseif ($role_id == 15) {
                $panel_label = 'Panel SDM & Keuangan';
                $panel_url   = base_url('sdmkeuangan/respon-ticketing');
            }

        ?>

        <?php if ($is_mahasiswa): ?>
            <li class="nav-item">
                <a href="<?= site_url('mahasiswa') ?>" class="nav-link" style="color: #ea580c; font-weight: 800;"><span>Portal Mahasiswa</span></a>
            </li>
        <?php elseif ($this->session->userdata('logged_in')): ?>
            <li class="nav-item">
                <a href="<?= $panel_url ?>" class="nav-link"><span><?= $panel_label ?></span></a>
                <?php if ($role_id == 1): ?>
                <!-- <div class="nav-dropdown nav-dropdown--right">
                    <a href="<?= site_url('admin') ?>">
                        <span class="btn-box"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg></span>
                        <span>Pusat Kendali Admin</span>
                    </a>
                    <a href="<?= site_url('dosenwali') ?>">
                        <span class="btn-box"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg></span>
                        <span>Portal Dosen Wali</span>
                    </a>
                    <a href="<?= site_url('news/newsroom') ?>">
                        <span class="btn-box"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"></path></svg></span>
                        <span>Kelola Berita</span>
                    </a>
                    <a href="<?= site_url('adminlayanan') ?>">
                        <span class="btn-box"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg></span>
                        <span>Admin Layanan (LAA)</span>
                    </a>
                    <a href="<?= site_url('ketuakk') ?>">
                        <span class="btn-box"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"></circle><path d="M6 20v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"></path></svg></span>
                        <span>Portal Ketua KK</span>
                    </a>
                    <a href="<?= site_url('koordinatorta') ?>">
                        <span class="btn-box"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg></span>
                        <span>Portal Koor TA</span>
                    </a>
                    <a href="<?= site_url('import-email') ?>">
                        <span class="btn-box"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg></span>
                        <span>Pendaftaran Akun Baru</span>
                    </a>
                    <a href="<?= site_url('adminheader') ?>">
                        <span class="btn-box"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1 0-2.83 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg></span>
                        <span>Pengaturan Header</span>
                    </a>
                    <a href="<?= site_url('adminfooter') ?>">
                        <span class="btn-box"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="3" y1="15" x2="21" y2="15"></line></svg></span>
                        <span>Pengaturan Footer</span>
                    </a>
                </div> -->
                <?php endif; ?>
            </li>
        <?php endif; ?>

        <li class="nav-item">
            <?php if ($this->session->userdata('logged_in')): ?>
                <a href="#" class="nav-link-login user-link">
                    <span class="btn-box">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                    </span>
                    <span><?php echo $this->session->userdata('name'); ?></span>
                </a>
                <div class="nav-dropdown user-dropdown">
                    <?php if ($is_mahasiswa): ?>
                    <a href="<?= site_url('mahasiswa') ?>">
                        <span class="btn-box"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg></span>
                        <span>Portal Mahasiswa</span>
                    </a>
                    <?php else: ?>
                    <a href="<?= $panel_url ?>">
                        <span class="btn-box"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg></span>
                        <span><?= $panel_label ?></span>
                    </a>
                    <?php endif; ?>
                    <a href="<?php echo base_url('login/logout'); ?>">
                        <span class="btn-box"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg></span>
                        <span>Logout</span>
                    </a>
                </div>
            <?php else: ?>
                <a href="<?php echo base_url('login'); ?>" class="nav-link-login">
                    <span class="btn-box">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                    </span>
                    <span>Login</span>
                </a>
            <?php endif; ?>
        </li>
    </ul>

    <div class="nav-overlay" id="navOverlay" onclick="closeMobileNav()"></div>

    <script>
        /**
         * Menggulirkan tampilan halaman secara halus (smooth scroll) menuju posisi teratas (Dashboard/Hero Section).
         * Mengutamakan library Lenis Smooth Scroll jika tersedia, atau native smooth scrolling pada container.
         * Juga mereset slider hero ke slide pertama (index 0) dan menutup sidebar drawer mobile.
         * 
         * @param {Event} e Event klik tautan untuk dicegah default reload halamannya
         */
        function scrollToDashboard(e) {
            if (e) e.preventDefault();
            if (window.lenis) window.lenis.scrollTo(0, { duration: 1.2 });
            else {
                const container = document.querySelector('.dashboard-container');
                if (container) container.scrollTo({ top: 0, behavior: 'smooth' });
            }
            if (typeof window.goToSlide === 'function') window.goToSlide(0);
            closeMobileNav();
        }

        /**
         * Menggulirkan halaman secara otomatis menuju seksi konten 'Berita' (#berita atau #section-contact).
         * Memanggil fungsi global scrollToSection jika tersedia, atau native scrollIntoView.
         * Menutup sidebar drawer mobile setelah navigasi diaktifkan.
         * 
         * @param {Event} e Event klik tautan
         */
        function scrollToBeritaNav(e) {
            if (e) e.preventDefault();
            if (typeof window.scrollToSection === 'function') {
                window.scrollToSection('berita');
            } else {
                const target = document.getElementById('section-contact') || document.getElementById('section-berita');
                if (target) target.scrollIntoView({ behavior: 'smooth' });
            }
            closeMobileNav();
        }

        /**
         * Membuka atau menutup sidebar drawer navigasi pada perangkat mobile.
         * Mengubah class .nav-open pada daftar menu, menampilkan overlay latar belakang gelap (.active),
         * mengunci scrolling halaman (body.no-scroll), serta memperbarui atribut aria-expanded untuk aksesibilitas.
         */
        function toggleMobileNav() {
            var navList = document.getElementById('navList');
            var overlay = document.getElementById('navOverlay');
            var burger  = document.getElementById('navbarBurger');
            var isOpen  = navList.classList.toggle('nav-open');
            overlay.classList.toggle('active', isOpen);
            document.body.classList.toggle('no-scroll', isOpen);
            burger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        }

        /**
         * Menutup sidebar drawer navigasi mobile secara eksplisit.
         * Dipanggil saat pengguna mengeklik overlay latar belakang gelap atau mengeklik tautan navigasi.
         */
        function closeMobileNav() {
            document.getElementById('navList').classList.remove('nav-open');
            document.getElementById('navOverlay').classList.remove('active');
            document.body.classList.remove('no-scroll');
            document.getElementById('navbarBurger').setAttribute('aria-expanded', 'false');
        }

        /**
         * Inisialisasi Event Listener Navigasi saat Dokumen Selesai Dimuat:
         * 1. Mendeteksi breakpoint media query mobile (maksimal 992px).
         * 2. Mengaktifkan perilaku akordion (accordion toggle) pada sub-menu dropdown di perangkat layar sentuh.
         * 3. Menambahkan listener klik pada setiap tautan dropdown untuk otomatis menutup sidebar drawer mobile.
         */
        document.addEventListener('DOMContentLoaded', function () {
            var mq = window.matchMedia('(max-width: 992px)');
            document.querySelectorAll('#navList > .nav-item').forEach(function (item) {
                var link = item.querySelector(':scope > .nav-link, :scope > .nav-link-login');
                var dropdown = item.querySelector(':scope > .nav-dropdown');
                if (link && dropdown) {
                    link.addEventListener('click', function (e) {
                        if (mq.matches) {
                            e.preventDefault();
                            var alreadyOpen = item.classList.contains('dropdown-open');
                            document.querySelectorAll('#navList > .nav-item.dropdown-open').forEach(function (openItem) {
                                if (openItem !== item) openItem.classList.remove('dropdown-open');
                            });
                            item.classList.toggle('dropdown-open', !alreadyOpen);
                        }
                    });
                }
            });
            document.querySelectorAll('#navList .nav-dropdown a').forEach(function (link) {
                link.addEventListener('click', function () { closeMobileNav(); });
            });
        });
    </script>