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
    <header class="glass-header sticky top-0 z-30 px-6 py-4 pl-16">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-orange-100 text-brand-600 flex items-center justify-center font-bold text-xl shadow-xs border border-orange-200/50">
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">Inbox Respon Tiket Laboratorium</h1>
                        <span class="px-2 py-0.5 rounded-md bg-blue-100 text-blue-800 text-[10px] font-extrabold tracking-wider uppercase border border-blue-200">Panel Laboran</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Tinjau & respon laporan kendala khusus unit Laboratorium & Sarana Prasarana.</p>
                </div>
            </div>

            <!-- Quick Action Links -->
            <div class="flex items-center gap-2.5">
                <a href="<?= site_url('laboran/ticketing/input'); ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-xs transition-all">
                    <i class="bi bi-plus-circle-fill"></i> Buat Tiket Baru
                </a>
                <a href="<?= site_url('laboran/ticketing/riwayat'); ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all border border-slate-200">
                    <i class="bi bi-clock-history"></i> Riwayat Tiket Saya
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
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto pb-2 md:pb-0">
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
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all <?= $isActive ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600' ?>">
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

        <!-- Table of Incoming Lab Tickets -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
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
                                    // Status Badge Colors
                                    $stClass = 'bg-slate-100 text-slate-600 border-slate-200';
                                    if ($t->status === 'Menunggu') $stClass = 'bg-amber-50 text-amber-700 border-amber-200';
                                    elseif ($t->status === 'Diproses') $stClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                    elseif ($t->status === 'Selesai') $stClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                    elseif ($t->status === 'Ditutup') $stClass = 'bg-slate-100 text-slate-500 border-slate-200';

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

                                    <!-- Status -->
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-1 rounded-lg text-[11px] font-extrabold border <?= $stClass; ?>">
                                            <?= $t->status; ?>
                                        </span>
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
        </div>
    </main>

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
                        <div class="p-3.5 bg-slate-50/70 border border-slate-200 rounded-2xl text-xs text-slate-700 leading-relaxed whitespace-pre-wrap font-medium" id="modalDeskripsi">-</div>
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
                            <label class="block text-xs font-bold text-slate-700 mb-1">Perbarui Status Pengerjaan <span class="text-rose-500">*</span></label>
                            <select name="status" id="formStatusSelect" class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                                <option value="Menunggu">Menunggu</option>
                                <option value="Diproses">Diproses (Sedang Ditangani Laboran)</option>
                                <option value="Selesai">Selesai (Kendala Telah Diatasi)</option>
                                <option value="Ditutup">Ditutup</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggapan / Catatan Tindak Lanjut Laboran <span class="text-rose-500">*</span></label>
                            <textarea name="tanggapan" id="formTanggapanText" rows="4" required placeholder="Tuliskan konfirmasi, solusi pengerjaan, atau tindak lanjut teknis di lab..." 
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

    <!-- Script AJAX Modal -->
    <script>
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
            document.getElementById('modalDeskripsi').innerText = '-';
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
                        document.getElementById('modalDeskripsi').innerText = d.deskripsi;

                        // Set status select value
                        const stSelect = document.getElementById('formStatusSelect');
                        if (stSelect) {
                            stSelect.value = d.status || 'Diproses';
                        }

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
            }
        });
    </script>
</body>
</html>
