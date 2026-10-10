<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$active_tab = $active_tab ?? 'pending';
$pending_count = (int)($pending_count ?? 0);
$peminjaman_list = $peminjaman_list ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= html_escape($title ?? 'Approval Peminjaman Barang - Kaur') ?></title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            900: '#14532d',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                padding-left: 76px;
            }
        }

        @media (max-width: 1023.98px) {
            .page-wrapper-for-sidebar {
                margin-left: 0 !important;
                width: 100% !important;
                padding-bottom: 90px;
            }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen">

    <!-- Curved Sidebar Component -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <div class="page-wrapper-for-sidebar">
        <!-- Top Navbar -->
        <header class="bg-white/80 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30 px-4 sm:px-8 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-600/10 text-emerald-600 flex items-center justify-center font-bold text-lg">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <h1 class="text-base sm:text-lg font-extrabold text-slate-900 leading-tight">Persetujuan Resmi Ka. Ur</h1>
                    <p class="text-xs text-slate-500">Peminjaman Barang &amp; Aset Laboratorium Fakultas</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="<?= site_url('kaur/approval') ?>" class="px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors flex items-center gap-1.5">
                    <i class="bi bi-door-open"></i>
                    <span class="hidden sm:inline">Persetujuan Ruangan</span>
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-8 py-6">

            <!-- Flashdata Notifications -->
            <?php if ($this->session->flashdata('success')): ?>
                <div class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-3 shadow-sm">
                    <i class="bi bi-check-circle-fill text-emerald-500 text-lg flex-shrink-0"></i>
                    <span><?= html_escape($this->session->flashdata('success')) ?></span>
                </div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('error')): ?>
                <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-semibold flex items-center gap-3 shadow-sm">
                    <i class="bi bi-exclamation-triangle-fill text-rose-500 text-lg flex-shrink-0"></i>
                    <span><?= html_escape($this->session->flashdata('error')) ?></span>
                </div>
            <?php endif; ?>

            <!-- Header Banner -->
            <div class="bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-800 rounded-3xl p-6 sm:p-8 text-white shadow-lg mb-6 relative overflow-hidden">
                <div class="relative z-10 max-w-2xl">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-md text-emerald-100 border border-white/20 mb-3">
                        <i class="bi bi-award-fill"></i>
                        <span>Otoritas Kepala Urusan (Kaur)</span>
                    </span>
                    <h2 class="text-xl sm:text-2xl font-black tracking-tight mb-2">Verifikasi &amp; Pengesahan Peminjaman Barang</h2>
                    <p class="text-xs sm:text-sm text-emerald-100/90 leading-relaxed">
                        Pengajuan barang yang telah disetujui Kaprodi dan diverifikasi fisik oleh Laboran memerlukan persetujuan resmi Ka. Ur agar kode QR serah terima aktif dan barang dapat diambil oleh peminjam.
                    </p>
                </div>
                <div class="absolute -right-6 -bottom-6 w-44 h-44 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
            </div>

            <!-- Tabs Navigation -->
            <div class="flex items-center gap-2 border-b border-slate-200 pb-3 mb-6 overflow-x-auto">
                <a href="<?= site_url('kaur/barang?tab=pending') ?>"
                   class="px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold flex items-center gap-2 transition-all <?= $active_tab === 'pending' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' ?>">
                    <i class="bi bi-hourglass-split"></i>
                    <span>Menunggu ACC Kaur</span>
                    <?php if ($pending_count > 0): ?>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black <?= $active_tab === 'pending' ? 'bg-white text-emerald-700' : 'bg-emerald-100 text-emerald-700' ?>">
                            <?= $pending_count ?>
                        </span>
                    <?php endif; ?>
                </a>

                <a href="<?= site_url('kaur/barang?tab=approved') ?>"
                   class="px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold flex items-center gap-2 transition-all <?= $active_tab === 'approved' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' ?>">
                    <i class="bi bi-check2-circle"></i>
                    <span>Disetujui / Selesai</span>
                </a>

                <a href="<?= site_url('kaur/barang?tab=rejected') ?>"
                   class="px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold flex items-center gap-2 transition-all <?= $active_tab === 'rejected' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' ?>">
                    <i class="bi bi-x-circle"></i>
                    <span>Ditolak</span>
                </a>
            </div>

            <!-- Data List -->
            <?php if (empty($peminjaman_list)): ?>
                <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center shadow-sm">
                    <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center font-bold text-2xl mx-auto mb-3">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Tidak ada pengajuan peminjaman barang</h3>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                        Saat ini tidak ada data peminjaman barang pada kategori tab ini.
                    </p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($peminjaman_list as $item): ?>
                        <?php
                            $progress = function_exists('scm_loan_progress') ? scm_loan_progress($item) : null;
                            $group_id = $item->group_id ?? ('single-' . ($item->id_peminjaman ?? '0'));
                            $is_need_action = ($item->status === 'Menunggu ACC Kaur' || (($item->status_laboran ?? '') === 'Disetujui' && ($item->status_kaur ?? '') === 'Pending'));
                        ?>
                        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow p-5 sm:p-6">
                            <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                                <div class="space-y-2 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-mono text-xs font-bold px-2.5 py-1 rounded-xl bg-slate-100 text-slate-700 border border-slate-200">
                                            <?= html_escape($group_id) ?>
                                        </span>
                                        <span class="px-2.5 py-1 rounded-xl text-xs font-bold <?= $is_need_action ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' ?>">
                                            <i class="bi <?= $is_need_action ? 'bi-hourglass-split' : 'bi-check-circle-fill' ?> me-1"></i>
                                            <?= html_escape($progress['status_label'] ?? ($item->status ?? '-')) ?>
                                        </span>
                                        <?php if (!empty($item->prodi)): ?>
                                            <span class="px-2.5 py-1 rounded-xl text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                <?= html_escape($item->prodi) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <div>
                                        <h3 class="text-base sm:text-lg font-extrabold text-slate-900">
                                            <?= html_escape($item->nama_peminjam ?? 'Mahasiswa') ?>
                                            <span class="text-xs font-medium text-slate-400 ms-1">(<?= html_escape($item->nim_nip ?? '-') ?>)</span>
                                        </h3>
                                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                                            Keperluan: <strong class="text-slate-700"><?= html_escape($item->keperluan ?? '-') ?></strong>
                                        </p>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-slate-600 pt-2">
                                        <div class="flex items-center gap-2">
                                            <i class="bi bi-calendar-event text-emerald-600 text-sm"></i>
                                            <span>Masa Pinjam: <strong><?= date('d/m/Y', strtotime($item->tanggal_pinjam)) ?> &ndash; <?= date('d/m/Y', strtotime($item->tanggal_kembali_rencana)) ?></strong></span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <i class="bi bi-person-check text-emerald-600 text-sm"></i>
                                            <span>Verifikasi Laboran: <strong class="text-emerald-700"><?= html_escape($item->status_laboran ?? 'Disetujui') ?></strong></span>
                                        </div>
                                    </div>

                                    <!-- Daftar Barang Mini Preview -->
                                    <?php if (!empty($item->detail_barang)): ?>
                                        <div class="pt-2">
                                            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Barang yang Dipinjam:</div>
                                            <div class="flex flex-wrap gap-2">
                                                <?php foreach ($item->detail_barang as $brg): ?>
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700">
                                                        <i class="bi bi-box-seam text-slate-400"></i>
                                                        <?= html_escape($brg->nama_aset ?? '-') ?>
                                                        <span class="font-bold text-emerald-600">(<?= (int)($brg->jumlah_pinjam ?? 1) ?> unit)</span>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-row lg:flex-col items-center lg:items-end justify-end gap-2 pt-3 lg:pt-0 border-t lg:border-t-0 border-slate-100 flex-shrink-0">
                                    <?php if ($is_need_action): ?>
                                        <button type="button"
                                                onclick="openApproveModal('<?= html_escape($group_id) ?>', '<?= html_escape(addslashes($item->nama_peminjam ?? '')) ?>')"
                                                class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm flex items-center gap-2 shadow-md shadow-emerald-600/20 transition-all">
                                            <i class="bi bi-check-circle-fill"></i>
                                            <span>ACC Kaur</span>
                                        </button>
                                        <button type="button"
                                                onclick="openRejectModal('<?= html_escape($group_id) ?>', '<?= html_escape(addslashes($item->nama_peminjam ?? '')) ?>')"
                                                class="px-4 py-2.5 rounded-xl bg-white border border-rose-200 hover:bg-rose-50 text-rose-600 font-bold text-xs sm:text-sm flex items-center gap-1.5 transition-colors">
                                            <i class="bi bi-x-circle"></i>
                                            <span>Tolak</span>
                                        </button>
                                    <?php endif; ?>
                                    <button type="button"
                                            onclick='openDetailModal(<?= html_escape(json_encode([
                                                'group_id' => $group_id,
                                                'nama_peminjam' => $item->nama_peminjam ?? 'Mahasiswa',
                                                'nim_nip' => $item->nim_nip ?? '-',
                                                'prodi' => $item->prodi ?? '-',
                                                'keperluan' => $item->keperluan ?? '-',
                                                'tgl_pinjam' => date('d/m/Y', strtotime($item->tanggal_pinjam)),
                                                'tgl_kembali' => date('d/m/Y', strtotime($item->tanggal_kembali_rencana)),
                                                'status' => $progress['status_label'] ?? ($item->status ?? '-'),
                                                'catatan_kaprodi' => $item->catatan_kaprodi ?? '',
                                                'items' => array_map(function($b) {
                                                    return [
                                                        'nama' => $b->nama_aset ?? '-',
                                                        'qty' => (int)($b->jumlah_pinjam ?? 1)
                                                    ];
                                                }, $item->detail_barang ?? [])
                                            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)) ?>)'
                                            class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm flex items-center gap-1.5 transition-colors">
                                        <i class="bi bi-eye"></i>
                                        <span>Rincian</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </main>
    </div>

    <!-- Modal ACC Kaur -->
    <div id="modalApproveKaur" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-base">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-slate-900">Persetujuan Resmi Ka. Ur</h3>
                </div>
                <button type="button" onclick="closeApproveModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>
            <form id="formApproveKaur" method="post" action="">
                <div class="py-4 space-y-3">
                    <p class="text-xs sm:text-sm text-slate-600">
                        Apakah Anda yakin menyetujui pengajuan peminjaman barang untuk <strong id="approveModalName">-</strong>?
                    </p>
                    <div>
                        <label for="catatan_kaur" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Catatan Persetujuan <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <textarea id="catatan_kaur" name="catatan_kaur" rows="2" placeholder="Contoh: Disetujui resmi untuk kegiatan akademik." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeApproveModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-md shadow-emerald-600/20">
                        <i class="bi bi-check2"></i>
                        <span>Konfirmasi ACC</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tolak Kaur -->
    <div id="modalRejectKaur" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-base">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-slate-900">Tolak Pengajuan Barang</h3>
                </div>
                <button type="button" onclick="closeRejectModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>
            <form id="formRejectKaur" method="post" action="">
                <div class="py-4 space-y-3">
                    <p class="text-xs sm:text-sm text-slate-600">
                        Tolak pengajuan barang untuk <strong id="rejectModalName">-</strong>. Stok yang sempat direservasi akan dikembalikan.
                    </p>
                    <div>
                        <label for="catatan_reject_kaur" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Alasan Penolakan <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="catatan_reject_kaur" name="catatan_kaur" rows="2" required placeholder="Tulis alasan penolakan..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-rose-500"></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-md shadow-rose-600/20">
                        <i class="bi bi-x-lg"></i>
                        <span>Tolak Pengajuan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail Kaur -->
    <div id="modalDetailKaur" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-slate-100 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-base">
                        <i class="bi bi-info-circle-fill"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Rincian Pengajuan Barang</h3>
                        <p class="text-xs text-slate-500 font-mono" id="detailGroupId">-</p>
                    </div>
                </div>
                <button type="button" onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>
            <div class="py-4 space-y-4">
                <div class="grid grid-cols-2 gap-3 text-xs bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <div>
                        <span class="text-slate-400 block font-semibold uppercase text-[10px]">Peminjam</span>
                        <strong class="text-slate-800 text-sm" id="detailPeminjam">-</strong>
                        <span class="text-slate-500 block mt-0.5" id="detailNim">-</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold uppercase text-[10px]">Program Studi</span>
                        <strong class="text-slate-800" id="detailProdi">-</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold uppercase text-[10px]">Tanggal Pinjam</span>
                        <strong class="text-slate-800" id="detailTglPinjam">-</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold uppercase text-[10px]">Rencana Kembali</span>
                        <strong class="text-slate-800" id="detailTglKembali">-</strong>
                    </div>
                    <div class="col-span-2">
                        <span class="text-slate-400 block font-semibold uppercase text-[10px]">Keperluan</span>
                        <p class="text-slate-700 font-medium mt-0.5" id="detailKeperluan">-</p>
                    </div>
                </div>

                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Daftar Barang yang Diajukan</h4>
                    <div id="detailBarangList" class="space-y-2">
                        <!-- Populated by JS -->
                    </div>
                </div>

                <div id="detailCatatanKaprodiWrap" class="p-3 bg-blue-50/70 border border-blue-100 rounded-xl text-xs hidden">
                    <span class="font-bold text-blue-900 block mb-0.5"><i class="bi bi-chat-left-text me-1"></i> Catatan Kaprodi:</span>
                    <p class="text-blue-800" id="detailCatatanKaprodi">-</p>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeDetailModal()" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        const modalApprove = document.getElementById('modalApproveKaur');
        const modalReject = document.getElementById('modalRejectKaur');
        const modalDetail = document.getElementById('modalDetailKaur');
        const formApprove = document.getElementById('formApproveKaur');
        const formReject = document.getElementById('formRejectKaur');
        const approveName = document.getElementById('approveModalName');
        const rejectName = document.getElementById('rejectModalName');

        function openApproveModal(groupId, name) {
            approveName.textContent = name;
            formApprove.action = '<?= site_url("kaur/approve_barang/") ?>' + encodeURIComponent(groupId);
            modalApprove.classList.remove('hidden');
        }

        function closeApproveModal() {
            modalApprove.classList.add('hidden');
        }

        function openRejectModal(groupId, name) {
            rejectName.textContent = name;
            formReject.action = '<?= site_url("kaur/reject_barang/") ?>' + encodeURIComponent(groupId);
            modalReject.classList.remove('hidden');
        }

        function closeRejectModal() {
            modalReject.classList.add('hidden');
        }

        function openDetailModal(data) {
            if (!data) return;
            document.getElementById('detailGroupId').textContent = data.group_id || '-';
            document.getElementById('detailPeminjam').textContent = data.nama_peminjam || '-';
            document.getElementById('detailNim').textContent = data.nim_nip || '-';
            document.getElementById('detailProdi').textContent = data.prodi || '-';
            document.getElementById('detailTglPinjam').textContent = data.tgl_pinjam || '-';
            document.getElementById('detailTglKembali').textContent = data.tgl_kembali || '-';
            document.getElementById('detailKeperluan').textContent = data.keperluan || '-';

            const listEl = document.getElementById('detailBarangList');
            listEl.innerHTML = '';
            if (data.items && data.items.length > 0) {
                data.items.forEach(function(b) {
                    const row = document.createElement('div');
                    row.className = 'flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/80 text-xs';
                    row.innerHTML = '<span class="font-semibold text-slate-800 flex items-center gap-2"><i class="bi bi-box-seam text-slate-400"></i>' + (b.nama || '-') + '</span><span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-bold border border-emerald-200">' + (b.qty || 1) + ' unit</span>';
                    listEl.appendChild(row);
                });
            } else {
                listEl.innerHTML = '<p class="text-xs text-slate-400 italic">Tidak ada rincian barang.</p>';
            }

            const cWrap = document.getElementById('detailCatatanKaprodiWrap');
            const cText = document.getElementById('detailCatatanKaprodi');
            if (data.catatan_kaprodi && data.catatan_kaprodi.trim() !== '') {
                cText.textContent = data.catatan_kaprodi;
                cWrap.classList.remove('hidden');
            } else {
                cWrap.classList.add('hidden');
            }

            modalDetail.classList.remove('hidden');
        }

        function closeDetailModal() {
            modalDetail.classList.add('hidden');
        }
    </script>
</body>
</html>
