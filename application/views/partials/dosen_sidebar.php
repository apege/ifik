<?php
$current_uri = uri_string();
$role_id = (int)$this->session->userdata('role_id');
$active_bimbingan = (strpos($current_uri, 'bimbingan') !== false);
$active_penguji   = (strpos($current_uri, 'penguji') !== false);
$active_wali      = (strpos($current_uri, 'wali') !== false || strpos($current_uri, 'dosenwali') !== false);
$active_approval  = (strpos($current_uri, 'kaur') !== false || strpos($current_uri, 'approval') !== false);
$active_booking   = (strpos($current_uri, 'ajukan') !== false || strpos($current_uri, 'booking') !== false);
$active_kalender  = (strpos($current_uri, 'kalender') !== false);
?>

<!-- Dosen Sidebar -->
<aside class="fixed left-0 top-0 h-screen w-64 bg-white/90 backdrop-blur-xl border-r border-orange-100 shadow-xl z-50 flex flex-col transition-all duration-300">
    
    <!-- Brand -->
    <div class="h-20 flex items-center px-6 border-b border-orange-100/60 bg-gradient-to-r from-orange-50/50 to-transparent">
        <a href="<?= site_url('dashboard') ?>" class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-tr from-orange-600 to-amber-500 rounded-2xl flex items-center justify-center shadow-lg shadow-orange-500/30">
                <span class="text-white font-extrabold text-xl">I</span>
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-800 leading-none">IFIK Portal</h2>
                <p class="text-[10px] uppercase font-bold text-orange-500 tracking-wider mt-1">
                    <?= ($role_id === 3) ? 'Kaur / Ka Lab' : 'Dosen Dashboard' ?>
                </p>
            </div>
        </a>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <div class="px-2 mb-2">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Navigasi Peran</span>
        </div>
        
        <a href="<?= site_url('dosen/bimbingan') ?>" 
           class="flex items-center gap-3 px-3 py-3 rounded-xl font-semibold transition-all group <?= $active_bimbingan ? 'bg-orange-50 text-orange-600 shadow-sm border border-orange-100' : 'text-slate-600 hover:bg-slate-50 hover:text-orange-500' ?>">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors <?= $active_bimbingan ? 'bg-orange-100 text-orange-600' : 'bg-slate-100 text-slate-500 group-hover:bg-orange-100 group-hover:text-orange-500' ?>">
                <i class="bi bi-person-workspace text-lg"></i>
            </div>
            Dosen Pembimbing
        </a>

        <a href="<?= site_url('dosen/penguji') ?>" 
           class="flex items-center gap-3 px-3 py-3 rounded-xl font-semibold transition-all group <?= $active_penguji ? 'bg-orange-50 text-orange-600 shadow-sm border border-orange-100' : 'text-slate-600 hover:bg-slate-50 hover:text-orange-500' ?>">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors <?= $active_penguji ? 'bg-orange-100 text-orange-600' : 'bg-slate-100 text-slate-500 group-hover:bg-orange-100 group-hover:text-orange-500' ?>">
                <i class="bi bi-clipboard-check text-lg"></i>
            </div>
            Dosen Penguji
        </a>

        <a href="<?= site_url('dosen/wali') ?>" 
           class="flex items-center gap-3 px-3 py-3 rounded-xl font-semibold transition-all group <?= $active_wali ? 'bg-orange-50 text-orange-600 shadow-sm border border-orange-100' : 'text-slate-600 hover:bg-slate-50 hover:text-orange-500' ?>">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors <?= $active_wali ? 'bg-orange-100 text-orange-600' : 'bg-slate-100 text-slate-500 group-hover:bg-orange-100 group-hover:text-orange-500' ?>">
                <i class="bi bi-people-fill text-lg"></i>
            </div>
            Dosen Wali
        </a>

        <div class="px-2 pt-4 mb-2 border-t border-slate-100">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Layanan & Fasilitas</span>
        </div>

        <?php if ($role_id === 3): ?>
        <a href="<?= site_url('kaur/approval') ?>" 
           class="flex items-center gap-3 px-3 py-3 rounded-xl font-semibold transition-all group <?= $active_approval ? 'bg-orange-50 text-orange-600 shadow-sm border border-orange-100' : 'text-slate-600 hover:bg-slate-50 hover:text-orange-500' ?>">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors <?= $active_approval ? 'bg-orange-100 text-orange-600' : 'bg-slate-100 text-slate-500 group-hover:bg-orange-100 group-hover:text-orange-500' ?>">
                <i class="bi bi-patch-check-fill text-lg"></i>
            </div>
            Approval Peminjaman
        </a>
        <?php endif; ?>

        <a href="<?= site_url('ajukan-booking') ?>" 
           class="flex items-center gap-3 px-3 py-3 rounded-xl font-semibold transition-all group <?= $active_booking ? 'bg-orange-50 text-orange-600 shadow-sm border border-orange-100' : 'text-slate-600 hover:bg-slate-50 hover:text-orange-500' ?>">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors <?= $active_booking ? 'bg-orange-100 text-orange-600' : 'bg-slate-100 text-slate-500 group-hover:bg-orange-100 group-hover:text-orange-500' ?>">
                <i class="bi bi-building-fill-add text-lg"></i>
            </div>
            Ajukan Peminjaman
        </a>

        <a href="<?= site_url('kalender') ?>" 
           class="flex items-center gap-3 px-3 py-3 rounded-xl font-semibold transition-all group <?= $active_kalender ? 'bg-orange-50 text-orange-600 shadow-sm border border-orange-100' : 'text-slate-600 hover:bg-slate-50 hover:text-orange-500' ?>">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors <?= $active_kalender ? 'bg-orange-100 text-orange-600' : 'bg-slate-100 text-slate-500 group-hover:bg-orange-100 group-hover:text-orange-500' ?>">
                <i class="bi bi-calendar2-range-fill text-lg"></i>
            </div>
            Kalender Jadwal
        </a>
    </nav>

    <!-- User Section -->
    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
        <div class="flex items-center gap-3 p-3 bg-white rounded-2xl shadow-sm border border-slate-100">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-slate-700 to-slate-900 flex items-center justify-center text-white font-bold shrink-0">
                <?= strtoupper(substr($this->session->userdata('name') ?: 'D', 0, 1)) ?>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-slate-800 truncate"><?= htmlspecialchars($this->session->userdata('name') ?: 'User') ?></p>
                <p class="text-xs text-slate-500 truncate"><?= htmlspecialchars($this->session->userdata('nidn_nim') ?: 'NIDN') ?></p>
            </div>
        </div>
        <a href="<?= site_url('login/logout') ?>" class="mt-2 w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-rose-500 hover:bg-rose-50 text-sm font-bold transition-colors">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</aside>

<!-- Spacer for sidebar to push main content -->
<style>
    /* Prevent body from overflowing under sidebar */
    body { padding-left: 16rem !important; }
    
    /* Responsive adjustment */
    @media (max-width: 1024px) {
        aside { transform: translateX(-100%); }
        body { padding-left: 0 !important; }
    }
</style>
