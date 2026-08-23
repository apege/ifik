<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> - IFIK</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="<?= base_url('assets/css/style.css'); ?>" rel="stylesheet">
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased min-h-screen flex flex-col selection:bg-orange-500 selection:text-white relative">

    <!-- Header Glass Navbar -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-xl border-b border-slate-200/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 bg-gradient-to-tr from-slate-900 to-slate-800 text-white rounded-2xl font-bold text-xl flex items-center justify-center box-3d shadow-md">
                        K
                    </div>
                    <span class="font-bold text-lg text-slate-900 tracking-tight">Detail & Approval Koordinator TA</span>
                </div>
                <a href="<?= site_url('koordinatorta'); ?>" class="text-xs font-bold text-slate-700 hover:text-orange-600 bg-white hover:bg-slate-100 border border-slate-200 px-4 py-2.5 rounded-xl shadow-xs transition flex items-center gap-2">
                    <i class="bi bi-arrow-left text-sm"></i> Dashboard
                </a>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full">

        <?php if($this->session->flashdata('success')): ?>
            <div class="mb-8 bg-emerald-50 border border-emerald-200 text-emerald-900 p-4 rounded-2xl shadow-xs flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                    <i class="bi bi-check-lg"></i>
                </div>
                <p class="text-xs font-bold"><?= $this->session->flashdata('success'); ?></p>
            </div>
        <?php endif; ?>

        <?php if($this->session->flashdata('error')): ?>
            <div class="mb-8 bg-rose-50 border border-rose-200 text-rose-900 p-4 rounded-2xl shadow-xs flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <p class="text-xs font-bold"><?= $this->session->flashdata('error'); ?></p>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Sidebar Profile Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl p-6 sm:p-8 text-center sticky top-24 border border-slate-200 shadow-sm">
                    <div class="w-20 h-20 bg-gradient-to-tr from-orange-600 to-amber-500 text-white rounded-3xl flex items-center justify-center text-3xl font-bold mx-auto mb-4 shadow-md shadow-orange-500/20">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight mb-1"><?= $detail['nama_depan'] ?? 'Mahasiswa'; ?> <?= $detail['nama_belakang'] ?? ''; ?></h2>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">NIM: <?= $detail['nim'] ?? '1301210001'; ?></p>
                    <span class="px-3.5 py-1.5 bg-orange-50 text-orange-700 text-xs font-semibold rounded-full border border-orange-200 inline-block shadow-xs"><?= $detail['konsentrasi_dkv'] ?? 'Informatika'; ?></span>
                    
                    <div class="border-t border-slate-200 mt-6 pt-5 text-left">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-2">Alamat & Geodata:</span>
                        <p class="text-xs text-slate-700 font-medium leading-relaxed flex items-start gap-2.5 bg-slate-50 p-4 rounded-xl border border-slate-200 shadow-xs">
                            <i class="bi bi-geo-alt-fill text-orange-600 text-base shrink-0 mt-0.5"></i>
                            <span><?= $detail['alamat'] ?? 'Bandung, Jawa Barat'; ?></span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Status Alur Approval Workflow Card -->
                <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-200">
                        <div class="w-9 h-9 rounded-xl bg-orange-600 text-white flex items-center justify-center text-base font-bold shrink-0 shadow-md shadow-orange-500/20">
                            <i class="bi bi-diagram-3-fill"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 tracking-tight">Status Persetujuan Berjenjang</h2>
                            <p class="text-xs text-slate-500 font-normal">Tahap persetujuan pendaftaran Tugas Akhir mahasiswa.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
                        <!-- Stage 1: Dosen Wali -->
                        <?php 
                            $stWali = $detail['status_approval_wali'] ?? 'Pending'; 
                            $bgWali = ($stWali === 'Approved') ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : (($stWali === 'Rejected') ? 'bg-rose-50 border-rose-200 text-rose-800' : 'bg-amber-50 border-amber-200 text-amber-800');
                        ?>
                        <div class="p-4 rounded-xl border <?= $bgWali; ?> shadow-xs">
                            <span class="text-[9px] font-bold uppercase tracking-wider block opacity-70 mb-1">1. Dosen Wali</span>
                            <div class="font-bold flex items-center gap-1.5 text-sm mb-1">
                                <?php if($stWali === 'Approved'): ?>
                                    <i class="bi bi-check-circle-fill text-emerald-600"></i> Disetujui
                                <?php elseif($stWali === 'Rejected'): ?>
                                    <i class="bi bi-x-circle-fill text-rose-600"></i> Ditolak
                                <?php else: ?>
                                    <i class="bi bi-clock-fill text-amber-600"></i> Pending
                                <?php endif; ?>
                            </div>
                            <?php if(!empty($detail['catatan_wali'])): ?>
                                <p class="text-[11px] opacity-80 mt-1 italic leading-tight">"<?= $detail['catatan_wali']; ?>"</p>
                            <?php endif; ?>
                        </div>

                        <!-- Stage 2: Admin Layanan -->
                        <?php 
                            $stAdmin = $detail['status_approval_admin'] ?? 'Pending'; 
                            $bgAdmin = ($stAdmin === 'Approved') ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : (($stAdmin === 'Rejected') ? 'bg-rose-50 border-rose-200 text-rose-800' : 'bg-amber-50 border-amber-200 text-amber-800');
                        ?>
                        <div class="p-4 rounded-xl border <?= $bgAdmin; ?> shadow-xs">
                            <span class="text-[9px] font-bold uppercase tracking-wider block opacity-70 mb-1">2. Admin Layanan</span>
                            <div class="font-bold flex items-center gap-1.5 text-sm mb-1">
                                <?php if($stAdmin === 'Approved'): ?>
                                    <i class="bi bi-check-circle-fill text-emerald-600"></i> Disetujui
                                <?php elseif($stAdmin === 'Rejected'): ?>
                                    <i class="bi bi-x-circle-fill text-rose-600"></i> Ditolak
                                <?php else: ?>
                                    <i class="bi bi-clock-fill text-amber-600"></i> Pending
                                <?php endif; ?>
                            </div>
                            <?php if(!empty($detail['catatan_admin'])): ?>
                                <p class="text-[11px] opacity-80 mt-1 italic leading-tight">"<?= $detail['catatan_admin']; ?>"</p>
                            <?php endif; ?>
                        </div>

                        <!-- Stage 3: Ketua KK -->
                        <?php 
                            $stKk = $detail['status_approval_kk'] ?? 'Pending'; 
                            $bgKk = ($stKk === 'Approved') ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : (($stKk === 'Rejected') ? 'bg-rose-50 border-rose-200 text-rose-800' : 'bg-amber-50 border-amber-200 text-amber-800');
                        ?>
                        <div class="p-4 rounded-xl border <?= $bgKk; ?> shadow-xs">
                            <span class="text-[9px] font-bold uppercase tracking-wider block opacity-70 mb-1">3. Ketua KK</span>
                            <div class="font-bold flex items-center gap-1.5 text-sm mb-1">
                                <?php if($stKk === 'Approved'): ?>
                                    <i class="bi bi-check-circle-fill text-emerald-600"></i> Disetujui
                                <?php elseif($stKk === 'Rejected'): ?>
                                    <i class="bi bi-x-circle-fill text-rose-600"></i> Ditolak
                                <?php else: ?>
                                    <i class="bi bi-clock-fill text-amber-600"></i> Pending
                                <?php endif; ?>
                            </div>
                            <?php if(!empty($detail['catatan_kk'])): ?>
                                <p class="text-[11px] opacity-80 mt-1 italic leading-tight">"<?= $detail['catatan_kk']; ?>"</p>
                            <?php endif; ?>
                        </div>

                        <!-- Stage 4: Koordinator TA -->
                        <?php 
                            $stKoor = $detail['status_approval_koor'] ?? 'Pending'; 
                            $bgKoor = ($stKoor === 'Approved') ? 'bg-emerald-50 border-emerald-300 text-emerald-800' : (($stKoor === 'Rejected') ? 'bg-rose-50 border-rose-300 text-rose-800' : 'bg-amber-50 border-amber-300 text-amber-800');
                        ?>
                        <div class="p-4 rounded-xl border-2 ring-2 ring-orange-500/20 <?= $bgKoor; ?> shadow-xs relative">
                            <span class="absolute -top-2.5 right-2 px-2 py-0.5 bg-orange-600 text-white text-[9px] font-bold rounded-full shadow-xs">SAAT INI</span>
                            <span class="text-[9px] font-bold uppercase tracking-wider block opacity-70 mb-1">4. Koordinator TA</span>
                            <div class="font-bold flex items-center gap-1.5 text-sm mb-1">
                                <?php if($stKoor === 'Approved'): ?>
                                    <i class="bi bi-check-circle-fill text-emerald-600"></i> Disetujui
                                <?php elseif($stKoor === 'Rejected'): ?>
                                    <i class="bi bi-x-circle-fill text-rose-600"></i> Ditolak
                                <?php else: ?>
                                    <i class="bi bi-clock-fill text-amber-600"></i> Pending
                                <?php endif; ?>
                            </div>
                            <?php if(!empty($detail['catatan_koor'])): ?>
                                <p class="text-[11px] opacity-80 mt-1 italic leading-tight">"<?= $detail['catatan_koor']; ?>"</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Judul & File PDF Card -->
                <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-200">
                        <div class="w-9 h-9 rounded-xl bg-orange-600 text-white flex items-center justify-center text-base font-bold shrink-0 shadow-md shadow-orange-500/20">
                            <i class="bi bi-journal-text"></i>
                        </div>
                        <h2 class="text-lg font-bold text-slate-900 tracking-tight">Berkas Usulan Judul & Persyaratan</h2>
                    </div>

                    <div class="space-y-5 text-xs">
                        <div>
                            <span class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Usulan Judul 1 (Utama):</span>
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 font-semibold text-slate-900 text-xs leading-relaxed shadow-xs"><?= $detail['judul_1'] ?? 'Pengembangan Sistem Informasi IFIK Berbasis Web'; ?></div>
                        </div>

                        <div>
                            <span class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Usulan Judul 2 (Alternatif 1):</span>
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 font-medium text-slate-800 text-xs shadow-xs"><?= $detail['judul_2'] ?? 'Rancang Bangun Modul Mahasiswa dan Dosen Wali IFIK'; ?></div>
                        </div>

                        <div>
                            <span class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Usulan Judul 3 (Alternatif 2):</span>
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 font-medium text-slate-800 text-xs shadow-xs"><?= $detail['judul_3'] ?? 'Implementasi Workflow Approval Pendaftaran Tugas Akhir'; ?></div>
                        </div>

                        <div>
                            <span class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Judul (Bahasa Inggris):</span>
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 font-medium italic text-slate-700 text-xs shadow-xs"><?= $detail['judul_en'] ?? 'Development of Web-Based IFIK Information System'; ?></div>
                        </div>

                        <div class="pt-4 border-t border-slate-200">
                            <span class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-3">Berkas Persyaratan (PDF):</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between shadow-xs hover:border-orange-400 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center text-lg font-bold shrink-0">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </div>
                                        <span class="font-bold text-xs text-slate-900">KSM</span>
                                    </div>
                                    <button class="text-xs bg-white hover:bg-orange-50 text-orange-600 hover:text-orange-700 border border-slate-200 hover:border-orange-300 font-semibold px-3 py-1.5 rounded-lg shadow-xs transition">Unduh</button>
                                </div>

                                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between shadow-xs hover:border-orange-400 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center text-lg font-bold shrink-0">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </div>
                                        <span class="font-bold text-xs text-slate-900">Transkrip Nilai</span>
                                    </div>
                                    <button class="text-xs bg-white hover:bg-orange-50 text-orange-600 hover:text-orange-700 border border-slate-200 hover:border-orange-300 font-semibold px-3 py-1.5 rounded-lg shadow-xs transition">Unduh</button>
                                </div>

                                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between shadow-xs hover:border-orange-400 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center text-lg font-bold shrink-0">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </div>
                                        <span class="font-bold text-xs text-slate-900">Surat Pernyataan</span>
                                    </div>
                                    <button class="text-xs bg-white hover:bg-orange-50 text-orange-600 hover:text-orange-700 border border-slate-200 hover:border-orange-300 font-semibold px-3 py-1.5 rounded-lg shadow-xs transition">Unduh</button>
                                </div>

                                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between shadow-xs hover:border-orange-400 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center text-lg font-bold shrink-0">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </div>
                                        <span class="font-bold text-xs text-slate-900">Bebas Lab</span>
                                    </div>
                                    <button class="text-xs bg-white hover:bg-orange-50 text-orange-600 hover:text-orange-700 border border-slate-200 hover:border-orange-300 font-semibold px-3 py-1.5 rounded-lg shadow-xs transition">Unduh</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Approval Koordinator TA Card -->
                <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-200">
                        <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-base font-bold shrink-0 shadow-md shadow-emerald-500/20">
                            <i class="bi bi-check-square-fill"></i>
                        </div>
                        <h2 class="text-lg font-bold text-slate-900 tracking-tight">Keputusan Approval Koordinator TA</h2>
                    </div>

                    <form action="" method="POST" class="space-y-6">
                        <input type="hidden" name="action" value="approval">

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Pilih Keputusan Persetujuan:</label>
                            
                            <!-- Custom Glass Dropdown for Approval Status -->
                            <div class="custom-dropdown relative w-full z-30" id="dropdownApprovalStatus">
                                <?php $currentStatus = $detail['status_approval_koor'] ?? 'Pending'; ?>
                                <?php $isRejected = ($currentStatus === 'Rejected'); ?>
                                <?php $isApproved = ($currentStatus === 'Approved'); ?>
                                <input type="hidden" name="status" id="inputApprovalStatus" value="<?= ($currentStatus === 'Pending') ? 'Approved' : $currentStatus; ?>" required>

                                <button type="button" class="dropdown-trigger w-full px-4 py-3 rounded-xl border transition shadow-xs outline-none flex items-center justify-between text-xs font-semibold <?= $isRejected ? 'border-rose-300 bg-rose-50 text-rose-700' : 'border-emerald-300 bg-emerald-50/60 text-emerald-800'; ?>">
                                    <span class="trigger-label">
                                        <?= $isRejected ? 'Reject' : 'Approve'; ?>
                                    </span>
                                    <i class="bi bi-chevron-down font-bold text-xs transition-transform duration-200 chevron-icon <?= $isRejected ? 'text-rose-600' : 'text-emerald-600'; ?>"></i>
                                </button>

                                <div class="dropdown-menu hidden absolute left-0 right-0 top-full mt-2 bg-white border border-slate-200 rounded-2xl p-2 shadow-xl z-[100] space-y-1">
                                    <div class="dropdown-option px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition flex items-center justify-between cursor-pointer <?= (!$isRejected) ? 'bg-emerald-100/80 text-emerald-700 font-bold' : ''; ?>" data-value="Approved">
                                        <span>Approve</span>
                                        <i class="bi bi-check-lg text-emerald-600 font-bold text-sm <?= (!$isRejected) ? '' : 'hidden'; ?> check-icon"></i>
                                    </div>
                                    <div class="dropdown-option px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-700 hover:bg-rose-50 hover:text-rose-700 transition flex items-center justify-between cursor-pointer <?= ($isRejected) ? 'bg-rose-100/80 text-rose-700 font-bold' : ''; ?>" data-value="Rejected">
                                        <span>Reject</span>
                                        <i class="bi bi-check-lg text-rose-600 font-bold text-sm <?= ($isRejected) ? '' : 'hidden'; ?> check-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2" id="catatanLabel">
                                <?= $isRejected ? 'Catatan Koordinator TA <span class="text-rose-500">*</span>' : 'Catatan Koordinator TA:'; ?>
                            </label>
                            <textarea class="w-full px-4 py-3 rounded-xl border border-slate-300 bg-slate-50/50 focus:bg-white focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none text-xs font-medium text-slate-800 leading-relaxed transition" name="catatan_koor" id="catatanKoor" rows="3" placeholder="Masukkan catatan atau revisi usulan judul jika ada..."><?= $detail['catatan_koor'] ?? ''; ?></textarea>
                            <p class="hidden text-xs font-semibold text-rose-600 mt-2 flex items-center gap-1.5" id="catatanWarning">
                                <i class="bi bi-exclamation-circle-fill"></i> Alasan penolakan wajib diisi sebelum menyimpan keputusan Reject!
                            </p>
                        </div>

                        <button type="submit" class="bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-500 hover:to-amber-500 text-white font-bold px-6 py-2.5 rounded-xl text-xs shadow-md shadow-orange-600/20 inline-flex items-center gap-2 transition-all hover:-translate-y-0.5 active:translate-y-0">
                            <i class="bi bi-send-fill text-sm"></i> Simpan Keputusan Approval
                        </button>
                    </form>
                </div>

            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-5 text-center text-xs text-slate-500 font-medium mt-12">
        &copy; <?= date('Y'); ?> IFIK Portal — Fakultas Industri Kreatif, Telkom University
    </footer>

    <script>
    const catatanTextarea = document.getElementById('catatanKoor');
    const catatanLabel = document.getElementById('catatanLabel');
    const catatanWarning = document.getElementById('catatanWarning');

    function toggleCatatanRequirement(isReject) {
        if (!catatanTextarea || !catatanLabel) return;

        if (isReject) {
            catatanTextarea.required = true;
            catatanLabel.innerHTML = 'Catatan Koordinator TA <span class="text-rose-500">*</span>';
            catatanTextarea.placeholder = 'Masukkan alasan penolakan / revisi usulan judul...';
        } else {
            catatanTextarea.required = false;
            catatanLabel.innerHTML = 'Catatan Koordinator TA:';
            catatanTextarea.placeholder = 'Masukkan catatan atau revisi usulan judul jika ada...';
            catatanTextarea.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/20');
            if (catatanWarning) catatanWarning.classList.add('hidden');
        }
    }

    document.querySelectorAll('.custom-dropdown').forEach(dropdown => {
        const trigger = dropdown.querySelector('.dropdown-trigger');
        const menu = dropdown.querySelector('.dropdown-menu');
        const hiddenInput = dropdown.querySelector('input[type="hidden"]');
        const triggerLabel = dropdown.querySelector('.trigger-label');
        const chevronIcon = dropdown.querySelector('.chevron-icon');
        const options = dropdown.querySelectorAll('.dropdown-option');

        if (!trigger || !menu) return;

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const isHidden = menu.classList.contains('hidden');
            document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.add('hidden'));
            document.querySelectorAll('.chevron-icon').forEach(i => i.classList.remove('rotate-180'));

            if (isHidden) {
                menu.classList.remove('hidden');
                if (chevronIcon) chevronIcon.classList.add('rotate-180');
            }
        });

        options.forEach(opt => {
            opt.addEventListener('click', (e) => {
                e.stopPropagation();
                const val = opt.getAttribute('data-value');
                const labelText = opt.querySelector('span').textContent;

                if (hiddenInput) {
                    hiddenInput.value = val;
                }

                if (triggerLabel) {
                    triggerLabel.textContent = labelText;
                }

                const isReject = (val === 'Rejected');

                // Toggle Catatan Required Validation
                toggleCatatanRequirement(isReject);

                // Toggle Trigger Button styling for Reject (Red) vs Approve (Green)
                if (isReject) {
                    trigger.className = 'dropdown-trigger w-full px-4 py-3 rounded-xl border border-rose-300 bg-rose-50 text-rose-700 font-semibold outline-none flex items-center justify-between transition shadow-xs text-xs';
                    if (chevronIcon) chevronIcon.className = 'bi bi-chevron-down font-bold text-xs transition-transform duration-200 chevron-icon text-rose-600';
                } else {
                    trigger.className = 'dropdown-trigger w-full px-4 py-3 rounded-xl border border-emerald-300 bg-emerald-50/60 text-emerald-800 font-semibold outline-none flex items-center justify-between transition shadow-xs text-xs';
                    if (chevronIcon) chevronIcon.className = 'bi bi-chevron-down font-bold text-xs transition-transform duration-200 chevron-icon text-emerald-600';
                }

                options.forEach(o => {
                    o.classList.remove('bg-emerald-100/80', 'text-emerald-700', 'bg-rose-100/80', 'text-rose-700', 'font-bold');
                    const check = o.querySelector('.check-icon');
                    if (check) check.classList.add('hidden');
                });

                opt.classList.add(isReject ? 'bg-rose-100/80' : 'bg-emerald-100/80', isReject ? 'text-rose-700' : 'text-emerald-700', 'font-bold');
                const check = opt.querySelector('.check-icon');
                if (check) check.classList.remove('hidden');

                menu.classList.add('hidden');
                if (chevronIcon) chevronIcon.classList.remove('rotate-180');
            });
        });
    });

    const approvalForm = document.querySelector('form[action=""]');
    if (approvalForm) {
        approvalForm.addEventListener('submit', function(e) {
            const hiddenInput = document.getElementById('inputApprovalStatus');
            const isReject = hiddenInput && hiddenInput.value === 'Rejected';
            const notesVal = catatanTextarea ? catatanTextarea.value.trim() : '';

            if (isReject && !notesVal) {
                e.preventDefault();
                catatanTextarea.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/20');
                catatanTextarea.focus();
                if (catatanWarning) {
                    catatanWarning.classList.remove('hidden');
                }
            }
        });
    }

    if (catatanTextarea) {
        catatanTextarea.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/20');
                if (catatanWarning) catatanWarning.classList.add('hidden');
            }
        });
    }

    document.addEventListener('click', () => {
        document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.add('hidden'));
        document.querySelectorAll('.chevron-icon').forEach(i => i.classList.remove('rotate-180'));
    });
    </script>
</body>
</html>
