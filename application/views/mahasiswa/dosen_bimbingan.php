<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard Bimbingan Dosen — IFIK Portal'; ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>?v=<?= time(); ?>">
    <style>
        body, button, input, textarea, select {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-amber-50/40 via-orange-50/25 to-slate-100 min-h-screen text-slate-800 antialiased flex flex-col justify-between selection:bg-orange-500 selection:text-white">

    <?php $this->load->view('partials/mahasiswa_navbar'); ?>

    <main class="w-full px-4 sm:px-6 lg:px-10 py-6 sm:py-8 flex-grow space-y-7">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="p-5 rounded-3xl bg-emerald-50 border-2 border-emerald-300 text-emerald-900 text-sm font-semibold flex items-center justify-between shadow-md shadow-emerald-500/10">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg shrink-0 box-3d">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <span><?= $this->session->flashdata('success'); ?></span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold text-2xl leading-none">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Hero Card Dosen -->
        <div class="bg-gradient-to-r from-[#9a3412] via-[#ea580c] to-[#c2410c] rounded-3xl p-7 sm:p-9 relative overflow-hidden shadow-2xl text-white">
            <div class="relative z-10 flex flex-col xl:flex-row items-start xl:items-center justify-between gap-8">
                <div class="space-y-4 max-w-3xl">
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                        Dashboard Bimbingan Dosen
                    </h1>
                    <p class="text-sm sm:text-base text-orange-100/95 font-normal leading-relaxed">
                        Kelola dan evaluasi dokumen Tugas Akhir mahasiswa bimbingan Anda.
                    </p>
                </div>
                <div class="w-full xl:w-[400px] bg-black/25 backdrop-blur-xl rounded-3xl p-6 border border-white/20 shadow-2xl space-y-4 text-white">
                    <div class="flex gap-4">
                        <a href="<?= site_url('mahasiswa/bimbingan?posisi=1') ?>" class="flex-1 py-3 px-4 rounded-2xl font-bold text-center border <?= $posisi == 1 ? 'bg-orange-500 border-orange-400 text-white shadow-lg' : 'bg-white/10 border-white/20 hover:bg-white/20' ?> transition">
                            <i class="bi bi-person-fill mr-2"></i> Sebagai P1
                        </a>
                        <a href="<?= site_url('mahasiswa/bimbingan?posisi=2') ?>" class="flex-1 py-3 px-4 rounded-2xl font-bold text-center border <?= $posisi == 2 ? 'bg-orange-500 border-orange-400 text-white shadow-lg' : 'bg-white/10 border-white/20 hover:bg-white/20' ?> transition">
                            <i class="bi bi-person mr-2"></i> Sebagai P2
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-7 sm:p-9 shadow-lg shadow-orange-500/5 space-y-7 border border-slate-200">
            <div class="flex flex-wrap items-center justify-between border-b border-orange-100 pb-5">
                <div>
                    <h3 class="text-xl sm:text-2xl font-bold text-slate-900">
                        <i class="bi bi-people-fill text-orange-500 text-xl"></i> Daftar Mahasiswa Bimbingan (Pembimbing <?= $posisi ?>)
                    </h3>
                </div>
            </div>

            <!-- Tabs -->
            <div class="flex flex-wrap gap-4 border-b border-slate-200 pb-4">
                <button onclick="switchDosenTab('preview1')" id="dosenTab1" class="px-5 py-2.5 rounded-xl font-bold text-sm bg-orange-100 text-orange-700 border border-orange-300">Preview 1</button>
                <button onclick="switchDosenTab('preview2')" id="dosenTab2" class="px-5 py-2.5 rounded-xl font-bold text-sm bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200">Preview 2</button>
                <button onclick="switchDosenTab('preview3')" id="dosenTab3" class="px-5 py-2.5 rounded-xl font-bold text-sm bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200">Preview 3</button>
            </div>

            <!-- Content Preview 1 -->
            <div id="dosenContent1" class="space-y-6">
                <?php foreach($students as $mhs): ?>
                <div class="border border-slate-200 rounded-2xl p-5 bg-slate-50/50">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h4 class="font-bold text-lg text-slate-800"><?= htmlspecialchars($mhs['nama_mahasiswa']) ?> (<?= $mhs['nim'] ?>)</h4>
                            <p class="text-xs text-slate-500 font-bold uppercase mt-1">Judul: <?= htmlspecialchars($mhs['judul']) ?></p>
                        </div>
                    </div>
                    <?php if(!empty($mhs['preview1'])): ?>
                        <?php $latest = $mhs['preview1'][0]; ?>
                        <div class="bg-white p-4 rounded-xl border border-slate-200 space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-bold text-slate-700">Berkas Terbaru: <a href="<?= base_url('uploads/preview_ta/' . $latest['file_draft']) ?>" target="_blank" class="text-orange-600 hover:underline"><?= htmlspecialchars($latest['file_draft']) ?></a></span>
                                <span class="text-xs font-bold px-3 py-1 bg-slate-100 rounded-lg"><?= date('d M Y, H:i', strtotime($latest['created_at'])) ?></span>
                            </div>
                            <p class="text-sm text-slate-600 italic">"<?= htmlspecialchars($latest['catatan_mahasiswa']) ?>"</p>
                            
                            <hr class="border-slate-100">
                            
                            <!-- Form Review -->
                            <form action="<?= site_url('mahasiswa/review_preview') ?>" method="POST" class="space-y-4">
                                <input type="hidden" name="id_preview" value="<?= $latest['id'] ?>">
                                <input type="hidden" name="posisi" value="<?= $posisi ?>">
                                
                                <?php if($posisi == 1): ?>
                                    <!-- Menampilkan Catatan Sebelumnya & Status "Apakah Revisi Sudah Dikerjakan?" -->
                                    <div class="bg-blue-50 p-4 rounded-xl border border-blue-200">
                                        <p class="text-sm font-bold text-blue-900 mb-1"><i class="bi bi-info-circle-fill"></i> Riwayat & Pengecekan Revisi</p>
                                        <p class="text-xs text-blue-800 mb-2">Pastikan mahasiswa telah mengerjakan revisi berdasarkan catatan yang Anda berikan sebelumnya.</p>
                                        <?php if(!empty($latest['catatan_pembimbing'])): ?>
                                            <div class="text-xs italic text-slate-600 p-2 bg-white rounded border border-blue-100 mt-2">
                                                <strong>Catatan Anda Sebelumnya:</strong><br>
                                                <?= htmlspecialchars($latest['catatan_pembimbing']) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="mt-3">
                                            <label class="flex items-center gap-2 text-sm font-bold text-slate-700">
                                                <input type="checkbox" required class="w-4 h-4 text-orange-500 rounded focus:ring-orange-500 border-slate-300">
                                                Apakah revisi ini sudah dikerjakan dengan baik?
                                            </label>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Status Penilaian (P1)</label>
                                        <select name="status_pembimbing" class="w-full p-3 rounded-xl border border-slate-300 focus:ring-orange-500 focus:border-orange-500 text-sm">
                                            <option value="Pending" <?= $latest['status_pembimbing'] == 'Pending' ? 'selected' : '' ?>>Menunggu Review</option>
                                            <option value="Approved" <?= $latest['status_pembimbing'] == 'Approved' ? 'selected' : '' ?>>Disetujui (ACC)</option>
                                            <option value="Revision" <?= $latest['status_pembimbing'] == 'Revision' ? 'selected' : '' ?>>Perlu Revisi</option>
                                        </select>
                                    </div>
                                <?php endif; ?>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Catatan / Feedback Anda</label>
                                    <textarea name="catatan_pembimbing" rows="3" class="w-full p-3 rounded-xl border border-slate-300 focus:ring-orange-500 focus:border-orange-500 text-sm"><?= htmlspecialchars($posisi == 1 ? ($latest['catatan_pembimbing'] ?? '') : ($latest['catatan_pembimbing_2'] ?? '')) ?></textarea>
                                </div>
                                <button type="submit" class="px-6 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-sm shadow-md">Simpan Review</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <p class="text-sm text-slate-500 italic">Belum ada file draft yang diunggah mahasiswa ini untuk Preview 1.</p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Content Preview 2 -->
            <div id="dosenContent2" class="space-y-6 hidden">
                <?php foreach($students as $mhs): ?>
                <div class="border border-slate-200 rounded-2xl p-5 bg-slate-50/50">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h4 class="font-bold text-lg text-slate-800"><?= htmlspecialchars($mhs['nama_mahasiswa']) ?> (<?= $mhs['nim'] ?>)</h4>
                        </div>
                    </div>
                    <?php if(!empty($mhs['preview2'])): ?>
                        <?php $latest = $mhs['preview2'][0]; ?>
                        <div class="bg-white p-4 rounded-xl border border-slate-200 space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-bold text-slate-700">Berkas Terbaru: <a href="<?= base_url('uploads/preview_ta/' . $latest['file_draft']) ?>" target="_blank" class="text-orange-600 hover:underline"><?= htmlspecialchars($latest['file_draft']) ?></a></span>
                                <span class="text-xs font-bold px-3 py-1 bg-slate-100 rounded-lg"><?= date('d M Y, H:i', strtotime($latest['created_at'])) ?></span>
                            </div>
                            <p class="text-sm text-slate-600 italic">"<?= htmlspecialchars($latest['catatan_mahasiswa']) ?>"</p>
                            <hr class="border-slate-100">
                            <!-- Form Review -->
                            <form action="<?= site_url('mahasiswa/review_preview') ?>" method="POST" class="space-y-4">
                                <input type="hidden" name="id_preview" value="<?= $latest['id'] ?>">
                                <input type="hidden" name="posisi" value="<?= $posisi ?>">
                                
                                <?php if($posisi == 1): ?>
                                    <div class="bg-blue-50 p-4 rounded-xl border border-blue-200">
                                        <p class="text-sm font-bold text-blue-900 mb-1"><i class="bi bi-info-circle-fill"></i> Riwayat & Pengecekan Revisi</p>
                                        <p class="text-xs text-blue-800 mb-2">Pastikan mahasiswa telah mengerjakan revisi berdasarkan catatan yang Anda berikan sebelumnya.</p>
                                        <?php if(!empty($latest['catatan_pembimbing'])): ?>
                                            <div class="text-xs italic text-slate-600 p-2 bg-white rounded border border-blue-100 mt-2">
                                                <strong>Catatan Anda Sebelumnya:</strong><br>
                                                <?= htmlspecialchars($latest['catatan_pembimbing']) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="mt-3">
                                            <label class="flex items-center gap-2 text-sm font-bold text-slate-700">
                                                <input type="checkbox" required class="w-4 h-4 text-orange-500 rounded focus:ring-orange-500 border-slate-300">
                                                Apakah revisi ini sudah dikerjakan dengan baik?
                                            </label>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Status Penilaian (P1)</label>
                                        <select name="status_pembimbing" class="w-full p-3 rounded-xl border border-slate-300 focus:ring-orange-500 focus:border-orange-500 text-sm">
                                            <option value="Pending" <?= $latest['status_pembimbing'] == 'Pending' ? 'selected' : '' ?>>Menunggu Review</option>
                                            <option value="Approved" <?= $latest['status_pembimbing'] == 'Approved' ? 'selected' : '' ?>>Disetujui (ACC)</option>
                                            <option value="Revision" <?= $latest['status_pembimbing'] == 'Revision' ? 'selected' : '' ?>>Perlu Revisi</option>
                                        </select>
                                    </div>
                                <?php endif; ?>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Catatan / Feedback Anda</label>
                                    <textarea name="catatan_pembimbing" rows="3" class="w-full p-3 rounded-xl border border-slate-300 focus:ring-orange-500 focus:border-orange-500 text-sm"><?= htmlspecialchars($posisi == 1 ? ($latest['catatan_pembimbing'] ?? '') : ($latest['catatan_pembimbing_2'] ?? '')) ?></textarea>
                                </div>
                                <button type="submit" class="px-6 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-sm shadow-md">Simpan Review</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <p class="text-sm text-slate-500 italic">Belum ada file draft yang diunggah mahasiswa ini untuk Preview 2.</p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Content Preview 3 -->
            <div id="dosenContent3" class="space-y-6 hidden">
                <?php foreach($students as $mhs): ?>
                <div class="border border-slate-200 rounded-2xl p-5 bg-slate-50/50">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h4 class="font-bold text-lg text-slate-800"><?= htmlspecialchars($mhs['nama_mahasiswa']) ?> (<?= $mhs['nim'] ?>)</h4>
                        </div>
                    </div>
                    <?php if(!empty($mhs['preview3'])): ?>
                        <?php $latest = $mhs['preview3'][0]; ?>
                        <div class="bg-white p-4 rounded-xl border border-slate-200 space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-bold text-slate-700">Berkas Terbaru: <a href="<?= base_url('uploads/preview_ta/' . $latest['file_draft']) ?>" target="_blank" class="text-orange-600 hover:underline"><?= htmlspecialchars($latest['file_draft']) ?></a></span>
                                <span class="text-xs font-bold px-3 py-1 bg-slate-100 rounded-lg"><?= date('d M Y, H:i', strtotime($latest['created_at'])) ?></span>
                            </div>
                            <p class="text-sm text-slate-600 italic">"<?= htmlspecialchars($latest['catatan_mahasiswa']) ?>"</p>
                            <hr class="border-slate-100">
                            <!-- Form Review -->
                            <form action="<?= site_url('mahasiswa/review_preview') ?>" method="POST" class="space-y-4">
                                <input type="hidden" name="id_preview" value="<?= $latest['id'] ?>">
                                <input type="hidden" name="posisi" value="<?= $posisi ?>">
                                
                                <?php if($posisi == 1): ?>
                                    <div class="bg-blue-50 p-4 rounded-xl border border-blue-200">
                                        <p class="text-sm font-bold text-blue-900 mb-1"><i class="bi bi-info-circle-fill"></i> Riwayat & Pengecekan Revisi</p>
                                        <p class="text-xs text-blue-800 mb-2">Pastikan mahasiswa telah mengerjakan revisi berdasarkan catatan yang Anda berikan sebelumnya.</p>
                                        <?php if(!empty($latest['catatan_pembimbing'])): ?>
                                            <div class="text-xs italic text-slate-600 p-2 bg-white rounded border border-blue-100 mt-2">
                                                <strong>Catatan Anda Sebelumnya:</strong><br>
                                                <?= htmlspecialchars($latest['catatan_pembimbing']) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="mt-3">
                                            <label class="flex items-center gap-2 text-sm font-bold text-slate-700">
                                                <input type="checkbox" required class="w-4 h-4 text-orange-500 rounded focus:ring-orange-500 border-slate-300">
                                                Apakah revisi ini sudah dikerjakan dengan baik?
                                            </label>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Status Penilaian (P1)</label>
                                        <select name="status_pembimbing" class="w-full p-3 rounded-xl border border-slate-300 focus:ring-orange-500 focus:border-orange-500 text-sm">
                                            <option value="Pending" <?= $latest['status_pembimbing'] == 'Pending' ? 'selected' : '' ?>>Menunggu Review</option>
                                            <option value="Approved" <?= $latest['status_pembimbing'] == 'Approved' ? 'selected' : '' ?>>Disetujui (ACC)</option>
                                            <option value="Revision" <?= $latest['status_pembimbing'] == 'Revision' ? 'selected' : '' ?>>Perlu Revisi</option>
                                        </select>
                                    </div>
                                <?php endif; ?>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Catatan / Feedback Anda</label>
                                    <textarea name="catatan_pembimbing" rows="3" class="w-full p-3 rounded-xl border border-slate-300 focus:ring-orange-500 focus:border-orange-500 text-sm"><?= htmlspecialchars($posisi == 1 ? ($latest['catatan_pembimbing'] ?? '') : ($latest['catatan_pembimbing_2'] ?? '')) ?></textarea>
                                </div>
                                <button type="submit" class="px-6 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-sm shadow-md">Simpan Review</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <p class="text-sm text-slate-500 italic">Belum ada file draft yang diunggah mahasiswa ini untuk Preview 3.</p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

        </div>
    </main>

    <script>
        function switchDosenTab(tab) {
            document.getElementById('dosenContent1').classList.add('hidden');
            document.getElementById('dosenContent2').classList.add('hidden');
            document.getElementById('dosenContent3').classList.add('hidden');
            
            let btnClassInactive = 'px-5 py-2.5 rounded-xl font-bold text-sm bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200'.split(' ');
            let btnClassActive = 'px-5 py-2.5 rounded-xl font-bold text-sm bg-orange-100 text-orange-700 border border-orange-300'.split(' ');

            ['dosenTab1', 'dosenTab2', 'dosenTab3'].forEach(id => {
                let el = document.getElementById(id);
                el.classList.remove(...btnClassActive);
                el.classList.add(...btnClassInactive);
            });

            if(tab === 'preview1') {
                document.getElementById('dosenContent1').classList.remove('hidden');
                document.getElementById('dosenTab1').classList.remove(...btnClassInactive);
                document.getElementById('dosenTab1').classList.add(...btnClassActive);
            } else if(tab === 'preview2') {
                document.getElementById('dosenContent2').classList.remove('hidden');
                document.getElementById('dosenTab2').classList.remove(...btnClassInactive);
                document.getElementById('dosenTab2').classList.add(...btnClassActive);
            } else if(tab === 'preview3') {
                document.getElementById('dosenContent3').classList.remove('hidden');
                document.getElementById('dosenTab3').classList.remove(...btnClassInactive);
                document.getElementById('dosenTab3').classList.add(...btnClassActive);
            }
        }
    </script>
</body>
</html>
