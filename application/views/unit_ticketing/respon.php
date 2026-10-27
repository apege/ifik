<?php
/**
 * View Inbox Respon Tiket - Unit Ticketing Panel
 * Reusable for: Kemahasiswaan, Sekretariat, SDM & Keuangan, Program Studi
 */
$baseResponUrl = $baseResponUrl ?? ($baseRoute . '/respon-ticketing');
$baseInputUrl   = $baseInputUrl   ?? ($baseRoute . '/ticketing/input');
$baseRiwayatUrl = $baseRiwayatUrl ?? ($baseRoute . '/ticketing/riwayat');
$panelBadge     = $panelBadge     ?? 'Unit Ticketing';
$panelTitle     = $panelTitle     ?? 'Inbox Respon Tiket';
$panelSubtitle  = $panelSubtitle  ?? 'Tinjau dan respon laporan kendala yang ditujukan kepada unit Anda.';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? ($panelTitle . ' — IFIK Portal'); ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #f8fafc;
            color: #1e293b;
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
        }
        .glass-header {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
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
<body class="antialiased">

    <!-- Include Curved Sidebar -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <div class="page-wrapper-for-sidebar">
    <!-- Header Navigation -->
    <header class="glass-header sticky top-0 z-30 px-4 sm:px-6 py-3.5 sm:py-4 pl-16 sm:pl-20">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
            <div class="flex items-start sm:items-center gap-3 sm:gap-3.5 min-w-0">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-orange-100 text-brand-600 flex items-center justify-center font-bold text-lg sm:text-xl shadow-xs border border-orange-200/50 shrink-0 mt-0.5 sm:mt-0">
                    <i class="bi bi-inbox-fill"></i>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                        <h1 class="text-sm sm:text-xl font-extrabold text-slate-900 tracking-tight leading-snug"><?= htmlspecialchars($panelTitle); ?></h1>
                        <span class="px-2.5 py-0.5 rounded-md bg-orange-100 text-orange-800 text-[10px] font-extrabold tracking-wider uppercase border border-orange-200 shrink-0"><?= htmlspecialchars($panelBadge); ?></span>
                    </div>
                    <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5 line-clamp-1 sm:line-clamp-none"><?= htmlspecialchars($panelSubtitle); ?></p>
                </div>
            </div>

            <!-- Quick Action Links (Only 3 menus) -->
            <div class="flex items-center gap-2 w-full sm:w-auto flex-wrap sm:flex-nowrap">
                <a href="<?= site_url($baseInputUrl); ?>" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-xs transition-all">
                    <i class="bi bi-plus-circle-fill"></i> Membuat Ticketing
                </a>
                <a href="<?= site_url($baseRiwayatUrl); ?>" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all border border-slate-200">
                    <i class="bi bi-clock-history"></i> Riwayat
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

        <!-- Flash Messages -->
        <?php if ($this->session->flashdata('success')): ?>
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl shadow-xs mb-6 flex items-center justify-between text-xs font-semibold">
                <div class="flex items-center gap-2.5">
                    <i class="bi bi-check-circle-fill text-emerald-600 text-base"></i>
                    <span><?= $this->session->flashdata('success'); ?></span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700"><i class="bi bi-x-lg"></i></button>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl shadow-xs mb-6 flex items-center justify-between text-xs font-semibold">
                <div class="flex items-center gap-2.5">
                    <i class="bi bi-exclamation-triangle-fill text-rose-600 text-base"></i>
                    <span><?= $this->session->flashdata('error'); ?></span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700"><i class="bi bi-x-lg"></i></button>
            </div>
        <?php endif; ?>

        <!-- Stats Overview Cards (3 Status Sesuai DB) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-8">
            <!-- Total -->
            <a href="<?= site_url($baseResponUrl . '?status=all'); ?>" class="bg-white p-4 rounded-2xl border <?= $filterStatus === 'all' ? 'border-orange-500 ring-2 ring-orange-100' : 'border-slate-200/80 hover:border-slate-300' ?> shadow-xs transition-all flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Masuk</span>
                    <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xs"><i class="bi bi-inbox-fill"></i></span>
                </div>
                <div class="text-2xl font-extrabold text-slate-900 mt-2 font-mono"><?= $stats['total'] ?? 0; ?></div>
            </a>

            <!-- Dikirim -->
            <a href="<?= site_url($baseResponUrl . '?status=Dikirim'); ?>" class="bg-white p-4 rounded-2xl border <?= in_array($filterStatus, ['Dikirim', 'Menunggu']) ? 'border-amber-500 ring-2 ring-amber-100' : 'border-slate-200/80 hover:border-slate-300' ?> shadow-xs transition-all flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">Dikirim</span>
                    <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs"><i class="bi bi-send-check"></i></span>
                </div>
                <div class="text-2xl font-extrabold text-amber-600 mt-2 font-mono"><?= $stats['dikirim'] ?? ($stats['menunggu'] ?? 0); ?></div>
            </a>

            <!-- Sedang Diproses -->
            <a href="<?= site_url($baseResponUrl . '?status=Sedang Diproses'); ?>" class="bg-white p-4 rounded-2xl border <?= in_array($filterStatus, ['Sedang Diproses', 'Diproses']) ? 'border-blue-500 ring-2 ring-blue-100' : 'border-slate-200/80 hover:border-slate-300' ?> shadow-xs transition-all flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Sedang Diproses</span>
                    <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs"><i class="bi bi-gear-fill"></i></span>
                </div>
                <div class="text-2xl font-extrabold text-blue-600 mt-2 font-mono"><?= $stats['sedang_diproses'] ?? ($stats['diproses'] ?? 0); ?></div>
            </a>

            <!-- Closed -->
            <a href="<?= site_url($baseResponUrl . '?status=Closed'); ?>" class="bg-white p-4 rounded-2xl border <?= in_array($filterStatus, ['Closed', 'Selesai', 'Ditutup']) ? 'border-emerald-500 ring-2 ring-emerald-100' : 'border-slate-200/80 hover:border-slate-300' ?> shadow-xs transition-all flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Closed</span>
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs"><i class="bi bi-check2-circle"></i></span>
                </div>
                <div class="text-2xl font-extrabold text-emerald-600 mt-2 font-mono"><?= $stats['closed'] ?? ($stats['selesai'] ?? 0); ?></div>
            </a>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs mb-6 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
            <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto pb-2 md:pb-0 scrollbar-none" style="-webkit-overflow-scrolling: touch;">
                <?php
                $statusPills = [
                    'all'             => 'Semua Tiket',
                    'Dikirim'         => 'Dikirim',
                    'Sedang Diproses' => 'Sedang Diproses',
                    'Closed'          => 'Closed'
                ];
                foreach ($statusPills as $stKey => $stLabel):
                    $isActive = ($filterStatus === $stKey);
                ?>
                    <a href="<?= site_url($baseResponUrl . '?status=' . $stKey . ($search ? '&q=' . urlencode($search) : '')); ?>" 
                       class="shrink-0 px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all <?= $isActive ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600' ?>">
                        <?= $stLabel; ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Search Form -->
            <form method="GET" action="<?= site_url($baseResponUrl); ?>" class="w-full md:w-72 relative">
                <input type="hidden" name="status" value="<?= htmlspecialchars($filterStatus); ?>">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400 text-xs">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="q" value="<?= htmlspecialchars($search); ?>" placeholder="Cari kode, pengirim, kendala..." 
                           class="w-full pl-9 pr-8 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-medium">
                    <?php if (!empty($search)): ?>
                        <a href="<?= site_url($baseResponUrl . '?status=' . $filterStatus); ?>" class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400 hover:text-slate-600 text-xs">
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
                            <th class="py-3.5 px-4 font-extrabold">Pengirim</th>
                            <th class="py-3.5 px-4 font-extrabold">Kendala & Kategori</th>
                            <th class="py-3.5 px-4 font-extrabold">Prioritas</th>
                            <th class="py-3.5 px-4 font-extrabold">Status</th>
                            <th class="py-3.5 px-4 font-extrabold">Waktu Masuk</th>
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
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-slate-800"><?= htmlspecialchars($t->nama_dosen ?: '-'); ?></div>
                                        <div class="text-[10px] text-slate-400 font-mono"><?= htmlspecialchars($t->nidn ?: ($t->id_user ?? '-')); ?></div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-slate-800 line-clamp-1"><?= htmlspecialchars($t->subjek ?: '-'); ?></div>
                                        <div class="text-[10px] text-slate-500 flex items-center gap-1.5 mt-0.5">
                                            <span class="inline-block px-1.5 py-0.2 rounded bg-slate-100 text-slate-600 font-medium"><?= htmlspecialchars($t->kategori ?: 'Umum'); ?></span>
                                            <?php if (!empty($t->unit_terkait)): ?>
                                                <span class="inline-block px-1.5 py-0.2 rounded bg-orange-50 text-orange-700 font-medium"><?= htmlspecialchars($t->unit_terkait); ?></span>
                                            <?php endif; ?>
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
                                        <button type="button" onclick="openResponModal('<?= htmlspecialchars($kode); ?>')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-orange-600 text-white text-[11px] font-bold transition-all shadow-xs">
                                            <i class="bi bi-reply-fill"></i> Tanggapi
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <i class="bi bi-inbox text-4xl block mb-2 text-slate-300"></i>
                                    <p class="font-semibold text-xs">Belum ada tiket masuk untuk kriteria ini.</p>
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
                        <?php
                        $kode = $t->kode_tiket ?: $t->id;
                        ?>
                        <div class="p-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-xs font-bold text-slate-800"><?= htmlspecialchars($kode); ?></span>
                                <span class="text-[10px] text-slate-400"><?= !empty($t->created_at) ? date('d M H:i', strtotime($t->created_at)) : '-'; ?></span>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-xs line-clamp-1"><?= htmlspecialchars($t->subjek ?: '-'); ?></h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">Dari: <span class="font-semibold text-slate-700"><?= htmlspecialchars($t->nama_dosen ?: '-'); ?></span></p>
                            </div>
                            <div class="flex items-center justify-between pt-1">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-700"><?= htmlspecialchars($t->status); ?></span>
                                <button type="button" onclick="openResponModal('<?= htmlspecialchars($kode); ?>')" class="px-3 py-1 bg-slate-900 text-white rounded-lg text-xs font-bold hover:bg-orange-600">
                                    Tanggapi
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="p-8 text-center text-slate-400 text-xs">
                        Tidak ada tiket ditemukan.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    </div>

    <!-- Modal Respon Tiket -->
    <div id="modalRespon" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs overflow-y-auto flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl border border-slate-200 overflow-hidden my-8 animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-sm">
                        <i class="bi bi-chat-square-text-fill"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-sm">Respon & Tindak Lanjut Tiket</h3>
                        <p class="text-[11px] text-slate-400 font-mono" id="modalKodeTiket">-</p>
                    </div>
                </div>
                <button type="button" onclick="closeResponModal()" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>

            <!-- Modal Content & Form -->
            <form id="formRespon" method="POST" action="<?= site_url($baseResponUrl . '/simpan_tanggapan'); ?>" class="p-6 space-y-4">
                <input type="hidden" name="id_tiket" id="modalInputId">

                <!-- Detail Tiket Summary -->
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/60 space-y-2 text-xs">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <span class="text-slate-400 font-medium">Pelapor:</span>
                            <div class="font-bold text-slate-800" id="modalPelapor">-</div>
                        </div>
                        <div>
                            <span class="text-slate-400 font-medium">Kategori:</span>
                            <div class="font-bold text-slate-800" id="modalKategori">-</div>
                        </div>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium">Subjek:</span>
                        <div class="font-bold text-slate-800" id="modalSubjek">-</div>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium">Deskripsi:</span>
                        <div class="text-slate-700 bg-white p-3 rounded-xl border border-slate-200/60 max-h-36 overflow-y-auto leading-relaxed mt-1" id="modalDeskripsi">-</div>
                    </div>
                    <div id="modalLampiranContainer" class="hidden pt-1">
                        <span class="text-slate-400 font-medium">Lampiran:</span>
                        <div class="mt-1">
                            <a id="modalLampiranLink" href="#" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-orange-50 text-orange-700 hover:bg-orange-100 text-xs font-bold border border-orange-200/60">
                                <i class="bi bi-file-earmark-arrow-down-fill"></i> Unduh Berkas Lampiran
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Update Status Stepper -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Perbarui Status Tiket <span class="text-rose-500">*</span></label>
                    <select name="status" id="modalStatusSelect" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 bg-white">
                        <option value="Dikirim">1. Dikirim (Antrean Masuk)</option>
                        <option value="Sedang Diproses">2. Sedang Diproses (Sedang Ditangani)</option>
                        <option value="Closed">3. Closed (Selesai / Ditutup)</option>
                    </select>
                </div>

                <!-- Respon / Tanggapan Textarea -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggapan / Catatan Penanganan <span class="text-rose-500">*</span></label>
                    <textarea name="tanggapan" id="modalTanggapan" rows="4" required placeholder="Tuliskan tanggapan, progres perbaikan, atau solusi kendala untuk pelapor..."
                              class="w-full p-3 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 resize-none font-medium leading-relaxed"></textarea>
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeResponModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition-all">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-xs transition-all flex items-center gap-1.5">
                        <i class="bi bi-send-fill"></i> Simpan Respon
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script AJAX Modal -->
    <script>
        function openResponModal(kodeTiket) {
            const modal = document.getElementById('modalRespon');
            modal.classList.remove('hidden');

            document.getElementById('modalKodeTiket').textContent = 'Memuat data...';
            document.getElementById('modalInputId').value = kodeTiket;
            document.getElementById('modalPelapor').textContent = '-';
            document.getElementById('modalKategori').textContent = '-';
            document.getElementById('modalSubjek').textContent = '-';
            document.getElementById('modalDeskripsi').innerHTML = '<span class="text-slate-400 italic">Memuat detail tiket...</span>';
            document.getElementById('modalLampiranContainer').classList.add('hidden');

            fetch('<?= site_url($baseResponUrl . "/detail/"); ?>' + encodeURIComponent(kodeTiket))
                .then(r => r.json())
                .then(res => {
                    if (res.status && res.data) {
                        const d = res.data;
                        document.getElementById('modalKodeTiket').textContent = '#' + (d.kode_tiket || d.id);
                        document.getElementById('modalInputId').value = d.id;
                        document.getElementById('modalPelapor').textContent = d.nama_dosen || d.nama || '-';
                        document.getElementById('modalKategori').textContent = d.kategori || 'Umum';
                        document.getElementById('modalSubjek').textContent = d.subjek || '-';
                        document.getElementById('modalDeskripsi').innerHTML = d.deskripsi || '-';

                        if (d.status) {
                            document.getElementById('modalStatusSelect').value = d.status;
                        }
                        if (d.tanggapan) {
                            document.getElementById('modalTanggapan').value = d.tanggapan;
                        } else {
                            document.getElementById('modalTanggapan').value = '';
                        }

                        if (d.lampiran_url) {
                            document.getElementById('modalLampiranContainer').classList.remove('hidden');
                            document.getElementById('modalLampiranLink').href = d.lampiran_url;
                        } else {
                            document.getElementById('modalLampiranContainer').classList.add('hidden');
                        }
                    } else {
                        Swal.fire('Error', res.message || 'Gagal memuat detail tiket.', 'error');
                        closeResponModal();
                    }
                })
                .catch(err => {
                    Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error');
                    closeResponModal();
                });
        }

        function closeResponModal() {
            document.getElementById('modalRespon').classList.add('hidden');
        }
    </script>
</body>
</html>
