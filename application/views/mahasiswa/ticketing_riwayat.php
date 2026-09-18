<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Riwayat Tiket Kendala — Mahasiswa IFIK'; ?> - IFIK</title>

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
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-orange-50/20 to-slate-100 min-h-screen text-slate-800 antialiased">

    <!-- Include Curved Sidebar (Panel Mahasiswa) -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <!-- Main Content Container with Left Padding for Sidebar Burger -->
    <main class="min-h-screen p-6 sm:p-8 lg:p-10 max-w-7xl mx-auto pl-16">
        
        <!-- Header & Breadcrumb -->
        <div class="mb-8">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-2">
                <a href="<?= site_url('mahasiswa') ?>" class="hover:text-orange-600 transition-colors">Portal Mahasiswa</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-slate-600">Layanan Ticketing</span>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-orange-600 font-bold">Riwayat Tiket Saya</span>
            </div>
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3">
                        <span class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-orange-600 to-amber-500 text-white flex items-center justify-center shadow-lg shadow-orange-500/25">
                            <i class="bi bi-clock-history text-xl"></i>
                        </span>
                        Riwayat & Status Tiket Mahasiswa
                    </h1>
                    <p class="text-sm text-slate-500 mt-1.5 max-w-2xl">
                        Pantau progres respon dan tindak lanjut atas kendala yang telah Anda ajukan ke berbagai unit kerja kampus.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="<?= site_url('mahasiswa/ticketing/input') ?>" 
                       class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white text-sm font-bold shadow-lg shadow-orange-500/25 transition-all">
                        <i class="bi bi-plus-lg text-base"></i>
                        <span>Buat Tiket Baru</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Flash Alert Messages -->
        <?php if ($this->session->flashdata('success')): ?>
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-start gap-3 shadow-xs">
                <i class="bi bi-check-circle-fill text-lg text-emerald-500 shrink-0 mt-0.5"></i>
                <div class="flex-1"><?= $this->session->flashdata('success'); ?></div>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-sm flex items-start gap-3 shadow-xs">
                <i class="bi bi-exclamation-octagon-fill text-lg text-rose-500 shrink-0 mt-0.5"></i>
                <div class="flex-1"><?= $this->session->flashdata('error'); ?></div>
            </div>
        <?php endif; ?>

        <!-- Stats Overview Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            
            <!-- Total -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Tiket</span>
                    <span class="text-2xl sm:text-3xl font-extrabold text-slate-800 mt-1 block"><?= (int)($stats['total'] ?? 0); ?></span>
                    <span class="text-[11px] text-slate-400 font-medium">Tiket diajukan</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center text-xl shrink-0">
                    <i class="bi bi-ticket-perforated"></i>
                </div>
            </div>

            <!-- Menunggu -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-amber-500 uppercase tracking-wider block">Menunggu</span>
                    <span class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-1 block"><?= (int)($stats['menunggu'] ?? 0); ?></span>
                    <span class="text-[11px] text-amber-500/80 font-medium">Belum diproses</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>

            <!-- Diproses -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-blue-500 uppercase tracking-wider block">Diproses</span>
                    <span class="text-2xl sm:text-3xl font-extrabold text-blue-600 mt-1 block"><?= (int)($stats['diproses'] ?? 0); ?></span>
                    <span class="text-[11px] text-blue-500/80 font-medium">Sedang ditindak</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
            </div>

            <!-- Selesai -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-emerald-500 uppercase tracking-wider block">Selesai</span>
                    <span class="text-2xl sm:text-3xl font-extrabold text-emerald-600 mt-1 block"><?= (int)($stats['selesai'] ?? 0); ?></span>
                    <span class="text-[11px] text-emerald-500/80 font-medium">Telah terselesaikan</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                    <i class="bi bi-check2-all"></i>
                </div>
            </div>

        </div>

        <!-- Table Container -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/40 overflow-hidden">
            
            <!-- Table Header Filter Bar -->
            <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                
                <!-- Status Filter Pills -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1.5 sm:pb-0 scrollbar-none" id="filter-pills" style="-webkit-overflow-scrolling: touch;">
                    <button type="button" onclick="filterByStatus('all', this)" class="status-btn active px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-orange-600 text-white shadow-xs shrink-0 cursor-pointer">
                        Semua (<?= (int)($stats['total'] ?? 0); ?>)
                    </button>
                    <button type="button" onclick="filterByStatus('Menunggu', this)" class="status-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-all shrink-0 cursor-pointer">
                        Menunggu (<?= (int)($stats['menunggu'] ?? 0); ?>)
                    </button>
                    <button type="button" onclick="filterByStatus('Diproses', this)" class="status-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-all shrink-0 cursor-pointer">
                        Diproses (<?= (int)($stats['diproses'] ?? 0); ?>)
                    </button>
                    <button type="button" onclick="filterByStatus('Selesai', this)" class="status-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-all shrink-0 cursor-pointer">
                        Selesai (<?= (int)($stats['selesai'] ?? 0); ?>)
                    </button>
                    <button type="button" onclick="filterByStatus('Ditutup', this)" class="status-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-all shrink-0 cursor-pointer">
                        Ditutup (<?= (int)($stats['ditutup'] ?? 0); ?>)
                    </button>
                </div>

                <!-- Search Input -->
                <div class="relative w-full sm:w-72">
                    <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="searchInput" onkeyup="searchTable()"
                           placeholder="Cari kode, subjek, tujuan..."
                           class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white text-xs font-semibold text-slate-800 placeholder-slate-400 transition-all outline-hidden">
                </div>

            </div>

            <!-- Desktop Table View (hidden on mobile, shown on md: and up) -->
            <div class="overflow-x-auto hidden md:block">
                <table class="w-full text-left border-collapse" id="ticketsTable">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/50 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                            <th class="py-3.5 px-6">Kode & Tanggal</th>
                            <th class="py-3.5 px-6">Tujuan & Kategori</th>
                            <th class="py-3.5 px-6">Subjek Kendala</th>
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
                                    <p class="font-bold text-slate-700">Belum Ada Tiket yang Diajukan</p>
                                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                        Jika Anda memiliki kendala akademik, fasilitas lab, atau administrasi, silakan ajukan tiket baru.
                                    </p>
                                    <a href="<?= site_url('mahasiswa/ticketing/input') ?>" 
                                       class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-xs transition-colors">
                                        <i class="bi bi-plus-lg"></i>
                                        <span>Buat Tiket Sekarang</span>
                                    </a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($tickets as $t): ?>
                                <?php
                                    // Prioritas Badge styling
                                    $prioColor = 'bg-slate-100 text-slate-700 border-slate-200';
                                    if ($t->prioritas === 'Sedang') $prioColor = 'bg-blue-50 text-blue-700 border-blue-200';
                                    elseif ($t->prioritas === 'Tinggi') $prioColor = 'bg-amber-50 text-amber-700 border-amber-200';
                                    elseif ($t->prioritas === 'Darurat') $prioColor = 'bg-rose-50 text-rose-700 border-rose-200';

                                    // Status Badge styling ala WhatsApp ticks
                                    $statusColor = 'bg-amber-50 text-amber-800 border-amber-200';
                                    $waTickIcon = '<i class="bi bi-check text-slate-400 font-extrabold text-sm" title="Terkirim ke Server (1 Ceklis)"></i>';
                                    if ($t->status === 'Diproses') {
                                        $statusColor = 'bg-blue-50 text-blue-800 border-blue-200';
                                        $waTickIcon = '<i class="bi bi-check-all text-slate-500 font-black text-base" title="Diterima & Ditinjau Unit (2 Ceklis Abu-abu)"></i>';
                                    } elseif ($t->status === 'Selesai') {
                                        $statusColor = 'bg-emerald-50 text-emerald-800 border-emerald-200';
                                        $waTickIcon = '<i class="bi bi-check-all text-sky-500 font-black text-base" title="Selesai & Ditanggapi (2 Ceklis Biru WhatsApp)"></i>';
                                    } elseif ($t->status === 'Ditutup') {
                                        $statusColor = 'bg-slate-100 text-slate-700 border-slate-300';
                                        $waTickIcon = '<i class="bi bi-patch-check-fill text-purple-600 text-xs" title="Tiket Ditutup Tuntas"></i>';
                                    }

                                    // Cuplikan teks bersih tanpa tag HTML dan entitas &nbsp;
                                    $cleanSnippet = trim(strip_tags(str_ireplace(['&nbsp;', '&amp;nbsp;'], ' ', $t->deskripsi ?? '')));
                                    $cleanSnippet = preg_replace('/\s+/u', ' ', html_entity_decode($cleanSnippet, ENT_QUOTES, 'UTF-8'));
                                    $tanggapanClean = trim(strip_tags(str_ireplace(['&nbsp;', '&amp;nbsp;'], ' ', $t->tanggapan ?? '')));
                                    $tanggapanClean = preg_replace('/\s+/u', ' ', html_entity_decode($tanggapanClean, ENT_QUOTES, 'UTF-8'));
                                ?>
                                <tr class="ticket-row hover:bg-slate-50/70 transition-colors" data-status="<?= htmlspecialchars($t->status); ?>">
                                    
                                    <!-- Kode & Tanggal -->
                                    <td class="py-4 px-6">
                                        <span class="font-mono font-bold text-xs text-orange-600 bg-orange-50 px-2.5 py-1 rounded-lg border border-orange-100/60 block w-fit">
                                            <?= htmlspecialchars($t->kode_tiket); ?>
                                        </span>
                                        <span class="text-[11px] text-slate-400 mt-1 block">
                                            <i class="bi bi-clock mr-1"></i><?= date('d M Y, H:i', strtotime($t->created_at)); ?> WIB
                                        </span>
                                    </td>

                                    <!-- Tujuan & Kategori -->
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-orange-500 shrink-0"></span>
                                            <span class="text-xs font-bold text-slate-800"><?= htmlspecialchars($t->unit_tujuan ?: 'Layanan IFIK'); ?></span>
                                        </div>
                                        <div class="text-[11px] text-slate-500 mt-0.5 truncate max-w-xs pl-3.5">
                                            <?= htmlspecialchars($t->kategori); ?>
                                        </div>
                                    </td>

                                    <!-- Subjek Kendala -->
                                    <td class="py-4 px-6">
                                        <span class="font-bold text-slate-800 block text-xs truncate max-w-xs" title="<?= htmlspecialchars($t->subjek); ?>">
                                            <?= htmlspecialchars($t->subjek); ?>
                                        </span>
                                        <p class="text-[11px] text-slate-400 line-clamp-1 max-w-xs mt-0.5">
                                            <?= htmlspecialchars($cleanSnippet); ?>
                                        </p>
                                    </td>

                                    <!-- Prioritas -->
                                    <td class="py-4 px-6 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border <?= $prioColor; ?>">
                                            <?= htmlspecialchars($t->prioritas); ?>
                                        </span>
                                    </td>

                                    <!-- Status (Interactive Popover Tracking) -->
                                    <td class="py-4 px-6 text-center relative">
                                        <div class="inline-block relative">
                                            <span class="status-badge-trigger inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold border cursor-pointer select-none transition-transform active:scale-95 <?= $statusColor; ?>"
                                                  data-kode="<?= htmlspecialchars($t->kode_tiket); ?>"
                                                  data-status="<?= htmlspecialchars($t->status); ?>"
                                                  data-unit="<?= htmlspecialchars($t->unit_tujuan); ?>"
                                                  data-created="<?= date('d M Y, H:i', strtotime($t->created_at)); ?>"
                                                  data-updated="<?= !empty($t->updated_at) ? date('d M Y, H:i', strtotime($t->updated_at)) : '-'; ?>"
                                                  data-tgl-tanggapan="<?= !empty($t->tgl_tanggapan) ? date('d M Y, H:i', strtotime($t->tgl_tanggapan)) : ''; ?>"
                                                  data-tanggapan="<?= htmlspecialchars($tanggapanClean); ?>">
                                                <?= $waTickIcon; ?>
                                                <span><?= htmlspecialchars($t->status); ?></span>
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Aksi -->
                                    <td class="py-4 px-6 text-center">
                                        <button type="button" onclick="showTicketDetail('<?= $t->kode_tiket; ?>')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-orange-50 hover:bg-orange-100 text-orange-700 text-xs font-bold transition-colors cursor-pointer"
                                                title="Lihat Detail & Jawaban">
                                            <i class="bi bi-eye-fill"></i>
                                            <span>Detail</span>
                                        </button>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <tr id="filterEmptyRow" class="hidden">
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <i class="bi bi-search text-3xl text-slate-300 block mb-2"></i>
                                <p class="font-bold text-slate-700 text-sm">Tidak ada tiket yang cocok</p>
                                <p class="text-xs text-slate-400 mt-1">Coba ubah kata kunci pencarian atau filter status.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View (shown on mobile, hidden on md: and up) -->
            <div class="block md:hidden p-3.5 sm:p-4 space-y-3 bg-slate-50/50" id="ticketsCardsContainer">
                <?php if (empty($tickets)): ?>
                    <div class="py-10 text-center text-slate-400 bg-white rounded-2xl border border-slate-200/80 p-5">
                        <div class="w-14 h-14 rounded-2xl bg-orange-50 text-orange-400 flex items-center justify-center mx-auto mb-3 text-2xl">
                            <i class="bi bi-inbox-fill"></i>
                        </div>
                        <p class="font-bold text-slate-700 text-sm">Belum Ada Tiket yang Diajukan</p>
                        <p class="text-xs text-slate-400 mt-1">
                            Jika Anda memiliki kendala akademik, fasilitas lab, atau administrasi, silakan ajukan tiket baru.
                        </p>
                        <a href="<?= site_url('mahasiswa/ticketing/input') ?>" 
                           class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-xs transition-colors">
                            <i class="bi bi-plus-lg"></i>
                            <span>Buat Tiket Sekarang</span>
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($tickets as $t): ?>
                        <?php
                            $prioColor = 'bg-slate-100 text-slate-700 border-slate-200';
                            if ($t->prioritas === 'Sedang') $prioColor = 'bg-blue-50 text-blue-700 border-blue-200';
                            elseif ($t->prioritas === 'Tinggi') $prioColor = 'bg-amber-50 text-amber-700 border-amber-200';
                            elseif ($t->prioritas === 'Darurat') $prioColor = 'bg-rose-50 text-rose-700 border-rose-200';

                            $statusColor = 'bg-amber-50 text-amber-800 border-amber-200';
                            $waTickIcon = '<i class="bi bi-check text-slate-400 font-extrabold text-sm"></i>';
                            if ($t->status === 'Diproses') {
                                $statusColor = 'bg-blue-50 text-blue-800 border-blue-200';
                                $waTickIcon = '<i class="bi bi-check-all text-slate-500 font-black text-base"></i>';
                            } elseif ($t->status === 'Selesai') {
                                $statusColor = 'bg-emerald-50 text-emerald-800 border-emerald-200';
                                $waTickIcon = '<i class="bi bi-check-all text-sky-500 font-black text-base"></i>';
                            } elseif ($t->status === 'Ditutup') {
                                $statusColor = 'bg-slate-100 text-slate-700 border-slate-300';
                                $waTickIcon = '<i class="bi bi-patch-check-fill text-purple-600 text-xs"></i>';
                            }

                            $cleanSnippet = trim(strip_tags(str_ireplace(['&nbsp;', '&amp;nbsp;'], ' ', $t->deskripsi ?? '')));
                            $cleanSnippet = preg_replace('/\s+/u', ' ', html_entity_decode($cleanSnippet, ENT_QUOTES, 'UTF-8'));
                            $tanggapanClean = trim(strip_tags(str_ireplace(['&nbsp;', '&amp;nbsp;'], ' ', $t->tanggapan ?? '')));
                            $tanggapanClean = preg_replace('/\s+/u', ' ', html_entity_decode($tanggapanClean, ENT_QUOTES, 'UTF-8'));
                        ?>
                        <div class="ticket-card bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs flex flex-col space-y-3"
                             data-status="<?= htmlspecialchars($t->status); ?>">
                            
                            <!-- Header Card: Kode Tiket, Prioritas, & Status -->
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="font-mono font-bold text-xs text-orange-600 bg-orange-50 px-2.5 py-1 rounded-lg border border-orange-200/60">
                                        <?= htmlspecialchars($t->kode_tiket); ?>
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold border <?= $prioColor; ?>">
                                        <?= htmlspecialchars($t->prioritas); ?>
                                    </span>
                                </div>
                                <div class="relative inline-flex items-center cursor-pointer select-none shrink-0">
                                    <span class="status-badge-trigger inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border <?= $statusColor; ?>"
                                          data-kode="<?= htmlspecialchars($t->kode_tiket); ?>"
                                          data-status="<?= htmlspecialchars($t->status); ?>"
                                          data-unit="<?= htmlspecialchars($t->unit_tujuan); ?>"
                                          data-created="<?= date('d M Y, H:i', strtotime($t->created_at)); ?>"
                                          data-updated="<?= !empty($t->updated_at) ? date('d M Y, H:i', strtotime($t->updated_at)) : '-'; ?>"
                                          data-tgl-tanggapan="<?= !empty($t->tgl_tanggapan) ? date('d M Y, H:i', strtotime($t->tgl_tanggapan)) : ''; ?>"
                                          data-tanggapan="<?= htmlspecialchars($tanggapanClean); ?>">
                                        <?= $waTickIcon; ?>
                                        <span><?= htmlspecialchars($t->status); ?></span>
                                    </span>
                                </div>
                            </div>

                            <!-- Body Card: Subjek & Cuplikan Kendala -->
                            <div>
                                <h4 class="font-extrabold text-slate-800 text-sm leading-snug">
                                    <?= htmlspecialchars($t->subjek); ?>
                                </h4>
                                <?php if (!empty($cleanSnippet)): ?>
                                    <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed mt-1">
                                        <?= htmlspecialchars(mb_substr($cleanSnippet, 0, 90)); ?>...
                                    </p>
                                <?php endif; ?>
                            </div>

                            <!-- Meta Info: Unit Tujuan & Kategori -->
                            <div class="flex flex-wrap items-center gap-1.5 pt-0.5">
                                <?php if (!empty($t->unit_tujuan)): ?>
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-orange-700 bg-orange-50 px-2 py-0.5 rounded-md border border-orange-200/60">
                                        <i class="bi bi-building"></i> <?= htmlspecialchars($t->unit_tujuan); ?>
                                    </span>
                                <?php endif; ?>
                                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md">
                                    <i class="bi bi-tag"></i> <?= htmlspecialchars($t->kategori); ?>
                                </span>
                            </div>

                            <!-- Footer Card: Waktu & Tombol Detail -->
                            <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2">
                                <span class="text-[11px] text-slate-400 flex items-center gap-1 font-medium">
                                    <i class="bi bi-clock"></i> <?= date('d M Y, H:i', strtotime($t->created_at)); ?> WIB
                                </span>
                                <button type="button" onclick="showTicketDetail('<?= $t->kode_tiket; ?>')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-orange-50 hover:bg-orange-600 text-orange-700 hover:text-white text-xs font-bold transition-all shadow-2xs border border-orange-200/70 cursor-pointer">
                                    <i class="bi bi-eye"></i>
                                    <span>Detail</span>
                                </button>
                            </div>

                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div id="filterEmptyMobileCard" class="hidden py-8 text-center text-slate-400 bg-white rounded-2xl border border-slate-200 p-5">
                    <i class="bi bi-search text-2xl text-slate-300 block mb-1.5"></i>
                    <p class="font-bold text-slate-700 text-xs">Tidak ada tiket yang cocok</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Coba ubah kata kunci atau filter status.</p>
                </div>
            </div>

        </div>

    </main>

    <!-- Floating Tracking Popover Tooltip -->
    <div id="statusTrackingPopover" class="fixed z-50 hidden opacity-0 transition-all duration-200 pointer-events-none w-80 bg-white rounded-2xl shadow-2xl border border-slate-200/90 p-4">
        <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-100">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tracking Status</span>
            <span id="popWaBadge" class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full border"></span>
        </div>

        <div class="relative pl-6 space-y-4 text-left text-xs">
            <div id="stepperLine" class="absolute left-3 top-2.5 bottom-2.5 w-0.5 bg-slate-200"></div>

            <!-- Step 1: Terkirim -->
            <div class="relative">
                <div class="absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-slate-500 text-white shadow-xs">
                    <i class="bi bi-check"></i>
                </div>
                <div class="font-bold text-[11px] text-slate-800">Tiket Terkirim</div>
                <div class="text-[10px] font-mono text-slate-500" id="step1Time">-</div>
                <div class="text-[11px] text-slate-500 mt-0.5">Terkirim ke unit <span id="step1Unit" class="font-semibold text-slate-700"></span>.</div>
            </div>

            <!-- Step 2: Diterima & Diproses -->
            <div class="relative">
                <div id="step2Icon" class="absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-slate-200 text-slate-500">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <div id="step2Title" class="font-bold text-[11px] text-slate-600">Diproses Unit</div>
                <div id="step2Time" class="text-[10px] font-mono text-slate-400">-</div>
                <div id="step2Desc" class="text-[11px] text-slate-500 mt-0.5">Staf unit sedang meninjau kendala.</div>
            </div>

            <!-- Step 3: Ditanggapi & Selesai -->
            <div class="relative">
                <div id="step3Icon" class="absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-slate-200 text-slate-500">
                    <i class="bi bi-check-all"></i>
                </div>
                <div id="step3Title" class="font-bold text-[11px] text-slate-600">Selesai & Solusi Diberikan</div>
                <div id="step3Time" class="text-[10px] font-mono text-slate-400">-</div>
                <div id="step3Desc" class="text-[11px] text-slate-500 mt-0.5">Solusi atau tanggapan telah diberikan.</div>
                <div id="step3TanggapanBox" class="hidden mt-2 p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-[11px] text-emerald-900 leading-snug">
                    <span class="font-bold block text-emerald-800 mb-0.5">Tanggapan Unit:</span>
                    <span id="step3TanggapanText" class="italic line-clamp-3"></span>
                </div>
            </div>

            <!-- Step 4: Ditutup Tuntas -->
            <div class="relative">
                <div id="step4Icon" class="absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-slate-200 text-slate-500">
                    <i class="bi bi-patch-check-fill"></i>
                </div>
                <div id="step4Title" class="font-bold text-[11px] text-slate-600">Tiket Ditutup Tuntas</div>
                <div id="step4Time" class="text-[10px] font-mono text-slate-400">-</div>
                <div id="step4Desc" class="text-[11px] text-slate-500 mt-0.5">Kendala dianggap tuntas.</div>
            </div>
        </div>

        <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400">
            <span>Kode: <strong id="popKode" class="font-mono text-orange-600"></strong></span>
            <span>Klik tombol Detail untuk info lengkap</span>
        </div>
    </div>

    <!-- Modal Detail Tiket -->
    <div id="detailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-150">
            
            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-orange-50/40 to-transparent">
                <div>
                    <span id="modalKode" class="text-xs font-mono font-bold text-orange-600 uppercase tracking-wider block">TIK-XXXXXXXX-XXXX</span>
                    <h3 id="modalSubjek" class="text-base font-extrabold text-slate-800 mt-0.5 line-clamp-1">Subjek Kendala</h3>
                </div>
                <button type="button" onclick="closeModal()" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition-colors">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-6 space-y-5 overflow-y-auto text-sm">
                
                <!-- Info Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
                    <div>
                        <span class="text-slate-400 block mb-0.5">Pelapor:</span>
                        <strong id="modalPelapor" class="text-slate-700 font-semibold block">-</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Unit Tujuan:</span>
                        <strong id="modalUnit" class="text-slate-700 font-semibold block">-</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Kategori:</span>
                        <strong id="modalKategori" class="text-slate-700 font-semibold block">-</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Prioritas:</span>
                        <strong id="modalPrioritas" class="text-slate-700 font-semibold block">-</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Status Terkini:</span>
                        <strong id="modalStatus" class="text-slate-700 font-semibold block">-</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Diajukan Pada:</span>
                        <strong id="modalWaktu" class="text-slate-700 font-semibold block">-</strong>
                    </div>
                </div>

                <!-- Card Stepper Progress Tracking (4 Tahap) -->
                <div>
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 flex items-center justify-between">
                        <span>Alur Progres Penanganan</span>
                        <span class="text-[10px] text-emerald-600 font-bold"><i class="bi bi-shield-check"></i> Terverifikasi Sistem</span>
                    </h4>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2" id="mhsStepperContainer">
                        <!-- Step 1: Menunggu -->
                        <div id="mhsStep_Menunggu" class="p-2.5 rounded-2xl border-2 transition-all flex flex-col items-center text-center gap-1 bg-slate-50 border-slate-200">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold step-icon bg-slate-200 text-slate-500">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <span class="text-[10px] font-extrabold text-slate-700 leading-tight">1. Menunggu</span>
                            <span class="text-[8px] text-slate-400 step-desc">Antrean Masuk</span>
                        </div>

                        <!-- Step 2: Diproses -->
                        <div id="mhsStep_Diproses" class="p-2.5 rounded-2xl border-2 transition-all flex flex-col items-center text-center gap-1 bg-slate-50 border-slate-200">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold step-icon bg-slate-200 text-slate-500">
                                <i class="bi bi-gear-wide-connected"></i>
                            </div>
                            <span class="text-[10px] font-extrabold text-slate-700 leading-tight">2. Diproses</span>
                            <span class="text-[8px] text-slate-400 step-desc">Sedang Ditangani</span>
                        </div>

                        <!-- Step 3: Selesai -->
                        <div id="mhsStep_Selesai" class="p-2.5 rounded-2xl border-2 transition-all flex flex-col items-center text-center gap-1 bg-slate-50 border-slate-200">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold step-icon bg-slate-200 text-slate-500">
                                <i class="bi bi-check2-circle"></i>
                            </div>
                            <span class="text-[10px] font-extrabold text-slate-700 leading-tight">3. Selesai</span>
                            <span class="text-[8px] text-slate-400 step-desc">Telah Diatasi</span>
                        </div>

                        <!-- Step 4: Ditutup -->
                        <div id="mhsStep_Ditutup" class="p-2.5 rounded-2xl border-2 transition-all flex flex-col items-center text-center gap-1 bg-slate-50 border-slate-200">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold step-icon bg-slate-200 text-slate-500">
                                <i class="bi bi-archive-fill"></i>
                            </div>
                            <span class="text-[10px] font-extrabold text-slate-700 leading-tight">4. Ditutup</span>
                            <span class="text-[8px] text-slate-400 step-desc">Arsip Final</span>
                        </div>
                    </div>
                </div>

                <!-- Deskripsi Lengkap -->
                <div>
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Deskripsi Kendala</h4>
                    <div id="modalDeskripsi" class="p-4 rounded-2xl bg-slate-50/70 border border-slate-100 text-slate-700 text-xs leading-relaxed space-y-2 max-h-56 overflow-y-auto">
                    </div>
                </div>

                <!-- Lampiran -->
                <div id="modalLampiranSection" class="hidden">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Lampiran Berkas</h4>
                    <a id="modalLampiranLink" href="#" target="_blank"
                       class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-orange-50 hover:bg-orange-100 text-orange-700 text-xs font-bold border border-orange-200 transition-colors">
                        <i class="bi bi-file-earmark-arrow-down text-base"></i>
                        <span id="modalLampiranName">Unduh / Buka Lampiran</span>
                    </a>
                </div>

                <!-- Tanggapan Admin Support -->
                <div>
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tanggapan / Solusi Unit Terkait</h4>
                    <div id="modalTanggapan" class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100 text-xs text-emerald-900 leading-relaxed">
                        <p class="text-slate-400 italic">Belum ada tanggapan dari unit terkait. Tiket Anda sedang dalam antrean penanganan.</p>
                    </div>
                    <span id="modalTglTanggapan" class="text-[11px] text-slate-400 mt-1 block"></span>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex justify-end">
                <button type="button" onclick="closeModal()" 
                        class="px-5 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold transition-colors">
                    Tutup
                </button>
            </div>

        </div>
    </div>

    <!-- Client-side Scripts for Filtering & Modal -->
    <script>
        const MHS_STATUS_WEIGHT = {
            'Menunggu': 1,
            'Diproses': 2,
            'Selesai':  3,
            'Ditutup':  4
        };

        function updateMhsStepperProgress(status) {
            const currentWeight = MHS_STATUS_WEIGHT[status] || 1;

            ['Menunggu', 'Diproses', 'Selesai', 'Ditutup'].forEach(st => {
                const card = document.getElementById('mhsStep_' + st);
                if (!card) return;

                const stepWeight = MHS_STATUS_WEIGHT[st];
                const iconBox = card.querySelector('.step-icon');
                const desc = card.querySelector('.step-desc');

                // Reset kelas dasar
                card.className = 'p-2.5 rounded-2xl border-2 transition-all flex flex-col items-center text-center gap-1';

                if (stepWeight < currentWeight) {
                    // Sudah terlewati (Tahap Selesai)
                    card.classList.add('bg-emerald-50/70', 'border-emerald-200');
                    if (iconBox) iconBox.className = 'w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold step-icon bg-emerald-500 text-white';
                    if (desc) desc.innerHTML = '<span class="text-emerald-700 font-bold"><i class="bi bi-check2"></i> Terlewati</span>';
                } else if (stepWeight === currentWeight) {
                    // Sedang aktif saat ini
                    if (st === 'Menunggu') {
                        card.classList.add('bg-amber-50', 'border-amber-400', 'ring-2', 'ring-amber-400/20');
                        if (iconBox) iconBox.className = 'w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold step-icon bg-amber-500 text-white animate-pulse';
                        if (desc) desc.innerHTML = '<span class="text-amber-700 font-extrabold">Antrean Aktif</span>';
                    } else if (st === 'Diproses') {
                        card.classList.add('bg-blue-50', 'border-blue-500', 'ring-2', 'ring-blue-500/20');
                        if (iconBox) iconBox.className = 'w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold step-icon bg-blue-600 text-white animate-pulse';
                        if (desc) desc.innerHTML = '<span class="text-blue-700 font-extrabold">Sedang Ditangani</span>';
                    } else if (st === 'Selesai') {
                        card.classList.add('bg-emerald-50', 'border-emerald-500', 'ring-2', 'ring-emerald-500/20');
                        if (iconBox) iconBox.className = 'w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold step-icon bg-emerald-600 text-white';
                        if (desc) desc.innerHTML = '<span class="text-emerald-700 font-extrabold">Selesai Ditindak</span>';
                    } else {
                        card.classList.add('bg-purple-50', 'border-purple-500', 'ring-2', 'ring-purple-500/20');
                        if (iconBox) iconBox.className = 'w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold step-icon bg-purple-600 text-white';
                        if (desc) desc.innerHTML = '<span class="text-purple-700 font-extrabold">Ditutup Resmi</span>';
                    }
                } else {
                    // Belum tercapai (Masa Datang)
                    card.classList.add('bg-slate-50', 'border-slate-200', 'opacity-60');
                    if (iconBox) iconBox.className = 'w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold step-icon bg-slate-200 text-slate-400';
                    if (desc) desc.innerHTML = '<span class="text-slate-400">Tahap Berikutnya</span>';
                }
            });
        }

        function showTicketDetail(kodeTiket) {
            fetch('<?= site_url("mahasiswa/ticketing/detail/") ?>' + encodeURIComponent(kodeTiket))
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success' || result.status === true) {
                        const d = result.data;
                        document.getElementById('modalKode').textContent = d.kode_tiket;
                        document.getElementById('modalSubjek').textContent = d.subjek;
                        document.getElementById('modalPelapor').textContent = d.nama_dosen + (d.nidn ? ' (NIM: ' + d.nidn + ')' : '');
                        document.getElementById('modalUnit').textContent = d.unit_tujuan || 'Unit Terkait';
                        document.getElementById('modalKategori').textContent = d.kategori;
                        document.getElementById('modalPrioritas').textContent = d.prioritas;
                        document.getElementById('modalStatus').textContent = d.status;
                        document.getElementById('modalWaktu').textContent = d.created_at || '-';
                        document.getElementById('modalDeskripsi').innerHTML = d.deskripsi;

                        // Lampiran
                        const lampSection = document.getElementById('modalLampiranSection');
                        if (d.lampiran && d.lampiran_url) {
                            lampSection.classList.remove('hidden');
                            document.getElementById('modalLampiranLink').href = d.lampiran_url;
                            document.getElementById('modalLampiranName').textContent = 'Buka Lampiran: ' + d.lampiran;
                        } else {
                            lampSection.classList.add('hidden');
                        }

                        // Tanggapan
                        const respBox = document.getElementById('modalTanggapan');
                        const respTime = document.getElementById('modalTglTanggapan');
                        if (d.tanggapan) {
                            respBox.innerHTML = d.tanggapan;
                            respTime.textContent = 'Ditanggapi pada: ' + (d.tgl_tanggapan || '-');
                        } else {
                            respBox.innerHTML = '<span class="text-slate-400 italic">Belum ada tanggapan dari unit terkait. Tiket Anda sedang dalam antrean penanganan.</span>';
                            respTime.textContent = '';
                        }

                        // Render Card Stepper Progress
                        updateMhsStepperProgress(d.status || 'Menunggu');

                        document.getElementById('detailModal').classList.remove('hidden');
                    } else {
                        alert(result.message || 'Gagal memuat detail tiket.');
                    }
                })
                .catch(err => {
                    alert('Gagal menghubungi server.');
                });
        }

        function closeModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        // Close on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
        });

        // Filter Table & Mobile Cards by Status Pill
        let currentFilterStatus = 'all';

        function filterByStatus(status, el) {
            currentFilterStatus = status;
            const targetBtn = el || (window.event ? (window.event.currentTarget || window.event.target.closest('.status-btn')) : null);
            if (targetBtn) {
                document.querySelectorAll('.status-btn').forEach(btn => {
                    btn.classList.remove('active', 'bg-orange-600', 'text-white', 'shadow-xs');
                    btn.classList.add('text-slate-500', 'hover:bg-slate-100');
                });
                targetBtn.classList.add('active', 'bg-orange-600', 'text-white', 'shadow-xs');
                targetBtn.classList.remove('text-slate-500', 'hover:bg-slate-100');
            }
            applyFilters();
        }

        // Search in Table & Mobile Cards
        function searchTable() {
            applyFilters();
        }

        function applyFilters() {
            const query = (document.getElementById('searchInput')?.value || '').toLowerCase().trim();
            const rows = document.querySelectorAll('.ticket-row');
            const cards = document.querySelectorAll('.ticket-card');

            let visibleRows = 0;
            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status');
                const matchesStatus = (currentFilterStatus === 'all' || rowStatus === currentFilterStatus);
                const text = row.textContent.toLowerCase();
                const matchesQuery = !query || text.includes(query);
                if (matchesStatus && matchesQuery) {
                    row.style.display = '';
                    visibleRows++;
                } else {
                    row.style.display = 'none';
                }
            });

            let visibleCards = 0;
            cards.forEach(card => {
                const cardStatus = card.getAttribute('data-status');
                const matchesStatus = (currentFilterStatus === 'all' || cardStatus === currentFilterStatus);
                const text = card.textContent.toLowerCase();
                const matchesQuery = !query || text.includes(query);
                if (matchesStatus && matchesQuery) {
                    card.style.display = '';
                    visibleCards++;
                } else {
                    card.style.display = 'none';
                }
            });

            const filterEmptyRow = document.getElementById('filterEmptyRow');
            if (filterEmptyRow) {
                filterEmptyRow.className = (visibleRows === 0 && rows.length > 0) ? '' : 'hidden';
            }
            const filterEmptyMobileCard = document.getElementById('filterEmptyMobileCard');
            if (filterEmptyMobileCard) {
                filterEmptyMobileCard.className = (visibleCards === 0 && cards.length > 0) ? 'py-8 text-center text-slate-400 bg-white rounded-2xl border border-slate-200 p-5' : 'hidden';
            }
        }

        // Status Stepper Popover Handling
        const popover = document.getElementById('statusTrackingPopover');
        let popoverTimeout = null;

        function hidePopover() {
            clearTimeout(popoverTimeout);
            if (!popover) return;
            popover.classList.remove('opacity-100', 'translate-y-0');
            popover.classList.add('opacity-0', '-translate-y-2');
            setTimeout(() => {
                if (popover.classList.contains('opacity-0')) {
                    popover.classList.add('hidden');
                }
            }, 200);
        }

        if (popover) {
            popover.addEventListener('mouseenter', () => clearTimeout(popoverTimeout));
            popover.addEventListener('mouseleave', () => hidePopover());
        }

        function triggerStepperPopover(triggerEl) {
            clearTimeout(popoverTimeout);

            const kode = triggerEl.getAttribute('data-kode');
            const status = triggerEl.getAttribute('data-status');
            const unit = triggerEl.getAttribute('data-unit');
            const created = triggerEl.getAttribute('data-created');
            const updated = triggerEl.getAttribute('data-updated');
            const tglTanggapan = triggerEl.getAttribute('data-tgl-tanggapan');
            const tanggapan = triggerEl.getAttribute('data-tanggapan');

            const popKode = document.getElementById('popKode');
            if (popKode) popKode.textContent = kode;
            document.getElementById('step1Time').textContent = created;
            document.getElementById('step1Unit').textContent = unit || 'terkait';

            const popWaBadge = document.getElementById('popWaBadge');
            const step2Icon = document.getElementById('step2Icon');
            const step2Title = document.getElementById('step2Title');
            const step2Time = document.getElementById('step2Time');
            const step2Desc = document.getElementById('step2Desc');

            const step3Icon = document.getElementById('step3Icon');
            const step3Title = document.getElementById('step3Title');
            const step3Time = document.getElementById('step3Time');
            const step3Desc = document.getElementById('step3Desc');
            const step3Box = document.getElementById('step3TanggapanBox');
            const step3Text = document.getElementById('step3TanggapanText');

            const step4Icon = document.getElementById('step4Icon');
            const step4Title = document.getElementById('step4Title');
            const step4Time = document.getElementById('step4Time');
            const step4Desc = document.getElementById('step4Desc');

            const stepperLine = document.getElementById('stepperLine');

            // Reset step 3 quote
            step3Box.classList.add('hidden');
            step3Text.textContent = '';

            if (status === 'Menunggu') {
                popWaBadge.className = 'inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full border bg-amber-50 text-amber-700 border-amber-200';
                popWaBadge.innerHTML = '<i class="bi bi-check text-slate-400 font-black text-sm"></i> Menunggu';

                step2Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-amber-100 text-amber-600 border border-amber-300';
                step2Icon.innerHTML = '<i class="bi bi-hourglass-split animate-pulse"></i>';
                step2Title.className = 'font-bold text-[11px] text-amber-800';
                step2Time.textContent = 'Dalam Antrean';
                step2Desc.textContent = 'Menunggu staf ' + (unit || 'unit') + ' membuka dan merespon laporan.';

                step3Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-slate-100 text-slate-400 border border-slate-200';
                step3Icon.innerHTML = '<i class="bi bi-dash"></i>';
                step3Title.className = 'font-bold text-[11px] text-slate-400';
                step3Time.textContent = '-';
                step3Desc.textContent = 'Solusi belum tersedia.';

                step4Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-slate-100 text-slate-400 border border-slate-200';
                step4Icon.innerHTML = '<i class="bi bi-lock text-[10px]"></i>';
                step4Title.className = 'font-bold text-[11px] text-slate-400';
                step4Time.textContent = '-';
                step4Desc.textContent = 'Tiket masih aktif.';

                stepperLine.className = 'absolute left-3 top-2.5 bottom-2.5 w-0.5 bg-slate-200';

            } else if (status === 'Diproses') {
                popWaBadge.className = 'inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full border bg-blue-50 text-blue-700 border-blue-200';
                popWaBadge.innerHTML = '<i class="bi bi-check-all text-slate-500 font-black text-base"></i> Diproses';

                step2Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-blue-600 text-white shadow-xs';
                step2Icon.innerHTML = '<i class="bi bi-check-all text-sm font-bold"></i>';
                step2Title.className = 'font-bold text-[11px] text-blue-800';
                step2Time.textContent = updated || created;
                step2Desc.textContent = 'Staf ' + (unit || 'unit') + ' sedang aktif menangani kendala Anda.';

                if (tanggapan && tanggapan.trim()) {
                    step3Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-blue-100 text-blue-600 border border-blue-300';
                    step3Icon.innerHTML = '<i class="bi bi-chat-dots-fill text-[9px]"></i>';
                    step3Title.className = 'font-bold text-[11px] text-blue-800';
                    step3Time.textContent = tglTanggapan || updated;
                    step3Desc.textContent = 'Staf telah memberikan tanggapan awal.';
                    step3Box.classList.remove('hidden');
                    step3Text.textContent = '"' + tanggapan.trim() + '"';
                } else {
                    step3Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200';
                    step3Icon.innerHTML = '<i class="bi bi-hourglass-split animate-pulse"></i>';
                    step3Title.className = 'font-bold text-[11px] text-slate-600';
                    step3Time.textContent = 'Sedang Disiapkan';
                    step3Desc.textContent = 'Solusi atau jawaban sedang dirumuskan staf.';
                }

                step4Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-slate-100 text-slate-400 border border-slate-200';
                step4Icon.innerHTML = '<i class="bi bi-lock text-[10px]"></i>';
                step4Title.className = 'font-bold text-[11px] text-slate-400';
                step4Time.textContent = '-';
                step4Desc.textContent = 'Tiket masih dalam penanganan.';

                stepperLine.className = 'absolute left-3 top-2.5 bottom-2.5 w-0.5 bg-blue-300';

            } else if (status === 'Selesai') {
                popWaBadge.className = 'inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full border bg-emerald-50 text-emerald-800 border-emerald-200';
                popWaBadge.innerHTML = '<i class="bi bi-check-all text-sky-500 font-black text-base"></i> Selesai';

                step2Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-blue-500 text-white shadow-xs';
                step2Icon.innerHTML = '<i class="bi bi-check-lg"></i>';
                step2Title.className = 'font-bold text-[11px] text-slate-700';
                step2Time.textContent = updated;
                step2Desc.textContent = 'Telah ditinjau dan ditindaklanjuti.';

                step3Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-emerald-600 text-white shadow-xs';
                step3Icon.innerHTML = '<i class="bi bi-check2-all text-xs"></i>';
                step3Title.className = 'font-bold text-[11px] text-emerald-800';
                step3Time.textContent = tglTanggapan || updated;
                step3Desc.textContent = 'Solusi telah diberikan oleh unit terkait.';
                if (tanggapan && tanggapan.trim()) {
                    step3Box.classList.remove('hidden');
                    step3Text.textContent = '"' + tanggapan.trim() + '"';
                }

                step4Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-slate-100 text-slate-400 border border-slate-200';
                step4Icon.innerHTML = '<i class="bi bi-clock text-[10px]"></i>';
                step4Title.className = 'font-bold text-[11px] text-slate-400';
                step4Time.textContent = 'Menunggu Konfirmasi Penutupan';
                step4Desc.textContent = 'Masalah telah diselesaikan.';

                stepperLine.className = 'absolute left-3 top-2.5 bottom-2.5 w-0.5 bg-emerald-400';

            } else if (status === 'Ditutup') {
                popWaBadge.className = 'inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full border bg-purple-50 text-purple-800 border-purple-200';
                popWaBadge.innerHTML = '<i class="bi bi-patch-check-fill text-purple-600 text-xs"></i> Ditutup';

                step2Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-blue-500 text-white shadow-xs';
                step2Icon.innerHTML = '<i class="bi bi-check-lg"></i>';
                step2Title.className = 'font-bold text-[11px] text-slate-700';
                step2Time.textContent = updated;
                step2Desc.textContent = 'Proses penanganan selesai.';

                step3Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-emerald-600 text-white shadow-xs';
                step3Icon.innerHTML = '<i class="bi bi-check2-all text-xs"></i>';
                step3Title.className = 'font-bold text-[11px] text-emerald-800';
                step3Time.textContent = tglTanggapan || updated;
                step3Desc.textContent = 'Solusi telah diterima.';
                if (tanggapan && tanggapan.trim()) {
                    step3Box.classList.remove('hidden');
                    step3Text.textContent = '"' + tanggapan.trim() + '"';
                }

                step4Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-purple-600 text-white shadow-xs';
                step4Icon.innerHTML = '<i class="bi bi-check-lg text-xs"></i>';
                step4Title.className = 'font-bold text-[11px] text-purple-800';
                step4Time.textContent = updated;
                step4Desc.textContent = 'Tiket telah ditutup tuntas.';

                stepperLine.className = 'absolute left-3 top-2.5 bottom-2.5 w-0.5 bg-purple-400';
            }

            // Position Popover
            const rect = triggerEl.getBoundingClientRect();
            const popoverHeight = 360;
            const popoverWidth = 320;

            let top = rect.bottom + 8;
            let left = rect.left + (rect.width / 2) - (popoverWidth / 2);

            if (top + popoverHeight > window.innerHeight) {
                top = rect.top - popoverHeight - 8;
            }
            if (left < 10) left = 10;
            if (left + popoverWidth > window.innerWidth - 10) {
                left = window.innerWidth - popoverWidth - 10;
            }

            popover.style.top = top + 'px';
            popover.style.left = left + 'px';

            popover.classList.remove('hidden');
            setTimeout(() => {
                popover.classList.remove('opacity-0', '-translate-y-2');
                popover.classList.add('opacity-100', 'translate-y-0');
            }, 10);
        }

        document.querySelectorAll('.status-badge-trigger').forEach(trigger => {
            trigger.addEventListener('mouseenter', function() {
                triggerStepperPopover(this);
            });

            trigger.addEventListener('mouseleave', function() {
                popoverTimeout = setTimeout(() => {
                    hidePopover();
                }, 150);
            });

            // Mobile click/tap support
            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
                if (popover.classList.contains('hidden') || popover.classList.contains('opacity-0')) {
                    triggerStepperPopover(this);
                } else {
                    hidePopover();
                }
            });
        });

        // Close popover when clicking anywhere outside
        document.addEventListener('click', function(e) {
            if (popover && !popover.contains(e.target) && !e.target.closest('.status-badge-trigger')) {
                hidePopover();
            }
        });
    </script>
</body>
</html>