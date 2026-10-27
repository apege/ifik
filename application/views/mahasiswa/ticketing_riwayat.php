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

    <!-- Main Page Content Wrapper (Shrinks / Expands with Sidebar) -->
    <div id="mainPageContent" class="page-wrapper-for-sidebar min-h-screen flex flex-col flex-grow">

    <!-- Main Content Container -->
    <main class="min-h-screen p-6 sm:p-8 lg:p-10 max-w-7xl mx-auto w-full">
        
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
                                    $cleanCatatanProses = !empty($t->catatan_proses) ? htmlspecialchars(preg_replace('/\s+/u', ' ', html_entity_decode(trim(strip_tags(str_ireplace(['&nbsp;', '&amp;nbsp;'], ' ', $t->catatan_proses))), ENT_QUOTES, 'UTF-8'))) : '';
                                    $cleanCatatanSelesai = !empty($t->catatan_selesai) ? htmlspecialchars(preg_replace('/\s+/u', ' ', html_entity_decode(trim(strip_tags(str_ireplace(['&nbsp;', '&amp;nbsp;'], ' ', $t->catatan_selesai))), ENT_QUOTES, 'UTF-8'))) : '';
                                    $cleanCatatanTutup = !empty($t->catatan_tutup) ? htmlspecialchars(preg_replace('/\s+/u', ' ', html_entity_decode(trim(strip_tags(str_ireplace(['&nbsp;', '&amp;nbsp;'], ' ', $t->catatan_tutup))), ENT_QUOTES, 'UTF-8'))) : '';
                                    $tglDiprosesFmt = !empty($t->tgl_diproses) ? date('d M Y, H:i', strtotime($t->tgl_diproses)) . ' WIB' : '';
                                    $tglSelesaiFmt = !empty($t->tgl_closed) ? date('d M Y, H:i', strtotime($t->tgl_closed)) . ' WIB' : '';
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
                                        <div class="flex flex-wrap items-center gap-1.5 mb-1">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-orange-100 text-orange-800 border border-orange-200">
                                                <i class="bi bi-building-check"></i> <?= htmlspecialchars($t->tujuan_penerima ?? $t->unit_terkait ?? 'Unit Penanganan'); ?>
                                            </span>
                                        </div>
                                        <div class="text-[11px] text-slate-600 font-medium truncate max-w-xs">
                                            <i class="bi bi-tag-fill text-orange-400 text-[10px] mr-1"></i><?= htmlspecialchars($t->kategori); ?>
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
                                                  data-tgl-diproses="<?= $tglDiprosesFmt; ?>"
                                                  data-tgl-selesai="<?= $tglSelesaiFmt; ?>"
                                                  data-catatan-proses="<?= $cleanCatatanProses; ?>"
                                                  data-catatan-selesai="<?= $cleanCatatanSelesai; ?>"
                                                  data-catatan-tutup="<?= $cleanCatatanTutup; ?>"
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
                            $cleanCatatanProses = !empty($t->catatan_proses) ? htmlspecialchars(preg_replace('/\s+/u', ' ', html_entity_decode(trim(strip_tags(str_ireplace(['&nbsp;', '&amp;nbsp;'], ' ', $t->catatan_proses))), ENT_QUOTES, 'UTF-8'))) : '';
                            $cleanCatatanSelesai = !empty($t->catatan_selesai) ? htmlspecialchars(preg_replace('/\s+/u', ' ', html_entity_decode(trim(strip_tags(str_ireplace(['&nbsp;', '&amp;nbsp;'], ' ', $t->catatan_selesai))), ENT_QUOTES, 'UTF-8'))) : '';
                            $cleanCatatanTutup = !empty($t->catatan_tutup) ? htmlspecialchars(preg_replace('/\s+/u', ' ', html_entity_decode(trim(strip_tags(str_ireplace(['&nbsp;', '&amp;nbsp;'], ' ', $t->catatan_tutup))), ENT_QUOTES, 'UTF-8'))) : '';
                            $tglDiprosesFmt = !empty($t->tgl_diproses) ? date('d M Y, H:i', strtotime($t->tgl_diproses)) . ' WIB' : '';
                            $tglSelesaiFmt = !empty($t->tgl_closed) ? date('d M Y, H:i', strtotime($t->tgl_closed)) . ' WIB' : '';
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
                                          data-tgl-diproses="<?= $tglDiprosesFmt; ?>"
                                          data-tgl-selesai="<?= $tglSelesaiFmt; ?>"
                                          data-catatan-proses="<?= $cleanCatatanProses; ?>"
                                          data-catatan-selesai="<?= $cleanCatatanSelesai; ?>"
                                          data-catatan-tutup="<?= $cleanCatatanTutup; ?>"
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

                            <!-- Meta Info: Unit Dituju & Kategori -->
                            <div class="flex flex-wrap items-center gap-1.5 pt-0.5">
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-orange-800 bg-orange-100 px-2 py-0.5 rounded-md border border-orange-200">
                                    <i class="bi bi-building-check"></i> <?= htmlspecialchars($t->tujuan_penerima ?? $t->unit_terkait ?? 'Unit Penanganan'); ?>
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
    </div> <!-- /#mainPageContent -->

    <!-- Floating Tracking Popover Tooltip -->
    <div id="statusTrackingPopover" class="fixed z-50 hidden opacity-0 transition-all duration-200 pointer-events-none w-80 bg-white rounded-2xl shadow-2xl border border-slate-200/90 p-4">
        <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-100">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tracking Status</span>
            <span id="popWaBadge" class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full border"></span>
        </div>

        <div class="relative pl-6 space-y-4 text-left text-xs">
            <div id="stepperLine" class="absolute left-3 top-2.5 bottom-2.5 w-0.5 bg-slate-200"></div>

            <!-- Step 1: Menunggu -->
            <div class="relative">
                <div id="step1Icon" class="absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-slate-500 text-white shadow-xs">
                    <i class="bi bi-check"></i>
                </div>
                <div id="step1Title" class="font-bold text-[11px] text-slate-800">1. Dikirim</div>
                <div class="text-[10px] font-mono text-slate-500" id="step1Time">-</div>
                <div id="step1Desc" class="text-[11px] text-slate-500 mt-0.5">Laporan masuk ke antrean unit <span id="step1Unit" class="font-semibold text-slate-700"></span>.</div>
            </div>

            <!-- Step 2: Diproses -->
            <div class="relative">
                <div id="step2Icon" class="absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-slate-200 text-slate-500">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <div id="step2Title" class="font-bold text-[11px] text-slate-600">2. Sedang Diproses</div>
                <div id="step2Time" class="text-[10px] font-mono text-slate-400">-</div>
                <div id="step2Desc" class="text-[11px] text-slate-500 mt-0.5">Kendala sedang diinvestigasi & dikerjakan.</div>
                <div id="step2TanggapanBox" class="hidden mt-2 p-2.5 rounded-xl bg-blue-50 border border-blue-200 text-[11px] text-blue-900 leading-snug">
                    <span class="font-bold block text-blue-800 mb-0.5">Catatan Pengerjaan Unit:</span>
                    <span id="step2TanggapanText" class="italic line-clamp-3"></span>
                </div>
            </div>

            <!-- Step 3: Selesai -->
            <div class="relative">
                <div id="step3Icon" class="absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-slate-200 text-slate-500">
                    <i class="bi bi-check-all"></i>
                </div>
                <div id="step3Title" class="font-bold text-[11px] text-slate-600">3. Closed</div>
                <div id="step3Time" class="text-[10px] font-mono text-slate-400">-</div>
                <div id="step3Desc" class="text-[11px] text-slate-500 mt-0.5">Solusi telah diberikan, kendala selesai ditangani.</div>
                <div id="step3TanggapanBox" class="hidden mt-2 p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-[11px] text-emerald-900 leading-snug">
                    <span class="font-bold block text-emerald-800 mb-0.5">Solusi & Hasil Penyelesaian:</span>
                    <span id="step3TanggapanText" class="italic line-clamp-3"></span>
                </div>
            </div>

            <!-- Step 4: Ditutup (Hidden) -->
            <div class="relative hidden" id="popStep4Container">
                <div id="step4Icon" class="absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-slate-200 text-slate-500">
                    <i class="bi bi-patch-check-fill"></i>
                </div>
                <div id="step4Title" class="font-bold text-[11px] text-slate-600">4. Ditutup</div>
                <div id="step4Time" class="text-[10px] font-mono text-slate-400">-</div>
                <div id="step4Desc" class="text-[11px] text-slate-500 mt-0.5">Tiket ditutup secara permanen & diarsipkan.</div>
                <div id="step4TanggapanBox" class="hidden mt-2 p-2.5 rounded-xl bg-purple-50 border border-purple-200 text-[11px] text-purple-900 leading-snug">
                    <span class="font-bold block text-purple-800 mb-0.5">Catatan Arsip Penutupan:</span>
                    <span id="step4TanggapanText" class="italic line-clamp-3"></span>
                </div>
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
                        <span class="text-slate-400 block mb-0.5">Unit yang Dituju:</span>
                        <strong id="modalPenerima" class="text-orange-600 font-bold block">-</strong>
                        <span id="modalUnit" class="hidden"></span>
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
                    <div class="col-span-2 sm:col-span-3 pt-1 border-t border-slate-200/60 flex items-center justify-between text-[11px] text-slate-400">
                        <span>Diajukan Pada: <strong id="modalWaktu" class="text-slate-700 font-medium">-</strong></span>
                    </div>
                </div>

                <!-- Card Stepper Progress Tracking (3 Status) -->
                <div>
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 flex items-center justify-between">
                        <span>Alur Progres Penanganan</span>
                        <span class="text-[10px] text-emerald-600 font-bold"><i class="bi bi-shield-check"></i> Terverifikasi Sistem</span>
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2" id="mhsStepperContainer">
                        <!-- Step 1: Dikirim -->
                        <div id="mhsStep_Dikirim" class="p-2.5 rounded-2xl border-2 transition-all flex flex-col items-center text-center gap-1 bg-slate-50 border-slate-200">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold step-icon bg-slate-200 text-slate-500">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <span class="text-[10px] font-extrabold text-slate-700 leading-tight">1. Dikirim</span>
                            <span class="text-[8px] text-slate-400 step-desc">Antrean Masuk</span>
                        </div>

                        <!-- Step 2: Sedang Diproses -->
                        <div id="mhsStep_Sedang_Diproses" class="p-2.5 rounded-2xl border-2 transition-all flex flex-col items-center text-center gap-1 bg-slate-50 border-slate-200">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold step-icon bg-slate-200 text-slate-500">
                                <i class="bi bi-gear-wide-connected"></i>
                            </div>
                            <span class="text-[10px] font-extrabold text-slate-700 leading-tight">2. Sedang Diproses</span>
                            <span class="text-[8px] text-slate-400 step-desc">Sedang Ditangani</span>
                        </div>

                        <!-- Step 3: Closed -->
                        <div id="mhsStep_Closed" class="p-2.5 rounded-2xl border-2 transition-all flex flex-col items-center text-center gap-1 bg-slate-50 border-slate-200">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold step-icon bg-slate-200 text-slate-500">
                                <i class="bi bi-check2-circle"></i>
                            </div>
                            <span class="text-[10px] font-extrabold text-slate-700 leading-tight">3. Closed</span>
                            <span class="text-[8px] text-slate-400 step-desc">Selesai & Ditutup</span>
                        </div>
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
                    <div class="flex items-center gap-2 flex-wrap">
                        <button type="button" onclick="bukaModalPreviewLampiranRiwayat()" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-orange-50 hover:bg-orange-100 text-orange-700 text-xs font-bold border border-orange-200 transition-all cursor-pointer shadow-xs">
                            <i class="bi bi-paperclip text-sm"></i> <span id="modalLampiranName">Lihat Lampiran</span>
                        </button>
                        <button type="button" onclick="unduhLampiranRiwayat()" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold border border-slate-200 transition-all cursor-pointer shadow-xs" title="Unduh File Langsung">
                            <i class="bi bi-download"></i> <span>Unduh</span>
                        </button>
                    </div>
                </div>

                <!-- Riwayat & Tanggapan Unit Terkait Berdasarkan Tahap -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggapan & Progres Unit Terkait</h4>
                    
                    <!-- Catatan Tahap Diproses -->
                    <div id="modalRiwayatProsesBox" class="hidden p-4 rounded-2xl bg-blue-50/70 border border-blue-200/80 text-xs text-blue-950 leading-relaxed">
                        <div class="flex items-center justify-between mb-1.5 pb-1 border-b border-blue-200/50">
                            <span class="font-extrabold text-blue-800 flex items-center gap-1.5">
                                <i class="bi bi-gear-wide-connected text-blue-600"></i> Catatan Pengerjaan (Tahap Diproses)
                            </span>
                            <span id="modalTglDiproses" class="text-[10px] font-mono text-blue-600 font-semibold"></span>
                        </div>
                        <div id="modalCatatanProses" class="text-slate-700 whitespace-pre-wrap"></div>
                    </div>

                    <!-- Solusi Tahap Selesai -->
                    <div id="modalRiwayatSelesaiBox" class="hidden p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 text-xs text-emerald-950 leading-relaxed">
                        <div class="flex items-center justify-between mb-1.5 pb-1 border-b border-emerald-200/50">
                            <span class="font-extrabold text-emerald-800 flex items-center gap-1.5">
                                <i class="bi bi-check2-circle text-emerald-600"></i> Solusi & Tanggapan Akhir (Tahap Selesai)
                            </span>
                            <span id="modalTglSelesai" class="text-[10px] font-mono text-emerald-600 font-semibold"></span>
                        </div>
                        <div id="modalCatatanSelesai" class="text-slate-700 whitespace-pre-wrap"></div>
                    </div>

                    <!-- Catatan Tahap Ditutup -->
                    <div id="modalRiwayatTutupBox" class="hidden p-4 rounded-2xl bg-purple-50/70 border border-purple-200/80 text-xs text-purple-950 leading-relaxed">
                        <div class="flex items-center justify-between mb-1.5 pb-1 border-b border-purple-200/50">
                            <span class="font-extrabold text-purple-800 flex items-center gap-1.5">
                                <i class="bi bi-archive-fill text-purple-600"></i> Catatan Arsip Penutupan
                            </span>
                            <span id="modalTglClosed" class="text-[10px] font-mono text-purple-600 font-semibold"></span>
                        </div>
                        <div id="modalCatatanTutup" class="text-slate-700 whitespace-pre-wrap"></div>
                    </div>

                    <!-- Fallback Belum Ada Tanggapan -->
                    <div id="modalEmptyTanggapanBox" class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-400 italic">
                        Belum ada tanggapan atau catatan dari unit terkait. Tiket Anda sedang dalam antrean penanganan.
                    </div>
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

    <!-- Modal Popup Preview & Unduh Lampiran Pendukung -->
    <div id="modalPreviewLampiran" class="fixed inset-0 z-[70] hidden overflow-y-auto" aria-labelledby="modal-preview-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 py-6 text-center sm:p-0">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="tutupModalPreviewLampiranRiwayat()"></div>

            <!-- Dialog Box -->
            <div class="relative inline-block w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-slate-200/80 text-left overflow-hidden transform transition-all align-middle z-10">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                    <div class="flex items-center gap-3 min-w-0 pr-4">
                        <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-base font-bold flex-shrink-0 shadow-xs">
                            <i class="bi bi-paperclip"></i>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-sm font-black text-slate-800 tracking-tight" id="modal-preview-title">Lampiran Pendukung Kendala</h4>
                            <p class="text-[11px] text-slate-400 font-mono truncate max-w-sm" id="previewLampiranFilename">-</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <button type="button" onclick="unduhLampiranRiwayat()" id="previewDownloadBtn" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-xs transition-all cursor-pointer">
                            <i class="bi bi-download"></i> <span>Unduh</span>
                        </button>
                        <button type="button" onclick="tutupModalPreviewLampiranRiwayat()" class="w-8 h-8 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer" title="Tutup Preview">
                            <i class="bi bi-x-lg text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Body (Preview Content) -->
                <div class="p-6 bg-slate-900/5 flex flex-col items-center justify-center min-h-[320px] max-h-[70vh] overflow-auto" id="previewContainer">
                    <!-- Image Preview -->
                    <img id="previewImage" src="" alt="Preview Lampiran" class="hidden max-h-[62vh] max-w-full rounded-2xl shadow-md object-contain mx-auto transition-all">
                    
                    <!-- PDF / Iframe Preview -->
                    <iframe id="previewIframe" src="" class="hidden w-full h-[62vh] rounded-2xl border border-slate-200 shadow-xs bg-white"></iframe>

                    <!-- Fallback if file cannot be previewed -->
                    <div id="previewFallback" class="hidden flex flex-col items-center justify-center py-10 text-center space-y-3">
                        <div class="w-16 h-16 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center text-3xl shadow-sm">
                            <i class="bi bi-file-earmark-arrow-down-fill"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800" id="previewFallbackFilename">lampiran.file</p>
                            <p class="text-xs text-slate-400 mt-1 max-w-xs">Format file ini tidak mendukung preview langsung. Silakan klik tombol di bawah untuk mengunduh dan membukanya.</p>
                        </div>
                        <button type="button" onclick="unduhLampiranRiwayat()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-sm transition-all cursor-pointer">
                            <i class="bi bi-download"></i> Unduh Berkas Ini
                        </button>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-3.5 bg-white border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span class="flex items-center gap-1.5 text-slate-400 text-[11px]">
                        <i class="bi bi-info-circle text-orange-500"></i> Klik Unduh untuk menyimpan berkas ke perangkat Anda.
                    </span>
                    <div class="flex items-center gap-2">
                        <a id="previewOpenTabBtn" href="#" target="_blank" class="px-3 py-1.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs font-semibold transition-all">
                            Buka di Tab Baru <i class="bi bi-box-arrow-up-right text-[10px] ml-1"></i>
                        </a>
                        <button type="button" onclick="tutupModalPreviewLampiranRiwayat()" class="px-4 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Client-side Scripts for Filtering & Modal -->
    <script>
        const MHS_STATUS_WEIGHT = {
            'Dikirim':         1,
            'Menunggu':        1,
            'Sedang Diproses': 2,
            'Diproses':        2,
            'Closed':          3,
            'Selesai':         3,
            'Ditutup':         3
        };

        function getNormalizedMhsStatus(st) {
            if (!st) return 'Dikirim';
            if (st === 'Menunggu') return 'Dikirim';
            if (st === 'Diproses') return 'Sedang Diproses';
            if (st === 'Selesai' || st === 'Ditutup') return 'Closed';
            return st;
        }

        function updateMhsStepperProgress(status) {
            const normStatus = getNormalizedMhsStatus(status);
            const currentWeight = MHS_STATUS_WEIGHT[normStatus] || 1;

            ['Dikirim', 'Sedang Diproses', 'Closed'].forEach(st => {
                const key = st.replace(/\s+/g, '_');
                const card = document.getElementById('mhsStep_' + key) || document.getElementById('mhsStep_' + st);
                if (!card) return;

                const stepWeight = MHS_STATUS_WEIGHT[st];
                const iconBox = card.querySelector('.step-icon');
                const desc = card.querySelector('.step-desc');

                card.className = 'p-2.5 rounded-2xl border-2 transition-all flex flex-col items-center text-center gap-1';

                if (stepWeight < currentWeight) {
                    card.classList.add('border-emerald-200', 'bg-emerald-50/50');
                    if (iconBox) {
                        iconBox.className = 'w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold step-icon bg-emerald-500 text-white shadow-2xs';
                        iconBox.innerHTML = '<i class="bi bi-check-lg"></i>';
                    }
                    if (desc) desc.innerHTML = '<span class="text-emerald-600 font-bold">Selesai</span>';

                } else if (stepWeight === currentWeight) {
                    if (st === 'Closed') {
                        card.classList.add('border-emerald-500', 'bg-emerald-50', 'ring-2', 'ring-emerald-500/20');
                        if (iconBox) {
                            iconBox.className = 'w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold step-icon bg-emerald-600 text-white shadow-2xs';
                            iconBox.innerHTML = '<i class="bi bi-check-circle-fill"></i>';
                        }
                        if (desc) desc.innerHTML = '<span class="text-emerald-700 font-extrabold">Ditutup</span>';
                    } else if (st === 'Sedang Diproses') {
                        card.classList.add('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-500/20');
                        if (iconBox) {
                            iconBox.className = 'w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold step-icon bg-blue-600 text-white shadow-2xs';
                            iconBox.innerHTML = '<i class="bi bi-gear-wide-connected animate-spin"></i>';
                        }
                        if (desc) desc.innerHTML = '<span class="text-blue-700 font-extrabold">Aktif Ditangani</span>';
                    } else {
                        card.classList.add('border-amber-500', 'bg-amber-50', 'ring-2', 'ring-amber-500/20');
                        if (iconBox) {
                            iconBox.className = 'w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold step-icon bg-amber-500 text-white shadow-2xs';
                            iconBox.innerHTML = '<i class="bi bi-hourglass-split"></i>';
                        }
                        if (desc) desc.innerHTML = '<span class="text-amber-700 font-extrabold">Dalam Antrean</span>';
                    }

                } else {
                    card.classList.add('border-slate-200', 'bg-slate-50', 'opacity-50');
                    if (iconBox) {
                        iconBox.className = 'w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold step-icon bg-slate-200 text-slate-400';
                        iconBox.innerHTML = '<i class="bi bi-clock"></i>';
                    }
                    if (desc) desc.innerHTML = '<span class="text-slate-400">Menunggu</span>';
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

                        // Lampiran
                        const lampSection = document.getElementById('modalLampiranSection');
                        if (d.lampiran && d.lampiran_url) {
                            lampSection.classList.remove('hidden');
                            currentLampiranUrl = d.lampiran_url;
                            currentLampiranFilename = d.lampiran;
                            document.getElementById('modalLampiranName').textContent = 'Lihat Lampiran (' + d.lampiran + ')';
                        } else {
                            lampSection.classList.add('hidden');
                            currentLampiranUrl = '';
                            currentLampiranFilename = '';
                        }

                        // Tanggapan Terpisah Berdasarkan Tahap
                        const prosesBox = document.getElementById('modalRiwayatProsesBox');
                        const selesaiBox = document.getElementById('modalRiwayatSelesaiBox');
                        const tutupBox = document.getElementById('modalRiwayatTutupBox');
                        const emptyBox = document.getElementById('modalEmptyTanggapanBox');

                        let hasAnyNote = false;

                        // Tahap Diproses
                        if (d.catatan_proses && d.catatan_proses.trim()) {
                            prosesBox.classList.remove('hidden');
                            document.getElementById('modalCatatanProses').innerHTML = d.catatan_proses;
                            document.getElementById('modalTglDiproses').textContent = d.tgl_diproses ? ('Pada: ' + d.tgl_diproses) : '';
                            hasAnyNote = true;
                        } else {
                            prosesBox.classList.add('hidden');
                        }

                        // Tahap Selesai
                        const solNote = d.catatan_selesai || (!d.catatan_proses ? d.tanggapan : '');
                        if (solNote && solNote.trim() && (d.status === 'Selesai' || d.status === 'Ditutup')) {
                            selesaiBox.classList.remove('hidden');
                            document.getElementById('modalCatatanSelesai').innerHTML = solNote;
                            document.getElementById('modalTglSelesai').textContent = d.tgl_tanggapan ? ('Pada: ' + d.tgl_tanggapan) : (d.updated_at ? ('Pada: ' + d.updated_at) : '');
                            hasAnyNote = true;
                        } else {
                            selesaiBox.classList.add('hidden');
                        }

                        // Tahap Ditutup
                        if (d.catatan_tutup && d.catatan_tutup.trim()) {
                            tutupBox.classList.remove('hidden');
                            document.getElementById('modalCatatanTutup').innerHTML = d.catatan_tutup;
                            document.getElementById('modalTglClosed').textContent = d.tgl_closed ? ('Pada: ' + d.tgl_closed) : '';
                            hasAnyNote = true;
                        } else {
                            tutupBox.classList.add('hidden');
                        }

                        // Fallback jika status Diproses tapi belum ada catatan khusus
                        if (!hasAnyNote && d.status === 'Diproses' && d.tanggapan && d.tanggapan.trim()) {
                            prosesBox.classList.remove('hidden');
                            document.getElementById('modalCatatanProses').innerHTML = d.tanggapan;
                            document.getElementById('modalTglDiproses').textContent = d.tgl_diproses ? ('Pada: ' + d.tgl_diproses) : '';
                            hasAnyNote = true;
                        }

                        if (hasAnyNote) {
                            emptyBox.classList.add('hidden');
                        } else {
                            emptyBox.classList.remove('hidden');
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

        let currentLampiranUrl = '';
        let currentLampiranFilename = '';

        function bukaModalPreviewLampiranRiwayat() {
            if (!currentLampiranUrl) return;
            const filename = currentLampiranFilename || 'lampiran';
            document.getElementById('previewLampiranFilename').textContent = filename;
            document.getElementById('previewOpenTabBtn').href = currentLampiranUrl;

            const previewImg = document.getElementById('previewImage');
            const previewIframe = document.getElementById('previewIframe');
            const previewFallback = document.getElementById('previewFallback');

            previewImg.classList.add('hidden');
            previewIframe.classList.add('hidden');
            previewFallback.classList.add('hidden');

            const ext = filename.split('.').pop().toLowerCase();
            if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(ext)) {
                previewImg.src = currentLampiranUrl;
                previewImg.classList.remove('hidden');
            } else if (ext === 'pdf') {
                previewIframe.src = currentLampiranUrl;
                previewIframe.classList.remove('hidden');
            } else {
                document.getElementById('previewFallbackFilename').textContent = filename;
                previewFallback.classList.remove('hidden');
            }

            document.getElementById('modalPreviewLampiran').classList.remove('hidden');
        }

        function tutupModalPreviewLampiranRiwayat() {
            const modal = document.getElementById('modalPreviewLampiran');
            if (modal) modal.classList.add('hidden');
            const img = document.getElementById('previewImage');
            if (img) img.src = '';
            const iframe = document.getElementById('previewIframe');
            if (iframe) iframe.src = '';
        }

        function unduhLampiranRiwayat() {
            if (!currentLampiranUrl) return;
            fetch(currentLampiranUrl)
                .then(res => res.blob())
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = currentLampiranFilename || 'lampiran_tiket';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                })
                .catch(() => {
                    window.open(currentLampiranUrl, '_blank');
                });
        }

        function closeModal() {
            document.getElementById('detailModal').classList.add('hidden');
            tutupModalPreviewLampiranRiwayat();
        }

        // Close on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const previewModal = document.getElementById('modalPreviewLampiran');
                if (previewModal && !previewModal.classList.contains('hidden')) {
                    tutupModalPreviewLampiranRiwayat();
                    return;
                }
                closeModal();
            }
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
            const tglDiproses = triggerEl.getAttribute('data-tgl-diproses') || '';
            const tglSelesai = triggerEl.getAttribute('data-tgl-selesai') || '';
            const tglTanggapan = triggerEl.getAttribute('data-tgl-tanggapan') || '';
            const catatanProses = (triggerEl.getAttribute('data-catatan-proses') || '').trim();
            const catatanSelesai = (triggerEl.getAttribute('data-catatan-selesai') || '').trim();
            const catatanTutup = (triggerEl.getAttribute('data-catatan-tutup') || '').trim();
            const tanggapan = (triggerEl.getAttribute('data-tanggapan') || '').trim();

            const popKode = document.getElementById('popKode');
            if (popKode) popKode.textContent = kode;
            document.getElementById('step1Time').textContent = created;
            document.getElementById('step1Unit').textContent = unit || 'terkait';

            const popWaBadge = document.getElementById('popWaBadge');
            const step1Icon = document.getElementById('step1Icon');
            const step1Title = document.getElementById('step1Title');
            const step1Desc = document.getElementById('step1Desc');

            const step2Icon = document.getElementById('step2Icon');
            const step2Title = document.getElementById('step2Title');
            const step2Time = document.getElementById('step2Time');
            const step2Desc = document.getElementById('step2Desc');
            const step2Box = document.getElementById('step2TanggapanBox');
            const step2Text = document.getElementById('step2TanggapanText');

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
            const step4Box = document.getElementById('step4TanggapanBox');
            const step4Text = document.getElementById('step4TanggapanText');

            const stepperLine = document.getElementById('stepperLine');

            // Default titles
            if (step1Title) step1Title.textContent = '1. Menunggu';
            if (step2Title) step2Title.textContent = '2. Diproses';
            if (step3Title) step3Title.textContent = '3. Selesai';
            if (step4Title) step4Title.textContent = '4. Ditutup';

            // Reset notes boxes
            if (step2Box) { step2Box.classList.add('hidden'); step2Text.textContent = ''; }
            if (step3Box) { step3Box.classList.add('hidden'); step3Text.textContent = ''; }
            if (step4Box) { step4Box.classList.add('hidden'); step4Text.textContent = ''; }

            const normStatus = getNormalizedMhsStatus(status);

            if (normStatus === 'Dikirim') {
                popWaBadge.className = 'inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full border bg-amber-50 text-amber-700 border-amber-200';
                popWaBadge.innerHTML = '<i class="bi bi-hourglass-split text-amber-600 font-black text-xs"></i> 1. Dikirim';

                if (step1Title) step1Title.textContent = '1. Dikirim';
                if (step2Title) step2Title.textContent = '2. Sedang Diproses';
                if (step3Title) step3Title.textContent = '3. Closed';

                if (step1Icon) {
                    step1Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-amber-500 text-white shadow-xs animate-pulse';
                    step1Icon.innerHTML = '<i class="bi bi-hourglass-split"></i>';
                }
                if (step1Title) step1Title.className = 'font-bold text-[11px] text-amber-800';
                if (step1Desc) step1Desc.innerHTML = 'Laporan masuk antrean unit <span class="font-semibold text-slate-700">' + (unit || 'terkait') + '</span>.';

                step2Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-slate-100 text-slate-400 border border-slate-200';
                step2Icon.innerHTML = '<i class="bi bi-hourglass text-[9px]"></i>';
                step2Title.className = 'font-bold text-[11px] text-slate-400';
                step2Time.textContent = 'Menunggu Antrean';
                step2Desc.textContent = 'Menunggu staf ' + (unit || 'unit') + ' membuka dan menangani kendala.';

                step3Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-slate-100 text-slate-400 border border-slate-200';
                step3Icon.innerHTML = '<i class="bi bi-dash"></i>';
                step3Title.className = 'font-bold text-[11px] text-slate-400';
                step3Time.textContent = '-';
                step3Desc.textContent = 'Solusi belum tersedia.';

                stepperLine.className = 'absolute left-3 top-2.5 bottom-2.5 w-0.5 bg-slate-200';

            } else if (normStatus === 'Sedang Diproses') {
                popWaBadge.className = 'inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full border bg-blue-50 text-blue-700 border-blue-200';
                popWaBadge.innerHTML = '<i class="bi bi-gear-wide-connected text-blue-600 font-black text-xs"></i> 2. Sedang Diproses';

                if (step1Title) step1Title.textContent = '1. Dikirim';
                if (step2Title) step2Title.textContent = '2. Sedang Diproses';
                if (step3Title) step3Title.textContent = '3. Closed';

                if (step1Icon) {
                    step1Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-emerald-500 text-white shadow-xs';
                    step1Icon.innerHTML = '<i class="bi bi-check-lg"></i>';
                }
                if (step1Title) step1Title.className = 'font-bold text-[11px] text-slate-700';
                if (step1Desc) step1Desc.innerHTML = 'Laporan telah diterima unit <span class="font-semibold text-slate-700">' + (unit || 'terkait') + '</span>.';

                step2Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-blue-600 text-white shadow-xs';
                step2Icon.innerHTML = '<i class="bi bi-gear-wide-connected text-xs animate-spin"></i>';
                step2Title.className = 'font-bold text-[11px] text-blue-800';
                step2Time.textContent = tglDiproses || updated || created;
                step2Desc.textContent = 'Kendala sedang diinvestigasi & dikerjakan staf ' + (unit || 'unit') + '.';

                const noteDiproses = catatanProses || tanggapan;
                if (noteDiproses && step2Box && step2Text) {
                    step2Box.classList.remove('hidden');
                    step2Text.textContent = '"' + noteDiproses + '"';
                }

                step3Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-slate-100 text-slate-400 border border-slate-200';
                step3Icon.innerHTML = '<i class="bi bi-dash"></i>';
                step3Title.className = 'font-bold text-[11px] text-slate-400';
                step3Time.textContent = 'Sedang Disiapkan';
                step3Desc.textContent = 'Solusi dan jawaban sedang dirumuskan oleh staf.';
                if (step3Box) step3Box.classList.add('hidden');

                stepperLine.className = 'absolute left-3 top-2.5 bottom-2.5 w-0.5 bg-blue-300';

            } else {
                // Closed
                popWaBadge.className = 'inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full border bg-emerald-50 text-emerald-800 border-emerald-200';
                popWaBadge.innerHTML = '<i class="bi bi-check-circle-fill text-emerald-600 font-black text-xs"></i> 3. Closed';

                if (step1Title) step1Title.textContent = '1. Dikirim';
                if (step2Title) step2Title.textContent = '2. Sedang Diproses';
                if (step3Title) step3Title.textContent = '3. Closed';

                if (step1Icon) {
                    step1Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-emerald-500 text-white shadow-xs';
                    step1Icon.innerHTML = '<i class="bi bi-check-lg"></i>';
                }
                if (step1Title) step1Title.className = 'font-bold text-[11px] text-slate-700';
                if (step1Desc) step1Desc.innerHTML = 'Laporan diterima sistem.';

                step2Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-emerald-500 text-white shadow-xs';
                step2Icon.innerHTML = '<i class="bi bi-check-lg"></i>';
                step2Title.className = 'font-bold text-[11px] text-slate-700';
                step2Time.textContent = tglDiproses || updated;
                step2Desc.textContent = 'Proses penanganan selesai.';
                if (catatanProses && step2Box && step2Text) {
                    step2Box.classList.remove('hidden');
                    step2Text.textContent = '"' + catatanProses + '"';
                }

                step3Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[11px] font-black bg-emerald-600 text-white shadow-xs ring-2 ring-emerald-200';
                step3Icon.innerHTML = '<i class="bi bi-check2-all text-xs"></i>';
                step3Title.className = 'font-bold text-[11px] text-emerald-800';
                step3Time.textContent = tglSelesai || tglTanggapan || updated;
                step3Desc.textContent = 'Solusi telah diterima & kendala teratasi.';
                const noteSelesai = catatanSelesai || catatanTutup || (!catatanProses ? tanggapan : '');
                if (noteSelesai && step3Box && step3Text) {
                    step3Box.classList.remove('hidden');
                    step3Text.textContent = '"' + noteSelesai + '"';
                }

                stepperLine.className = 'absolute left-3 top-2.5 bottom-2.5 w-0.5 bg-emerald-500';
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
    <?php $this->load->view('partials/custom_cursor'); ?>
</body>
</html>