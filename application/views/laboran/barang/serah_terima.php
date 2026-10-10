<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$workflow_stage = $workflow_stage ?? 'serah_terima';
$is_serah_terima = ($workflow_stage === 'serah_terima');
$boleh_serah = !empty($qr_valid) && $is_serah_terima;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= html_escape($title ?? 'Serah Terima Barang - Panel Laboran') ?></title>
    
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
    
    <!-- Google Fonts & Icons -->
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
            }
        }

        @media (max-width: 1023.98px) {
            .page-wrapper-for-sidebar {
                margin-left: 0 !important;
                width: 100% !important;
                padding-top: 48px;
            }
        }

        .camera-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 antialiased selection:bg-orange-500 selection:text-white">

    <!-- Universal Curved Sidebar Component -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <div class="page-wrapper-for-sidebar">
        <!-- Main Content Container -->
        <main class="min-h-screen p-4 sm:p-6 lg:p-8 max-w-5xl mx-auto">

            <!-- Top Header Navigation -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <a href="<?= site_url('peminjamanbarang/scanner') ?>"
                           class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white border border-slate-200 text-slate-600 hover:text-orange-600 hover:border-orange-300 text-xs font-semibold shadow-sm transition-colors">
                            <i class="bi bi-arrow-left"></i>
                            <span>Kembali ke Scanner</span>
                        </a>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-100 text-orange-700 text-xs font-bold uppercase tracking-wider">
                            <i class="bi bi-box-seam"></i>
                            <span>Serah Terima Barang</span>
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Serah Terima Barang
                    </h1>
                    <p class="text-sm text-slate-500 mt-1">
                        Verifikasi fisik peralatan laboratorium, sesuaikan kuantitas serah, dan ambil bukti dokumentasi serah terima.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="<?= site_url('peminjamanbarang/scanner') ?>"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white border border-slate-200 text-slate-700 hover:border-orange-300 hover:text-orange-600 text-sm font-semibold shadow-sm transition-all">
                        <i class="bi bi-qr-code-scan text-orange-500"></i>
                        <span>Scan Ulang</span>
                    </a>
                </div>
            </div>

            <!-- Flash Error Message -->
            <?php if ($this->session->flashdata('error')): ?>
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3.5 rounded-2xl flex items-center gap-3 shadow-sm">
                    <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center flex-shrink-0">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div class="text-sm font-medium leading-relaxed">
                        <?= html_escape($this->session->flashdata('error')) ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Detail Peminjaman Header Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5 sm:p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-5 border-b border-slate-100">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-orange-500/10 text-orange-600 flex items-center justify-center font-bold text-xl flex-shrink-0">
                            <i class="bi bi-box-seam-fill"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kode / Nomor Peminjaman</div>
                            <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 mt-0.5">
                                <?= html_escape($peminjaman->group_id ?? '-') ?>
                            </h2>
                            <div class="text-sm font-semibold text-slate-700 mt-0.5 flex flex-wrap items-center gap-2">
                                <span><?= html_escape($peminjaman->nama_peminjam ?? '-') ?></span>
                                <span class="text-slate-300">•</span>
                                <span class="text-slate-500 font-mono text-xs"><?= html_escape($peminjaman->nim_nip ?? '-') ?></span>
                                <?php if (!empty($peminjaman->prodi ?? $peminjaman->prodi_peminjam)): ?>
                                    <span class="text-slate-300">•</span>
                                    <span class="text-xs px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-medium">
                                        <?= html_escape($peminjaman->prodi ?? $peminjaman->prodi_peminjam) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div>
                        <?php if ($is_serah_terima): ?>
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <i class="bi bi-check-circle-fill text-emerald-500"></i>
                                <span>Siap Serah Terima Fisik</span>
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                <i class="bi bi-hourglass-split text-amber-500"></i>
                                <span><?= html_escape($peminjaman->status ?? '-') ?></span>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-5">
                    <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-100">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tanggal Pinjam</div>
                        <div class="text-sm font-bold text-slate-800 mt-1 flex items-center gap-1.5">
                            <i class="bi bi-calendar-event text-orange-500"></i>
                            <span><?= html_escape(function_exists('tanggal_indonesia') ? tanggal_indonesia($peminjaman->tanggal_pinjam ?? null) : ($peminjaman->tanggal_pinjam ?? '-')) ?></span>
                        </div>
                    </div>

                    <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-100">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Rencana Kembali</div>
                        <div class="text-sm font-bold text-slate-800 mt-1 flex items-center gap-1.5">
                            <i class="bi bi-calendar-check text-orange-500"></i>
                            <span><?= html_escape(function_exists('tanggal_indonesia') ? tanggal_indonesia($peminjaman->tanggal_kembali_rencana ?? null) : ($peminjaman->tanggal_kembali_rencana ?? '-')) ?></span>
                        </div>
                    </div>

                    <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-100">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Keperluan Pinjam</div>
                        <div class="text-sm font-medium text-slate-700 mt-1 line-clamp-2" title="<?= html_escape($peminjaman->keperluan ?? '-') ?>">
                            <?= html_escape($peminjaman->keperluan ?? '-') ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Callout Stage Banners -->
            <?php if ($is_serah_terima): ?>
                <div class="bg-emerald-50/80 border border-emerald-200 rounded-3xl p-5 sm:p-6 mb-6">
                    <div class="flex items-start gap-4">
                        <div class="w-11 h-11 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-bold text-lg flex-shrink-0 shadow-md shadow-emerald-500/20">
                            <i class="bi bi-box-seam-fill"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Siap Serah Terima Fisik</div>
                            <h3 class="text-base font-extrabold text-emerald-950 mt-0.5">Verifikasi Fisik &amp; Serah Terima Barang</h3>
                            <p class="text-xs sm:text-sm text-emerald-800/90 mt-1 leading-relaxed">
                                Pengajuan telah disetujui. Silakan periksa kelayakan fisik barang di laboratorium, sesuaikan jumlah yang diserahkan jika ada kekurangan stok fisik, ambil foto bukti dokumentasi, dan klik <strong>Konfirmasi &amp; Serahkan Barang</strong> di bawah. Status akan langsung diperbarui menjadi <strong>Sedang Dipinjam</strong>.
                            </p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-amber-50 border-2 border-amber-300 rounded-3xl p-5 sm:p-6 mb-6 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="w-11 h-11 rounded-2xl bg-amber-500 text-white flex items-center justify-center font-bold text-lg flex-shrink-0 shadow-md shadow-amber-500/20">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-amber-800 uppercase tracking-wider">Menunggu Persetujuan Resmi</div>
                            <h3 class="text-base font-extrabold text-amber-950 mt-0.5">Belum Siap Diserahterimakan</h3>
                            <p class="text-xs sm:text-sm text-amber-800/90 mt-1 leading-relaxed">
                                <?= html_escape($qr_message ?? 'Pengajuan ini masih menunggu persetujuan resmi dan belum dapat diserahterimakan di loket laboratorium.') ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($is_serah_terima): ?>
                <form id="handoverForm" method="post" enctype="multipart/form-data" action="<?= site_url('peminjamanbarang/proses_serah/' . rawurlencode($peminjaman->group_id)) ?>">
                    <!-- Table Rincian Barang Card -->
                    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5 sm:p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-base font-bold text-slate-800">Daftar Barang yang Diserahkan</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Jumlah unit dapat dikurangi jika ketersediaan fisik saat serah terima kurang dari pengajuan.</p>
                            </div>
                        </div>

                        <div class="overflow-x-auto rounded-2xl border border-slate-200">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-slate-50 text-slate-600 font-semibold text-xs uppercase border-b border-slate-200">
                                    <tr>
                                        <th class="px-4 py-3.5">Nama Aset / Barang</th>
                                        <th class="px-4 py-3.5">Kode Aset</th>
                                        <th class="px-4 py-3.5">Ruangan / Lab</th>
                                        <th class="px-4 py-3.5 text-center w-32">Sisa Stok</th>
                                        <th class="px-4 py-3.5 text-center w-28">Diajukan</th>
                                        <th class="px-4 py-3.5 text-right w-44">Diserahkan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                                    <?php foreach (($peminjaman->detail_barang ?? []) as $item): ?>
                                        <?php 
                                            $jumlah_pinjam = (int)($item->jumlah_pinjam ?? 0); 
                                            $tersedia = (int)($item->jumlah_tersedia ?? 0);
                                            $total    = (int)($item->jumlah_total ?? 0);
                                        ?>
                                        <tr class="hover:bg-slate-50/60 transition-colors">
                                            <td class="px-4 py-3.5">
                                                <div class="font-bold text-slate-900"><?= html_escape($item->nama_aset ?? '-') ?></div>
                                            </td>
                                            <td class="px-4 py-3.5 font-mono text-xs text-slate-500">
                                                <?= html_escape($item->kode_aset ?? '-') ?>
                                            </td>
                                            <td class="px-4 py-3.5 text-slate-600">
                                                <?= html_escape($item->nama_ruangan ?? '-') ?>
                                            </td>
                                            <td class="px-4 py-3.5 text-center">
                                                <div class="inline-flex flex-col items-center">
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold <?= $tersedia > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/70' : 'bg-rose-50 text-rose-700 border border-rose-200/70' ?>" title="Sisa stok tersedia fisik di lab saat ini">
                                                        <span class="w-1.5 h-1.5 rounded-full <?= $tersedia > 0 ? 'bg-emerald-500' : 'bg-rose-500' ?>"></span>
                                                        <?= $tersedia ?> unit
                                                    </span>
                                                    <?php if ($total > 0): ?>
                                                        <span class="text-[10px] text-slate-400 mt-0.5 font-medium">Total: <?= $total ?> unit</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3.5 text-center font-bold text-slate-600">
                                                <?= $jumlah_pinjam ?> unit
                                            </td>
                                            <td class="px-4 py-3.5 text-right">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-decrement w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center transition-colors">
                                                        -
                                                    </button>
                                                    <input type="number"
                                                           name="jumlah_barang[<?= html_escape($item->kode_aset ?? '') ?>]"
                                                           value="<?= $jumlah_pinjam ?>"
                                                           min="0"
                                                           max="<?= $jumlah_pinjam ?>"
                                                           class="jumlah-input w-16 px-2 py-1.5 text-center border border-slate-200 rounded-lg font-bold text-slate-900 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none"
                                                           data-max="<?= $jumlah_pinjam ?>"
                                                           required>
                                                    <button type="button" class="btn-increment w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center transition-colors">
                                                        +
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Bukti Foto Dokumentasi Card -->
                    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5 sm:p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-base font-bold text-slate-800">Dokumentasi Foto Serah Terima</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Ambil foto bukti penyerahan barang secara langsung atau unggah dari perangkat.</p>
                            </div>
                        </div>

                        <input type="file" id="fileInputSerah" class="hidden" accept="image/*" multiple>

                        <div class="flex flex-wrap gap-3 mb-4">
                            <button type="button" id="btnKameraSerah"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-orange-600 hover:bg-orange-700 text-white font-semibold text-sm shadow-sm shadow-orange-600/20 transition-all active:scale-[0.98]">
                                <i class="bi bi-camera-fill"></i>
                                <span>Buka Kamera</span>
                            </button>
                            <button type="button" id="btnGaleriSerah"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white border border-slate-200 text-slate-700 hover:border-orange-300 hover:text-orange-600 font-semibold text-sm shadow-sm transition-all active:scale-[0.98]">
                                <i class="bi bi-images text-orange-500"></i>
                                <span>Pilih dari Galeri / File</span>
                            </button>
                        </div>

                        <div id="previewSerah" class="grid grid-cols-2 sm:grid-cols-4 gap-3"></div>
                    </div>

                    <!-- Catatan Serah Terima Card -->
                    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5 sm:p-6 mb-8">
                        <h3 class="text-base font-bold text-slate-800 mb-1">Catatan Tambahan (Opsional)</h3>
                        <p class="text-xs text-slate-500 mb-3">Tuliskan keterangan nomor seri khusus, kelengkapan aksesoris, atau instruksi khusus kepada peminjam.</p>
                        <textarea name="catatan_serah" rows="3"
                                  placeholder="Contoh: Kabel power dan adaptor VGA lengkap diserahkan dalam tas..."
                                  class="w-full px-4 py-3 text-sm rounded-2xl border border-slate-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition-all resize-none"></textarea>
                    </div>

                    <!-- Action Submit Buttons -->
                    <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-2">
                        <a href="<?= site_url('peminjamanbarang/scanner') ?>"
                           class="w-full sm:w-auto px-6 py-3.5 rounded-2xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold text-sm text-center transition-all">
                            Batal
                        </a>
                        <button type="button" id="btnSubmitSerah"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl bg-orange-600 hover:bg-orange-700 shadow-orange-600/25 text-white font-bold text-sm shadow-lg transition-all active:scale-[0.98]">
                            <i class="bi bi-check2-circle text-lg"></i>
                            <span>Konfirmasi &amp; Serahkan Barang</span>
                        </button>
                    </div>
                </form>

            <?php else: ?>
                <!-- State Alert Ketika Status Tidak Memenuhi Syarat Serah -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-8 text-center max-w-lg mx-auto my-8">
                    <div class="w-16 h-16 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center text-3xl mx-auto mb-4">
                        <i class="bi bi-exclamation-octagon"></i>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 mb-2">Tidak Dapat Memproses Serah Terima</h3>
                    <p class="text-sm text-slate-600 leading-relaxed mb-6">
                        <?= html_escape($qr_message ?? 'Status peminjaman ini bukan "Disetujui (Menunggu Pengambilan)" atau belum melewati persetujuan Kaprodi.') ?>
                    </p>
                    <a href="<?= site_url('peminjamanbarang/scanner') ?>"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm transition-all">
                        <i class="bi bi-qr-code-scan"></i>
                        <span>Kembali ke Scanner</span>
                    </a>
                </div>
            <?php endif; ?>

        </main>
    </div>

    <!-- Camera Live Modal Overlay -->
    <div id="cameraOverlaySerah" class="camera-overlay hidden">
        <div class="bg-slate-900 rounded-3xl overflow-hidden w-full max-w-lg border border-slate-800 shadow-2xl">
            <div class="p-4 border-b border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-2 text-white font-bold text-sm">
                    <i class="bi bi-camera text-orange-500"></i>
                    <span>Ambil Foto Dokumentasi</span>
                </div>
                <button type="button" id="btnTutupKameraSerah" class="text-slate-400 hover:text-white text-lg w-8 h-8 rounded-full flex items-center justify-center">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="relative bg-black aspect-video flex items-center justify-center overflow-hidden">
                <video id="cameraVideoSerah" autoplay playsinline muted class="w-full h-full object-cover"></video>
            </div>
            <div class="p-4 bg-slate-950 flex items-center justify-center gap-4">
                <button type="button" id="btnJepretSerah"
                        class="w-16 h-16 rounded-full bg-orange-600 hover:bg-orange-500 text-white flex items-center justify-center text-2xl shadow-lg shadow-orange-600/40 transition-transform active:scale-90 border-4 border-slate-800">
                    <i class="bi bi-camera-fill"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Script Logic -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('handoverForm');
        const fileInput = document.getElementById('fileInputSerah');
        const preview = document.getElementById('previewSerah');
        const btnKamera = document.getElementById('btnKameraSerah');
        const btnGaleri = document.getElementById('btnGaleriSerah');
        const btnSubmit = document.getElementById('btnSubmitSerah');
        
        let selectedFiles = [];
        let cameraStream = null;

        // Counter Increment / Decrement
        document.querySelectorAll('.btn-decrement').forEach(btn => {
            btn.addEventListener('click', () => {
                const input = btn.parentElement.querySelector('.jumlah-input');
                const min = parseInt(input.getAttribute('min') || '0', 10);
                let val = parseInt(input.value || '0', 10);
                if (val > min) {
                    input.value = val - 1;
                }
            });
        });

        document.querySelectorAll('.btn-increment').forEach(btn => {
            btn.addEventListener('click', () => {
                const input = btn.parentElement.querySelector('.jumlah-input');
                const max = parseInt(input.getAttribute('data-max') || input.getAttribute('max') || '999', 10);
                let val = parseInt(input.value || '0', 10);
                if (val < max) {
                    input.value = val + 1;
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Maksimal Sesuai Pengajuan',
                        text: `Barang yang diserahkan tidak boleh melebihi kuantitas yang diajukan peminjam (${max} unit).`,
                        confirmButtonColor: '#ea580c'
                    });
                }
            });
        });

        // Validasi Real-time Input Manual Keyboard
        document.querySelectorAll('.jumlah-input').forEach(input => {
            input.addEventListener('change', () => {
                const max = parseInt(input.getAttribute('data-max') || input.getAttribute('max') || '999', 10);
                const min = parseInt(input.getAttribute('min') || '0', 10);
                let val = parseInt(input.value || '0', 10);
                if (val > max) {
                    input.value = max;
                    Swal.fire({
                        icon: 'warning',
                        title: 'Melebihi Kuota Pengajuan',
                        text: `Jumlah yang diserahkan (${val} unit) melebihi kuantitas yang disetujui (${max} unit). Nilai otomatis dikembalikan ke ${max} unit.`,
                        confirmButtonColor: '#ea580c'
                    });
                } else if (val < min || isNaN(val)) {
                    input.value = min;
                }
            });
        });

        function syncFileInput() {
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            fileInput.files = dt.files;
        }

        function renderPreview() {
            preview.innerHTML = '';
            selectedFiles.forEach((file, idx) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const card = document.createElement('div');
                    card.className = 'relative group rounded-2xl overflow-hidden border border-slate-200 bg-slate-100 shadow-sm aspect-video';
                    card.innerHTML = `
                        <img src="${e.target.result}" alt="Preview" class="w-full h-full object-cover">
                        <button type="button" class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-slate-900/80 hover:bg-rose-600 text-white flex items-center justify-center text-xs shadow-md transition-colors" data-idx="${idx}">
                            <i class="bi bi-x"></i>
                        </button>`;
                    preview.appendChild(card);
                    card.querySelector('button').addEventListener('click', () => {
                        selectedFiles.splice(idx, 1);
                        syncFileInput();
                        renderPreview();
                    });
                };
                reader.readAsDataURL(file);
            });
        }

        if (btnGaleri) {
            btnGaleri.addEventListener('click', () => {
                fileInput.removeAttribute('capture');
                fileInput.setAttribute('multiple', 'multiple');
                fileInput.click();
            });
        }

        if (fileInput) {
            fileInput.addEventListener('change', () => {
                Array.from(fileInput.files || []).forEach(file => {
                    if (file.type.startsWith('image/') && file.size <= 5 * 1024 * 1024) {
                        selectedFiles.push(file);
                    } else if (file.size > 5 * 1024 * 1024) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Ukuran File Terlalu Besar',
                            text: 'File ' + file.name + ' melebihi batas 5MB.',
                            confirmButtonColor: '#ea580c'
                        });
                    }
                });
                syncFileInput();
                renderPreview();
            });
        }

        // Camera Live
        const overlay = document.getElementById('cameraOverlaySerah');
        const video = document.getElementById('cameraVideoSerah');
        const btnJepret = document.getElementById('btnJepretSerah');
        const btnTutupKamera = document.getElementById('btnTutupKameraSerah');

        async function openCamera() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kamera Tidak Didukung',
                    text: 'Browser Anda tidak mendukung akses kamera langsung.',
                    confirmButtonColor: '#ea580c'
                });
                return;
            }
            try {
                cameraStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: { ideal: 'environment' } }
                });
                video.srcObject = cameraStream;
                overlay.classList.remove('hidden');
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Membuka Kamera',
                    text: 'Pastikan izin kamera diaktifkan. Detail: ' + err.message,
                    confirmButtonColor: '#ea580c'
                });
            }
        }

        function closeCamera() {
            if (cameraStream) {
                cameraStream.getTracks().forEach(t => t.stop());
                cameraStream = null;
            }
            overlay.classList.add('hidden');
        }

        function ambilFoto() {
            if (!video.videoWidth) return;
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            canvas.toBlob(blob => {
                const file = new File([blob], 'serah_terima_' + Date.now() + '.jpg', { type: 'image/jpeg' });
                selectedFiles.push(file);
                syncFileInput();
                renderPreview();
                closeCamera();
            }, 'image/jpeg', 0.9);
        }

        if (btnKamera) btnKamera.addEventListener('click', openCamera);
        if (btnJepret) btnJepret.addEventListener('click', ambilFoto);
        if (btnTutupKamera) btnTutupKamera.addEventListener('click', closeCamera);

        // Submit Confirmation
        if (btnSubmit && form) {
            btnSubmit.addEventListener('click', () => {
                if (!form.reportValidity()) return;

                const inputs = form.querySelectorAll('.jumlah-input');
                let totalUnits = 0;
                let invalidItem = null;

                inputs.forEach(i => {
                    const val = parseInt(i.value || '0', 10);
                    const max = parseInt(i.getAttribute('data-max') || i.getAttribute('max') || '999', 10);
                    if (val > max) {
                        invalidItem = { max, val };
                    }
                    totalUnits += val;
                });

                if (invalidItem) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kuantitas Melebihi Pengajuan',
                        text: `Jumlah barang yang diserahkan (${invalidItem.val} unit) tidak boleh melebihi kuantitas yang diajukan (${invalidItem.max} unit).`,
                        confirmButtonColor: '#ea580c'
                    });
                    return;
                }

                if (totalUnits === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Jumlah Barang Kosong',
                        text: 'Minimal harus ada 1 unit barang yang diserahkan.',
                        confirmButtonColor: '#ea580c'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi Serah Terima Fisik',
                    html: `Apakah Anda yakin ingin menyerahkan total <b>${totalUnits} unit</b> barang ini kepada peminjam? Status akan langsung diperbarui menjadi <b>Sedang Dipinjam</b>.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#ea580c',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Serahkan Barang',
                    cancelButtonText: 'Batal'
                }).then((res) => {
                    if (res.isConfirmed) {
                        fileInput.name = 'foto_serah[]';
                        form.submit();
                    }
                });
            });
        }
    });
    </script>
</body>
</html>
