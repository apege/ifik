<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? '[SIMULASI] Ticketing Admin LAA'; ?> - IFIK</title>

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

    <!-- Auto Role-Aware Curved Animated Sidebar for Dosen -->
    <?php $this->load->view('partials/dosen_sidebar'); ?>

    <!-- Header Navigation -->
    <header class="glass-header sticky top-0 z-30 px-6 py-4 pl-16">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-orange-100 text-brand-600 flex items-center justify-center font-bold text-xl shadow-xs border border-orange-200/50">
                    <i class="bi bi-ticket-detailed-fill"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">Inbox Tiket Masuk Unit LAA</h1>
                        <span class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 text-[10px] font-extrabold tracking-wider uppercase border border-amber-200">Simulasi</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Tinjau laporan kendala dari dosen wali & akademik khusus untuk Layanan Akademik.</p>
                </div>
            </div>

            <!-- Quick Action Links -->
            <div class="flex items-center gap-2.5">
                <a href="<?= site_url('dosen/ticketing/input'); ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-xs transition-all">
                    <i class="bi bi-plus-circle-fill"></i> Test Kirim Tiket Dosen
                </a>
                <a href="<?= site_url('dosen/ticketing/riwayat'); ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all border border-slate-200">
                    <i class="bi bi-person-badge"></i> Cek Riwayat Dosen
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
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl shadow-xs mb-6 flex items-center justify-between text-xs font-semibold">
                <div class="flex items-center gap-2.5">
                    <i class="bi bi-exclamation-triangle-fill text-rose-600 text-base"></i>
                    <span><?= $this->session->flashdata('error'); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Stats Overview Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Total -->
            <a href="<?= site_url('adminlayanan/ticketing?status=all'); ?>" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-orange-300 transition-all">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500">Total Tiket Masuk</span>
                    <span class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-sm font-bold"><i class="bi bi-collection-fill"></i></span>
                </div>
                <div class="text-2xl font-black text-slate-800"><?= $stats['total']; ?></div>
                <p class="text-[11px] text-slate-400 mt-1">Semua tiket ke unit LAA</p>
            </a>

            <!-- Menunggu -->
            <a href="<?= site_url('adminlayanan/ticketing?status=Menunggu'); ?>" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-amber-300 transition-all">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-amber-700">Menunggu Respon</span>
                    <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-sm font-bold"><i class="bi bi-hourglass-split"></i></span>
                </div>
                <div class="text-2xl font-black text-amber-600"><?= $stats['menunggu']; ?></div>
                <p class="text-[11px] text-slate-400 mt-1">Belum ditanggapi</p>
            </a>

            <!-- Diproses -->
            <a href="<?= site_url('adminlayanan/ticketing?status=Diproses'); ?>" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-blue-300 transition-all">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-blue-700">Sedang Diproses</span>
                    <span class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-bold"><i class="bi bi-gear-wide-connected"></i></span>
                </div>
                <div class="text-2xl font-black text-blue-600"><?= $stats['diproses']; ?></div>
                <p class="text-[11px] text-slate-400 mt-1">Dalam penanganan staf</p>
            </a>

            <!-- Selesai -->
            <a href="<?= site_url('adminlayanan/ticketing?status=Selesai'); ?>" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-emerald-300 transition-all">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-emerald-700">Selesai / Teratasi</span>
                    <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm font-bold"><i class="bi bi-check2-circle"></i></span>
                </div>
                <div class="text-2xl font-black text-emerald-600"><?= $stats['selesai']; ?></div>
                <p class="text-[11px] text-slate-400 mt-1">Solusi telah dikirim</p>
            </a>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
            
            <!-- Status Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto pb-2 md:pb-0">
                <?php
                $tabs = [
                    'all'      => 'Semua',
                    'Menunggu' => 'Menunggu',
                    'Diproses' => 'Diproses',
                    'Selesai'  => 'Selesai',
                    'Ditutup'  => 'Ditutup'
                ];
                foreach ($tabs as $key => $label):
                    $isActive = ($filterStatus === $key);
                ?>
                    <a href="<?= site_url('adminlayanan/ticketing?status=' . $key . (!empty($search) ? '&q=' . urlencode($search) : '')); ?>"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap <?= $isActive ? 'bg-orange-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'; ?>">
                        <?= $label; ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Search Form -->
            <form action="<?= site_url('adminlayanan/ticketing'); ?>" method="GET" class="w-full md:w-72 relative">
                <input type="hidden" name="status" value="<?= htmlspecialchars($filterStatus); ?>">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    <i class="bi bi-search text-xs"></i>
                </span>
                <input type="text" name="q" value="<?= htmlspecialchars($search); ?>"
                       placeholder="Cari kode, dosen, subjek..."
                       class="w-full pl-9 pr-8 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-hidden focus:border-orange-500 focus:bg-white transition-all">
                <?php if (!empty($search)): ?>
                    <a href="<?= site_url('adminlayanan/ticketing?status=' . $filterStatus); ?>" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <i class="bi bi-x-circle-fill text-xs"></i>
                    </a>
                <?php endif; ?>
            </form>

        </div>

        <!-- Tickets Table Container -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-bold text-[10px]">
                            <th class="py-3.5 px-5">Kode & Waktu</th>
                            <th class="py-3.5 px-5">Dosen Pengirim</th>
                            <th class="py-3.5 px-5">Kategori Kendala</th>
                            <th class="py-3.5 px-5">Subjek & Rincian</th>
                            <th class="py-3.5 px-5 text-center">Prioritas</th>
                            <th class="py-3.5 px-5 text-center">Status</th>
                            <th class="py-3.5 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        <?php if (empty($tickets)): ?>
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-400 text-xl">
                                        <i class="bi bi-inbox"></i>
                                    </div>
                                    <p class="font-bold text-slate-700 text-sm">Belum Ada Tiket Masuk untuk Unit LAA</p>
                                    <p class="text-xs text-slate-400 mt-1">Tiket yang dikirim dosen dengan tujuan Layanan Akademik (LAA) akan muncul di sini.</p>
                                    <a href="<?= site_url('dosen/ticketing/input'); ?>" target="_blank" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs shadow-xs transition-all">
                                        <i class="bi bi-send-fill"></i> Buat Tiket Percobaan
                                    </a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($tickets as $t): 
                                $prioBadge = match($t->prioritas) {
                                    'Rendah'  => 'bg-slate-100 text-slate-700 border-slate-200',
                                    'Sedang'  => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'Tinggi'  => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'Darurat' => 'bg-rose-50 text-rose-700 border-rose-200 font-extrabold',
                                    default   => 'bg-slate-100 text-slate-700 border-slate-200'
                                };

                                $statusBadge = match($t->status) {
                                    'Menunggu' => 'bg-amber-50 text-amber-800 border-amber-200/80',
                                    'Diproses' => 'bg-blue-50 text-blue-800 border-blue-200/80',
                                    'Selesai'  => 'bg-emerald-50 text-emerald-800 border-emerald-200/80',
                                    'Ditutup'  => 'bg-slate-100 text-slate-600 border-slate-200/80',
                                    default    => 'bg-slate-100 text-slate-700 border-slate-200'
                                };

                                $waTickIcon = match($t->status) {
                                    'Menunggu' => '<i class="bi bi-check text-slate-400 font-extrabold text-sm" title="Terkirim ke Server (1 Ceklis)"></i>',
                                    'Diproses' => '<i class="bi bi-check-all text-slate-500 font-black text-base" title="Diterima & Ditinjau Unit (2 Ceklis Abu-abu)"></i>',
                                    'Selesai'  => '<i class="bi bi-check-all text-sky-500 font-black text-base" title="Selesai & Ditanggapi (2 Ceklis Biru WhatsApp)"></i>',
                                    'Ditutup'  => '<i class="bi bi-slash-circle text-slate-400 text-xs" title="Ditutup"></i>',
                                    default    => ''
                                };
                            ?>
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <!-- Kode & Waktu -->
                                    <td class="py-4 px-5">
                                        <span class="font-mono font-bold text-slate-800 text-xs block"><?= htmlspecialchars($t->kode_tiket); ?></span>
                                        <span class="text-[10px] text-slate-400 mt-0.5 block">
                                            <i class="bi bi-clock mr-1"></i><?= date('d M Y, H:i', strtotime($t->created_at)); ?>
                                        </span>
                                    </td>

                                    <!-- Dosen -->
                                    <td class="py-4 px-5">
                                        <p class="font-bold text-slate-800 text-xs leading-snug"><?= htmlspecialchars($t->nama_dosen); ?></p>
                                        <p class="text-[10px] text-slate-400 font-mono mt-0.5">NIDN: <?= htmlspecialchars($t->nidn ?: '-'); ?></p>
                                    </td>

                                    <!-- Kategori -->
                                    <td class="py-4 px-5 max-w-xs">
                                        <span class="inline-block px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold text-[11px] border border-slate-200 leading-relaxed">
                                            <?= htmlspecialchars($t->kategori); ?>
                                        </span>
                                    </td>

                                    <!-- Subjek -->
                                    <td class="py-4 px-5 max-w-xs">
                                        <p class="font-bold text-slate-800 text-xs truncate"><?= htmlspecialchars($t->subjek); ?></p>
                                        <p class="text-[11px] text-slate-400 truncate mt-0.5"><?= htmlspecialchars(mb_substr(strip_tags($t->deskripsi), 0, 65)); ?>...</p>
                                    </td>

                                    <!-- Prioritas -->
                                    <td class="py-4 px-5 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold border <?= $prioBadge; ?>">
                                            <?= htmlspecialchars($t->prioritas); ?>
                                        </span>
                                    </td>

                                    <!-- Status -->
                                    <td class="py-4 px-5 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border <?= $statusBadge; ?>">
                                            <?= $waTickIcon; ?>
                                            <span><?= htmlspecialchars($t->status); ?></span>
                                        </span>
                                    </td>

                                    <!-- Aksi -->
                                    <td class="py-4 px-5 text-right whitespace-nowrap">
                                        <button type="button" onclick="openDetailModal('<?= $t->kode_tiket; ?>')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-orange-600 hover:text-white text-slate-700 text-xs font-bold transition-all shadow-2xs">
                                            <i class="bi bi-chat-left-text-fill text-[11px]"></i> Tanggapi
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

    <!-- Interactive Detail & Response Modal -->
    <div id="modalDetail" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-3xl rounded-3xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[90vh] animate-in fade-in zoom-in duration-200">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span id="mStatusBadge" class="px-2.5 py-1 rounded-full text-xs font-bold border bg-amber-50 text-amber-700 border-amber-200">Menunggu</span>
                    <span id="mKode" class="font-mono font-bold text-slate-800 text-sm">TIK-XXXXXXXX</span>
                    <span id="mPrioritasBadge" class="px-2 py-0.5 rounded-md text-[10px] font-bold border bg-slate-100 text-slate-700">Sedang</span>
                </div>
                <button type="button" onclick="closeModal()" class="w-8 h-8 rounded-full hover:bg-slate-200 text-slate-500 flex items-center justify-center transition-colors">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-6 overflow-y-auto space-y-6 flex-1 text-xs">
                
                <!-- Dosen & Waktu Bar -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Dosen Pelapor</span>
                        <span id="mNamaDosen" class="font-bold text-slate-800 text-sm">Nama Dosen</span>
                        <span id="mNidn" class="text-slate-400 ml-1.5 font-mono">NIDN: -</span>
                    </div>
                    <div class="text-left sm:text-right">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Waktu Diajukan</span>
                        <span id="mWaktu" class="text-slate-600 font-semibold">-</span>
                    </div>
                </div>

                <!-- Kategori & Subjek -->
                <div>
                    <span class="text-[10px] text-slate-400 font-bold uppercase block mb-1">Kategori Kendala</span>
                    <p id="mKategori" class="font-bold text-slate-800 text-xs bg-orange-50/70 text-orange-900 border border-orange-200/80 px-3 py-1.5 rounded-xl inline-block"></p>
                </div>

                <div>
                    <span class="text-[10px] text-slate-400 font-bold uppercase block mb-1">Subjek Kendala</span>
                    <h3 id="mSubjek" class="font-black text-slate-900 text-base leading-snug">Subjek</h3>
                </div>

                <!-- Deskripsi Lengkap -->
                <div>
                    <span class="text-[10px] text-slate-400 font-bold uppercase block mb-1.5">Rincian / Deskripsi Lengkap</span>
                    <div id="mDeskripsi" class="p-4 rounded-2xl bg-slate-50/60 border border-slate-200 text-slate-700 leading-relaxed max-h-60 overflow-y-auto prose prose-sm"></div>
                </div>

                <!-- Lampiran -->
                <div id="mLampiranContainer" class="hidden">
                    <span class="text-[10px] text-slate-400 font-bold uppercase block mb-1.5">File Bukti Lampiran</span>
                    <a id="mLampiranLink" href="#" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 font-bold hover:bg-blue-100 transition-all text-xs">
                        <i class="bi bi-paperclip"></i>
                        <span id="mLampiranName">Buka File Lampiran</span>
                    </a>
                </div>

                <hr class="border-slate-200">

                <!-- Form Tanggapan Admin LAA -->
                <form id="formTanggapan" action="<?= site_url('adminlayanan/ticketing/simpan_tanggapan'); ?>" method="POST" class="space-y-4">
                    <input type="hidden" id="formIdTiket" name="id_tiket" value="">

                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-2">
                            Ubah Status Tiket <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            <label class="flex items-center justify-center gap-2 p-2.5 rounded-xl border border-slate-200 cursor-pointer hover:border-amber-400 has-checked:border-amber-500 has-checked:bg-amber-50/50 has-checked:font-bold text-center">
                                <input type="radio" name="status" value="Menunggu" class="text-amber-600 focus:ring-amber-500">
                                <span>Menunggu</span>
                            </label>
                            <label class="flex items-center justify-center gap-2 p-2.5 rounded-xl border border-slate-200 cursor-pointer hover:border-blue-400 has-checked:border-blue-500 has-checked:bg-blue-50/50 has-checked:font-bold text-center">
                                <input type="radio" name="status" value="Diproses" class="text-blue-600 focus:ring-blue-500">
                                <span>Diproses</span>
                            </label>
                            <label class="flex items-center justify-center gap-2 p-2.5 rounded-xl border border-slate-200 cursor-pointer hover:border-emerald-400 has-checked:border-emerald-500 has-checked:bg-emerald-50/50 has-checked:font-bold text-center">
                                <input type="radio" name="status" value="Selesai" class="text-emerald-600 focus:ring-emerald-500">
                                <span>Selesai</span>
                            </label>
                            <label class="flex items-center justify-center gap-2 p-2.5 rounded-xl border border-slate-200 cursor-pointer hover:border-purple-400 has-checked:border-purple-500 has-checked:bg-purple-50/50 has-checked:font-bold text-center">
                                <input type="radio" name="status" value="Ditutup" class="text-purple-600 focus:ring-purple-500">
                                <span>Ditutup</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="formTanggapanText" class="block text-xs font-bold text-slate-800 mb-1.5">
                            Tanggapan / Solusi Admin LAA <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="formTanggapanText" name="tanggapan" rows="4" required
                                  placeholder="Tuliskan respon, verifikasi, atau arahan penyelesaian untuk dosen pengirim..."
                                  class="w-full p-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white text-xs font-medium text-slate-800 placeholder-slate-400 transition-all outline-hidden"></textarea>
                        <p id="mTglTanggapan" class="text-[11px] text-slate-400 mt-1 italic"></p>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-2">
                        <button type="button" onclick="closeModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold transition-all text-xs">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold transition-all text-xs shadow-xs flex items-center gap-1.5">
                            <i class="bi bi-send-check-fill"></i> Simpan & Kirim Tanggapan
                        </button>
                    </div>
                </form>

            </div>

        </div>
    </div>

    <!-- Script AJAX Modal -->
    <script>
        function openDetailModal(kodeTiket) {
            fetch('<?= site_url("adminlayanan/ticketing/detail/"); ?>' + encodeURIComponent(kodeTiket))
                .then(res => res.json())
                .then(res => {
                    if (res.status && res.data) {
                        const d = res.data;
                        document.getElementById('formIdTiket').value = d.id;
                        document.getElementById('mKode').textContent = d.kode_tiket;
                        document.getElementById('mNamaDosen').textContent = d.nama_dosen;
                        document.getElementById('mNidn').textContent = 'NIDN: ' + (d.nidn || '-');
                        document.getElementById('mWaktu').textContent = d.created_at;
                        document.getElementById('mKategori').textContent = d.kategori;
                        document.getElementById('mSubjek').textContent = d.subjek;
                        document.getElementById('mDeskripsi').innerHTML = d.deskripsi;
                        document.getElementById('mStatusBadge').textContent = d.status;
                        document.getElementById('mPrioritasBadge').textContent = d.prioritas;

                        // Radio Status Selection
                        const radios = document.querySelectorAll('input[name="status"]');
                        radios.forEach(r => {
                            r.checked = (r.value === d.status);
                        });

                        // Tanggapan
                        document.getElementById('formTanggapanText').value = d.tanggapan || '';
                        if (d.tgl_tanggapan) {
                            document.getElementById('mTglTanggapan').textContent = 'Terkahir ditanggapi pada: ' + d.tgl_tanggapan;
                        } else {
                            document.getElementById('mTglTanggapan').textContent = 'Belum pernah ditanggapi.';
                        }

                        // Lampiran
                        const lampContainer = document.getElementById('mLampiranContainer');
                        if (d.lampiran && d.lampiran_url) {
                            lampContainer.classList.remove('hidden');
                            document.getElementById('mLampiranLink').href = d.lampiran_url;
                            document.getElementById('mLampiranName').textContent = 'Buka Lampiran: ' + d.lampiran;
                        } else {
                            lampContainer.classList.add('hidden');
                        }

                        document.getElementById('modalDetail').classList.remove('hidden');
                    } else {
                        alert(res.message || 'Gagal memuat detail tiket.');
                    }
                })
                .catch(err => {
                    alert('Gagal menghubungi server untuk mengambil detail tiket.');
                });
        }

        function closeModal() {
            document.getElementById('modalDetail').classList.add('hidden');
        }

        // Close on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
        });
    </script>
</body>
</html>
