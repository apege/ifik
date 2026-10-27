<?php
/**
 * Komponen Sidebar Khusus Admin Layanan (LAA)
 * Path: application/views/admin_layanan/sidebar.php
 * Selaras dengan tata letak Curved Sidebar (curved_sidebar.css & curved_sidebar.js)
 */

$isLoggedIn       = (bool)$this->session->userdata('logged_in');
$sessionRoleId    = $isLoggedIn ? (int)$this->session->userdata('role_id') : 0;
$session_name     = (string)($this->session->userdata('nama') ?: ($this->session->userdata('name') ?: ($this->session->userdata('username') ?: 'Admin Layanan')));
$session_username = (string)$this->session->userdata('username');
$session_nidn_nim = (string)($this->session->userdata('nidn_nim') ?: ($this->session->userdata('nim') ?: ''));

$curr_uri = trim(uri_string(), '/');

$laaNavGroups = [
    [
        'category' => 'Pengelolaan Tugas Akhir',
        'items' => [
            [
                // Cek dan verifikasi berkas pendaftaran proposal tugas akhir mahasiswa
                'heading' => 'Pendaftaran TA',
                'href'    => site_url('adminlayanan'),
                'icon_3d' => 'assets/images/icons_3d/daftar.png',
                'is_active' => ($curr_uri === 'adminlayanan' || $curr_uri === 'adminlayanan/index')
            ],
            [
// Verifikasi berkas pengajuan dan laporan kerja praktik mahasiswa
                'heading' => 'Verifikasi Berkas KP',
                'href'    => site_url('adminlayanan/verifikasi_kp'),
                'icon_3d' => 'assets/images/icons_3d/approval.png',
                'is_active' => (strpos($curr_uri, 'adminlayanan/verifikasi_kp') === 0 || strpos($curr_uri, 'adminlayanan/kp') === 0)
            ],
            [
                // Berikan rekomendasi sidang atau kembalikan (rollback) tahap mahasiswa jika butuh revisi
                'heading' => 'Rekomendasi & Downgrade',
                'href'    => site_url('adminlayanan/rekomendasi'),
                'icon_3d' => 'assets/images/icons_3d/approval.png',
                'is_active' => (strpos($curr_uri, 'adminlayanan/rekomendasi') === 0)
            ],
            [
                // Cek berkas kelayakan sidang, seperti bebas lab, bebas perpus, dan transkrip
                'heading' => 'Pendaftaran Sidang',
                'href'    => site_url('adminlayanan/pendaftaran_sidang'),
                'icon_3d' => 'assets/images/icons_3d/sidang.png',
                'is_active' => (strpos($curr_uri, 'adminlayanan/pendaftaran_sidang') === 0)
            ],
            [
                // Pantau daftar mahasiswa yang sudah lulus sidang dan sedang melengkapi revisi
                'heading' => 'Sudah Lulus Sidang',
                'href'    => site_url('adminlayanan/lulus_sidang'),
                'icon_3d' => 'assets/images/icons_3d/preview2.png',
                'is_active' => (strpos($curr_uri, 'adminlayanan/lulus_sidang') === 0)
            ],
            [
                // Rekap status seluruh peserta tugas akhir yang sedang aktif di fakultas
                'heading' => 'Status Peserta TA',
                'href'    => site_url('adminlayanan/status_peserta_ta'),
                'icon_3d' => 'assets/images/icons_3d/approval.png',
                'is_active' => (strpos($curr_uri, 'adminlayanan/status_peserta_ta') === 0)
            ],
            [
                // Daftar dosen pembimbing beserta kuota dan jumlah bimbingan aktifnya
                'heading' => 'Lihat Pembimbing',
                'href'    => site_url('adminlayanan/lihat_pembimbing'),
                'icon_3d' => 'assets/images/icons_3d/preview.png',
                'is_active' => (strpos($curr_uri, 'adminlayanan/lihat_pembimbing') === 0)
            ],
            [
                // Data kelulusan akhir dan persiapan berkas peserta yudisium fakultas
                'heading' => 'Yudisium',
                'href'    => site_url('adminlayanan/yudisium'),
                'icon_3d' => 'assets/images/icons_3d/approval.png',
                'is_active' => (strpos($curr_uri, 'adminlayanan/yudisium') === 0)
            ],
            [
                // Atur jadwal pelaksanaan sidang, ruang yang dipakai, dan susunan dosen penguji
                'heading' => 'Jadwal Sidang',
                'href'    => site_url('adminlayanan/jadwal_sidang'),
                'icon_3d' => 'assets/images/icons_3d/kalender.png',
                'is_active' => (strpos($curr_uri, 'adminlayanan/jadwal_sidang') === 0)
            ],
            [
                // Buat dan cetak Berita Acara Sidang (BAP) versi Fakultas maupun sinkronisasi iGracias
                'heading' => 'BAP Sidang',
                'href'    => site_url('adminlayanan/bap_sidang'),
                'icon_3d' => 'assets/images/icons_3d/tanda_tangan.png',
                'is_active' => (strpos($curr_uri, 'adminlayanan/bap_sidang') === 0)
            ],
            [
                // Reset berkas unggahan mahasiswa jika filenya rusak atau salah upload
                'heading' => 'Reset File TA',
                'href'    => site_url('adminlayanan/reset_file_ta'),
                'icon_3d' => 'assets/images/icons_3d/daftar.png',
                'is_active' => (strpos($curr_uri, 'adminlayanan/reset_file_ta') === 0)
            ],
        ]
    ],
    [
        'category' => 'Layanan & Bantuan',
        'has_divider' => true,
        'items' => [
            [
                // Obrolan langsung (live chat) untuk membantu mahasiswa atau dosen yang mengalami kendala
                'heading' => 'Bantuan & Live Chat',
                'href'    => site_url('adminlayanan/help'),
                'icon_3d' => 'assets/images/icons_3d/help_chat.png',
                'is_active' => (strpos($curr_uri, 'adminlayanan/help') === 0)
            ],
            [
                // Pengelolaan tiket bantuan dan laporan kendala administrasi akademik
                'heading' => 'Kelola Tiket LAA',
                'href'    => site_url('adminlayanan/ticketing'),
                'icon_3d' => 'assets/images/icons_3d/unit_ticketing.png',
                'is_active' => (strpos($curr_uri, 'adminlayanan/ticketing') === 0)
            ],
        ]
    ],
    [
        'category' => 'Pengaturan & Informasi',
        'has_divider' => true,
        'items' => [
            [
                // Atur syarat berkas pendaftaran TA secara fleksibel (tipe file, wajib/opsional, template)
                'heading' => 'Pengaturan Syarat Berkas',
                'href'    => site_url('adminlayanan/pengaturan_berkas'),
                'icon_3d' => 'assets/images/icons_3d/settings.png',
                'is_active' => (strpos($curr_uri, 'adminlayanan/pengaturan_berkas') === 0 && strpos($curr_uri, 'adminlayanan/pengaturan_berkas_sidang') === false)
            ],
            [
                // Atur daftar syarat dokumen yang wajib dikumpulkan saat mahasiswa mendaftar sidang
                'heading' => 'Syarat Berkas Sidang',
                'href'    => site_url('adminlayanan/pengaturan_berkas_sidang'),
                'icon_3d' => 'assets/images/icons_3d/sidang.png',
                'is_active' => (strpos($curr_uri, 'adminlayanan/pengaturan_berkas_sidang') === 0)
            ],
            [
                // Kelola jalur tugas akhir di fakultas, seperti Reguler, Capstone, MBKM, atau Fast Track
                'heading' => 'Pengaturan Jalur TA',
                'href'    => site_url('adminlayanan/pengaturan_jalur'),
                'icon_3d' => 'assets/images/icons_3d/daftar.png',
                'is_active' => (strpos($curr_uri, 'adminlayanan/pengaturan_jalur') === 0)
            ],
            [
                // Pendaftaran akun baru dan impor email civitas akademika
                'heading' => 'Pendaftaran Akun Baru',
                'href'    => site_url('import-email'),
                'icon_3d' => 'assets/images/icons_3d/email_token.png',
                'is_active' => (strpos($curr_uri, 'import-email') === 0 || strpos($curr_uri, 'importemail') === 0)
            ],
            [
                // Tulis, edit, dan kelola publikasi berita atau pengumuman fakultas
                'heading' => 'Kelola Berita',
                'href'    => site_url('adminlayanan/berita'),
                'icon_3d' => 'assets/images/icons_3d/email_token.png',
                'is_active' => (strpos($curr_uri, 'adminlayanan/berita') === 0 || strpos($curr_uri, 'news') === 0)
            ],
        ]
    ],
    [
        'category' => 'Akun',
        'has_divider' => true,
        'items' => [
            [
                // Keluar dari sistem dan hapus sesi login akun
                'heading' => 'Keluar',
                'href'    => site_url('login/logout'),
                'icon_3d' => 'assets/images/icons_3d/logout.png',
                'is_active' => false
            ]
        ]
    ]
];
?>

<!-- Curved Sidebar Stylesheet -->
<link rel="stylesheet" href="<?= base_url('assets/css/curved_sidebar.css?v=' . time()); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* =========================================================
   CURVED SIDEBAR TYPOGRAPHY & DIVIDERS (LAA MODULE)
   ========================================================= */
.curved-nav-category {
    font-size: 0.62rem;
    font-weight: 800;
    color: #78350f;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    padding: 6px 8px 3px 8px;
    margin-top: 3px;
    user-select: none;
}
.curved-nav-category.has-divider {
    border-top: 1px solid rgba(245, 158, 11, 0.2);
    margin-top: 6px;
    padding-top: 8px;
}

.curved-header-role-badge {
    display: inline-flex;
    align-items: center;
    padding: 1px 7px;
    background: rgba(245, 158, 11, 0.22);
    border: 1px solid rgba(245, 158, 11, 0.38);
    border-radius: 9999px;
    font-size: 0.60rem;
    font-weight: 800;
    color: #78350f;
    letter-spacing: 0.02em;
}

.curved-sidebar-panel {
    background: rgba(255, 255, 255, 0.85) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border-right: 1.5px solid rgba(255, 255, 255, 0.85) !important;
    border-radius: 0 20px 20px 0 !important;
    box-shadow: inset 0 2px 0 rgba(255, 255, 255, 0.9), 0 20px 45px rgba(15, 23, 42, 0.08), 8px 0 25px rgba(0, 0, 0, 0.04) !important;
}

.curved-sidebar-svg {
    fill: rgba(255, 255, 255, 0.85) !important;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
}

.curved-sidebar-toggle-btn {
    background: rgba(255, 255, 255, 0.9) !important;
    backdrop-filter: blur(12px) !important;
    -webkit-backdrop-filter: blur(12px) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.9) !important;
}

.curved-sidebar-inner {
    padding: 58px 10px 18px 10px !important;
}

.curved-nav-item {
    padding: 5px 6px !important;
    overflow: visible !important;
}

.curved-nav-content {
    gap: 8px !important;
}

.curved-nav-heading {
    font-size: 0.78rem !important;
    letter-spacing: -0.015em !important;
    white-space: nowrap !important;
}

.curved-nav-item.is-current {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
}
.curved-nav-item.is-current .curved-nav-heading,
.curved-nav-item.is-current .curved-nav-letter {
    color: #ea580c !important;
    font-weight: 800 !important;
    font-size: 0.78rem !important;
    letter-spacing: -0.015em !important;
}
.curved-nav-item.is-current .curved-nav-3d-wrap {
    transform: translateY(-1px) scale(1.08);
    filter: drop-shadow(0 4px 8px rgba(234, 88, 12, 0.22));
}

.curved-nav-item:hover {
    background: rgba(245, 158, 11, 0.12) !important;
    backdrop-filter: blur(8px) !important;
}
.curved-nav-item:hover .curved-nav-heading {
    transform: translateX(1px);
    color: #ea580c;
}

/* User Profile Card inside Sidebar */
.curved-sidebar-user-card {
    margin: 8px 0 6px 0;
    padding: 7px 9px;
    background: rgba(255, 255, 255, 0.65);
    border-radius: 10px;
    border: 1px solid rgba(226, 232, 240, 0.9);
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
}
.curved-sidebar-user-avatar {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
    color: #ffffff;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(234, 88, 12, 0.25);
}
.curved-sidebar-user-info {
    flex: 1;
    min-width: 0;
}
.curved-sidebar-user-name {
    font-size: 0.78rem;
    font-weight: 700;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.2;
}
.curved-sidebar-user-role {
    font-size: 0.62rem;
    color: #64748b;
    font-weight: 600;
    margin-top: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Push Content Layout for Admin Layanan (Unified 270px width) */
body > main,
body > div:not(.curved-sidebar-panel):not(.curved-sidebar-backdrop):not(#curvedSidebarBackdrop):not(.modal-overlay):not(#dwToast):not(#lihatBerkasContainer):not(#modalDetailSidang):not(#modalPreviewBap):not(#modalRevertPreview3),
.page-wrapper-for-sidebar,
.curved-page-container {
    width: 100%;
    min-width: 0;
    transition: margin-left 0.75s cubic-bezier(0.76, 0, 0.24, 1), width 0.75s cubic-bezier(0.76, 0, 0.24, 1);
    box-sizing: border-box;
}

@media (min-width: 992px) {
    body:not(.curved-sidebar-desktop-collapsed) > main,
    body:not(.curved-sidebar-desktop-collapsed) > div:not(.curved-sidebar-panel):not(.curved-sidebar-backdrop):not(#curvedSidebarBackdrop):not(.modal-overlay):not(#dwToast):not(#lihatBerkasContainer):not(#modalDetailSidang):not(#modalPreviewBap):not(#modalRevertPreview3),
    body:not(.curved-sidebar-desktop-collapsed) .page-wrapper-for-sidebar,
    body:not(.curved-sidebar-desktop-collapsed) .curved-page-container {
        margin-left: 270px;
        width: calc(100% - 270px);
    }
    body.curved-sidebar-desktop-collapsed > main,
    body.curved-sidebar-desktop-collapsed > div:not(.curved-sidebar-panel):not(.curved-sidebar-backdrop):not(#curvedSidebarBackdrop):not(.modal-overlay):not(#dwToast):not(#lihatBerkasContainer):not(#modalDetailSidang):not(#modalPreviewBap):not(#modalRevertPreview3),
    body.curved-sidebar-desktop-collapsed .page-wrapper-for-sidebar,
    body.curved-sidebar-desktop-collapsed .curved-page-container {
        margin-left: 0;
        width: 100%;
    }
}

/* Hamburger button clearance when sidebar is collapsed or on mobile */
@media (max-width: 991.98px) {
    .glass-header {
        padding-left: 56px !important;
    }
}
body.curved-sidebar-desktop-collapsed .glass-header {
    padding-left: 56px !important;
}
</style>

<!-- Floating Trigger Button (Top Left - Frosted Glass with Morphing Burger) -->
<button type="button" id="curvedSidebarToggle" class="curved-sidebar-toggle-btn is-active" aria-expanded="true" aria-label="Toggle Sidebar Menu" title="Buka Menu Navigasi">
    <div class="curved-sidebar-burger">
        <span></span>
        <span></span>
        <span></span>
    </div>
</button>

<!-- Backdrop Blur Overlay -->
<div id="curvedSidebarBackdrop" class="curved-sidebar-backdrop"></div>

<!-- Sliding Sidebar Panel (Left Side - 270px Width, Frosted Glass) -->
<aside id="curvedSidebarPanel" class="curved-sidebar-panel is-active" aria-label="Sidebar Navigasi Admin Layanan">
    <div class="curved-sidebar-inner">
        <!-- Top Section: Header & Nav Links -->
        <div>
            <div class="curved-sidebar-header">
                <p>NAVIGATION</p>
                <div class="curved-header-role-badge">
                    <span>Admin Layanan</span>
                </div>
            </div>
            
            <nav class="curved-sidebar-nav">
                <?php foreach ($laaNavGroups as $group): ?>
                    <div class="curved-nav-category <?= !empty($group['has_divider']) ? 'has-divider' : '' ?>">
                        <?= htmlspecialchars($group['category']); ?>
                    </div>
                    <?php foreach ($group['items'] as $item): ?>
                        <a href="<?= htmlspecialchars($item['href']); ?>" class="curved-nav-item <?= !empty($item['is_active']) ? 'is-current' : ''; ?>">
                            <div class="curved-nav-content">
                                <div class="curved-nav-3d-wrap">
                                    <img src="<?= base_url($item['icon_3d']); ?>" alt="" class="curved-nav-3d-img" loading="lazy" />
                                </div>
                                <div class="curved-nav-text">
                                    <span class="curved-nav-heading"><?= htmlspecialchars($item['heading']); ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </nav>
        </div>

        <!-- Bottom Section: User Profile Summary Card & Footer -->
        <div>
            <?php if ($isLoggedIn): ?>
            <!-- User Profile Summary Card -->
            <div class="curved-sidebar-user-card">
                <div class="curved-sidebar-user-avatar">
                    <?= strtoupper(substr($session_name ?: ($session_username ?: 'A'), 0, 1)) ?>
                </div>
                <div class="curved-sidebar-user-info">
                    <div class="curved-sidebar-user-name" title="<?= htmlspecialchars($session_name ?: 'Admin Layanan') ?>">
                        <?= htmlspecialchars($session_name ?: ($session_username ?: 'Admin Layanan')) ?>
                    </div>
                    <div class="curved-sidebar-user-role">
                        <?= htmlspecialchars($session_nidn_nim ?: '') ?><?= !empty($session_nidn_nim) ? ' • ' : '' ?>Admin Layanan (LAA)
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="curved-sidebar-footer">
                <div class="curved-sidebar-footer-brand">
                    <i class="fa-solid fa-graduation-cap text-orange-500"></i>
                    <span>Admin Layanan (LAA) • IFIK</span>
                </div>
                <span class="curved-sidebar-footer-version">v2.0</span>
            </div>
        </div>
    </div>

    <!-- Morphing Bezier Curve SVG (Right Edge of Left Sidebar) -->
    <svg id="curvedSidebarSvg" class="curved-sidebar-svg">
        <path id="curvedSidebarPath" />
    </svg>
</aside>

<!-- Curved Sidebar Script -->
<script src="<?= base_url('assets/js/curved_sidebar.js?v=' . time()); ?>"></script>

<!-- Global Custom Circle Cursor -->
<?php $this->load->view('partials/custom_cursor'); ?>
