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

$isLoggedIn    = (bool)$this->session->userdata('logged_in');
$sessionRoleId = $isLoggedIn ? (int)$this->session->userdata('role_id') : 0;
$sessionEmail  = (string)$this->session->userdata('email');
$currentUri    = trim(uri_string(), '/');

// Tentukan active role ID (utamakan session role jika user sudah login)
$activeRoleId = $sessionRoleId;

// Jika role 2 tapi akun khusus laboran, arahkan ke role 21 (Laboran)
if ($isLoggedIn && $sessionRoleId === 2 && strpos(strtolower($sessionEmail), 'laboran') !== false) {
    $activeRoleId = 21;
}

// Fallback deteksi URI hanya jika user sudah login tapi sessionRoleId belum match
if ($isLoggedIn && $activeRoleId === 0) {
    if (strpos($currentUri, 'laboran') === 0 || strpos($currentUri, 'peminjamanbarang') === 0) {
        $activeRoleId = 21; // Laboran
    } elseif (strpos($currentUri, 'kaur') === 0) {
        $activeRoleId = 2; // Kaur / Ka Lab
    } elseif (strpos($currentUri, 'dosen') === 0 || strpos($currentUri, 'dosenwali') === 0) {
        $activeRoleId = 3; // Dosen
    } elseif (strpos($currentUri, 'koordinatorta') === 0 || strpos($currentUri, 'koordinator') === 0) {
        $activeRoleId = 6; // Koordinator TA
    } elseif (strpos($currentUri, 'adminlayanan') === 0) {
        $activeRoleId = 5; // Admin LAA
    } elseif (strpos($currentUri, 'ketuakk') === 0) {
        $activeRoleId = 9; // Ketua KK
    } elseif (strpos($currentUri, 'mahasiswa') === 0) {
        $activeRoleId = 4; // Mahasiswa
    } elseif (strpos($currentUri, 'kemahasiswaan') === 0) {
        $activeRoleId = 17; // Kemahasiswaan
    } elseif (strpos($currentUri, 'sekretariat') === 0) {
        $activeRoleId = 11; // Sekretariat
    } elseif (strpos($currentUri, 'sdmkeuangan') === 0 || strpos($currentUri, 'sdm') === 0) {
        $activeRoleId = 15; // SDM dan Keuangan
    } elseif (strpos($currentUri, 'prodi') === 0) {
        $activeRoleId = 16; // Program Studi
    } elseif (strpos($currentUri, 'admin') === 0 || strpos($currentUri, 'kelolabooking') === 0) {
        $activeRoleId = 1; // Admin
    } elseif (strpos($currentUri, 'importemail') === 0 || strpos($currentUri, 'import-email') === 0) {
        $ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        if (strpos($ref, 'admin') !== false) {
            $activeRoleId = 1; // Admin
        } else {
            $activeRoleId = 21; // Laboran
        }
    } else {
        $activeRoleId = 0; // Publik
    }
}

$roleBadgeMap = [
    0 => 'Publik / Tamu',
    1 => 'Panel Admin',
    2 => 'Ka. Ur / Ka Lab',
    3 => 'Portal Dosen',
    4 => 'Mahasiswa',
    5 => 'Admin Layanan',
    6 => 'Koordinator TA',
    7 => 'PIC KK',
    9 => 'Ketua KK',
    11 => 'Sekretariat',
    15 => 'SDM dan Keuangan',
    16 => 'Program Studi',
    17 => 'Kemahasiswaan',
    21 => 'Laboran',
    22 => 'Panel Super Admin'
];
$activeRoleBadge = $roleBadgeMap[$activeRoleId] ?? 'Portal IFIK';

if (isset($navItems) && is_array($navItems) && !empty($navItems)) {
    $defaultNavItems = $navItems;
} else {
    switch ($activeRoleId) {
        case 22: // Super Admin (Khusus Import Akun)
            $defaultNavItems = [
                ['category' => 'Super Admin'],
                ['heading' => 'Pendaftaran Akun Baru', 'href' => site_url('import-email'), 'icon_3d' => 'assets/images/icons_3d/email_token.png'],

                ['category' => 'Akun', 'has_divider' => true],
                ['heading' => 'Keluar', 'href' => site_url('login/logout'), 'icon_3d' => 'assets/images/icons_3d/logout.png'],
            ];
            break;
        case 21: // Laboran (Staff Operasional Laboratorium)
            $defaultNavItems = [
                ['category' => 'Peminjaman Ruangan & Lab'],
                ['heading' => 'Approval Peminjaman Ruangan', 'href' => site_url('laboran/booking'), 'icon_3d' => 'assets/images/icons_3d/approval.png'],
                ['heading' => 'Ajukan Peminjaman Ruangan', 'href' => site_url('ajukan-booking'), 'icon_3d' => 'assets/images/icons_3d/ruangan.png'],
                ['heading' => 'Riwayat Booking Saya', 'href' => site_url('riwayat-booking'), 'icon_3d' => 'assets/images/icons_3d/riwayat_booking.png'],
                ['heading' => 'Tanda Tangan Digital', 'href' => site_url('laboran/tanda-tangan'), 'icon_3d' => 'assets/images/icons_3d/tanda_tangan.png'],
                ['heading' => 'Pendaftaran Akun Baru', 'href' => site_url('laboran/import-email'), 'icon_3d' => 'assets/images/icons_3d/email_token.png'],

                ['category' => 'Peminjaman Barang & Aset', 'has_divider' => true],
                ['heading' => 'Kalender & Jadwal Barang', 'href' => site_url('peminjaman_barang/kalender'), 'icon_3d' => 'assets/images/icons_3d/kalender.png'],
                ['heading' => 'Katalog & Pengajuan Alat', 'href' => site_url('peminjaman_barang'), 'icon_3d' => 'assets/images/icons_3d/ruangan.png'],
                ['heading' => 'Kelola Master Barang', 'href' => site_url('admin/barang'), 'icon_3d' => 'assets/images/icons_3d/kelola_barang.png'],
                ['heading' => 'Scanner QR Serah Terima', 'href' => site_url('peminjamanbarang/scanner'), 'icon_3d' => 'assets/images/icons_3d/preview2.png'],

                ['category' => 'Layanan Ticketing & Bantuan', 'has_divider' => true],
                ['heading' => 'Bantuan & Live Chat Lab', 'href' => site_url('laboran/help'), 'icon_3d' => 'assets/images/icons_3d/help_chat.png'],
                ['heading' => 'Respon Ticketing Lab', 'href' => site_url('laboran/respon-ticketing'), 'icon_3d' => 'assets/images/icons_3d/unit_ticketing.png'],
                ['heading' => 'Buat Tiket Kendala', 'href' => site_url('laboran/ticketing/input'), 'icon_3d' => 'assets/images/icons_3d/ticketing.png'],
                ['heading' => 'Riwayat Tiket Saya', 'href' => site_url('laboran/ticketing/riwayat'), 'icon_3d' => 'assets/images/icons_3d/preview.png'],
                ['heading' => 'Pengaturan Input Tiket', 'href' => site_url('laboran/ticketing/fields'), 'icon_3d' => 'assets/images/icons_3d/unit_ticketing.png'],

                ['category' => 'Informasi & Jadwal', 'has_divider' => true],
                ['heading' => 'Kalender Jadwal', 'href' => site_url('kalender'), 'icon_3d' => 'assets/images/icons_3d/kalender.png'],
                ['heading' => 'Keluar', 'href' => site_url('login/logout'), 'icon_3d' => 'assets/images/icons_3d/logout.png'],
            ];
            break;

        case 2: // Kaur / Ka Lab (Kepala Urusan / Kepala Lab & Dosen) - Full Super Admin Access
            $defaultNavItems = [
                ['category' => 'Persetujuan Resmi & Lab'],
                ['heading' => 'Approval Ruangan', 'href' => site_url('kaur/approval'), 'icon_3d' => 'assets/images/icons_3d/approval.png'],
                ['heading' => 'Approval Peminjaman Barang', 'href' => site_url('kaur/barang'), 'icon_3d' => 'assets/images/icons_3d/ruangan.png'],
                ['heading' => 'Kelola Master Barang', 'href' => site_url('admin/barang'), 'icon_3d' => 'assets/images/icons_3d/kelola_barang.png'],
                ['heading' => 'Ajukan Peminjaman Ruangan', 'href' => site_url('ajukan-booking'), 'icon_3d' => 'assets/images/icons_3d/ruangan.png'],
                ['heading' => 'Riwayat Booking Saya', 'href' => site_url('riwayat-booking'), 'icon_3d' => 'assets/images/icons_3d/riwayat_booking.png'],
                ['heading' => 'Tanda Tangan Digital', 'href' => site_url('kaur/tanda-tangan'), 'icon_3d' => 'assets/images/icons_3d/tanda_tangan.png'],

                ['category' => 'Manajemen Akun & User', 'has_divider' => true],
                ['heading' => 'Pendaftaran Akun Baru', 'href' => site_url('import-email'), 'icon_3d' => 'assets/images/icons_3d/email_token.png'],

                ['category' => 'Portal Akademik & Dosen', 'has_divider' => true],
                ['heading' => 'Dosen Pembimbing', 'href' => site_url('dosen/bimbingan'), 'icon_3d' => 'assets/images/icons_3d/daftar.png'],
                ['heading' => 'Dosen Wali', 'href' => site_url('dosen/wali'), 'icon_3d' => 'assets/images/icons_3d/approval.png'],

                ['category' => 'Layanan & Bantuan', 'has_divider' => true],
                ['heading' => 'Bantuan & Live Chat', 'href' => site_url('kaur/help'), 'icon_3d' => 'assets/images/icons_3d/help_chat.png'],
                ['heading' => 'Respon Ticketing Kaur', 'href' => site_url('kaur/respon-ticketing'), 'icon_3d' => 'assets/images/icons_3d/unit_ticketing.png'],
                ['heading' => 'Buat Tiket Kendala', 'href' => site_url('dosen/ticketing/input'), 'icon_3d' => 'assets/images/icons_3d/ticketing.png'],
                ['heading' => 'Riwayat Ticketing', 'href' => site_url('dosen/ticketing/riwayat'), 'icon_3d' => 'assets/images/icons_3d/preview.png'],

                ['category' => 'Informasi & Jadwal', 'has_divider' => true],
                ['heading' => 'Kalender Jadwal Ruangan', 'href' => site_url('kalender'), 'icon_3d' => 'assets/images/icons_3d/kalender.png'],
                ['heading' => 'Kalender Jadwal Barang', 'href' => site_url('peminjaman_barang/kalender'), 'icon_3d' => 'assets/images/icons_3d/kalender.png'],
                ['heading' => 'Keluar', 'href' => site_url('login/logout'), 'icon_3d' => 'assets/images/icons_3d/logout.png'],
            ];
            break;

        case 6: // Koordinator TA
            $defaultNavItems = [
                ['category' => 'Pengelolaan Tugas Akhir'],
                ['heading' => 'Pendaftaran TA', 'href' => site_url('koordinatorta#pendaftaran'), 'icon_3d' => 'assets/images/icons_3d/daftar.png'],
                ['heading' => 'Tahap Preview 2', 'href' => site_url('koordinatorta#preview2'), 'icon_3d' => 'assets/images/icons_3d/preview2.png'],
                ['heading' => 'Jadwal Sidang TA', 'href' => site_url('koordinatorta#sidang'), 'icon_3d' => 'assets/images/icons_3d/sidang.png'],
                ['heading' => 'Monitoring Status Peserta', 'href' => site_url('koordinatorta/monitoring'), 'icon_3d' => 'assets/images/icons_3d/approval.png'],

                ['category' => 'Manajemen Akun Koordinator', 'has_divider' => true],
                ['heading' => 'Pendaftaran Akun Baru', 'href' => site_url('import-email'), 'icon_3d' => 'assets/images/icons_3d/email_token.png'],

                ['category' => 'Layanan Ticketing & Bantuan', 'has_divider' => true],
                ['heading' => 'Bantuan & Live Chat', 'href' => site_url('koordinatorta/help'), 'icon_3d' => 'assets/images/icons_3d/help_chat.png'],
                ['heading' => 'Buat Tiket Kendala', 'href' => site_url('dosen/ticketing/input'), 'icon_3d' => 'assets/images/icons_3d/ticketing.png'],
                ['heading' => 'Riwayat Tiket Saya', 'href' => site_url('dosen/ticketing/riwayat'), 'icon_3d' => 'assets/images/icons_3d/preview.png'],

                ['category' => 'Fasilitas & Jadwal', 'has_divider' => true],
                ['heading' => 'Ajukan Peminjaman Ruangan', 'href' => site_url('ajukan-booking'), 'icon_3d' => 'assets/images/icons_3d/ruangan.png'],
                ['heading' => 'Riwayat Booking Saya', 'href' => site_url('riwayat-booking'), 'icon_3d' => 'assets/images/icons_3d/riwayat_booking.png'],
                ['heading' => 'Kalender Jadwal', 'href' => site_url('kalender'), 'icon_3d' => 'assets/images/icons_3d/kalender.png'],
                ['heading' => 'Keluar', 'href' => site_url('login/logout'), 'icon_3d' => 'assets/images/icons_3d/logout.png'],
            ];
            break;

        case 3: // Dosen
            $defaultNavItems = [
                ['category' => 'Bimbingan & Pengujian'],
                ['heading' => 'Dosen Pembimbing', 'href' => site_url('dosen/bimbingan'), 'icon_3d' => 'assets/images/icons_3d/daftar.png'],
                ['heading' => 'Dosen Wali', 'href' => site_url('dosen/wali'), 'icon_3d' => 'assets/images/icons_3d/approval.png'],
                ['heading' => 'Tanda Tangan Digital', 'href' => site_url('dosen/tanda-tangan'), 'icon_3d' => 'assets/images/icons_3d/tanda_tangan.png'],

                ['category' => 'Layanan & Bantuan', 'has_divider' => true],
                ['heading' => 'Bantuan & Live Chat', 'href' => site_url('dosen/help'), 'icon_3d' => 'assets/images/icons_3d/help_chat.png'],
                ['heading' => 'Buat Tiket Kendala', 'href' => site_url('dosen/ticketing/input'), 'icon_3d' => 'assets/images/icons_3d/ticketing.png'],
                ['heading' => 'Riwayat Ticketing', 'href' => site_url('dosen/ticketing/riwayat'), 'icon_3d' => 'assets/images/icons_3d/preview.png'],

                ['category' => 'Fasilitas & Jadwal', 'has_divider' => true],
                ['heading' => 'Approval Peminjaman Wadek', 'href' => site_url('wadek/peminjaman'), 'icon_3d' => 'assets/images/icons_3d/approval.png'],
                ['heading' => 'Ajukan Peminjaman Ruangan', 'href' => site_url('ajukan-booking'), 'icon_3d' => 'assets/images/icons_3d/ruangan.png'],
                ['heading' => 'Riwayat Booking Saya', 'href' => site_url('riwayat-booking'), 'icon_3d' => 'assets/images/icons_3d/riwayat_booking.png'],
                ['heading' => 'Kalender Jadwal', 'href' => site_url('kalender'), 'icon_3d' => 'assets/images/icons_3d/kalender.png'],
                ['heading' => 'Keluar', 'href' => site_url('login/logout'), 'icon_3d' => 'assets/images/icons_3d/logout.png'],
            ];
            break;

        case 1: // Admin System
            $defaultNavItems = [
                ['category' => 'Pusat Kendali'],
                ['heading' => 'Dashboard Control', 'href' => site_url('adminheader'), 'icon_3d' => 'assets/images/icons_3d/home.png'],
                ['heading' => 'Approval Peminjaman', 'href' => site_url('kelolabooking'), 'icon_3d' => 'assets/images/icons_3d/approval.png'],
                ['heading' => 'Kelola Master Barang', 'href' => site_url('admin/barang'), 'icon_3d' => 'assets/images/icons_3d/kelola_barang.png'],
                ['heading' => 'Ajukan Peminjaman Ruangan', 'href' => site_url('ajukan-booking'), 'icon_3d' => 'assets/images/icons_3d/ruangan.png'],
                ['heading' => 'Riwayat Booking Saya', 'href' => site_url('riwayat-booking'), 'icon_3d' => 'assets/images/icons_3d/riwayat_booking.png'],

                ['category' => 'Manajemen Akun & User', 'has_divider' => true],
                ['heading' => 'Pendaftaran Akun Baru', 'href' => site_url('import-email'), 'icon_3d' => 'assets/images/icons_3d/email_token.png'],

                ['category' => 'Manajemen Sistem & Fasilitas', 'has_divider' => true],
                ['heading' => 'Kelola Fasilitas & Ruangan', 'href' => site_url('kelolaruangan'), 'icon_3d' => 'assets/images/icons_3d/ruangan.png'],
                ['heading' => 'Pengaturan Header', 'href' => site_url('adminheader'), 'icon_3d' => 'assets/images/icons_3d/header_setting.png'],
                ['heading' => 'Pengaturan Footer', 'href' => site_url('adminfooter'), 'icon_3d' => 'assets/images/icons_3d/footer_setting.png'],
                ['heading' => 'Pengaturan Unit Ticketing', 'href' => site_url('admin#unit-ticketing'), 'icon_3d' => 'assets/images/icons_3d/unit_ticketing.png'],
                ['heading' => 'Respon Ticketing Lab', 'href' => site_url('laboran/respon-ticketing'), 'icon_3d' => 'assets/images/icons_3d/ticketing.png'],
                ['heading' => 'Riwayat Log History', 'href' => site_url('admin/log_history'), 'icon_3d' => 'assets/images/icons_3d/preview.png'],

                ['category' => 'Informasi & Jadwal', 'has_divider' => true],
                ['heading' => 'Kalender Jadwal Ruangan', 'href' => site_url('kalender'), 'icon_3d' => 'assets/images/icons_3d/kalender.png'],
                ['heading' => 'Kalender Jadwal Barang', 'href' => site_url('peminjaman_barang/kalender'), 'icon_3d' => 'assets/images/icons_3d/kalender.png'],
                ['heading' => 'Keluar', 'href' => site_url('login/logout'), 'icon_3d' => 'assets/images/icons_3d/logout.png'],
            ];
            break;
        case 5: // Admin LAA
            $defaultNavItems = [
                ['category' => 'Layanan Akademik'],
                ['heading' => 'Verifikasi Berkas', 'href' => site_url('adminlayanan'), 'icon_3d' => 'assets/images/icons_3d/approval.png'],
                ['heading' => 'Pengaturan Syarat Berkas', 'href' => site_url('adminlayanan/pengaturan_berkas'), 'icon_3d' => 'assets/images/icons_3d/unit_ticketing.png'],
                ['heading' => 'Pengaturan Jalur TA', 'href' => site_url('adminlayanan/pengaturan_jalur'), 'icon_3d' => 'assets/images/icons_3d/daftar.png'],

                ['category' => 'Layanan Ticketing & Bantuan', 'has_divider' => true],
                ['heading' => 'Bantuan & Live Chat', 'href' => site_url('adminlayanan/help'), 'icon_3d' => 'assets/images/icons_3d/help_chat.png'],
                ['heading' => 'Respon Ticketing LAA', 'href' => site_url('adminlayanan/ticketing'), 'icon_3d' => 'assets/images/icons_3d/unit_ticketing.png'],
                ['heading' => 'Buat Tiket Kendala', 'href' => site_url('dosen/ticketing/input'), 'icon_3d' => 'assets/images/icons_3d/ticketing.png'],
                ['heading' => 'Riwayat Tiket Saya', 'href' => site_url('dosen/ticketing/riwayat'), 'icon_3d' => 'assets/images/icons_3d/preview.png'],

                ['category' => 'Fasilitas & Jadwal', 'has_divider' => true],
                ['heading' => 'Ajukan Peminjaman Ruangan', 'href' => site_url('ajukan-booking'), 'icon_3d' => 'assets/images/icons_3d/ruangan.png'],
                ['heading' => 'Riwayat Booking Saya', 'href' => site_url('riwayat-booking'), 'icon_3d' => 'assets/images/icons_3d/riwayat_booking.png'],
                ['heading' => 'Kalender Jadwal', 'href' => site_url('kalender'), 'icon_3d' => 'assets/images/icons_3d/kalender.png'],
                ['heading' => 'Keluar', 'href' => site_url('login/logout'), 'icon_3d' => 'assets/images/icons_3d/logout.png'],
            ];
            break;

        case 7: // PIC KK
        case 9: // Ketua KK
            $defaultNavItems = [
                ['category' => 'Kelompok Keahlian'],
                ['heading' => 'Approval Usulan TA', 'href' => site_url('ketuakk'), 'icon_3d' => 'assets/images/icons_3d/approval.png'],

                ['category' => 'Portal Dosen & Pembimbing', 'has_divider' => true],
                ['heading' => 'Dosen Pembimbing', 'href' => site_url('dosen/bimbingan'), 'icon_3d' => 'assets/images/icons_3d/daftar.png'],
                ['heading' => 'Dosen Wali', 'href' => site_url('dosen/wali'), 'icon_3d' => 'assets/images/icons_3d/approval.png'],
                ['heading' => 'Tanda Tangan Digital', 'href' => site_url('dosen/tanda-tangan'), 'icon_3d' => 'assets/images/icons_3d/tanda_tangan.png'],

                ['category' => 'Layanan & Bantuan', 'has_divider' => true],
                ['heading' => 'Buat Tiket Kendala', 'href' => site_url('dosen/ticketing/input'), 'icon_3d' => 'assets/images/icons_3d/ticketing.png'],
                ['heading' => 'Riwayat Ticketing', 'href' => site_url('dosen/ticketing/riwayat'), 'icon_3d' => 'assets/images/icons_3d/preview.png'],

                ['category' => 'Fasilitas & Jadwal', 'has_divider' => true],
                ['heading' => 'Approval Peminjaman Wadek', 'href' => site_url('wadek/peminjaman'), 'icon_3d' => 'assets/images/icons_3d/approval.png'],
                ['heading' => 'Ajukan Peminjaman Ruangan', 'href' => site_url('ajukan-booking'), 'icon_3d' => 'assets/images/icons_3d/ruangan.png'],
                ['heading' => 'Riwayat Booking Saya', 'href' => site_url('riwayat-booking'), 'icon_3d' => 'assets/images/icons_3d/riwayat_booking.png'],
                ['heading' => 'Kalender Jadwal', 'href' => site_url('kalender'), 'icon_3d' => 'assets/images/icons_3d/kalender.png'],
                ['heading' => 'Keluar', 'href' => site_url('login/logout'), 'icon_3d' => 'assets/images/icons_3d/logout.png'],
            ];
            break;

        case 4: // Mahasiswa
            $defaultNavItems = [
                ['category' => 'Akademik Mahasiswa'],
                ['heading' => 'Dashboard', 'href' => site_url('mahasiswa'), 'icon_3d' => 'assets/images/icons_3d/home.png'],
                ['heading' => 'Pendaftaran TA', 'href' => site_url('mahasiswa/pendaftaran_ta'), 'icon_3d' => 'assets/images/icons_3d/daftar.png'],
                ['heading' => 'Bimbingan TA', 'href' => site_url('mahasiswa/bimbingan'), 'icon_3d' => 'assets/images/icons_3d/sidang.png'],

                ['category' => 'Fasilitas & Jadwal', 'has_divider' => true],
                ['heading' => 'Ajukan Peminjaman Ruangan', 'href' => site_url('ajukan-booking'), 'icon_3d' => 'assets/images/icons_3d/ruangan.png'],
                ['heading' => 'Riwayat Booking Ruangan', 'href' => site_url('riwayat-booking'), 'icon_3d' => 'assets/images/icons_3d/riwayat_booking.png'],
                ['heading' => 'Kalender Jadwal Ruangan', 'href' => site_url('kalender'), 'icon_3d' => 'assets/images/icons_3d/kalender.png'],
                ['heading' => 'Katalog Peminjaman Alat', 'href' => site_url('peminjaman_barang'), 'icon_3d' => 'assets/images/icons_3d/kelola_barang.png'],
                ['heading' => 'Kalender Jadwal Barang', 'href' => site_url('peminjaman_barang/kalender'), 'icon_3d' => 'assets/images/icons_3d/kalender.png'],
                ['heading' => 'Riwayat Peminjaman Alat', 'href' => site_url('peminjaman_barang/riwayat'), 'icon_3d' => 'assets/images/icons_3d/riwayat_booking.png'],

                ['category' => 'Layanan Ticketing', 'has_divider' => true],
                ['heading' => 'Buat Tiket Kendala', 'href' => site_url('mahasiswa/ticketing/input'), 'icon_3d' => 'assets/images/icons_3d/ticketing.png'],
                ['heading' => 'Riwayat Tiket Saya', 'href' => site_url('mahasiswa/ticketing/riwayat'), 'icon_3d' => 'assets/images/icons_3d/preview.png'],

                ['category' => 'Akun', 'has_divider' => true],
                ['heading' => 'Keluar', 'href' => site_url('login/logout'), 'icon_3d' => 'assets/images/icons_3d/logout.png'],
            ];
            break;

        case 17: // Kemahasiswaan
            $defaultNavItems = [
                ['category' => 'Layanan Kemahasiswaan'],
                ['heading' => 'Membuat Ticketing', 'href' => site_url('kemahasiswaan/ticketing/input'), 'icon_3d' => 'assets/images/icons_3d/ticketing.png'],
                ['heading' => 'Respon Ticketing', 'href' => site_url('kemahasiswaan/respon-ticketing'), 'icon_3d' => 'assets/images/icons_3d/unit_ticketing.png'],
                ['heading' => 'Riwayat', 'href' => site_url('kemahasiswaan/ticketing/riwayat'), 'icon_3d' => 'assets/images/icons_3d/preview.png'],

                ['category' => 'Akun', 'has_divider' => true],
                ['heading' => 'Keluar', 'href' => site_url('login/logout'), 'icon_3d' => 'assets/images/icons_3d/logout.png'],
            ];
            break;

        case 11: // Sekretariat
            $defaultNavItems = [
                ['category' => 'Layanan Sekretariat'],
                ['heading' => 'Membuat Ticketing', 'href' => site_url('sekretariat/ticketing/input'), 'icon_3d' => 'assets/images/icons_3d/ticketing.png'],
                ['heading' => 'Respon Ticketing', 'href' => site_url('sekretariat/respon-ticketing'), 'icon_3d' => 'assets/images/icons_3d/unit_ticketing.png'],
                ['heading' => 'Riwayat', 'href' => site_url('sekretariat/ticketing/riwayat'), 'icon_3d' => 'assets/images/icons_3d/preview.png'],

                ['category' => 'Akun', 'has_divider' => true],
                ['heading' => 'Keluar', 'href' => site_url('login/logout'), 'icon_3d' => 'assets/images/icons_3d/logout.png'],
            ];
            break;

        case 15: // SDM dan Keuangan
            $defaultNavItems = [
                ['category' => 'Layanan SDM dan Keuangan'],
                ['heading' => 'Membuat Ticketing', 'href' => site_url('sdmkeuangan/ticketing/input'), 'icon_3d' => 'assets/images/icons_3d/ticketing.png'],
                ['heading' => 'Respon Ticketing', 'href' => site_url('sdmkeuangan/respon-ticketing'), 'icon_3d' => 'assets/images/icons_3d/unit_ticketing.png'],
                ['heading' => 'Riwayat', 'href' => site_url('sdmkeuangan/ticketing/riwayat'), 'icon_3d' => 'assets/images/icons_3d/preview.png'],

                ['category' => 'Akun', 'has_divider' => true],
                ['heading' => 'Keluar', 'href' => site_url('login/logout'), 'icon_3d' => 'assets/images/icons_3d/logout.png'],
            ];
            break;

        case 16: // Program Studi
            $userProdiSession = $this->session->userdata('prodi') ? ' (' . $this->session->userdata('prodi') . ')' : '';
            $defaultNavItems = [
                ['category' => 'Layanan Program Studi' . $userProdiSession],
                ['heading' => 'Membuat Ticketing', 'href' => site_url('prodi/ticketing/input'), 'icon_3d' => 'assets/images/icons_3d/ticketing.png'],
                ['heading' => 'Respon Ticketing', 'href' => site_url('prodi/respon-ticketing'), 'icon_3d' => 'assets/images/icons_3d/unit_ticketing.png'],
                ['heading' => 'Riwayat', 'href' => site_url('prodi/ticketing/riwayat'), 'icon_3d' => 'assets/images/icons_3d/preview.png'],

                ['category' => 'Akun', 'has_divider' => true],
                ['heading' => 'Keluar', 'href' => site_url('login/logout'), 'icon_3d' => 'assets/images/icons_3d/logout.png'],
            ];
            break;

        default: // Publik / Tamu
            $defaultNavItems = [
                ['category' => 'Menu Utama'],
                ['heading' => 'Dashboard Utama', 'href' => site_url('dashboard'), 'icon_3d' => 'assets/images/icons_3d/home.png'],
                ['heading' => 'Kalender Jadwal', 'href' => site_url('kalender'), 'icon_3d' => 'assets/images/icons_3d/kalender.png'],
                ['heading' => $isLoggedIn ? 'Keluar' : 'Masuk ke Portal', 'href' => site_url($isLoggedIn ? 'login/logout' : 'login'), 'icon_3d' => 'assets/images/icons_3d/logout.png'],
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

    /* Mobile only nav items in sidebar (hidden on desktop screens >= 768px, matching Tailwind md breakpoint) */
    @media (min-width: 768px) {
        .curved-nav-mobile-only {
            display: none !important;
        }
    }
    @media (max-width: 767.98px) {
        .curved-nav-category.has-divider-mobile {
            border-top: 1px solid rgba(245, 158, 11, 0.2);
            margin-top: 6px;
            padding-top: 8px;
        }
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

    /* Premium Frosted Glass Layout with Smooth Curved Edge */
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

    .curved-nav-item:hover {
        background: rgba(245, 158, 11, 0.12) !important;
        backdrop-filter: blur(8px) !important;
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
</style>

<!-- Floating Trigger Button (Top Left) -->
<button type="button" id="curvedSidebarToggle" class="curved-sidebar-toggle-btn is-active" aria-expanded="true" aria-label="Toggle Sidebar Menu" title="Buka Menu Navigasi">
    <div class="curved-sidebar-burger">
        <span></span>
        <span></span>
        <span></span>
    </div>
</button>

<!-- Backdrop Blur Overlay -->
<div id="curvedSidebarBackdrop" class="curved-sidebar-backdrop"></div>

<!-- Sliding Sidebar Panel with Morphing Curved SVG (Left Side) -->
<aside id="curvedSidebarPanel" class="curved-sidebar-panel is-active" aria-label="Sidebar Navigasi">
    <div class="curved-sidebar-inner">
        <!-- Top Section: Header & Nav Links -->
        <div>
            <div class="curved-sidebar-header">
                <p>Navigation</p>
                <div class="curved-header-role-badge">
                    <span><?= htmlspecialchars($activeRoleBadge); ?></span>
                </div>
            </div>
            
            <nav class="curved-sidebar-nav">
                <?php 
                $curr_uri = trim(uri_string(), '/');
                $navCount = 1;
                foreach ($defaultNavItems as $idx => $item): 
                    $isMobileOnly = !empty($item['mobile_only']);
                    if (isset($item['category'])):
                ?>
                    <div class="curved-nav-category <?= !empty($item['has_divider']) ? 'has-divider' : '' ?> <?= !empty($item['has_divider_mobile']) ? 'has-divider-mobile' : '' ?> <?= $isMobileOnly ? 'curved-nav-mobile-only' : '' ?>">
                        <?= htmlspecialchars($item['category']); ?>
                    </div>
                <?php 
                    continue;
                    endif;

                    $num = isset($item['index']) ? sprintf('%02d', $item['index']) : sprintf('%02d', $navCount++);
                    $icon = isset($item['icon']) ? $item['icon'] : null;
                    $icon3d = isset($item['icon_3d']) ? $item['icon_3d'] : null;

                    $cleanHref = trim(str_replace([site_url(), base_url()], '', $item['href']), '/');
                    $hasHash = (strpos($cleanHref, '#') !== false);
                    $cleanHrefUri = strtok($cleanHref, '#');

                    if ($hasHash) {
                        // Links with # hashes are client-side tabs, will be activated dynamically via JS
                        $isCurrent = false;
                    } else {
                        $isCurrent = (!empty($cleanHrefUri) && ($curr_uri === $cleanHrefUri));
                        if (!$isCurrent && !empty($cleanHrefUri) && !in_array($cleanHrefUri, ['dashboard', 'admin', 'laboran', 'kaur', 'dosen', 'koordinatorta', 'mahasiswa'])) {
                            $isCurrent = (strpos($curr_uri, $cleanHrefUri) === 0);
                        }
                        if (!$isCurrent) {
                            if ($cleanHrefUri === 'mahasiswa' && ($curr_uri === 'mahasiswa' || $curr_uri === 'mahasiswa/index')) {
                                $isCurrent = true;
                            } elseif ($cleanHrefUri === 'mahasiswa/pendaftaran_ta' && strpos($curr_uri, 'pendaftaran') !== false) {
                                $isCurrent = true;
                            } elseif ($cleanHrefUri === 'mahasiswa/bimbingan' && strpos($curr_uri, 'bimbingan') !== false) {
                                $isCurrent = true;
                            }
                        }
                    }
                ?>
                    <a href="<?= htmlspecialchars($item['href']); ?>" class="curved-nav-item <?= $isCurrent ? 'is-current' : '' ?> <?= $isMobileOnly ? 'curved-nav-mobile-only' : '' ?>">
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
        <div>
            <?php if ($isLoggedIn): ?>
            <!-- User Profile Summary Card -->
            <div class="curved-sidebar-user-card">
                <div class="curved-sidebar-user-avatar">
                    <?= strtoupper(substr($this->session->userdata('name') ?: ($this->session->userdata('username') ?: 'U'), 0, 1)) ?>
                </div>
                <div class="curved-sidebar-user-info">
                    <div class="curved-sidebar-user-name" title="<?= htmlspecialchars($this->session->userdata('name') ?: 'Pengguna') ?>">
                        <?= htmlspecialchars($this->session->userdata('name') ?: ($this->session->userdata('username') ?: 'Pengguna')) ?>
                    </div>
                    <div class="curved-sidebar-user-role">
                        <?= htmlspecialchars($this->session->userdata('nidn_nim') ?: '') ?><?= !empty($this->session->userdata('nidn_nim')) ? ' • ' : '' ?><?= htmlspecialchars($activeRoleBadge) ?>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <!-- Public Login Button -->
            <a href="<?= site_url('login'); ?>" class="btn-sidebar-login" style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 10px 14px; background: linear-gradient(135deg, #ea580c, #c2410c); color: #fff; border-radius: 12px; font-weight: 700; font-size: 0.82rem; text-decoration: none; margin-bottom: 10px; box-shadow: 0 4px 12px rgba(234, 88, 12, 0.25); transition: all 0.2s ease;">
                <i class="fa-solid fa-right-to-bracket"></i>
                <span>Masuk ke Akun</span>
            </a>
            <?php endif; ?>

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

<!-- Instant Pre-init to ensure desktop is always open by default -->
<script>
(function() {
    try {
        localStorage.removeItem('ifik_curved_sidebar_state');
        var isDesktop = (window.matchMedia && window.matchMedia('(min-width: 1024px)').matches) || window.innerWidth >= 992;
        if (!isDesktop) {
            document.body.classList.add('curved-sidebar-desktop-collapsed');
            document.body.classList.remove('curved-sidebar-desktop-open');
            var btn = document.getElementById('curvedSidebarToggle');
            if (btn) {
                btn.classList.remove('is-active');
                btn.setAttribute('aria-expanded', 'false');
            }
            var panel = document.getElementById('curvedSidebarPanel');
            if (panel) {
                panel.classList.remove('is-active');
            }
        }
    } catch(e) {}
})();
</script>

<!-- Curved Sidebar Core Script -->
<script src="<?= base_url('assets/js/curved_sidebar.js?v=' . time()); ?>"></script>
