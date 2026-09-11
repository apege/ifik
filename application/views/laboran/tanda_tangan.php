<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Pengaturan Tanda Tangan Digital - Panel Laboran') ?></title>
    
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
    
    <!-- Google Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Signature Pad JS -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

    <style>
        body, button, input, textarea, select {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        body {
            padding-left: 0;
        }
        @media (min-width: 1024px) {
            body {
                padding-left: 76px; /* space for left floating trigger */
            }
        }

        .canvas-container {
            position: relative;
            width: 100%;
            height: 230px;
            background-color: #ffffff;
            border-radius: 1rem;
            border: 2px dashed #cbd5e1;
            overflow: hidden;
            touch-action: none;
            transition: all 0.2s ease;
        }
        .canvas-container:hover {
            border-color: #ea580c;
        }
        .canvas-baseline {
            position: absolute;
            left: 8%;
            right: 8%;
            bottom: 45px;
            height: 1px;
            border-bottom: 1px dashed #cbd5e1;
            pointer-events: none;
        }

        /* Checkerboard pattern for transparent preview */
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
<body class="bg-slate-50 min-h-screen text-slate-800 antialiased selection:bg-orange-500 selection:text-white">

    <!-- Universal Curved Sidebar Component -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <!-- Main Content Container -->
    <main class="min-h-screen p-6 sm:p-8 lg:p-10 max-w-7xl mx-auto">

        <!-- Top Header Navigation -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-100 text-orange-700 text-xs font-bold uppercase tracking-wider mb-2">
                    <i class="bi bi-pen-fill"></i>
                    <span>Operasional Laboratorium</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Tanda Tangan Digital Laboran
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Kelola tanda tangan resmi Anda untuk diterbitkan pada <strong>Surat Resmi Persetujuan Peminjaman Ruangan</strong> ketika Anda menyetujui permohonan.
                </p>
            </div>

            <!-- Profile Info Badge -->
            <div class="flex items-center gap-3 bg-white px-4 py-3 rounded-2xl border border-slate-200/80 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-orange-600 to-amber-500 text-white flex items-center justify-center font-extrabold text-base shadow-sm shadow-orange-500/30">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-800 leading-tight"><?= htmlspecialchars($nama ?? $this->session->userdata('name') ?? 'Laboran FIK'); ?></p>
                    <p class="text-[11px] text-slate-500 mt-0.5">NIP/NIDN: <?= htmlspecialchars($nip ?? '-'); ?> · <span class="text-orange-600 font-semibold">Staff Laboran</span></p>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if ($this->session->flashdata('success')): ?>
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3.5 rounded-2xl flex items-center gap-3 shadow-sm animate-fade-in">
                <div class="w-8 h-8 bg-emerald-500 text-white rounded-xl flex items-center justify-center shrink-0 text-base">
                    <i class="bi bi-check-lg"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-emerald-900">Berhasil Disimpan</h4>
                    <p class="text-xs text-emerald-700 mt-0.5"><?= $this->session->flashdata('success'); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3.5 rounded-2xl flex items-center gap-3 shadow-sm animate-fade-in">
                <div class="w-8 h-8 bg-rose-500 text-white rounded-xl flex items-center justify-center shrink-0 text-base">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-rose-900">Peringatan</h4>
                    <p class="text-xs text-rose-700 mt-0.5"><?= $this->session->flashdata('error'); ?></p>
                </div>
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
                            <i class="bi bi-patch-check-fill text-orange-500 text-base"></i>
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

                    <!-- Area Preview Gambar -->
                    <div class="checkerboard-bg rounded-2xl border border-slate-200 p-4 h-48 flex items-center justify-center relative group">
                        <?php if (!empty($tanda_tangan) && file_exists(FCPATH . 'uploads/signatures/' . $tanda_tangan)): ?>
                            <img id="activeTtdImg" src="<?= base_url('uploads/signatures/' . $tanda_tangan) ?>" 
                                 alt="Tanda Tangan Digital" 
                                 class="max-h-36 max-w-full object-contain filter drop-shadow-sm transition-transform duration-300 group-hover:scale-105">
                        <?php else: ?>
                            <div class="text-center py-6">
                                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2 text-xl">
                                    <i class="bi bi-pen"></i>
                                </div>
                                <p class="text-xs font-semibold text-slate-500">Belum Ada Tanda Tangan Tersimpan</p>
                                <p class="text-[11px] text-slate-400 mt-1 max-w-[220px]">Goreskan tanda tangan di panel samping atau unggah file gambar TTD Anda.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Identitas Penandatangan -->
                    <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                        <div>
                            <p class="text-slate-400 text-[11px]">Nama Penandatangan</p>
                            <p class="font-bold text-slate-800"><?= htmlspecialchars($nama ?? 'Laboran FIK'); ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-slate-400 text-[11px]">NIP / NIDN</p>
                            <p class="font-bold text-slate-700 font-mono"><?= htmlspecialchars($nip ?? '-'); ?></p>
                        </div>
                    </div>

                    <!-- Action Buttons for Saved Signature -->
                    <?php if (!empty($tanda_tangan)): ?>
                        <div class="mt-5 grid grid-cols-2 gap-3 pt-3 border-t border-slate-100">
                            <a href="<?= site_url('laboran/tanda-tangan/download'); ?>" 
                               class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                                <i class="bi bi-download"></i>
                                <span>Unduh File TTD</span>
                            </a>
                            <button type="button" onclick="confirmDeleteTtd()" 
                                    class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-colors">
                                <i class="bi bi-trash3"></i>
                                <span>Hapus TTD</span>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 2. Informasi Surat Resmi -->
                <div class="bg-gradient-to-br from-amber-500/10 to-orange-500/5 rounded-3xl p-6 border border-orange-200/60 shadow-xs">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-orange-500 text-white flex items-center justify-center shrink-0 text-sm">
                            <i class="bi bi-file-earmark-text-fill"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900">Otomatisasi Surat Resmi</h4>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Saat Anda menyetujui permohonan peminjaman ruangan dengan status <strong>"Disetujui Laboran"</strong>, tanda tangan digital ini akan langsung disematkan pada dokumen resmi yang dapat dicetak atau diunduh sebagai PDF oleh pemohon.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Signature Creation / Upload Studio (7 Cols) -->
            <div class="lg:col-span-7">
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
                    
                    <!-- Tabs Navigation -->
                    <div class="flex items-center justify-between border-b border-slate-200 pb-4 mb-6 flex-wrap gap-3">
                        <div>
                            <h2 class="text-lg font-extrabold text-slate-900">Buat atau Perbarui TTD</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Pilih metode goresan langsung atau unggah gambar.</p>
                        </div>

                        <div class="inline-flex p-1 bg-slate-100 rounded-2xl border border-slate-200/80">
                            <button type="button" id="tabBtnCanvas" onclick="switchTab('canvas')" 
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-white text-orange-600 shadow-xs flex items-center gap-1.5">
                                <i class="bi bi-brush-fill"></i>
                                <span>Canvas Gores</span>
                            </button>
                            <button type="button" id="tabBtnUpload" onclick="switchTab('upload')" 
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all text-slate-600 hover:text-slate-900 flex items-center gap-1.5">
                                <i class="bi bi-cloud-arrow-up-fill"></i>
                                <span>Unggah Gambar</span>
                            </button>
                        </div>
                    </div>

                    <!-- TAB 1: CANVAS DRAWING -->
                    <div id="tabContentCanvas" class="space-y-5">
                        <div class="flex items-center justify-between text-xs text-slate-500">
                            <span class="flex items-center gap-1.5 font-medium">
                                <i class="bi bi-hand-index-thumb text-orange-500"></i>
                                Goreskan tanda tangan Anda di dalam kotak di bawah:
                            </span>

                            <!-- Color Palette Selector -->
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] text-slate-400">Tinta:</span>
                                <button type="button" onclick="setPenColor('#0f172a')" title="Hitam Pekat" 
                                        class="w-5 h-5 rounded-full bg-slate-900 border-2 border-white shadow-xs focus:ring-2 focus:ring-orange-500 pen-color-btn ring-2 ring-orange-500 active-color" data-color="#0f172a"></button>
                                <button type="button" onclick="setPenColor('#1e40af')" title="Biru Resmi" 
                                        class="w-5 h-5 rounded-full bg-blue-800 border-2 border-white shadow-xs focus:ring-2 focus:ring-orange-500 pen-color-btn" data-color="#1e40af"></button>
                            </div>
                        </div>

                        <!-- Canvas Drawing Container -->
                        <div class="canvas-container shadow-inner">
                            <canvas id="signatureCanvas" class="w-full h-full block cursor-crosshair"></canvas>
                            <div class="canvas-baseline"></div>
                            <span class="absolute bottom-2.5 right-4 text-[10px] font-semibold text-slate-300 select-none pointer-events-none uppercase tracking-wider">
                                Garis Dasar Tanda Tangan
                            </span>
                        </div>

                        <!-- Canvas Action Buttons (Clear, Undo) -->
                        <div class="flex items-center justify-between pt-2">
                            <div class="flex items-center gap-2">
                                <button type="button" id="clearCanvasBtn" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 hover:text-slate-800 transition-colors">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                    <span>Bersihkan Kanvas</span>
                                </button>
                                <button type="button" id="undoCanvasBtn" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 hover:text-slate-800 transition-colors">
                                    <i class="bi bi-arrow-left"></i>
                                    <span>Undo Goresan</span>
                                </button>
                            </div>

                            <span class="text-[11px] text-slate-400 italic">
                                *Otomatis disimpan tanpa background (Transparan)
                            </span>
                        </div>

                        <!-- Form Submit Canvas -->
                        <form id="formCanvasTtd" action="<?= site_url('laboran/tanda-tangan/simpan'); ?>" method="POST" class="pt-4 border-t border-slate-100">
                            <input type="hidden" name="tipe" value="canvas">
                            <input type="hidden" name="signature_data" id="signatureDataInput">

                            <button type="submit" id="saveCanvasBtn" 
                                    class="w-full py-3 px-4 rounded-2xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm shadow-md shadow-orange-600/25 hover:shadow-lg hover:shadow-orange-600/35 transition-all flex items-center justify-center gap-2">
                                <i class="bi bi-shield-check text-base"></i>
                                <span>Simpan Tanda Tangan Hasil Goresan</span>
                            </button>
                        </form>
                    </div>

                    <!-- TAB 2: UPLOAD IMAGE FILE -->
                    <div id="tabContentUpload" class="hidden space-y-5">
                        <form id="formUploadTtd" action="<?= site_url('laboran/tanda-tangan/simpan'); ?>" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="tipe" value="upload">

                            <!-- File Upload Area -->
                            <div id="dropZone" 
                                 class="border-2 border-dashed border-slate-300 hover:border-orange-500 rounded-3xl p-8 text-center bg-slate-50 hover:bg-orange-50/20 transition-all cursor-pointer relative group">
                                <input type="file" name="file_ttd" id="fileTtdInput" accept="image/png, image/jpeg, image/jpg" class="hidden">
                                
                                <div id="uploadPlaceholder">
                                    <div class="w-16 h-16 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center mx-auto mb-3 text-2xl group-hover:scale-110 transition-transform">
                                        <i class="bi bi-cloud-arrow-up"></i>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-800">Klik atau seret file gambar TTD Anda ke sini</h4>
                                    <p class="text-xs text-slate-500 mt-1">Mendukung format PNG transparan, JPG, atau JPEG (Maks. 3 MB)</p>
                                    <div class="mt-4 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 shadow-xs">
                                        <i class="bi bi-folder2-open"></i>
                                        <span>Pilih File dari Komputer</span>
                                    </div>
                                </div>

                                <!-- File Preview Area after selection -->
                                <div id="uploadPreviewBox" class="hidden">
                                    <div class="checkerboard-bg rounded-2xl border border-slate-200 p-4 h-40 flex items-center justify-center mb-3">
                                        <img id="filePreviewImg" src="" alt="Preview Upload" class="max-h-32 max-w-full object-contain">
                                    </div>
                                    <p id="fileNameLabel" class="text-xs font-bold text-slate-700 truncate max-w-xs mx-auto"></p>
                                    <p id="fileSizeLabel" class="text-[11px] text-slate-400 mt-0.5"></p>
                                    <button type="button" onclick="resetFileUpload(event)" class="mt-3 text-xs font-bold text-rose-600 hover:underline">
                                        Ganti File Gambar Lain
                                    </button>
                                </div>
                            </div>

                            <div class="mt-4 bg-slate-50 p-4 rounded-2xl border border-slate-200/80 text-xs text-slate-600 flex items-start gap-2.5">
                                <i class="bi bi-info-circle-fill text-orange-500 mt-0.5 shrink-0 text-sm"></i>
                                <p class="leading-relaxed">
                                    <strong>Tips:</strong> Disarankan menggunakan file <strong>PNG dengan latar belakang transparan</strong> agar tanda tangan menyatu alami pada formulir dokumen surat tanpa bayangan persegi putih.
                                </p>
                            </div>

                            <button type="submit" id="saveUploadBtn" 
                                    class="w-full mt-6 py-3 px-4 rounded-2xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm shadow-md shadow-orange-600/25 hover:shadow-lg hover:shadow-orange-600/35 transition-all flex items-center justify-center gap-2">
                                <i class="bi bi-cloud-check-fill text-base"></i>
                                <span>Unggah & Simpan Tanda Tangan</span>
                            </button>
                        </form>
                    </div>

                </div>
            </div>

        </div>

    </main>

    <!-- Hidden form for deleting signature -->
    <form id="deleteTtdForm" action="<?= site_url('laboran/tanda-tangan/hapus'); ?>" method="POST" class="hidden"></form>

    <!-- JavaScript Logic -->
    <script>
        let signaturePad = null;
        let activePenColor = '#0f172a';

        document.addEventListener('DOMContentLoaded', () => {
            initSignaturePad();
            initUploadHandlers();
        });

        // Initialize Signature Pad
        function initSignaturePad() {
            const canvas = document.getElementById('signatureCanvas');
            if (!canvas) return;

            // Resize canvas to match actual rendered size for crisp lines
            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const rect = canvas.getBoundingClientRect();
                canvas.width = rect.width * ratio;
                canvas.height = rect.height * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                if (signaturePad) {
                    signaturePad.clear(); // Clear on resize to avoid distortion
                }
            }

            window.addEventListener("resize", resizeCanvas);

            // Initialize Pad
            signaturePad = new SignaturePad(canvas, {
                minWidth: 1.5,
                maxWidth: 3.5,
                penColor: activePenColor,
                backgroundColor: 'rgba(0,0,0,0)' // Transparent PNG
            });

            resizeCanvas();

            // Clear Button
            document.getElementById('clearCanvasBtn').addEventListener('click', () => {
                signaturePad.clear();
            });

            // Undo Button
            document.getElementById('undoCanvasBtn').addEventListener('click', () => {
                const data = signaturePad.toData();
                if (data && data.length > 0) {
                    data.pop(); // Remove the last stroke
                    signaturePad.fromData(data);
                }
            });

            // Form Submit Interceptor
            document.getElementById('formCanvasTtd').addEventListener('submit', function(e) {
                if (signaturePad.isEmpty()) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Kanvas Masih Kosong',
                        text: 'Silakan goreskan tanda tangan Anda terlebih dahulu pada area kanvas.',
                        confirmButtonColor: '#ea580c'
                    });
                    return false;
                }

                // Get PNG Data URL
                const dataUrl = signaturePad.toDataURL('image/png');
                document.getElementById('signatureDataInput').value = dataUrl;
            });
        }

        // Change Pen Color
        function setPenColor(color) {
            activePenColor = color;
            if (signaturePad) {
                signaturePad.penColor = color;
            }
            document.querySelectorAll('.pen-color-btn').forEach(btn => {
                if (btn.getAttribute('data-color') === color) {
                    btn.classList.add('ring-2', 'ring-orange-500');
                } else {
                    btn.classList.remove('ring-2', 'ring-orange-500');
                }
            });
        }

        // Tab Switcher
        function switchTab(tab) {
            const canvasContent = document.getElementById('tabContentCanvas');
            const uploadContent = document.getElementById('tabContentUpload');
            const tabBtnCanvas = document.getElementById('tabBtnCanvas');
            const tabBtnUpload = document.getElementById('tabBtnUpload');

            if (tab === 'canvas') {
                canvasContent.classList.remove('hidden');
                uploadContent.classList.add('hidden');

                tabBtnCanvas.className = 'px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-white text-orange-600 shadow-xs flex items-center gap-1.5';
                tabBtnUpload.className = 'px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all text-slate-600 hover:text-slate-900 flex items-center gap-1.5';
            } else {
                canvasContent.classList.add('hidden');
                uploadContent.classList.remove('hidden');

                tabBtnUpload.className = 'px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-white text-orange-600 shadow-xs flex items-center gap-1.5';
                tabBtnCanvas.className = 'px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all text-slate-600 hover:text-slate-900 flex items-center gap-1.5';
            }
        }

        // File Upload Handlers
        function initUploadHandlers() {
            const dropZone = document.getElementById('dropZone');
            const fileInput = document.getElementById('fileTtdInput');
            const placeholder = document.getElementById('uploadPlaceholder');
            const previewBox = document.getElementById('uploadPreviewBox');
            const previewImg = document.getElementById('filePreviewImg');
            const nameLabel = document.getElementById('fileNameLabel');
            const sizeLabel = document.getElementById('fileSizeLabel');

            if (!dropZone || !fileInput) return;

            dropZone.addEventListener('click', (e) => {
                if (e.target.tagName !== 'BUTTON') {
                    fileInput.click();
                }
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropZone.classList.add('border-orange-500', 'bg-orange-50/40');
                });
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropZone.classList.remove('border-orange-500', 'bg-orange-50/40');
                });
            });

            dropZone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    handleFileSelection(files[0]);
                }
            });

            fileInput.addEventListener('change', (e) => {
                if (e.target.files && e.target.files[0]) {
                    handleFileSelection(e.target.files[0]);
                }
            });

            function handleFileSelection(file) {
                if (!file.type.match('image.*')) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format Tidak Didukung',
                        text: 'Silakan pilih file gambar dengan format PNG, JPG, atau JPEG.',
                        confirmButtonColor: '#ea580c'
                    });
                    fileInput.value = '';
                    return;
                }

                if (file.size > 3 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ukuran Terlalu Besar',
                        text: 'Ukuran file gambar maksimal adalah 3 MB.',
                        confirmButtonColor: '#ea580c'
                    });
                    fileInput.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    nameLabel.textContent = file.name;
                    sizeLabel.textContent = (file.size / 1024).toFixed(1) + ' KB';
                    placeholder.classList.add('hidden');
                    previewBox.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }

            document.getElementById('formUploadTtd').addEventListener('submit', function(e) {
                if (!fileInput.files || fileInput.files.length === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Belum Ada File Dipilih',
                        text: 'Silakan pilih file gambar tanda tangan terlebih dahulu.',
                        confirmButtonColor: '#ea580c'
                    });
                    return false;
                }
            });
        }

        function resetFileUpload(e) {
            e.stopPropagation();
            const fileInput = document.getElementById('fileTtdInput');
            const placeholder = document.getElementById('uploadPlaceholder');
            const previewBox = document.getElementById('uploadPreviewBox');
            if (fileInput) fileInput.value = '';
            if (placeholder) placeholder.classList.remove('hidden');
            if (previewBox) previewBox.classList.add('hidden');
        }

        // SweetAlert Delete Confirmation
        function confirmDeleteTtd() {
            Swal.fire({
                title: 'Hapus Tanda Tangan?',
                text: 'Tanda tangan digital Anda akan dihapus permanen dari server. Anda dapat membuat atau mengunggah yang baru kapan saja.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus Sekarang',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteTtdForm').submit();
                }
            });
        }
    </script>
</body>
</html>
