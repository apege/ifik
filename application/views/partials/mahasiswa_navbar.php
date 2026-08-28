<?php
/* Partial: Shared Portal Navbar — used by both Mahasiswa and Dosen views.
   Expects either $mahasiswa array (student views) or falls back to session data (dosen views). */

$role_id   = $this->session->userdata('role_id');
$is_dosen  = ($role_id == 4);

// Resolve display name & identifier
if (!empty($mahasiswa['nama_depan'])) {
    $display_name = $mahasiswa['nama_depan'];
    $display_id   = $mahasiswa['nim'] ?? 'NIM';
} else {
    $display_name = $this->session->userdata('name') ?: 'User';
    $display_id   = $this->session->userdata('nidn_nim') ?: 'NIDN';
}

$subtitle = $is_dosen ? 'Akademik Dosen' : 'Akademik Mahasiswa';
$active_bimbingan = (strpos(uri_string(), 'bimbingan') !== false);
$active_dashboard = (!$active_bimbingan && strpos(uri_string(), 'pendaftaran') === false);
$active_pendaftaran = (strpos(uri_string(), 'pendaftaran') !== false);
?>
<header class="sticky top-0 z-50 bg-white/90 backdrop-blur-2xl border-b border-orange-100/80 shadow-xs">
    <div class="w-full px-4 sm:px-6 lg:px-10">
        <div class="flex items-center justify-between h-16 sm:h-18">
            <!-- Brand -->
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-gradient-to-tr from-orange-600 to-amber-500 text-white rounded-xl font-bold text-lg flex items-center justify-center box-3d">
                    I
                </div>
                <div>
                    <span class="font-bold text-base text-slate-900 tracking-tight block leading-none">IFIK Portal</span>
                    <span class="text-[9px] uppercase font-bold tracking-wider text-orange-500 mt-0.5 block"><?= $subtitle ?></span>
                </div>
            </div>
            <!-- Nav Menu -->
            <nav class="hidden md:flex items-center gap-7 relative" id="mainNav">
                <a href="<?= site_url('mahasiswa'); ?>" class="nav-link <?= $active_dashboard ? 'active-link' : '' ?> flex items-center gap-2 tracking-wide">
                    <i class="bi bi-grid-1x2-fill"></i> <span>Dashboard</span>
                </a>
                <a href="<?= site_url('mahasiswa/pendaftaran_ta'); ?>" class="nav-link <?= $active_pendaftaran ? 'active-link' : '' ?> flex items-center gap-2 tracking-wide">
                    <i class="bi bi-file-earmark-text"></i> <span>Pendaftaran TA</span>
                </a>
                <a href="<?= site_url('mahasiswa/bimbingan'); ?>" class="nav-link <?= $active_bimbingan ? 'active-link' : '' ?> flex items-center gap-2 tracking-wide">
                    <i class="bi bi-person-video3"></i> <span>Bimbingan TA</span>
                </a>
            </nav>
            <!-- User Quick Info -->
            <div class="flex items-center gap-2.5">
                <div class="hidden sm:flex flex-col text-right">
                    <span class="text-xs font-semibold text-slate-800 leading-tight"><?= htmlspecialchars($display_name); ?></span>
                    <span class="text-[9px] text-slate-400 font-medium"><?= htmlspecialchars($display_id); ?></span>
                </div>
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white flex items-center justify-center font-bold text-xs box-3d">
                    <?= strtoupper(substr($display_name, 0, 1)); ?>
                </div>
            </div>
        </div>
    </div>
</header>
