<?php
/**
 * Meja Bantuan & Kelola Tiket Pengaduan Layanan Akademik - Admin LAA
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> - IFIK Telkom University</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #fbf7f1; color: #1e293b; }
        .glass-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(16px); border: 1px solid #e2e8f0; border-radius: 1.25rem; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased pb-16">

    <!-- Dedicated Admin LAA Sidebar Component -->
    <?php $this->load->view('admin_layanan/sidebar'); ?>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">

        <!-- Flash Messages -->
        <?php if ($this->session->flashdata('success')): ?>
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <i class="bi bi-check-circle-fill text-emerald-600 text-lg"></i>
                    <span class="font-semibold"><?= htmlspecialchars($this->session->flashdata('success')); ?></span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900"><i class="bi bi-x-lg"></i></button>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill text-rose-600 text-lg"></i>
                    <span class="font-semibold"><?= htmlspecialchars($this->session->flashdata('error')); ?></span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900"><i class="bi bi-x-lg"></i></button>
            </div>
        <?php endif; ?>

        <!-- Sub Title Bar & Tab Header -->
        <div class="glass-card p-4 sm:p-6 mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-sm">
            <div class="flex items-start sm:items-center gap-3.5">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center text-xl sm:text-2xl font-bold shrink-0">
                    <i class="bi bi-ticket-perforated-fill"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Ticketing LAA</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola pembuatan tiket layanan, approval permohonan, dan riwayat ticketing mahasiswa.</p>
                </div>
            </div>

            <!-- Module Navigation Tabs -->
            <div class="flex flex-wrap sm:flex-nowrap items-center gap-1.5 bg-slate-100 p-1.5 rounded-2xl border border-slate-200 w-full md:w-auto overflow-x-auto">
                <a href="<?= site_url('adminlayanan/ticketing_input'); ?>" class="flex-1 sm:flex-initial justify-center px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 <?= $active_tab === 'input' ? 'bg-orange-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200' ?>">
                    <i class="bi bi-plus-circle-fill"></i> Input Ticketing
                </a>
                <a href="<?= site_url('adminlayanan/ticketing_approval'); ?>" class="flex-1 sm:flex-initial justify-center px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 <?= $active_tab === 'approval' ? 'bg-orange-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200' ?>">
                    <i class="bi bi-shield-check"></i> Approval Ticketing
                </a>
                <a href="<?= site_url('adminlayanan/ticketing_riwayat'); ?>" class="flex-1 sm:flex-initial justify-center px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 <?= $active_tab === 'riwayat' ? 'bg-orange-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200' ?>">
                    <i class="bi bi-clock-history"></i> Riwayat Ticketing
                </a>
            </div>
        </div>

        <!-- TAB CONTENT: INPUT TICKETING -->
        <?php if ($active_tab === 'input'): ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Form Input Tiket Baru -->
                <div class="lg:col-span-2 glass-card p-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <i class="bi bi-pencil-square text-orange-500"></i> Form Buat Tiket Layanan Baru
                    </h3>
                    <form action="<?= site_url('adminlayanan/simpan_ticket'); ?>" method="POST" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">NIM / NIP Pemohon *</label>
                                <input type="text" name="nim_nip" required placeholder="Contoh: 1601200331" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Lengkap *</label>
                                <input type="text" name="nama" required placeholder="Contoh: Maharani Mutiara" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email Pemohon</label>
                                <input type="email" name="email" placeholder="maharani@student.telkomuniversity.ac.id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kategori Layanan *</label>
                                <select name="kategori" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition">
                                    <option value="Pengajuan Berkas TA">Pengajuan Berkas TA</option>
                                    <option value="Reset Tahapan Preview">Reset Tahapan Preview</option>
                                    <option value="Bebas Tanggungan Lab">Bebas Tanggungan Lab</option>
                                    <option value="Surat Keterangan LAA">Surat Keterangan LAA</option>
                                    <option value="Layanan Umum">Layanan Umum</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Perihal / Subjek Tiket *</label>
                            <input type="text" name="perihal" required placeholder="Perihal permohonan layanan..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Prioritas Tiket</label>
                            <select name="prioritas" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition">
                                <option value="Normal">Normal</option>
                                <option value="Tinggi">Tinggi</option>
                                <option value="Urgent">Urgent (Segera)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Deskripsi & Rincian Kasus</label>
                            <textarea name="deskripsi" rows="4" placeholder="Jelaskan detail permohonan atau masalah..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition"></textarea>
                        </div>

                        <div class="pt-2 flex justify-end">
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm shadow-md transition-all flex items-center gap-2">
                                <i class="bi bi-send-fill"></i> Submit Tiket Layanan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Info Sidebar Box -->
                <div class="glass-card p-6 h-fit">
                    <h4 class="font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <i class="bi bi-info-circle-fill text-orange-500"></i> Ketentuan Input Tiket
                    </h4>
                    <ul class="text-xs text-slate-600 space-y-2.5 list-disc pl-4">
                        <li>Semua tiket yang diinput akan masuk ke antrean **Approval Ticketing**.</li>
                        <li>Pastikan NIM/NIP pemohon telah sesuai dengan database mahasiswa IFIK.</li>
                        <li>Gunakan kualifikasi prioritas **Urgent** jika permohonan mendesak terkait sidang/yudisium.</li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <!-- TAB CONTENT: APPROVAL & RIWAYAT TICKETING -->
        <?php if ($active_tab === 'approval' || $active_tab === 'riwayat'): ?>
            <div class="glass-card p-4 sm:p-6">
                <!-- Search Bar & Filters -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="bi bi-card-checklist text-orange-500"></i>
                        <?= $active_tab === 'approval' ? 'Daftar Tiket Membutuhkan Verification/Approval' : 'Riwayat & Log Tiket Terproses' ?>
                    </h3>

                    <form action="" method="GET" class="w-full sm:w-80">
                        <div class="relative">
                            <i class="bi bi-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                            <input type="text" id="ticketingSearchInput" name="q" value="<?= htmlspecialchars($search); ?>" placeholder="Cari No. Tiket, NIM, Nama..." class="w-full pl-9 pr-8 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all shadow-xs font-medium text-slate-800 placeholder:text-slate-400">
                            <button type="button" id="ticketingClearBtn" onclick="clearTicketingSearch()" class="<?= empty($search) ? 'opacity-0 scale-75 pointer-events-none' : 'opacity-100 scale-100'; ?> absolute right-2.5 top-2 text-slate-400 hover:text-rose-600 text-xs transition-all duration-200 cursor-pointer">
                                <i class="bi bi-x-circle-fill"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <script>
                function clearTicketingSearch() {
                    const input = document.getElementById('ticketingSearchInput');
                    const btnClear = document.getElementById('ticketingClearBtn');
                    if (input) {
                        input.value = '';
                        input.focus();
                    }
                    if (btnClear) {
                        btnClear.classList.remove('opacity-100', 'scale-100');
                        btnClear.classList.add('opacity-0', 'scale-75', 'pointer-events-none');
                    }
                    filterTicketingTable('');
                }

                function filterTicketingTable(query) {
                    const q = query.toLowerCase().trim();

                    // Filter desktop rows
                    document.querySelectorAll('.tkt-row').forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = (!q || text.includes(q)) ? '' : 'none';
                    });

                    // Filter mobile cards
                    document.querySelectorAll('.tkt-card').forEach(card => {
                        const text = card.textContent.toLowerCase();
                        card.style.display = (!q || text.includes(q)) ? '' : 'none';
                    });

                    // Desktop no-results
                    const visibleRows = document.querySelectorAll('.tkt-row:not([style*="none"])');
                    const noResDesk = document.getElementById('noResultsDesktopTkt');
                    if (noResDesk) noResDesk.style.display = (visibleRows.length === 0 && q) ? '' : 'none';

                    // Mobile no-results
                    const visibleCards = document.querySelectorAll('.tkt-card:not([style*="none"])');
                    const noResMobile = document.getElementById('noResultsMobileTkt');
                    if (noResMobile) noResMobile.style.display = (visibleCards.length === 0 && q) ? '' : 'none';
                }

                document.addEventListener('DOMContentLoaded', () => {
                    const input = document.getElementById('ticketingSearchInput');
                    const btnClear = document.getElementById('ticketingClearBtn');
                    if (input) {
                        input.addEventListener('input', function() {
                            const q = this.value.trim();
                            if (btnClear) {
                                if (q.length > 0) {
                                    btnClear.classList.remove('opacity-0', 'scale-75', 'pointer-events-none');
                                    btnClear.classList.add('opacity-100', 'scale-100');
                                } else {
                                    btnClear.classList.remove('opacity-100', 'scale-100');
                                    btnClear.classList.add('opacity-0', 'scale-75', 'pointer-events-none');
                                }
                            }
                            filterTicketingTable(q);
                        });
                    }
                });
                </script>

                <!-- ======= DESKTOP TABLE (md+) ======= -->
                <div class="hidden md:block overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full min-w-[780px] text-left text-xs text-slate-600">
                        <thead class="bg-slate-100 text-slate-700 font-bold uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="p-3">No</th>
                                <th class="p-3">No. Tiket</th>
                                <th class="p-3">NIM / Nama Pemohon</th>
                                <th class="p-3">Kategori</th>
                                <th class="p-3">Perihal</th>
                                <th class="p-3">Prioritas</th>
                                <th class="p-3">Status</th>
                                <th class="p-3 text-center">Aksi / Respon</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (!empty($tickets)): ?>
                                <?php foreach ($tickets as $idx => $t): ?>
                                    <tr class="tkt-row hover:bg-orange-50/50 transition">
                                        <td class="p-3 font-semibold text-slate-500"><?= $idx + 1 ?></td>
                                        <td class="p-3 font-mono font-bold text-orange-600"><?= htmlspecialchars($t['ticket_number']); ?></td>
                                        <td class="p-3 font-bold text-slate-900">
                                            <?= htmlspecialchars($t['nama']); ?>
                                            <div class="text-[10px] text-slate-400 font-mono"><?= htmlspecialchars($t['nim_nip']); ?></div>
                                        </td>
                                        <td class="p-3">
                                            <?php if (!empty($t['unit_terkait'])): ?>
                                                <span class="px-2 py-0.5 rounded bg-orange-50 text-orange-700 border border-orange-100 font-semibold text-[10px] block w-fit mb-1"><?= htmlspecialchars($t['unit_terkait']); ?></span>
                                            <?php endif; ?>
                                            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold text-[10px]"><?= htmlspecialchars($t['kategori']); ?></span>
                                        </td>
                                        <td class="p-3 max-w-xs truncate" title="<?= htmlspecialchars($t['deskripsi'] ?? ''); ?>"><?= htmlspecialchars($t['perihal']); ?></td>
                                        <td class="p-3">
                                            <?php if ($t['prioritas'] === 'Urgent'): ?>
                                                <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 font-bold text-[10px]">Urgent</span>
                                            <?php elseif ($t['prioritas'] === 'Tinggi'): ?>
                                                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold text-[10px]">Tinggi</span>
                                            <?php else: ?>
                                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-medium text-[10px]">Normal</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="p-3">
                                            <?php if ($t['status'] === 'Approved' || $t['status'] === 'Selesai'): ?>
                                                <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]"><i class="bi bi-check-circle-fill mr-1"></i><?= $t['status']; ?></span>
                                            <?php elseif ($t['status'] === 'Rejected'): ?>
                                                <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 font-bold text-[10px]"><i class="bi bi-x-circle-fill mr-1"></i>Rejected</span>
                                            <?php else: ?>
                                                <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-bold text-[10px]"><i class="bi bi-clock-fill mr-1"></i>Pending Approval</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="p-3 text-center">
                                            <?php if ($active_tab === 'approval' && in_array($t['status'], ['Inputted', 'Pending'])): ?>
                                                <form action="<?= site_url('adminlayanan/update_ticket_status'); ?>" method="POST" class="inline-flex gap-1">
                                                    <input type="hidden" name="id" value="<?= $t['id']; ?>">
                                                    <button type="submit" name="status" value="Approved" class="px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px]">Approve</button>
                                                    <button type="submit" name="status" value="Rejected" class="px-3 py-1 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold text-[11px]">Reject</button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-slate-400 italic text-[11px]"><?= !empty($t['catatan']) ? htmlspecialchars($t['catatan']) : 'Selesai' ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="p-8 text-center text-slate-400 italic">Belum ada data tiket layanan yang ditemukan.</td>
                                </tr>
                            <?php endif; ?>
                            <tr id="noResultsDesktopTkt" style="display:none">
                                <td colspan="8" class="p-8 text-center text-slate-400 italic">Tidak ada tiket yang cocok dengan pencarian.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- ======= MOBILE CARDS (< md) ======= -->
                <div class="block md:hidden space-y-3">
                    <?php if (!empty($tickets)): ?>
                        <?php foreach ($tickets as $idx => $t): ?>
                            <?php
                                $statusClass = 'bg-amber-100 text-amber-800';
                                $statusIcon  = 'bi-clock-fill';
                                $statusLabel = 'Pending Approval';
                                if ($t['status'] === 'Approved' || $t['status'] === 'Selesai') {
                                    $statusClass = 'bg-emerald-100 text-emerald-800';
                                    $statusIcon  = 'bi-check-circle-fill';
                                    $statusLabel = $t['status'];
                                } elseif ($t['status'] === 'Rejected') {
                                    $statusClass = 'bg-rose-100 text-rose-800';
                                    $statusIcon  = 'bi-x-circle-fill';
                                    $statusLabel = 'Rejected';
                                }

                                $prioClass = 'bg-slate-100 text-slate-600';
                                $prioLabel = 'Normal';
                                if ($t['prioritas'] === 'Urgent') { $prioClass = 'bg-rose-100 text-rose-700'; $prioLabel = 'Urgent'; }
                                elseif ($t['prioritas'] === 'Tinggi') { $prioClass = 'bg-amber-100 text-amber-800'; $prioLabel = 'Tinggi'; }
                            ?>
                            <div class="tkt-card bg-white rounded-2xl border border-slate-200 p-4 shadow-xs space-y-3">
                                <!-- Card Header: Ticket number + status -->
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <span class="font-mono font-extrabold text-orange-600 text-sm"><?= htmlspecialchars($t['ticket_number']); ?></span>
                                        <div class="text-[10px] text-slate-400 mt-0.5">No. <?= $idx + 1 ?></div>
                                    </div>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full font-bold text-[10px] <?= $statusClass ?>">
                                        <i class="bi <?= $statusIcon ?>"></i> <?= $statusLabel ?>
                                    </span>
                                </div>

                                <!-- Pemohon -->
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-extrabold text-sm shrink-0">
                                        <?= strtoupper(substr($t['nama'], 0, 1)); ?>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 text-xs truncate"><?= htmlspecialchars($t['nama']); ?></div>
                                        <div class="text-[10px] text-slate-400 font-mono"><?= htmlspecialchars($t['nim_nip']); ?></div>
                                    </div>
                                </div>

                                <!-- Unit Terkait + Kategori + Prioritas -->
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <?php if (!empty($t['unit_terkait'])): ?>
                                        <span class="px-2 py-0.5 rounded bg-orange-50 text-orange-700 border border-orange-100 font-semibold text-[10px]"><?= htmlspecialchars($t['unit_terkait']); ?></span>
                                    <?php endif; ?>
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold text-[10px]"><?= htmlspecialchars($t['kategori']); ?></span>
                                    <span class="px-2 py-0.5 rounded-full font-bold text-[10px] <?= $prioClass ?>"><?= $prioLabel ?></span>
                                </div>

                                <!-- Perihal -->
                                <p class="text-xs text-slate-700 leading-snug line-clamp-2" title="<?= htmlspecialchars($t['perihal']); ?>"><?= htmlspecialchars($t['perihal']); ?></p>

                                <!-- Action buttons -->
                                <?php if ($active_tab === 'approval' && in_array($t['status'], ['Inputted', 'Pending'])): ?>
                                    <form action="<?= site_url('adminlayanan/update_ticket_status'); ?>" method="POST" class="flex gap-2 pt-1 border-t border-slate-100">
                                        <input type="hidden" name="id" value="<?= $t['id']; ?>">
                                        <button type="submit" name="status" value="Approved" class="flex-1 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center justify-center gap-1">
                                            <i class="bi bi-check2"></i> Approve
                                        </button>
                                        <button type="submit" name="status" value="Rejected" class="flex-1 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs flex items-center justify-center gap-1">
                                            <i class="bi bi-x-lg"></i> Reject
                                        </button>
                                    </form>
                                <?php elseif (!empty($t['catatan'])): ?>
                                    <div class="pt-1 border-t border-slate-100 text-[11px] text-slate-500 italic">
                                        <i class="bi bi-chat-left-text text-slate-400 mr-1"></i><?= htmlspecialchars($t['catatan']); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="pt-1 border-t border-slate-100 text-[11px] text-slate-400 italic">Selesai diproses</div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="py-10 text-center text-slate-400 italic text-xs">Belum ada data tiket layanan yang ditemukan.</div>
                    <?php endif; ?>
                    <div id="noResultsMobileTkt" style="display:none" class="py-8 text-center text-slate-400 italic text-xs">
                        Tidak ada tiket yang cocok dengan pencarian.
                    </div>
                </div>

            </div>
        <?php endif; ?>

    </main>

</body>
</html>
