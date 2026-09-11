<?php
$current_uri = trim(uri_string(), '/');
$role_id = (int)$this->session->userdata('role_id');

$active_bimbingan    = (strpos($current_uri, 'bimbingan') !== false);
$active_penguji      = (strpos($current_uri, 'penguji') !== false);
$active_tanda_tangan = (strpos($current_uri, 'tanda-tangan') !== false || strpos($current_uri, 'signature') !== false);
$active_wali         = ((strpos($current_uri, 'wali') !== false || strpos($current_uri, 'dosenwali') !== false)) && !$active_tanda_tangan;
$active_approval     = (strpos($current_uri, 'kaur') !== false || strpos($current_uri, 'approval') !== false);
$active_booking      = (strpos($current_uri, 'ajukan') !== false || strpos($current_uri, 'booking') !== false);
$active_kalender     = (strpos($current_uri, 'kalender') !== false);
$active_respon_ticketing = (strpos($current_uri, 'respon-ticketing') !== false || strpos($current_uri, 'ticketing/respon') !== false);
$active_ticketing    = (strpos($current_uri, 'ticketing') !== false && !$active_respon_ticketing);
$active_ticketing_input = (strpos($current_uri, 'ticketing/input') !== false || $current_uri === 'dosen/ticketing');
$active_ticketing_riwayat = (strpos($current_uri, 'ticketing/riwayat') !== false || strpos($current_uri, 'dosen/ticketing/detail') !== false);
?>

<!-- Curved Sidebar Stylesheet -->
<link rel="stylesheet" href="<?= base_url('assets/css/curved_sidebar.css?v=' . time()); ?>">

<style>
    /* Reset padding statis agar layout halaman dosen fleksibel dan luas */
    body {
        padding-left: 0 !important;
    }

    /* Category Section Headers */
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

    /* Active State: Hanya tulisan yang jadi oranye, tanpa kotak/border oranye */
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

    /* Ticketing Dropdown Submenu */
    .curved-nav-submenu {
        padding: 4px 6px 6px 52px;
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .curved-nav-submenu.hidden {
        display: none;
    }
    .curved-subitem {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 10px;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #64748b;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .curved-subitem:hover {
        color: #ea580c;
        background: rgba(255, 247, 237, 0.6);
        transform: translateX(2px);
    }
    .curved-subitem.is-current {
        color: #ea580c !important;
        font-weight: 800 !important;
    }

    /* User Profile Card inside Sidebar */
    .curved-sidebar-user-card {
        margin: 12px 0 10px 0;
        padding: 10px 12px;
        background: #f8fafc;
        border-radius: 14px;
        border: 1px solid rgba(226, 232, 240, 0.9);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .curved-sidebar-user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
        color: #ffffff;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(234, 88, 12, 0.25);
    }
    .curved-sidebar-user-info {
        flex: 1;
        min-width: 0;
    }
    .curved-sidebar-user-name {
        font-size: 0.82rem;
        font-weight: 700;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.2;
    }
    .curved-sidebar-user-role {
        font-size: 0.68rem;
        color: #64748b;
        font-weight: 600;
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Role Badge in Header */
    .curved-header-role-badge {
        font-size: 0.68rem;
        font-weight: 700;
        color: #ea580c;
        background: #fff7ed;
        border: 1px solid #ffedd5;
        padding: 2px 8px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
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
<aside id="curvedSidebarPanel" class="curved-sidebar-panel" aria-label="Sidebar Navigasi Dosen">
    <div class="curved-sidebar-inner">
        <!-- Top Section: Header & Nav Links -->
        <div>
            <div class="curved-sidebar-header">
                <p>Navigation</p>
                <div class="curved-header-role-badge">
                    <span><?= ($role_id === 3) ? 'Kaur / Ka Lab' : 'Portal Dosen' ?></span>
                </div>
            </div>
            
            <nav class="curved-sidebar-nav">
                <!-- Section 1: Navigasi Peran -->
                <div class="curved-nav-category">Navigasi Peran</div>

                <!-- 1. Dosen Pembimbing -->
                <a href="<?= site_url('dosen/bimbingan'); ?>" class="curved-nav-item <?= $active_bimbingan ? 'is-current' : '' ?>">
                    <div class="curved-nav-content">
                        <div class="curved-nav-3d-wrap">
                            <img src="<?= base_url('assets/images/icons_3d/daftar.png'); ?>" alt="" class="curved-nav-3d-img" loading="lazy" />
                        </div>
                        <div class="curved-nav-text">
                            <span class="curved-nav-heading">Dosen Pembimbing</span>
                        </div>
                    </div>
                </a>

                <!-- 2. Dosen Penguji -->
                <a href="<?= site_url('dosen/penguji'); ?>" class="curved-nav-item <?= $active_penguji ? 'is-current' : '' ?>">
                    <div class="curved-nav-content">
                        <div class="curved-nav-3d-wrap">
                            <img src="<?= base_url('assets/images/icons_3d/sidang.png'); ?>" alt="" class="curved-nav-3d-img" loading="lazy" />
                        </div>
                        <div class="curved-nav-text">
                            <span class="curved-nav-heading">Dosen Penguji</span>
                        </div>
                    </div>
                </a>

                <!-- 3. Dosen Wali -->
                <a href="<?= site_url('dosen/wali'); ?>" class="curved-nav-item <?= $active_wali ? 'is-current' : '' ?>">
                    <div class="curved-nav-content">
                        <div class="curved-nav-3d-wrap">
                            <img src="<?= base_url('assets/images/icons_3d/approval.png'); ?>" alt="" class="curved-nav-3d-img" loading="lazy" />
                        </div>
                        <div class="curved-nav-text">
                            <span class="curved-nav-heading">Dosen Wali</span>
                        </div>
                    </div>
                </a>

                <!-- 4. Tanda Tangan -->
                <a href="<?= site_url('dosen/tanda-tangan'); ?>" class="curved-nav-item <?= $active_tanda_tangan ? 'is-current' : '' ?>">
                    <div class="curved-nav-content">
                        <div class="curved-nav-3d-wrap">
                            <img src="<?= base_url('assets/images/icons_3d/preview.png'); ?>" alt="" class="curved-nav-3d-img" loading="lazy" />
                        </div>
                        <div class="curved-nav-text">
                            <span class="curved-nav-heading">Tanda Tangan</span>
                        </div>
                    </div>
                </a>

                <!-- Section 2: Layanan & Bantuan -->
                <div class="curved-nav-category has-divider">Layanan & Bantuan</div>

                <!-- 5. Ticketing Dropdown -->
                <div class="curved-nav-dropdown">
                    <button type="button" 
                            id="curvedTicketingToggle" 
                            onclick="toggleCurvedTicketing(event)" 
                            class="curved-nav-item w-full <?= $active_ticketing ? 'is-current' : '' ?>" 
                            style="background: transparent; border: none; text-align: left; width: 100%;">
                        <div class="curved-nav-content justify-between">
                            <div class="flex items-center gap-3.5">
                                <div class="curved-nav-3d-wrap">
                                    <img src="<?= base_url('assets/images/icons_3d/ticketing.png'); ?>" alt="" class="curved-nav-3d-img" loading="lazy" />
                                </div>
                                <span class="curved-nav-heading">Ticketing</span>
                            </div>
                            <i id="curvedTicketingChevron" class="bi bi-chevron-down text-xs text-slate-400 transition-transform duration-200 <?= $active_ticketing ? 'rotate-180 text-orange-600' : '' ?>" style="margin-right: 4px;"></i>
                        </div>
                    </button>

                    <!-- Submenu: Input Ticketing & Riwayat -->
                    <div id="curvedTicketingSubmenu" class="curved-nav-submenu <?= $active_ticketing ? '' : 'hidden' ?>">
                        <a href="<?= site_url('dosen/ticketing/input'); ?>" class="curved-subitem <?= $active_ticketing_input ? 'is-current' : '' ?>">
                            <i class="bi bi-pencil-square text-xs"></i>
                            <span>Input Ticketing</span>
                        </a>
                        <a href="<?= site_url('dosen/ticketing/riwayat'); ?>" class="curved-subitem <?= $active_ticketing_riwayat ? 'is-current' : '' ?>">
                            <i class="bi bi-clock-history text-xs"></i>
                            <span>Riwayat</span>
                        </a>
                    </div>
                </div>

                <!-- 6. Respon Ticketing -->
                <a href="<?= site_url('dosen/respon-ticketing'); ?>" class="curved-nav-item <?= $active_respon_ticketing ? 'is-current' : '' ?>">
                    <div class="curved-nav-content">
                        <div class="curved-nav-3d-wrap">
                            <img src="<?= base_url('assets/images/icons_3d/unit_ticketing.png'); ?>" alt="" class="curved-nav-3d-img" loading="lazy" />
                        </div>
                        <div class="curved-nav-text">
                            <span class="curved-nav-heading">Respon Ticketing</span>
                        </div>
                    </div>
                </a>

                <!-- Section 3: Layanan & Fasilitas -->
                <div class="curved-nav-category has-divider">Layanan & Fasilitas</div>

                <!-- 6. Approval Peminjaman (Role 3 only) -->
                <?php if ($role_id === 3): ?>
                <a href="<?= site_url('kaur/approval'); ?>" class="curved-nav-item <?= $active_approval ? 'is-current' : '' ?>">
                    <div class="curved-nav-content">
                        <div class="curved-nav-3d-wrap">
                            <img src="<?= base_url('assets/images/icons_3d/approval.png'); ?>" alt="" class="curved-nav-3d-img" loading="lazy" />
                        </div>
                        <div class="curved-nav-text">
                            <span class="curved-nav-heading">Approval Peminjaman</span>
                        </div>
                    </div>
                </a>
                <?php endif; ?>

                <!-- 7. Ajukan Peminjaman -->
                <a href="<?= site_url('ajukan-booking'); ?>" class="curved-nav-item <?= $active_booking ? 'is-current' : '' ?>">
                    <div class="curved-nav-content">
                        <div class="curved-nav-3d-wrap">
                            <img src="<?= base_url('assets/images/icons_3d/ruangan.png'); ?>" alt="" class="curved-nav-3d-img" loading="lazy" />
                        </div>
                        <div class="curved-nav-text">
                            <span class="curved-nav-heading">Ajukan Peminjaman</span>
                        </div>
                    </div>
                </a>

                <!-- 8. Kalender Jadwal -->
                <a href="<?= site_url('kalender'); ?>" class="curved-nav-item <?= $active_kalender ? 'is-current' : '' ?>">
                    <div class="curved-nav-content">
                        <div class="curved-nav-3d-wrap">
                            <img src="<?= base_url('assets/images/icons_3d/kalender.png'); ?>" alt="" class="curved-nav-3d-img" loading="lazy" />
                        </div>
                        <div class="curved-nav-text">
                            <span class="curved-nav-heading">Kalender Jadwal</span>
                        </div>
                    </div>
                </a>

                <!-- 9. Keluar -->
                <a href="<?= site_url('login/logout'); ?>" class="curved-nav-item">
                    <div class="curved-nav-content">
                        <div class="curved-nav-3d-wrap">
                            <img src="<?= base_url('assets/images/icons_3d/logout.png'); ?>" alt="" class="curved-nav-3d-img" loading="lazy" />
                        </div>
                        <div class="curved-nav-text">
                            <span class="curved-nav-heading">Keluar</span>
                        </div>
                    </div>
                </a>
            </nav>
        </div>

        <!-- Bottom Section: User Profile & Portal Info -->
        <div>
            <!-- User Profile Summary Card -->
            <div class="curved-sidebar-user-card">
                <div class="curved-sidebar-user-avatar">
                    <?= strtoupper(substr($this->session->userdata('name') ?: 'D', 0, 1)) ?>
                </div>
                <div class="curved-sidebar-user-info">
                    <div class="curved-sidebar-user-name" title="<?= htmlspecialchars($this->session->userdata('name') ?: 'Dosen') ?>">
                        <?= htmlspecialchars($this->session->userdata('name') ?: 'Dosen FIK') ?>
                    </div>
                    <div class="curved-sidebar-user-role">
                        <?= htmlspecialchars($this->session->userdata('nidn_nim') ?: 'Dosen') ?> • <?= ($role_id === 3) ? 'Kaur / Ka Lab' : 'Dosen' ?>
                    </div>
                </div>
            </div>

            <!-- Portal Branding & Version -->
            <div class="curved-sidebar-footer">
                <div class="curved-sidebar-footer-brand">
                    <i class="fa-solid fa-graduation-cap text-orange-500"></i>
                    <span>Portal Tugas Akhir • IFIK</span>
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

<!-- Curved Sidebar Core Script -->
<script src="<?= base_url('assets/js/curved_sidebar.js?v=' . time()); ?>"></script>

<script>
function toggleCurvedTicketing(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
        if (e.stopImmediatePropagation) {
            e.stopImmediatePropagation();
        }
    }
    var submenu = document.getElementById('curvedTicketingSubmenu');
    var chevron = document.getElementById('curvedTicketingChevron');
    if (submenu) {
        submenu.classList.toggle('hidden');
    }
    if (chevron) {
        chevron.classList.toggle('rotate-180');
    }
}
</script>
