<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Pengaturan Jalur Sidang & Non-Sidang (Dinamis)'; ?> - IFIK Portal</title>
    
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

        .tab-btn-active {
            background-color: #ea580c !important;
            color: #ffffff !important;
            box-shadow: 0 4px 14px rgba(234, 88, 12, 0.25) !important;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased pb-16">

    <!-- Header Navbar Partial -->
    <?php $this->load->view('partials/app_navbar', [
        'user_role_label'   => 'Admin Layanan (LAA)',
        'user_display_name' => 'Admin Layanan FIK',
        'user_display_sub'  => 'Unit Akademik & Kelulusan'
    ]); ?>

    <!-- Sub Navigation Page Title Bar -->
    <div class="glass-header px-6 py-4 mb-8">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="<?= site_url('adminlayanan'); ?>" class="w-10 h-10 rounded-xl bg-orange-50 border border-orange-200 text-brand-600 flex items-center justify-center font-bold text-lg hover:bg-orange-100 transition">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Pengaturan Jalur Sidang &amp; Non-Sidang (Tab Dinamis)</h1>
                        <span class="bg-orange-100 text-brand-700 text-[11px] font-extrabold px-2.5 py-0.5 rounded-full border border-orange-200 uppercase tracking-wider">Admin Panel</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Tambah tab kategori utama baru, edit sub-jalur, dan atur form persyaratannya secara dinamis.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 space-y-6">

        <!-- Navigation Section Tabs (Dynamic Tabs Bar) -->
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-200 pb-4">
            <div class="flex items-center gap-3 flex-wrap" id="dynamicTabsList">
                <button onclick="switchAdminTab('main')" id="tabBtn_main" class="px-5 py-2.5 rounded-xl font-extrabold text-xs tracking-wider uppercase transition tab-btn-active">
                    <i class="bi bi-grid-1x2-fill mr-1.5"></i> 1. Kategori Utama (Pop-Up 1 Choices)
                </button>
                <button onclick="switchAdminTab('non_sidang')" id="tabBtn_non_sidang" class="px-5 py-2.5 rounded-xl font-extrabold text-xs tracking-wider uppercase bg-white text-slate-600 border border-slate-200 hover:bg-slate-100 transition">
                    <i class="bi bi-diagram-3-fill mr-1.5"></i> 2. Sub-Jalur Non-Sidang &amp; Form Fields
                </button>

                <!-- Dynamic Extra Tabs Created by Admin -->
                <?php 
                if (!empty($options_grouped)):
                    foreach ($options_grouped as $cat_key => $cat_items):
                        if (in_array($cat_key, ['main', 'sidang', 'non_sidang'])) continue;
                        $cat_label = strtoupper(str_replace('_', ' ', $cat_key));
                ?>
                <button onclick="switchAdminTab('<?= $cat_key ?>')" id="tabBtn_<?= $cat_key ?>" class="px-5 py-2.5 rounded-xl font-extrabold text-xs tracking-wider uppercase bg-white text-slate-600 border border-slate-200 hover:bg-slate-100 transition">
                    <i class="bi bi-folder-fill mr-1.5"></i> <?= $cat_label ?>
                </button>
                <?php endforeach; endif; ?>
            </div>

            <!-- Action Button: Add New Tab / Category -->
            <div class="flex items-center gap-2">
                <button onclick="openFormAddTabAdmin()" class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-500 to-brand-600 hover:from-orange-600 hover:to-brand-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-md transition transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-plus-circle"></i>
                    <span>+ Tambah Tab / Kategori Baru</span>
                </button>
            </div>
        </div>

        <!-- ================= TAB CONTENT PANELS ================= -->

        <!-- TAB 1: KATEGORI UTAMA (POP-UP 1 CHOICES) -->
        <div id="sectionTab_main" class="space-y-6 tab-section-panel">
            <div class="p-5 rounded-2xl bg-orange-50 border border-orange-200 text-orange-950 flex items-start justify-between gap-4 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-orange-500 text-white flex items-center justify-center text-xl shrink-0 font-bold">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <div class="text-xs sm:text-sm leading-relaxed space-y-1">
                        <h4 class="font-extrabold text-orange-900 text-base">Pilihan Utama Pop-Up 1</h4>
                        <p class="text-orange-800">
                            Kartu pilihan utama ini langsung tampil saat Dosen/Mahasiswa menekan tombol rekomendasi di Pop-up 1.
                        </p>
                    </div>
                </div>
                <button onclick="openFormAddItemInTab('main')" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold rounded-xl shadow-sm transition shrink-0">
                    <i class="bi bi-plus-lg mr-1"></i> Tambah Card Pilihan Utama
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php 
                $main_items = array_merge($options_grouped['main'] ?? [], $options_grouped['sidang'] ?? []);
                if(!empty($main_items)):
                    foreach($main_items as $opt): 
                ?>
                <div class="card-custom p-6 space-y-4 border-2 border-orange-200/60 hover:border-orange-400 transition">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3.5">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-500 to-amber-500 text-white flex items-center justify-center font-bold text-3xl shadow-md shadow-orange-500/20">
                                <i class="bi <?= $opt['icon_class'] ?? 'bi-mortarboard-fill'; ?>"></i>
                            </div>
                            <div>
                                <span class="bg-orange-100 text-orange-800 text-[10px] font-extrabold px-2.5 py-0.5 rounded-full uppercase">Kategori Utama</span>
                                <h3 class="font-extrabold text-xl text-slate-900 mt-1"><?= htmlspecialchars($opt['title']); ?></h3>
                                <p class="text-xs text-slate-500 font-medium mt-0.5"><?= htmlspecialchars($opt['description'] ?? 'Pilihan utama'); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button onclick='openEditJalurModal(<?= json_encode($opt); ?>)' class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-100 hover:bg-amber-200 text-amber-900 text-xs font-bold rounded-xl border border-amber-300 transition">
                            <i class="bi bi-pencil-square"></i> Edit Card
                        </button>
                        <button onclick="deleteOptionAdmin(<?= $opt['id']; ?>)" class="inline-flex items-center gap-1.5 px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-xl border border-rose-200 transition">
                            <i class="bi bi-trash-fill"></i> Hapus
                        </button>
                    </div>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>

        <!-- TAB 2: SUB-JALUR NON-SIDANG & FORM FIELDS -->
        <div id="sectionTab_non_sidang" class="hidden space-y-6 tab-section-panel">
            <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-950 flex items-start justify-between gap-4 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xl shrink-0 font-bold">
                        <i class="bi bi-diagram-3-fill"></i>
                    </div>
                    <div class="text-xs sm:text-sm leading-relaxed space-y-1">
                        <h4 class="font-extrabold text-emerald-900 text-base">Sub-Jalur Non-Sidang &amp; Form Fields</h4>
                        <p class="text-emerald-800">
                            Sub-jalur ini tampil saat Dosen memilih **"NON SIDANG"** pada Pop-up 3.
                        </p>
                    </div>
                </div>
                <button onclick="openFormAddItemInTab('non_sidang')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition shrink-0">
                    <i class="bi bi-plus-lg mr-1"></i> Tambah Sub-Jalur
                </button>
            </div>

            <div class="space-y-6">
                <?php 
                $sub_items = $options_grouped['non_sidang'] ?? [];
                if(!empty($sub_items)):
                    foreach($sub_items as $opt): 
                ?>
                <div class="card-custom p-6 space-y-4 border-2 border-emerald-100">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b border-slate-100 pb-4 gap-3">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-2xl border border-emerald-200">
                                <i class="bi <?= $opt['icon_class'] ?? 'bi-award-fill'; ?>"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-extrabold text-lg text-slate-900"><?= htmlspecialchars($opt['title']); ?></h3>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">Sub-Jalur</span>
                                </div>
                                <p class="text-xs text-slate-500 font-medium mt-0.5"><?= htmlspecialchars($opt['description'] ?? 'Tugas akhir jalur khusus'); ?></p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 flex-wrap">
                            <button onclick='openEditJalurModal(<?= json_encode($opt); ?>)' class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 text-xs font-bold rounded-xl border border-amber-300 transition">
                                <i class="bi bi-pencil-square"></i> Edit Jalur
                            </button>
                            <button onclick="addFieldAdmin(<?= $opt['id']; ?>)" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-xl border border-emerald-300 transition">
                                <i class="bi bi-plus-circle-fill"></i> Tambah Field
                            </button>
                            <button onclick="deleteOptionAdmin(<?= $opt['id']; ?>)" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-xl border border-rose-200 transition">
                                <i class="bi bi-trash-fill"></i> Hapus
                            </button>
                        </div>
                    </div>

                    <!-- Form Requirements List -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Persyaratan Form Upload:</h4>
                        <?php if(!empty($opt['fields'])): ?>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <?php foreach($opt['fields'] as $f): ?>
                                <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                                    <div class="space-y-0.5">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-slate-900"><?= htmlspecialchars($f['field_label']); ?></span>
                                            <?php if($f['is_required']): ?>
                                                <span class="text-rose-600 font-bold">*Wajib</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex items-center gap-2 text-[11px] text-slate-500">
                                            <span class="bg-slate-200 px-2 py-0.5 rounded font-mono font-bold uppercase"><?= $f['field_type']; ?></span>
                                            <span>Format: <strong class="text-brand-600 font-mono"><?= $f['allowed_ext'] ?? 'pdf,docx'; ?></strong></span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button onclick='openEditFieldModal(<?= json_encode($f); ?>)' class="text-amber-700 hover:text-amber-900 font-bold text-xs p-1.5 hover:bg-amber-100 rounded-lg transition" title="Edit Field">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <button onclick="deleteFieldAdmin(<?= $f['id']; ?>)" class="text-rose-600 hover:text-rose-800 font-bold text-xs p-1.5 hover:bg-rose-100 rounded-lg transition" title="Hapus Field">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-xs text-slate-400 italic">Belum ada field persyaratan khusus untuk jalur ini.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>

        <!-- DYNAMIC EXTRA TABS PANELS (FOR CUSTOM TABS CREATED BY ADMIN) -->
        <?php 
        if (!empty($options_grouped)):
            foreach ($options_grouped as $cat_key => $cat_items):
                if (in_array($cat_key, ['main', 'sidang', 'non_sidang'])) continue;
                $cat_title = strtoupper(str_replace('_', ' ', $cat_key));
        ?>
        <div id="sectionTab_<?= $cat_key ?>" class="hidden space-y-6 tab-section-panel">
            <div class="p-5 rounded-2xl bg-indigo-50 border border-indigo-200 text-indigo-950 flex items-start justify-between gap-4 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-xl shrink-0 font-bold">
                        <i class="bi bi-folder-fill"></i>
                    </div>
                    <div class="text-xs sm:text-sm leading-relaxed space-y-1">
                        <h4 class="font-extrabold text-indigo-900 text-base">Tab Kategori: <?= $cat_title ?></h4>
                        <p class="text-indigo-800">
                            Kelola item dan form persyaratan khusus untuk tab kategori ini.
                        </p>
                    </div>
                </div>
                <button onclick="openFormAddItemInTab('<?= $cat_key ?>')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition shrink-0">
                    <i class="bi bi-plus-lg mr-1"></i> Tambah Item di Tab Ini
                </button>
            </div>

            <div class="space-y-6">
                <?php foreach($cat_items as $opt): ?>
                <div class="card-custom p-6 space-y-4 border-2 border-indigo-100">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b border-slate-100 pb-4 gap-3">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-2xl border border-indigo-200">
                                <i class="bi <?= $opt['icon_class'] ?? 'bi-file-earmark-text'; ?>"></i>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-lg text-slate-900"><?= htmlspecialchars($opt['title']); ?></h3>
                                <p class="text-xs text-slate-500 font-medium mt-0.5"><?= htmlspecialchars($opt['description'] ?? ''); ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick='openEditJalurModal(<?= json_encode($opt); ?>)' class="px-3.5 py-2 bg-amber-50 text-amber-800 text-xs font-bold rounded-xl border border-amber-300">Edit</button>
                            <button onclick="addFieldAdmin(<?= $opt['id']; ?>)" class="px-3.5 py-2 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-xl border border-emerald-300">+ Field</button>
                            <button onclick="deleteOptionAdmin(<?= $opt['id']; ?>)" class="px-3.5 py-2 bg-rose-50 text-rose-700 text-xs font-bold rounded-xl border border-rose-200">Hapus</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; endif; ?>

    </main>

    <!-- MODAL EDIT JALUR -->
    <div id="modalEditJalur" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="relative bg-white rounded-3xl p-7 w-full max-w-md shadow-2xl">
            <button onclick="closeModalAdmin('modalEditJalur')" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 text-2xl font-bold">&times;</button>
            <h3 class="text-xl font-bold text-slate-900 mb-4 flex items-center gap-2 border-b pb-3"><i class="bi bi-pencil-square text-orange-500"></i> Edit Jalur / Kategori</h3>
            
            <form id="formEditJalurSubmit" action="<?= site_url('mahasiswa/ajax_save_rekomen_option') ?>" method="POST" class="space-y-4">
                <input type="hidden" name="id" id="editJalurId">
                <input type="hidden" name="category" id="editJalurCategory">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Jalur / Kategori</label>
                    <input type="text" name="title" id="editJalurTitle" required class="w-full p-3 rounded-xl border border-slate-300 text-sm font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Deskripsi Singkat</label>
                    <textarea name="description" id="editJalurDesc" rows="3" class="w-full p-3 rounded-xl border border-slate-300 text-xs font-medium"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Ikon Class (Bootstrap Icons)</label>
                    <input type="text" name="icon_class" id="editJalurIcon" placeholder="bi-award-fill" class="w-full p-3 rounded-xl border border-slate-300 text-xs font-mono">
                </div>
                <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" onclick="closeModalAdmin('modalEditJalur')" class="px-4 py-2 bg-slate-100 font-bold text-xs rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-orange-500 text-white font-bold text-xs rounded-xl shadow-md">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT FIELD -->
    <div id="modalEditField" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="relative bg-white rounded-3xl p-7 w-full max-w-md shadow-2xl">
            <button onclick="closeModalAdmin('modalEditField')" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 text-2xl font-bold">&times;</button>
            <h3 class="text-xl font-bold text-slate-900 mb-4 flex items-center gap-2 border-b pb-3"><i class="bi bi-pencil-square text-emerald-500"></i> Edit Field Persyaratan</h3>
            
            <form id="formEditFieldSubmit" action="<?= site_url('mahasiswa/ajax_save_rekomen_field') ?>" method="POST" class="space-y-4">
                <input type="hidden" name="id" id="editFieldId">
                <input type="hidden" name="jalur_id" id="editFieldJalurId">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Label Field Persyaratan</label>
                    <input type="text" name="field_label" id="editFieldLabel" required class="w-full p-3 rounded-xl border border-slate-300 text-sm font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tipe Field</label>
                    <select name="field_type" id="editFieldType" class="w-full p-3 rounded-xl border border-slate-300 text-xs font-semibold">
                        <option value="file">File Upload (PDF / DOCX)</option>
                        <option value="textarea">Textarea (Catatan / Alasan)</option>
                        <option value="text">Text Short</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Allowed File Extensions</label>
                    <input type="text" name="allowed_ext" id="editFieldExt" value="pdf,docx,doc" class="w-full p-3 rounded-xl border border-slate-300 text-xs font-mono">
                </div>
                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="is_required" id="editFieldReq" value="1" class="w-4 h-4 text-orange-500 rounded">
                    <label for="editFieldReq" class="text-xs font-bold text-slate-700">Wajib Diisi (Required)</label>
                </div>
                <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" onclick="closeModalAdmin('modalEditField')" class="px-4 py-2 bg-slate-100 font-bold text-xs rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 text-white font-bold text-xs rounded-xl shadow-md">Simpan Field</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JS Actions -->
    <script>
        let currentAdminTab = 'main';

        function switchAdminTab(tabKey) {
            currentAdminTab = tabKey;
            
            // Hide all tab sections
            document.querySelectorAll('.tab-section-panel').forEach(el => el.classList.add('hidden'));
            
            // Unset all tab active classes
            document.querySelectorAll('#dynamicTabsList button').forEach(el => {
                el.className = "px-5 py-2.5 rounded-xl font-extrabold text-xs tracking-wider uppercase bg-white text-slate-600 border border-slate-200 hover:bg-slate-100 transition";
            });

            // Show active panel
            const activePanel = document.getElementById('sectionTab_' + tabKey);
            if (activePanel) activePanel.classList.remove('hidden');

            const activeBtn = document.getElementById('tabBtn_' + tabKey);
            if (activeBtn) activeBtn.className = "px-5 py-2.5 rounded-xl font-extrabold text-xs tracking-wider uppercase transition tab-btn-active";
        }

        function openFormAddTabAdmin() {
            const tabName = prompt("Masukkan Nama Tab / Kategori Utama Baru (Contoh: SEMINAR / REKOGNISI KHUSUS):");
            if (!tabName) return;
            const desc = prompt("Masukkan Deskripsi Singkat untuk Tab ini:");

            const catKey = tabName.toLowerCase().replace(/[^a-z0-9]/g, '_');

            const formData = new FormData();
            formData.append('title', tabName);
            formData.append('description', desc || '');
            formData.append('category', catKey);
            formData.append('icon_class', 'bi-folder-fill');

            fetch('<?= site_url("mahasiswa/ajax_save_rekomen_option") ?>', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(res => {
                alert(res.message);
                window.location.reload();
            });
        }

        function openFormAddItemInTab(catKey) {
            const title = prompt(`Masukkan Nama Item / Jalur Baru untuk Tab [${catKey}]:`);
            if (!title) return;
            const desc = prompt("Masukkan Deskripsi Singkat:");

            const formData = new FormData();
            formData.append('title', title);
            formData.append('description', desc || '');
            formData.append('category', catKey);
            formData.append('icon_class', catKey === 'main' ? 'bi-mortarboard-fill' : 'bi-award-fill');

            fetch('<?= site_url("mahasiswa/ajax_save_rekomen_option") ?>', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(res => {
                alert(res.message);
                window.location.reload();
            });
        }

        function closeModalAdmin(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function openEditJalurModal(opt) {
            document.getElementById('editJalurId').value = opt.id;
            document.getElementById('editJalurCategory').value = opt.category || 'non_sidang';
            document.getElementById('editJalurTitle').value = opt.title;
            document.getElementById('editJalurDesc').value = opt.description || '';
            document.getElementById('editJalurIcon').value = opt.icon_class || 'bi-award-fill';
            document.getElementById('modalEditJalur').classList.remove('hidden');
        }

        function openEditFieldModal(f) {
            document.getElementById('editFieldId').value = f.id;
            document.getElementById('editFieldJalurId').value = f.jalur_id;
            document.getElementById('editFieldLabel').value = f.field_label;
            document.getElementById('editFieldType').value = f.field_type || 'file';
            document.getElementById('editFieldExt').value = f.allowed_ext || 'pdf,docx,doc';
            document.getElementById('editFieldReq').checked = parseInt(f.is_required) === 1;
            document.getElementById('modalEditField').classList.remove('hidden');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const formEditJalur = document.getElementById('formEditJalurSubmit');
            if (formEditJalur) {
                formEditJalur.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    fetch(this.action, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.json())
                    .then(res => { alert(res.message); window.location.reload(); });
                });
            }

            const formEditField = document.getElementById('formEditFieldSubmit');
            if (formEditField) {
                formEditField.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    fetch(this.action, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.json())
                    .then(res => { alert(res.message); window.location.reload(); });
                });
            }
        });

        function addFieldAdmin(jalurId) {
            const label = prompt("Masukkan Label Persyaratan Baru (Contoh: Surat Rekomendasi Dekan):");
            if (!label) return;
            const type = prompt("Tipe Field (file / text / textarea):", "file");

            const formData = new FormData();
            formData.append('jalur_id', jalurId);
            formData.append('field_label', label);
            formData.append('field_type', type || 'file');
            formData.append('allowed_ext', 'pdf,docx,doc');
            formData.append('is_required', 1);

            fetch('<?= site_url("mahasiswa/ajax_save_rekomen_field") ?>', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(res => {
                alert(res.message);
                window.location.reload();
            });
        }

        function deleteOptionAdmin(id) {
            if (!confirm("Yakin ingin menghapus jalur/kategori ini beserta seluruh persyaratannya?")) return;
            const formData = new FormData();
            formData.append('id', id);

            fetch('<?= site_url("mahasiswa/ajax_delete_rekomen_option") ?>', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(res => {
                alert(res.message);
                window.location.reload();
            });
        }

        function deleteFieldAdmin(id) {
            if (!confirm("Yakin ingin menghapus field persyaratan ini?")) return;
            const formData = new FormData();
            formData.append('id', id);

            fetch('<?= site_url("mahasiswa/ajax_delete_rekomen_field") ?>', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(res => {
                alert(res.message);
                window.location.reload();
            });
        }
    </script>
</body>
</html>
