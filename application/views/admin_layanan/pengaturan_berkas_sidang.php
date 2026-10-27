<?php
/**
 * Pengaturan Master Syarat Berkas Dinamis Pendaftaran Sidang - Admin LAA
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> - IFIK Telkom University</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 & Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- TailwindCSS CDN -->
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
            background-color: #fbf7f1;
            color: #1e293b;
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
        }

        .glass-header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(234, 88, 12, 0.15);
        }

        .card-custom {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.05);
            border-radius: 1rem;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased pb-16">

    <!-- Auto Role-Aware Sidebar Component -->
    <?php 
    $roleId = (int)$this->session->userdata('role_id');
    if ($roleId === 5) {
        $this->load->view('admin_layanan/sidebar');
    } else {
        $this->load->view('components/curved_sidebar');
    }

    $roleLabel = ($roleId === 22) ? 'Super Admin' : (($roleId === 1) ? 'Panel Admin' : 'Admin Layanan (LAA)');
    $backUrl   = ($roleId === 22) ? site_url('import-email') : (($roleId === 1) ? site_url('admin') : site_url('adminlayanan'));
    $badgeText = ($roleId === 22) ? 'Super Admin' : (($roleId === 1) ? 'Admin Panel' : 'LAA System');

    // Hitung ringkasan
    $total_syarat    = count($syarat_sidang);
    $total_wajib     = 0;
    $total_opsional  = 0;
    $total_aktif     = 0;
    foreach ($syarat_sidang as $s) {
        if ($s['is_required'] == 1) $total_wajib++;
        else $total_opsional++;
        if ($s['is_active'] == 1) $total_aktif++;
    }
    ?>

    <div id="mainPageContent" class="page-wrapper-for-sidebar min-h-screen flex flex-col">
        <!-- Top Sticky Header -->
        <header class="glass-header sticky top-0 z-30 transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="<?= $backUrl; ?>" class="w-10 h-10 rounded-xl bg-orange-50 border border-orange-200 text-brand-600 flex items-center justify-center hover:bg-brand-600 hover:text-white transition-all shadow-sm">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold tracking-wide uppercase bg-orange-100 text-brand-700 border border-orange-200">
                                <?= $badgeText; ?>
                            </span>
                            <span class="text-xs text-slate-400">•</span>
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Persyaratan Sidang</span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                            <span>Form Dinamis Syarat Berkas Sidang</span>
                            <span class="text-brand-600 font-mono text-base font-bold bg-brand-50 px-2 py-0.5 rounded-lg border border-brand-200">Sidang TA</span>
                        </h1>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <button onclick="openModalTambah()" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-gradient-to-r from-orange-500 to-brand-600 text-white font-semibold text-sm shadow-md hover:from-orange-600 hover:to-brand-700 transition-all transform active:scale-95 space-x-2">
                        <i class="fa-solid fa-plus-circle"></i>
                        <span>Tambah Berkas Sidang</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 flex-1 w-full">
            
            <!-- Flash Message Alerts -->
            <?php if ($this->session->flashdata('success')): ?>
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center space-x-3 text-emerald-800 shadow-sm">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                    <div class="text-sm font-semibold flex-1"><?= $this->session->flashdata('success'); ?></div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700"><i class="fa-solid fa-xmark"></i></button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-center space-x-3 text-rose-800 shadow-sm">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600 text-lg"></i>
                    <div class="text-sm font-semibold flex-1"><?= $this->session->flashdata('error'); ?></div>
                    <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700"><i class="fa-solid fa-xmark"></i></button>
                </div>
            <?php endif; ?>

            <!-- Metrics Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                <!-- Total Syarat -->
                <div class="card-custom p-5 flex items-center justify-between border-l-4 border-l-brand-500 hover:shadow-md transition">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Dokumen Sidang</p>
                        <h3 class="text-2xl font-extrabold text-slate-800 mt-1"><?= $total_syarat; ?></h3>
                        <p class="text-xs text-slate-400 mt-1 font-medium">Syarat terdaftar di sistem</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-orange-100 text-brand-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fa-solid fa-file-shield"></i>
                    </div>
                </div>

                <!-- Wajib Diunggah -->
                <div class="card-custom p-5 flex items-center justify-between border-l-4 border-l-rose-500 hover:shadow-md transition">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Wajib Diunggah</p>
                        <h3 class="text-2xl font-extrabold text-rose-600 mt-1"><?= $total_wajib; ?></h3>
                        <p class="text-xs text-slate-400 mt-1 font-medium">Syarat mandatory sidang</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fa-solid fa-asterisk"></i>
                    </div>
                </div>

                <!-- Opsional -->
                <div class="card-custom p-5 flex items-center justify-between border-l-4 border-l-amber-500 hover:shadow-md transition">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Opsional</p>
                        <h3 class="text-2xl font-extrabold text-amber-600 mt-1"><?= $total_opsional; ?></h3>
                        <p class="text-xs text-slate-400 mt-1 font-medium">Dokumen pelengkap</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fa-solid fa-circle-question"></i>
                    </div>
                </div>

                <!-- Berkas Aktif -->
                <div class="card-custom p-5 flex items-center justify-between border-l-4 border-l-emerald-500 hover:shadow-md transition">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Status Aktif</p>
                        <h3 class="text-2xl font-extrabold text-emerald-600 mt-1"><?= $total_aktif; ?></h3>
                        <p class="text-xs text-slate-400 mt-1 font-medium">Tampil bagi mahasiswa</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
            </div>

            <!-- Table Card: Syarat Berkas Sidang -->
            <div class="card-custom overflow-hidden mb-12">
                <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-file-lines text-brand-600"></i>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700">Daftar Dokumen Persyaratan Pendaftaran Sidang</h2>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-200 text-slate-700">
                        <?= count($syarat_sidang); ?> Berkas Terkonfigurasi
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100/70 text-slate-600 text-xs font-bold uppercase tracking-wider border-b border-slate-200">
                                <th class="py-3.5 px-4 text-center w-14">#</th>
                                <th class="py-3.5 px-4 w-44">Kode Berkas</th>
                                <th class="py-3.5 px-4">Nama Dokumen Persyaratan</th>
                                <th class="py-3.5 px-4 text-center w-28">Sifat</th>
                                <th class="py-3.5 px-4 text-center w-36">Template File</th>
                                <th class="py-3.5 px-4 text-center w-28">Status</th>
                                <th class="py-3.5 px-4 text-center w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <?php if (empty($syarat_sidang)): ?>
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400">
                                        <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-2xl">
                                            <i class="fa-solid fa-folder-open"></i>
                                        </div>
                                        <p class="font-semibold text-slate-600">Belum ada persyaratan berkas sidang</p>
                                        <p class="text-xs text-slate-400 mt-1">Gunakan tombol 'Tambah Berkas Sidang' untuk menambahkan persyaratan dokumen.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($syarat_sidang as $idx => $s): ?>
                                    <tr class="hover:bg-orange-50/40 transition group">
                                        <!-- No / Urutan -->
                                        <td class="py-4 px-4 text-center font-mono text-xs text-slate-400">
                                            <span class="font-bold text-slate-600"><?= $s['urutan'] ?? ($idx + 1); ?></span>
                                        </td>

                                        <!-- Kode Berkas -->
                                        <td class="py-4 px-4">
                                            <div class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-mono font-bold text-xs border border-slate-200 shadow-xs">
                                                <i class="fa-solid fa-key text-[10px] text-slate-400"></i>
                                                <span><?= htmlspecialchars($s['kode_berkas']); ?></span>
                                            </div>
                                        </td>

                                        <!-- Nama Dokumen & Deskripsi -->
                                        <td class="py-4 px-4">
                                            <div class="font-bold text-slate-900 group-hover:text-brand-600 transition flex items-center gap-1.5">
                                                <span><?= htmlspecialchars($s['nama_berkas']); ?></span>
                                                <?php if ($s['is_required'] == 1): ?>
                                                    <span class="text-rose-500 font-bold" title="Wajib Diunggah">*</span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($s['deskripsi'])): ?>
                                                <div class="text-xs text-slate-500 mt-1 line-clamp-2 max-w-lg">
                                                    <?= htmlspecialchars($s['deskripsi']); ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="text-xs text-slate-400 italic mt-0.5">Tidak ada petunjuk khusus.</div>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Sifat (Wajib / Opsional) -->
                                        <td class="py-4 px-4 text-center">
                                            <?php if ($s['is_required'] == 1): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-700 border border-rose-200">
                                                    Wajib
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                                    Opsional
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Template Unduhan -->
                                        <td class="py-4 px-4 text-center">
                                            <?php if (!empty($s['file_template'])): ?>
                                                <a href="<?= base_url('uploads/templates/' . $s['file_template']); ?>" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-orange-50 hover:bg-orange-100 text-brand-700 font-semibold text-xs border border-orange-200 transition" title="Unduh File Template">
                                                    <i class="fa-solid fa-file-arrow-down text-brand-500"></i>
                                                    <span>Unduh</span>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-xs text-slate-400 italic">Tanpa Template</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Status Toggle -->
                                        <td class="py-4 px-4 text-center">
                                            <a href="<?= site_url('adminlayanan/toggle_syarat_sidang/' . $s['id']); ?>" class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-bold transition shadow-xs <?= ($s['is_active'] == 1) ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200 border border-emerald-300' : 'bg-slate-100 text-slate-500 hover:bg-slate-200 border border-slate-300'; ?>">
                                                <i class="fa-solid <?= ($s['is_active'] == 1) ? 'fa-check-circle text-emerald-500' : 'fa-circle-xmark text-slate-400'; ?>"></i>
                                                <span><?= ($s['is_active'] == 1) ? 'Aktif' : 'Non-Aktif'; ?></span>
                                            </a>
                                        </td>

                                        <!-- Actions -->
                                        <td class="py-4 px-4 text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <button onclick="openModalEdit(<?= htmlspecialchars(json_encode($s)); ?>)" class="w-8 h-8 rounded-lg bg-orange-50 hover:bg-brand-600 text-brand-600 hover:text-white border border-orange-200 transition flex items-center justify-center text-xs shadow-xs" title="Edit Persyaratan">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>

                                                <button onclick="deleteSyaratSidang(<?= $s['id']; ?>, '<?= htmlspecialchars(addslashes($s['nama_berkas'])); ?>')" class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white border border-rose-200 transition flex items-center justify-center text-xs shadow-xs" title="Hapus Persyaratan">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- ================= MODAL TAMBAH / EDIT SYARAT SIDANG ================= -->
    <div id="modalFormSyarat" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full overflow-hidden transform transition-all border border-slate-200">
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-orange-500 to-brand-600 text-white flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-file-circle-plus text-lg"></i>
                    <h3 id="modalTitle" class="text-base font-extrabold tracking-tight">Tambah Berkas Persyaratan Sidang</h3>
                </div>
                <button onclick="closeModalForm()" class="text-orange-100 hover:text-white text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form id="formSyarat" method="POST" action="<?= site_url('adminlayanan/simpan_syarat_sidang'); ?>" enctype="multipart/form-data" class="p-6 space-y-4">
                <input type="hidden" name="id" id="syarat_id" value="">

                <!-- Nama Berkas -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Dokumen / Berkas Persyaratan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama_berkas" id="syarat_nama" required placeholder="Contoh: Surat Izin Sidang yang Telah Ditandatangani Dosen Wali" oninput="autoGenerateKode(this)" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition font-semibold">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Kode Berkas -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kode Berkas (ID Teknis)
                        </label>
                        <input type="text" name="kode_berkas" id="syarat_kode" placeholder="Contoh: surat_izin_wali" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono font-bold focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        <p class="text-[10px] text-slate-400 mt-1">Otomatis dari nama (format lowercase_underscore).</p>
                    </div>

                    <!-- Urutan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Urutan Tampilan
                        </label>
                        <input type="number" name="urutan" id="syarat_urutan" value="<?= count($syarat_sidang) + 1; ?>" min="1" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition font-mono">
                    </div>
                </div>

                <!-- Deskripsi / Petunjuk Pengunggahan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Deskripsi / Petunjuk Pengunggahan
                    </label>
                    <textarea name="deskripsi" id="syarat_deskripsi" rows="2" placeholder="Tuliskan petunjuk atau catatan bagi mahasiswa terkait dokumen ini..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"></textarea>
                </div>

                <!-- Upload File Template -->
                <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 space-y-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Unggah Template Dokumen (Opsional)
                    </label>
                    <input type="file" name="file_template" id="syarat_file_template" accept=".pdf,.doc,.docx,.xlsx,.xls,.zip" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-orange-100 file:text-brand-700 hover:file:bg-orange-200">
                    <p class="text-[11px] text-slate-400">Format yang didukung: .pdf, .docx, .doc, .xlsx, atau .zip (Maks 10MB).</p>
                    
                    <div id="wrapperExistingTemplate" class="hidden pt-1">
                        <div class="flex items-center justify-between text-xs bg-white p-2 rounded-lg border border-slate-200">
                            <span id="existingTemplateName" class="text-slate-600 font-mono text-[11px] truncate max-w-xs"></span>
                            <label class="flex items-center space-x-1.5 text-rose-600 font-semibold cursor-pointer">
                                <input type="checkbox" name="hapus_template" value="1" class="rounded text-rose-600">
                                <span class="text-[11px]">Hapus Template</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Checkboxes: Wajib & Aktif -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" name="is_required" id="syarat_is_required" value="1" checked class="w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                        <label for="syarat_is_required" class="text-xs font-bold text-slate-700 cursor-pointer select-none">
                            Wajib Diunggah (Mandatory)
                        </label>
                    </div>

                    <div class="flex items-center space-x-2">
                        <input type="checkbox" name="is_active" id="syarat_is_active" value="1" checked class="w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                        <label for="syarat_is_active" class="text-xs font-bold text-slate-700 cursor-pointer select-none">
                            Status Aktif Digunakan
                        </label>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
                    <button type="button" onclick="closeModalForm()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold text-xs hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-orange-500 to-brand-600 text-white font-semibold text-xs shadow-md hover:from-orange-600 hover:to-brand-700 transition">
                        Simpan Persyaratan Sidang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript Handlers -->
    <script>
        const baseUrl = '<?= site_url(); ?>';

        function autoGenerateKode(inputEl) {
            const kodeInput = document.getElementById('syarat_kode');
            if (!document.getElementById('syarat_id').value) {
                const val = inputEl.value.trim().toLowerCase();
                kodeInput.value = val.replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
            }
        }

        function openModalTambah() {
            document.getElementById('modalTitle').textContent = 'Tambah Berkas Persyaratan Sidang';
            document.getElementById('syarat_id').value = '';
            document.getElementById('syarat_nama').value = '';
            document.getElementById('syarat_kode').value = '';
            document.getElementById('syarat_deskripsi').value = '';
            document.getElementById('syarat_urutan').value = '<?= count($syarat_sidang) + 1; ?>';
            document.getElementById('syarat_file_template').value = '';
            document.getElementById('syarat_is_required').checked = true;
            document.getElementById('syarat_is_active').checked = true;
            document.getElementById('wrapperExistingTemplate').classList.add('hidden');

            document.getElementById('modalFormSyarat').classList.remove('hidden');
        }

        function openModalEdit(data) {
            document.getElementById('modalTitle').textContent = 'Edit Berkas Persyaratan Sidang';
            document.getElementById('syarat_id').value = data.id;
            document.getElementById('syarat_nama').value = data.nama_berkas || '';
            document.getElementById('syarat_kode').value = data.kode_berkas || '';
            document.getElementById('syarat_deskripsi').value = data.deskripsi || '';
            document.getElementById('syarat_urutan').value = data.urutan || 1;
            document.getElementById('syarat_file_template').value = '';
            document.getElementById('syarat_is_required').checked = (parseInt(data.is_required) === 1);
            document.getElementById('syarat_is_active').checked = (parseInt(data.is_active) === 1);

            if (data.file_template) {
                document.getElementById('existingTemplateName').textContent = 'File: ' + data.file_template;
                document.getElementById('wrapperExistingTemplate').classList.remove('hidden');
            } else {
                document.getElementById('wrapperExistingTemplate').classList.add('hidden');
            }

            document.getElementById('modalFormSyarat').classList.remove('hidden');
        }

        function closeModalForm() {
            document.getElementById('modalFormSyarat').classList.add('hidden');
        }

        function deleteSyaratSidang(id, name) {
            Swal.fire({
                title: 'Hapus Berkas Persyaratan?',
                html: `Apakah Anda yakin ingin menghapus persyaratan <b>"${name}"</b>? Dokumen ini tidak akan lagi diminta pada pendaftaran sidang.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `${baseUrl}adminlayanan/hapus_syarat_sidang/${id}`;
                }
            });
        }
    </script>

    <!-- Global Custom Circle Cursor -->
    <?php $this->load->view('partials/custom_cursor'); ?>
</body>
</html>
