<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Riwayat Tiket Kendala — Panel Laboran'; ?> - IFIK</title>

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
                padding-top: 48px;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-orange-50/20 to-slate-100 min-h-screen text-slate-800 antialiased">

    <!-- Include Curved Sidebar (Panel Laboran) -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <div class="page-wrapper-for-sidebar">
    <!-- Main Content Container -->
    <main class="min-h-screen p-6 sm:p-8 lg:p-10 max-w-7xl mx-auto">
        
        <!-- Header & Breadcrumb -->
        <div class="mb-8">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-2">
                <a href="<?= site_url('laboran') ?>" class="hover:text-orange-600 transition-colors">Panel Laboran</a>
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
                        Riwayat & Status Tiket Laboran
                    </h1>
                    <p class="text-sm text-slate-500 mt-1.5 max-w-2xl">
                        Pantau status penanganan tiket kendala yang diajukan oleh laboran ke berbagai unit kerja terkait.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="<?= site_url('laboran/respon-ticketing') ?>" 
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:text-orange-600 hover:border-orange-200 hover:bg-orange-50/50 text-sm font-bold shadow-xs transition-all">
                        <i class="bi bi-inbox-fill text-base text-orange-600"></i>
                        <span>Inbox Respon Lab</span>
                    </a>
                    <a href="<?= site_url('laboran/ticketing/input') ?>" 
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

        <!-- Stats Overview Cards (3 Status Sesuai DB) -->
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

            <!-- Dikirim -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-amber-500 uppercase tracking-wider block">Dikirim</span>
                    <span class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-1 block"><?= (int)($stats['dikirim'] ?? $stats['menunggu'] ?? 0); ?></span>
                    <span class="text-[11px] text-amber-500/80 font-medium">Antrean masuk</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>

            <!-- Sedang Diproses -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-blue-500 uppercase tracking-wider block">Sedang Diproses</span>
                    <span class="text-2xl sm:text-3xl font-extrabold text-blue-600 mt-1 block"><?= (int)($stats['sedang_diproses'] ?? $stats['diproses'] ?? 0); ?></span>
                    <span class="text-[11px] text-blue-500/80 font-medium">Sedang ditangani</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                    <i class="bi bi-gear-wide-connected"></i>
                </div>
            </div>

            <!-- Closed -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-emerald-500 uppercase tracking-wider block">Closed</span>
                    <span class="text-2xl sm:text-3xl font-extrabold text-emerald-600 mt-1 block"><?= (int)($stats['closed'] ?? $stats['selesai'] ?? 0); ?></span>
                    <span class="text-[11px] text-emerald-500/80 font-medium">Selesai & ditutup</span>
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
                
                <!-- Status Filter Pills (3 Status Sesuai DB) -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1.5 sm:pb-0 scrollbar-none" id="filter-pills" style="-webkit-overflow-scrolling: touch;">
                    <button type="button" onclick="filterByStatus('all', this)" class="status-btn active px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-orange-600 text-white shadow-xs shrink-0 cursor-pointer">
                        Semua (<?= (int)($stats['total'] ?? 0); ?>)
                    </button>
                    <button type="button" onclick="filterByStatus('Dikirim', this)" class="status-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-all shrink-0 cursor-pointer">
                        Dikirim (<?= (int)($stats['dikirim'] ?? $stats['menunggu'] ?? 0); ?>)
                    </button>
                    <button type="button" onclick="filterByStatus('Sedang Diproses', this)" class="status-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-all shrink-0 cursor-pointer">
                        Sedang Diproses (<?= (int)($stats['sedang_diproses'] ?? $stats['diproses'] ?? 0); ?>)
                    </button>
                    <button type="button" onclick="filterByStatus('Closed', this)" class="status-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-all shrink-0 cursor-pointer">
                        Closed (<?= (int)($stats['closed'] ?? $stats['selesai'] ?? 0); ?>)
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
                                        Jika laboratorium Anda membutuhkan bantuan fasilitas, perizinan, atau koordinasi antar unit, silakan ajukan tiket baru.
                                    </p>
                                    <a href="<?= site_url('laboran/ticketing/input') ?>" 
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

                                    $deskripsiSnippet = trim(strip_tags(str_ireplace(['&nbsp;', '&amp;nbsp;'], ' ', $t->deskripsi ?? '')));
                                    $deskripsiSnippet = preg_replace('/\s+/u', ' ', html_entity_decode($deskripsiSnippet, ENT_QUOTES, 'UTF-8'));
                                    $tanggapanSnippet = trim(strip_tags(str_ireplace(['&nbsp;', '&amp;nbsp;'], ' ', $t->tanggapan ?? '')));
                                    $tanggapanSnippet = preg_replace('/\s+/u', ' ', html_entity_decode($tanggapanSnippet, ENT_QUOTES, 'UTF-8'));
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

                                    <!-- Unit Dituju & Kategori -->
                                    <td class="py-4 px-6">
                                        <div class="mb-1">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-orange-100 text-orange-800 border border-orange-200">
                                                <i class="bi bi-building"></i> <?= htmlspecialchars($t->unit_tujuan ?? $t->unit ?? $t->unit_terkait ?? $t->tujuan_penerima ?? 'Laboratorium Bengkel dan Studio'); ?>
                                            </span>
                                        </div>
                                        <p class="font-semibold text-xs text-slate-600">
                                            <i class="bi bi-tag-fill text-orange-400 text-[10px] mr-1"></i><?= htmlspecialchars($t->kategori); ?>
                                        </p>
                                    </td>

                                    <!-- Subjek -->
                                    <td class="py-4 px-6 max-w-xs">
                                        <p class="font-bold text-slate-800 text-sm truncate"><?= htmlspecialchars($t->subjek); ?></p>
                                        <p class="text-xs text-slate-400 truncate mt-0.5"><?= htmlspecialchars(mb_substr($deskripsiSnippet, 0, 70)); ?>...</p>
                                    </td>

                                    <!-- Prioritas -->
                                    <td class="py-4 px-6 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border <?= $prioColor; ?>">
                                            <?= htmlspecialchars($t->prioritas); ?>
                                        </span>
                                    </td>

                                    <!-- Status (WhatsApp Ticks & Stepper Hover) -->
                                    <td class="py-4 px-6 text-center">
                                        <?php
                                            $cleanCatatanProses = !empty($t->catatan_proses) ? htmlspecialchars(preg_replace('/\s+/u', ' ', html_entity_decode(trim(strip_tags(str_ireplace(['&nbsp;', '&amp;nbsp;'], ' ', $t->catatan_proses))), ENT_QUOTES, 'UTF-8'))) : '';
                                            $cleanCatatanSelesai = !empty($t->catatan_selesai) ? htmlspecialchars(preg_replace('/\s+/u', ' ', html_entity_decode(trim(strip_tags(str_ireplace(['&nbsp;', '&amp;nbsp;'], ' ', $t->catatan_selesai))), ENT_QUOTES, 'UTF-8'))) : '';
                                            $cleanCatatanTutup = !empty($t->catatan_tutup) ? htmlspecialchars(preg_replace('/\s+/u', ' ', html_entity_decode(trim(strip_tags(str_ireplace(['&nbsp;', '&amp;nbsp;'], ' ', $t->catatan_tutup))), ENT_QUOTES, 'UTF-8'))) : '';
                                            $tglDiprosesFmt = !empty($t->tgl_diproses) ? date('d M Y, H:i', strtotime($t->tgl_diproses)) . ' WIB' : '';
                                            $tglSelesaiFmt = !empty($t->tgl_closed) ? date('d M Y, H:i', strtotime($t->tgl_closed)) . ' WIB' : '';
                                        ?>
                                        <div class="relative inline-flex items-center cursor-help status-stepper-trigger select-none"
                                             data-kode="<?= htmlspecialchars($t->kode_tiket); ?>"
                                             data-status="<?= htmlspecialchars($t->status); ?>"
                                             data-unit="<?= htmlspecialchars($t->unit_tujuan); ?>"
                                             data-subjek="<?= htmlspecialchars($t->subjek); ?>"
                                             data-created="<?= date('d M Y, H:i', strtotime($t->created_at)) . ' WIB'; ?>"
                                             data-updated="<?= !empty($t->updated_at) ? date('d M Y, H:i', strtotime($t->updated_at)) . ' WIB' : ''; ?>"
                                             data-tgl-diproses="<?= $tglDiprosesFmt; ?>"
                                             data-tgl-selesai="<?= $tglSelesaiFmt; ?>"
                                             data-catatan-proses="<?= $cleanCatatanProses; ?>"
                                             data-catatan-selesai="<?= $cleanCatatanSelesai; ?>"
                                             data-catatan-tutup="<?= $cleanCatatanTutup; ?>"
                                             data-tgl-tanggapan="<?= !empty($t->tgl_tanggapan) ? date('d M Y, H:i', strtotime($t->tgl_tanggapan)) . ' WIB' : (!empty($t->updated_at) && $t->status !== 'Menunggu' ? date('d M Y, H:i', strtotime($t->updated_at)) . ' WIB' : ''); ?>"
                                             data-tanggapan="<?= htmlspecialchars($tanggapanSnippet); ?>">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border transition-all hover:scale-105 hover:shadow-xs <?= $statusColor; ?>">
                                                <?= $waTickIcon; ?>
                                                <span><?= htmlspecialchars($t->status); ?></span>
                                                <i class="bi bi-info-circle-fill text-[10px] opacity-40 hover:opacity-100 transition-opacity ml-0.5"></i>
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Aksi -->
                                    <td class="py-4 px-6 text-center">
                                        <button type="button" onclick="showTicketDetail('<?= $t->kode_tiket; ?>')"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-orange-100 text-slate-700 hover:text-orange-700 text-xs font-bold transition-all shadow-2xs">
                                            <i class="bi bi-eye"></i>
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
                            Jika laboratorium Anda membutuhkan bantuan fasilitas, silakan ajukan tiket baru.
                        </p>
                        <a href="<?= site_url('laboran/ticketing/input') ?>" 
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

                            $deskripsiSnippet = trim(strip_tags(str_ireplace(['&nbsp;', '&amp;nbsp;'], ' ', $t->deskripsi ?? '')));
                            $deskripsiSnippet = preg_replace('/\s+/u', ' ', html_entity_decode($deskripsiSnippet, ENT_QUOTES, 'UTF-8'));
                            $tanggapanSnippet = trim(strip_tags(str_ireplace(['&nbsp;', '&amp;nbsp;'], ' ', $t->tanggapan ?? '')));
                            $tanggapanSnippet = preg_replace('/\s+/u', ' ', html_entity_decode($tanggapanSnippet, ENT_QUOTES, 'UTF-8'));
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
                                <div class="relative inline-flex items-center cursor-pointer status-stepper-trigger select-none shrink-0"
                                     data-kode="<?= htmlspecialchars($t->kode_tiket); ?>"
                                     data-status="<?= htmlspecialchars($t->status); ?>"
                                     data-unit="<?= htmlspecialchars($t->unit_tujuan); ?>"
                                     data-subjek="<?= htmlspecialchars($t->subjek); ?>"
                                     data-created="<?= date('d M Y, H:i', strtotime($t->created_at)) . ' WIB'; ?>"
                                     data-updated="<?= !empty($t->updated_at) ? date('d M Y, H:i', strtotime($t->updated_at)) . ' WIB' : ''; ?>"
                                     data-tgl-diproses="<?= $tglDiprosesFmt; ?>"
                                     data-tgl-selesai="<?= $tglSelesaiFmt; ?>"
                                     data-catatan-proses="<?= $cleanCatatanProses; ?>"
                                     data-catatan-selesai="<?= $cleanCatatanSelesai; ?>"
                                     data-catatan-tutup="<?= $cleanCatatanTutup; ?>"
                                     data-tgl-tanggapan="<?= !empty($t->tgl_tanggapan) ? date('d M Y, H:i', strtotime($t->tgl_tanggapan)) . ' WIB' : (!empty($t->updated_at) && $t->status !== 'Menunggu' ? date('d M Y, H:i', strtotime($t->updated_at)) . ' WIB' : ''); ?>"
                                     data-tanggapan="<?= htmlspecialchars($tanggapanSnippet); ?>">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border <?= $statusColor; ?>">
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
                                <?php if (!empty($deskripsiSnippet)): ?>
                                    <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed mt-1">
                                        <?= htmlspecialchars(mb_substr($deskripsiSnippet, 0, 90)); ?>...
                                    </p>
                                <?php endif; ?>
                            </div>

                            <!-- Meta Info: Unit yang Dituju & Kategori -->
                            <div class="flex flex-wrap items-center gap-1.5 pt-0.5">
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-orange-800 bg-orange-100 px-2 py-0.5 rounded-md border border-orange-200">
                                    <i class="bi bi-building"></i> <?= htmlspecialchars($t->unit_tujuan ?? $t->unit ?? $t->unit_terkait ?? $t->tujuan_penerima ?? 'Laboratorium Bengkel dan Studio'); ?>
                                </span>
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
    </div>

    <!-- Floating Popover Status Stepper Tracking ala WhatsApp & Mahasiswa Workflow -->
    <div id="statusTrackingPopover"
         style="width: 340px;"
         class="fixed z-50 hidden transition-all duration-200 opacity-0 transform -translate-y-2 max-w-[90vw] bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-slate-200/90 p-4 text-left pointer-events-auto">
        
        <!-- Header -->
        <div class="flex items-center justify-between pb-2.5 border-b border-slate-100 mb-3">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center text-xs font-bold">
                    <i class="bi bi-signpost-split-fill"></i>
                </div>
                <div>
                    <h4 class="text-xs font-black text-slate-800 tracking-tight">Status Progres Kendala</h4>
                    <p class="text-[10px] text-slate-400 font-mono" id="popKode">TIK-XXXXXXXX</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <div id="popWaBadge" class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full border">
                    <!-- WA Icon + Status -->
                </div>
                <button type="button" onclick="hidePopover()" class="w-5 h-5 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer" title="Tutup Popup">
                    <i class="bi bi-x-lg text-[10px]"></i>
                </button>
            </div>
        </div>

        <!-- 4-Step Stepper Flow: 1. Tiket Terkirim, 2. Sedang Diproses, 3. Tanggapan & Solusi, 4. Ditutup -->
        <div class="relative pl-7 space-y-3.5 text-xs">
            <!-- Vertical Stepper Track Line -->
            <div id="stepperLine" class="absolute left-3 top-2.5 bottom-2.5 w-0.5 bg-slate-200"></div>

            <!-- Step 1: Menunggu -->
            <div class="relative">
                <span id="step1Icon" class="absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-emerald-500 text-white shadow-xs">
                    <i class="bi bi-check-lg"></i>
                </span>
                <div class="flex items-center justify-between gap-2">
                    <span class="font-bold text-slate-800 text-[11px]">1. Dikirim</span>
                    <span id="step1Time" class="text-[10px] font-mono text-emerald-600 font-bold whitespace-nowrap">-</span>
                </div>
                <p class="text-[10px] text-slate-400 leading-snug mt-0.5">
                    Laporan kendala masuk ke sistem & antrean unit <span id="step1Unit" class="font-semibold text-slate-600"></span>.
                </p>
            </div>

            <!-- Step 2: Diproses -->
            <div class="relative">
                <span id="step2Icon" class="absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold border">
                    <!-- Dynamic Icon -->
                </span>
                <div class="flex items-center justify-between gap-2">
                    <span id="step2Title" class="font-bold text-[11px]">2. Sedang Diproses</span>
                    <span id="step2Time" class="text-[10px] font-mono font-bold whitespace-nowrap">-</span>
                </div>
                <p id="step2Desc" class="text-[10px] text-slate-400 leading-snug mt-0.5">
                    -
                </p>

                <!-- Cuplikan Catatan Diproses -->
                <div id="step2TanggapanBox" class="hidden mt-1.5 p-2 rounded-xl bg-blue-50 border border-blue-200/80 text-[10px] text-slate-700 leading-relaxed">
                    <div class="text-[9px] font-bold text-blue-800 uppercase flex items-center gap-1 mb-0.5">
                        <i class="bi bi-clock-history text-blue-600"></i>
                        <span>Catatan Pengerjaan Unit:</span>
                    </div>
                    <div id="step2TanggapanText" class="italic line-clamp-2 text-slate-600"></div>
                </div>
            </div>

            <!-- Step 3: Selesai -->
            <div class="relative">
                <span id="step3Icon" class="absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold border">
                    <!-- Dynamic Icon -->
                </span>
                <div class="flex items-center justify-between gap-2">
                    <span id="step3Title" class="font-bold text-[11px]">3. Closed</span>
                    <span id="step3Time" class="text-[10px] font-mono font-bold whitespace-nowrap">-</span>
                </div>
                <p id="step3Desc" class="text-[10px] text-slate-400 leading-snug mt-0.5">
                    -
                </p>

                <!-- Cuplikan Balasan Tanggapan (Jika sudah ada tanggapan) -->
                <div id="step3TanggapanBox" class="hidden mt-1.5 p-2 rounded-xl bg-emerald-50 border border-emerald-200/80 text-[10px] text-slate-700 leading-relaxed">
                    <div class="text-[9px] font-bold text-emerald-800 uppercase flex items-center gap-1 mb-0.5">
                        <i class="bi bi-check2-circle text-emerald-600"></i>
                        <span>Solusi & Hasil Penyelesaian:</span>
                    </div>
                    <div id="step3TanggapanText" class="italic line-clamp-2 text-slate-600"></div>
                </div>
            </div>

            <!-- Step 4: Ditutup (Hidden) -->
            <div class="relative hidden" id="popStep4Container">
                <span id="step4Icon" class="absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold border">
                    <!-- Dynamic Icon -->
                </span>
                <div class="flex items-center justify-between gap-2">
                    <span id="step4Title" class="font-bold text-[11px]">4. Ditutup</span>
                    <span id="step4Time" class="text-[10px] font-mono font-bold whitespace-nowrap">-</span>
                </div>
                <p id="step4Desc" class="text-[10px] text-slate-400 leading-snug mt-0.5">
                    -
                </p>

                <!-- Cuplikan Catatan Ditutup -->
                <div id="step4TanggapanBox" class="hidden mt-1.5 p-2 rounded-xl bg-purple-50 border border-purple-200/80 text-[10px] text-slate-700 leading-relaxed">
                    <div class="text-[9px] font-bold text-purple-800 uppercase flex items-center gap-1 mb-0.5">
                        <i class="bi bi-archive-fill text-purple-600"></i>
                        <span>Catatan Arsip Penutupan:</span>
                    </div>
                    <div id="step4TanggapanText" class="italic line-clamp-2 text-slate-600"></div>
                </div>
            </div>

        </div>

        <!-- Popover Footer -->
        <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400">
            <span class="flex items-center gap-1 text-slate-500 font-medium">
                <i class="bi bi-info-circle text-orange-500 text-[10px]"></i> Klik tombol Detail untuk info lengkap
            </span>
            <button type="button" onclick="hidePopover()" class="text-[10px] font-bold text-slate-400 hover:text-slate-600 hover:underline cursor-pointer">
                Tutup
            </button>
        </div>

    </div>

    <!-- Modal Detail Tiket -->
    <div id="detailModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
            
            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-r from-orange-50/40 to-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-lg">
                        <i class="bi bi-ticket-perforated"></i>
                    </div>
                    <div>
                        <span id="modalKode" class="font-mono font-bold text-xs text-orange-600 bg-orange-50 px-2 py-0.5 rounded border border-orange-200">TIK-XXXX</span>
                        <h3 id="modalSubjek" class="text-base font-extrabold text-slate-800 mt-1">Subjek Kendala</h3>
                    </div>
                </div>
                <button type="button" onclick="closeModal()" class="w-8 h-8 rounded-full hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors">
                    <i class="bi bi-x-lg text-sm"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 max-h-[70vh] overflow-y-auto space-y-5">
                
                <!-- Info Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-3.5 bg-slate-50 rounded-2xl border border-slate-100 text-xs">
                    <div>
                        <span class="text-slate-400 block font-semibold">Pelapor</span>
                        <span id="modalPelapor" class="font-bold text-slate-800">-</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold">Unit yang Dituju</span>
                        <span id="modalPenerima" class="font-bold text-orange-600">-</span>
                        <span id="modalUnit" class="hidden"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold">Kategori</span>
                        <span id="modalKategori" class="font-bold text-slate-800">-</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold">Prioritas</span>
                        <span id="modalPrioritas" class="font-bold text-slate-800">-</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold">Status</span>
                        <span id="modalStatus" class="font-bold text-slate-800">-</span>
                    </div>
                    <div class="col-span-2 sm:col-span-3 pt-1 border-t border-slate-200/60 flex items-center justify-between text-[11px] text-slate-400">
                        <span>Diajukan Pada: <strong id="modalWaktu" class="text-slate-700 font-medium">-</strong></span>
                    </div>
                </div>

                <!-- Deskripsi Lengkap (Rich Text) -->
                <div>
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Deskripsi Kendala</h4>
                    <div id="modalDeskripsi" class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-700 leading-relaxed overflow-x-auto space-y-2">
                        -
                    </div>
                </div>

                <!-- Informasi Tambahan Tiket (Field Dinamis) -->
                <div id="modalCustomFieldsSection" class="hidden">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Informasi Tambahan Tiket</h4>
                    <div id="modalCustomFieldsContainer" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 p-3 rounded-2xl bg-orange-50/40 border border-orange-100">
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
        function showTicketDetail(kodeTiket) {
            fetch('<?= site_url("laboran/ticketing/detail/") ?>' + encodeURIComponent(kodeTiket))
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success' || result.status === true) {
                        const d = result.data;
                        document.getElementById('modalKode').textContent = d.kode_tiket;
                        document.getElementById('modalSubjek').textContent = d.subjek;
                        document.getElementById('modalPelapor').textContent = d.nama_dosen + (d.nidn ? ' (' + d.nidn + ')' : '');
                        const targetUnit = d.unit_tujuan || d.unit || d.unit_terkait || d.tujuan_penerima || 'Laboratorium Bengkel dan Studio';
                        const elPenerima = document.getElementById('modalPenerima');
                        if (elPenerima) elPenerima.textContent = targetUnit;
                        const elUnit = document.getElementById('modalUnit');
                        if (elUnit) elUnit.textContent = targetUnit;
                        document.getElementById('modalKategori').textContent = d.kategori;
                        document.getElementById('modalPrioritas').textContent = d.prioritas;
                        document.getElementById('modalStatus').textContent = d.status;
                        document.getElementById('modalWaktu').textContent = d.created_at || '-';
                        document.getElementById('modalDeskripsi').innerHTML = d.deskripsi;

                        // Custom Fields
                        const cfSec = document.getElementById('modalCustomFieldsSection');
                        const cfCont = document.getElementById('modalCustomFieldsContainer');
                        if (d.custom_fields && Array.isArray(d.custom_fields) && d.custom_fields.length > 0) {
                            cfCont.innerHTML = '';
                            d.custom_fields.forEach(f => {
                                const fCol = document.createElement('div');
                                fCol.className = 'p-2.5 rounded-xl bg-white border border-orange-100/80 shadow-2xs';
                                const fLbl = document.createElement('span');
                                fLbl.className = 'text-[10px] font-bold text-orange-600 block uppercase tracking-wider';
                                fLbl.textContent = f.label || f.name;
                                const fVal = document.createElement('span');
                                fVal.className = 'text-xs font-semibold text-slate-800 mt-0.5 block whitespace-pre-wrap';
                                fVal.textContent = f.value || '-';
                                fCol.appendChild(fLbl);
                                fCol.appendChild(fVal);
                                cfCont.appendChild(fCol);
                            });
                            cfSec.classList.remove('hidden');
                        } else {
                            cfSec.classList.add('hidden');
                            cfCont.innerHTML = '';
                        }

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

                        document.getElementById('detailModal').classList.remove('hidden');
                    } else {
                        alert(result.message || 'Gagal memuat detail tiket.');
                    }
                })
                .catch(err => {
                    console.error(err);
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
        function getNormalizedStatus(st) {
            if (!st) return 'Dikirim';
            if (st === 'Menunggu') return 'Dikirim';
            if (st === 'Diproses') return 'Sedang Diproses';
            if (st === 'Selesai' || st === 'Ditutup') return 'Closed';
            return st;
        }

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
                const matchesStatus = (currentFilterStatus === 'all' || getNormalizedStatus(rowStatus) === getNormalizedStatus(currentFilterStatus));
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
                const matchesStatus = (currentFilterStatus === 'all' || getNormalizedStatus(cardStatus) === getNormalizedStatus(currentFilterStatus));
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

        // Floating Popover Status Stepper ala WhatsApp & Workflow Mahasiswa
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

        // Keep popover open while mouse is hovering it, close when mouse leaves
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
            const tglDiproses = triggerEl.getAttribute('data-tgl-diproses') || '';
            const tglSelesai = triggerEl.getAttribute('data-tgl-selesai') || '';
            const tglTanggapan = triggerEl.getAttribute('data-tgl-tanggapan') || '';
            const catatanProses = (triggerEl.getAttribute('data-catatan-proses') || '').trim();
            const catatanSelesai = (triggerEl.getAttribute('data-catatan-selesai') || '').trim();
            const catatanTutup = (triggerEl.getAttribute('data-catatan-tutup') || '').trim();
            const tanggapan = (triggerEl.getAttribute('data-tanggapan') || '').trim();

            document.getElementById('popKode').textContent = kode;
            document.getElementById('step1Time').innerHTML = '<i class="bi bi-clock-fill text-[9px] mr-1"></i>' + created;
            document.getElementById('step1Unit').textContent = unit || 'terkait';

            const popWaBadge = document.getElementById('popWaBadge');

            // Step 2 elements
            const step2Icon = document.getElementById('step2Icon');
            const step2Title = document.getElementById('step2Title');
            const step2Time = document.getElementById('step2Time');
            const step2Desc = document.getElementById('step2Desc');
            const step2Box = document.getElementById('step2TanggapanBox');
            const step2Text = document.getElementById('step2TanggapanText');

            // Step 3 elements
            const step3Icon = document.getElementById('step3Icon');
            const step3Title = document.getElementById('step3Title');
            const step3Time = document.getElementById('step3Time');
            const step3Desc = document.getElementById('step3Desc');
            const step3Box = document.getElementById('step3TanggapanBox');
            const step3Text = document.getElementById('step3TanggapanText');

            // Step 4 elements
            const step4Icon = document.getElementById('step4Icon');
            const step4Title = document.getElementById('step4Title');
            const step4Time = document.getElementById('step4Time');
            const step4Desc = document.getElementById('step4Desc');
            const step4Box = document.getElementById('step4TanggapanBox');
            const step4Text = document.getElementById('step4TanggapanText');

            const stepperLine = document.getElementById('stepperLine');

            // Reset notes boxes
            if (step2Box) { step2Box.classList.add('hidden'); step2Text.textContent = ''; }
            if (step3Box) { step3Box.classList.add('hidden'); step3Text.textContent = ''; }
            if (step4Box) { step4Box.classList.add('hidden'); step4Text.textContent = ''; }

            step2Title.textContent = '2. Diproses';
            step3Title.textContent = '3. Selesai';
            step4Title.textContent = '4. Ditutup';

            if (status === 'Menunggu') {
                popWaBadge.className = 'inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full border bg-amber-50 text-amber-700 border-amber-200';
                popWaBadge.innerHTML = '<i class="bi bi-check text-slate-400 font-black text-sm"></i> Menunggu';

                // Step 2: Waiting
                step2Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-amber-100 text-amber-600 border border-amber-300 shadow-xs';
                step2Icon.innerHTML = '<i class="bi bi-hourglass-split animate-pulse"></i>';
                step2Title.className = 'font-bold text-[11px] text-amber-800';
                step2Time.className = 'text-[10px] font-semibold text-amber-600 flex items-center whitespace-nowrap';
                step2Time.innerHTML = '<i class="bi bi-hourglass mr-1 text-[9px]"></i> Dalam Antrean';
                step2Desc.textContent = 'Menunggu staf ' + (unit || 'unit terkait') + ' membuka dan merespon laporan Anda.';

                // Step 3: Disabled
                step3Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-slate-100 text-slate-400 border border-slate-200';
                step3Icon.innerHTML = '<i class="bi bi-dash"></i>';
                step3Title.className = 'font-bold text-[11px] text-slate-400';
                step3Time.className = 'text-[10px] font-normal text-slate-400 flex items-center whitespace-nowrap';
                step3Time.innerHTML = '<i class="bi bi-dash mr-1 text-[10px]"></i> Belum Selesai';
                step3Desc.textContent = 'Tanggapan dan solusi belum diberikan oleh staf.';

                // Step 4: Disabled
                step4Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-slate-100 text-slate-400 border border-slate-200';
                step4Icon.innerHTML = '<i class="bi bi-lock text-[10px]"></i>';
                step4Title.className = 'font-bold text-[11px] text-slate-400';
                step4Time.className = 'text-[10px] font-normal text-slate-400 flex items-center whitespace-nowrap';
                step4Time.innerHTML = '<i class="bi bi-dash mr-1 text-[10px]"></i> Belum Ditutup';
                step4Desc.textContent = 'Tiket masih berstatus aktif.';

                stepperLine.className = 'absolute left-3 top-2.5 bottom-2.5 w-0.5 bg-slate-200';

            } else if (status === 'Diproses') {
                popWaBadge.className = 'inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full border bg-blue-50 text-blue-700 border-blue-200';
                popWaBadge.innerHTML = '<i class="bi bi-check-all text-slate-500 font-black text-base"></i> Diproses';

                // Step 2: Processing (Active)
                step2Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-blue-600 text-white shadow-xs';
                step2Icon.innerHTML = '<i class="bi bi-check-all text-sm font-bold"></i>';
                step2Title.className = 'font-bold text-[11px] text-blue-800';
                step2Time.className = 'text-[10px] font-mono font-bold text-blue-600 flex items-center whitespace-nowrap';
                step2Time.innerHTML = '<i class="bi bi-clock-history mr-1 text-[9px]"></i>' + (tglDiproses || updated || created);
                step2Desc.textContent = 'Staf ' + (unit || 'unit') + ' sedang aktif menangani kendala Anda.';

                // Tampilkan catatan investigasi/pengerjaan di Step 2 jika ada
                const noteDiproses = catatanProses || tanggapan;
                if (noteDiproses && step2Box && step2Text) {
                    step2Box.classList.remove('hidden');
                    step2Text.textContent = '"' + noteDiproses + '"';
                }

                // Step 3: Pending (BELUM SELESAI & TIDAK DITAMPILKAN TANGGAPAN DI SINI)
                step3Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-slate-100 text-slate-400 border border-slate-200';
                step3Icon.innerHTML = '<i class="bi bi-circle text-[8px]"></i>';
                step3Title.className = 'font-bold text-[11px] text-slate-400';
                step3Time.className = 'text-[10px] font-normal text-slate-400 flex items-center whitespace-nowrap';
                step3Time.innerHTML = '<i class="bi bi-dash mr-1 text-[10px]"></i> Belum Selesai';
                step3Desc.textContent = 'Solusi dan jawaban sedang dirumuskan oleh staf.';
                if (step3Box) step3Box.classList.add('hidden');

                // Step 4: Pending
                step4Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-slate-100 text-slate-400 border border-slate-200';
                step4Icon.innerHTML = '<i class="bi bi-lock text-[10px]"></i>';
                step4Title.className = 'font-bold text-[11px] text-slate-400';
                step4Time.className = 'text-[10px] font-normal text-slate-400 flex items-center whitespace-nowrap';
                step4Time.innerHTML = '<i class="bi bi-dash mr-1 text-[10px]"></i> Belum Ditutup';
                step4Desc.textContent = 'Tiket masih dalam proses penyelesaian.';

                stepperLine.className = 'absolute left-3 top-2.5 bottom-2.5 w-0.5 bg-blue-300';

            } else if (status === 'Selesai') {
                popWaBadge.className = 'inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full border bg-emerald-50 text-emerald-800 border-emerald-200';
                popWaBadge.innerHTML = '<i class="bi bi-check-all text-sky-500 font-black text-base"></i> Selesai';

                // Step 2: Complete
                step2Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-blue-500 text-white shadow-xs';
                step2Icon.innerHTML = '<i class="bi bi-check-lg"></i>';
                step2Title.className = 'font-bold text-[11px] text-slate-700';
                step2Time.className = 'text-[10px] font-mono font-bold text-slate-500 flex items-center whitespace-nowrap';
                step2Time.innerHTML = '<i class="bi bi-clock-history mr-1 text-[9px]"></i>' + (tglDiproses || created);
                step2Desc.textContent = 'Kendala telah ditelaah dan diproses oleh staf.';

                // Tampilkan catatan pengerjaan tahap Diproses jika ada
                if (catatanProses && step2Box && step2Text) {
                    step2Box.classList.remove('hidden');
                    step2Text.textContent = '"' + catatanProses + '"';
                }

                // Step 3: Complete with WhatsApp Blue Double Check & Solusi
                step3Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[11px] font-black bg-sky-500 text-white shadow-xs ring-2 ring-sky-200';
                step3Icon.innerHTML = '<i class="bi bi-check-all"></i>';
                step3Title.className = 'font-bold text-[11px] text-sky-900';
                step3Time.className = 'text-[10px] font-mono font-bold text-sky-700 flex items-center whitespace-nowrap';
                step3Time.innerHTML = '<i class="bi bi-clock-fill mr-1 text-[9px]"></i>' + (tglSelesai || tglTanggapan || updated);
                step3Desc.textContent = 'Solusi resmi telah diberikan oleh staf unit.';

                const noteSelesai = catatanSelesai || (!catatanProses ? tanggapan : '');
                if (noteSelesai && step3Box && step3Text) {
                    step3Box.classList.remove('hidden');
                    step3Text.textContent = '"' + noteSelesai + '"';
                }

                // Step 4: Solved but not fully closed
                step4Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-slate-100 text-slate-400 border border-slate-200';
                step4Icon.innerHTML = '<i class="bi bi-clock text-[10px]"></i>';
                step4Title.className = 'font-bold text-[11px] text-slate-400';
                step4Time.className = 'text-[10px] font-semibold text-slate-400 flex items-center whitespace-nowrap';
                step4Time.innerHTML = '<i class="bi bi-clock mr-1 text-[9px]"></i> Menunggu Konfirmasi';
                step4Desc.textContent = 'Tiket menunggu konfirmasi penutupan tuntas.';

                stepperLine.className = 'absolute left-3 top-2.5 bottom-2.5 w-0.5 bg-emerald-400';

            } else if (status === 'Ditutup') {
                popWaBadge.className = 'inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full border bg-purple-50 text-purple-800 border-purple-200';
                popWaBadge.innerHTML = '<i class="bi bi-patch-check-fill text-purple-600 text-xs"></i> Ditutup';

                // Step 2: Complete
                step2Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-blue-500 text-white shadow-xs';
                step2Icon.innerHTML = '<i class="bi bi-check-lg"></i>';
                step2Title.className = 'font-bold text-[11px] text-slate-700';
                step2Time.className = 'text-[10px] font-mono font-bold text-slate-500 flex items-center whitespace-nowrap';
                step2Time.innerHTML = '<i class="bi bi-clock-history mr-1 text-[9px]"></i>' + (tglDiproses || created);
                step2Desc.textContent = 'Kendala telah ditelaah dan diproses oleh staf.';
                if (catatanProses && step2Box && step2Text) {
                    step2Box.classList.remove('hidden');
                    step2Text.textContent = '"' + catatanProses + '"';
                }

                // Step 3: Complete with WhatsApp Blue Double Check & Solusi
                step3Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[11px] font-black bg-sky-500 text-white shadow-xs ring-2 ring-sky-200';
                step3Icon.innerHTML = '<i class="bi bi-check-all"></i>';
                step3Title.className = 'font-bold text-[11px] text-sky-900';
                step3Time.className = 'text-[10px] font-mono font-bold text-sky-700 flex items-center whitespace-nowrap';
                step3Time.innerHTML = '<i class="bi bi-clock-fill mr-1 text-[9px]"></i>' + (tglSelesai || tglTanggapan || updated);
                step3Desc.textContent = 'Solusi telah diterima oleh pelapor.';

                const noteSelesai = catatanSelesai || (!catatanProses ? tanggapan : '');
                if (noteSelesai && step3Box && step3Text) {
                    step3Box.classList.remove('hidden');
                    step3Text.textContent = '"' + noteSelesai + '"';
                }

                // Step 4: Closed
                step4Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-purple-600 text-white shadow-sm ring-2 ring-purple-200';
                step4Icon.innerHTML = '<i class="bi bi-check-lg font-black"></i>';
                step4Title.className = 'font-bold text-[11px] text-purple-900';
                step4Time.className = 'text-[10px] font-mono font-bold text-purple-700 flex items-center whitespace-nowrap';
                step4Time.innerHTML = '<i class="bi bi-patch-check-fill mr-1 text-[10px]"></i>' + (tglSelesai || updated || created);
                step4Desc.textContent = 'Kendala telah tuntas diselesaikan dan tiket resmi ditutup.';
                if (catatanTutup && step4Box && step4Text) {
                    step4Box.classList.remove('hidden');
                    step4Text.textContent = '"' + catatanTutup + '"';
                }

                stepperLine.className = 'absolute left-3 top-2.5 bottom-2.5 w-0.5 bg-purple-500';
            }

            // Show & Positioning
            popover.classList.remove('hidden');
            const rect = triggerEl.getBoundingClientRect();
            const actualWidth = popover.offsetWidth || 340;
            const actualHeight = popover.offsetHeight || 320;

            let top = rect.top - actualHeight - 12;
            if (top < 10) {
                top = rect.bottom + 12;
            }

            // Center precisely over the status badge
            let left = (rect.left + rect.right) / 2 - (actualWidth / 2);

            // Boundary check to keep within screen bounds
            const maxLeft = window.innerWidth - actualWidth - 20;
            if (left > maxLeft) {
                left = maxLeft;
            }
            if (left < 16) {
                left = 16;
            }

            popover.style.top = top + 'px';
            popover.style.left = left + 'px';

            requestAnimationFrame(() => {
                popover.classList.remove('opacity-0', '-translate-y-2');
                popover.classList.add('opacity-100', 'translate-y-0');
            });
        }

        document.querySelectorAll('.status-stepper-trigger').forEach(trigger => {
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
            if (popover && !popover.contains(e.target) && !e.target.closest('.status-stepper-trigger')) {
                hidePopover();
            }
        });
    </script>

    <!-- Global Custom Circle Cursor -->
    <?php $this->load->view('partials/custom_cursor'); ?>
</body>
</html>
