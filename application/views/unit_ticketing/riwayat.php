<?php
/**
 * View Riwayat Tiket - Unit Ticketing Panel
 * Reusable for: Kemahasiswaan, Sekretariat, SDM & Keuangan, Program Studi
 */
$baseResponUrl  = $baseResponUrl  ?? ($baseRoute . '/respon-ticketing');
$baseInputUrl    = $baseInputUrl    ?? ($baseRoute . '/ticketing/input');
$baseRiwayatUrl  = $baseRiwayatUrl  ?? ($baseRoute . '/ticketing/riwayat');
$panelBadge      = $panelBadge      ?? 'Unit Ticketing';
$panelTitle      = $panelTitle      ?? 'Riwayat Tiket Saya';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? ($panelTitle . ' — IFIK Portal'); ?></title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            900: '#7c2d12',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body, button, input, textarea, select {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        .page-wrapper-for-sidebar {
            width: 100%;
            min-width: 0;
            min-height: 100vh;
            transition: margin-left 0.75s cubic-bezier(0.76, 0, 0.24, 1), width 0.75s cubic-bezier(0.76, 0, 0.24, 1);
            box-sizing: border-box;
        }

        @media (min-width: 1024px) {
            .page-wrapper-for-sidebar {
                margin-left: 270px;
                width: calc(100% - 270px);
            }

            body.curved-sidebar-desktop-collapsed .page-wrapper-for-sidebar {
                margin-left: 0;
                width: 100%;
            }
        }

        @media (max-width: 1023.98px) {
            .page-wrapper-for-sidebar {
                margin-left: 0 !important;
                width: 100% !important;
                padding-top: 56px;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-orange-50/20 to-slate-100 min-h-screen text-slate-800 antialiased">

    <!-- Include Curved Sidebar -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <div class="page-wrapper-for-sidebar">
    <!-- Main Content -->
    <main class="min-h-screen p-6 sm:p-8 lg:p-10 max-w-7xl mx-auto">
        
        <!-- Header & Breadcrumb -->
        <div class="mb-8">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-2">
                <a href="<?= site_url($baseResponUrl) ?>" class="hover:text-orange-600 transition-colors"><?= htmlspecialchars($panelBadge) ?></a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-slate-600">Ticketing</span>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-orange-600 font-bold">Riwayat Tiket</span>
            </div>
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                        <span class="w-10 h-10 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center text-xl shadow-xs">
                            <i class="bi bi-clock-history"></i>
                        </span>
                        <span><?= htmlspecialchars($panelTitle); ?></span>
                    </h1>
                    <p class="text-sm text-slate-500 mt-1">Daftar seluruh laporan kendala yang pernah Anda ajukan melalui sistem.</p>
                </div>
                
                <div class="flex items-center gap-2">
                    <a href="<?= site_url($baseInputUrl); ?>" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold transition-all shadow-md shadow-orange-600/20">
                        <i class="bi bi-plus-circle-fill text-sm"></i>
                        <span>Membuat Ticketing</span>
                    </a>
                    <a href="<?= site_url($baseResponUrl); ?>" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200/80 text-slate-700 hover:text-orange-600 hover:border-orange-200 text-xs font-bold transition-all shadow-xs">
                        <i class="bi bi-inbox-fill text-sm"></i>
                        <span>Respon Ticketing</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if ($this->session->flashdata('success')): ?>
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-xs font-semibold shadow-xs">
                <div class="flex items-center gap-2">
                    <i class="bi bi-check-circle-fill text-emerald-600 text-base"></i>
                    <span><?= $this->session->flashdata('success'); ?></span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700"><i class="bi bi-x-lg"></i></button>
            </div>
        <?php endif; ?>

        <!-- Stats Overview Cards (3 Status Sesuai DB) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-8">
            <a href="<?= site_url($baseRiwayatUrl . '?status=all'); ?>" class="bg-white p-4 rounded-2xl border <?= $filterStatus === 'all' ? 'border-orange-500 ring-2 ring-orange-100' : 'border-slate-200/80 hover:border-slate-300' ?> shadow-xs transition-all flex flex-col justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Diajukan</span>
                <div class="text-2xl font-extrabold text-slate-900 mt-2 font-mono"><?= $stats['total'] ?? 0; ?></div>
            </a>
            <a href="<?= site_url($baseRiwayatUrl . '?status=Dikirim'); ?>" class="bg-white p-4 rounded-2xl border <?= in_array($filterStatus, ['Dikirim', 'Menunggu']) ? 'border-amber-500 ring-2 ring-amber-100' : 'border-slate-200/80 hover:border-slate-300' ?> shadow-xs transition-all flex flex-col justify-between">
                <span class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">Dikirim</span>
                <div class="text-2xl font-extrabold text-amber-600 mt-2 font-mono"><?= $stats['dikirim'] ?? ($stats['menunggu'] ?? 0); ?></div>
            </a>
            <a href="<?= site_url($baseRiwayatUrl . '?status=Sedang Diproses'); ?>" class="bg-white p-4 rounded-2xl border <?= in_array($filterStatus, ['Sedang Diproses', 'Diproses']) ? 'border-blue-500 ring-2 ring-blue-100' : 'border-slate-200/80 hover:border-slate-300' ?> shadow-xs transition-all flex flex-col justify-between">
                <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Sedang Diproses</span>
                <div class="text-2xl font-extrabold text-blue-600 mt-2 font-mono"><?= $stats['sedang_diproses'] ?? ($stats['diproses'] ?? 0); ?></div>
            </a>
            <a href="<?= site_url($baseRiwayatUrl . '?status=Closed'); ?>" class="bg-white p-4 rounded-2xl border <?= in_array($filterStatus, ['Closed', 'Selesai', 'Ditutup']) ? 'border-emerald-500 ring-2 ring-emerald-100' : 'border-slate-200/80 hover:border-slate-300' ?> shadow-xs transition-all flex flex-col justify-between">
                <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Closed</span>
                <div class="text-2xl font-extrabold text-emerald-600 mt-2 font-mono"><?= $stats['closed'] ?? ($stats['selesai'] ?? 0); ?></div>
            </a>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs mb-6 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
            <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto pb-2 md:pb-0 scrollbar-none" style="-webkit-overflow-scrolling: touch;">
                <?php
                $statusPills = [
                    'all'             => 'Semua',
                    'Dikirim'         => 'Dikirim',
                    'Sedang Diproses' => 'Sedang Diproses',
                    'Closed'          => 'Closed'
                ];
                foreach ($statusPills as $stKey => $stLabel):
                    $isActive = ($filterStatus === $stKey);
                ?>
                    <a href="<?= site_url($baseRiwayatUrl . '?status=' . $stKey . ($search ? '&q=' . urlencode($search) : '')); ?>" 
                       class="shrink-0 px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all <?= $isActive ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600' ?>">
                        <?= $stLabel; ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Search Form -->
            <form method="GET" action="<?= site_url($baseRiwayatUrl); ?>" class="w-full md:w-72 relative">
                <input type="hidden" name="status" value="<?= htmlspecialchars($filterStatus); ?>">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400 text-xs">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="q" value="<?= htmlspecialchars($search); ?>" placeholder="Cari kode tiket, kendala..." 
                           class="w-full pl-9 pr-8 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-medium">
                    <?php if (!empty($search)): ?>
                        <a href="<?= site_url($baseRiwayatUrl . '?status=' . $filterStatus); ?>" class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400 hover:text-slate-600 text-xs">
                            <i class="bi bi-x-circle-fill"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Table & Mobile Cards -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <!-- Desktop Table (md:block) -->
            <div class="overflow-x-auto hidden md:block">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-extrabold uppercase text-slate-500 tracking-wider">
                            <th class="py-3.5 px-4 font-extrabold">Kode Tiket</th>
                            <th class="py-3.5 px-4 font-extrabold">Unit Tujuan</th>
                            <th class="py-3.5 px-4 font-extrabold">Subjek & Kategori</th>
                            <th class="py-3.5 px-4 font-extrabold">Prioritas</th>
                            <th class="py-3.5 px-4 font-extrabold">Status</th>
                            <th class="py-3.5 px-4 font-extrabold">Tanggal Diajukan</th>
                            <th class="py-3.5 px-4 font-extrabold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        <?php if (!empty($tickets)): ?>
                            <?php foreach ($tickets as $t): ?>
                                <?php
                                $kode = $t->kode_tiket ?: $t->id;
                                $statusBadge = [
                                    'Menunggu' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'icon' => 'bi-hourglass-split'],
                                    'Diproses' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'icon' => 'bi-gear-fill'],
                                    'Selesai'  => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'icon' => 'bi-check2-circle'],
                                    'Ditutup'  => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'border' => 'border-slate-200', 'icon' => 'bi-archive-fill']
                                ][$t->status] ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'icon' => 'bi-question-circle'];

                                $prioritasBadge = [
                                    'Darurat' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200'],
                                    'Tinggi'  => ['bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'border' => 'border-orange-200'],
                                    'Sedang'  => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200'],
                                    'Rendah'  => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'border' => 'border-slate-200']
                                ][$t->prioritas] ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'border' => 'border-slate-200'];
                                ?>
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3 px-4 font-mono font-bold text-slate-800 text-[11px] whitespace-nowrap">
                                        <?= htmlspecialchars($kode); ?>
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <div class="font-bold text-slate-800"><?= htmlspecialchars($t->unit_tujuan ?: ($t->tujuan_penerima ?: 'Umum')); ?></div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-slate-800 line-clamp-1"><?= htmlspecialchars($t->subjek ?: '-'); ?></div>
                                        <div class="text-[10px] text-slate-500 mt-0.5">
                                            <span class="inline-block px-1.5 py-0.2 rounded bg-slate-100 text-slate-600 font-medium"><?= htmlspecialchars($t->kategori ?: 'Umum'); ?></span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border <?= $prioritasBadge['bg'] ?> <?= $prioritasBadge['text'] ?> <?= $prioritasBadge['border'] ?>">
                                            <?= htmlspecialchars($t->prioritas ?: 'Sedang'); ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold border <?= $statusBadge['bg'] ?> <?= $statusBadge['text'] ?> <?= $statusBadge['border'] ?>">
                                            <i class="bi <?= $statusBadge['icon'] ?>"></i>
                                            <?= htmlspecialchars($t->status ?: 'Menunggu'); ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-slate-500 text-[11px] whitespace-nowrap">
                                        <?= !empty($t->created_at) ? date('d M Y H:i', strtotime($t->created_at)) : '-'; ?>
                                    </td>
                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        <button type="button" onclick="openDetailModal('<?= htmlspecialchars($kode); ?>')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-orange-600 hover:text-white text-slate-700 text-[11px] font-bold transition-all border border-slate-200">
                                            <i class="bi bi-eye-fill"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <i class="bi bi-journal-text text-4xl block mb-2 text-slate-300"></i>
                                    <p class="font-semibold text-xs">Belum ada riwayat tiket yang Anda ajukan.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile View (Cards) -->
            <div class="divide-y divide-slate-100 md:hidden">
                <?php if (!empty($tickets)): ?>
                    <?php foreach ($tickets as $t): ?>
                        <?php $kode = $t->kode_tiket ?: $t->id; ?>
                        <div class="p-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-xs font-bold text-slate-800"><?= htmlspecialchars($kode); ?></span>
                                <span class="text-[10px] text-slate-400"><?= !empty($t->created_at) ? date('d M Y', strtotime($t->created_at)) : '-'; ?></span>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-xs"><?= htmlspecialchars($t->subjek ?: '-'); ?></h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">Tujuan: <?= htmlspecialchars($t->unit_tujuan ?: '-'); ?></p>
                            </div>
                            <div class="flex items-center justify-between pt-1">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-700"><?= htmlspecialchars($t->status); ?></span>
                                <button type="button" onclick="openDetailModal('<?= htmlspecialchars($kode); ?>')" class="px-3 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-bold border border-slate-200 hover:bg-orange-600 hover:text-white">
                                    Detail
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="p-8 text-center text-slate-400 text-xs">Belum ada riwayat tiket.</div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    </div>

    <!-- Modal Detail Tiket -->
    <div id="modalDetail" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs overflow-y-auto flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl border border-slate-200 overflow-hidden my-8 animate-in fade-in zoom-in-95 duration-200">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-sm">
                        <i class="bi bi-file-earmark-text-fill"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-sm">Detail Tiket Kendala</h3>
                        <p class="text-[11px] text-slate-400 font-mono" id="detailKodeTiket">-</p>
                    </div>
                </div>
                <button type="button" onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>

            <div class="p-6 space-y-4 text-xs">
                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200/60">
                    <div>
                        <span class="text-slate-400 font-medium">Unit Tujuan:</span>
                        <div class="font-bold text-slate-800" id="detailUnit">-</div>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium">Kategori:</span>
                        <div class="font-bold text-slate-800" id="detailKategori">-</div>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium">Prioritas:</span>
                        <div class="font-bold text-slate-800" id="detailPrioritas">-</div>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium">Status Penanganan:</span>
                        <div class="font-bold text-orange-600" id="detailStatus">-</div>
                    </div>
                </div>

                <!-- Subjek & Deskripsi -->
                <div>
                    <span class="text-slate-400 font-medium">Subjek Kendala:</span>
                    <div class="font-bold text-slate-900 text-sm mt-0.5" id="detailSubjek">-</div>
                </div>
                <div>
                    <span class="text-slate-400 font-medium">Deskripsi Lengkap:</span>
                    <div class="text-slate-700 bg-slate-50 p-3.5 rounded-xl border border-slate-200/60 max-h-48 overflow-y-auto leading-relaxed mt-1" id="detailDeskripsi">-</div>
                </div>

                <!-- Lampiran -->
                <div id="detailLampiranContainer" class="hidden">
                    <span class="text-slate-400 font-medium">Lampiran Bukti:</span>
                    <div class="mt-1">
                        <a id="detailLampiranLink" href="#" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-orange-50 text-orange-700 hover:bg-orange-100 font-bold border border-orange-200/60">
                            <i class="bi bi-paperclip"></i> Unduh Lampiran
                        </a>
                    </div>
                </div>

                <!-- Tanggapan / Solusi Petugas -->
                <div id="detailTanggapanContainer" class="pt-3 border-t border-slate-100">
                    <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Tanggapan & Solusi dari Unit Tujuan:</span>
                    <div class="mt-1 p-3.5 rounded-xl bg-orange-50/50 border border-orange-200 text-slate-800 leading-relaxed font-medium" id="detailTanggapan">
                        <span class="text-slate-400 italic">Belum ada tanggapan untuk tiket ini.</span>
                    </div>
                </div>

                <div class="flex justify-end pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeDetailModal()" class="px-5 py-2 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Modal Detail -->
    <script>
        function openDetailModal(kodeTiket) {
            const modal = document.getElementById('modalDetail');
            modal.classList.remove('hidden');

            document.getElementById('detailKodeTiket').textContent = '#' + kodeTiket;
            document.getElementById('detailUnit').textContent = '-';
            document.getElementById('detailKategori').textContent = '-';
            document.getElementById('detailPrioritas').textContent = '-';
            document.getElementById('detailStatus').textContent = '-';
            document.getElementById('detailSubjek').textContent = '-';
            document.getElementById('detailDeskripsi').innerHTML = '<span class="text-slate-400 italic">Memuat data...</span>';
            document.getElementById('detailLampiranContainer').classList.add('hidden');
            document.getElementById('detailTanggapan').innerHTML = '<span class="text-slate-400 italic">Memuat...</span>';

            fetch('<?= site_url($baseRoute . "/ticketing/detail/"); ?>' + encodeURIComponent(kodeTiket))
                .then(r => r.json())
                .then(res => {
                    if (res.status && res.data) {
                        const d = res.data;
                        document.getElementById('detailKodeTiket').textContent = '#' + (d.kode_tiket || d.id);
                        document.getElementById('detailUnit').textContent = d.unit_tujuan || '-';
                        document.getElementById('detailKategori').textContent = d.kategori || 'Umum';
                        document.getElementById('detailPrioritas').textContent = d.prioritas || 'Sedang';
                        document.getElementById('detailStatus').textContent = d.status || 'Menunggu';
                        document.getElementById('detailSubjek').textContent = d.subjek || '-';
                        document.getElementById('detailDeskripsi').innerHTML = d.deskripsi || '-';

                        if (d.tanggapan) {
                            document.getElementById('detailTanggapan').innerHTML = d.tanggapan;
                        } else {
                            document.getElementById('detailTanggapan').innerHTML = '<span class="text-slate-400 italic">Belum ada tanggapan resmi dari unit tujuan.</span>';
                        }

                        if (d.lampiran_url) {
                            document.getElementById('detailLampiranContainer').classList.remove('hidden');
                            document.getElementById('detailLampiranLink').href = d.lampiran_url;
                        } else {
                            document.getElementById('detailLampiranContainer').classList.add('hidden');
                        }
                    } else {
                        Swal.fire('Error', res.message || 'Gagal memuat detail tiket.', 'error');
                        closeDetailModal();
                    }
                })
                .catch(err => {
                    Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error');
                    closeDetailModal();
                });
        }

        function closeDetailModal() {
            document.getElementById('modalDetail').classList.add('hidden');
        }
    </script>
</body>
</html>
