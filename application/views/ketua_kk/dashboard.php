<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> - IFIK Portal</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }
        .clean-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col selection:bg-orange-600 selection:text-white">

    <!-- Header Navbar Partial -->
    <?php $this->load->view('partials/app_navbar', [
        'user_role_label'   => 'Ketua Kelompok Keahlian (KK)',
        'user_display_name' => 'Ketua KK Fakultas',
        'user_display_sub'  => 'Dewan Keahlian FIK'
    ]); ?>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full space-y-6">

        <!-- Flash Message -->
        <?php if($this->session->flashdata('success')): ?>
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-xs flex items-center justify-between text-xs">
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <p class="font-semibold"><?= $this->session->flashdata('success'); ?></p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold"><i class="bi bi-x-lg"></i></button>
            </div>
        <?php endif; ?>

        <!-- Welcome Banner & Page Title -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-orange-50 border border-orange-200 rounded-full text-[10px] font-bold text-orange-700 uppercase tracking-wider mb-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Tahap 04 Final Approval
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Portal Approval Ketua Kelompok Keahlian (KK)</h1>
                <p class="text-slate-500 text-xs mt-0.5">Tinjau kesesuaian topik TA dengan 4 rumpun kepakaran dan buka gembok akses modul bimbingan mahasiswa.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?= site_url('ketuakk'); ?>" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 rounded-xl border border-slate-200 text-xs font-semibold shadow-xs flex items-center gap-1.5 transition-all">
                    <i class="bi bi-arrow-clockwise"></i> Refresh Data
                </a>
            </div>
        </div>

        <!-- 4 Kelompok Keahlian Cards Navigation -->
        <div>
            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block mb-2">4 Kelompok Keahlian:</span>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                <?php 
                    $kk_icons = array('1' => 'bi-palette-fill', '2' => 'bi-box-seam-fill', '3' => 'bi-buildings-fill', '4' => 'bi-brush-fill');
                    foreach($all_kk as $kk): 
                        $is_active = ($selected_kk == $kk['id']);
                        $icon = $kk_icons[$kk['id']] ?? 'bi-diagram-3-fill';
                ?>
                    <a href="<?= site_url('ketuakk?kk=' . $kk['id'] . '&status=' . $filter_status); ?>" 
                       class="clean-card rounded-2xl p-4 flex flex-col justify-between transition-all <?= $is_active ? 'border-orange-500 bg-orange-50/50 ring-2 ring-orange-200' : 'hover:border-slate-300'; ?>">
                        <div class="flex items-center justify-between mb-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold <?= $is_active ? 'bg-orange-600 text-white' : 'bg-slate-100 text-slate-700 border border-slate-200'; ?>">
                                <?= $kk['kode_kk']; ?>
                            </span>
                            <div class="w-6 h-6 rounded-lg <?= $is_active ? 'bg-orange-500 text-white' : 'bg-slate-100 text-slate-500'; ?> flex items-center justify-center text-xs">
                                <i class="bi <?= $icon; ?>"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-bold text-xs text-slate-900 leading-snug mb-0.5"><?= htmlspecialchars($kk['nama_kk']); ?></h3>
                            <span class="text-[10px] text-slate-400 flex items-center gap-1">
                                <i class="bi bi-person text-slate-400"></i> <?= htmlspecialchars($kk['ketua_kk']); ?>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="clean-card rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <!-- Filter Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto w-full sm:w-auto pb-1 sm:pb-0">
                <a href="<?= site_url('ketuakk?kk=' . $selected_kk . '&status=all'); ?>" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap <?= $filter_status === 'all' ? 'bg-orange-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">
                    Semua (<?= $stats['total']; ?>)
                </a>
                <a href="<?= site_url('ketuakk?kk=' . $selected_kk . '&status=Pending'); ?>" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap <?= $filter_status === 'Pending' ? 'bg-amber-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">
                    Siap Review KK (<?= $stats['ready']; ?>)
                </a>
                <a href="<?= site_url('ketuakk?kk=' . $selected_kk . '&status=Approved'); ?>" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap <?= $filter_status === 'Approved' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">
                    Bimbingan Terbuka (<?= $stats['approved']; ?>)
                </a>
            </div>

            <!-- Search Bar -->
            <form method="GET" action="<?= site_url('ketuakk'); ?>" class="relative w-full sm:w-80">
                <input type="hidden" name="kk" value="<?= htmlspecialchars($selected_kk); ?>">
                <input type="hidden" name="status" value="<?= htmlspecialchars($filter_status); ?>">
                <input type="text" name="q" value="<?= htmlspecialchars($search ?? ''); ?>" 
                       placeholder="Cari mahasiswa atau judul..." 
                       class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 focus:bg-white transition-all">
                <i class="bi bi-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
            </form>
        </div>

        <!-- Table Card -->
        <div class="clean-card rounded-2xl overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase tracking-wider text-[10px] font-bold">
                            <th class="py-3.5 px-4 whitespace-nowrap">Mahasiswa</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">Kelompok Keahlian</th>
                            <th class="py-3.5 px-4">Judul Rencana TA</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap">Rantai Prasyarat</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap">Status Ketua KK</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap">Modul Bimbingan</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php if(empty($list_mahasiswa)): ?>
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <i class="bi bi-inbox text-3xl text-slate-300 block mb-2"></i>
                                    Tidak ada data mahasiswa pada kelompok keahlian ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($list_mahasiswa as $row): ?>
                                <?php
                                    $is_wali_app  = ($row['status_approval_wali'] === 'Approved');
                                    $is_admin_app = ($row['status_approval_admin'] === 'Approved');
                                    $is_koor_app  = ($row['status_approval_koor'] === 'Approved');
                                    $is_kk_app    = ($row['status_approval_kk'] === 'Approved');
                                    $is_kk_rej    = ($row['status_approval_kk'] === 'Rejected');
                                    $is_unlocked  = ($row['is_bimbingan_unlocked'] == 1);
                                ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <!-- Mahasiswa -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center flex-shrink-0">
                                                <?= strtoupper(substr($row['nama_depan'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 text-xs"><?= htmlspecialchars($row['nama_depan'] . ' ' . $row['nama_belakang']); ?></div>
                                                <div class="text-[11px] text-slate-400 font-mono"><?= htmlspecialchars($row['nim']); ?></div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- KK -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-slate-100 text-slate-700 rounded text-[10px] font-semibold border border-slate-200">
                                            <?= htmlspecialchars($row['kode_kk'] ?? 'KK-VCM'); ?>
                                        </span>
                                    </td>

                                    <!-- Judul TA -->
                                    <td class="py-3.5 px-4 min-w-[240px] max-w-xs">
                                        <div class="font-medium text-slate-800 line-clamp-2 text-xs leading-relaxed" title="<?= htmlspecialchars($row['judul_1']); ?>">
                                            <?= htmlspecialchars($row['judul_1']); ?>
                                        </div>
                                    </td>

                                    <!-- Rantai Prasyarat -->
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-200 text-[10px] font-mono">
                                            <span class="<?= $is_wali_app?'text-emerald-600 font-bold':'text-slate-400'; ?>">Wali</span>
                                            <span class="text-slate-300">›</span>
                                            <span class="<?= $is_admin_app?'text-emerald-600 font-bold':'text-slate-400'; ?>">LAA</span>
                                            <span class="text-slate-300">›</span>
                                            <span class="<?= $is_koor_app?'text-emerald-600 font-bold':'text-slate-400'; ?>">Koor</span>
                                        </div>
                                    </td>

                                    <!-- Status Ketua KK -->
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <?php if($is_kk_app): ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 whitespace-nowrap">
                                                <i class="bi bi-check-circle-fill text-emerald-500"></i> Disetujui KK
                                            </span>
                                        <?php elseif($is_kk_rej): ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 whitespace-nowrap">
                                                <i class="bi bi-x-circle-fill text-rose-500"></i> Ditolak KK
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 whitespace-nowrap">
                                                <i class="bi bi-clock text-amber-600"></i> Menunggu KK
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Akses Bimbingan -->
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <?php if($is_unlocked): ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-bold border border-emerald-200 whitespace-nowrap">
                                                <i class="bi bi-unlock-fill text-emerald-600"></i> Unlocked
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-medium border border-slate-200 whitespace-nowrap">
                                                <i class="bi bi-lock-fill text-slate-400"></i> Terkunci
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Aksi -->
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <a href="<?= site_url('ketuakk/detail/' . $row['nim']); ?>" 
                                           class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-semibold shadow-xs transition-all whitespace-nowrap">
                                            <i class="bi bi-shield-check"></i> Review
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 mt-8 text-center text-xs text-slate-500">
        &copy; <?= date('Y'); ?> Fakultas Industri Kreatif - Telkom University. Modul Ketua Kelompok Keahlian (KK).
    </footer>

</body>
</html>
