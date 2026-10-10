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
    $roleSub   = ($roleId === 22 || $roleId === 1) ? 'Pusat Kontrol Super Admin' : 'Unit Akademik & Kelulusan';
    $backUrl   = ($roleId === 22 || $roleId === 1) ? site_url('import-email') : site_url('adminlayanan');
    $badgeText = ($roleId === 22) ? 'Super Admin' : (($roleId === 1) ? 'Admin Panel' : 'LAA System');
    ?>

    <div id="mainPageContent" class="page-wrapper-for-sidebar min-h-screen flex flex-col">
        <!-- Header Navbar Partial -->
        <?php $this->load->view('partials/app_navbar', [
            'user_role_label'   => $roleLabel,
            'user_display_name' => $this->session->userdata('nama') ?: ($this->session->userdata('name') ?: 'Administrator'),
            'user_display_sub'  => $roleSub
        ]); ?>

        <!-- Sub Navigation Page Title Bar -->
        <div class="glass-header px-4 sm:px-6 py-4 mb-8">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-start sm:items-center gap-3.5">
                    <a href="<?= $backUrl; ?>" class="w-10 h-10 rounded-xl bg-orange-50 border border-orange-200 text-brand-600 flex items-center justify-center font-bold text-lg hover:bg-orange-100 transition shrink-0 mt-0.5 sm:mt-0" title="Kembali">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Pengaturan Persyaratan Berkas TA (Dinamis)</h1>
                            <span class="bg-orange-100 text-brand-700 text-[11px] font-extrabold px-2.5 py-0.5 rounded-full border border-orange-200 uppercase tracking-wider"><?= $badgeText; ?></span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Kelola jenis, jumlah, status wajib, dan keaktifan berkas pendaftaran Tugas Akhir secara fleksibel tanpa hardcode.</p>
                    </div>
                </div>

            <!-- Header Action Button -->
            <button onclick="openModalAdd()" class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-500 to-brand-600 hover:from-orange-600 hover:to-brand-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-md transition transform hover:-translate-y-0.5 shrink-0">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Syarat Berkas Baru</span>
            </button>
        </div>
    </div>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6">

        <!-- Flash Messages -->
        <?php if($this->session->flashdata('success')): ?>
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

        <?php if($this->session->flashdata('error')): ?>
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

        <!-- List Syarat Berkas Table Card -->
        <div class="card-custom p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Daftar Persyaratan Berkas Pendaftaran TA</h2>
                    <p class="text-xs text-slate-500">Berkas yang aktif akan secara otomatis dipersyaratkan pada form mahasiswa dan terhubung ke logika approval verifikasi LAA.</p>
                </div>
                <span class="text-xs font-bold bg-slate-100 text-slate-700 px-3 py-1.5 rounded-lg border border-slate-200 shrink-0">
                    Total Berkas: <?= count($syarat_berkas); ?>
                </span>
            </div>

            <!-- 1. Desktop View (>= 768px) -->
            <div class="hidden md:block overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full min-w-[750px] text-left text-xs text-slate-700">
                    <thead class="bg-slate-100/80 text-slate-700 font-bold border-b border-slate-200 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="py-3.5 px-4 text-center w-12">Urutan</th>
                            <th class="py-3.5 px-4">Nama & Kode Berkas</th>
                            <th class="py-3.5 px-4">Deskripsi / Petunjuk Mahasiswa</th>
                            <th class="py-3.5 px-4 text-center">Template Dokumen</th>
                            <th class="py-3.5 px-4 text-center">Sifat Berkas</th>
                            <th class="py-3.5 px-4 text-center">Status Keaktifan</th>
                            <th class="py-3.5 px-4 text-right w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        <?php if(!empty($syarat_berkas)): ?>
                            <?php foreach($syarat_berkas as $sb): ?>
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-4 px-4 text-center font-bold text-slate-500">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-slate-100 font-mono text-xs">
                                            <?= $sb['urutan']; ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($sb['nama_berkas']); ?></div>
                                        <div class="font-mono text-[11px] text-slate-400 mt-0.5">Kode: <span class="bg-slate-100 px-1.5 py-0.5 rounded text-slate-600"><?= htmlspecialchars($sb['kode_berkas']); ?></span></div>
                                    </td>
                                    <td class="py-4 px-4 text-slate-600 max-w-xs">
                                        <?= htmlspecialchars($sb['deskripsi'] ?: '-'); ?>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <?php if(!empty($sb['file_template'])): ?>
                                            <a href="<?= base_url('uploads/templates/' . $sb['file_template']); ?>" target="_blank"
                                               class="inline-flex items-center gap-1.5 bg-orange-50 hover:bg-orange-100 text-brand-700 border border-orange-200 px-2.5 py-1 rounded-lg text-[11px] font-bold shadow-2xs transition"
                                               title="Unduh: <?= htmlspecialchars($sb['file_template']); ?>">
                                                <i class="fa-solid fa-file-arrow-down text-orange-600"></i>
                                                <span>Ada Template</span>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-slate-400 text-[11px] italic">Tidak ada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <?php if($sb['is_required'] == 1): ?>
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
                                        <?php if($sb['is_active'] == 1): ?>
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
                                        <!-- Button Toggle -->
                                        <a href="<?= site_url('adminlayanan/toggle_syarat_berkas/' . $sb['id']); ?>" 
                                            title="<?= $sb['is_active'] == 1 ? 'Non-aktifkan Berkas' : 'Aktifkan Berkas'; ?>"
                                            class="p-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 transition inline-block">
                                            <i class="fa-solid <?= $sb['is_active'] == 1 ? 'fa-toggle-on text-emerald-600' : 'fa-toggle-off text-slate-400'; ?> text-base"></i>
                                        </a>

                                        <!-- Button Edit -->
                                        <button onclick='openModalEdit(<?= json_encode($sb); ?>)' 
                                                title="Edit Syarat Berkas"
                                                class="p-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-orange-50 hover:text-brand-600 hover:border-orange-200 transition inline-block">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

                                        <!-- Button Delete -->
                                        <a href="<?= site_url('adminlayanan/hapus_syarat_berkas/' . $sb['id']); ?>" 
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus persyaratan berkas ini?');"
                                            title="Hapus Syarat Berkas"
                                            class="p-2 rounded-lg border border-slate-200 text-rose-500 hover:bg-rose-50 hover:border-rose-200 transition inline-block">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 font-medium">
                                    Belum ada data persyaratan berkas. Klik tombol <strong>Tambah Syarat Berkas Baru</strong> di atas.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- 2. Mobile Card View (< 768px) - Zero Horizontal Scrolling -->
            <div class="block md:hidden space-y-3" id="mobileCardsContainer">
                <?php if(!empty($syarat_berkas)): ?>
                    <?php foreach($syarat_berkas as $sb): ?>
                        <div class="card-custom p-3.5 bg-white border border-slate-200 rounded-2xl shadow-2xs space-y-2.5">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-xl bg-orange-100 border border-orange-200 text-brand-600 font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                        <?= $sb['urutan']; ?>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 text-xs truncate"><?= htmlspecialchars($sb['nama_berkas']); ?></div>
                                        <div class="font-mono text-[10px] text-slate-400">Kode: <span class="bg-slate-100 px-1.5 py-0.5 rounded text-slate-600"><?= htmlspecialchars($sb['kode_berkas']); ?></span></div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <?php if($sb['is_required'] == 1): ?>
                                        <span class="inline-flex items-center gap-1 bg-rose-50 text-rose-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-rose-200">
                                            Wajib
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 bg-sky-50 text-sky-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-sky-200">
                                            Opsional
                                        </span>
                                    <?php endif; ?>

                                    <?php if($sb['is_active'] == 1): ?>
                                        <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-emerald-200">
                                            Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-500 text-[10px] font-bold px-2 py-0.5 rounded-full border border-slate-300">
                                            Off
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if (!empty($sb['deskripsi'])): ?>
                                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/80 text-[11px] text-slate-600 leading-relaxed">
                                    <?= htmlspecialchars($sb['deskripsi']); ?>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($sb['file_template'])): ?>
                                <div class="flex items-center justify-between bg-orange-50/70 p-2 rounded-xl border border-orange-200 text-[11px]">
                                    <span class="text-slate-600 font-medium flex items-center gap-1.5">
                                        <i class="fa-solid fa-file-arrow-down text-orange-600"></i>
                                        <span>Template Dokumen</span>
                                    </span>
                                    <a href="<?= base_url('uploads/templates/' . $sb['file_template']); ?>" target="_blank" class="text-brand-600 font-bold hover:underline">
                                        Unduh
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                                <span class="text-slate-400 text-[10px]">Aksi Pengaturan:</span>
                                <div class="flex items-center gap-1.5">
                                    <a href="<?= site_url('adminlayanan/toggle_syarat_berkas/' . $sb['id']); ?>" 
                                       title="<?= $sb['is_active'] == 1 ? 'Non-aktifkan Berkas' : 'Aktifkan Berkas'; ?>"
                                       class="px-2.5 py-1 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 font-bold text-[11px] transition inline-flex items-center gap-1">
                                        <i class="fa-solid <?= $sb['is_active'] == 1 ? 'fa-toggle-on text-emerald-600' : 'fa-toggle-off text-slate-400'; ?>"></i>
                                        <span><?= $sb['is_active'] == 1 ? 'Nonaktifkan' : 'Aktifkan'; ?></span>
                                    </a>

                                    <button onclick='openModalEdit(<?= json_encode($sb); ?>)' 
                                            title="Edit Syarat Berkas"
                                            class="px-2.5 py-1 rounded-lg border border-slate-200 text-slate-600 hover:bg-orange-50 hover:text-brand-600 hover:border-orange-200 font-bold text-[11px] transition inline-flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>Edit</span>
                                    </button>

                                    <a href="<?= site_url('adminlayanan/hapus_syarat_berkas/' . $sb['id']); ?>" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus persyaratan berkas ini?');"
                                       title="Hapus Syarat Berkas"
                                       class="px-2 py-1 rounded-lg border border-slate-200 text-rose-500 hover:bg-rose-50 hover:border-rose-200 font-bold text-[11px] transition inline-flex items-center gap-1">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="py-8 text-center text-slate-400 font-medium text-xs">
                        Belum ada data persyaratan berkas.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    </div>

    <!-- Modal Form Tambah / Edit Syarat Berkas -->
    <div id="modalForm" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all">
            <div class="bg-gradient-to-r from-orange-500 to-brand-600 p-5 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center text-lg font-bold">
                        <i class="fa-solid fa-file-circle-plus"></i>
                    </div>
                    <div>
                        <h3 id="modalTitle" class="font-bold text-base">Tambah Syarat Berkas TA</h3>
                        <p class="text-xs text-orange-100">Pengaturan parameter persyaratan berkas dinamis.</p>
                    </div>
                </div>
                <button onclick="closeModal()" class="text-white/80 hover:text-white text-xl font-bold">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="<?= site_url('adminlayanan/simpan_syarat_berkas'); ?>" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                <input type="hidden" id="field_id" name="id" value="">

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Berkas <span class="text-rose-500">*</span></label>
                    <input type="text" id="field_nama_berkas" name="nama_berkas" required placeholder="Contoh: Sertifikat EPT / TOEFL"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kode Berkas (Slug Unik)</label>
                    <input type="text" id="field_kode_berkas" name="kode_berkas" placeholder="Contoh: sertifikat_ept (Otomatis dibuat jika kosong)"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs font-mono focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    <p class="text-[11px] text-slate-400 mt-1">Digunakan sebagai identifier internal & nama input file.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi / Petunjuk Pengunggahan</label>
                    <textarea id="field_deskripsi" name="deskripsi" rows="3" placeholder="Jelaskan spesifikasi file yang harus diunggah mahasiswa..."
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
                    <input type="file" id="field_file_template" name="file_template" accept=".pdf,.doc,.docx,.xlsx,.xls,.zip"
                           class="w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-brand-600 file:text-white hover:file:bg-brand-700 cursor-pointer">
                    
                    <div id="boxExistingTemplate" class="hidden flex items-center justify-between pt-1 border-t border-orange-200/80 text-[11px]">
                        <span class="text-slate-600 flex items-center gap-1 truncate max-w-xs font-mono">
                            <i class="fa-solid fa-paperclip text-orange-600"></i>
                            <span id="nameExistingTemplate"></span>
                        </span>
                        <div class="flex items-center gap-2">
                            <a href="#" id="linkDownloadTemplate" target="_blank" class="text-brand-600 font-bold hover:underline">Unduh</a>
                            <label class="inline-flex items-center gap-1 text-rose-600 cursor-pointer font-bold">
                                <input type="checkbox" id="checkHapusTemplate" name="hapus_template" value="1" class="rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                                <span>Hapus</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Urutan Tampilan</label>
                        <?php $total_count = count($syarat_berkas); ?>
                        <input type="number" id="field_urutan" name="urutan" min="1" max="<?= $total_count + 1; ?>" value="1"
                               oninput="validateUrutanInput(this)"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                        <p id="urutanHelpText" class="text-[11px] text-slate-400 mt-1 font-medium">Maksimal urutan: <?= $total_count + 1; ?> (sesuai jumlah berkas)</p>
                    </div>
                    <div class="flex flex-col justify-end space-y-2 pt-2">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="field_is_required" name="is_required" value="1" checked class="w-4 h-4 text-brand-600 rounded focus:ring-brand-500 border-slate-300">
                            <span class="text-xs font-bold text-slate-700">Wajib Diunggah</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="field_is_active" name="is_active" value="1" checked class="w-4 h-4 text-brand-600 rounded focus:ring-brand-500 border-slate-300">
                            <span class="text-xs font-bold text-slate-700">Aktifkan di Sistem</span>
                        </label>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200">
                    <button type="button" onclick="closeModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-100 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-orange-500 to-brand-600 hover:from-orange-600 hover:to-brand-700 text-white font-bold text-xs shadow-md transition">
                        Simpan Persyaratan Berkas
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const TOTAL_BERKAS_COUNT = <?= (int)$total_count; ?>;
        const BASE_TEMPLATE_URL  = '<?= base_url("uploads/templates/"); ?>';

        function validateUrutanInput(input) {
            const maxVal = parseInt(input.getAttribute('max')) || 1;
            const minVal = parseInt(input.getAttribute('min')) || 1;
            let val = parseInt(input.value);
            if (isNaN(val)) return;
            if (val > maxVal) {
                input.value = maxVal;
            } else if (val < minVal) {
                input.value = minVal;
            }
        }

        function openModalAdd() {
            document.getElementById('modalTitle').innerText = 'Tambah Syarat Berkas TA Baru';
            document.getElementById('field_id').value = '';
            document.getElementById('field_nama_berkas').value = '';
            document.getElementById('field_kode_berkas').value = '';
            document.getElementById('field_deskripsi').value = '';
            document.getElementById('field_file_template').value = '';
            
            const checkHapus = document.getElementById('checkHapusTemplate');
            if (checkHapus) checkHapus.checked = false;
            document.getElementById('boxExistingTemplate').classList.add('hidden');
            
            const maxAdd = TOTAL_BERKAS_COUNT + 1;
            const inputUrutan = document.getElementById('field_urutan');
            inputUrutan.setAttribute('max', maxAdd);
            inputUrutan.setAttribute('min', 1);
            inputUrutan.value = maxAdd;
            document.getElementById('urutanHelpText').innerText = 'Maksimal urutan: ' + maxAdd + ' (sesuai jumlah berkas)';

            document.getElementById('field_is_required').checked = true;
            document.getElementById('field_is_active').checked = true;
            document.getElementById('modalForm').classList.remove('hidden');
        }

        function openModalEdit(data) {
            document.getElementById('modalTitle').innerText = 'Edit Syarat Berkas TA';
            document.getElementById('field_id').value = data.id || '';
            document.getElementById('field_nama_berkas').value = data.nama_berkas || '';
            document.getElementById('field_kode_berkas').value = data.kode_berkas || '';
            document.getElementById('field_deskripsi').value = data.deskripsi || '';
            document.getElementById('field_file_template').value = '';
            
            const checkHapus = document.getElementById('checkHapusTemplate');
            if (checkHapus) checkHapus.checked = false;

            const boxExisting = document.getElementById('boxExistingTemplate');
            if (data.file_template && data.file_template.trim() !== '') {
                document.getElementById('nameExistingTemplate').innerText = data.file_template;
                document.getElementById('linkDownloadTemplate').href = BASE_TEMPLATE_URL + encodeURIComponent(data.file_template);
                boxExisting.classList.remove('hidden');
            } else {
                boxExisting.classList.add('hidden');
            }
            
            const maxEdit = Math.max(1, TOTAL_BERKAS_COUNT);
            const inputUrutan = document.getElementById('field_urutan');
            inputUrutan.setAttribute('max', maxEdit);
            inputUrutan.setAttribute('min', 1);
            let targetVal = parseInt(data.urutan) || 1;
            if (targetVal > maxEdit) targetVal = maxEdit;
            inputUrutan.value = targetVal;
            document.getElementById('urutanHelpText').innerText = 'Maksimal urutan: ' + maxEdit + ' (sesuai jumlah berkas)';

            document.getElementById('field_is_required').checked = (data.is_required == 1);
            document.getElementById('field_is_active').checked = (data.is_active == 1);
            document.getElementById('modalForm').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modalForm').classList.add('hidden');
        }
    </script>
</body>
</html>
