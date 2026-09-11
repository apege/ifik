<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Pengaturan Footer — Admin Panel'; ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body, button, input, textarea, select {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }
        .form-input-focus:focus {
            border-color: #ea580c !important;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.15) !important;
        }
        /* Footer preview frame */
        .footer-preview-box {
            background-color: #090d16;
            background-image: radial-gradient(circle at 50% 0%, rgba(234, 88, 12, 0.15) 0%, transparent 70%);
            border-top: 3px solid #ea580c;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col justify-between antialiased selection:bg-orange-500 selection:text-white">

    <!-- Main Container -->
    <main class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 flex-grow space-y-8">

        <!-- Flashdata Alerts -->
        <?php if ($this->session->flashdata('success')): ?>
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-sm font-semibold flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-base shrink-0">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <span><?= $this->session->flashdata('success'); ?></span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold text-xl leading-none">&times;</button>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 text-sm font-semibold flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center text-base shrink-0">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <span><?= $this->session->flashdata('error'); ?></span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 font-bold text-xl leading-none">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Hero Header -->
        <div class="bg-gradient-to-r from-[#7c2d12] via-[#c2410c] to-[#ea580c] rounded-3xl p-6 sm:p-8 text-white relative overflow-hidden shadow-xl">
            <div class="absolute -right-8 -bottom-8 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="space-y-2 max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-orange-100 text-xs font-bold uppercase tracking-wider">
                        <i class="bi bi-shield-lock-fill"></i> Khusus Administrator (Role 1)
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                        Pusat Kendali Pengaturan Footer
                    </h1>
                    <p class="text-xs sm:text-sm text-orange-100/90 font-normal leading-relaxed">
                        Kelola seluruh teks identitas, tautan media sosial resmi, alamat kontak, dan peta Google Maps yang ditampilkan di bagian bawah seluruh halaman portal IFIK.
                    </p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <a href="<?= base_url() ?>" class="px-4 py-2.5 rounded-xl bg-white/15 hover:bg-white/25 border border-white/30 text-white text-xs font-bold flex items-center gap-2 transition shadow-sm">
                        <i class="bi bi-arrow-left text-sm"></i> Kembali ke Halaman Utama
                    </a>
                </div>
            </div>
        </div>

        <!-- Form & Live Preview Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- FORM SETTINGS (Col 7) -->
            <div class="lg:col-span-7 space-y-6">
                <?= form_open('adminfooter/update_settings', ['id' => 'formFooterSettings', 'class' => 'space-y-6']); ?>

                    <!-- Card 1: Identitas & Deskripsi Brand -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-5">
                        <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                            <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-lg font-bold">
                                <i class="bi bi-building"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-base">Identitas &amp; Profil Singkat</h3>
                                <p class="text-xs text-slate-500">Badge kecil, judul institusi, dan deskripsi singkat pada kolom pertama footer.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Badge Teks <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="brand_badge" id="inp_brand_badge" 
                                    value="<?= htmlspecialchars($settings->brand_badge ?? 'TELKOM UNIVERSITY'); ?>" 
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 bg-slate-50/50 form-input-focus transition" 
                                    placeholder="TELKOM UNIVERSITY" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Judul / Nama Fakultas <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="brand_title" id="inp_brand_title" 
                                    value="<?= htmlspecialchars($settings->brand_title ?? 'Fakultas Industri Kreatif'); ?>" 
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 bg-slate-50/50 form-input-focus transition" 
                                    placeholder="Fakultas Industri Kreatif" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Deskripsi Profil Singkat
                            </label>
                            <textarea name="brand_desc" id="inp_brand_desc" rows="3" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-normal text-slate-700 bg-slate-50/50 form-input-focus transition" 
                                placeholder="Pusat unggulan pendidikan industri kreatif..."><?= htmlspecialchars($settings->brand_desc ?? ''); ?></textarea>
                        </div>
                    </div>

                    <!-- Card 2: Media Sosial Resmi -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-5">
                        <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                            <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-lg font-bold">
                                <i class="bi bi-share-fill"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-base">Tautan Media Sosial Resmi</h3>
                                <p class="text-xs text-slate-500">Ikon tombol Instagram, YouTube, dan LinkedIn di bawah profil.</p>
                            </div>
                        </div>

                        <div class="space-y-3.5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                    <i class="fa-brands fa-instagram text-rose-500 mr-1.5 text-sm"></i> URL Akun Instagram
                                </label>
                                <input type="url" name="instagram_url" id="inp_instagram_url" 
                                    value="<?= htmlspecialchars($settings->instagram_url ?? ''); ?>" 
                                    class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 bg-slate-50/50 form-input-focus transition" 
                                    placeholder="https://www.instagram.com/telkomuniversity/">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                    <i class="fa-brands fa-youtube text-red-600 mr-1.5 text-sm"></i> URL Channel YouTube
                                </label>
                                <input type="url" name="youtube_url" id="inp_youtube_url" 
                                    value="<?= htmlspecialchars($settings->youtube_url ?? ''); ?>" 
                                    class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 bg-slate-50/50 form-input-focus transition" 
                                    placeholder="https://www.youtube.com/@TelkomUniversityOfficial">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                    <i class="fa-brands fa-linkedin text-sky-600 mr-1.5 text-sm"></i> URL Profil LinkedIn
                                </label>
                                <input type="url" name="linkedin_url" id="inp_linkedin_url" 
                                    value="<?= htmlspecialchars($settings->linkedin_url ?? ''); ?>" 
                                    class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 bg-slate-50/50 form-input-focus transition" 
                                    placeholder="https://www.linkedin.com/school/telkom-university/">
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Informasi Kontak Kami -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-5">
                        <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                            <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-lg font-bold">
                                <i class="bi bi-telephone-inbound-fill"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-base">Informasi Kontak Kami</h3>
                                <p class="text-xs text-slate-500">Alamat gedung fakultas, email resmi layanan, dan kontak telepon.</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Alamat Kampus Lengkap
                            </label>
                            <textarea name="alamat_kampus" id="inp_alamat_kampus" rows="2" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-normal text-slate-800 bg-slate-50/50 form-input-focus transition" 
                                placeholder="Gedung Sebatik (FIK), Telkom University..."><?= htmlspecialchars($settings->alamat_kampus ?? ''); ?></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Email Resmi Fakultas
                                </label>
                                <input type="email" name="email_resmi" id="inp_email_resmi" 
                                    value="<?= htmlspecialchars($settings->email_resmi ?? 'fik@telkomuniversity.ac.id'); ?>" 
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 bg-slate-50/50 form-input-focus transition" 
                                    placeholder="fik@telkomuniversity.ac.id">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Layanan Telepon
                                </label>
                                <input type="text" name="telepon" id="inp_telepon" 
                                    value="<?= htmlspecialchars($settings->telepon ?? '(022) 756 5923'); ?>" 
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 bg-slate-50/50 form-input-focus transition" 
                                    placeholder="(022) 756 5923">
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: Lokasi Kampus (Google Maps) -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-5">
                        <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                            <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-lg font-bold">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-base">Lokasi Kampus (Google Maps)</h3>
                                <p class="text-xs text-slate-500">Iframe peta Google Maps dan link tombol Buka di Google Maps.</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Google Maps Embed Iframe URL / HTML Tag
                            </label>
                            <textarea name="maps_embed_url" id="inp_maps_embed_url" rows="3" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono text-slate-700 bg-slate-50/50 form-input-focus transition" 
                                placeholder="https://www.google.com/maps/embed?..."><?= htmlspecialchars($settings->maps_embed_url ?? ''); ?></textarea>
                            <p class="text-[11px] text-slate-500 mt-1">Anda dapat menempelkan link <code>src</code> langsung atau menyalin seluruh kode <code>&lt;iframe src="..."&gt;</code> dari Google Maps Share.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Tautan Tombol "Buka Google Maps"
                            </label>
                            <input type="url" name="maps_link_url" id="inp_maps_link_url" 
                                value="<?= htmlspecialchars($settings->maps_link_url ?? 'https://maps.google.com/?q=Telkom+University+Fakultas+Industri+Kreatif'); ?>" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 bg-slate-50/50 form-input-focus transition" 
                                placeholder="https://maps.google.com/?q=...">
                        </div>
                    </div>

                    <!-- Card 5: Footer Bottom & Copyright -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
                        <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                            <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-lg font-bold">
                                <i class="bi bi-c-circle"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-base">Teks Hak Cipta (Copyright)</h3>
                                <p class="text-xs text-slate-500">Teks hak cipta pada baris terbawah footer.</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Teks Copyright (Tahun dinamis otomatis: <?= date('Y'); ?>)
                            </label>
                            <input type="text" name="copyright_text" id="inp_copyright_text" 
                                value="<?= htmlspecialchars($settings->copyright_text ?? 'Fakultas Industri Kreatif - Telkom University. All rights reserved.'); ?>" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 bg-slate-50/50 form-input-focus transition" 
                                placeholder="Fakultas Industri Kreatif - Telkom University. All rights reserved.">
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end pt-2">
                        <button type="submit" class="px-7 py-3 rounded-xl bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white text-xs font-extrabold shadow-md shadow-orange-600/30 flex items-center gap-2 transition hover:scale-[1.02] cursor-pointer">
                            <i class="bi bi-check2-circle text-base"></i> Simpan Konfigurasi Footer
                        </button>
                    </div>

                <?= form_close(); ?>
            </div>

            <!-- LIVE PREVIEW PANEL (Col 5) -->
            <div class="lg:col-span-5 space-y-4 lg:sticky lg:top-24">
                <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3">
                        <div class="flex items-center gap-2 text-xs font-extrabold text-slate-800 uppercase tracking-wider">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Live Preview Footer
                        </div>
                        <span class="text-[11px] font-medium text-slate-400">Pratinjau visual instan</span>
                    </div>

                    <!-- Visual Mini Footer Box -->
                    <div class="footer-preview-box rounded-2xl p-5 text-slate-200 text-xs space-y-5 shadow-2xl">
                        
                        <!-- Col 1 Preview -->
                        <div class="space-y-2.5">
                            <div id="prev_brand_badge" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-orange-500/20 text-orange-400 border border-orange-500/30 uppercase tracking-wider">
                                <?= htmlspecialchars($settings->brand_badge ?? 'TELKOM UNIVERSITY'); ?>
                            </div>
                            <h4 id="prev_brand_title" class="text-white font-extrabold text-base leading-tight">
                                <?= htmlspecialchars($settings->brand_title ?? 'Fakultas Industri Kreatif'); ?>
                            </h4>
                            <p id="prev_brand_desc" class="text-slate-400 text-[11px] leading-relaxed">
                                <?= htmlspecialchars($settings->brand_desc ?? 'Pusat unggulan pendidikan industri kreatif yang menghasilkan lulusan berkarakter, inovatif, dan siap bersaing di tingkat global.'); ?>
                            </p>
                            <!-- Social preview -->
                            <div class="flex gap-2 pt-1">
                                <span class="w-7 h-7 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-300 text-xs">
                                    <i class="fa-brands fa-instagram"></i>
                                </span>
                                <span class="w-7 h-7 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-300 text-xs">
                                    <i class="fa-brands fa-youtube"></i>
                                </span>
                                <span class="w-7 h-7 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-300 text-xs">
                                    <i class="fa-brands fa-linkedin"></i>
                                </span>
                            </div>
                        </div>

                        <div class="border-t border-white/10 pt-4 space-y-3">
                            <div class="text-[11px] font-extrabold uppercase text-orange-500 tracking-wider">Kontak Kami</div>
                            
                            <div class="flex items-start gap-2.5 p-2 rounded-lg bg-white/5 border border-white/5">
                                <i class="bi bi-geo-alt-fill text-orange-500 mt-0.5 text-xs"></i>
                                <div>
                                    <div class="text-[9px] text-orange-400 font-bold uppercase tracking-wider">Alamat Kampus</div>
                                    <div id="prev_alamat" class="text-[11px] text-slate-300 font-normal leading-tight mt-0.5">
                                        <?= htmlspecialchars($settings->alamat_kampus ?? 'Gedung Sebatik (FIK), Telkom University, Bandung, Jawa Barat 40287'); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-start gap-2.5 p-2 rounded-lg bg-white/5 border border-white/5">
                                <i class="bi bi-envelope-fill text-orange-500 mt-0.5 text-xs"></i>
                                <div>
                                    <div class="text-[9px] text-orange-400 font-bold uppercase tracking-wider">Email Resmi</div>
                                    <div id="prev_email" class="text-[11px] text-slate-300 font-normal leading-tight mt-0.5">
                                        <?= htmlspecialchars($settings->email_resmi ?? 'fik@telkomuniversity.ac.id'); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-start gap-2.5 p-2 rounded-lg bg-white/5 border border-white/5">
                                <i class="bi bi-telephone-fill text-orange-500 mt-0.5 text-xs"></i>
                                <div>
                                    <div class="text-[9px] text-orange-400 font-bold uppercase tracking-wider">Layanan Telepon</div>
                                    <div id="prev_telepon" class="text-[11px] text-slate-300 font-normal leading-tight mt-0.5">
                                        <?= htmlspecialchars($settings->telepon ?? '(022) 756 5923'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Copyright mini -->
                        <div class="border-t border-white/10 pt-3 flex items-center justify-between text-[10px] text-slate-500">
                            <div>
                                &copy; <?= date('Y'); ?> <span id="prev_copyright"><?= htmlspecialchars($settings->copyright_text ?? 'Fakultas Industri Kreatif - Telkom University. All rights reserved.'); ?></span>
                            </div>
                            <span class="text-orange-500 font-bold flex items-center gap-1">
                                Atas <i class="bi bi-arrow-up-short"></i>
                            </span>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </main>

    <script>
        // Live Preview Synchronization
        const inpBadge = document.getElementById('inp_brand_badge');
        const inpTitle = document.getElementById('inp_brand_title');
        const inpDesc = document.getElementById('inp_brand_desc');
        const inpAlamat = document.getElementById('inp_alamat_kampus');
        const inpEmail = document.getElementById('inp_email_resmi');
        const inpTelepon = document.getElementById('inp_telepon');
        const inpCopyright = document.getElementById('inp_copyright_text');

        const prevBadge = document.getElementById('prev_brand_badge');
        const prevTitle = document.getElementById('prev_brand_title');
        const prevDesc = document.getElementById('prev_brand_desc');
        const prevAlamat = document.getElementById('prev_alamat');
        const prevEmail = document.getElementById('prev_email');
        const prevTelepon = document.getElementById('prev_telepon');
        const prevCopyright = document.getElementById('prev_copyright');

        if (inpBadge && prevBadge) {
            inpBadge.addEventListener('input', () => {
                prevBadge.textContent = inpBadge.value.trim() || 'TELKOM UNIVERSITY';
            });
        }
        if (inpTitle && prevTitle) {
            inpTitle.addEventListener('input', () => {
                prevTitle.textContent = inpTitle.value.trim() || 'Fakultas Industri Kreatif';
            });
        }
        if (inpDesc && prevDesc) {
            inpDesc.addEventListener('input', () => {
                prevDesc.textContent = inpDesc.value.trim() || 'Pusat unggulan pendidikan industri kreatif...';
            });
        }
        if (inpAlamat && prevAlamat) {
            inpAlamat.addEventListener('input', () => {
                prevAlamat.textContent = inpAlamat.value.trim() || '-';
            });
        }
        if (inpEmail && prevEmail) {
            inpEmail.addEventListener('input', () => {
                prevEmail.textContent = inpEmail.value.trim() || '-';
            });
        }
        if (inpTelepon && prevTelepon) {
            inpTelepon.addEventListener('input', () => {
                prevTelepon.textContent = inpTelepon.value.trim() || '-';
            });
        }
        if (inpCopyright && prevCopyright) {
            inpCopyright.addEventListener('input', () => {
                prevCopyright.textContent = inpCopyright.value.trim() || 'Fakultas Industri Kreatif - Telkom University. All rights reserved.';
            });
        }
    </script>

    <!-- Global Custom Circle Cursor -->
    <?php $this->load->view('partials/custom_cursor'); ?>
</body>
</html>
