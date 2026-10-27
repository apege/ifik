<?php
    $current_uri = $this->uri->segment(1) ?: 'dashboard';
    $role_id = (int)($this->session->userdata('role_id') ?? 1); // Default to 1 (Super Admin) if previewing
    $logged_in = (bool)$this->session->userdata('logged_in');

    $role_names = [
        1  => 'Admin System',
        2  => 'Kepala Urusan (Ka Lab)',
        3  => 'Dosen',
        4  => 'Mahasiswa',
        5  => 'Admin Layanan (LAA)',
        6  => 'Koordinator Tugas Akhir',
        7  => 'PIC Kelompok Keahlian',
        9  => 'Ketua Kelompok Keahlian',
        21 => 'Laboran',
        22 => 'Super Administrator'
    ];

    $user_role_label = $role_names[$role_id] ?? ($current_uri === 'adminlayanan' ? 'Admin Layanan (LAA)' : ($current_uri === 'ketuakk' ? 'Ketua Kelompok Keahlian' : ($current_uri === 'koordinatorta' ? 'Koordinator Tugas Akhir' : ($current_uri === 'laboran' ? 'Laboran' : ($current_uri === 'kaur' ? 'Kepala Urusan (Ka Lab)' : ($role_id == 22 ? 'Panel Super Admin' : 'Pusat Kendali Admin'))))));
    $user_display_name = $this->session->userdata('name') ?: 'Unit Layanan FIK';
    $user_email = $this->session->userdata('email') ?: 'admin@telkomuniversity.ac.id';
?>
<!-- Unified Clean Top Navbar Partial -->
<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 sm:h-18">
            
            <!-- Brand -->
            <a href="<?= base_url('/'); ?>" class="flex items-center gap-3 group shrink-0" title="Kembali ke Beranda Utama Website IFIK">
                <div class="w-9 h-9 bg-gradient-to-tr from-orange-600 to-amber-500 text-white rounded-xl font-extrabold text-base flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
                    I
                </div>
                <div>
                    <span class="font-extrabold text-sm sm:text-base text-slate-900 tracking-tight block leading-none">IFIK Portal</span>
                    <span class="text-[10px] uppercase font-bold tracking-wider text-orange-600 mt-1 block"><?= htmlspecialchars($user_role_label); ?></span>
                </div>
            </a>

            <!-- Nav Links (Role-Based Unlocked Tabs) -->
            <nav class="hidden md:flex items-center gap-2 lg:gap-3.5 xl:gap-4 shrink-0">
                <!-- Beranda Utama / Portal Publik -->
                <a href="<?= base_url('/'); ?>" 
                   class="whitespace-nowrap text-xs font-bold text-slate-700 hover:text-orange-600 transition-colors flex items-center gap-1.5 py-1.5 px-2.5 rounded-xl hover:bg-orange-50 border border-transparent hover:border-orange-200">
                    <i class="bi bi-house-door-fill text-orange-600"></i>
                    <span>Beranda Utama</span>
                </a>

                <!-- Dosen & Bimbingan - Role 1, 2, 3, 6, 7, 9 -->
                <?php if (in_array($role_id, [1, 2, 3, 6, 7, 9])): ?>
                <?php 
                    $is_active_dosen = in_array($current_uri, ['dosen', 'dosenwali']) || ($this->uri->segment(1) === 'dosen') || ($this->uri->segment(1) === 'dosenwali');
                ?>
                <a href="<?= site_url('dosen/bimbingan'); ?>" 
                   class="whitespace-nowrap text-xs font-semibold flex items-center gap-1.5 transition-colors py-1 <?= $is_active_dosen ? 'text-orange-600 font-bold border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600'; ?>">
                    <i class="bi bi-person-workspace <?= $is_active_dosen ? 'text-orange-600' : 'text-slate-400'; ?>"></i>
                    <span>Dosen</span>
                </a>
                <?php endif; ?>

                <!-- Laboran - Role 1 or 21 -->
                <?php if (in_array($role_id, [1, 21])): ?>
                <a href="<?= site_url('laboran/booking'); ?>" 
                   class="whitespace-nowrap text-xs font-semibold flex items-center gap-1.5 transition-colors py-1 <?= $current_uri === 'laboran' || $this->uri->segment(1) === 'laboran' ? 'text-orange-600 font-bold border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600'; ?>">
                    <i class="bi bi-building-gear <?= $current_uri === 'laboran' || $this->uri->segment(1) === 'laboran' ? 'text-orange-600' : 'text-slate-400'; ?>"></i>
                    <span>Laboran</span>
                </a>
                <?php endif; ?>

                <!-- Kaur / Ka Lab - Role 1 or 2 -->
                <?php if (in_array($role_id, [1, 2])): ?>
                <a href="<?= site_url('kaur/approval'); ?>" 
                   class="whitespace-nowrap text-xs font-semibold flex items-center gap-1.5 transition-colors py-1 <?= $current_uri === 'kaur' || $this->uri->segment(1) === 'kaur' ? 'text-orange-600 font-bold border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600'; ?>">
                    <i class="bi bi-patch-check-fill <?= $current_uri === 'kaur' || $this->uri->segment(1) === 'kaur' ? 'text-orange-600' : 'text-slate-400'; ?>"></i>
                    <span>Ka. Ur</span>
                </a>
                <?php endif; ?>

                <!-- Admin Layanan (LAA) - Role 1 or 5 with Dropdown -->
                <?php if (in_array($role_id, [1, 5])): ?>
                <?php 
                    $sub_seg = $this->uri->segment(2);
                    $is_laa_active = ($current_uri === 'adminlayanan' || $current_uri === 'adminlayananticketing');
                ?>
                <div class="relative group">
                    <button type="button" class="whitespace-nowrap text-xs font-semibold flex items-center gap-1.5 transition-colors py-1.5 px-2 rounded-xl <?= $is_laa_active ? 'text-orange-600 font-bold bg-orange-50/80 border border-orange-200' : 'text-slate-600 hover:text-orange-600 hover:bg-slate-50'; ?>">
                        <i class="bi bi-file-earmark-check-fill <?= $is_laa_active ? 'text-orange-600' : 'text-slate-400'; ?>"></i>
                        <span>Admin LAA</span>
                        <i class="bi bi-chevron-down text-[10px] text-slate-400 group-hover:text-orange-600 transition-transform duration-200 group-hover:rotate-180"></i>
                    </button>
                    <!-- Dropdown Menu (with invisible hover bridge to prevent closing on mouse move) -->
                    <div class="absolute left-0 top-full mt-1 w-60 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 hidden group-hover:block transition-all z-50 animate-in fade-in slide-in-from-top-1 duration-150 before:content-[''] before:absolute before:-top-3 before:left-0 before:right-0 before:h-3">
                        <div class="px-3 py-1.5 border-b border-slate-100 mb-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Modul Layanan LAA</span>
                        </div>
                        <a href="<?= site_url('adminlayanan'); ?>" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:text-orange-600 hover:bg-orange-50 transition-colors <?= (empty($sub_seg) || $sub_seg === 'index' || $sub_seg === 'detail_berkas') ? 'text-orange-600 font-bold bg-orange-50/60' : '' ?>">
                            <i class="bi bi-file-earmark-text text-orange-500"></i>
                            <span>Pendaftaran TA</span>
                        </a>
                        <a href="<?= site_url('adminlayanan/pendaftaran_sidang'); ?>" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:text-orange-600 hover:bg-orange-50 transition-colors <?= in_array($sub_seg, ['pendaftaran_sidang', 'detail_pendaftaran_sidang']) ? 'text-orange-600 font-bold bg-orange-50/60' : '' ?>">
                            <i class="bi bi-mortarboard text-amber-500"></i>
                            <span>Pendaftaran Sidang</span>
                        </a>
                        <a href="<?= site_url('adminlayanan/lihat_pembimbing'); ?>" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:text-orange-600 hover:bg-orange-50 transition-colors <?= $sub_seg === 'lihat_pembimbing' ? 'text-orange-600 font-bold bg-orange-50/60' : '' ?>">
                            <i class="bi bi-people text-blue-500"></i>
                            <span>Lihat Pembimbing</span>
                        </a>
                        <a href="<?= site_url('adminlayanan/jadwal_sidang'); ?>" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:text-orange-600 hover:bg-orange-50 transition-colors <?= $sub_seg === 'jadwal_sidang' ? 'text-orange-600 font-bold bg-orange-50/60' : '' ?>">
                            <i class="bi bi-calendar-check text-cyan-500"></i>
                            <span>Jadwal Sidang</span>
                        </a>
                        <a href="<?= site_url('adminlayanan/bap_sidang'); ?>" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:text-orange-600 hover:bg-orange-50 transition-colors <?= in_array($sub_seg, ['bap_sidang', 'preview_bap', 'cetak_bap']) ? 'text-orange-600 font-bold bg-orange-50/60' : '' ?>">
                            <i class="bi bi-file-earmark-check text-rose-500"></i>
                            <span>BAP Sidang</span>
                        </a>
                        <a href="<?= site_url('adminlayanan/yudisium'); ?>" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:text-orange-600 hover:bg-orange-50 transition-colors <?= $sub_seg === 'yudisium' ? 'text-orange-600 font-bold bg-orange-50/60' : '' ?>">
                            <i class="bi bi-award text-purple-500"></i>
                            <span>Yudisium (S1 & S2)</span>
                        </a>
                        <a href="<?= site_url('adminlayanan/lulus_sidang'); ?>" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:text-orange-600 hover:bg-orange-50 transition-colors <?= $sub_seg === 'lulus_sidang' ? 'text-orange-600 font-bold bg-orange-50/60' : '' ?>">
                            <i class="bi bi-patch-check text-emerald-500"></i>
                            <span>Sudah Lulus Sidang</span>
                        </a>
                        <a href="<?= site_url('adminlayanan/status_peserta_ta'); ?>" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:text-orange-600 hover:bg-orange-50 transition-colors <?= $sub_seg === 'status_peserta_ta' ? 'text-orange-600 font-bold bg-orange-50/60' : '' ?>">
                            <i class="bi bi-person-lines-fill text-indigo-500"></i>
                            <span>Status Peserta TA</span>
                        </a>
                        <div class="my-1 border-t border-slate-100"></div>
                        <a href="<?= site_url('adminlayanan/ticketing'); ?>" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:text-orange-600 hover:bg-orange-50 transition-colors <?= in_array($sub_seg, ['ticketing', 'ticketing_input', 'ticketing_approval', 'ticketing_riwayat']) ? 'text-orange-600 font-bold bg-orange-50/60' : '' ?>">
                            <i class="bi bi-ticket-perforated text-purple-500"></i>
                            <span>Kelola Tiket LAA</span>
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Ketua KK / PIC KK - Role 1, 7, 9 -->
                <?php if (in_array($role_id, [1, 7, 9])): ?>
                <a href="<?= site_url('ketuakk'); ?>" 
                   class="whitespace-nowrap text-xs font-semibold flex items-center gap-1.5 transition-colors py-1 <?= $current_uri === 'ketuakk' ? 'text-orange-600 font-bold border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600'; ?>">
                    <i class="bi bi-diagram-3-fill <?= $current_uri === 'ketuakk' ? 'text-orange-600' : 'text-slate-400'; ?>"></i>
                    <span>Ketua KK</span>
                </a>
                <?php endif; ?>

                <!-- Koordinator TA - Role 1 or 6 -->
                <?php if (in_array($role_id, [1, 6])): ?>
                <a href="<?= site_url('koordinatorta'); ?>" 
                   class="whitespace-nowrap text-xs font-semibold flex items-center gap-1.5 transition-colors py-1 <?= $current_uri === 'koordinatorta' ? 'text-orange-600 font-bold border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600'; ?>">
                    <i class="bi bi-mortarboard-fill <?= $current_uri === 'koordinatorta' ? 'text-orange-600' : 'text-slate-400'; ?>"></i>
                    <span>Koor TA</span>
                </a>
                <?php endif; ?>

                <!-- Riwayat Log Approval - Global Access for Admin & Staff -->
                <?php if (in_array($role_id, [1, 2, 3, 5, 6, 7, 9, 21])): ?>
                <a href="<?= site_url('admin/log_history'); ?>" 
                   class="whitespace-nowrap text-xs font-semibold flex items-center gap-1.5 transition-colors py-1 <?= $current_uri === 'log_history' || $this->uri->segment(2) === 'log_history' ? 'text-orange-600 font-bold border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600'; ?>" title="Audit Trail & Riwayat Log Approval System (Seluruh Modul)">
                    <i class="bi bi-clock-history <?= $current_uri === 'log_history' || $this->uri->segment(2) === 'log_history' ? 'text-orange-600' : 'text-slate-400'; ?>"></i>
                    <span>Log History</span>
                </a>
                <?php endif; ?>

                <!-- Pusat Admin Hub (Hanya Admin System - Role 1) -->
                <?php if ($role_id == 1): ?>
                <a href="<?= site_url('admin'); ?>" 
                   class="whitespace-nowrap text-xs font-semibold flex items-center gap-1.5 transition-colors py-1 <?= $current_uri === 'admin' ? 'text-orange-600 font-bold border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600'; ?>">
                    <i class="bi bi-grid-fill <?= $current_uri === 'admin' ? 'text-orange-600' : 'text-slate-400'; ?>"></i>
                    <span>Pusat Admin</span>
                </a>
                <?php endif; ?>

                <!-- Super Admin Hub (Hanya Super Admin - Role 22) -->
                <?php if ($role_id == 22): ?>
                <a href="<?= site_url('import-email'); ?>" 
                   class="whitespace-nowrap text-xs font-semibold flex items-center gap-1.5 transition-colors py-1 <?= $current_uri === 'importemail' || $current_uri === 'import-email' ? 'text-orange-600 font-bold border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600'; ?>">
                    <i class="bi bi-people-fill <?= $current_uri === 'importemail' || $current_uri === 'import-email' ? 'text-orange-600' : 'text-slate-400'; ?>"></i>
                    <span>Panel Super Admin</span>
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

<!-- Global Custom Circle Cursor -->
<?php $this->load->view('partials/custom_cursor'); ?>
