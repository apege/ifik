<?php
/**
 * Pengaturan Master Syarat Berkas Kerja Praktik (KP) - Admin LAA
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
    $this->load->view('components/curved_sidebar');

    $total_syarat   = count($syarat_kp);
    $total_wajib    = 0;
    $total_opsional = 0;
    $total_aktif    = 0;
    foreach ($syarat_kp as $s) {
        if ((int)$s['is_required'] === 1) $total_wajib++;
        else $total_opsional++;
        if ((int)$s['is_active'] === 1) $total_aktif++;
    }
    ?>

    <div id="mainPageContent" class="page-wrapper-for-sidebar min-h-screen flex flex-col">
        <!-- Header Navbar Partial -->
        <?php $this->load->view('partials/app_navbar', [
            'user_role_label'   => 'Super Admin',
            'user_display_name' => $this->session->userdata('nama') ?: ($this->session->userdata('name') ?: ''),
            'user_display_sub'  => 'Pusat Kontrol Super Admin'
        ]); ?>

        <!-- Sub Navigation Page Title Bar -->
        <div class="glass-header px-4 sm:px-6 py-4 mb-8">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-start sm:items-center gap-3.5">
                    <a href="<?= site_url('import-email'); ?>" class="w-10 h-10 rounded-xl bg-orange-50 border border-orange-200 text-brand-600 flex items-center justify-center font-bold text-lg hover:bg-orange-100 transition shrink-0 mt-0.5 sm:mt-0" title="Kembali">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Pengaturan Syarat Berkas KP (Dinamis)</h1>
                            <span class="bg-orange-100 text-brand-700 text-[11px] font-extrabold px-2.5 py-0.5 rounded-full border border-orange-200 uppercase tracking-wider">Super Admin</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Kelola daftar dokumen persyaratan Kerja Praktik (KP) yang wajib diunggah oleh mahasiswa secara dinamis.</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button onclick="openModalTambah()" class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-500 to-brand-600 hover:from-orange-600 hover:to-brand-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-md transition transform hover:-translate-y-0.5 shrink-0 cursor-pointer">
                        <i class="fa-solid fa-plus"></i>
                        <span>Tambah Syarat Berkas KP</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 flex-1 w-full">
            
            <!-- Flash Message Alerts -->
            <?php if ($this->session->flashdata('success')): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl shadow-xs mb-6 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2.5">
                        <div class="w-6 h-6 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <p class="font-bold"><?= $this->session->flashdata('success'); ?></p>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold"><i class="fa-solid fa-xmark"></i></button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl shadow-xs mb-6 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2.5">
                        <div class="w-6 h-6 rounded-lg bg-rose-600 text-white flex items-center justify-center font-bold text-xs">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <p class="font-bold"><?= $this->session->flashdata('error'); ?></p>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 font-bold"><i class="fa-solid fa-xmark"></i></button>
                </div>
            <?php endif; ?>

            <!-- Metrics Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                <div class="card-custom p-5 flex items-center justify-between border-l-4 border-l-brand-500 hover:shadow-md transition">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Syarat KP</p>
                        <h3 class="text-2xl font-extrabold text-slate-800 mt-1"><?= $total_syarat; ?></h3>
                        <p class="text-xs text-slate-400 mt-1 font-medium">Terdaftar di syarat_berkas_kp</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-orange-100 text-brand-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                </div>

                <div class="card-custom p-5 flex items-center justify-between border-l-4 border-l-rose-500 hover:shadow-md transition">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Wajib Diunggah</p>
                        <h3 class="text-2xl font-extrabold text-rose-600 mt-1"><?= $total_wajib; ?></h3>
                        <p class="text-xs text-slate-400 mt-1 font-medium">Mandatory bagi mahasiswa</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fa-solid fa-asterisk"></i>
                    </div>
                </div>

                <div class="card-custom p-5 flex items-center justify-between border-l-4 border-l-amber-500 hover:shadow-md transition">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Opsional</p>
                        <h3 class="text-2xl font-extrabold text-amber-600 mt-1"><?= $total_opsional; ?></h3>
                        <p class="text-xs text-slate-400 mt-1 font-medium">Dokumen pendukung KP</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fa-solid fa-circle-question"></i>
                    </div>
                </div>

                <div class="card-custom p-5 flex items-center justify-between border-l-4 border-l-emerald-500 hover:shadow-md transition">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Status Aktif</p>
                        <h3 class="text-2xl font-extrabold text-emerald-600 mt-1"><?= $total_aktif; ?></h3>
                        <p class="text-xs text-slate-400 mt-1 font-medium">Tampil di form mahasiswa</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
            </div>

            <!-- Table Card: Syarat Berkas KP -->
            <div class="card-custom p-4 sm:p-6 mb-12">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Daftar Persyaratan Berkas Pengajuan KP</h2>
                        <p class="text-xs text-slate-500">Berkas yang aktif akan otomatis muncul di halaman form upload persyaratan KP mahasiswa dan terhubung ke tabel <code>file_pendaftaran_kp</code>.</p>
                    </div>
                    <span class="text-xs font-bold bg-slate-100 text-slate-700 px-3 py-1.5 rounded-lg border border-slate-200 shrink-0">
                        Total Berkas: <?= count($syarat_kp); ?>
                    </span>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full min-w-[750px] text-left text-xs text-slate-700">
                        <thead class="bg-slate-100/80 text-slate-700 font-bold border-b border-slate-200 uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="py-3.5 px-4 text-center w-14">Urutan</th>
                                <th class="py-3.5 px-4">Nama &amp; Kode Berkas</th>
                                <th class="py-3.5 px-4">Deskripsi / Petunjuk Mahasiswa</th>
                                <th class="py-3.5 px-4 text-center">Template Dokumen</th>
                                <th class="py-3.5 px-4 text-center">Sifat Berkas</th>
                                <th class="py-3.5 px-4 text-center">Status Keaktifan</th>
                                <th class="py-3.5 px-4 text-right w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            <?php if (empty($syarat_kp)): ?>
                                <tr>
                                    <td colspan="7" class="py-10 text-center text-slate-400 font-medium">
                                        Belum ada data persyaratan berkas KP. Klik tombol <strong>Tambah Syarat Berkas KP</strong> di atas.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($syarat_kp as $idx => $s): ?>
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-4 px-4 text-center font-bold text-slate-500">
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-slate-100 font-mono text-xs">
                                                <?= $s['urutan'] ?? ($idx + 1); ?>
                                            </span>
                                        </td>

                                        <td class="py-4 px-4">
                                            <div class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($s['nama_berkas']); ?></div>
                                            <div class="font-mono text-[11px] text-slate-400 mt-0.5">
                                                Kode: <span class="bg-slate-100 px-1.5 py-0.5 rounded text-slate-600"><?= htmlspecialchars($s['kode_berkas']); ?></span>
                                            </div>
                                        </td>

                                        <td class="py-4 px-4 text-slate-600 max-w-xs">
                                            <?= htmlspecialchars($s['deskripsi'] ?: '-'); ?>
                                        </td>

                                        <td class="py-4 px-4 text-center">
                                            <?php if (!empty($s['file_template'])): ?>
                                                <a href="<?= base_url('uploads/templates/' . $s['file_template']); ?>" target="_blank"
                                                   class="inline-flex items-center gap-1.5 bg-orange-50 hover:bg-orange-100 text-brand-700 border border-orange-200 px-2.5 py-1 rounded-lg text-[11px] font-bold shadow-2xs transition"
                                                   title="Unduh: <?= htmlspecialchars($s['file_template']); ?>">
                                                    <i class="fa-solid fa-file-arrow-down text-orange-600"></i>
                                                    <span>Ada Template</span>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-slate-400 text-[11px] italic">Tidak ada</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="py-4 px-4 text-center">
                                            <?php if ((int)$s['is_required'] === 1): ?>
                                                <span class="inline-flex items-center gap-1.5 bg-rose-50 text-rose-700 text-[11px] font-bold px-2.5 py-1 rounded-full border border-rose-200">
                                                    <i class="fa-solid fa-asterisk text-[9px]"></i> Wajib
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1.5 bg-sky-50 text-sky-700 text-[11px] font-bold px-2.5 py-1 rounded-full border border-sky-200">
                                                    Opsional
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="py-4 px-4 text-center">
                                            <?php if ((int)$s['is_active'] === 1): ?>
                                                <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 text-[11px] font-bold px-2.5 py-1 rounded-full border border-emerald-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Aktif
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-500 text-[11px] font-bold px-2.5 py-1 rounded-full border border-slate-300">
                                                    Non-Aktif
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="py-4 px-4 text-right space-x-1">
                                            <a href="<?= site_url('superadmin/toggle_syarat_kp/' . $s['id']); ?>" 
                                               title="<?= (int)$s['is_active'] === 1 ? 'Non-aktifkan Berkas' : 'Aktifkan Berkas'; ?>"
                                               class="p-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 transition inline-block">
                                                <i class="fa-solid <?= (int)$s['is_active'] === 1 ? 'fa-toggle-on text-emerald-600' : 'fa-toggle-off text-slate-400'; ?> text-base"></i>
                                            </a>

                                            <button type="button" onclick='openModalEdit(<?= json_encode($s); ?>)' 
                                                    title="Edit Syarat Berkas"
                                                    class="p-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-orange-50 hover:text-brand-600 hover:border-orange-200 transition inline-block cursor-pointer">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>

                                            <button type="button" onclick="deleteSyaratKp(<?= $s['id']; ?>, '<?= htmlspecialchars(addslashes($s['nama_berkas'])); ?>')" 
                                                    title="Hapus Syarat Berkas"
                                                    class="p-2 rounded-lg border border-slate-200 text-rose-500 hover:bg-rose-50 hover:border-rose-200 transition inline-block cursor-pointer">
                                                <i class="fa-solid fa-trash-can"></i>
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
    </div>

    <!-- ================= MODAL TAMBAH / EDIT SYARAT BERKAS KP ================= -->
    <div id="modalFormSyarat" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all">
            <div class="bg-gradient-to-r from-orange-500 to-brand-600 p-5 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center text-lg font-bold">
                        <i class="fa-solid fa-file-circle-plus"></i>
                    </div>
                    <div>
                        <h3 id="modalTitle" class="font-bold text-base">Tambah Syarat Berkas KP</h3>
                        <p class="text-xs text-orange-100">Pengaturan parameter persyaratan berkas KP dinamis.</p>
                    </div>
                </div>
                <button type="button" onclick="closeModalForm()" class="text-white/80 hover:text-white text-xl font-bold cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="formSyarat" method="POST" action="<?= site_url('superadmin/simpan_syarat_kp'); ?>" enctype="multipart/form-data" class="p-6 space-y-4">
                <input type="hidden" name="id" id="syarat_id" value="">

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Nama Berkas Persyaratan KP <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama_berkas" id="syarat_nama" required placeholder="Contoh: Surat Penerimaan KP dari Perusahaan" oninput="autoGenerateKode(this)"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Kode Berkas (Slug Unik)
                    </label>
                    <input type="text" name="kode_berkas" id="syarat_kode" placeholder="Contoh: surat_penerimaan_kp (Otomatis dibuat dari nama)"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs font-mono focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    <p class="text-[11px] text-slate-400 mt-1">Digunakan sebagai identifier di kolom <code>file_pendaftaran_kp.nama</code>.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Deskripsi / Petunjuk Pengunggahan
                    </label>
                    <textarea name="deskripsi" id="syarat_deskripsi" rows="3" placeholder="Jelaskan spesifikasi file yang harus diunggah mahasiswa..."
                              class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none"></textarea>
                </div>

                <!-- Input Upload File Template Resmi (Dinamis) -->
                <div class="p-3.5 bg-orange-50/70 border border-orange-200 rounded-xl space-y-2">
                    <label class="block text-xs font-bold text-slate-800 flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-file-arrow-up text-brand-600"></i>
                            <span>File Template / Contoh Dokumen Resmi</span>
                        </span>
                        <span class="text-[10px] text-slate-400 font-normal">(.pdf, .docx, .doc, .xlsx, .zip)</span>
                    </label>
                    <input type="file" name="file_template" id="syarat_file_template" accept=".pdf,.doc,.docx,.xlsx,.xls,.zip"
                           class="w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-brand-600 file:text-white hover:file:bg-brand-700 cursor-pointer">
                    
                    <div id="wrapperExistingTemplate" class="hidden flex items-center justify-between pt-1 border-t border-orange-200/80 text-[11px]">
                        <span class="text-slate-600 flex items-center gap-1 truncate max-w-xs font-mono">
                            <i class="fa-solid fa-paperclip text-orange-600"></i>
                            <span id="existingTemplateName"></span>
                        </span>
                        <label class="inline-flex items-center gap-1 text-rose-600 cursor-pointer font-bold">
                            <input type="checkbox" name="hapus_template" value="1" class="rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                            <span>Hapus Template</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Urutan Tampilan</label>
                        <input type="number" name="urutan" id="syarat_urutan" value="<?= count($syarat_kp) + 1; ?>" min="1"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs font-mono focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    </div>

                    <div class="flex flex-col justify-end space-y-2 pt-2">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_required" id="syarat_is_required" value="1" checked class="w-4 h-4 text-brand-600 rounded focus:ring-brand-500 border-slate-300">
                            <span class="text-xs font-bold text-slate-700">Wajib Diunggah</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" id="syarat_is_active" value="1" checked class="w-4 h-4 text-brand-600 rounded focus:ring-brand-500 border-slate-300">
                            <span class="text-xs font-bold text-slate-700">Aktifkan di Sistem</span>
                        </label>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200">
                    <button type="button" onclick="closeModalForm()" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-100 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-orange-500 to-brand-600 hover:from-orange-600 hover:to-brand-700 text-white font-bold text-xs shadow-md transition cursor-pointer">
                        Simpan Syarat KP
                    </button>
                </div>
            </form>
        </div>
    </div>

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
            document.getElementById('modalTitle').textContent = 'Tambah Syarat Berkas KP';
            document.getElementById('syarat_id').value = '';
            document.getElementById('syarat_nama').value = '';
            document.getElementById('syarat_kode').value = '';
            document.getElementById('syarat_deskripsi').value = '';
            document.getElementById('syarat_urutan').value = '<?= count($syarat_kp) + 1; ?>';
            document.getElementById('syarat_file_template').value = '';
            document.getElementById('syarat_is_required').checked = true;
            document.getElementById('syarat_is_active').checked = true;
            document.getElementById('wrapperExistingTemplate').classList.add('hidden');

            document.getElementById('modalFormSyarat').classList.remove('hidden');
        }

        function openModalEdit(data) {
            document.getElementById('modalTitle').textContent = 'Edit Syarat Berkas KP';
            document.getElementById('syarat_id').value = data.id;
            document.getElementById('syarat_nama').value = data.nama_berkas || '';
            document.getElementById('syarat_kode').value = data.kode_berkas || '';
            document.getElementById('syarat_deskripsi').value = data.deskripsi || '';
            document.getElementById('syarat_urutan').value = data.urutan || 1;
            document.getElementById('syarat_file_template').value = '';
            document.getElementById('syarat_is_required').checked = (parseInt(data.is_required) === 1);
            document.getElementById('syarat_is_active').checked = (parseInt(data.is_active) === 1);

            if (data.file_template) {
                document.getElementById('existingTemplateName').textContent = data.file_template;
                document.getElementById('wrapperExistingTemplate').classList.remove('hidden');
            } else {
                document.getElementById('wrapperExistingTemplate').classList.add('hidden');
            }

            document.getElementById('modalFormSyarat').classList.remove('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const formSyarat = document.getElementById('formSyarat');
            if (formSyarat) {
                formSyarat.addEventListener('submit', function(e) {
                    const btn = formSyarat.querySelector('button[type="submit"]');
                    if (!btn) return;
                    if (btn.disabled || btn.getAttribute('data-submitting') === 'true') {
                        e.preventDefault();
                        return false;
                    }
                    btn.setAttribute('data-submitting', 'true');
                    btn.disabled = true;
                    btn.classList.add('opacity-75', 'cursor-not-allowed');
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
                });
            }
        });

        function closeModalForm() {
            document.getElementById('modalFormSyarat').classList.add('hidden');
        }

        function deleteSyaratKp(id, name) {
            Swal.fire({
                title: 'Hapus Syarat Berkas KP?',
                html: `Apakah Anda yakin ingin menghapus persyaratan <b>"${name}"</b>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `${baseUrl}superadmin/hapus_syarat_kp/${id}`;
                }
            });
        }
    </script>

    <?php $this->load->view('partials/custom_cursor'); ?>
</body>
</html>
