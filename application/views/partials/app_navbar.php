<?php
    $current_uri = $this->uri->segment(1) ?: 'dashboard';
    $role_id = (int)($this->session->userdata('role_id') ?? 1); // Default to 1 (Super Admin) if previewing
    $logged_in = (bool)$this->session->userdata('logged_in');

    $role_names = [
        1 => 'Super Admin System',
        2 => 'Dosen Wali Akademik',
        3 => 'Admin Layanan (LAA)',
        4 => 'Koordinator Tugas Akhir',
        5 => 'Ketua Kelompok Keahlian',
        6 => 'Mahasiswa'
    ];

    $user_role_label = $role_names[$role_id] ?? ($current_uri === 'adminlayanan' ? 'Admin Layanan (LAA)' : ($current_uri === 'ketuakk' ? 'Ketua Kelompok Keahlian' : ($current_uri === 'koordinatorta' ? 'Koordinator Tugas Akhir' : 'Pusat Kendali Admin')));
    $user_display_name = $this->session->userdata('name') ?: 'Unit Layanan FIK';
    $user_email = $this->session->userdata('email') ?: 'admin@telkomuniversity.ac.id';
?>
<!-- Unified Clean Top Navbar Partial -->
<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 sm:h-18">
            
            <!-- Brand -->
            <a href="<?= base_url('/'); ?>" class="flex items-center gap-3 group" title="Kembali ke Beranda Utama Website IFIK">
                <div class="w-9 h-9 bg-gradient-to-tr from-orange-600 to-amber-500 text-white rounded-xl font-extrabold text-base flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
                    I
                </div>
                <div>
                    <span class="font-extrabold text-sm sm:text-base text-slate-900 tracking-tight block leading-none">IFIK Portal</span>
                    <span class="text-[10px] uppercase font-bold tracking-wider text-orange-600 mt-1 block"><?= htmlspecialchars($user_role_label); ?></span>
                </div>
            </a>

            <!-- Nav Links (Role-Based Unlocked Tabs) -->
            <nav class="hidden md:flex items-center gap-3 lg:gap-4">
                <!-- Beranda Utama / Portal Publik -->
                <a href="<?= base_url('/'); ?>" 
                   class="text-xs font-bold text-slate-700 hover:text-orange-600 transition-colors flex items-center gap-1.5 py-1.5 px-3 rounded-xl hover:bg-orange-50 border border-transparent hover:border-orange-200">
                    <i class="bi bi-house-door-fill text-orange-600"></i>
                    <span>Beranda Utama</span>
                </a>

                <!-- Dosen Wali - Role 1 or 2 -->
                <?php if (in_array($role_id, [1, 2])): ?>
                <a href="<?= site_url('dosenwali'); ?>" 
                   class="text-xs font-semibold flex items-center gap-1.5 transition-colors py-1 <?= $current_uri === 'dosenwali' ? 'text-orange-600 font-bold border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600'; ?>">
                    <i class="bi bi-person-check-fill <?= $current_uri === 'dosenwali' ? 'text-orange-600' : 'text-slate-400'; ?>"></i>
                    <span>Dosen Wali</span>
                </a>
                <?php endif; ?>

                <!-- Admin Layanan (LAA) - Role 1 or 3 -->
                <?php if (in_array($role_id, [1, 3])): ?>
                <a href="<?= site_url('adminlayanan'); ?>" 
                   class="text-xs font-semibold flex items-center gap-1.5 transition-colors py-1 <?= $current_uri === 'adminlayanan' ? 'text-orange-600 font-bold border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600'; ?>">
                    <i class="bi bi-file-earmark-check-fill <?= $current_uri === 'adminlayanan' ? 'text-orange-600' : 'text-slate-400'; ?>"></i>
                    <span>Admin LAA</span>
                </a>
                <?php endif; ?>

                <!-- Ketua KK - Role 1 or 7 -->
                <?php if (in_array($role_id, [1, 7])): ?>
                <a href="<?= site_url('ketuakk'); ?>" 
                   class="text-xs font-semibold flex items-center gap-1.5 transition-colors py-1 <?= $current_uri === 'ketuakk' ? 'text-orange-600 font-bold border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600'; ?>">
                    <i class="bi bi-diagram-3-fill <?= $current_uri === 'ketuakk' ? 'text-orange-600' : 'text-slate-400'; ?>"></i>
                    <span>Ketua KK</span>
                </a>
                <?php endif; ?>

                <!-- Koordinator TA - Role 1 or 6 -->
                <?php if (in_array($role_id, [1, 6])): ?>
                <a href="<?= site_url('koordinatorta'); ?>" 
                   class="text-xs font-semibold flex items-center gap-1.5 transition-colors py-1 <?= $current_uri === 'koordinatorta' ? 'text-orange-600 font-bold border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600'; ?>">
                    <i class="bi bi-mortarboard-fill <?= $current_uri === 'koordinatorta' ? 'text-orange-600' : 'text-slate-400'; ?>"></i>
                    <span>Koor TA</span>
                </a>
                <?php endif; ?>

                <!-- Kelola Berita - Role 1 or 3 -->
                <?php if (in_array($role_id, [1, 3])): ?>
                <a href="<?= site_url('news/newsroom'); ?>" 
                   class="text-xs font-semibold flex items-center gap-1.5 transition-colors py-1 <?= $current_uri === 'news' ? 'text-orange-600 font-bold border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600'; ?>">
                    <i class="bi bi-newspaper <?= $current_uri === 'news' ? 'text-orange-600' : 'text-slate-400'; ?>"></i>
                    <span>Berita</span>
                </a>
                <?php endif; ?>

                <!-- Riwayat Log Approval - Global Access for Admin & Staff -->
                <?php if (in_array($role_id, [1, 2, 3, 4, 6, 7])): ?>
                <a href="<?= site_url('admin/log_history'); ?>" 
                   class="text-xs font-semibold flex items-center gap-1.5 transition-colors py-1 <?= $current_uri === 'log_history' || $this->uri->segment(2) === 'log_history' ? 'text-orange-600 font-bold border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600'; ?>" title="Audit Trail & Riwayat Log Approval System (Seluruh Modul)">
                    <i class="bi bi-clock-history <?= $current_uri === 'log_history' || $this->uri->segment(2) === 'log_history' ? 'text-orange-600' : 'text-slate-400'; ?>"></i>
                    <span>Log History</span>
                </a>
                <?php endif; ?>

                <!-- Pusat Admin Hub (Hanya Super Admin - Role 1) -->
                <?php if ($role_id == 1): ?>
                <a href="<?= site_url('admin'); ?>" 
                   class="text-xs font-semibold flex items-center gap-1.5 transition-colors py-1 <?= $current_uri === 'admin' ? 'text-orange-600 font-bold border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600'; ?>">
                    <i class="bi bi-grid-fill <?= $current_uri === 'admin' ? 'text-orange-600' : 'text-slate-400'; ?>"></i>
                    <span>Pusat Admin</span>
                </a>
                <?php endif; ?>
            </nav>

            <!-- User Profile / Quick Pill & Logout -->
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex flex-col text-right">
                    <span class="text-xs font-bold text-slate-800 leading-tight">
                        <?= htmlspecialchars($user_display_name); ?>
                    </span>
                    <span class="text-[10px] font-medium text-slate-500">
                        <?= htmlspecialchars($user_email); ?>
                    </span>
                </div>
                <div class="w-9 h-9 rounded-xl bg-orange-50 border border-orange-200 text-orange-600 flex items-center justify-center font-bold text-sm shadow-xs">
                    <i class="bi bi-shield-check"></i>
                </div>
                <?php if ($logged_in): ?>
                <a href="<?= site_url('login/logout'); ?>" class="text-xs font-bold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 px-3 py-1.5 rounded-xl transition-all flex items-center gap-1" title="Keluar dari akun">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="hidden lg:inline">Logout</span>
                </a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</header>

