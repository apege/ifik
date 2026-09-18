<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Inbox Respon Tiket — Laboran'; ?> - IFIK</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

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
    </style>
</head>
<body class="antialiased">

    <!-- Curved Animated Sidebar for Laboran -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <!-- Header Navigation -->
    <header class="glass-header sticky top-0 z-30 px-4 sm:px-6 py-3.5 sm:py-4 pl-16 sm:pl-20">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
            <div class="flex items-start sm:items-center gap-3 w-full sm:w-auto">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-orange-100 text-brand-600 flex items-center justify-center font-bold text-lg sm:text-xl shadow-xs border border-orange-200/50 shrink-0 mt-0.5 sm:mt-0">
                    <i class="bi bi-tools"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                        <h1 class="text-sm sm:text-xl font-extrabold text-slate-900 tracking-tight leading-snug">Inbox Respon Tiket Laboratorium</h1>
                        <span class="px-2 py-0.5 rounded-md bg-blue-100 text-blue-800 text-[10px] font-extrabold tracking-wider uppercase border border-blue-200 shrink-0">Panel Laboran</span>
                    </div>
                    <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5 leading-relaxed">Tinjau & respon laporan kendala khusus unit Laboratorium & Sarana Prasarana.</p>
                </div>
            </div>

            <!-- Quick Action Links -->
            <div class="flex items-center gap-2 w-full sm:w-auto pt-1 sm:pt-0">
                <a href="<?= site_url('laboran/ticketing/input'); ?>" class="flex-1 sm:flex-initial justify-center inline-flex items-center gap-1.5 px-3 py-2 sm:px-3.5 sm:py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-xs transition-all text-center">
                    <i class="bi bi-plus-circle-fill"></i> <span>Buat Tiket Baru</span>
                </a>
                <a href="<?= site_url('laboran/ticketing/riwayat'); ?>" class="flex-1 sm:flex-initial justify-center inline-flex items-center gap-1.5 px-3 py-2 sm:px-3.5 sm:py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all border border-slate-200 text-center">
                    <i class="bi bi-clock-history"></i> <span>Riwayat Tiket Saya</span>
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

        <!-- Stats Overview Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 sm:gap-4 mb-8">
            <!-- Total -->
            <a href="<?= site_url('laboran/respon-ticketing?status=all'); ?>" class="bg-white p-4 rounded-2xl border <?= $filterStatus === 'all' ? 'border-orange-500 ring-2 ring-orange-100' : 'border-slate-200/80 hover:border-slate-300' ?> shadow-xs transition-all flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Masuk</span>
                    <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xs"><i class="bi bi-inbox-fill"></i></span>
                </div>
                <div class="text-2xl font-extrabold text-slate-900 mt-2 font-mono"><?= $stats['total'] ?? 0; ?></div>
            </a>

            <!-- Menunggu -->
            <a href="<?= site_url('laboran/respon-ticketing?status=Menunggu'); ?>" class="bg-white p-4 rounded-2xl border <?= $filterStatus === 'Menunggu' ? 'border-amber-500 ring-2 ring-amber-100' : 'border-slate-200/80 hover:border-slate-300' ?> shadow-xs transition-all flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">Menunggu</span>
                    <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs"><i class="bi bi-hourglass-split"></i></span>
                </div>
                <div class="text-2xl font-extrabold text-amber-600 mt-2 font-mono"><?= $stats['menunggu'] ?? 0; ?></div>
            </a>

            <!-- Diproses -->
            <a href="<?= site_url('laboran/respon-ticketing?status=Diproses'); ?>" class="bg-white p-4 rounded-2xl border <?= $filterStatus === 'Diproses' ? 'border-blue-500 ring-2 ring-blue-100' : 'border-slate-200/80 hover:border-slate-300' ?> shadow-xs transition-all flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Diproses</span>
                    <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs"><i class="bi bi-gear-fill"></i></span>
                </div>
                <div class="text-2xl font-extrabold text-blue-600 mt-2 font-mono"><?= $stats['diproses'] ?? 0; ?></div>
            </a>

            <!-- Selesai -->
            <a href="<?= site_url('laboran/respon-ticketing?status=Selesai'); ?>" class="bg-white p-4 rounded-2xl border <?= $filterStatus === 'Selesai' ? 'border-emerald-500 ring-2 ring-emerald-100' : 'border-slate-200/80 hover:border-slate-300' ?> shadow-xs transition-all flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Selesai</span>
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs"><i class="bi bi-check2-circle"></i></span>
                </div>
                <div class="text-2xl font-extrabold text-emerald-600 mt-2 font-mono"><?= $stats['selesai'] ?? 0; ?></div>
            </a>

            <!-- Ditutup -->
            <a href="<?= site_url('laboran/respon-ticketing?status=Ditutup'); ?>" class="bg-white p-4 rounded-2xl border <?= $filterStatus === 'Ditutup' ? 'border-slate-400 ring-2 ring-slate-100' : 'border-slate-200/80 hover:border-slate-300' ?> shadow-xs transition-all flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Ditutup</span>
                    <span class="w-7 h-7 rounded-lg bg-slate-50 text-slate-400 flex items-center justify-center text-xs"><i class="bi bi-archive-fill"></i></span>
                </div>
                <div class="text-2xl font-extrabold text-slate-400 mt-2 font-mono"><?= $stats['ditutup'] ?? 0; ?></div>
            </a>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-xs mb-6 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 sm:gap-4">
            <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto w-full md:w-auto pb-1.5 md:pb-0 scrollbar-none" style="-webkit-overflow-scrolling: touch;">
                <?php
                $statusPills = [
                    'all'      => 'Semua Tiket',
                    'Menunggu' => 'Menunggu',
                    'Diproses' => 'Sedang Diproses',
                    'Selesai'  => 'Selesai',
                    'Ditutup'  => 'Ditutup'
                ];
                foreach ($statusPills as $stKey => $stLabel):
                    $isActive = ($filterStatus === $stKey);
                ?>
                    <a href="<?= site_url('laboran/respon-ticketing?status=' . $stKey . ($search ? '&q=' . urlencode($search) : '')); ?>" 
                       class="shrink-0 px-3 py-1.5 sm:px-3.5 sm:py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all <?= $isActive ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600' ?>">
                        <?= $stLabel; ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Search Form -->
            <form method="GET" action="<?= site_url('laboran/respon-ticketing'); ?>" class="w-full md:w-72 relative">
                <input type="hidden" name="status" value="<?= htmlspecialchars($filterStatus); ?>">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400 text-xs">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="q" value="<?= htmlspecialchars($search); ?>" placeholder="Cari kode, nama, kendala..." 
                           class="w-full pl-9 pr-8 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-medium">
                    <?php if (!empty($search)): ?>
                        <a href="<?= site_url('laboran/respon-ticketing?status=' . $filterStatus); ?>" class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400 hover:text-slate-600 text-xs">
                            <i class="bi bi-x-circle-fill"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Table / Cards of Incoming Lab Tickets -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <!-- Desktop Table View -->
            <div class="overflow-x-auto hidden md:block">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-extrabold uppercase text-slate-500 tracking-wider">
                            <th class="py-3.5 px-4 font-extrabold">Kode Tiket</th>
                            <th class="py-3.5 px-4 font-extrabold">Pengirim / Dosen</th>
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
                                    // Status Badge styling ala WhatsApp ticks & Stepper Hover
                                    $statusColor = 'bg-amber-50 text-amber-800 border-amber-200';
                                    $waTickIcon = '<i class="bi bi-check text-slate-400 font-bold text-sm"></i>';

                                    if ($t->status === 'Diproses') {
                                        $statusColor = 'bg-blue-50 text-blue-800 border-blue-200';
                                        $waTickIcon = '<i class="bi bi-check-all text-slate-500 font-bold text-base -mr-0.5"></i>';
                                    } elseif ($t->status === 'Selesai') {
                                        $statusColor = 'bg-emerald-50 text-emerald-800 border-emerald-200';
                                        $waTickIcon = '<i class="bi bi-check-all text-sky-500 font-bold text-base -mr-0.5"></i>';
                                    } elseif ($t->status === 'Ditutup') {
                                        $statusColor = 'bg-slate-100 text-slate-700 border-slate-300';
                                        $waTickIcon = '<i class="bi bi-patch-check-fill text-purple-600 text-xs mr-0.5"></i>';
                                    }

                                    // Priority Badge Colors
                                    $prioClass = 'text-slate-600 bg-slate-50 border-slate-200';
                                    if ($t->prioritas === 'Sedang') $prioClass = 'text-blue-700 bg-blue-50 border-blue-200';
                                    elseif ($t->prioritas === 'Tinggi') $prioClass = 'text-amber-700 bg-amber-50 border-amber-200';
                                    elseif ($t->prioritas === 'Darurat') $prioClass = 'text-rose-700 bg-rose-50 border-rose-200 font-bold';
                                ?>
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <!-- Kode Tiket -->
                                    <td class="py-3.5 px-4">
                                        <span class="font-mono font-bold text-slate-900 bg-slate-100 px-2 py-1 rounded-md text-[11px] border border-slate-200">
                                            <?= htmlspecialchars($t->kode_tiket); ?>
                                        </span>
                                    </td>

                                    <!-- Pengirim / Dosen -->
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-900"><?= htmlspecialchars($t->nama_dosen); ?></div>
                                        <div class="text-[11px] text-slate-400 font-mono mt-0.5">NIDN: <?= htmlspecialchars($t->nidn ?: '-'); ?></div>
                                    </td>

                                    <!-- Kendala & Kategori -->
                                    <td class="py-3.5 px-4 max-w-xs">
                                        <div class="font-bold text-slate-800 truncate" title="<?= htmlspecialchars($t->subjek); ?>">
                                            <?= htmlspecialchars($t->subjek); ?>
                                        </div>
                                        <div class="text-[11px] text-slate-400 mt-0.5 truncate" title="<?= htmlspecialchars($t->kategori); ?>">
                                            <i class="bi bi-tag mr-0.5"></i> <?= htmlspecialchars($t->kategori); ?>
                                        </div>
                                    </td>

                                    <!-- Prioritas -->
                                    <td class="py-3.5 px-4">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold border <?= $prioClass; ?>">
                                            <?= $t->prioritas; ?>
                                        </span>
                                    </td>

                                    <!-- Status (Hover Stepper Trigger) -->
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="relative inline-flex items-center cursor-help status-stepper-trigger select-none"
                                             data-kode="<?= htmlspecialchars($t->kode_tiket); ?>"
                                             data-status="<?= htmlspecialchars($t->status); ?>"
                                             data-unit="<?= htmlspecialchars($t->unit_tujuan); ?>"
                                             data-subjek="<?= htmlspecialchars($t->subjek); ?>"
                                             data-created="<?= date('d M Y, H:i', strtotime($t->created_at)) . ' WIB'; ?>"
                                             data-updated="<?= !empty($t->updated_at) ? date('d M Y, H:i', strtotime($t->updated_at)) . ' WIB' : ''; ?>"
                                             data-tgl-tanggapan="<?= !empty($t->tgl_tanggapan) ? date('d M Y, H:i', strtotime($t->tgl_tanggapan)) . ' WIB' : (!empty($t->updated_at) && $t->status !== 'Menunggu' ? date('d M Y, H:i', strtotime($t->updated_at)) . ' WIB' : ''); ?>"
                                             data-tanggapan="<?= htmlspecialchars(preg_replace('/\s+/u', ' ', html_entity_decode(trim(strip_tags(str_ireplace(['&nbsp;', '&amp;nbsp;'], ' ', $t->tanggapan ?? ''))), ENT_QUOTES, 'UTF-8'))); ?>">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border transition-all hover:scale-105 hover:shadow-xs <?= $statusColor; ?>">
                                                <?= $waTickIcon; ?>
                                                <span><?= htmlspecialchars($t->status); ?></span>
                                                <i class="bi bi-info-circle-fill text-[10px] opacity-40 hover:opacity-100 transition-opacity ml-0.5"></i>
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Waktu Masuk -->
                                    <td class="py-3.5 px-4 text-slate-500 font-medium">
                                        <?= date('d M Y', strtotime($t->created_at)); ?>
                                        <div class="text-[10px] text-slate-400 font-mono"><?= date('H:i', strtotime($t->created_at)); ?> WIB</div>
                                    </td>

                                    <!-- Aksi -->
                                    <td class="py-3.5 px-4 text-center">
                                        <button type="button" onclick="bukaModalRespon('<?= $t->kode_tiket; ?>')" 
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-orange-50 hover:bg-orange-600 text-orange-700 hover:text-white text-xs font-bold transition-all border border-orange-200 hover:border-orange-600 shadow-2xs">
                                            <i class="bi bi-reply-fill"></i> Tanggapi
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="py-12 px-4 text-center">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-xl">
                                        <i class="bi bi-inbox"></i>
                                    </div>
                                    <div class="text-sm font-bold text-slate-700">Tidak ada tiket masuk untuk Laboratorium</div>
                                    <p class="text-xs text-slate-400 mt-1">Saat ini belum ada laporan kendala lab atau filter yang dipilih tidak memiliki data.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View (shown on mobile, hidden on md: and up) -->
            <div class="block md:hidden p-3.5 space-y-3 bg-slate-50/50">
                <?php if (!empty($tickets)): ?>
                    <?php foreach ($tickets as $t): ?>
                        <?php
                            $statusColor = 'bg-amber-50 text-amber-800 border-amber-200';
                            $waTickIcon = '<i class="bi bi-check text-slate-400 font-bold text-sm"></i>';

                            if ($t->status === 'Diproses') {
                                $statusColor = 'bg-blue-50 text-blue-800 border-blue-200';
                                $waTickIcon = '<i class="bi bi-check-all text-slate-500 font-bold text-base -mr-0.5"></i>';
                            } elseif ($t->status === 'Selesai') {
                                $statusColor = 'bg-emerald-50 text-emerald-800 border-emerald-200';
                                $waTickIcon = '<i class="bi bi-check-all text-sky-500 font-bold text-base -mr-0.5"></i>';
                            } elseif ($t->status === 'Ditutup') {
                                $statusColor = 'bg-slate-100 text-slate-700 border-slate-300';
                                $waTickIcon = '<i class="bi bi-patch-check-fill text-purple-600 text-xs mr-0.5"></i>';
                            }

                            $prioClass = 'text-slate-600 bg-slate-50 border-slate-200';
                            if ($t->prioritas === 'Sedang') $prioClass = 'text-blue-700 bg-blue-50 border-blue-200';
                            elseif ($t->prioritas === 'Tinggi') $prioClass = 'text-amber-700 bg-amber-50 border-amber-200';
                            elseif ($t->prioritas === 'Darurat') $prioClass = 'text-rose-700 bg-rose-50 border-rose-200 font-bold';
                        ?>
                        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs flex flex-col space-y-3">
                            <!-- Header: Kode, Prioritas, Status -->
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="font-mono font-bold text-xs text-slate-900 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200">
                                        <?= htmlspecialchars($t->kode_tiket); ?>
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold border <?= $prioClass; ?>">
                                        <?= $t->prioritas; ?>
                                    </span>
                                </div>
                                <div class="relative inline-flex items-center cursor-pointer status-stepper-trigger select-none shrink-0"
                                     data-kode="<?= htmlspecialchars($t->kode_tiket); ?>"
                                     data-status="<?= htmlspecialchars($t->status); ?>"
                                     data-unit="<?= htmlspecialchars($t->unit_tujuan); ?>"
                                     data-subjek="<?= htmlspecialchars($t->subjek); ?>"
                                     data-created="<?= date('d M Y, H:i', strtotime($t->created_at)) . ' WIB'; ?>"
                                     data-updated="<?= !empty($t->updated_at) ? date('d M Y, H:i', strtotime($t->updated_at)) . ' WIB' : ''; ?>"
                                     data-tgl-tanggapan="<?= !empty($t->tgl_tanggapan) ? date('d M Y, H:i', strtotime($t->tgl_tanggapan)) . ' WIB' : (!empty($t->updated_at) && $t->status !== 'Menunggu' ? date('d M Y, H:i', strtotime($t->updated_at)) . ' WIB' : ''); ?>"
                                     data-tanggapan="<?= htmlspecialchars(preg_replace('/\s+/u', ' ', html_entity_decode(trim(strip_tags(str_ireplace(['&nbsp;', '&amp;nbsp;'], ' ', $t->tanggapan ?? ''))), ENT_QUOTES, 'UTF-8'))); ?>">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border <?= $statusColor; ?>">
                                        <?= $waTickIcon; ?>
                                        <span><?= htmlspecialchars($t->status); ?></span>
                                    </span>
                                </div>
                            </div>

                            <!-- Pengirim -->
                            <div class="flex items-center gap-2 p-2 rounded-xl bg-slate-50/80 border border-slate-100 text-xs">
                                <div class="w-7 h-7 rounded-lg bg-orange-100 text-orange-700 flex items-center justify-center font-bold text-xs shrink-0">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-bold text-slate-800 truncate"><?= htmlspecialchars($t->nama_dosen); ?></p>
                                    <p class="text-[10px] text-slate-400 font-mono">NIDN: <?= htmlspecialchars($t->nidn ?: '-'); ?></p>
                                </div>
                            </div>

                            <!-- Kendala & Kategori -->
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm leading-snug"><?= htmlspecialchars($t->subjek); ?></h4>
                                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md mt-1.5">
                                    <i class="bi bi-tag text-orange-500"></i> <?= htmlspecialchars($t->kategori); ?>
                                </span>
                            </div>

                            <!-- Footer: Waktu Masuk & Tombol Tanggapi -->
                            <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2">
                                <span class="text-[11px] text-slate-400 font-medium flex items-center gap-1">
                                    <i class="bi bi-clock"></i> <?= date('d M Y, H:i', strtotime($t->created_at)); ?> WIB
                                </span>
                                <button type="button" onclick="bukaModalRespon('<?= $t->kode_tiket; ?>')" 
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold transition-all shadow-xs cursor-pointer">
                                    <i class="bi bi-reply-fill"></i>
                                    <span>Tanggapi</span>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="py-10 px-4 text-center bg-white rounded-2xl border border-slate-200">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-xl">
                            <i class="bi bi-inbox"></i>
                        </div>
                        <div class="text-sm font-bold text-slate-700">Tidak ada tiket masuk untuk Laboratorium</div>
                        <p class="text-xs text-slate-400 mt-1">Saat ini belum ada laporan kendala lab atau filter yang dipilih tidak memiliki data.</p>
                    </div>
                <?php endif; ?>
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

        <!-- 4-Step Stepper Flow: 1. Tiket Masuk, 2. Sedang Diproses, 3. Tanggapan & Solusi, 4. Ditutup -->
        <div class="relative pl-7 space-y-3.5 text-xs">
            <!-- Vertical Stepper Track Line -->
            <div id="stepperLine" class="absolute left-3 top-2.5 bottom-2.5 w-0.5 bg-slate-200"></div>

            <!-- Step 1: Tiket Masuk -->
            <div class="relative">
                <span id="step1Icon" class="absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-emerald-500 text-white shadow-xs">
                    <i class="bi bi-check-lg"></i>
                </span>
                <div class="flex items-center justify-between gap-2">
                    <span class="font-bold text-slate-800 text-[11px]">1. Tiket Masuk</span>
                    <span id="step1Time" class="text-[10px] font-mono text-emerald-600 font-bold whitespace-nowrap">-</span>
                </div>
                <p class="text-[10px] text-slate-400 leading-snug mt-0.5">
                    Laporan kendala masuk ke antrean unit <span id="step1Unit" class="font-semibold text-slate-600"></span>.
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
                <div id="step3TanggapanBox" class="hidden mt-1.5 p-2 rounded-xl bg-orange-50 border border-orange-200/80 text-[10px] text-slate-700 leading-relaxed">
                    <div class="text-[9px] font-bold text-orange-800 uppercase flex items-center gap-1 mb-0.5">
                        <i class="bi bi-chat-quote-fill text-orange-600"></i>
                        <span>Respon Resmi Laboran:</span>
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
                <i class="bi bi-info-circle text-orange-500 text-[10px]"></i> Klik Tanggapi untuk perbarui status
            </span>
            <button type="button" onclick="hidePopover()" class="text-[10px] font-bold text-slate-400 hover:text-slate-600 hover:underline cursor-pointer">
                Tutup
            </button>
        </div>

    </div>

    <!-- Modal Respon & Detail Tiket Laboran -->
    <div id="modalRespon" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Backdrop -->
            <div id="modalBackdrop" onclick="tutupModalRespon()" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-200">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-orange-100 text-brand-600 flex items-center justify-center text-lg font-bold">
                            <i class="bi bi-ticket-perforated"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-900" id="modalKodeTiket">Memuat...</h3>
                            <p class="text-[11px] text-slate-400" id="modalCreatedAt">-</p>
                        </div>
                    </div>
                    <button type="button" onclick="tutupModalRespon()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200/50 flex items-center justify-center transition-all">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="px-6 py-5 max-h-[75vh] overflow-y-auto space-y-4">
                    <!-- Info Pengirim -->
                    <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200/70 grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pengirim</span>
                            <span class="font-extrabold text-slate-800" id="modalNamaDosen">-</span>
                            <span class="text-[11px] text-slate-400 font-mono block" id="modalNidn">-</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Unit Tujuan</span>
                            <span class="font-bold text-blue-700 inline-flex items-center gap-1 mt-0.5 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-200 text-[11px]" id="modalUnitTujuan">-</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Kategori</span>
                            <span class="font-semibold text-slate-700" id="modalKategori">-</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Prioritas</span>
                            <span class="font-bold text-slate-700" id="modalPrioritas">-</span>
                        </div>
                    </div>

                    <!-- Detail Kendala -->
                    <div>
                        <span class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider block mb-1">Subjek Kendala</span>
                        <div class="font-bold text-slate-900 text-sm" id="modalSubjek">-</div>
                    </div>

                    <div>
                        <span class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider block mb-1">Deskripsi Lengkap</span>
                        <div class="p-3.5 bg-slate-50/70 border border-slate-200 rounded-2xl text-xs text-slate-700 leading-relaxed font-medium overflow-x-auto space-y-1.5" id="modalDeskripsi">-</div>
                    </div>

                    <!-- Informasi Tambahan Tiket (Field Dinamis) -->
                    <div id="modalCustomFieldsSection" class="hidden">
                        <span class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider block mb-1.5">Informasi Tambahan Tiket</span>
                        <div id="modalCustomFieldsContainer" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 p-3 rounded-2xl bg-orange-50/40 border border-orange-100">
                        </div>
                    </div>

                    <!-- Lampiran File/Foto -->
                    <div id="modalLampiranSection" class="hidden">
                        <span class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider block mb-1">Lampiran Pendukung</span>
                        <div class="flex items-center gap-2">
                            <a id="modalLampiranLink" href="#" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-orange-50 hover:bg-orange-100 text-brand-700 text-xs font-bold border border-orange-200 transition-all">
                                <i class="bi bi-paperclip"></i> <span id="modalLampiranName">Lihat Lampiran</span>
                            </a>
                        </div>
                    </div>

                    <!-- Riwayat Tanggapan Terakhir (Jika ada) -->
                    <div id="modalRiwayatTanggapanSection" class="hidden p-3.5 bg-emerald-50/60 border border-emerald-200 rounded-2xl">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[10px] font-extrabold text-emerald-800 uppercase tracking-wider">Tanggapan Terakhir Laboran</span>
                            <span class="text-[10px] text-emerald-600 font-mono" id="modalTglTanggapan">-</span>
                        </div>
                        <p class="text-xs text-emerald-950 whitespace-pre-wrap" id="modalTanggapanText">-</p>
                    </div>

                    <!-- Form Tanggapan Laboran -->
                    <form id="formResponTiket" method="POST" action="<?= site_url('laboran/respon-ticketing/simpan_tanggapan'); ?>" class="pt-3 border-t border-slate-100 space-y-3">
                        <input type="hidden" name="ticket_id" id="formTicketId" value="">

                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-extrabold text-slate-700">Tahap Penanganan Tiket <span class="text-rose-500">*</span></label>
                                <span class="text-[10px] font-bold text-slate-400"><i class="bi bi-shield-check text-emerald-500"></i> Alur Maju Terkunci (Satu Arah)</span>
                            </div>
                            <input type="hidden" name="status" id="formStatusInput" value="Menunggu">
                            
                            <!-- Card Selector Stepper (One-Way) -->
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5" id="statusCardContainer">
                                <!-- Card Menunggu -->
                                <div id="cardStatus_Menunggu" onclick="selectStatusCard('Menunggu')" 
                                     class="status-choice-card cursor-pointer p-3 rounded-2xl border-2 transition-all flex flex-col items-center justify-center text-center gap-1.5 select-none relative bg-slate-50 border-slate-200">
                                    <div class="w-7 h-7 rounded-xl flex items-center justify-center text-sm font-bold icon-box bg-amber-100 text-amber-600">
                                        <i class="bi bi-hourglass-split"></i>
                                    </div>
                                    <div>
                                        <span class="text-[11px] font-extrabold block text-slate-800 leading-tight">1. Menunggu</span>
                                        <span class="text-[9px] text-slate-400 block mt-0.5 card-subtext">Antrean Masuk</span>
                                    </div>
                                    <span class="status-badge-indicator absolute -top-1.5 -right-1.5 hidden w-5 h-5 rounded-full bg-orange-600 text-white text-[10px] flex items-center justify-center shadow-xs">
                                        <i class="bi bi-check-lg"></i>
                                    </span>
                                </div>

                                <!-- Card Diproses -->
                                <div id="cardStatus_Diproses" onclick="selectStatusCard('Diproses')" 
                                     class="status-choice-card cursor-pointer p-3 rounded-2xl border-2 transition-all flex flex-col items-center justify-center text-center gap-1.5 select-none relative bg-slate-50 border-slate-200">
                                    <div class="w-7 h-7 rounded-xl flex items-center justify-center text-sm font-bold icon-box bg-blue-100 text-blue-600">
                                        <i class="bi bi-gear-wide-connected"></i>
                                    </div>
                                    <div>
                                        <span class="text-[11px] font-extrabold block text-slate-800 leading-tight">2. Diproses</span>
                                        <span class="text-[9px] text-slate-400 block mt-0.5 card-subtext">Sedang Ditangani</span>
                                    </div>
                                    <span class="status-badge-indicator absolute -top-1.5 -right-1.5 hidden w-5 h-5 rounded-full bg-blue-600 text-white text-[10px] flex items-center justify-center shadow-xs">
                                        <i class="bi bi-check-lg"></i>
                                    </span>
                                </div>

                                <!-- Card Selesai -->
                                <div id="cardStatus_Selesai" onclick="selectStatusCard('Selesai')" 
                                     class="status-choice-card cursor-pointer p-3 rounded-2xl border-2 transition-all flex flex-col items-center justify-center text-center gap-1.5 select-none relative bg-slate-50 border-slate-200">
                                    <div class="w-7 h-7 rounded-xl flex items-center justify-center text-sm font-bold icon-box bg-emerald-100 text-emerald-600">
                                        <i class="bi bi-check2-circle"></i>
                                    </div>
                                    <div>
                                        <span class="text-[11px] font-extrabold block text-slate-800 leading-tight">3. Selesai</span>
                                        <span class="text-[9px] text-slate-400 block mt-0.5 card-subtext">Kendala Teratasi</span>
                                    </div>
                                    <span class="status-badge-indicator absolute -top-1.5 -right-1.5 hidden w-5 h-5 rounded-full bg-emerald-600 text-white text-[10px] flex items-center justify-center shadow-xs">
                                        <i class="bi bi-check-lg"></i>
                                    </span>
                                </div>

                                <!-- Card Ditutup -->
                                <div id="cardStatus_Ditutup" onclick="selectStatusCard('Ditutup')" 
                                     class="status-choice-card cursor-pointer p-3 rounded-2xl border-2 transition-all flex flex-col items-center justify-center text-center gap-1.5 select-none relative bg-slate-50 border-slate-200">
                                    <div class="w-7 h-7 rounded-xl flex items-center justify-center text-sm font-bold icon-box bg-purple-100 text-purple-600">
                                        <i class="bi bi-archive-fill"></i>
                                    </div>
                                    <div>
                                        <span class="text-[11px] font-extrabold block text-slate-800 leading-tight">4. Ditutup</span>
                                        <span class="text-[9px] text-slate-400 block mt-0.5 card-subtext">Arsip Final</span>
                                    </div>
                                    <span class="status-badge-indicator absolute -top-1.5 -right-1.5 hidden w-5 h-5 rounded-full bg-purple-600 text-white text-[10px] flex items-center justify-center shadow-xs">
                                        <i class="bi bi-check-lg"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-bold text-slate-700">Tanggapan / Catatan Tindak Lanjut Laboran</label>
                                <span class="text-[10px] text-slate-400 font-semibold">(Opsional jika status Menunggu)</span>
                            </div>
                            <textarea name="tanggapan" id="formTanggapanText" rows="4" placeholder="Tuliskan konfirmasi, solusi pengerjaan, atau tindak lanjut teknis jika ada (opsional jika masih menunggu antrean)..." 
                                      class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-medium"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-2.5 pt-2">
                            <button type="button" onclick="tutupModalRespon()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-xs transition-all flex items-center gap-1.5">
                                <i class="bi bi-send-fill"></i> Kirim Respon & Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script AJAX Modal & Card Stepper Satu Arah -->
    <script>
        // Urutan tahapan status (Nilai lebih besar = tahap lebih maju)
        const STATUS_WEIGHT = {
            'Menunggu': 1,
            'Diproses': 2,
            'Selesai':  3,
            'Ditutup':  4
        };

        let currentTicketInitialStatus = 'Menunggu';

        function setupStatusCards(currentStatus) {
            currentTicketInitialStatus = currentStatus || 'Menunggu';
            const initialWeight = STATUS_WEIGHT[currentTicketInitialStatus] || 1;
            const inputHidden = document.getElementById('formStatusInput');
            const noticeLocked = document.getElementById('statusLockedNotice');

            // Default seleksi ke status sekarang
            inputHidden.value = currentTicketInitialStatus;

            let hasLockedBefore = false;

            // Iterasi 4 card status
            ['Menunggu', 'Diproses', 'Selesai', 'Ditutup'].forEach(st => {
                const card = document.getElementById('cardStatus_' + st);
                if (!card) return;

                const cardWeight = STATUS_WEIGHT[st];
                const badge = card.querySelector('.status-badge-indicator');
                const subtext = card.querySelector('.card-subtext');

                // Reset kelas dasar
                card.className = 'status-choice-card p-3 rounded-2xl border-2 transition-all flex flex-col items-center justify-center text-center gap-1.5 select-none relative';

                if (cardWeight < initialWeight) {
                    // Tahap sebelumnya: TERKUNCI & TIDAK BISA MUNDUR
                    hasLockedBefore = true;
                    card.classList.add('bg-slate-100', 'border-slate-200', 'opacity-50', 'cursor-not-allowed');
                    card.setAttribute('title', 'Tahap ini sudah dilewati dan tidak dapat dimundurkan kembali.');
                    card.onclick = function() {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tahap Telah Dilewati',
                            text: 'Status tiket tidak dapat dimundurkan kembali ke tahap sebelumnya.',
                            confirmButtonColor: '#ea580c',
                            customClass: { popup: 'rounded-3xl' }
                        });
                    };
                    if (subtext) subtext.innerHTML = '<span class="text-slate-400 font-bold">Dilewati</span>';
                    if (badge) badge.classList.add('hidden');
                } else {
                    // Tahap saat ini atau tahap maju: BISA DIPILIH
                    card.classList.add('cursor-pointer');
                    card.onclick = function() { selectStatusCard(st); };

                    if (st === currentTicketInitialStatus) {
                        // Card Aktif terpilih
                        card.classList.add('border-orange-500', 'bg-orange-50/60', 'ring-2', 'ring-orange-500/20', 'shadow-sm');
                        if (badge) badge.classList.remove('hidden');
                    } else {
                        card.classList.add('border-slate-200', 'bg-white', 'hover:border-slate-300', 'hover:bg-slate-50/60');
                        if (badge) badge.classList.add('hidden');
                    }
                }
            });

            if (noticeLocked) {
                if (hasLockedBefore) noticeLocked.classList.remove('hidden');
                else noticeLocked.classList.add('hidden');
            }
        }

        function selectStatusCard(selectedStatus) {
            const initialWeight = STATUS_WEIGHT[currentTicketInitialStatus] || 1;
            const targetWeight  = STATUS_WEIGHT[selectedStatus] || 1;

            // Larang mundur secara tegas
            if (targetWeight < initialWeight) {
                return;
            }

            document.getElementById('formStatusInput').value = selectedStatus;

            ['Menunggu', 'Diproses', 'Selesai', 'Ditutup'].forEach(st => {
                const card = document.getElementById('cardStatus_' + st);
                if (!card) return;

                const cardWeight = STATUS_WEIGHT[st];
                if (cardWeight < initialWeight) return; // Lewati yang sudah terkunci

                const badge = card.querySelector('.status-badge-indicator');

                if (st === selectedStatus) {
                    card.className = 'status-choice-card cursor-pointer p-3 rounded-2xl border-2 transition-all flex flex-col items-center justify-center text-center gap-1.5 select-none relative border-orange-500 bg-orange-50/60 ring-2 ring-orange-500/20 shadow-sm';
                    if (badge) badge.classList.remove('hidden');
                } else {
                    card.className = 'status-choice-card cursor-pointer p-3 rounded-2xl border-2 transition-all flex flex-col items-center justify-center text-center gap-1.5 select-none relative border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/60';
                    if (badge) badge.classList.add('hidden');
                }
            });
        }

        function bukaModalRespon(kodeTiket) {
            const modal = document.getElementById('modalRespon');
            modal.classList.remove('hidden');

            // Reset modal data
            document.getElementById('modalKodeTiket').innerText = 'Memuat data tiket...';
            document.getElementById('modalCreatedAt').innerText = '-';
            document.getElementById('modalNamaDosen').innerText = '-';
            document.getElementById('modalNidn').innerText = '-';
            document.getElementById('modalUnitTujuan').innerText = '-';
            document.getElementById('modalKategori').innerText = '-';
            document.getElementById('modalPrioritas').innerText = '-';
            document.getElementById('modalSubjek').innerText = '-';
            document.getElementById('modalDeskripsi').innerHTML = '-';
            document.getElementById('modalCustomFieldsSection').classList.add('hidden');
            document.getElementById('modalCustomFieldsContainer').innerHTML = '';
            document.getElementById('modalLampiranSection').classList.add('hidden');
            document.getElementById('modalRiwayatTanggapanSection').classList.add('hidden');
            document.getElementById('formTanggapanText').value = '';

            // Fetch detail tiket
            fetch('<?= site_url("laboran/respon-ticketing/detail/"); ?>' + encodeURIComponent(kodeTiket))
                .then(res => res.json())
                .then(res => {
                    if (res.status && res.data) {
                        const d = res.data;
                        document.getElementById('formTicketId').value = d.id;
                        document.getElementById('modalKodeTiket').innerText = d.kode_tiket;
                        document.getElementById('modalCreatedAt').innerText = 'Dibuat pada: ' + d.created_at_fmt;
                        document.getElementById('modalNamaDosen').innerText = d.nama_dosen;
                        document.getElementById('modalNidn').innerText = 'NIDN/ID: ' + d.nidn;
                        document.getElementById('modalUnitTujuan').innerText = d.unit_tujuan;
                        document.getElementById('modalKategori').innerText = d.kategori;
                        document.getElementById('modalPrioritas').innerText = d.prioritas;
                        document.getElementById('modalSubjek').innerText = d.subjek;
                        document.getElementById('modalDeskripsi').innerHTML = d.deskripsi || '-';

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
                        }

                        // Atur Card Stepper Satu Arah (Non-reversible)
                        setupStatusCards(d.status || 'Menunggu');

                        // Lampiran
                        if (d.lampiran) {
                            document.getElementById('modalLampiranSection').classList.remove('hidden');
                            document.getElementById('modalLampiranLink').href = d.lampiran;
                            document.getElementById('modalLampiranName').innerText = d.lampiran_name || 'Buka Lampiran';
                        }

                        // Tanggapan sebelumnya
                        if (d.tanggapan) {
                            document.getElementById('modalRiwayatTanggapanSection').classList.remove('hidden');
                            document.getElementById('modalTanggapanText').innerText = d.tanggapan;
                            document.getElementById('modalTglTanggapan').innerText = d.tgl_tanggapan || '';
                            document.getElementById('formTanggapanText').value = d.tanggapan;
                        }
                    } else {
                        alert(res.message || 'Gagal memuat detail tiket.');
                        tutupModalRespon();
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Terjadi kesalahan saat memuat data tiket.');
                    tutupModalRespon();
                });
        }

        function tutupModalRespon() {
            document.getElementById('modalRespon').classList.add('hidden');
        }

        // Close on ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                tutupModalRespon();
                hidePopover();
            }
        });

        // Floating Popover Status Stepper ala WhatsApp & Workflow
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

        function showPopoverForTrigger(el) {
            clearTimeout(popoverTimeout);

            const kode = el.getAttribute('data-kode');
            const status = el.getAttribute('data-status');
            const unit = el.getAttribute('data-unit');
            const created = el.getAttribute('data-created');
            const updated = el.getAttribute('data-updated');
            const tglTanggapan = el.getAttribute('data-tgl-tanggapan');
            const tanggapan = el.getAttribute('data-tanggapan');

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
                step2Desc.textContent = 'Menunggu giliran peninjauan oleh Laboran / unit ' + (unit || 'terkait') + '.';

                // Step 3: Pending
                step3Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-slate-100 text-slate-400 border border-slate-200';
                step3Icon.innerHTML = '<i class="bi bi-circle text-[8px]"></i>';
                step3Title.className = 'font-bold text-[11px] text-slate-400';
                step3Time.className = 'text-[10px] font-normal text-slate-400 flex items-center whitespace-nowrap';
                step3Time.innerHTML = '<i class="bi bi-dash mr-1 text-[10px]"></i> Belum Ada Solusi';
                step3Desc.textContent = 'Solusi teknis lab akan disampaikan setelah kendala dianalisis.';

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
                step2Desc.textContent = 'Laboran sedang aktif menangani dan memperbaiki kendala lab.';

                // Step 3: Preparing or with notes
                if (tanggapan && tanggapan.trim()) {
                    step3Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-blue-100 text-blue-600 border border-blue-300';
                    step3Icon.innerHTML = '<i class="bi bi-chat-dots-fill text-[9px]"></i>';
                    step3Title.className = 'font-bold text-[11px] text-blue-800';
                    step3Time.className = 'text-[10px] font-mono font-bold text-blue-600 flex items-center whitespace-nowrap';
                    step3Time.innerHTML = '<i class="bi bi-clock mr-1 text-[9px]"></i>' + (tglTanggapan || updated);
                    step3Desc.textContent = 'Laboran telah memberikan catatan tindak lanjut.';
                    step3Box.classList.remove('hidden');
                    step3Text.textContent = '"' + tanggapan.trim() + '"';
                } else {
                    step3Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200';
                    step3Icon.innerHTML = '<i class="bi bi-hourglass-split animate-pulse"></i>';
                    step3Title.className = 'font-bold text-[11px] text-slate-600';
                    step3Time.className = 'text-[10px] font-semibold text-amber-600 flex items-center whitespace-nowrap';
                    step3Time.innerHTML = '<i class="bi bi-hourglass mr-1 text-[9px]"></i> Sedang Ditangani';
                    step3Desc.textContent = 'Solusi dan perbaikan sedang dikerjakan di laboratorium.';
                }

                // Step 4: Pending
                step4Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold bg-slate-100 text-slate-400 border border-slate-200';
                step4Icon.innerHTML = '<i class="bi bi-lock text-[10px]"></i>';
                step4Title.className = 'font-bold text-[11px] text-slate-400';
                step4Time.className = 'text-[10px] font-normal text-slate-400 flex items-center whitespace-nowrap';
                step4Time.innerHTML = '<i class="bi bi-dash mr-1 text-[10px]"></i> Belum Ditutup';
                step4Desc.textContent = 'Tiket masih dalam proses pengerjaan teknis.';

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
                step2Desc.textContent = 'Kendala laboratorium telah diperiksa dan diselesaikan.';

                // Step 3: Complete with WhatsApp Blue Double Check & Solusi
                step3Icon.className = 'absolute -left-7 top-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[11px] font-black bg-sky-500 text-white shadow-xs ring-2 ring-sky-200';
                step3Icon.innerHTML = '<i class="bi bi-check-all"></i>';
                step3Title.className = 'font-bold text-[11px] text-sky-900';
                step3Time.className = 'text-[10px] font-mono font-bold text-sky-700 flex items-center whitespace-nowrap';
                step3Time.innerHTML = '<i class="bi bi-clock-fill mr-1 text-[9px]"></i>' + (tglTanggapan || updated);
                step3Desc.textContent = 'Solusi resmi telah diberikan oleh Laboran.';

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
                step4Desc.textContent = 'Perangkat/fasilitas telah pulih, menunggu konfirmasi penutupan.';

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
            const rect = el.getBoundingClientRect();
            const actualWidth = popover.offsetWidth || 340;
            const actualHeight = popover.offsetHeight || 320;

            let top = rect.top - actualHeight - 12;
            if (top < 10) {
                top = rect.bottom + 12;
            }

            let left = (rect.left + rect.right) / 2 - (actualWidth / 2);

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
                showPopoverForTrigger(this);
            });

            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
                if (popover && !popover.classList.contains('hidden') && popover.classList.contains('opacity-100')) {
                    hidePopover();
                } else {
                    showPopoverForTrigger(this);
                }
            });

            trigger.addEventListener('mouseleave', function() {
                popoverTimeout = setTimeout(() => {
                    hidePopover();
                }, 150);
            });
        });

        // Close popover when clicked outside
        document.addEventListener('click', function(e) {
            if (popover && !popover.contains(e.target) && !e.target.closest('.status-stepper-trigger')) {
                hidePopover();
            }
        });
    </script>
</body>
</html>
