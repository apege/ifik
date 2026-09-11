<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Input Ticketing — IFIK Portal'; ?></title>
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
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- TinyMCE CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <style>
        body, button, input, textarea, select {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }
        /* TinyMCE wrapper styling */
        .tox-tinymce {
            border-radius: 1rem !important;
            border-color: #e2e8f0 !important;
            box-shadow: none !important;
        }
        .tox-tinymce:focus-within {
            border-color: #ea580c !important;
            box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.1) !important;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-orange-50/20 to-slate-100 min-h-screen text-slate-800 antialiased">

    <!-- Include Dosen Sidebar -->
    <?php $this->load->view('partials/dosen_sidebar'); ?>

    <!-- Main Content -->
    <main class="min-h-screen p-6 sm:p-8 lg:p-10 max-w-5xl mx-auto">
        
        <!-- Header & Breadcrumb -->
        <div class="mb-8">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-2">
                <a href="<?= site_url('dosen/bimbingan') ?>" class="hover:text-orange-600 transition-colors">Portal Dosen</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-slate-600">Ticketing</span>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-orange-600 font-bold">Input Tiket Baru</span>
            </div>
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3">
                        <span class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-orange-600 to-amber-500 text-white flex items-center justify-center shadow-lg shadow-orange-500/25">
                            <i class="bi bi-ticket-detailed-fill text-xl"></i>
                        </span>
                        Buat Tiket Kendala Baru
                    </h1>
                    <p class="text-sm text-slate-500 mt-1.5 max-w-2xl">
                        Sampaikan kendala teknis atau permohonan layanan akademik/fasilitas kepada unit yang berwenang.
                    </p>
                </div>

                <a href="<?= site_url('dosen/ticketing/riwayat') ?>" 
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:text-orange-600 hover:border-orange-200 hover:bg-orange-50/50 text-sm font-bold shadow-xs transition-all">
                    <i class="bi bi-clock-history text-base"></i>
                    <span>Lihat Riwayat Tiket</span>
                </a>
            </div>
        </div>

        <!-- Flash Alert Messages -->
        <?php if ($this->session->flashdata('error')): ?>
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-sm flex items-start gap-3 shadow-xs">
                <i class="bi bi-exclamation-octagon-fill text-lg text-rose-500 shrink-0 mt-0.5"></i>
                <div class="flex-1"><?= $this->session->flashdata('error'); ?></div>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('success')): ?>
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-start gap-3 shadow-xs">
                <i class="bi bi-check-circle-fill text-lg text-emerald-500 shrink-0 mt-0.5"></i>
                <div class="flex-1"><?= $this->session->flashdata('success'); ?></div>
            </div>
        <?php endif; ?>

        <!-- Form Card Container -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/40 overflow-hidden">
            
            <!-- Card Header Notice -->
            <div class="px-6 sm:px-8 py-5 border-b border-slate-100 bg-gradient-to-r from-orange-50/40 via-amber-50/20 to-transparent flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-slate-400">Akun Pelapor Terverifikasi:</div>
                        <div class="text-sm font-bold text-slate-800">
                            <?= htmlspecialchars($user['nama'] ?: 'Dosen'); ?> 
                            <span class="text-xs font-medium text-slate-500 ml-1.5">(NIDN: <?= htmlspecialchars($user['nidn'] ?: '-'); ?>)</span>
                        </div>
                    </div>
                </div>
                <div class="text-xs font-semibold text-slate-400">
                    Email: <span class="text-slate-700 font-bold"><?= htmlspecialchars($user['email'] ?: '-'); ?></span>
                </div>
            </div>

            <!-- Form -->
            <form id="ticketingForm" action="<?= site_url('dosen/ticketing/simpan') ?>" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
                
                <!-- 1. Nama Lengkap -->
                <div>
                    <label for="nama_lengkap" class="block text-sm font-bold text-slate-700 mb-2">
                        1. Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <i class="bi bi-person-fill text-base"></i>
                        </span>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" required
                               value="<?= htmlspecialchars($user['nama'] ?: ''); ?>"
                               placeholder="Masukkan nama lengkap Anda..."
                               class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 text-sm font-semibold text-slate-800 transition-all outline-hidden">
                    </div>
                    <p class="text-xs text-slate-400 mt-1.5">Nama dosen yang mengajukan tiket kendala ini.</p>
                </div>

                <!-- 2. Unit yang Dituju (Dropdown) -->
                <div>
                    <label for="unit_tujuan" class="block text-sm font-bold text-slate-700 mb-2">
                        2. Unit yang Dituju <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <i class="bi bi-building-gear text-base"></i>
                        </span>
                        <select id="unit_tujuan" name="unit_tujuan" required onchange="handleUnitChange(this.value)"
                                class="w-full pl-11 pr-10 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 text-sm font-semibold text-slate-800 transition-all outline-hidden appearance-none cursor-pointer">
                            <option value="">-- Pilih Unit yang Dituju --</option>
                            <?php foreach ($unit_kategori_map as $unitName => $kategoriList): ?>
                                <option value="<?= htmlspecialchars($unitName); ?>"><?= htmlspecialchars($unitName); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <i class="bi bi-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                    </div>
                    <p class="text-xs text-slate-400 mt-1.5">Pilih unit kerja atau departemen yang berwenang menindaklanjuti kendala Anda.</p>
                </div>

                <!-- 3. Kategori Kendala (Dropdown Dinamis Berdasarkan Unit) -->
                <div>
                    <label for="kategori" class="block text-sm font-bold text-slate-700 mb-2">
                        3. Kategori Kendala <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <i class="bi bi-tags-fill text-base"></i>
                        </span>
                        <select id="kategori" name="kategori" required disabled onchange="handleKategoriChange(this.value)"
                                class="w-full pl-11 pr-10 py-3 rounded-xl bg-slate-100 border border-slate-200 focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 text-sm font-semibold text-slate-800 transition-all outline-hidden appearance-none cursor-not-allowed disabled:opacity-75">
                            <option value="">-- Silakan pilih Unit yang Dituju terlebih dahulu --</option>
                        </select>
                        <i class="bi bi-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                    </div>
                    <p id="kategori-hint" class="text-xs text-slate-400 mt-1.5">Kategori akan otomatis disesuaikan dengan unit yang Anda pilih di atas.</p>
                </div>

                <!-- 3b. Detail / Keterangan Kategori Lainnya (Muncul jika pilih 'Lain-lain' atau 'Lainnya') -->
                <div id="container_kategori_lainnya" class="hidden transition-all duration-300">
                    <label for="kategori_lainnya" class="block text-sm font-bold text-slate-700 mb-2 flex items-center justify-between">
                        <span>Detail / Keterangan Kategori Lainnya <span class="text-rose-500">*</span></span>
                        <span class="text-[11px] font-normal text-orange-600 bg-orange-50 px-2 py-0.5 rounded-md border border-orange-200">
                            Wajib Diisi Khusus Opsi Lainnya
                        </span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-orange-500 pointer-events-none">
                            <i class="bi bi-pencil-square text-base"></i>
                        </span>
                        <input type="text" id="kategori_lainnya" name="kategori_lainnya" maxlength="150"
                               placeholder="Tuliskan kategori kendala yang Anda maksud..."
                               class="w-full pl-11 pr-4 py-3 rounded-xl bg-orange-50/40 border border-orange-200 focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all outline-hidden">
                    </div>
                    <p class="text-xs text-slate-500 mt-1.5 flex items-center gap-1.5">
                        <i class="bi bi-info-circle-fill text-orange-500 text-xs"></i>
                        <span>Sebutkan topik kendala secara ringkas karena Anda memilih opsi Lain-lain / Lainnya pada kategori di atas.</span>
                    </p>
                </div>

                <!-- 4. Grid: Prioritas & Subjek -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    
                    <!-- Tingkat Prioritas -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            Tingkat Prioritas <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5" id="prioritasGroup">
                            
                            <!-- Rendah -->
                            <label id="card_prioritas_Rendah" class="relative flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 bg-white cursor-pointer hover:border-slate-300 hover:bg-slate-50/50 transition-all text-center select-none shadow-2xs">
                                <input type="radio" name="prioritas" value="Rendah" onchange="updatePrioritasUI(this.value)" class="sr-only">
                                <span class="p-dot w-2.5 h-2.5 rounded-full bg-slate-400 mb-1.5 transition-all"></span>
                                <span class="p-title text-xs font-semibold text-slate-700">Rendah</span>
                                <span class="p-desc text-[10px] text-slate-400 leading-tight mt-0.5">Umum</span>
                            </label>

                            <!-- Sedang (Default) -->
                            <label id="card_prioritas_Sedang" class="relative flex flex-col items-center justify-center p-3 rounded-xl border border-blue-500 bg-blue-50/80 ring-2 ring-blue-500/20 shadow-xs cursor-pointer transition-all text-center select-none">
                                <input type="radio" name="prioritas" value="Sedang" checked onchange="updatePrioritasUI(this.value)" class="sr-only">
                                <span class="p-dot w-2.5 h-2.5 rounded-full bg-blue-600 mb-1.5 scale-125 transition-all"></span>
                                <span class="p-title text-xs font-bold text-blue-700">Sedang</span>
                                <span class="p-desc text-[10px] text-blue-600 leading-tight mt-0.5">Standar</span>
                            </label>

                            <!-- Tinggi -->
                            <label id="card_prioritas_Tinggi" class="relative flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 bg-white cursor-pointer hover:border-amber-300 hover:bg-amber-50/20 transition-all text-center select-none shadow-2xs">
                                <input type="radio" name="prioritas" value="Tinggi" onchange="updatePrioritasUI(this.value)" class="sr-only">
                                <span class="p-dot w-2.5 h-2.5 rounded-full bg-amber-400 mb-1.5 transition-all"></span>
                                <span class="p-title text-xs font-semibold text-slate-700">Tinggi</span>
                                <span class="p-desc text-[10px] text-slate-400 leading-tight mt-0.5">Mendesak</span>
                            </label>

                            <!-- Darurat -->
                            <label id="card_prioritas_Darurat" class="relative flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 bg-white cursor-pointer hover:border-rose-300 hover:bg-rose-50/20 transition-all text-center select-none shadow-2xs">
                                <input type="radio" name="prioritas" value="Darurat" onchange="updatePrioritasUI(this.value)" class="sr-only">
                                <span class="p-dot w-2.5 h-2.5 rounded-full bg-rose-400 mb-1.5 transition-all"></span>
                                <span class="p-title text-xs font-semibold text-slate-700">Darurat</span>
                                <span class="p-desc text-[10px] text-slate-400 leading-tight mt-0.5">Kritis</span>
                            </label>

                        </div>
                        <p class="text-xs text-slate-400 mt-1.5">Tingkat urgensi penanganan kendala ini.</p>
                    </div>

                    <!-- Subjek / Judul Kendala -->
                    <div>
                        <label for="subjek" class="block text-sm font-bold text-slate-700 mb-2">
                            Subjek / Ringkasan Kendala <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                <i class="bi bi-chat-square-text-fill text-base"></i>
                            </span>
                            <input type="text" id="subjek" name="subjek" required
                                   placeholder="Contoh: Kendala verifikasi berkas mahasiswa 130220469"
                                   class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all outline-hidden">
                        </div>
                        <p class="text-xs text-slate-400 mt-1.5">Ringkasan singkat kendala yang dialami.</p>
                    </div>

                </div>

                <!-- 5. Deskripsi Rinci (TinyMCE) -->
                <div class="pt-2">
                    <label for="deskripsi" class="block text-sm font-bold text-slate-700 mb-2">
                        4. Deskripsi Kendala Rinci <span class="text-rose-500">*</span>
                        <span class="text-xs font-normal text-slate-400 ml-1">(Dilengkapi Rich Text Editor)</span>
                    </label>
                    <textarea id="deskripsi" name="deskripsi" rows="6"
                              placeholder="Tuliskan rincian kendala Anda di sini (langkah kejadian, pesan error, NIM mahasiswa atau ruang terkait)..."></textarea>
                    <p class="text-xs text-slate-400 mt-1.5">
                        Jelaskan secara detail langkah yang dilakukan, pesan error, nama/NIM mahasiswa terkait, atau kronologi kendala.
                    </p>
                </div>

                <!-- 6. Upload Lampiran Berkas / Screenshot -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">
                        Lampiran Berkas / Screenshot <span class="text-xs font-normal text-slate-400">(Opsional)</span>
                    </label>
                    <div class="relative border-2 border-dashed border-slate-200 hover:border-orange-400 bg-slate-50/60 rounded-2xl p-6 transition-all group text-center cursor-pointer"
                         onclick="document.getElementById('lampiran').click()">
                        <input type="file" id="lampiran" name="lampiran" class="hidden"
                               accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.zip,.rar"
                               onchange="handleFileSelected(this)">
                        
                        <div id="upload-placeholder" class="flex flex-col items-center justify-center pointer-events-none">
                            <div class="w-12 h-12 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                <i class="bi bi-cloud-arrow-up-fill text-2xl"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-700">
                                Klik untuk mengunggah tangkapan layar atau berkas pendukung
                            </p>
                            <p class="text-xs text-slate-400 mt-1">
                                Format didukung: JPG, PNG, PDF, DOCX, ZIP (Maksimal 10 MB)
                            </p>
                        </div>

                        <!-- Selected File Preview -->
                        <div id="file-preview" class="hidden flex items-center justify-between p-3 bg-white rounded-xl border border-orange-200 shadow-xs max-w-md mx-auto text-left">
                            <div class="flex items-center gap-3 overflow-hidden">
                                <div class="w-9 h-9 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center shrink-0">
                                    <i class="bi bi-file-earmark-check text-lg"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <p id="file-name" class="text-xs font-bold text-slate-800 truncate">filename.pdf</p>
                                    <p id="file-size" class="text-[11px] text-slate-400">0 KB</p>
                                </div>
                            </div>
                            <button type="button" onclick="event.stopPropagation(); clearSelectedFile();" 
                                    class="text-slate-400 hover:text-rose-500 p-1.5 transition-colors">
                                <i class="bi bi-x-circle-fill text-lg"></i>
                            </button>
                        </div>

                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-end gap-3">
                    <a href="<?= site_url('dosen/ticketing/riwayat') ?>" 
                       class="w-full sm:w-auto px-5 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-bold text-center transition-colors">
                        Batal
                    </a>
                    <button type="submit" id="btnSubmitTicket"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3 rounded-xl bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white text-sm font-bold shadow-lg shadow-orange-500/25 hover:shadow-orange-500/35 transition-all">
                        <i class="bi bi-send-fill text-sm"></i>
                        <span>Kirim Tiket Kendala</span>
                    </button>
                </div>

            </form>

        </div>

    </main>

    <!-- Client-side Logic for Dynamic Kategori & TinyMCE -->
    <script>
        // Data Kategori per Unit
        const unitKategoriMap = <?= json_encode($unit_kategori_map); ?>;

        function handleUnitChange(selectedUnit) {
            const kategoriSelect = document.getElementById('kategori');
            const kategoriHint = document.getElementById('kategori-hint');
            
            // Reset input kategori lainnya
            handleKategoriChange('');

            // Clear current options
            kategoriSelect.innerHTML = '';

            if (!selectedUnit || !unitKategoriMap[selectedUnit]) {
                kategoriSelect.disabled = true;
                kategoriSelect.classList.add('cursor-not-allowed', 'bg-slate-100');
                kategoriSelect.classList.remove('cursor-pointer', 'bg-slate-50');
                const defaultOpt = document.createElement('option');
                defaultOpt.value = '';
                defaultOpt.textContent = '-- Silakan pilih Unit yang Dituju terlebih dahulu --';
                kategoriSelect.appendChild(defaultOpt);
                kategoriHint.textContent = 'Kategori akan otomatis disesuaikan dengan unit yang Anda pilih di atas.';
                return;
            }

            // Enable select
            kategoriSelect.disabled = false;
            kategoriSelect.classList.remove('cursor-not-allowed', 'bg-slate-100');
            kategoriSelect.classList.add('cursor-pointer', 'bg-slate-50');

            // Default prompt option
            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = '-- Pilih Kategori Kendala (' + selectedUnit + ') --';
            kategoriSelect.appendChild(defaultOpt);

            // Populate category options
            const categories = unitKategoriMap[selectedUnit];
            categories.forEach(function(cat) {
                const opt = document.createElement('option');
                opt.value = cat;
                opt.textContent = cat;
                kategoriSelect.appendChild(opt);
            });

            kategoriHint.innerHTML = '<span class="text-emerald-600 font-semibold"><i class="bi bi-check-circle-fill mr-1"></i>Menampilkan ' + categories.length + ' kategori spesifik untuk ' + selectedUnit + '</span>';
        }

        function handleKategoriChange(val) {
            const container = document.getElementById('container_kategori_lainnya');
            const inputLainnya = document.getElementById('kategori_lainnya');
            if (!container || !inputLainnya) return;

            // Trigger when value contains 'lain' (Lain-lain, Lainnya, dll)
            const isLainnya = /lain/i.test((val || '').trim());

            if (isLainnya) {
                container.classList.remove('hidden');
                inputLainnya.required = true;
                setTimeout(function() {
                    inputLainnya.focus();
                }, 100);
            } else {
                container.classList.add('hidden');
                inputLainnya.required = false;
                inputLainnya.value = '';
            }
        }

        // Priority Selector UI Handler
        const prioritasConfig = {
            'Rendah': {
                activeCard: 'border-slate-500 bg-slate-100 ring-2 ring-slate-400/20 shadow-xs',
                inactiveCard: 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50 shadow-2xs',
                dotActive: 'bg-slate-600 scale-125',
                dotInactive: 'bg-slate-400',
                titleActive: 'text-slate-800 font-bold',
                titleInactive: 'text-slate-700 font-semibold',
                descActive: 'text-slate-600',
                descInactive: 'text-slate-400'
            },
            'Sedang': {
                activeCard: 'border-blue-500 bg-blue-50/80 ring-2 ring-blue-500/20 shadow-xs',
                inactiveCard: 'border-slate-200 bg-white hover:border-blue-300 hover:bg-blue-50/20 shadow-2xs',
                dotActive: 'bg-blue-600 scale-125',
                dotInactive: 'bg-blue-400',
                titleActive: 'text-blue-700 font-bold',
                titleInactive: 'text-slate-700 font-semibold',
                descActive: 'text-blue-600',
                descInactive: 'text-slate-400'
            },
            'Tinggi': {
                activeCard: 'border-amber-500 bg-amber-50/80 ring-2 ring-amber-500/20 shadow-xs',
                inactiveCard: 'border-slate-200 bg-white hover:border-amber-300 hover:bg-amber-50/20 shadow-2xs',
                dotActive: 'bg-amber-500 scale-125',
                dotInactive: 'bg-amber-400',
                titleActive: 'text-amber-700 font-bold',
                titleInactive: 'text-slate-700 font-semibold',
                descActive: 'text-amber-600',
                descInactive: 'text-slate-400'
            },
            'Darurat': {
                activeCard: 'border-rose-500 bg-rose-50/80 ring-2 ring-rose-500/20 shadow-xs',
                inactiveCard: 'border-slate-200 bg-white hover:border-rose-300 hover:bg-rose-50/20 shadow-2xs',
                dotActive: 'bg-rose-600 scale-125',
                dotInactive: 'bg-rose-400',
                titleActive: 'text-rose-700 font-bold',
                titleInactive: 'text-slate-700 font-semibold',
                descActive: 'text-rose-600',
                descInactive: 'text-slate-400'
            }
        };

        function updatePrioritasUI(selectedVal) {
            Object.keys(prioritasConfig).forEach(function(key) {
                const card = document.getElementById('card_prioritas_' + key);
                if (!card) return;
                const dot = card.querySelector('.p-dot');
                const title = card.querySelector('.p-title');
                const desc = card.querySelector('.p-desc');
                const cfg = prioritasConfig[key];

                if (key === selectedVal) {
                    card.className = 'relative flex flex-col items-center justify-center p-3 rounded-xl border cursor-pointer transition-all text-center select-none ' + cfg.activeCard;
                    if (dot) dot.className = 'p-dot w-2.5 h-2.5 rounded-full mb-1.5 transition-all ' + cfg.dotActive;
                    if (title) title.className = 'p-title text-xs ' + cfg.titleActive;
                    if (desc) desc.className = 'p-desc text-[10px] leading-tight mt-0.5 ' + cfg.descActive;
                } else {
                    card.className = 'relative flex flex-col items-center justify-center p-3 rounded-xl border cursor-pointer transition-all text-center select-none ' + cfg.inactiveCard;
                    if (dot) dot.className = 'p-dot w-2.5 h-2.5 rounded-full mb-1.5 transition-all ' + cfg.dotInactive;
                    if (title) title.className = 'p-title text-xs ' + cfg.titleInactive;
                    if (desc) desc.className = 'p-desc text-[10px] leading-tight mt-0.5 ' + cfg.descInactive;
                }
            });
        }

        // Initialize priority selection state and listener
        const initPrioritas = document.querySelector('input[name="prioritas"]:checked');
        if (initPrioritas) {
            updatePrioritasUI(initPrioritas.value);
        }
        document.querySelectorAll('input[name="prioritas"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                updatePrioritasUI(this.value);
            });
        });

        // Initialize TinyMCE for Deskripsi
        tinymce.init({
            selector: '#deskripsi',
            menubar: false,
            statusbar: false,
            plugins: 'lists link code table paste autolink',
            toolbar: 'undo redo | formatselect | bold italic underline forecolor | bullist numlist | link blockquote code | removeformat',
            height: 280,
            skin: 'oxide',
            content_style: `
                @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
                body {
                    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    font-size: 14px;
                    line-height: 1.6;
                    color: #334155;
                    margin: 0;
                    padding: 12px 14px;
                }
                p {
                    margin: 0 0 8px 0;
                    padding: 0;
                    line-height: 1.6;
                }
                .mce-content-body[data-mce-placeholder]:not(.mce-visualblocks)::before {
                    top: 12px !important;
                    left: 14px !important;
                    line-height: 1.6 !important;
                    color: #94a3b8 !important;
                    font-weight: 400 !important;
                }
            `,
            placeholder: "Tuliskan rincian kendala Anda di sini (langkah kejadian, pesan error, NIM mahasiswa atau ruang terkait)...",
            setup: function (editor) {
                editor.on('change keyup', function () {
                    tinymce.triggerSave();
                });
            }
        });

        let isSubmittingTicket = false;

        // Form Submission Validation
        document.getElementById('ticketingForm').addEventListener('submit', function(e) {
            if (isSubmittingTicket) {
                e.preventDefault();
                return false;
            }

            if (typeof tinymce !== 'undefined') {
                tinymce.triggerSave();
            }

            const kategoriSelect = document.getElementById('kategori');
            const kategoriVal = (kategoriSelect ? kategoriSelect.value : '').trim();
            const isLainnya = /lain/i.test(kategoriVal);
            const inputLainnya = document.getElementById('kategori_lainnya');

            if (isLainnya && (!inputLainnya || !inputLainnya.value.trim())) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Keterangan Kategori Belum Diisi',
                    text: 'Karena Anda memilih kategori Lain-lain/Lainnya, harap sebutkan detail kendala pada kolom Keterangan Kategori Lainnya.',
                    confirmButtonColor: '#ea580c'
                });
                if (inputLainnya) {
                    inputLainnya.focus();
                }
                return false;
            }

            const deskripsiInput = document.getElementById('deskripsi');
            const cleanText = deskripsiInput.value.replace(/<[^>]*>/g, '').trim();

            if (!cleanText) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Deskripsi Masih Kosong',
                    text: 'Harap tuliskan penjelasan rinci mengenai kendala Anda pada kolom deskripsi.',
                    confirmButtonColor: '#ea580c'
                });
                return false;
            }

            // Lock submit & show loading state to prevent double clicks
            isSubmittingTicket = true;
            const btn = document.getElementById('btnSubmitTicket');
            if (btn) {
                btn.disabled = true;
                btn.classList.add('opacity-75', 'cursor-not-allowed');
                btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin text-base"></i><span>Mengirim Tiket...</span>';
            }
        });

        // File Uploader Feedback
        function handleFileSelected(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                document.getElementById('upload-placeholder').classList.add('hidden');
                document.getElementById('file-preview').classList.remove('hidden');
                document.getElementById('file-name').textContent = file.name;
                document.getElementById('file-size').textContent = (file.size / 1024).toFixed(1) + ' KB';
            }
        }

        function clearSelectedFile() {
            const input = document.getElementById('lampiran');
            input.value = '';
            document.getElementById('file-preview').classList.add('hidden');
            document.getElementById('upload-placeholder').classList.remove('hidden');
        }
    </script>
</body>
</html>
