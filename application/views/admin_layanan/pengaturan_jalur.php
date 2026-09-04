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

                <!-- Dynamic Custom Category Tabs Created from Main Choice Cards -->
                <?php 
                $main_items_all = array_merge($options_grouped['main'] ?? [], $options_grouped['sidang'] ?? []);
                $custom_categories = [];
                if (!empty($main_items_all)) {
                    foreach ($main_items_all as $mopt) {
                        $mcode = $mopt['code'] ?? '';
                        $mtitle = $mopt['title'] ?? '';
                        if (in_array($mcode, ['main_sidang', 'sidang_reguler', 'main_non_sidang'])) continue;
                        if (in_array(strtolower($mtitle), ['sidang', 'non sidang'])) continue;

                        $ckey = '';
                        if (strpos($mcode, 'main_') === 0) {
                            $ckey = substr($mcode, 5);
                        } else {
                            $ckey = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $mtitle));
                        }
                        if (!empty($ckey) && !in_array($ckey, ['main', 'sidang', 'non_sidang'])) {
                            $custom_categories[$ckey] = [
                                'title' => $mtitle,
                                'icon' => $mopt['icon_class'] ?? 'bi-folder-fill'
                            ];
                        }
                    }
                }
                ?>

                <?php foreach ($custom_categories as $ckey => $cinfo): ?>
                <button onclick="switchAdminTab('<?= $ckey ?>')" id="tabBtn_<?= $ckey ?>" class="px-5 py-2.5 rounded-xl font-extrabold text-xs tracking-wider uppercase bg-white text-slate-600 border border-slate-200 hover:bg-slate-100 transition">
                    <i class="bi <?= $cinfo['icon'] ?> mr-1.5"></i> <?= htmlspecialchars(strtoupper($cinfo['title'])) ?>
                </button>
                <?php endforeach; ?>
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
        if (!empty($custom_categories)):
            foreach ($custom_categories as $cat_key => $cat_info):
                $cat_items = $options_grouped[$cat_key] ?? [];
                $cat_title = strtoupper($cat_info['title']);
        ?>
        <div id="sectionTab_<?= $cat_key ?>" class="hidden space-y-6 tab-section-panel">
            <div class="p-5 rounded-2xl bg-indigo-50 border border-indigo-200 text-indigo-950 flex items-start justify-between gap-4 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-xl shrink-0 font-bold">
                        <i class="bi <?= $cat_info['icon'] ?>"></i>
                    </div>
                    <div class="text-xs sm:text-sm leading-relaxed space-y-1">
                        <h4 class="font-extrabold text-indigo-900 text-base">Kelola Sub-Jalur &amp; Form Requirements: <?= $cat_title ?></h4>
                        <p class="text-indigo-800">
                            Kelola sub-item pilihan dan form upload berkas persyaratan khusus untuk kategori ini.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0 flex-wrap">
                    <button onclick="openFormAddItemInTab('<?= $cat_key ?>')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                        <i class="bi bi-plus-lg mr-1"></i> Tambah Item di Tab Ini
                    </button>
                    <button onclick="deleteCategoryAdmin('<?= $cat_key ?>')" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                        <i class="bi bi-trash-fill mr-1"></i> Hapus Tab Kategori Ini
                    </button>
                </div>
            </div>

            <div class="space-y-6">
                <?php if (!empty($cat_items)): foreach($cat_items as $opt): ?>
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
                        <div class="flex items-center gap-2 flex-wrap">
                            <button onclick='openEditJalurModal(<?= json_encode($opt); ?>)' class="inline-flex items-center gap-1 px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 text-xs font-bold rounded-xl border border-amber-300 transition">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                            <button onclick="addFieldAdmin(<?= $opt['id']; ?>)" class="inline-flex items-center gap-1 px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-xl border border-emerald-300 transition">
                                <i class="bi bi-plus-circle-fill"></i> + Tambah Field
                            </button>
                            <button onclick="deleteOptionAdmin(<?= $opt['id']; ?>)" class="inline-flex items-center gap-1 px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-xl border border-rose-200 transition">
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
                            <p class="text-xs text-slate-400 italic">Belum ada field persyaratan khusus untuk kartu ini. Klik tombol "+ Tambah Field" di atas untuk menambahkan berkas/persyaratan wajib.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; else: ?>
                <div class="p-8 rounded-2xl bg-white border-2 border-dashed border-slate-200 text-center space-y-3">
                    <div class="w-12 h-12 mx-auto rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl font-bold">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <p class="text-xs font-semibold text-slate-500">Belum ada item / sub-jalur khusus di dalam tab <?= htmlspecialchars($cat_title) ?>.</p>
                    <button onclick="openFormAddItemInTab('<?= $cat_key ?>')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                        <i class="bi bi-plus-lg mr-1"></i> Tambah Item Pertama
                    </button>
                </div>
                <?php endif; ?>
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

    <!-- MODAL TAMBAH TAB BARU -->
    <div id="modalAddTabAdmin" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md transition-opacity">
        <div class="relative bg-white rounded-3xl p-7 sm:p-8 w-full max-w-md shadow-2xl border border-slate-100 transform transition-all">
            <button onclick="closeModalAdmin('modalAddTabAdmin')" class="absolute top-5 right-5 w-9 h-9 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 text-xl font-bold flex items-center justify-center transition">&times;</button>
            <h3 class="text-xl font-extrabold text-slate-900 mb-4 flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="bi bi-folder-plus text-orange-500"></i> Tambah Tab / Kategori Baru
            </h3>
            
            <form id="formAddTabSubmit" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Tab / Kategori Utama <span class="text-rose-500">*</span></label>
                    <input type="text" id="addTabName" required placeholder="Contoh: SEMINAR / REKOGNISI KHUSUS" class="w-full p-3 rounded-2xl border border-slate-300 focus:ring-orange-500 focus:border-orange-500 text-xs font-bold text-slate-900">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Deskripsi Singkat</label>
                    <input type="text" id="addTabDesc" placeholder="Penjelasan singkat mengenai kelompok jalur ini..." class="w-full p-3 rounded-2xl border border-slate-300 focus:ring-orange-500 focus:border-orange-500 text-xs font-medium text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pilih Ikon / Logo Tab</label>
                    <select id="addTabIcon" class="w-full p-3 rounded-2xl border border-slate-300 focus:ring-orange-500 focus:border-orange-500 text-xs font-bold text-slate-800">
                        <option value="bi-folder-fill">📁 Folder / Kategori (bi-folder-fill)</option>
                        <option value="bi-mortarboard-fill">🎓 Sidang / Akademik (bi-mortarboard-fill)</option>
                        <option value="bi-award-fill">🏆 Penghargaan / Lomba (bi-award-fill)</option>
                        <option value="bi-patch-check-fill">📜 Sertifikat / HKI (bi-patch-check-fill)</option>
                        <option value="bi-journal-text">📚 Publikasi / Jurnal (bi-journal-text)</option>
                        <option value="bi-briefcase-fill">💼 MBKM / Magang / Industri (bi-briefcase-fill)</option>
                        <option value="bi-easel-fill">🎨 Pameran / Karya (bi-easel-fill)</option>
                        <option value="bi-gear-wide-connected">⚙️ Kebijakan / Sistem (bi-gear-wide-connected)</option>
                        <option value="bi-star-fill">⭐ Khusus / Spesial (bi-star-fill)</option>
                        <option value="bi-bookmark-star-fill">🔖 Bookmark / Pilihan (bi-bookmark-star-fill)</option>
                    </select>
                </div>

                <div class="pt-4 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" onclick="closeModalAdmin('modalAddTabAdmin')" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">Batal</button>
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                        <i class="bi bi-check-lg"></i> Simpan Tab
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL TAMBAH CARD JALUR BARU -->
    <div id="modalAddCardAdmin" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md transition-opacity">
        <div class="relative bg-white rounded-3xl p-7 sm:p-8 w-full max-w-md shadow-2xl border border-slate-100 transform transition-all">
            <button onclick="closeModalAdmin('modalAddCardAdmin')" class="absolute top-5 right-5 w-9 h-9 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 text-xl font-bold flex items-center justify-center transition">&times;</button>
            <h3 class="text-xl font-extrabold text-slate-900 mb-4 flex items-center gap-2 border-b border-slate-100 pb-3" id="modalAddCardTitle">
                <i class="bi bi-plus-square-fill text-orange-500"></i> Tambah Card Jalur Baru
            </h3>
            
            <form id="formAddCardSubmit" class="space-y-4">
                <input type="hidden" id="addCardCategory">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Jalur / Card <span class="text-rose-500">*</span></label>
                    <input type="text" id="addCardTitle" required placeholder="Contoh: Juara Lomba Desain / HKI" class="w-full p-3 rounded-2xl border border-slate-300 focus:ring-orange-500 focus:border-orange-500 text-xs font-bold text-slate-900">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Deskripsi Singkat</label>
                    <textarea id="addCardDesc" rows="3" placeholder="Keterangan singkat jalur ekuivalensi ini..." class="w-full p-3 rounded-2xl border border-slate-300 focus:ring-orange-500 focus:border-orange-500 text-xs font-medium text-slate-800"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Ikon</label>
                    <select id="addCardIcon" class="w-full p-3 rounded-2xl border border-slate-300 focus:ring-orange-500 focus:border-orange-500 text-xs font-bold text-slate-800">
                        <option value="bi-award-fill">🏆 Penghargaan / Lomba (bi-award-fill)</option>
                        <option value="bi-mortarboard-fill">🎓 Sidang / Akademik (bi-mortarboard-fill)</option>
                        <option value="bi-journal-text">📚 Publikasi / Jurnal (bi-journal-text)</option>
                        <option value="bi-patch-check-fill">📜 Sertifikat HKI / HKI (bi-patch-check-fill)</option>
                        <option value="bi-briefcase-fill">💼 MBKM / Magang (bi-briefcase-fill)</option>
                        <option value="bi-star-fill">⭐ Khusus / Lainnya (bi-star-fill)</option>
                    </select>
                </div>
                <div class="pt-4 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" onclick="closeModalAdmin('modalAddCardAdmin')" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">Batal</button>
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                        <i class="bi bi-check-lg"></i> Simpan Jalur Baru
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL TAMBAH FIELD PERSYARATAN BARU -->
    <div id="modalAddFieldAdmin" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md transition-opacity">
        <div class="relative bg-white rounded-3xl p-7 sm:p-8 w-full max-w-md shadow-2xl border border-slate-100 transform transition-all">
            <button onclick="closeModalAdmin('modalAddFieldAdmin')" class="absolute top-5 right-5 w-9 h-9 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 text-xl font-bold flex items-center justify-center transition">&times;</button>
            <h3 class="text-xl font-extrabold text-slate-900 mb-4 flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="bi bi-file-earmark-plus-fill text-orange-500"></i> Tambah Field Persyaratan Baru
            </h3>
            
            <form id="formAddFieldSubmit" class="space-y-4">
                <input type="hidden" id="addFieldJalurId">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Label Field Persyaratan <span class="text-rose-500">*</span></label>
                    <input type="text" id="addFieldLabel" required placeholder="Contoh: Sertifikat HKI / Surat Rekomendasi" class="w-full p-3 rounded-2xl border border-slate-300 focus:ring-orange-500 focus:border-orange-500 text-xs font-bold text-slate-900">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tipe Input Field</label>
                    <select id="addFieldType" class="w-full p-3 rounded-2xl border border-slate-300 focus:ring-orange-500 focus:border-orange-500 text-xs font-bold text-slate-800">
                        <option value="file">📄 File Upload (PDF / DOCX)</option>
                        <option value="textarea">📝 Textarea (Catatan / Alasan Uraian)</option>
                        <option value="text">✍️ Teks Singkat</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Ekstensi File yang Diizinkan</label>
                    <input type="text" id="addFieldExt" value="pdf,docx,doc" class="w-full p-3 rounded-2xl border border-slate-300 focus:ring-orange-500 focus:border-orange-500 text-xs font-mono text-slate-800">
                </div>
                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" id="addFieldReq" value="1" checked class="w-4 h-4 text-orange-600 rounded border-slate-300 focus:ring-orange-500 cursor-pointer">
                    <label for="addFieldReq" class="text-xs font-bold text-slate-700 cursor-pointer">Wajib Diisi (Required)</label>
                </div>
                <div class="pt-4 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" onclick="closeModalAdmin('modalAddFieldAdmin')" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">Batal</button>
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                        <i class="bi bi-check-lg"></i> Simpan Field
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL KONFIRMASI HAPUS (TAILWIND MODERN) -->
    <div id="modalConfirmDeleteAdmin" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md transition-opacity">
        <div class="relative bg-white rounded-3xl p-7 sm:p-8 w-full max-w-md shadow-2xl border border-slate-100 transform transition-all text-center">
            <button onclick="closeModalAdmin('modalConfirmDeleteAdmin')" class="absolute top-5 right-5 w-9 h-9 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 text-xl font-bold flex items-center justify-center transition">&times;</button>
            
            <div class="w-16 h-16 mx-auto mb-4 rounded-3xl bg-rose-100 text-rose-600 flex items-center justify-center text-3xl font-bold border border-rose-200 shadow-inner">
                <i class="bi bi-trash-fill"></i>
            </div>

            <h3 class="text-xl font-extrabold text-slate-900 mb-2">Konfirmasi Hapus</h3>
            <p id="confirmDeleteMsg" class="text-xs text-slate-600 font-medium leading-relaxed mb-6 px-2">
                Apakah Anda yakin ingin menghapus item ini? Tindakan ini tidak dapat dibatalkan.
            </p>

            <div class="flex items-center justify-center gap-3">
                <button type="button" onclick="closeModalAdmin('modalConfirmDeleteAdmin')" class="w-1/2 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-2xl transition">
                    Batal
                </button>
                <button type="button" id="btnConfirmDeleteSubmit" class="w-1/2 py-3 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white font-bold text-xs rounded-2xl shadow-lg shadow-rose-500/25 transition flex items-center justify-center gap-1.5">
                    <i class="bi bi-trash-fill"></i> Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    <!-- JS Actions -->
    <script>
        let currentAdminTab = 'main';
        let pendingDeleteAction = null;


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
            document.getElementById('addTabName').value = '';
            document.getElementById('addTabDesc').value = '';
            document.getElementById('modalAddTabAdmin').classList.remove('hidden');
        }

        function openFormAddItemInTab(catKey) {
            document.getElementById('addCardCategory').value = catKey;
            document.getElementById('addCardTitle').value = '';
            document.getElementById('addCardDesc').value = '';
            document.getElementById('addCardIcon').value = catKey === 'main' ? 'bi-mortarboard-fill' : 'bi-award-fill';
            document.getElementById('modalAddCardTitle').innerHTML = `<i class="bi bi-plus-square-fill text-orange-500"></i> Tambah Jalur Baru [${catKey.toUpperCase()}]`;
            document.getElementById('modalAddCardAdmin').classList.remove('hidden');
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

        function addFieldAdmin(jalurId) {
            document.getElementById('addFieldJalurId').value = jalurId;
            document.getElementById('addFieldLabel').value = '';
            document.getElementById('addFieldType').value = 'file';
            document.getElementById('addFieldExt').value = 'pdf,docx,doc';
            document.getElementById('addFieldReq').checked = true;
            document.getElementById('modalAddFieldAdmin').classList.remove('hidden');
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Form Add Tab Submit
            const formAddTab = document.getElementById('formAddTabSubmit');
            if (formAddTab) {
                formAddTab.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const tabName = document.getElementById('addTabName').value.trim();
                    const desc = document.getElementById('addTabDesc').value.trim();
                    const catKey = tabName.toLowerCase().replace(/[^a-z0-9]/g, '_');

                    const iconClass = document.getElementById('addTabIcon').value;

                    // 1. Create main item inside new tab category
                    const formData1 = new FormData();
                    formData1.append('title', tabName);
                    formData1.append('description', desc);
                    formData1.append('category', catKey);
                    formData1.append('code', catKey + '_default');
                    formData1.append('icon_class', iconClass);

                    fetch('<?= site_url("mahasiswa/ajax_save_rekomen_option") ?>', {
                        method: 'POST',
                        body: formData1,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.json())
                    .then(res => {
                        // 2. Create corresponding main choice card for Pop-up 1
                        const formData2 = new FormData();
                        formData2.append('title', tabName);
                        formData2.append('description', desc);
                        formData2.append('category', 'main');
                        formData2.append('code', 'main_' + catKey);
                        formData2.append('icon_class', iconClass);

                        return fetch('<?= site_url("mahasiswa/ajax_save_rekomen_option") ?>', {
                            method: 'POST',
                            body: formData2,
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                    })
                    .then(() => {
                        window.location.reload();
                    });

                });
            }

            // Form Add Card Submit
            const formAddCard = document.getElementById('formAddCardSubmit');
            if (formAddCard) {
                formAddCard.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const title = document.getElementById('addCardTitle').value.trim();
                    const desc = document.getElementById('addCardDesc').value.trim();
                    const catKey = document.getElementById('addCardCategory').value;
                    const iconClass = document.getElementById('addCardIcon').value;

                    const formData = new FormData();
                    formData.append('title', title);
                    formData.append('description', desc);
                    formData.append('category', catKey);
                    if (catKey === 'main') {
                        const slug = title.toLowerCase().replace(/[^a-z0-9]/g, '_');
                        formData.append('code', 'main_' + slug);
                    }
                    formData.append('icon_class', iconClass);

                    fetch('<?= site_url("mahasiswa/ajax_save_rekomen_option") ?>', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.json())
                    .then(res => {
                        window.location.reload();
                    });
                });
            }


            // Form Add Field Submit
            const formAddField = document.getElementById('formAddFieldSubmit');
            if (formAddField) {
                formAddField.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const jalurId = document.getElementById('addFieldJalurId').value;
                    const label = document.getElementById('addFieldLabel').value.trim();
                    const type = document.getElementById('addFieldType').value;
                    const ext = document.getElementById('addFieldExt').value.trim();
                    const req = document.getElementById('addFieldReq').checked ? 1 : 0;

                    const formData = new FormData();
                    formData.append('jalur_id', jalurId);
                    formData.append('field_label', label);
                    formData.append('field_type', type);
                    formData.append('allowed_ext', ext);
                    formData.append('is_required', req);

                    fetch('<?= site_url("mahasiswa/ajax_save_rekomen_field") ?>', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.json())
                    .then(res => {
                        window.location.reload();
                    });
                });
            }

            const formEditJalur = document.getElementById('formEditJalurSubmit');
            if (formEditJalur) {
                formEditJalur.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    fetch(this.action, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.json())
                    .then(res => { window.location.reload(); });
                });
            }

            const formEditField = document.getElementById('formEditFieldSubmit');
            if (formEditField) {
                formEditField.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    fetch(this.action, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.json())
                    .then(res => { window.location.reload(); });
                });
            }
        });

        function showDeleteConfirmModal(message, actionCallback) {

            document.getElementById('confirmDeleteMsg').innerText = message;
            pendingDeleteAction = actionCallback;
            document.getElementById('modalConfirmDeleteAdmin').classList.remove('hidden');
        }

        document.getElementById('btnConfirmDeleteSubmit')?.addEventListener('click', function() {
            if (typeof pendingDeleteAction === 'function') {
                pendingDeleteAction();
            }
            closeModalAdmin('modalConfirmDeleteAdmin');
        });

        function deleteOptionAdmin(id) {
            showDeleteConfirmModal("Apakah Anda yakin ingin menghapus kartu jalur ini beserta seluruh persyaratannya di dalamnya?", function() {
                const formData = new FormData();
                formData.append('id', id);

                fetch('<?= site_url("mahasiswa/ajax_delete_rekomen_option") ?>', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(res => {
                    window.location.reload();
                });
            });
        }

        function deleteFieldAdmin(id) {
            showDeleteConfirmModal("Apakah Anda yakin ingin menghapus field persyaratan ini?", function() {
                const formData = new FormData();
                formData.append('id', id);

                fetch('<?= site_url("mahasiswa/ajax_delete_rekomen_field") ?>', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(res => {
                    window.location.reload();
                });
            });
        }

        function deleteCategoryAdmin(catKey) {
            showDeleteConfirmModal(`Apakah Anda yakin ingin menghapus seluruh Tab Kategori [${catKey.toUpperCase()}] beserta seluruh item dan persyaratannya di dalamnya?`, function() {
                const formData = new FormData();
                formData.append('category', catKey);

                fetch('<?= site_url("mahasiswa/ajax_delete_rekomen_category") ?>', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(res => {
                    window.location.reload();
                });
            });
        }

    </script>
</body>
</html>
