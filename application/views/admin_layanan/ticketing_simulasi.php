<?php
/**
 * Simulasi Alur Penanganan Tiket Layanan Akademik - Admin LAA
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Inbox Tiket — Admin LAA'; ?> - IFIK</title>

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
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body, button, input, textarea, select {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        /* Hide scrollbar for tabs */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-orange-50/20 to-slate-100 min-h-screen text-slate-800 antialiased">

    <!-- Dedicated Admin Layanan (LAA) Curved Sidebar -->
    <?php $this->load->view('admin_layanan/sidebar'); ?>

    <!-- Main Content Container (Balanced padding on mobile & desktop) -->
    <main class="min-h-screen p-4 sm:p-6 lg:p-10 max-w-7xl mx-auto">

        <!-- Header & Breadcrumb -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-[11px] sm:text-xs font-semibold text-slate-400 mb-2 pl-11 sm:pl-0 pt-0.5 sm:pt-0">
                <a href="<?= site_url('adminlayanan') ?>" class="hover:text-orange-600 transition-colors">Portal LAA</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-slate-600">Layanan Ticketing</span>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-orange-600 font-bold">Inbox Tiket Masuk</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2.5 sm:gap-3">
                        <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-gradient-to-tr from-orange-600 to-amber-500 text-white flex items-center justify-center shadow-lg shadow-orange-500/25 shrink-0">
                            <i class="bi bi-ticket-detailed-fill text-lg sm:text-xl"></i>
                        </span>
                        <span>Inbox Tiket Masuk Unit LAA</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
                        Tinjau dan tanggapi laporan kendala dari dosen dan mahasiswa yang ditujukan ke Layanan Akademik (LAA).
                    </p>
                </div>

                <div class="flex items-center gap-2 self-start sm:self-auto">
                    <span class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl bg-orange-100 text-orange-800 text-[11px] sm:text-xs font-extrabold tracking-wider uppercase border border-orange-200 shadow-xs">
                        <i class="bi bi-shield-check mr-1"></i> Panel LAA
                    </span>
                </div>
            </div>
        </div>

        <!-- Flash Alert Messages -->
        <?php if ($this->session->flashdata('success')): ?>
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs sm:text-sm flex items-start gap-3 shadow-xs">
                <i class="bi bi-check-circle-fill text-lg text-emerald-500 shrink-0 mt-0.5"></i>
                <div class="flex-1"><?= $this->session->flashdata('success'); ?></div>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs sm:text-sm flex items-start gap-3 shadow-xs">
                <i class="bi bi-exclamation-octagon-fill text-lg text-rose-500 shrink-0 mt-0.5"></i>
                <div class="flex-1"><?= $this->session->flashdata('error'); ?></div>
            </div>
        <?php endif; ?>

        <!-- Stats Overview Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">

            <!-- Total -->
            <div class="bg-white p-3.5 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs sm:shadow-sm flex items-center justify-between">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider block truncate">Total Tiket</span>
                    <span class="text-xl sm:text-3xl font-extrabold text-slate-800 mt-0.5 sm:mt-1 block"><?= (int)($stats['total'] ?? 0); ?></span>
                    <span class="text-[10px] sm:text-[11px] text-slate-400 font-medium truncate block">Semua tiket ke LAA</span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center text-lg sm:text-xl shrink-0 ml-2">
                    <i class="bi bi-ticket-perforated"></i>
                </div>
            </div>

            <!-- Menunggu -->
            <div class="bg-white p-3.5 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs sm:shadow-sm flex items-center justify-between">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-amber-500 uppercase tracking-wider block truncate">Menunggu</span>
                    <span class="text-xl sm:text-3xl font-extrabold text-amber-600 mt-0.5 sm:mt-1 block"><?= (int)($stats['menunggu'] ?? 0); ?></span>
                    <span class="text-[10px] sm:text-[11px] text-amber-500/80 font-medium truncate block">Belum ditanggapi</span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg sm:text-xl shrink-0 ml-2">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>

            <!-- Diproses -->
            <div class="bg-white p-3.5 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs sm:shadow-sm flex items-center justify-between">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-blue-500 uppercase tracking-wider block truncate">Diproses</span>
                    <span class="text-xl sm:text-3xl font-extrabold text-blue-600 mt-0.5 sm:mt-1 block"><?= (int)($stats['diproses'] ?? 0); ?></span>
                    <span class="text-[10px] sm:text-[11px] text-blue-500/80 font-medium truncate block">Sedang ditindak</span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg sm:text-xl shrink-0 ml-2">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
            </div>

            <!-- Selesai -->
            <div class="bg-white p-3.5 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs sm:shadow-sm flex items-center justify-between">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-emerald-500 uppercase tracking-wider block truncate">Selesai</span>
                    <span class="text-xl sm:text-3xl font-extrabold text-emerald-600 mt-0.5 sm:mt-1 block"><?= (int)($stats['selesai'] ?? 0); ?></span>
                    <span class="text-[10px] sm:text-[11px] text-emerald-500/80 font-medium truncate block">Telah selesai</span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg sm:text-xl shrink-0 ml-2">
                    <i class="bi bi-check2-all"></i>
                </div>
            </div>

        </div>

        <!-- Main Card Container: Filter, Desktop Table & Mobile Cards -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/40 overflow-hidden">

            <!-- Header Filter Bar -->
            <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">

                <!-- Status Filter Pills (client-side) -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 no-scrollbar" id="filter-pills">
                    <button type="button" onclick="filterByStatus('all', this)" class="status-btn active px-3 sm:px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-orange-600 text-white shadow-xs shrink-0">
                        Semua (<?= (int)($stats['total'] ?? 0); ?>)
                    </button>
                    <button type="button" onclick="filterByStatus('Menunggu', this)" class="status-btn px-3 sm:px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-all shrink-0">
                        Menunggu (<?= (int)($stats['menunggu'] ?? 0); ?>)
                    </button>
                    <button type="button" onclick="filterByStatus('Diproses', this)" class="status-btn px-3 sm:px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-all shrink-0">
                        Diproses (<?= (int)($stats['diproses'] ?? 0); ?>)
                    </button>
                    <button type="button" onclick="filterByStatus('Selesai', this)" class="status-btn px-3 sm:px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-all shrink-0">
                        Selesai (<?= (int)($stats['selesai'] ?? 0); ?>)
                    </button>
                    <button type="button" onclick="filterByStatus('Ditutup', this)" class="status-btn px-3 sm:px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-all shrink-0">
                        Ditutup (<?= (int)($stats['ditutup'] ?? 0); ?>)
                    </button>
                </div>

                <!-- Search Input -->
                <div class="relative w-full sm:w-72 shrink-0">
                    <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="searchInput" onkeyup="searchTable()"
                           placeholder="Cari kode, pengirim, subjek..."
                           class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white text-xs font-semibold text-slate-800 placeholder-slate-400 transition-all outline-hidden">
                </div>

            </div>

            <!-- ========================================== -->
            <!-- 1. DESKTOP / TABLET TABLE VIEW (>= md)    -->
            <!-- ========================================== -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse" id="ticketsTable">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/50 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                            <th class="py-3.5 px-6">Kode & Tanggal</th>
                            <th class="py-3.5 px-6">Pengirim</th>
                            <th class="py-3.5 px-6">Kategori & Subjek</th>
                            <th class="py-3.5 px-6 text-center">Prioritas</th>
                            <th class="py-3.5 px-6 text-center">Status</th>
                            <th class="py-3.5 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php if (empty($tickets)): ?>
                            <tr id="emptyRow">
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <div class="w-16 h-16 rounded-3xl bg-orange-50 text-orange-400 flex items-center justify-center mx-auto mb-3 text-2xl">
                                        <i class="bi bi-inbox-fill"></i>
                                    </div>
                                    <p class="font-bold text-slate-700">Belum Ada Tiket Masuk untuk Unit LAA</p>
                                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                        Tiket yang dikirim dengan tujuan Layanan Akademik (LAA) akan muncul di sini.
                                    </p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($tickets as $t):
                                // Prioritas Badge styling
                                $prioColor = 'bg-slate-100 text-slate-700 border-slate-200';
                                if ($t->prioritas === 'Sedang') $prioColor = 'bg-blue-50 text-blue-700 border-blue-200';
                                elseif ($t->prioritas === 'Tinggi') $prioColor = 'bg-amber-50 text-amber-700 border-amber-200';
                                elseif ($t->prioritas === 'Darurat') $prioColor = 'bg-rose-50 text-rose-700 border-rose-200 font-bold';

                                // Status Badge styling
                                $statusColor = 'bg-amber-50 text-amber-800 border-amber-200';
                                $waTickIcon  = '<i class="bi bi-check text-slate-400 font-extrabold text-sm" title="Terkirim (1 Ceklis)"></i>';
                                if ($t->status === 'Diproses') {
                                    $statusColor = 'bg-blue-50 text-blue-800 border-blue-200';
                                    $waTickIcon  = '<i class="bi bi-check-all text-slate-500 font-black text-base" title="Diterima & Ditinjau (2 Ceklis Abu-abu)"></i>';
                                } elseif ($t->status === 'Selesai') {
                                    $statusColor = 'bg-emerald-50 text-emerald-800 border-emerald-200';
                                    $waTickIcon  = '<i class="bi bi-check-all text-sky-500 font-black text-base" title="Selesai & Ditanggapi (2 Ceklis Biru)"></i>';
                                } elseif ($t->status === 'Ditutup') {
                                    $statusColor = 'bg-slate-100 text-slate-700 border-slate-300';
                                    $waTickIcon  = '<i class="bi bi-patch-check-fill text-purple-600 text-xs" title="Tiket Ditutup Tuntas"></i>';
                                }

                                $deskripsiSnippet = trim(html_entity_decode(strip_tags($t->deskripsi ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                                $deskripsiSnippet = preg_replace('/\s+/', ' ', $deskripsiSnippet);
                            ?>
                                <tr class="ticket-item ticket-row hover:bg-slate-50/70 transition-colors" data-status="<?= htmlspecialchars($t->status); ?>">

                                    <!-- Kode & Tanggal -->
                                    <td class="py-4 px-6">
                                        <span class="font-mono font-bold text-xs text-orange-600 block">
                                            <?= htmlspecialchars($t->kode_tiket); ?>
                                        </span>
                                        <span class="text-[11px] text-slate-400">
                                            <?= date('d M Y, H:i', strtotime($t->created_at)); ?>
                                        </span>
                                    </td>

                                    <!-- Pengirim -->
                                    <td class="py-4 px-6">
                                        <p class="font-bold text-slate-800 text-xs leading-snug"><?= htmlspecialchars($t->nama_dosen); ?></p>
                                        <p class="text-[10px] text-slate-400 font-mono mt-0.5">
                                            <?= htmlspecialchars(!empty($t->label_identitas) ? ($t->label_identitas . ': ') : '') . htmlspecialchars($t->nidn ?? '-'); ?>
                                        </p>
                                    </td>

                                    <!-- Kategori & Subjek -->
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-1.5 mb-0.5">
                                            <span class="w-2 h-2 rounded-full bg-orange-500 shrink-0"></span>
                                            <span class="text-[11px] font-bold text-slate-500"><?= htmlspecialchars($t->kategori); ?></span>
                                        </div>
                                        <span class="font-bold text-slate-800 block text-xs truncate max-w-xs" title="<?= htmlspecialchars($t->subjek); ?>">
                                            <?= htmlspecialchars($t->subjek); ?>
                                        </span>
                                        <p class="text-[11px] text-slate-400 line-clamp-1 max-w-xs mt-0.5">
                                            <?= htmlspecialchars(mb_substr($deskripsiSnippet, 0, 60)); ?>...
                                        </p>
                                    </td>

                                    <!-- Prioritas -->
                                    <td class="py-4 px-6 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border <?= $prioColor; ?>">
                                            <?= htmlspecialchars($t->prioritas); ?>
                                        </span>
                                    </td>

                                    <!-- Status -->
                                    <td class="py-4 px-6 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border <?= $statusColor; ?>">
                                            <?= $waTickIcon; ?>
                                            <span><?= htmlspecialchars($t->status); ?></span>
                                        </span>
                                    </td>

                                    <!-- Aksi -->
                                    <td class="py-4 px-6 text-center">
                                        <button type="button" onclick="openDetailModal('<?= $t->kode_tiket; ?>')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-orange-50 hover:bg-orange-600 hover:text-white text-orange-700 text-xs font-bold transition-all cursor-pointer shadow-xs"
                                                title="Tanggapi Tiket">
                                            <i class="bi bi-chat-left-text-fill text-[11px]"></i>
                                            <span>Tanggapi</span>
                                        </button>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                            <tr id="noResultsDesktop" style="display: none;">
                                <td colspan="6" class="py-10 text-center text-slate-400">
                                    <i class="bi bi-search text-2xl text-slate-300 block mb-2"></i>
                                    <p class="font-bold text-slate-600 text-xs">Tidak ada tiket yang sesuai dengan filter atau pencarian</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- ========================================== -->
            <!-- 2. MOBILE CARD VIEW (< md)                 -->
            <!-- ========================================== -->
            <div class="block md:hidden p-3.5 space-y-3 bg-slate-50/40" id="mobileCardsContainer">
                <?php if (empty($tickets)): ?>
                    <div class="py-10 text-center text-slate-400" id="emptyMobile">
                        <div class="w-14 h-14 rounded-2xl bg-orange-50 text-orange-400 flex items-center justify-center mx-auto mb-2.5 text-xl">
                            <i class="bi bi-inbox-fill"></i>
                        </div>
                        <p class="font-bold text-slate-700 text-sm">Belum Ada Tiket Masuk untuk Unit LAA</p>
                        <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">
                            Tiket yang dikirim dengan tujuan Layanan Akademik (LAA) akan muncul di sini.
                        </p>
                    </div>
                <?php else: ?>
                    <?php foreach ($tickets as $t):
                        $prioColor = 'bg-slate-100 text-slate-700 border-slate-200';
                        if ($t->prioritas === 'Sedang') $prioColor = 'bg-blue-50 text-blue-700 border-blue-200';
                        elseif ($t->prioritas === 'Tinggi') $prioColor = 'bg-amber-50 text-amber-700 border-amber-200';
                        elseif ($t->prioritas === 'Darurat') $prioColor = 'bg-rose-50 text-rose-700 border-rose-200 font-bold';

                        $statusColor = 'bg-amber-50 text-amber-800 border-amber-200';
                        $waTickIcon  = '<i class="bi bi-check text-slate-400 font-extrabold text-sm"></i>';
                        if ($t->status === 'Diproses') {
                            $statusColor = 'bg-blue-50 text-blue-800 border-blue-200';
                            $waTickIcon  = '<i class="bi bi-check-all text-slate-500 font-black text-sm"></i>';
                        } elseif ($t->status === 'Selesai') {
                            $statusColor = 'bg-emerald-50 text-emerald-800 border-emerald-200';
                            $waTickIcon  = '<i class="bi bi-check-all text-sky-500 font-black text-sm"></i>';
                        } elseif ($t->status === 'Ditutup') {
                            $statusColor = 'bg-slate-100 text-slate-700 border-slate-300';
                            $waTickIcon  = '<i class="bi bi-patch-check-fill text-purple-600 text-xs"></i>';
                        }

                        $deskripsiSnippet = trim(html_entity_decode(strip_tags($t->deskripsi ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                        $deskripsiSnippet = preg_replace('/\s+/', ' ', $deskripsiSnippet);
                    ?>
                        <!-- Mobile Ticket Card -->
                        <div class="ticket-item ticket-card bg-white rounded-2xl border border-slate-200 p-4 shadow-xs space-y-3 transition-all" data-status="<?= htmlspecialchars($t->status); ?>">
                            
                            <!-- Card Header: Kode & Status Badge -->
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <span class="font-mono font-bold text-xs text-orange-600 block"><?= htmlspecialchars($t->kode_tiket); ?></span>
                                    <span class="text-[10px] text-slate-400 flex items-center gap-1 mt-0.5">
                                        <i class="bi bi-calendar-event text-[9px]"></i>
                                        <?= date('d M Y, H:i', strtotime($t->created_at)); ?>
                                    </span>
                                </div>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold border shrink-0 <?= $statusColor; ?>">
                                    <?= $waTickIcon; ?>
                                    <span><?= htmlspecialchars($t->status); ?></span>
                                </span>
                            </div>

                            <!-- Pengirim & Kategori -->
                            <div class="flex items-center justify-between gap-2 pt-1 border-t border-slate-100 text-xs">
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-800 text-xs truncate"><?= htmlspecialchars($t->nama_dosen); ?></p>
                                    <p class="text-[10px] text-slate-400 font-mono"><?= htmlspecialchars(!empty($t->label_identitas) ? ($t->label_identitas . ': ') : '') . htmlspecialchars($t->nidn ?? '-'); ?></p>
                                </div>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-orange-50 text-orange-700 text-[10px] font-bold border border-orange-100 shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                                    <?= htmlspecialchars($t->kategori); ?>
                                </span>
                            </div>

                            <!-- Subjek & Snippet Deskripsi -->
                            <div class="bg-slate-50/80 rounded-xl p-2.5 border border-slate-100">
                                <h4 class="font-bold text-slate-800 text-xs line-clamp-1 mb-0.5"><?= htmlspecialchars($t->subjek); ?></h4>
                                <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">
                                    <?= htmlspecialchars(mb_substr($deskripsiSnippet, 0, 90)); ?><?= mb_strlen($deskripsiSnippet) > 90 ? '...' : ''; ?>
                                </p>
                            </div>

                            <!-- Bottom Row: Prioritas & Aksi -->
                            <div class="flex items-center justify-between gap-2 pt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold border <?= $prioColor; ?>">
                                    Prioritas: <?= htmlspecialchars($t->prioritas); ?>
                                </span>
                                <button type="button" onclick="openDetailModal('<?= $t->kode_tiket; ?>')"
                                        class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold transition-all shadow-xs active:scale-95 cursor-pointer">
                                    <i class="bi bi-chat-left-text-fill text-[11px]"></i>
                                    <span>Tanggapi</span>
                                </button>
                            </div>

                        </div>
                    <?php endforeach; ?>
                    <div id="noResultsMobile" style="display: none;" class="py-8 text-center text-slate-400 bg-white rounded-2xl border border-slate-200 p-4">
                        <i class="bi bi-search text-xl text-slate-300 block mb-1.5"></i>
                        <p class="font-bold text-slate-600 text-xs">Tidak ada tiket yang sesuai dengan filter atau pencarian</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>

    </main>

    <!-- Modal Detail & Form Tanggapan Admin LAA -->
    <div id="modalDetail" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-3 sm:p-4 hidden">
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-2xl max-w-2xl w-full max-h-[92vh] overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-150">

            <!-- Modal Header -->
            <div class="px-4 sm:px-6 py-3.5 sm:py-4 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-orange-50/50 to-transparent">
                <div class="flex flex-wrap items-center gap-2">
                    <span id="mStatusBadge" class="px-2.5 py-0.5 rounded-full text-[11px] sm:text-xs font-bold border bg-amber-50 text-amber-700 border-amber-200">Menunggu</span>
                    <span id="mKode" class="text-xs font-mono font-bold text-orange-600 uppercase tracking-wider">TIK-XXXXXXXX-XXXX</span>
                    <span id="mPrioritasBadge" class="px-2 py-0.5 rounded-md text-[10px] font-bold border bg-slate-100 text-slate-700">Sedang</span>
                </div>
                <button type="button" onclick="closeModal()" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition-colors shrink-0">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-4 sm:p-6 space-y-4 sm:space-y-5 overflow-y-auto text-xs sm:text-sm flex-1">

                <!-- Info Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 sm:gap-3 p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-50 border border-slate-100 text-xs">
                    <div>
                        <span class="text-slate-400 block mb-0.5">Pengirim:</span>
                        <strong id="mNamaDosen" class="text-slate-700 font-semibold block truncate">-</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">NIM / NIDN:</span>
                        <strong id="mNidn" class="text-slate-700 font-semibold font-mono block truncate">-</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Kategori:</span>
                        <strong id="mKategori" class="text-slate-700 font-semibold block truncate">-</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Subjek:</span>
                        <strong id="mSubjek" class="text-slate-700 font-semibold block text-xs leading-snug truncate">-</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Status:</span>
                        <strong id="mStatusText" class="text-slate-700 font-semibold block truncate">-</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Diajukan Pada:</span>
                        <strong id="mWaktu" class="text-slate-700 font-semibold block truncate">-</strong>
                    </div>
                </div>

                <!-- Deskripsi Lengkap -->
                <div>
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Deskripsi Kendala</h4>
                    <div id="mDeskripsi" class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-50/70 border border-slate-100 text-slate-700 text-xs leading-relaxed space-y-2 max-h-36 sm:max-h-44 overflow-y-auto"></div>
                </div>

                <!-- Lampiran Berkas Direct Preview -->
                <div id="mLampiranContainer" class="hidden space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Lampiran Berkas</h4>
                        <a id="mLampiranLink" href="#" target="_blank"
                           class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-orange-50 hover:bg-orange-100 text-orange-700 text-xs font-bold border border-orange-200 transition-colors">
                            <i class="bi bi-box-arrow-up-right"></i>
                            <span>Buka / Unduh</span>
                        </a>
                    </div>

                    <!-- Image Preview Container -->
                    <div id="mLampiranImgContainer" class="hidden rounded-2xl border border-slate-200 bg-slate-100/70 p-2 overflow-hidden flex flex-col items-center justify-center">
                        <a id="mLampiranImgLink" href="#" target="_blank" class="block group relative w-full text-center">
                            <img id="mLampiranImg" src="" alt="Pratinjau Lampiran" class="max-h-56 sm:max-h-72 w-auto max-w-full mx-auto rounded-xl object-contain shadow-xs transition-transform duration-200 group-hover:scale-[1.01]">
                            <div class="absolute inset-0 bg-slate-900/10 opacity-0 group-hover:opacity-100 transition-opacity rounded-xl flex items-center justify-center pointer-events-none">
                                <span class="px-3 py-1.5 rounded-xl bg-slate-900/80 text-white text-xs font-bold backdrop-blur-xs flex items-center gap-1.5">
                                    <i class="bi bi-arrows-fullscreen"></i> Klik untuk ukuran penuh
                                </span>
                            </div>
                        </a>
                    </div>

                    <!-- PDF Preview Container -->
                    <div id="mLampiranPdfContainer" class="hidden rounded-2xl border border-slate-200 overflow-hidden bg-slate-100">
                        <div class="p-2.5 px-3 bg-slate-800 text-white text-xs font-bold flex items-center justify-between">
                            <span class="flex items-center gap-1.5 truncate">
                                <i class="bi bi-file-earmark-pdf-fill text-rose-400"></i>
                                <span id="mLampiranPdfName" class="truncate">Dokumen PDF</span>
                            </span>
                            <a id="mLampiranPdfLink" href="#" target="_blank" class="text-xs text-orange-300 hover:text-orange-200 underline font-semibold flex items-center gap-1 shrink-0">
                                <i class="bi bi-box-arrow-up-right"></i> Tab Baru
                            </a>
                        </div>
                        <iframe id="mLampiranPdfFrame" src="about:blank" class="w-full h-56 sm:h-72 border-0"></iframe>
                    </div>

                    <!-- Generic File Card Container -->
                    <div id="mLampiranGenericContainer" class="hidden">
                        <a id="mLampiranGenericLink" href="#" target="_blank"
                           class="flex items-center gap-3 p-3.5 rounded-2xl bg-orange-50/70 hover:bg-orange-100/80 border border-orange-200 text-orange-900 transition-all group">
                            <div class="w-10 h-10 rounded-xl bg-orange-500 text-white flex items-center justify-center font-bold text-lg shadow-sm shrink-0">
                                <i class="bi bi-file-earmark-arrow-down-fill"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div id="mLampiranGenericName" class="text-xs font-bold truncate">Nama File</div>
                                <div class="text-[11px] text-orange-700/80">Klik untuk mengunduh / membuka berkas</div>
                            </div>
                            <i class="bi bi-download text-orange-600 text-sm group-hover:translate-y-0.5 transition-transform"></i>
                        </a>
                    </div>
                </div>

                <hr class="border-slate-100">

                <!-- Tanggapan Sebelumnya (jika ada) -->
                <div id="mTanggapanSebelumnyaBox" class="hidden">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tanggapan Sebelumnya</h4>
                    <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-emerald-50/60 border border-emerald-100 text-xs text-emerald-900 leading-relaxed">
                        <div id="mTanggapanSebelumnya"></div>
                        <span id="mTglTanggapan" class="text-[11px] text-emerald-600/80 mt-1.5 block italic"></span>
                    </div>
                </div>

                <!-- Form Tanggapan Admin LAA -->
                <form id="formTanggapan" action="<?= site_url('adminlayanan/ticketing/simpan_tanggapan'); ?>" method="POST" class="space-y-4">
                    <input type="hidden" id="formIdTiket" name="id_tiket" value="">

                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-2">
                            Ubah Status Tiket <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            <label class="flex items-center justify-center gap-2 p-2 sm:p-2.5 rounded-xl border border-slate-200 cursor-pointer hover:border-amber-400 has-checked:border-amber-500 has-checked:bg-amber-50/50 has-checked:font-bold text-center text-xs transition-all">
                                <input type="radio" name="status" value="Menunggu" class="text-amber-600 focus:ring-amber-500">
                                <span>Menunggu</span>
                            </label>
                            <label class="flex items-center justify-center gap-2 p-2 sm:p-2.5 rounded-xl border border-slate-200 cursor-pointer hover:border-blue-400 has-checked:border-blue-500 has-checked:bg-blue-50/50 has-checked:font-bold text-center text-xs transition-all">
                                <input type="radio" name="status" value="Diproses" class="text-blue-600 focus:ring-blue-500">
                                <span>Diproses</span>
                            </label>
                            <label class="flex items-center justify-center gap-2 p-2 sm:p-2.5 rounded-xl border border-slate-200 cursor-pointer hover:border-emerald-400 has-checked:border-emerald-500 has-checked:bg-emerald-50/50 has-checked:font-bold text-center text-xs transition-all">
                                <input type="radio" name="status" value="Selesai" class="text-emerald-600 focus:ring-emerald-500">
                                <span>Selesai</span>
                            </label>
                            <label class="flex items-center justify-center gap-2 p-2 sm:p-2.5 rounded-xl border border-slate-200 cursor-pointer hover:border-purple-400 has-checked:border-purple-500 has-checked:bg-purple-50/50 has-checked:font-bold text-center text-xs transition-all">
                                <input type="radio" name="status" value="Ditutup" class="text-purple-600 focus:ring-purple-500">
                                <span>Ditutup</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="formTanggapanText" class="block text-xs font-bold text-slate-800 mb-1.5">
                            Tanggapan / Solusi Admin LAA <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="formTanggapanText" name="tanggapan" rows="3" required
                                  placeholder="Tuliskan respon, verifikasi, atau arahan penyelesaian untuk pengirim tiket..."
                                  class="w-full p-3 sm:p-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white text-xs font-medium text-slate-800 placeholder-slate-400 transition-all outline-hidden"></textarea>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2 sm:gap-2.5 pt-1">
                        <button type="button" onclick="closeModal()" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors text-center">
                            Batal
                        </button>
                        <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold transition-all text-xs shadow-xs flex items-center justify-center gap-1.5">
                            <i class="bi bi-send-check-fill"></i> Simpan & Kirim Tanggapan
                        </button>
                    </div>
                </form>

            </div>

        </div>
    </div>

    <!-- Scripts -->
    <script>
        let currentStatusFilter = 'all';

        // Filter by Status Pill (client-side)
        function filterByStatus(status, btn) {
            currentStatusFilter = status;
            document.querySelectorAll('.status-btn').forEach(b => {
                b.classList.remove('active', 'bg-orange-600', 'text-white', 'shadow-xs');
                b.classList.add('text-slate-500', 'hover:bg-slate-100');
            });
            btn.classList.add('active', 'bg-orange-600', 'text-white', 'shadow-xs');
            btn.classList.remove('text-slate-500', 'hover:bg-slate-100');

            applyFilters();
        }

        // Search in Table and Mobile Cards
        function searchTable() {
            applyFilters();
        }

        function applyFilters() {
            const query = (document.getElementById('searchInput').value || '').toLowerCase().trim();
            let visibleDesktop = 0;
            let visibleMobile = 0;

            // 1. Filter Desktop Rows
            const desktopRows = document.querySelectorAll('.ticket-row');
            desktopRows.forEach(row => {
                const rowStatus = row.getAttribute('data-status');
                const matchesStatus = (currentStatusFilter === 'all' || rowStatus === currentStatusFilter);
                const matchesQuery = !query || row.textContent.toLowerCase().includes(query);
                const show = matchesStatus && matchesQuery;
                row.style.display = show ? '' : 'none';
                if (show) visibleDesktop++;
            });

            // 2. Filter Mobile Cards
            const mobileCards = document.querySelectorAll('.ticket-card');
            mobileCards.forEach(card => {
                const cardStatus = card.getAttribute('data-status');
                const matchesStatus = (currentStatusFilter === 'all' || cardStatus === currentStatusFilter);
                const matchesQuery = !query || card.textContent.toLowerCase().includes(query);
                const show = matchesStatus && matchesQuery;
                card.style.display = show ? '' : 'none';
                if (show) visibleMobile++;
            });

            // 3. Toggle Empty State messages when filtering returns 0 items
            const noResDesktop = document.getElementById('noResultsDesktop');
            if (noResDesktop) {
                noResDesktop.style.display = (visibleDesktop === 0 && desktopRows.length > 0) ? '' : 'none';
            }

            const noResMobile = document.getElementById('noResultsMobile');
            if (noResMobile) {
                noResMobile.style.display = (visibleMobile === 0 && mobileCards.length > 0) ? '' : 'none';
            }
        }

        // AJAX Modal Detail + Tanggapi
        function openDetailModal(kodeTiket) {
            fetch('<?= site_url("adminlayanan/ticketing/detail/"); ?>' + encodeURIComponent(kodeTiket))
                .then(res => res.json())
                .then(res => {
                    if (res.status && res.data) {
                        const d = res.data;

                        document.getElementById('formIdTiket').value       = d.id;
                        document.getElementById('mKode').textContent        = d.kode_tiket;
                        document.getElementById('mNamaDosen').textContent   = d.nama_dosen;
                        document.getElementById('mNidn').textContent        = d.nidn || '-';
                        document.getElementById('mWaktu').textContent       = d.created_at || '-';
                        document.getElementById('mKategori').textContent    = d.kategori;
                        document.getElementById('mSubjek').textContent      = d.subjek;
                        document.getElementById('mStatusText').textContent  = d.status;
                        document.getElementById('mDeskripsi').innerHTML     = d.deskripsi;

                        // Status badge in header
                        const statusColors = {
                            'Menunggu': 'bg-amber-50 text-amber-700 border-amber-200',
                            'Diproses': 'bg-blue-50 text-blue-700 border-blue-200',
                            'Selesai':  'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'Ditutup':  'bg-slate-100 text-slate-600 border-slate-300',
                        };
                        const statusBadge = document.getElementById('mStatusBadge');
                        statusBadge.textContent = d.status;
                        statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[11px] sm:text-xs font-bold border ' + (statusColors[d.status] || 'bg-slate-100 text-slate-600 border-slate-300');

                        document.getElementById('mPrioritasBadge').textContent = d.prioritas;

                        // Pre-select current radio status
                        document.querySelectorAll('input[name="status"]').forEach(r => {
                            r.checked = (r.value === d.status);
                        });

                        // Tanggapan sebelumnya
                        const prevBox = document.getElementById('mTanggapanSebelumnyaBox');
                        if (d.tanggapan && d.tanggapan.trim()) {
                            prevBox.classList.remove('hidden');
                            document.getElementById('mTanggapanSebelumnya').innerHTML = d.tanggapan;
                            document.getElementById('mTglTanggapan').textContent = d.tgl_tanggapan
                                ? 'Ditanggapi pada: ' + d.tgl_tanggapan
                                : '';
                        } else {
                            prevBox.classList.add('hidden');
                        }
                        document.getElementById('formTanggapanText').value = d.tanggapan || '';

                        // Lampiran direct preview
                        renderAdminLampiran(d.lampiran, d.lampiran_url);

                        document.getElementById('modalDetail').classList.remove('hidden');
                    } else {
                        alert(res.message || 'Gagal memuat detail tiket.');
                    }
                })
                .catch(() => alert('Gagal menghubungi server untuk mengambil detail tiket.'));
        }

        function renderAdminLampiran(lampiranFilename, lampiranUrl) {
            const section = document.getElementById('mLampiranContainer');
            if (!section) return;

            if (!lampiranFilename || !lampiranUrl) {
                section.classList.add('hidden');
                return;
            }

            section.classList.remove('hidden');

            const topLink = document.getElementById('mLampiranLink');
            if (topLink) topLink.href = lampiranUrl;

            const imgContainer = document.getElementById('mLampiranImgContainer');
            const imgEl = document.getElementById('mLampiranImg');
            const imgLink = document.getElementById('mLampiranImgLink');

            const pdfContainer = document.getElementById('mLampiranPdfContainer');
            const pdfFrame = document.getElementById('mLampiranPdfFrame');
            const pdfName = document.getElementById('mLampiranPdfName');
            const pdfLink = document.getElementById('mLampiranPdfLink');

            const genContainer = document.getElementById('mLampiranGenericContainer');
            const genLink = document.getElementById('mLampiranGenericLink');
            const genName = document.getElementById('mLampiranGenericName');

            if (imgContainer) imgContainer.classList.add('hidden');
            if (pdfContainer) {
                pdfContainer.classList.add('hidden');
                if (pdfFrame) pdfFrame.src = 'about:blank';
            }
            if (genContainer) genContainer.classList.add('hidden');

            const ext = lampiranFilename.split('.').pop().toLowerCase();
            const imageExtensions = ['png', 'jpg', 'jpeg', 'webp', 'gif', 'svg', 'bmp'];

            if (imageExtensions.includes(ext)) {
                if (imgContainer && imgEl) {
                    imgEl.src = lampiranUrl;
                    if (imgLink) imgLink.href = lampiranUrl;
                    imgContainer.classList.remove('hidden');
                }
            } else if (ext === 'pdf') {
                if (pdfContainer && pdfFrame) {
                    pdfFrame.src = lampiranUrl;
                    if (pdfName) pdfName.textContent = lampiranFilename;
                    if (pdfLink) pdfLink.href = lampiranUrl;
                    pdfContainer.classList.remove('hidden');
                }
            } else {
                if (genContainer && genName) {
                    genName.textContent = lampiranFilename;
                    if (genLink) genLink.href = lampiranUrl;
                    genContainer.classList.remove('hidden');
                }
            }
        }

        function closeModal() {
            document.getElementById('modalDetail').classList.add('hidden');
        }

        // Close on ESC
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeModal();
        });

        // Close on backdrop click
        document.getElementById('modalDetail').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>
</body>
</html>
