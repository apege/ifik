<?php
/**
 * Curved Animated Sidebar Component (Left-Side Layout)
 * Converted from React (components/ui/sidebar.tsx)
 * 
 * Usage in any view:
 *   <?php $this->load->view('components/curved_sidebar', [
 *       'navItems' => [
 *           ['heading' => 'Dashboard', 'href' => site_url('dashboard'), 'subheading' => 'Beranda Utama Sistem', 'index' => 1],
 *           ['heading' => 'Pendaftaran TA', 'href' => site_url('koordinatorta'), 'subheading' => 'Verifikasi Berkas & Pembimbing', 'index' => 2],
 *           ['heading' => 'Sidang TA', 'href' => site_url('koordinatorta#tab3'), 'subheading' => 'Jadwal Sidang & Penilaian', 'index' => 3],
 *           ['heading' => 'Informasi Ruangan', 'href' => site_url('dashboard#ruangan'), 'subheading' => 'Ketersediaan Lab & Studio', 'index' => 4],
 *       ]
 *   ]); ?>
 */

$sessionRoleId = (int)$this->session->userdata('role_id');

// Fallback cerdas: jika belum ada role sesi tetapi berada di halaman laboran, gunakan menu Laboran
$currentUri = trim(uri_string(), '/');
if ($sessionRoleId === 0 && (strpos($currentUri, 'laboran') === 0 || strpos($currentUri, 'kelolabooking') === 0)) {
    $sessionRoleId = 2;
}

if (isset($navItems) && is_array($navItems) && !empty($navItems)) {
    $defaultNavItems = $navItems;
} else {
    switch ($sessionRoleId) {
        case 2: // Laboran
            $defaultNavItems = [
                ['category' => 'Operasional Laboratorium'],
                ['heading' => 'Approval Peminjaman', 'href' => site_url('kelolabooking'), 'icon_3d' => 'assets/images/icons_3d/approval.png'],
                ['heading' => 'Ajukan Peminjaman Ruangan', 'href' => site_url('ajukan-booking'), 'icon_3d' => 'assets/images/icons_3d/ruangan.png'],

                ['category' => 'Layanan Ticketing', 'has_divider' => true],
                ['heading' => 'Respon Ticketing Lab', 'href' => site_url('laboran/respon-ticketing'), 'icon_3d' => 'assets/images/icons_3d/ticketing.png'],
                ['heading' => 'Buat Tiket Kendala', 'href' => site_url('laboran/ticketing/input'), 'icon_3d' => 'assets/images/icons_3d/daftar.png'],
                ['heading' => 'Riwayat Tiket Saya', 'href' => site_url('laboran/ticketing/riwayat'), 'icon_3d' => 'assets/images/icons_3d/preview.png'],

                ['category' => 'Sistem & Jadwal', 'has_divider' => true],
                ['heading' => 'Import Email & Token', 'href' => site_url('importemail'), 'icon_3d' => 'assets/images/icons_3d/email_token.png'],
                ['heading' => 'Pengaturan Unit Ticketing', 'href' => site_url('admin#unit-ticketing'), 'icon_3d' => 'assets/images/icons_3d/unit_ticketing.png'],
                ['heading' => 'Kalender Jadwal', 'href' => site_url('kalender'), 'icon_3d' => 'assets/images/icons_3d/kalender.png'],
                ['heading' => 'Keluar', 'href' => site_url('login/logout'), 'icon_3d' => 'assets/images/icons_3d/logout.png'],
            ];
            break;

        case 3: // Kaur / Ka Lab
            $defaultNavItems = [
                ['heading' => 'Bimbingan TA', 'href' => site_url('dosen/bimbingan'), 'icon_3d' => 'assets/images/icons_3d/daftar.png', 'index' => 1],
                ['heading' => 'Approval Peminjaman', 'href' => site_url('kaur/approval'), 'icon_3d' => 'assets/images/icons_3d/approval.png', 'index' => 2],
                ['heading' => 'Ajukan Peminjaman Ruangan', 'href' => site_url('ajukan-booking'), 'icon_3d' => 'assets/images/icons_3d/ruangan.png', 'index' => 3],
                ['heading' => 'Respon Ticketing Lab', 'href' => site_url('kaur#ticketing'), 'icon_3d' => 'assets/images/icons_3d/ticketing.png', 'index' => 4],
                ['heading' => 'Kalender Jadwal', 'href' => site_url('kalender'), 'icon_3d' => 'assets/images/icons_3d/kalender.png', 'index' => 5],
                ['heading' => 'Keluar', 'href' => site_url('login/logout'), 'icon_3d' => 'assets/images/icons_3d/logout.png', 'index' => 6],
            ];
            break;

        case 6: // Koordinator TA
            $defaultNavItems = [
                ['heading' => 'Dashboard Utama', 'href' => site_url('koordinatorta'), 'icon_3d' => 'assets/images/icons_3d/home.png', 'index' => 1],
                ['heading' => 'Pendaftaran TA', 'href' => site_url('koordinatorta#pendaftaran'), 'icon_3d' => 'assets/images/icons_3d/daftar.png', 'index' => 2],
                ['heading' => 'Tahap Preview 2', 'href' => site_url('koordinatorta#preview2'), 'icon_3d' => 'assets/images/icons_3d/preview.png', 'index' => 3],
                ['heading' => 'Jadwal Sidang TA', 'href' => site_url('koordinatorta#sidang'), 'icon_3d' => 'assets/images/icons_3d/sidang.png', 'index' => 4],
                ['heading' => 'Ajukan Peminjaman Ruangan', 'href' => site_url('ajukan-booking'), 'icon_3d' => 'assets/images/icons_3d/ruangan.png', 'index' => 5],
                ['heading' => 'Kalender Jadwal', 'href' => site_url('kalender'), 'icon_3d' => 'assets/images/icons_3d/kalender.png', 'index' => 6],
                ['heading' => 'Keluar', 'href' => site_url('login/logout'), 'icon_3d' => 'assets/images/icons_3d/logout.png', 'index' => 7],
            ];
            break;

        case 4: // Dosen
            $defaultNavItems = [
                ['heading' => 'Menu Dosen Utama', 'href' => site_url('dosen/bimbingan'), 'icon_3d' => 'assets/images/icons_3d/home.png', 'index' => 1],
                ['heading' => 'Ajukan Peminjaman Ruangan', 'href' => site_url('ajukan-booking'), 'icon_3d' => 'assets/images/icons_3d/ruangan.png', 'index' => 2],
                ['heading' => 'Kalender Jadwal', 'href' => site_url('kalender'), 'icon_3d' => 'assets/images/icons_3d/kalender.png', 'index' => 3],
                ['heading' => 'Keluar', 'href' => site_url('login/logout'), 'icon_3d' => 'assets/images/icons_3d/logout.png', 'index' => 4],
            ];
            break;

        case 1: // Admin System
            $defaultNavItems = [
                ['heading' => 'Dashboard Control', 'href' => site_url('admin'), 'icon_3d' => 'assets/images/icons_3d/home.png', 'index' => 1],
                ['heading' => 'Approval Peminjaman', 'href' => site_url('kelolabooking'), 'icon_3d' => 'assets/images/icons_3d/approval.png', 'index' => 2],
                ['heading' => 'Ajukan Peminjaman Ruangan', 'href' => site_url('ajukan-booking'), 'icon_3d' => 'assets/images/icons_3d/ruangan.png', 'index' => 3],
                ['heading' => 'Import Email & Token', 'href' => site_url('importemail'), 'icon_3d' => 'assets/images/icons_3d/email_token.png', 'index' => 4],
                ['heading' => 'Pengaturan Unit Ticketing', 'href' => site_url('admin#unit-ticketing'), 'icon_3d' => 'assets/images/icons_3d/unit_ticketing.png', 'index' => 5],
                ['heading' => 'Respon Ticketing Lab', 'href' => site_url('laboran#ticketing'), 'icon_3d' => 'assets/images/icons_3d/ticketing.png', 'index' => 6],
                ['heading' => 'Kalender Jadwal', 'href' => site_url('kalender'), 'icon_3d' => 'assets/images/icons_3d/kalender.png', 'index' => 7],
                ['heading' => 'Keluar', 'href' => site_url('login/logout'), 'icon_3d' => 'assets/images/icons_3d/logout.png', 'index' => 8],
            ];
            break;

        default: // Mahasiswa (5) / Publik
            $defaultNavItems = [
                ['heading' => 'Dashboard Utama', 'href' => site_url('dashboard'), 'icon_3d' => 'assets/images/icons_3d/home.png', 'index' => 1],
                ['heading' => 'Ajukan Peminjaman Ruangan', 'href' => site_url('ajukan-booking'), 'icon_3d' => 'assets/images/icons_3d/ruangan.png', 'index' => 2],
                ['heading' => 'Kalender Jadwal', 'href' => site_url('kalender'), 'icon_3d' => 'assets/images/icons_3d/kalender.png', 'index' => 3],
                ['heading' => 'Keluar', 'href' => site_url('login/logout'), 'icon_3d' => 'assets/images/icons_3d/logout.png', 'index' => 4],
            ];
            break;
    }
}
?>

<!-- Curved Sidebar Stylesheet -->
<link rel="stylesheet" href="<?= base_url('assets/css/curved_sidebar.css?v=' . time()); ?>">

<style>
    /* Category Section Headers / Tagline */
    .curved-nav-category {
        font-size: 0.68rem;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        padding: 8px 10px 4px 10px;
        margin-top: 4px;
        user-select: none;
    }
    .curved-nav-category.has-divider {
        border-top: 1px solid rgba(241, 245, 249, 0.95);
        margin-top: 8px;
        padding-top: 10px;
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
    }
    .curved-nav-item.is-current .curved-nav-3d-wrap {
        transform: translateY(-1px) scale(1.08);
        filter: drop-shadow(0 4px 8px rgba(234, 88, 12, 0.22));
    }
</style>

<!-- Floating Trigger Button (Top Left) -->
<button type="button" id="curvedSidebarToggle" class="curved-sidebar-toggle-btn" aria-label="Toggle Sidebar Menu" title="Buka Menu Navigasi">
    <div class="curved-sidebar-burger">
        <span></span>
        <span></span>
        <span></span>
    </div>
</button>

<!-- Backdrop Blur Overlay -->
<div id="curvedSidebarBackdrop" class="curved-sidebar-backdrop"></div>

<!-- Sliding Sidebar Panel with Morphing Curved SVG (Left Side) -->
<aside id="curvedSidebarPanel" class="curved-sidebar-panel" aria-label="Sidebar Navigasi">
    <div class="curved-sidebar-inner">
        <!-- Top Section: Header & Nav Links -->
        <div>
            <div class="curved-sidebar-header">
                <p>Navigation</p>
            </div>
            
            <nav class="curved-sidebar-nav">
                <?php 
                $curr_uri = trim(uri_string(), '/');
                $navCount = 1;
                foreach ($defaultNavItems as $idx => $item): 
                    if (isset($item['category'])):
                ?>
                    <div class="curved-nav-category <?= !empty($item['has_divider']) ? 'has-divider' : '' ?>">
                        <?= htmlspecialchars($item['category']); ?>
                    </div>
                <?php 
                    continue;
                    endif;

                    $num = isset($item['index']) ? sprintf('%02d', $item['index']) : sprintf('%02d', $navCount++);
                    $icon = isset($item['icon']) ? $item['icon'] : null;
                    $icon3d = isset($item['icon_3d']) ? $item['icon_3d'] : null;

                    $cleanHref = trim(str_replace([site_url(), base_url()], '', $item['href']), '/');
                    $isCurrent = (!empty($cleanHref) && ($curr_uri === $cleanHref || strpos($curr_uri, $cleanHref) === 0));
                ?>
                    <a href="<?= htmlspecialchars($item['href']); ?>" class="curved-nav-item <?= $isCurrent ? 'is-current' : '' ?>">
                        <div class="curved-nav-content">
                            <?php if (!empty($icon3d)): ?>
                                <div class="curved-nav-3d-wrap">
                                    <img src="<?= base_url($icon3d); ?>" alt="" class="curved-nav-3d-img" loading="lazy" />
                                </div>
                            <?php elseif (!empty($icon)): ?>
                                <span class="curved-nav-icon"><i class="<?= htmlspecialchars($icon); ?>"></i></span>
                            <?php else: ?>
                                <span class="curved-nav-index"><?= $num ?>.</span>
                            <?php endif; ?>
                            <div class="curved-nav-text">
                                <span class="curved-nav-heading"><?= htmlspecialchars($item['heading']); ?></span>
                                <?php if (!empty($item['subheading'])): ?>
                                    <div class="curved-nav-subheading"><?= htmlspecialchars($item['subheading']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </nav>
        </div>

        <!-- Bottom Section: Portal Info & Version -->
        <div class="curved-sidebar-footer">
            <div class="curved-sidebar-footer-brand">
                <i class="fa-solid fa-graduation-cap text-orange-500"></i>
                <span>Portal Tugas Akhir • IFIK</span>
            </div>
            <span class="curved-sidebar-footer-version">v2.0</span>
        </div>
    </div>

    <!-- Morphing Bezier Curve SVG (Right Edge of Left Sidebar) -->
    <svg id="curvedSidebarSvg" class="curved-sidebar-svg">
        <path id="curvedSidebarPath" />
    </svg>
</aside>

<!-- Curved Sidebar Core Script -->
<script src="<?= base_url('assets/js/curved_sidebar.js?v=' . time()); ?>"></script>
