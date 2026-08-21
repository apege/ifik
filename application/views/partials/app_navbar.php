<?php
    $current_uri = $this->uri->segment(1) ?: 'dashboard';
    $user_role_label = $user_role_label ?? ($current_uri === 'adminlayanan' ? 'Admin Layanan (LAA)' : ($current_uri === 'ketuakk' ? 'Ketua Kelompok Keahlian' : ($current_uri === 'koordinatorta' ? 'Koordinator Tugas Akhir' : ($current_uri === 'dosenwali' ? 'Dosen Wali Akademik' : ($current_uri === 'mahasiswa' ? 'Akademik Mahasiswa' : 'Pusat Kendali Admin')))));
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
                    <span class="text-[10px] uppercase font-bold tracking-wider text-orange-600 mt-1 block"><?= $user_role_label; ?></span>
                </div>
            </a>

            <!-- Nav Links -->
            <nav class="hidden md:flex items-center gap-4 lg:gap-5">
                <!-- Beranda Utama / Portal Publik -->
                <a href="<?= base_url('/'); ?>" 
                   class="text-xs font-bold text-slate-700 hover:text-orange-600 transition-colors flex items-center gap-1.5 py-1.5 px-3 rounded-xl hover:bg-orange-50 border border-transparent hover:border-orange-200">
                    <i class="bi bi-house-door-fill text-orange-600"></i>
                    <span>Beranda Utama</span>
                </a>

                <!-- Admin Layanan -->
                <a href="<?= site_url('adminlayanan'); ?>" 
                   class="text-xs font-semibold flex items-center gap-1.5 transition-colors py-1 <?= $current_uri === 'adminlayanan' ? 'text-orange-600 font-bold border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600'; ?>">
                    <i class="bi bi-file-earmark-check-fill <?= $current_uri === 'adminlayanan' ? 'text-orange-600' : 'text-slate-400'; ?>"></i>
                    <span>Admin LAA</span>
                </a>

                <!-- Ketua KK -->
                <a href="<?= site_url('ketuakk'); ?>" 
                   class="text-xs font-semibold flex items-center gap-1.5 transition-colors py-1 <?= $current_uri === 'ketuakk' ? 'text-orange-600 font-bold border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600'; ?>">
                    <i class="bi bi-diagram-3-fill <?= $current_uri === 'ketuakk' ? 'text-orange-600' : 'text-slate-400'; ?>"></i>
                    <span>Ketua KK</span>
                </a>

                <!-- Kelola Berita -->
                <a href="<?= site_url('news/newsroom'); ?>" 
                   class="text-xs font-semibold flex items-center gap-1.5 transition-colors py-1 <?= $current_uri === 'news' ? 'text-orange-600 font-bold border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600'; ?>">
                    <i class="bi bi-newspaper <?= $current_uri === 'news' ? 'text-orange-600' : 'text-slate-400'; ?>"></i>
                    <span>Berita</span>
                </a>

                <!-- Pusat Admin Hub -->
                <a href="<?= site_url('admin'); ?>" 
                   class="text-xs font-semibold flex items-center gap-1.5 transition-colors py-1 <?= $current_uri === 'admin' ? 'text-orange-600 font-bold border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600'; ?>">
                    <i class="bi bi-grid-fill <?= $current_uri === 'admin' ? 'text-orange-600' : 'text-slate-400'; ?>"></i>
                    <span>Pusat Admin</span>
                </a>
            </nav>

            <!-- User Profile / Quick Pill -->
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex flex-col text-right">
                    <span class="text-xs font-bold text-slate-800 leading-tight">
                        <?= htmlspecialchars($user_display_name ?? 'Unit Layanan FIK'); ?>
                    </span>
                    <span class="text-[10px] font-medium text-slate-500">
                        <?= htmlspecialchars($user_display_sub ?? 'Telkom University'); ?>
                    </span>
                </div>
                <div class="w-9 h-9 rounded-xl bg-orange-50 border border-orange-200 text-orange-600 flex items-center justify-center font-bold text-sm shadow-xs">
                    <i class="bi <?= $current_uri === 'mahasiswa' ? 'bi-person-fill' : 'bi-shield-check'; ?>"></i>
                </div>
            </div>

        </div>
    </div>
</header>
