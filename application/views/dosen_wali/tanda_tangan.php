<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Pengaturan Tanda Tangan Digital Dosen — IFIK Portal'; ?></title>
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
    <!-- Signature Pad JS (Smooth Canvas Drawing) -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

    <style>
        body, button, input, textarea, select {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }
        .canvas-container {
            position: relative;
            width: 100%;
            height: 220px;
            background-color: #ffffff;
            border-radius: 1rem;
            border: 2px dashed #cbd5e1;
            overflow: hidden;
            touch-action: none;
        }
        .canvas-container:hover {
            border-color: #ea580c;
        }
        .canvas-baseline {
            position: absolute;
            left: 10%;
            right: 10%;
            bottom: 45px;
            height: 1px;
            border-bottom: 1px dashed #cbd5e1;
            pointer-events: none;
        }
        /* Checkerboard pattern for showing transparent background */
        .checkerboard-bg {
            background-color: #ffffff;
            background-image: 
                linear-gradient(45deg, #f1f5f9 25%, transparent 25%), 
                linear-gradient(-45deg, #f1f5f9 25%, transparent 25%), 
                linear-gradient(45deg, transparent 75%, #f1f5f9 75%), 
                linear-gradient(-45deg, transparent 75%, #f1f5f9 75%);
            background-size: 14px 14px;
            background-position: 0 0, 0 7px, 7px -7px, -7px 0px;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-orange-50/20 to-slate-100 min-h-screen text-slate-800 antialiased">

    <!-- Include Dosen Sidebar -->
    <?php $this->load->view('partials/dosen_sidebar'); ?>

    <!-- Main Content -->
    <main class="min-h-screen p-6 sm:p-8 lg:p-10 max-w-6xl mx-auto">

        <!-- Top Header Navigation -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-100/80 text-orange-700 text-xs font-bold uppercase tracking-wider mb-2">
                    <i class="bi bi-pen-fill"></i>
                    <span>Pengaturan Dosen</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Tanda Tangan Digital Dosen
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Kelola tanda tangan resmi Anda (format PNG transparan / tanpa background) untuk pengesahan berkas & persetujuan Tugas Akhir mahasiswa.
                </p>
            </div>

            <!-- Dosen Info Card -->
            <div class="flex items-center gap-3 bg-white/90 backdrop-blur-md px-4 py-3 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="w-10 h-10 rounded-xl bg-orange-500 text-white flex items-center justify-center font-extrabold text-base shadow-sm shadow-orange-500/30">
                    <i class="bi bi-person-check-fill"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-800 leading-tight"><?= htmlspecialchars($dosen_info['nama_dosen'] ?? $this->session->userdata('name') ?? 'Dosen'); ?></p>
                    <p class="text-[11px] text-slate-500 mt-0.5">NIP: <?= htmlspecialchars($nip ?? '-'); ?> · <span class="text-orange-600 font-semibold"><?= htmlspecialchars($dosen_info['jurusan'] ?? $dosen_info['kejuruan'] ?? 'Informatika'); ?></span></p>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if ($this->session->flashdata('success')): ?>
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center gap-3 shadow-xs animate-fade-in">
                <div class="w-7 h-7 bg-emerald-500 text-white rounded-lg flex items-center justify-center shrink-0 text-sm">
                    <i class="bi bi-check-lg"></i>
                </div>
                <div class="text-xs font-bold"><?= $this->session->flashdata('success'); ?></div>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl flex items-center gap-3 shadow-xs animate-fade-in">
                <div class="w-7 h-7 bg-rose-500 text-white rounded-lg flex items-center justify-center shrink-0 text-sm">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div class="text-xs font-bold"><?= $this->session->flashdata('error'); ?></div>
            </div>
        <?php endif; ?>

        <!-- Main Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- LEFT COLUMN: Status & Live Preview TTD (5 Cols) -->
            <div class="lg:col-span-5 space-y-6">

                <!-- 1. Card Status TTD Aktif -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm relative overflow-hidden">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <i class="bi bi-patch-check-fill text-orange-500"></i>
                            Tanda Tangan Aktif
                        </h3>
                        <?php if (!empty($tanda_tangan)): ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Tersimpan & Aktif
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                Belum Ada TTD
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Specimen Frame Container with Transparent Checkerboard BG -->
                    <div class="checkerboard-bg rounded-2xl border border-slate-200 p-6 flex flex-col items-center justify-center min-h-[220px] relative overflow-hidden">
                        <?php if (!empty($tanda_tangan)): ?>
                            <span class="absolute top-2.5 right-3 px-2 py-0.5 rounded-md bg-white/90 border border-slate-200 text-[10px] font-bold text-emerald-700 flex items-center gap-1 shadow-xs">
                                <i class="bi bi-transparency text-xs"></i> Latar Transparan (PNG)
                            </span>

                            <div class="w-full flex items-center justify-center py-5">
                                <img id="activeTtdImg" src="<?= base_url('uploads/signatures/' . $tanda_tangan) ?>" 
                                     alt="Tanda Tangan <?= htmlspecialchars($dosen_info['nama_dosen'] ?? 'Dosen'); ?>" 
                                     class="max-h-36 max-w-full object-contain filter drop-shadow-sm transition-transform hover:scale-105 duration-300">
                            </div>
                            <div class="w-full pt-3 mt-2 border-t border-slate-200/80 bg-white/80 backdrop-blur-xs rounded-xl p-2.5 text-center">
                                <p class="text-xs font-bold text-slate-800 leading-tight underline decoration-slate-300 underline-offset-4">
                                    <?= htmlspecialchars($dosen_info['nama_dosen'] ?? 'Dosen Pengampu'); ?>
                                </p>
                                <p class="text-[10px] text-slate-500 mt-1 font-mono">
                                    NIP / NIDN: <?= htmlspecialchars($nip ?? '-'); ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-6">
                                <div class="w-14 h-14 mx-auto rounded-2xl bg-orange-100/70 text-orange-500 flex items-center justify-center text-2xl mb-3 shadow-xs">
                                    <i class="bi bi-pen"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-700">Tanda Tangan Belum Tersedia</p>
                                <p class="text-[11px] text-slate-400 mt-1 max-w-[240px] mx-auto">
                                    Silakan goreskan tanda tangan Anda di canvas atau upload gambar tanda tangan pada formulir di sebelah kanan.
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Action Buttons for Existing Signature -->
                    <?php if (!empty($tanda_tangan)): ?>
                        <div class="mt-4 pt-3 border-t border-slate-100 space-y-2">
                            <!-- Download Button (Primary) -->
                            <a href="<?= site_url('dosen/tanda-tangan/download'); ?>" 
                               class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-2xl text-xs font-bold text-white bg-gradient-to-r from-slate-900 to-slate-800 hover:from-black hover:to-slate-900 shadow-md shadow-slate-900/10 hover:shadow-lg transition-all cursor-pointer">
                                <i class="bi bi-download text-sm text-emerald-400"></i>
                                <span>Unduh File Tanda Tangan (.PNG Transparan)</span>
                            </a>

                            <div class="flex items-center justify-between pt-1 text-xs">
                                <a href="<?= base_url('uploads/signatures/' . $tanda_tangan) ?>" target="_blank" 
                                   class="inline-flex items-center gap-1.5 text-slate-500 hover:text-orange-600 transition-colors font-semibold">
                                    <i class="bi bi-box-arrow-up-right text-[11px]"></i>
                                    <span>Buka di Tab Baru</span>
                                </a>

                                <button type="button" onclick="konfirmasiHapusTtd()" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-rose-600 hover:bg-rose-50 border border-transparent hover:border-rose-200 transition-all cursor-pointer">
                                    <i class="bi bi-trash3-fill"></i>
                                    <span>Hapus</span>
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 2. Card Informasi & Ketentuan Penggunaan -->
                <div class="bg-gradient-to-br from-amber-500/5 to-orange-500/10 rounded-3xl p-6 border border-orange-200/60 shadow-xs">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-orange-700 flex items-center gap-2 mb-3">
                        <i class="bi bi-shield-check text-base"></i>
                        Ketentuan Tanda Tangan Digital
                    </h4>
                    <ul class="space-y-2.5 text-xs text-slate-600">
                        <li class="flex items-start gap-2.5">
                            <i class="bi bi-check2-circle text-orange-600 text-sm shrink-0 mt-0.5"></i>
                            <span><strong>Format Transparan (PNG):</strong> Tanda tangan akan disimpan tanpa background putih, sehingga saat ditempelkan pada dokumen berkas mahasiswa hasilnya rapi dan tidak menutupi tulisan lain.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="bi bi-check2-circle text-orange-600 text-sm shrink-0 mt-0.5"></i>
                            <span><strong>Bisa Diunduh (.png):</strong> Anda dapat mengunduh file tanda tangan transparan kapan saja untuk keperluan administrasi lainnya.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="bi bi-check2-circle text-orange-600 text-sm shrink-0 mt-0.5"></i>
                            <span><strong>Keamanan:</strong> Tanda tangan digital hanya digunakan pada saat Anda menekan tombol setujui/approve berkas bimbingan mahasiswa.</span>
                        </li>
                    </ul>
                </div>

            </div>

            <!-- RIGHT COLUMN: Form Buat / Perbarui Tanda Tangan (7 Cols) -->
            <div class="lg:col-span-7 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
                
                <div class="mb-6 flex items-start justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-sm">
                                <i class="bi bi-pencil-square"></i>
                            </span>
                            Buat / Perbarui Tanda Tangan
                        </h2>
                        <p class="text-xs text-slate-500 mt-1">Pilih metode yang diinginkan: gores langsung di layar atau upload foto TTD.</p>
                    </div>

                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-lg">
                        <i class="bi bi-transparency"></i> Auto Transparan
                    </span>
                </div>

                <!-- Tab Navigation Buttons -->
                <div class="flex items-center gap-2 p-1.5 bg-slate-100/80 rounded-2xl mb-6">
                    <button type="button" id="tabBtnCanvas" onclick="switchTtdTab('canvas')" 
                            class="flex-1 flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs font-bold transition-all bg-white text-orange-600 shadow-xs border border-slate-200/60 cursor-pointer">
                        <i class="bi bi-draw-polygon text-sm"></i>
                        <span>1. Gores Langsung di Canvas</span>
                    </button>
                    <button type="button" id="tabBtnUpload" onclick="switchTtdTab('upload')" 
                            class="flex-1 flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs font-bold transition-all text-slate-500 hover:text-slate-800 cursor-pointer">
                        <i class="bi bi-cloud-arrow-up-fill text-sm"></i>
                        <span>2. Upload File Gambar TTD</span>
                    </button>
                </div>

                <!-- TAB 1 CONTENT: Digital Canvas Drawing Pad -->
                <div id="panelCanvas" class="space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500">
                        <span>Gunakan mouse, stylus pen, atau sentuhan jari:</span>
                        <div class="flex items-center gap-3">
                            <!-- Color Picker -->
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] font-semibold text-slate-400">Warna:</span>
                                <button type="button" onclick="setPenColor('#0f172a')" title="Tinta Hitam"
                                        class="w-5 h-5 rounded-full bg-slate-900 border-2 border-white shadow-xs focus:ring-2 focus:ring-orange-500 cursor-pointer"></button>
                                <button type="button" onclick="setPenColor('#1d4ed8')" title="Tinta Biru Resmi"
                                        class="w-5 h-5 rounded-full bg-blue-700 border-2 border-white shadow-xs focus:ring-2 focus:ring-orange-500 cursor-pointer"></button>
                            </div>
                            <!-- Pen Width -->
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] font-semibold text-slate-400">Ketebalan:</span>
                                <select id="penWidthSelect" onchange="setPenWidth(this.value)" class="text-xs bg-slate-50 border border-slate-200 rounded-lg px-2 py-0.5 outline-hidden">
                                    <option value="1.5">Tipis</option>
                                    <option value="2.5" selected>Sedang</option>
                                    <option value="4.0">Tebal</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Canvas Area with Transparent Checkerboard BG -->
                    <div class="canvas-container checkerboard-bg shadow-inner" id="canvasBox">
                        <canvas id="signaturePadCanvas" class="w-full h-full cursor-crosshair"></canvas>
                        <div class="canvas-baseline"></div>
                        <span class="absolute bottom-2 right-3 text-[10px] font-semibold text-slate-300 pointer-events-none uppercase tracking-wider">
                            Goreskan Tanda Tangan di Sini (Hasil Transparan)
                        </span>
                    </div>

                    <!-- Canvas Controls -->
                    <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="clearSignatureCanvas()" 
                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer">
                                <i class="bi bi-eraser-fill"></i>
                                <span>Bersihkan</span>
                            </button>
                            <button type="button" onclick="undoSignatureStroke()" 
                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer">
                                <i class="bi bi-arrow-counterclockwise"></i>
                                <span>Undo</span>
                            </button>
                            <!-- Direct Download from Canvas -->
                            <button type="button" onclick="downloadCanvasTtd()" 
                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 transition-colors shadow-xs cursor-pointer" title="Download gambar tanda tangan yang baru saja digambar">
                                <i class="bi bi-download text-orange-600"></i>
                                <span>Unduh PNG</span>
                            </button>
                        </div>

                        <!-- Form Submit Canvas -->
                        <form id="formCanvasTtd" action="<?= site_url('dosen/tanda-tangan/simpan'); ?>" method="POST" onsubmit="return handleCanvasSubmit(event)">
                            <input type="hidden" name="tipe" value="canvas">
                            <input type="hidden" name="signature_data" id="signature_data_input">
                            <button type="submit" 
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 shadow-md shadow-orange-500/20 hover:shadow-lg transition-all cursor-pointer">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Simpan Tanda Tangan</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- TAB 2 CONTENT: Upload Image File with Auto Background Remover -->
                <div id="panelUpload" class="space-y-5 hidden">

                    <!-- Auto Remove White Background Option Box -->
                    <div class="p-3.5 rounded-2xl bg-orange-50/70 border border-orange-200/80 flex items-start gap-3">
                        <input type="checkbox" id="autoRemoveBgToggle" checked onchange="handleRemoveBgToggle()" 
                               class="mt-1 w-4 h-4 text-orange-600 rounded border-slate-300 focus:ring-orange-500 cursor-pointer">
                        <label for="autoRemoveBgToggle" class="cursor-pointer">
                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <i class="bi bi-magic text-orange-600"></i>
                                Otomatis Hapus Background Putih (Jadikan Transparan)
                            </span>
                            <span class="text-[11px] text-slate-500 block mt-0.5 leading-relaxed">
                                Fitur pintar: Jika Anda mengunggah foto TTD di atas kertas putih, sistem akan otomatis menghapus warna putih kertas sehingga hanya tersisa goresan tanda tangan tanpa background!
                            </span>
                        </label>
                    </div>

                    <form id="formUploadTtd" action="<?= site_url('dosen/tanda-tangan/simpan'); ?>" method="POST" enctype="multipart/form-data" onsubmit="return handleUploadSubmit(event)">
                        <input type="hidden" name="tipe" id="uploadFormTipe" value="upload">
                        <input type="hidden" name="signature_data" id="uploadTransparentBase64">

                        <div class="border-2 border-dashed border-slate-300 hover:border-orange-500 bg-slate-50/70 hover:bg-orange-50/30 rounded-2xl p-8 text-center transition-all group cursor-pointer relative"
                             onclick="document.getElementById('file_ttd_input').click()">
                            
                            <input type="file" id="file_ttd_input" name="file_ttd" accept="image/png,image/jpeg,image/jpg" class="hidden" onchange="previewUploadFile(this)">
                            
                            <div id="uploadPlaceholderBox">
                                <div class="w-14 h-14 mx-auto rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center text-2xl mb-3 group-hover:scale-110 transition-transform">
                                    <i class="bi bi-cloud-arrow-up-fill"></i>
                                </div>
                                <p class="text-sm font-bold text-slate-800">Klik untuk memilih file foto/scan tanda tangan</p>
                                <p class="text-xs text-slate-400 mt-1">Mendukung format PNG, JPG, atau JPEG (Maks. 3 MB)</p>
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-slate-200 rounded-lg text-[10px] font-semibold text-slate-600 mt-3 shadow-xs">
                                    <i class="bi bi-check2-circle text-emerald-600"></i> Mendukung background transparan otomatis
                                </div>
                            </div>

                            <!-- Live Image Preview Box with Checkerboard Transparency Background -->
                            <div id="uploadPreviewBox" class="hidden">
                                <div class="checkerboard-bg rounded-xl border border-slate-200 p-4 max-w-sm mx-auto mb-3">
                                    <img id="uploadPreviewImg" src="#" alt="Preview Tanda Tangan" class="max-h-36 max-w-full object-contain mx-auto filter drop-shadow-sm">
                                </div>
                                <p id="uploadPreviewName" class="text-xs font-bold text-slate-700"></p>
                                <p id="uploadPreviewSize" class="text-[11px] text-slate-400"></p>
                                <div class="flex items-center justify-center gap-4 mt-3">
                                    <!-- Download Processed Transparent PNG -->
                                    <button type="button" onclick="downloadProcessedUpload(event)" class="inline-flex items-center gap-1 text-xs font-bold text-slate-700 hover:text-orange-600 cursor-pointer">
                                        <i class="bi bi-download text-orange-600"></i> Unduh Hasil Transparan (.png)
                                    </button>
                                    <button type="button" onclick="cancelUploadFile(event)" class="inline-flex items-center gap-1 text-xs text-rose-600 font-bold hover:underline cursor-pointer">
                                        <i class="bi bi-x-circle-fill"></i> Ganti File
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4">
                            <button type="submit" 
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 shadow-md shadow-orange-500/20 hover:shadow-lg transition-all cursor-pointer">
                                <i class="bi bi-cloud-arrow-up-fill"></i>
                                <span>Upload & Simpan Tanda Tangan</span>
                            </button>
                        </div>
                    </form>
                </div>

            </div>

        </div>

    </main>

    <!-- Hidden Canvas for Processing Image Transparency -->
    <canvas id="offscreenCanvas" class="hidden"></canvas>

    <!-- Client-side Logic for Signature Pad, Transparency Processing, Download & Tabs -->
    <script>
        let signaturePad = null;
        let strokeHistory = [];
        let rawUploadImageSrc = null;
        let processedTransparentDataUrl = null;

        // Inisialisasi Signature Pad saat halaman siap
        window.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('signaturePadCanvas');
            if (canvas) {
                signaturePad = new SignaturePad(canvas, {
                    penColor: '#0f172a',
                    minWidth: 1.5,
                    maxWidth: 3.5,
                    throttle: 16
                });

                resizeCanvas();
                window.addEventListener('resize', resizeCanvas);

                // Catat history coretan untuk fitur Undo
                signaturePad.addEventListener('afterUpdateStroke', function() {
                    strokeHistory = signaturePad.toData();
                });
            }
        });

        // Sesuaikan ukuran canvas dengan rasio piksel device (HiDPI / Retina Sharp)
        function resizeCanvas() {
            const canvas = document.getElementById('signaturePadCanvas');
            if (!canvas || !signaturePad) return;

            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            const rect = canvas.parentElement.getBoundingClientRect();
            
            // Simpan gambar sekarang sebelum resize
            const data = signaturePad.toData();

            canvas.width = rect.width * ratio;
            canvas.height = rect.height * ratio;
            canvas.getContext('2d').scale(ratio, ratio);

            signaturePad.clear();
            if (data && data.length > 0) {
                signaturePad.fromData(data);
            }
        }

        // Tab Switcher
        function switchTtdTab(tab) {
            const btnCanvas = document.getElementById('tabBtnCanvas');
            const btnUpload = document.getElementById('tabBtnUpload');
            const panelCanvas = document.getElementById('panelCanvas');
            const panelUpload = document.getElementById('panelUpload');

            if (tab === 'canvas') {
                btnCanvas.className = 'flex-1 flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs font-bold transition-all bg-white text-orange-600 shadow-xs border border-slate-200/60 cursor-pointer';
                btnUpload.className = 'flex-1 flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs font-bold transition-all text-slate-500 hover:text-slate-800 cursor-pointer';
                panelCanvas.classList.remove('hidden');
                panelUpload.classList.add('hidden');
                setTimeout(resizeCanvas, 50);
            } else {
                btnUpload.className = 'flex-1 flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs font-bold transition-all bg-white text-orange-600 shadow-xs border border-slate-200/60 cursor-pointer';
                btnCanvas.className = 'flex-1 flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs font-bold transition-all text-slate-500 hover:text-slate-800 cursor-pointer';
                panelUpload.classList.remove('hidden');
                panelCanvas.classList.add('hidden');
            }
        }

        // Set Warna Tinta Canvas
        function setPenColor(color) {
            if (signaturePad) {
                signaturePad.penColor = color;
            }
        }

        // Set Ketebalan Tinta Canvas
        function setPenWidth(val) {
            const width = parseFloat(val) || 2.5;
            if (signaturePad) {
                signaturePad.minWidth = width * 0.7;
                signaturePad.maxWidth = width * 1.5;
            }
        }

        // Bersihkan Canvas
        function clearSignatureCanvas() {
            if (signaturePad) {
                signaturePad.clear();
                strokeHistory = [];
            }
        }

        // Undo Coretan Terakhir
        function undoSignatureStroke() {
            if (signaturePad) {
                const data = signaturePad.toData();
                if (data && data.length > 0) {
                    data.pop();
                    signaturePad.fromData(data);
                    strokeHistory = data;
                }
            }
        }

        // Download Hasil Canvas Langsung (.png Transparan)
        function downloadCanvasTtd() {
            if (!signaturePad || signaturePad.isEmpty()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Canvas Masih Kosong',
                    text: 'Silakan goreskan tanda tangan Anda terlebih dahulu untuk mengunduh.',
                    confirmButtonColor: '#ea580c'
                });
                return;
            }

            const dataUrl = signaturePad.toDataURL('image/png');
            const a = document.createElement('a');
            a.href = dataUrl;
            a.download = 'TTD_Digital_Transparan_<?= htmlspecialchars($nip ?? 'dosen'); ?>.png';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        // Handle Submit Canvas
        function handleCanvasSubmit(e) {
            if (!signaturePad || signaturePad.isEmpty()) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Canvas Masih Kosong',
                    text: 'Silakan buat goresan tanda tangan Anda pada canvas terlebih dahulu.',
                    confirmButtonColor: '#ea580c'
                });
                return false;
            }

            // Dapatkan Base64 Data PNG Transparan
            const dataUrl = signaturePad.toDataURL('image/png');
            document.getElementById('signature_data_input').value = dataUrl;
            return true;
        }

        // Algoritma Pintar Penghapus Background Kertas Putih (Hapus BG -> Transparan)
        function makeImageTransparent(imgElement, threshold = 215) {
            const canvas = document.getElementById('offscreenCanvas');
            canvas.width = imgElement.naturalWidth || imgElement.width;
            canvas.height = imgElement.naturalHeight || imgElement.height;
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(imgElement, 0, 0);

            const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const data = imgData.data;

            for (let i = 0; i < data.length; i += 4) {
                const r = data[i];
                const g = data[i + 1];
                const b = data[i + 2];
                // Hitung luminansi/kecerahan (formula perseptual)
                const brightness = (r * 0.299 + g * 0.587 + b * 0.114);

                if (brightness > threshold) {
                    // Kertas putih/terang -> Ubah opacity jadi 0 (100% transparan)
                    data[i + 3] = 0;
                } else if (brightness > threshold - 40) {
                    // Haluskan pinggiran tinta agar tidak pecah (anti-aliasing)
                    const factor = (threshold - brightness) / 40;
                    data[i + 3] = Math.round(data[i + 3] * factor);
                }
            }

            ctx.putImageData(imgData, 0, 0);
            return canvas.toDataURL('image/png');
        }

        // Toggle Hapus Background pada File Upload
        function handleRemoveBgToggle() {
            if (!rawUploadImageSrc) return;

            const isAutoRemove = document.getElementById('autoRemoveBgToggle').checked;
            const previewImg = document.getElementById('uploadPreviewImg');

            if (isAutoRemove) {
                const tempImg = new Image();
                tempImg.crossOrigin = 'anonymous';
                tempImg.onload = function() {
                    processedTransparentDataUrl = makeImageTransparent(tempImg);
                    previewImg.src = processedTransparentDataUrl;
                    document.getElementById('uploadTransparentBase64').value = processedTransparentDataUrl;
                    document.getElementById('uploadFormTipe').value = 'canvas';
                };
                tempImg.src = rawUploadImageSrc;
            } else {
                previewImg.src = rawUploadImageSrc;
                processedTransparentDataUrl = null;
                document.getElementById('uploadTransparentBase64').value = '';
                document.getElementById('uploadFormTipe').value = 'upload';
            }
        }

        // Preview File Upload & Auto-Apply Transparency
        function previewUploadFile(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];

                if (file.size > 3 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ukuran File Terlalu Besar',
                        text: 'Maksimal ukuran file tanda tangan adalah 3 MB.',
                        confirmButtonColor: '#ea580c'
                    });
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    rawUploadImageSrc = e.target.result;
                    document.getElementById('uploadPlaceholderBox').classList.add('hidden');
                    document.getElementById('uploadPreviewBox').classList.remove('hidden');
                    document.getElementById('uploadPreviewName').textContent = file.name;
                    document.getElementById('uploadPreviewSize').textContent = (file.size / 1024).toFixed(1) + ' KB';

                    handleRemoveBgToggle();
                };
                reader.readAsDataURL(file);
            }
        }

        // Download Hasil Upload yang Sudah Transparan
        function downloadProcessedUpload(e) {
            e.stopPropagation();
            const previewImg = document.getElementById('uploadPreviewImg');
            if (!previewImg.src || previewImg.src === '#' || previewImg.src.indexOf('data:') === -1) {
                return;
            }
            const a = document.createElement('a');
            a.href = previewImg.src;
            a.download = 'TTD_Transparan_<?= htmlspecialchars($nip ?? 'dosen'); ?>.png';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        // Batal Upload File
        function cancelUploadFile(e) {
            e.stopPropagation();
            const input = document.getElementById('file_ttd_input');
            input.value = '';
            rawUploadImageSrc = null;
            processedTransparentDataUrl = null;
            document.getElementById('uploadTransparentBase64').value = '';
            document.getElementById('uploadFormTipe').value = 'upload';
            document.getElementById('uploadPreviewBox').classList.add('hidden');
            document.getElementById('uploadPlaceholderBox').classList.remove('hidden');
        }

        // Handle Submit Upload
        function handleUploadSubmit(e) {
            const input = document.getElementById('file_ttd_input');
            const transparentBase64 = document.getElementById('uploadTransparentBase64').value;

            if (!input.files || !input.files[0]) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'File Belum Dipilih',
                    text: 'Silakan pilih file gambar tanda tangan (PNG/JPG) Anda terlebih dahulu.',
                    confirmButtonColor: '#ea580c'
                });
                return false;
            }
            return true;
        }

        // Konfirmasi Hapus Tanda Tangan
        function konfirmasiHapusTtd() {
            Swal.fire({
                title: 'Hapus Tanda Tangan?',
                text: 'Tanda tangan digital aktif Anda akan dihapus dari sistem. Anda dapat membuat atau mengunggahnya kembali kapan saja.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus Tanda Tangan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= site_url("dosen/tanda-tangan/hapus"); ?>';
                }
            });
        }
    </script>
</body>
</html>
