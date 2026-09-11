<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Riwayat Ticketing — IFIK Portal'; ?></title>
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body, button, input, textarea, select {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-orange-50/20 to-slate-100 min-h-screen text-slate-800 antialiased">

    <!-- Include Dosen Sidebar -->
    <?php $this->load->view('partials/dosen_sidebar'); ?>

    <!-- Main Content -->
    <main class="min-h-screen p-6 sm:p-8 lg:p-10 max-w-7xl mx-auto">
        
        <!-- Header & Breadcrumb -->
        <div class="mb-8">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-2">
                <a href="<?= site_url('dosen/bimbingan') ?>" class="hover:text-orange-600 transition-colors">Portal Dosen</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-slate-600">Ticketing</span>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-orange-600 font-bold">Riwayat Tiket</span>
            </div>
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3">
                        <span class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-orange-600 to-amber-500 text-white flex items-center justify-center shadow-lg shadow-orange-500/25">
                            <i class="bi bi-clock-history text-xl"></i>
                        </span>
                        Riwayat & Status Tiket
                    </h1>
                    <p class="text-sm text-slate-500 mt-1.5 max-w-2xl">
                        Pantau status penanganan kendala yang pernah Anda ajukan dan periksa tanggapan resmi dari tim layanan IFIK.
                    </p>
                </div>

                <a href="<?= site_url('dosen/ticketing/input') ?>" 
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white text-sm font-bold shadow-lg shadow-orange-500/25 transition-all">
                    <i class="bi bi-plus-lg text-base"></i>
                    <span>Buat Tiket Baru</span>
                </a>
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
            <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                
                <!-- Status Filter Pills -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-2 sm:pb-0" id="filter-pills">
                    <button type="button" onclick="filterByStatus('all')" class="status-btn active px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-orange-600 text-white shadow-xs">
                        Semua (<?= (int)($stats['total'] ?? 0); ?>)
                    </button>
                    <button type="button" onclick="filterByStatus('Menunggu')" class="status-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-all">
                        Menunggu (<?= (int)($stats['menunggu'] ?? 0); ?>)
                    </button>
                    <button type="button" onclick="filterByStatus('Diproses')" class="status-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-all">
                        Diproses (<?= (int)($stats['diproses'] ?? 0); ?>)
                    </button>
                    <button type="button" onclick="filterByStatus('Selesai')" class="status-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-all">
                        Selesai (<?= (int)($stats['selesai'] ?? 0); ?>)
                    </button>
                    <button type="button" onclick="filterByStatus('Ditutup')" class="status-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-all">
                        Ditutup (<?= (int)($stats['ditutup'] ?? 0); ?>)
                    </button>
                </div>

                <!-- Search Input -->
                <div class="relative w-full sm:w-72">
                    <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="searchInput" onkeyup="searchTable()"
                           placeholder="Cari kode, subjek, kategori..."
                           class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white text-xs font-semibold text-slate-800 placeholder-slate-400 transition-all outline-hidden">
                </div>

            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
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
                                        Jika Anda memiliki kendala pada sistem IFIK, bimbingan, atau jadwal ujian, silakan buat tiket baru.
                                    </p>
                                    <a href="<?= site_url('dosen/ticketing/input') ?>" 
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
                                        <?php if (!empty($t->unit_tujuan)): ?>
                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-orange-700 bg-orange-50 px-2 py-0.5 rounded-md border border-orange-200/60 mb-1">
                                                <i class="bi bi-building"></i> <?= htmlspecialchars($t->unit_tujuan); ?>
                                            </span>
                                        <?php endif; ?>
                                        <p class="font-semibold text-xs text-slate-700">
                                            <?= htmlspecialchars($t->kategori); ?>
                                        </p>
                                    </td>

                                    <!-- Subjek -->
                                    <td class="py-4 px-6 max-w-xs">
                                        <p class="font-bold text-slate-800 text-sm truncate"><?= htmlspecialchars($t->subjek); ?></p>
                                        <p class="text-xs text-slate-400 truncate mt-0.5"><?= htmlspecialchars(mb_substr(strip_tags($t->deskripsi), 0, 70)); ?>...</p>
                                    </td>

                                    <!-- Prioritas -->
                                    <td class="py-4 px-6 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border <?= $prioColor; ?>">
                                            <?= htmlspecialchars($t->prioritas); ?>
                                        </span>
                                    </td>

                                    <!-- Status (WhatsApp Ticks & Stepper Hover) -->
                                    <td class="py-4 px-6 text-center">
                                        <div class="relative inline-flex items-center cursor-help status-stepper-trigger select-none"
                                             data-kode="<?= htmlspecialchars($t->kode_tiket); ?>"
                                             data-status="<?= htmlspecialchars($t->status); ?>"
                                             data-unit="<?= htmlspecialchars($t->unit_tujuan); ?>"
                                             data-subjek="<?= htmlspecialchars($t->subjek); ?>"
                                             data-created="<?= date('d M Y, H:i', strtotime($t->created_at)) . ' WIB'; ?>"
                                             data-updated="<?= !empty($t->updated_at) ? date('d M Y, H:i', strtotime($t->updated_at)) . ' WIB' : ''; ?>"
                                             data-tgl-tanggapan="<?= !empty($t->tgl_tanggapan) ? date('d M Y, H:i', strtotime($t->tgl_tanggapan)) . ' WIB' : (!empty($t->updated_at) && $t->status !== 'Menunggu' ? date('d M Y, H:i', strtotime($t->updated_at)) . ' WIB' : ''); ?>"
                                             data-tanggapan="<?= htmlspecialchars(strip_tags($t->tanggapan ?? '')); ?>">
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
                    </tbody>
                </table>
            </div>

        </div>

    </main>

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

            <!-- Step 1: Tiket Terkirim -->
            <div class="relative">
                <span id="step1Icon" class="absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-emerald-500 text-white shadow-xs">
                    <i class="bi bi-check-lg"></i>
                </span>
                <div class="flex items-center justify-between gap-2">
                    <span class="font-bold text-slate-800 text-[11px]">1. Tiket Terkirim</span>
                    <span id="step1Time" class="text-[10px] font-mono text-emerald-600 font-bold whitespace-nowrap">-</span>
                </div>
                <p class="text-[10px] text-slate-400 leading-snug mt-0.5">
                    Laporan kendala masuk ke sistem & antrean unit <span id="step1Unit" class="font-semibold text-slate-600"></span>.
                </p>
            </div>

            <!-- Step 2: Sedang Diproses -->
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
            </div>

            <!-- Step 3: Tanggapan & Solusi -->
            <div class="relative">
                <span id="step3Icon" class="absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold border">
                    <!-- Dynamic Icon -->
                </span>
                <div class="flex items-center justify-between gap-2">
                    <span id="step3Title" class="font-bold text-[11px]">3. Tanggapan & Solusi</span>
                    <span id="step3Time" class="text-[10px] font-mono font-bold whitespace-nowrap">-</span>
                </div>
                <p id="step3Desc" class="text-[10px] text-slate-400 leading-snug mt-0.5">
                    -
                </p>

                <!-- Cuplikan Balasan Tanggapan (Jika sudah ada tanggapan) -->
                <div id="step3TanggapanBox" class="hidden mt-1.5 p-2 rounded-xl bg-sky-50 border border-sky-200/80 text-[10px] text-slate-700 leading-relaxed">
                    <div class="text-[9px] font-bold text-sky-800 uppercase flex items-center gap-1 mb-0.5">
                        <i class="bi bi-chat-quote-fill text-sky-600"></i>
                        <span>Respon Resmi Unit:</span>
                    </div>
                    <div id="step3TanggapanText" class="italic line-clamp-2 text-slate-600"></div>
                </div>
            </div>

            <!-- Step 4: Ditutup -->
            <div class="relative">
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
                        <span id="modalUnit" class="font-bold text-orange-600">-</span>
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
                    <div>
                        <span class="text-slate-400 block font-semibold">Tanggal Diajukan</span>
                        <span id="modalWaktu" class="font-bold text-slate-800">-</span>
                    </div>
                </div>

                <!-- Deskripsi Lengkap (Rich Text) -->
                <div>
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Deskripsi Kendala</h4>
                    <div id="modalDeskripsi" class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-700 leading-relaxed overflow-x-auto space-y-2">
                        -
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
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tanggapan / Solusi Tim Layanan IFIK</h4>
                    <div id="modalTanggapan" class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100 text-xs text-emerald-900 leading-relaxed">
                        <p class="text-slate-400 italic">Belum ada tanggapan dari tim admin. Tiket Anda sedang dalam antrean review.</p>
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
            fetch('<?= site_url("dosen/ticketing/detail/") ?>' + encodeURIComponent(kodeTiket))
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        const d = result.data;
                        document.getElementById('modalKode').textContent = d.kode_tiket;
                        document.getElementById('modalSubjek').textContent = d.subjek;
                        document.getElementById('modalPelapor').textContent = d.nama_dosen + (d.nidn ? ' (' + d.nidn + ')' : '');
                        document.getElementById('modalUnit').textContent = d.unit_tujuan || 'Layanan IFIK';
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
                            respBox.innerHTML = '<span class="text-slate-400 italic">Belum ada tanggapan dari tim admin. Tiket Anda sedang dalam antrean review.</span>';
                            respTime.textContent = '';
                        }

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

        // Filter Table by Status Pill
        function filterByStatus(status) {
            // Update active pill button
            document.querySelectorAll('.status-btn').forEach(btn => {
                btn.classList.remove('active', 'bg-orange-600', 'text-white', 'shadow-xs');
                btn.classList.add('text-slate-500', 'hover:bg-slate-100');
            });
            event.target.classList.add('active', 'bg-orange-600', 'text-white', 'shadow-xs');
            event.target.classList.remove('text-slate-500', 'hover:bg-slate-100');

            const rows = document.querySelectorAll('.ticket-row');
            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status');
                if (status === 'all' || rowStatus === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Search in Table
        function searchTable() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('.ticket-row');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
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

        document.querySelectorAll('.status-stepper-trigger').forEach(trigger => {
            trigger.addEventListener('mouseenter', function(e) {
                clearTimeout(popoverTimeout);

                const kode = this.getAttribute('data-kode');
                const status = this.getAttribute('data-status');
                const unit = this.getAttribute('data-unit');
                const created = this.getAttribute('data-created');
                const updated = this.getAttribute('data-updated');
                const tglTanggapan = this.getAttribute('data-tgl-tanggapan');
                const tanggapan = this.getAttribute('data-tanggapan');

                document.getElementById('popKode').textContent = kode;
                document.getElementById('step1Time').innerHTML = '<i class="bi bi-clock-fill text-[9px] mr-1"></i>' + created;
                document.getElementById('step1Unit').textContent = unit || 'terkait';

                const popWaBadge = document.getElementById('popWaBadge');

                // Step 2 elements
                const step2Icon = document.getElementById('step2Icon');
                const step2Title = document.getElementById('step2Title');
                const step2Time = document.getElementById('step2Time');
                const step2Desc = document.getElementById('step2Desc');

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

                const stepperLine = document.getElementById('stepperLine');

                // Reset step 3 quote
                step3Box.classList.add('hidden');
                step3Text.textContent = '';

                if (status === 'Menunggu') {
                    popWaBadge.className = 'inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full border bg-amber-50 text-amber-700 border-amber-200';
                    popWaBadge.innerHTML = '<i class="bi bi-check text-slate-400 font-black text-sm"></i> Menunggu';

                    // Step 2: Waiting
                    step2Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-amber-100 text-amber-600 border border-amber-300';
                    step2Icon.innerHTML = '<i class="bi bi-hourglass-split animate-pulse"></i>';
                    step2Title.className = 'font-bold text-[11px] text-amber-800';
                    step2Time.className = 'text-[10px] font-semibold text-amber-600 flex items-center whitespace-nowrap';
                    step2Time.innerHTML = '<i class="bi bi-hourglass-split mr-1 text-[9px]"></i> Menunggu Antrean';
                    step2Desc.textContent = 'Menunggu giliran peninjauan oleh staf ' + (unit || 'terkait') + '.';

                    // Step 3: Pending
                    step3Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-slate-100 text-slate-400 border border-slate-200';
                    step3Icon.innerHTML = '<i class="bi bi-circle text-[8px]"></i>';
                    step3Title.className = 'font-bold text-[11px] text-slate-400';
                    step3Time.className = 'text-[10px] font-normal text-slate-400 flex items-center whitespace-nowrap';
                    step3Time.innerHTML = '<i class="bi bi-dash mr-1 text-[10px]"></i> Belum Ada Solusi';
                    step3Desc.textContent = 'Solusi akan disampaikan setelah kendala dianalisis.';

                    // Step 4: Pending
                    step4Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-slate-100 text-slate-400 border border-slate-200';
                    step4Icon.innerHTML = '<i class="bi bi-lock text-[10px]"></i>';
                    step4Title.className = 'font-bold text-[11px] text-slate-400';
                    step4Time.className = 'text-[10px] font-normal text-slate-400 flex items-center whitespace-nowrap';
                    step4Time.innerHTML = '<i class="bi bi-dash mr-1 text-[10px]"></i> Belum Ditutup';
                    step4Desc.textContent = 'Tiket masih berstatus aktif dalam antrean.';

                    stepperLine.className = 'absolute left-3 top-2.5 bottom-2.5 w-0.5 bg-slate-200';

                } else if (status === 'Diproses') {
                    popWaBadge.className = 'inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full border bg-blue-50 text-blue-700 border-blue-200';
                    popWaBadge.innerHTML = '<i class="bi bi-check-all text-slate-500 font-black text-base"></i> Diproses';

                    // Step 2: Processing (Active)
                    step2Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-blue-600 text-white shadow-xs';
                    step2Icon.innerHTML = '<i class="bi bi-check-all text-sm font-bold"></i>';
                    step2Title.className = 'font-bold text-[11px] text-blue-800';
                    step2Time.className = 'text-[10px] font-mono font-bold text-blue-600 flex items-center whitespace-nowrap';
                    step2Time.innerHTML = '<i class="bi bi-clock-history mr-1 text-[9px]"></i>' + (updated || created);
                    step2Desc.textContent = 'Staf ' + (unit || 'unit') + ' sedang aktif menangani kendala Anda.';

                    // Step 3: Preparing or with notes
                    if (tanggapan && tanggapan.trim()) {
                        step3Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-blue-100 text-blue-600 border border-blue-300';
                        step3Icon.innerHTML = '<i class="bi bi-chat-dots-fill text-[9px]"></i>';
                        step3Title.className = 'font-bold text-[11px] text-blue-800';
                        step3Time.className = 'text-[10px] font-mono font-bold text-blue-600 flex items-center whitespace-nowrap';
                        step3Time.innerHTML = '<i class="bi bi-clock mr-1 text-[9px]"></i>' + (tglTanggapan || updated);
                        step3Desc.textContent = 'Staf telah memberikan catatan tanggapan.';
                        step3Box.classList.remove('hidden');
                        step3Text.textContent = '"' + tanggapan.trim() + '"';
                    } else {
                        step3Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200';
                        step3Icon.innerHTML = '<i class="bi bi-hourglass-split animate-pulse"></i>';
                        step3Title.className = 'font-bold text-[11px] text-slate-600';
                        step3Time.className = 'text-[10px] font-semibold text-amber-600 flex items-center whitespace-nowrap';
                        step3Time.innerHTML = '<i class="bi bi-hourglass mr-1 text-[9px]"></i> Sedang Disiapkan';
                        step3Desc.textContent = 'Solusi dan jawaban sedang dirumuskan oleh staf.';
                    }

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
                    step2Time.innerHTML = '<i class="bi bi-clock-history mr-1 text-[9px]"></i>' + (updated || created);
                    step2Desc.textContent = 'Kendala telah ditelaah dan diproses oleh staf.';

                    // Step 3: Complete with WhatsApp Blue Double Check & Solusi
                    step3Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[11px] font-black bg-sky-500 text-white shadow-xs ring-2 ring-sky-200';
                    step3Icon.innerHTML = '<i class="bi bi-check-all"></i>';
                    step3Title.className = 'font-bold text-[11px] text-sky-900';
                    step3Time.className = 'text-[10px] font-mono font-bold text-sky-700 flex items-center whitespace-nowrap';
                    step3Time.innerHTML = '<i class="bi bi-clock-fill mr-1 text-[9px]"></i>' + (tglTanggapan || updated);
                    step3Desc.textContent = 'Solusi resmi telah diberikan oleh staf unit.';

                    if (tanggapan && tanggapan.trim()) {
                        step3Box.classList.remove('hidden');
                        step3Text.textContent = '"' + tanggapan.trim() + '"';
                    }

                    // Step 4: Ready to close
                    step4Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-300';
                    step4Icon.innerHTML = '<i class="bi bi-hourglass-split"></i>';
                    step4Title.className = 'font-bold text-[11px] text-amber-800';
                    step4Time.className = 'text-[10px] font-semibold text-amber-600 flex items-center whitespace-nowrap';
                    step4Time.innerHTML = '<i class="bi bi-check-circle mr-1 text-[9px]"></i> Siap Ditutup';
                    step4Desc.textContent = 'Solusi telah terkirim, menunggu konfirmasi penutupan.';

                    stepperLine.className = 'absolute left-3 top-2.5 bottom-2.5 w-0.5 bg-sky-400';

                } else {
                    // Status Ditutup
                    popWaBadge.className = 'inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full border bg-slate-100 text-slate-700 border-slate-300';
                    popWaBadge.innerHTML = '<i class="bi bi-patch-check-fill text-purple-600 text-xs"></i> Ditutup';

                    // Step 2: Complete
                    step2Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-blue-500 text-white shadow-xs';
                    step2Icon.innerHTML = '<i class="bi bi-check-lg"></i>';
                    step2Title.className = 'font-bold text-[11px] text-slate-700';
                    step2Time.className = 'text-[10px] font-mono font-bold text-slate-500 flex items-center whitespace-nowrap';
                    step2Time.innerHTML = '<i class="bi bi-clock-history mr-1 text-[9px]"></i>' + (updated || created);
                    step2Desc.textContent = 'Kendala telah selesai diproses.';

                    // Step 3: Complete with Solusi
                    step3Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[11px] font-black bg-sky-500 text-white shadow-xs';
                    step3Icon.innerHTML = '<i class="bi bi-check-all"></i>';
                    step3Title.className = 'font-bold text-[11px] text-sky-900';
                    step3Time.className = 'text-[10px] font-mono font-bold text-sky-700 flex items-center whitespace-nowrap';
                    step3Time.innerHTML = '<i class="bi bi-clock-fill mr-1 text-[9px]"></i>' + (tglTanggapan || updated);
                    step3Desc.textContent = 'Solusi resmi telah diterima pelapor.';

                    if (tanggapan && tanggapan.trim()) {
                        step3Box.classList.remove('hidden');
                        step3Text.textContent = '"' + tanggapan.trim() + '"';
                    }

                    // Step 4: Closed
                    step4Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-purple-600 text-white shadow-sm ring-2 ring-purple-200';
                    step4Icon.innerHTML = '<i class="bi bi-check-lg font-black"></i>';
                    step4Title.className = 'font-bold text-[11px] text-purple-900';
                    step4Time.className = 'text-[10px] font-mono font-bold text-purple-700 flex items-center whitespace-nowrap';
                    step4Time.innerHTML = '<i class="bi bi-patch-check-fill mr-1 text-[10px]"></i>' + (updated || tglTanggapan || created);
                    step4Desc.textContent = 'Kendala telah tuntas diselesaikan dan tiket resmi ditutup.';

                    stepperLine.className = 'absolute left-3 top-2.5 bottom-2.5 w-0.5 bg-purple-500';
                }

                // Show & Positioning
                popover.classList.remove('hidden');
                const rect = this.getBoundingClientRect();
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
            });

            trigger.addEventListener('mouseleave', function() {
                popoverTimeout = setTimeout(() => {
                    hidePopover();
                }, 150);
            });
        });
    </script>
</body>
</html>
