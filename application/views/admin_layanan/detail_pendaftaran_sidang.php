<?php
/**
 * Detail Berkas Pendaftaran Sidang & Validasi Dokumen - Admin LAA
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Detail Pendaftaran Sidang — Admin LAA'; ?> - IFIK</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fff7ed', 100: '#ffedd5', 500: '#f97316',
                            600: '#ea580c', 700: '#c2410c', 900: '#7c2d12'
                        }
                    }
                }
            }
        }
    </script>

    <!-- Icons & Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

    <!-- Sidebar -->
    <?php $this->load->view('admin_layanan/sidebar'); ?>

    <main id="mainDetailContainer" class="min-h-screen p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto transition-all duration-300">

        <!-- Header & Breadcrumb -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-[11px] sm:text-xs font-semibold text-slate-400 mb-2 pl-11 sm:pl-0 pt-0.5 sm:pt-0">
                <a href="<?= site_url('adminlayanan') ?>" class="hover:text-orange-600 transition-colors">Portal LAA</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <a href="<?= site_url('adminlayanan/pendaftaran_sidang') ?>" class="hover:text-orange-600 transition-colors">Pendaftaran Sidang</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-orange-600 font-bold">Detail Mahasiswa</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
                <div class="flex items-center gap-3">
                    <a href="<?= site_url('adminlayanan/pendaftaran_sidang') ?>" class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-orange-600 flex items-center justify-center shadow-xs hover:shadow-md transition shrink-0">
                        <i class="bi bi-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2">
                            <span>File Pendaftaran Sidang</span>
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                            Verifikasi berkas persyaratan sidang mahasiswa: <strong><?= htmlspecialchars($detail['nama'] ?? 'Mahasiswa'); ?></strong>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 self-start sm:self-auto" id="overallStatusBadgeSidang">
                    <?php if ($detail['is_approved']): ?>
                        <span class="px-3.5 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200 flex items-center gap-1.5 shadow-xs">
                            <i class="bi bi-check-circle-fill text-emerald-500"></i> Status: Disetujui Admin LAA
                        </span>
                    <?php else: ?>
                        <span class="px-3.5 py-1.5 rounded-xl bg-amber-50 text-amber-700 text-xs font-bold border border-amber-200 flex items-center gap-1.5 shadow-xs">
                            <i class="bi bi-clock-fill text-amber-500"></i> Status: Pending Verifikasi
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if($this->session->flashdata('success')): ?>
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-semibold flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-green-500"></i> <?= $this->session->flashdata('success') ?>
        </div>
        <?php endif; ?>
        <?php if($this->session->flashdata('error')): ?>
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm font-semibold flex items-center gap-2">
            <i class="bi bi-x-circle-fill text-red-500"></i> <?= $this->session->flashdata('error') ?>
        </div>
        <?php endif; ?>

        <!-- Student Profile Information Card (Matching Photo 2) -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-md p-5 sm:p-6 mb-6">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3 mb-4">
                <i class="bi bi-person-badge text-orange-600 text-lg"></i>
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Informasi Pendaftar Sidang</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-3.5 gap-x-8 text-xs">
                <!-- Left Column -->
                <div class="space-y-2.5">
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">Nama</span>
                        <span class="font-bold text-slate-800">: <?= htmlspecialchars($detail['nama']); ?></span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-36 text-slate-400 font-medium shrink-0">NIM</span>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-orange-600">: <?= htmlspecialchars($detail['nim']); ?></span>
                            <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-bold text-[10px]"><?= htmlspecialchars($detail['konsentrasi'] ?? 'DKV'); ?></span>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">Prodi</span>
                        <span class="font-semibold text-slate-700">: <?= htmlspecialchars($detail['prodi']); ?></span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">Konsentrasi</span>
                        <span class="font-semibold text-slate-700">: <?= htmlspecialchars($detail['konsentrasi']); ?></span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">Alamat</span>
                        <span class="text-slate-600">: <?= htmlspecialchars($detail['alamat'] ?? 'Jl. Telekomunikasi No. 1, Terusan Buah Batu, Bandung'); ?></span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">No. Telp</span>
                        <span class="text-slate-600">: <?= htmlspecialchars($detail['no_hp'] ?? '087808487798'); ?></span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">Tempat Lahir</span>
                        <span class="text-slate-600">: Jakarta</span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">Tanggal Lahir</span>
                        <span class="text-slate-600">: 2002-04-24</span>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-2.5">
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">Judul Tugas Akhir</span>
                        <span class="font-bold text-slate-800 leading-snug">: <?= htmlspecialchars($detail['judul']); ?></span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">Dosen Pembimbing 1</span>
                        <span class="font-semibold text-slate-700">: <?= htmlspecialchars($detail['pembimbing_1']); ?></span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">Dosen Pembimbing 2</span>
                        <span class="font-semibold text-slate-700">: <?= htmlspecialchars($detail['pembimbing_2']); ?></span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">Total SKS</span>
                        <span class="font-bold text-slate-800">: <?= $detail['totalsks'] ?? 141; ?> SKS</span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">IPK</span>
                        <span class="font-bold text-emerald-600">: <?= $detail['ipk'] ?? 3.72; ?></span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">Score EPRT</span>
                        <span class="font-bold text-indigo-600">: <?= $detail['scoreeprt'] ?? 567; ?></span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">Score TAK</span>
                        <span class="font-bold text-purple-600">: <?= $detail['scoretak'] ?? 146; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Berkas Pendaftaran Sidang Table (Dinamis dari Master Syarat) -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/40 overflow-hidden mb-6">
            
            <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                <h3 class="text-xs sm:text-sm font-extrabold text-slate-800 flex items-center gap-2">
                    <i class="bi bi-files text-orange-600"></i> Berkas Persyaratan Pendaftaran Sidang (<?= count($berkas); ?> Dokumen)
                </h3>
                <a href="<?= site_url('adminlayanan/pendaftaran_sidang'); ?>?manage_syarat=1" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-orange-50 hover:bg-orange-100 text-orange-700 text-xs font-bold border border-orange-200 shadow-xs transition">
                    <i class="bi bi-file-earmark-diff-fill text-sm"></i>
                    <span>Ubah / Kelola Syarat Berkas</span>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/60 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                            <th class="py-3.5 px-4 text-center w-12">#</th>
                            <th class="py-3.5 px-5">Nama File</th>
                            <th class="py-3.5 px-4">File Terlampir</th>
                            <th class="py-3.5 px-4 text-center">Lihat</th>
                            <th class="py-3.5 px-4 text-center">Action</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        <?php foreach ($berkas as $bk): 
                            $isValid = $bk['is_valid'];
                        ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- # -->
                                <td class="py-4 px-4 text-center font-bold text-slate-400"><?= $bk['no']; ?></td>

                                <!-- Nama File -->
                                <td class="py-4 px-5 font-bold text-slate-800">
                                    <?= htmlspecialchars($bk['nama_file']); ?>
                                </td>

                                <!-- File Terlampir -->
                                <td class="py-4 px-4 font-mono text-[11px] text-slate-500 truncate max-w-[180px]">
                                    <?= htmlspecialchars($bk['file_name']); ?>
                                </td>

                                <!-- Tombol Lihat (Fast Switch & Preview) -->
                                <td class="py-4 px-4 text-center">
                                    <button type="button" 
                                            id="btn_lihat_<?= $bk['kode']; ?>"
                                            onclick="toggleBerkasPreview('<?= $bk['kode']; ?>')" 
                                            class="btn-lihat-berkas inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-orange-50 hover:text-orange-700 text-slate-700 text-[11px] font-bold shadow-2xs transition cursor-pointer">
                                        <i class="bi bi-eye"></i> <span>Lihat</span>
                                    </button>
                                </td>

                                <!-- Action Buttons (Approve / Reject) -->
                                <td class="py-4 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" onclick="updateBerkasStatus('<?= $detail['nim']; ?>', '<?= $bk['kode']; ?>', 'Disetujui Admin LAA', this)"
                                                class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-[11px] font-bold transition cursor-pointer">
                                            <i class="bi bi-check-lg"></i> ACC
                                        </button>
                                        <button type="button" onclick="openModalRevisiBerkas('<?= $detail['nim']; ?>', '<?= $bk['kode']; ?>', '<?= htmlspecialchars($bk['nama_file'], ENT_QUOTES); ?>', this)"
                                                class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-[11px] font-bold transition cursor-pointer">
                                            <i class="bi bi-x-lg"></i> Tolak
                                        </button>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="py-4 px-4 text-center" id="status_cell_<?= $bk['kode']; ?>">
                                    <?php 
                                        $stClean = strtolower($bk['status']);
                                        $isApproved = (strpos($stClean, 'setuju') !== false || strpos($stClean, 'valid') !== false || strpos($stClean, 'approved') !== false);
                                        $isRejected = (strpos($stClean, 'revisi') !== false || strpos($stClean, 'tolak') !== false || strpos($stClean, 'rejected') !== false || strpos($stClean, 'invalid') !== false);
                                    ?>
                                    <?php if ($isApproved): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="bi bi-check-circle-fill"></i> Disetujui Admin LAA
                                        </span>
                                    <?php elseif ($isRejected): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <i class="bi bi-x-circle-fill"></i> Revisi Admin LAA
                                        </span>
                                        <?php if (!empty($bk['catatan'])): ?>
                                            <span class="text-[10px] text-rose-600 font-medium block mt-1 italic max-w-[170px] truncate mx-auto" title="<?= htmlspecialchars($bk['catatan']); ?>">
                                                "<?= htmlspecialchars($bk['catatan']); ?>"
                                            </span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <i class="bi bi-clock-fill"></i> Pending Verifikasi
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Global Verification Action Bar -->
            <div class="p-4 sm:p-6 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-slate-500 font-medium">
                    Pastikan seluruh <?= count($berkas); ?> berkas telah dicek dan valid sebelum menyetujui pendaftaran sidang mahasiswa.
                </p>
                <form action="<?= site_url('adminlayanan/submit_approval_sidang/' . $detail['nim']); ?>" method="POST" class="flex gap-2">
                    <button type="submit" name="status" value="Disetujui Admin LAA" class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-1.5 cursor-pointer">
                        <i class="bi bi-check2-circle text-sm"></i> Setujui Seluruh Berkas Sidang
                    </button>
                </form>
            </div>

        </div>

    </main>

    <!-- Floating Non-Blocking Container: Preview Berkas Persyaratan (Multiple Concurrent Floating Windows - Left Docked Vertical Stack) -->
    <div id="floatingBerkasContainer" class="fixed bottom-0 left-16 sm:left-20 top-16 pointer-events-none z-[100000] p-3 sm:p-5 flex flex-col items-start justify-start gap-4 overflow-y-auto max-h-[calc(100vh-4rem)] scroll-smooth" style="display: none; max-width: 65vw;">
        <!-- Dynamic Floating Windows will be rendered here vertically stacked -->
    </div>

    <!-- Modal Catatan Revisi Berkas Sidang -->
    <div id="modalRevisiBerkasSidang" class="fixed inset-0 z-[100000] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 hidden transition-all duration-300">
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden transform scale-95 transition-all duration-300">
            <!-- Header -->
            <div class="p-4 px-6 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-rose-500/20 border border-rose-500/40 text-rose-400 flex items-center justify-center font-bold text-lg">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">Alasan Revisi Berkas</h3>
                        <p id="modalRevisiFileName" class="text-[11px] text-slate-300 truncate max-w-[280px]">Nama Berkas</p>
                    </div>
                </div>
                <button type="button" onclick="closeModalRevisiBerkas()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-sm transition cursor-pointer">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Quick Tag Alasan Revisi:</label>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="setQuickRevisiNote('File Buram / Tidak Terbaca')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-orange-100 text-slate-700 hover:text-orange-800 text-[11px] font-semibold border border-slate-200 transition cursor-pointer">
                            📄 File Buram
                        </button>
                        <button type="button" onclick="setQuickRevisiNote('Dokumen Tidak Sesuai Syarat')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-orange-100 text-slate-700 hover:text-orange-800 text-[11px] font-semibold border border-slate-200 transition cursor-pointer">
                            📋 Dokumen Tidak Sesuai
                        </button>
                        <button type="button" onclick="setQuickRevisiNote('Tanda Tangan / Stempel Belum Lengkap')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-orange-100 text-slate-700 hover:text-orange-800 text-[11px] font-semibold border border-slate-200 transition cursor-pointer">
                            ✍️ TTD / Stempel Kurang
                        </button>
                        <button type="button" onclick="setQuickRevisiNote('Masa Berlaku Dokumen Kedaluwarsa')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-orange-100 text-slate-700 hover:text-orange-800 text-[11px] font-semibold border border-slate-200 transition cursor-pointer">
                            ⏳ Kedaluwarsa
                        </button>
                        <button type="button" onclick="setQuickRevisiNote('Halaman Dokumen Tidak Lengkap')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-orange-100 text-slate-700 hover:text-orange-800 text-[11px] font-semibold border border-slate-200 transition cursor-pointer">
                            📑 Halaman Kurang
                        </button>
                    </div>
                </div>

                <div>
                    <label for="modalRevisiInputNote" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Catatan Detail untuk Mahasiswa:</label>
                    <textarea id="modalRevisiInputNote" rows="3" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none transition" placeholder="Tuliskan detail perbaikan berkas yang perlu dilakukan mahasiswa..."></textarea>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-4 px-6 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModalRevisiBerkas()" class="px-4 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 font-bold text-xs transition cursor-pointer">
                    Batal
                </button>
                <button type="button" id="btnSubmitModalRevisi" onclick="submitModalRevisiBerkas()" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer">
                    <i class="bi bi-send-fill"></i> Simpan Catatan & Tolak
                </button>
            </div>
        </div>
    </div>

    <script>
        // =====================================================================
        // MULTIPLE FLOATING BERKAS PREVIEW SYSTEM (TRUE CONCURRENT MULTI-WINDOW)
        // =====================================================================
        window.ALL_BERKAS = <?= json_encode($berkas); ?>;
        window.CURRENT_MHS = <?= json_encode($detail); ?>;
        window.activeBerkasDocs = []; // [ { kode, isMinimized } ]

        function updateLayoutSplitView() {
            const mainContainer = document.getElementById('mainDetailContainer');
            if (!mainContainer) return;
            mainContainer.style.maxWidth = '';
            mainContainer.style.marginLeft = '';
            mainContainer.style.marginRight = '';
        }

        window.addEventListener('resize', updateLayoutSplitView);

        function toggleBerkasPreview(kode) {
            const existingIdx = window.activeBerkasDocs.findIndex(d => d.kode === kode);

            if (existingIdx > -1) {
                // If currently open, close this specific preview
                closeFloatingBerkas(kode);
            } else {
                // Open a NEW separate floating card side-by-side!
                window.activeBerkasDocs.push({
                    kode: kode,
                    isMinimized: false,
                    customLeft: null,
                    customTop: null
                });
                renderFloatingBerkas();
                highlightBerkasCard(kode);
                updateTableButtonHighlights();
                updateLayoutSplitView();
            }
        }

        function switchActiveBerkas(targetKode, currentKode) {
            const doc = window.activeBerkasDocs.find(d => d.kode === currentKode);
            if (doc) {
                doc.kode = targetKode;
                renderFloatingBerkas();
                highlightBerkasCard(targetKode);
                updateTableButtonHighlights();
                updateLayoutSplitView();
            }
        }

        function stepBerkas(direction, currentKode) {
            const list = window.ALL_BERKAS || [];
            if (!list.length) return;
            const currentIndex = list.findIndex(b => b.kode === currentKode);
            if (currentIndex === -1) return;
            let nextIndex = currentIndex + direction;
            if (nextIndex < 0) nextIndex = list.length - 1;
            if (nextIndex >= list.length) nextIndex = 0;
            switchActiveBerkas(list[nextIndex].kode, currentKode);
        }

        function toggleMinimizeBerkas(kode) {
            const doc = window.activeBerkasDocs.find(d => d.kode === kode);
            if (doc) {
                doc.isMinimized = !doc.isMinimized;
                renderFloatingBerkas();
                updateLayoutSplitView();
            }
        }

        function dockBerkasCard(kode, position) {
            const doc = window.activeBerkasDocs.find(d => d.kode === kode);
            const card = document.getElementById('berkasCard_' + kode);
            if (!doc || !card) return;

            card.style.position = 'fixed';
            card.style.bottom = '16px';
            card.style.top = 'auto';

            if (position === 'left') {
                card.style.left = '20px';
                card.style.right = 'auto';
                doc.customLeft = 20;
                doc.customTop = null;
            } else {
                card.style.right = '20px';
                card.style.left = 'auto';
                doc.customLeft = null;
                doc.customTop = null;
            }
        }

        function closeFloatingBerkas(kode) {
            window.activeBerkasDocs = window.activeBerkasDocs.filter(d => d.kode !== kode);
            renderFloatingBerkas();
            updateTableButtonHighlights();
            updateLayoutSplitView();
        }

        function closeAllFloatingBerkas() {
            window.activeBerkasDocs = [];
            renderFloatingBerkas();
            updateTableButtonHighlights();
            updateLayoutSplitView();
        }

        function highlightBerkasCard(kode) {
            setTimeout(() => {
                const el = document.getElementById('berkasCard_' + kode);
                if (el) {
                    el.scrollIntoView({ behavior: 'smooth', inline: 'end', block: 'nearest' });
                    el.classList.add('ring-4', 'ring-orange-400', 'scale-[1.01]');
                    setTimeout(() => el.classList.remove('ring-4', 'ring-orange-400', 'scale-[1.01]'), 800);
                }
            }, 50);
        }

        function updateTableButtonHighlights() {
            const openKodes = (window.activeBerkasDocs || []).map(d => d.kode);
            document.querySelectorAll('.btn-lihat-berkas').forEach(btn => {
                const kode = btn.id.replace('btn_lihat_', '');
                if (openKodes.includes(kode)) {
                    btn.className = 'btn-lihat-berkas inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-orange-600 text-white font-bold text-[11px] shadow-md ring-2 ring-orange-400 scale-105 transition cursor-pointer';
                    btn.innerHTML = '<i class="bi bi-x-circle-fill"></i> <span>Tutup</span>';
                    btn.title = 'Tutup Pratinjau Ini';
                } else {
                    btn.className = 'btn-lihat-berkas inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-orange-50 hover:text-orange-700 text-slate-700 text-[11px] font-bold shadow-2xs transition cursor-pointer';
                    btn.innerHTML = '<i class="bi bi-eye"></i> <span>Lihat</span>';
                    btn.title = 'Pratinjau Berkas';
                }
            });
        }

        function renderFloatingBerkas() {
            const container = document.getElementById('floatingBerkasContainer');
            if (!container) return;

            if (window.activeBerkasDocs.length === 0) {
                container.style.display = 'none';
                container.innerHTML = '';
                return;
            }

            container.style.display = 'flex';
            const total = window.activeBerkasDocs.length;
            const mhs = window.CURRENT_MHS || {};

            let html = '';

            // Summary Pill if multiple open
            if (total > 1) {
                html += `
                    <div class="pointer-events-auto shrink-0 self-start mb-1 bg-slate-900/95 backdrop-blur text-white px-3.5 py-1.5 rounded-2xl shadow-xl border border-slate-700 flex items-center gap-2 text-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="font-bold">${total} Berkas Dibuka</span>
                        <button type="button" onclick="closeAllFloatingBerkas()" class="ml-1 px-2 py-0.5 rounded-lg bg-rose-600/80 hover:bg-rose-600 text-white font-bold text-[10px] transition cursor-pointer">
                            Tutup Semua
                        </button>
                    </div>
                `;
            }

            window.activeBerkasDocs.forEach((d, cardIdx) => {
                const berkasInfo = (window.ALL_BERKAS || []).find(b => b.kode === d.kode) || {
                    kode: d.kode,
                    nama_file: 'Berkas ' + d.kode,
                    file_url: '',
                    is_valid: false,
                    no: 1
                };

                const posStyle = '';

                if (d.isMinimized) {
                    html += `
                        <div id="berkasCard_${d.kode}" style="${posStyle}" class="pointer-events-auto bg-slate-900 text-white rounded-2xl shadow-2xl border border-slate-700 shrink-0 w-72 p-2.5 flex items-center justify-between gap-2 transition-all hover:border-orange-500 cursor-pointer" onclick="toggleMinimizeBerkas('${d.kode}')">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-6 h-6 rounded-lg bg-orange-600/30 border border-orange-500/40 text-orange-400 flex items-center justify-center text-xs shrink-0">
                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold truncate max-w-[150px]">${berkasInfo.nama_file}</h4>
                                    <p class="text-[9px] text-slate-400 truncate">${mhs.nim || ''}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 shrink-0" onclick="event.stopPropagation()">
                                <button type="button" onclick="toggleMinimizeBerkas('${d.kode}')" class="w-6 h-6 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer" title="Perbesar">
                                    <i class="bi bi-arrows-angle-expand text-[10px]"></i>
                                </button>
                                <button type="button" onclick="closeFloatingBerkas('${d.kode}')" class="w-6 h-6 rounded-lg bg-white/10 hover:bg-rose-600 text-slate-300 hover:text-white flex items-center justify-center text-xs transition ml-0.5 cursor-pointer" title="Tutup">
                                    <i class="bi bi-x-lg text-[10px]"></i>
                                </button>
                            </div>
                        </div>
                    `;
                } else {
                    // Build Rapid Switcher Pill Tabs for all files!
                    const navPillsHtml = (window.ALL_BERKAS || []).map((b, idx) => {
                        const isCurrentTab = b.kode === d.kode;
                        const statusDot = b.is_valid 
                            ? '<span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>' 
                            : '<span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>';

                        const tabStyle = isCurrentTab 
                            ? 'bg-orange-600 text-white font-extrabold shadow-sm ring-1 ring-orange-300' 
                            : 'bg-slate-800 text-slate-300 hover:bg-slate-700 hover:text-white font-semibold';

                        return `
                            <button type="button" 
                                    onclick="switchActiveBerkas('${b.kode}', '${d.kode}')"
                                    class="px-2 py-1 rounded-lg text-[10px] flex items-center gap-1 shrink-0 transition-all cursor-pointer whitespace-nowrap ${tabStyle}"
                                    title="${b.nama_file}">
                                ${statusDot}
                                <span>${idx + 1}. ${b.nama_file.length > 15 ? b.nama_file.substring(0, 15) + '…' : b.nama_file}</span>
                            </button>
                        `;
                    }).join('');

                    html += `
                        <div id="berkasCard_${d.kode}" style="${posStyle}" class="pointer-events-auto bg-white rounded-3xl shadow-2xl border border-slate-300 flex flex-col overflow-hidden shrink-0 transition-shadow duration-200 w-[92vw] sm:w-[460px] xl:w-[500px] h-[540px] max-h-[78vh]">
                            <!-- Static Header -->
                            <div id="berkasHeader_${d.kode}" class="p-2.5 px-3.5 bg-slate-900 text-white flex items-center justify-between gap-2 shrink-0 border-b border-slate-800 select-none">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-6 h-6 rounded-lg bg-orange-600/30 border border-orange-500/50 text-orange-400 flex items-center justify-center font-bold text-xs shrink-0">
                                        <i class="bi bi-file-earmark-pdf-fill text-[11px]"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-bold text-white truncate max-w-[170px] sm:max-w-[210px]">${berkasInfo.nama_file}</h4>
                                        <p class="text-[10px] text-slate-300 truncate">${mhs.nama || ''} · <span class="font-mono text-orange-400">${mhs.nim || ''}</span></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 shrink-0" onclick="event.stopPropagation()">
                                    <a href="${berkasInfo.file_url}" target="_blank" class="w-6 h-6 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer" title="Buka Dokumen di Tab Baru">
                                        <i class="bi bi-arrow-up-right-square text-[10px]"></i>
                                    </a>
                                    <button type="button" onclick="toggleMinimizeBerkas('${d.kode}')" class="w-6 h-6 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer" title="Minimize (Ciutkan)">
                                        <i class="bi bi-dash-lg text-[10px] font-bold"></i>
                                    </button>
                                    <button type="button" onclick="closeFloatingBerkas('${d.kode}')" class="w-6 h-6 rounded-lg bg-white/10 hover:bg-rose-600 text-slate-300 hover:text-white flex items-center justify-center text-xs font-bold transition ml-0.5 cursor-pointer" title="Tutup">
                                        <i class="bi bi-x-lg text-[10px]"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Fast Navigation Pill Strip ("Tek Tek Tek" Bar) -->
                            <div class="bg-slate-950 p-1.5 px-2 flex items-center gap-1.5 overflow-x-auto border-b border-slate-800 shrink-0 scrollbar-thin">
                                <button type="button" onclick="stepBerkas(-1, '${d.kode}')" class="w-5 h-5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center text-[10px] shrink-0 cursor-pointer" title="Dokumen Sebelumnya (←)">
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                                <div class="flex items-center gap-1 overflow-x-auto py-0.5 flex-1">
                                    ${navPillsHtml}
                                </div>
                                <button type="button" onclick="stepBerkas(1, '${d.kode}')" class="w-5 h-5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center text-[10px] shrink-0 cursor-pointer" title="Dokumen Selanjutnya (→)">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>

                            <!-- Body (Iframe) -->
                            <div class="flex-1 bg-slate-100 p-1.5 overflow-hidden flex flex-col relative">
                                ${(berkasInfo.status && (berkasInfo.status.toLowerCase().includes('revisi') || berkasInfo.status.toLowerCase().includes('tolak')) && berkasInfo.catatan) ? `
                                    <div class="mb-1.5 p-2 px-3 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-800 flex items-start gap-2 shrink-0">
                                        <i class="bi bi-exclamation-octagon-fill text-rose-600 text-sm shrink-0 mt-0.5"></i>
                                        <div class="min-w-0 flex-1">
                                            <span class="font-bold block text-[11px] text-rose-900">Catatan Revisi:</span>
                                            <p class="text-[11px] leading-tight text-rose-700 italic">"${berkasInfo.catatan}"</p>
                                        </div>
                                    </div>
                                ` : ''}
                                <iframe src="${berkasInfo.file_url}" class="w-full h-full bg-white rounded-2xl shadow-inner border border-slate-200" frameborder="0"></iframe>
                            </div>

                            <!-- Footer (Action Buttons) -->
                            <div class="p-2 px-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs shrink-0">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" onclick="updateBerkasStatus('${mhs.nim}', '${d.kode}', 'Disetujui Admin LAA', '', this)"
                                            class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-2xs transition flex items-center gap-1 cursor-pointer">
                                        <i class="bi bi-check-lg"></i> ACC
                                    </button>
                                    <button type="button" onclick="openModalRevisiBerkas('${mhs.nim}', '${d.kode}', '${berkasInfo.nama_file.replace(/'/g, "\\'")}', this)"
                                            class="px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-[11px] shadow-2xs transition flex items-center gap-1 cursor-pointer">
                                        <i class="bi bi-x-lg"></i> Tolak
                                    </button>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <a href="${berkasInfo.file_url}" download target="_blank" class="px-2.5 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-600 border border-slate-200 font-bold text-[11px] transition flex items-center gap-1 cursor-pointer" title="Unduh File">
                                        <i class="bi bi-download text-[10px]"></i> Unduh
                                    </a>
                                    <button type="button" onclick="closeFloatingBerkas('${d.kode}')" class="px-2.5 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-600 border border-slate-200 font-bold text-[11px] transition cursor-pointer">
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                }
            });

            container.innerHTML = html;
            initDraggableBerkasCards();
        }

        function initDraggableBerkasCards() {}

        // Keyboard Arrow Navigation (← / → to switch files rapidly)
        document.addEventListener('keydown', function(e) {
            if (window.activeBerkasDocs && window.activeBerkasDocs.length > 0) {
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
                const activeCard = window.activeBerkasDocs[0];
                if (activeCard && !activeCard.isMinimized) {
                    if (e.key === 'ArrowLeft') {
                        e.preventDefault();
                        stepBerkas(-1, activeCard.kode);
                    } else if (e.key === 'ArrowRight') {
                        e.preventDefault();
                        stepBerkas(1, activeCard.kode);
                    }
                }
            }
        });

        function showToastNotification(title, msg, type = 'success') {
            let toastBox = document.getElementById('toastNotificationBox');
            if (!toastBox) {
                toastBox = document.createElement('div');
                toastBox.id = 'toastNotificationBox';
                toastBox.className = 'fixed top-5 right-5 z-[100000] flex flex-col gap-2 pointer-events-none';
                document.body.appendChild(toastBox);
            }

            const iconClass = type === 'success' ? 'bi-check-circle-fill text-emerald-400' : 'bi-exclamation-triangle-fill text-rose-400';
            const borderClass = type === 'success' ? 'border-emerald-500/30' : 'border-rose-500/30';

            const toast = document.createElement('div');
            toast.className = `pointer-events-auto bg-slate-900/95 backdrop-blur text-white p-3.5 px-4 rounded-2xl shadow-2xl border ${borderClass} flex items-center gap-3 text-xs transition-all duration-300 transform translate-y-2 opacity-0 max-w-sm`;
            toast.innerHTML = `
                <i class="bi ${iconClass} text-lg shrink-0"></i>
                <div class="min-w-0 flex-1">
                    <h5 class="font-bold text-white text-xs">${title}</h5>
                    <p class="text-[11px] text-slate-300 truncate">${msg}</p>
                </div>
            `;

            toastBox.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            }, 10);

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // =====================================================================
        // REVISION MODAL & STATUS UPDATE SYSTEM
        // =====================================================================
        let currentRevisiContext = null;

        function openModalRevisiBerkas(nim, kodeBerkas, namaFile, btnEl) {
            currentRevisiContext = { nim, kodeBerkas, namaFile, btnEl };
            const modal = document.getElementById('modalRevisiBerkasSidang');
            const nameEl = document.getElementById('modalRevisiFileName');
            const noteInput = document.getElementById('modalRevisiInputNote');
            
            const berkas = (window.ALL_BERKAS || []).find(b => b.kode === kodeBerkas);
            if (noteInput) {
                noteInput.value = (berkas && berkas.catatan) ? berkas.catatan : '';
            }
            if (nameEl) {
                nameEl.textContent = namaFile || ('Dokumen (' + kodeBerkas + ')');
            }
            if (modal) {
                modal.classList.remove('hidden');
                const dialog = modal.firstElementChild;
                setTimeout(() => {
                    dialog.classList.remove('scale-95');
                    dialog.classList.add('scale-100');
                }, 10);
            }
        }

        function closeModalRevisiBerkas() {
            const modal = document.getElementById('modalRevisiBerkasSidang');
            if (modal) {
                const dialog = modal.firstElementChild;
                dialog.classList.remove('scale-100');
                dialog.classList.add('scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    currentRevisiContext = null;
                }, 200);
            }
        }

        function setQuickRevisiNote(text) {
            const noteInput = document.getElementById('modalRevisiInputNote');
            if (noteInput) {
                if (noteInput.value.trim().length > 0) {
                    noteInput.value += '; ' + text;
                } else {
                    noteInput.value = text;
                }
                noteInput.focus();
            }
        }

        function submitModalRevisiBerkas() {
            if (!currentRevisiContext) return;
            const noteInput = document.getElementById('modalRevisiInputNote');
            const catatan = noteInput ? noteInput.value.trim() : '';
            
            const { nim, kodeBerkas, btnEl } = currentRevisiContext;
            closeModalRevisiBerkas();
            updateBerkasStatus(nim, kodeBerkas, 'Revisi Admin LAA', catatan, btnEl);
        }

        function updateBerkasStatus(nim, kodeBerkas, status, catatan = '', btnEl) {
            if (catatan && typeof catatan === 'object' && catatan.nodeType) {
                btnEl = catatan;
                catatan = '';
            }

            const button = btnEl || (typeof window !== 'undefined' && window.event ? window.event.currentTarget : null);
            if (button) {
                button.disabled = true;
                button.classList.add('opacity-50', 'cursor-not-allowed');
            }

            const params = new URLSearchParams();
            params.append('nim', nim);
            params.append('kode_berkas', kodeBerkas);
            params.append('status', status);
            params.append('catatan', catatan || '');

            fetch('<?= site_url('adminlayanan/ajax_update_berkas_sidang'); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body: params.toString()
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    const berkas = (window.ALL_BERKAS || []).find(b => b.kode === kodeBerkas);
                    const stClean = status.toLowerCase();
                    const isApproved = stClean.includes('disetujui') || stClean.includes('approved') || stClean.includes('acc');
                    const isRejected = stClean.includes('revisi') || stClean.includes('tolak') || stClean.includes('rejected');
                    
                    if (berkas) {
                        berkas.is_valid = isApproved;
                        berkas.status = status;
                        berkas.catatan = catatan;
                    }

                    const statusCell = document.getElementById('status_cell_' + kodeBerkas);
                    if (statusCell) {
                        if (isApproved) {
                            statusCell.innerHTML = `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"><i class="bi bi-check-circle-fill"></i> Disetujui Admin LAA</span>`;
                        } else if (isRejected) {
                            let catHtml = '';
                            if (catatan) {
                                const escapedCat = catatan.replace(/"/g, '&quot;');
                                catHtml = `<span class="text-[10px] text-rose-600 font-medium block mt-1 italic max-w-[170px] truncate mx-auto" title="${escapedCat}">"${catatan}"</span>`;
                            }
                            statusCell.innerHTML = `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200"><i class="bi bi-x-circle-fill"></i> Revisi Admin LAA</span>${catHtml}`;
                        } else {
                            statusCell.innerHTML = `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200"><i class="bi bi-clock-fill"></i> Pending Verifikasi</span>`;
                        }
                    }

                    const overallBadgeEl = document.getElementById('overallStatusBadgeSidang');
                    if (overallBadgeEl) {
                        const allValidLocal = (window.ALL_BERKAS || []).length > 0 && (window.ALL_BERKAS || []).every(b => b.is_valid);
                        if (res.is_approved || allValidLocal) {
                            overallBadgeEl.innerHTML = `<span class="px-3.5 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200 flex items-center gap-1.5 shadow-xs"><i class="bi bi-check-circle-fill text-emerald-500"></i> Status: Disetujui Admin LAA</span>`;
                        } else if ((res.overall_status || '').toLowerCase().includes('revisi')) {
                            overallBadgeEl.innerHTML = `<span class="px-3.5 py-1.5 rounded-xl bg-rose-50 text-rose-700 text-xs font-bold border border-rose-200 flex items-center gap-1.5 shadow-xs"><i class="bi bi-x-circle-fill text-rose-500"></i> Status: Revisi Admin LAA</span>`;
                        } else {
                            overallBadgeEl.innerHTML = `<span class="px-3.5 py-1.5 rounded-xl bg-amber-50 text-amber-700 text-xs font-bold border border-amber-200 flex items-center gap-1.5 shadow-xs"><i class="bi bi-clock-fill text-amber-500"></i> Status: Pending Verifikasi</span>`;
                        }
                    }

                    renderFloatingBerkas();
                    showToastNotification('Berhasil!', res.message || ('Status berkas diupdate: ' + status), 'success');

                    if (button) {
                        button.disabled = false;
                        button.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                } else {
                    if (button) {
                        button.disabled = false;
                        button.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                    alert(res.message || 'Gagal mengubah status berkas.');
                }
            })
            .catch(err => {
                if (button) {
                    button.disabled = false;
                    button.classList.remove('opacity-50', 'cursor-not-allowed');
                }
                console.error('Update error:', err);
                alert('Terjadi kesalahan koneksi saat mengupdate status berkas.');
            });
        }
    </script>
</body>
</html>
