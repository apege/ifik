<?php
/**
 * Modul Perwalian Mahasiswa (Tahap 1 Rencana Studi & Tahap 2 Realisasi Nilai)
 * Dilengkapi validasi anti-duplikasi pada impor nilai Excel.
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Perwalian & Kontrak KRS - IFIK' ?></title>

    <!-- Tailwind CSS & Fonts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SheetJS (XLSX parsing & export) -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#fff7ed', 100: '#ffedd5', 200: '#fed7aa', 300: '#fdba74',
                            400: '#fb923c', 500: '#f97316', 600: '#ea580c', 700: '#c2410c',
                            800: '#9a3412', 900: '#7c2d12',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #1e293b; }
        
        .clay-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05), 0 0 0 1px rgba(226, 232, 240, 0.8);
            transition: all 0.2s ease;
        }

        .stat-card-3d {
            border-radius: 18px;
            padding: 16px 20px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 8px 20px -4px rgba(0,0,0,0.05);
        }

        .btn-brand-primary {
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
            color: white;
            box-shadow: 0 4px 14px rgba(234, 88, 12, 0.3);
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-brand-primary:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(234, 88, 12, 0.4);
        }
        .btn-brand-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .dropzone-upload {
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            background: #f8fafc;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .dropzone-upload:hover, .dropzone-upload.dragover {
            border-color: #ea580c;
            background: #fff7ed;
        }

        .dropzone-box {
            border: 2px dashed #cbd5e1;
            border-radius: 1rem;
            background-color: #f8fafc;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .dropzone-box:hover, .dropzone-box.drag-active {
            border-color: #10b981;
            background-color: #ecfdf5;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50">

    <!-- Curved Sidebar Component -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <!-- Main Container -->
    <main class="page-wrapper-for-sidebar transition-all duration-300 min-h-screen p-4 sm:p-6 lg:p-8">
        <div class="max-w-6xl mx-auto space-y-6">

            <!-- Banner Identitas Mahasiswa & Dosen Wali -->
            <div class="clay-card p-6 sm:p-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6 border-l-8 border-brand-500">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 bg-orange-100 text-brand-700 text-[11px] font-bold rounded-md uppercase tracking-wider">Perwalian Akademik</span>
                        <span class="text-xs text-slate-400">•</span>
                        <span class="text-xs text-slate-500 font-medium"><?= htmlspecialchars($tahun_ajaran) ?> (<?= htmlspecialchars($semester) ?>)</span>
                    </div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight"><?= htmlspecialchars($nama_mhs ?: 'Mahasiswa') ?></h1>
                    <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 pt-1">
                        <span class="flex items-center gap-1 font-mono text-slate-700 font-semibold">
                            <span>🆔</span> NIM: <?= htmlspecialchars($nim) ?>
                        </span>
                        <span>•</span>
                        <span class="flex items-center gap-1">
                            <span>👨‍🏫</span> Dosen Wali: <strong class="text-slate-800"><?= htmlspecialchars($perwalian['nama_dosen_wali'] ?: 'Belum Ditugaskan') ?></strong>
                        </span>
                    </div>
                </div>

                <!-- Status Perwalian Pill -->
                <?php 
                    $st1 = $perwalian['status_tahap1'] ?? 'Draft';
                    $st2 = $perwalian['status_tahap2'] ?? 'Belum Dinilai';
                    $badgeBg = 'bg-slate-100 text-slate-700 border-slate-200';
                    $icon = '📝';
                    $labelDesc = 'Silakan pilih mata kuliah dan upload bukti KRS';

                    if ($st1 === 'Diajukan') {
                        $badgeBg = 'bg-amber-50 text-amber-800 border-amber-300';
                        $icon = '⏳';
                        $labelDesc = 'Menunggu verifikasi & persetujuan Dosen Wali';
                    } elseif ($st1 === 'Disetujui') {
                        $badgeBg = 'bg-emerald-50 text-emerald-800 border-emerald-300';
                        $icon = '✅';
                        $labelDesc = 'Kontrak KRS Tahap 1 disetujui Dosen Wali';
                    } elseif ($st1 === 'Revisi') {
                        $badgeBg = 'bg-rose-50 text-rose-800 border-rose-300';
                        $icon = '⚠️';
                        $labelDesc = 'Ada catatan revisi dari Dosen Wali';
                    }
                ?>
                <div class="flex flex-col items-start md:items-end gap-2">
                    <div class="flex items-center gap-2 flex-wrap justify-end">
                        <div class="flex flex-col items-start md:items-end">
                            <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Tahap 1 (KRS)</span>
                            <div class="px-3 py-1.5 border rounded-xl font-bold text-xs flex items-center gap-1.5 shadow-2xs <?= $badgeBg ?>">
                                <span><?= $icon ?></span>
                                <span><?= htmlspecialchars($st1) ?></span>
                            </div>
                        </div>
                        <?php if ($st1 === 'Disetujui'): ?>
                            <div class="flex flex-col items-start md:items-end">
                                <span class="text-[10px] text-purple-600 font-semibold uppercase tracking-wider">Tahap 2 (Nilai)</span>
                                <?php 
                                    $st2Bg = 'bg-slate-100 text-slate-700 border-slate-200';
                                    if ($st2 === 'Selesai') $st2Bg = 'bg-emerald-100 text-emerald-800 border-emerald-300';
                                    elseif ($st2 === 'Dinilai' || $st2 === 'Proses Penilaian') $st2Bg = 'bg-purple-100 text-purple-800 border-purple-300';
                                ?>
                                <div class="px-3 py-1.5 border rounded-xl font-bold text-xs flex items-center gap-1.5 shadow-2xs <?= $st2Bg ?>">
                                    <span><?= ($st2 === 'Selesai') ? '🏆' : '📊' ?></span>
                                    <span><?= htmlspecialchars($st2) ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <span class="text-[11px] text-slate-500"><?= $labelDesc ?></span>
                </div>
            </div>

            <!-- Pesan Catatan Wali Jika Ada Revisi -->
            <?php if (!empty($perwalian['catatan_wali'])): ?>
                <div class="clay-card p-4 bg-amber-50/80 border-l-4 border-amber-500 text-xs flex items-start gap-3">
                    <span class="text-xl">💬</span>
                    <div>
                        <h4 class="font-bold text-amber-900">Catatan dari Dosen Wali:</h4>
                        <p class="text-amber-800 mt-0.5"><?= nl2br(htmlspecialchars($perwalian['catatan_wali'])) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- 2 Stat Summary -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="stat-card-3d bg-gradient-to-br from-orange-500/10 to-amber-500/10 border-orange-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-orange-700 uppercase tracking-wider">MK Dikontrak Semester Ini</p>
                            <div class="text-2xl font-extrabold text-orange-900 mt-1"><?= count($mk_kontrak) ?> <span class="text-sm font-semibold text-orange-700">Mata Kuliah</span></div>
                        </div>
                        <span class="text-3xl">📚</span>
                    </div>
                </div>
                <div class="stat-card-3d bg-gradient-to-br from-emerald-500/10 to-teal-500/10 border-emerald-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Total Beban SKS Kontrak</p>
                            <div class="text-2xl font-extrabold text-emerald-900 mt-1"><?= (int)$total_sks_kontrak ?> <span class="text-sm font-semibold text-emerald-700">SKS</span></div>
                        </div>
                        <span class="text-3xl">⚖️</span>
                    </div>
                </div>
            </div>

            <?php if ($st1 === 'Disetujui'): ?>
            <!-- =============================================================== -->
            <!-- BAGIAN TAHAP 2: EVALUASI & INPUT NILAI MAHASISWA (FITUR ALIF)   -->
            <!-- =============================================================== -->
            <div class="clay-card overflow-hidden shadow-xl border border-purple-200">
                <!-- Header Ungu/Indigo khas Alif -->
                <div class="px-5 py-4 sm:px-6 sm:py-5 bg-gradient-to-r from-purple-600 via-indigo-600 to-purple-700 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center space-x-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-white shrink-0 shadow-inner">
                            <i class="fa-solid fa-award text-lg"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-extrabold text-base sm:text-lg leading-tight truncate">
                                Evaluasi & Rekap Nilai Tahap 2
                            </h3>
                            <p class="text-xs text-purple-200 truncate mt-0.5">
                                NIM: <span class="font-mono font-bold text-white"><?= htmlspecialchars($nim) ?></span> | 
                                Nama: <span class="font-semibold text-white"><?= htmlspecialchars($nama_mhs) ?></span> | 
                                Periode: <span class="font-semibold text-white"><?= htmlspecialchars($tahun_ajaran . ' ' . $semester) ?></span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 bg-white/20 backdrop-blur-xs text-white rounded-full text-xs font-bold border border-white/30 flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check text-emerald-300"></i> Tahap 1 Disetujui
                        </span>
                        <span class="px-3 py-1 bg-purple-900/60 text-purple-200 rounded-full text-xs font-bold border border-purple-400/40">
                            Tahap 2: <?= htmlspecialchars($perwalian['status_tahap2'] ?? 'Belum Dinilai') ?>
                        </span>
                    </div>
                </div>

                <div class="p-5 sm:p-7 space-y-5">
                    <!-- Info Banner & Quick Action Buttons -->
                    <div class="p-4 rounded-xl bg-purple-50/80 border border-purple-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h4 class="text-xs font-bold text-purple-900 flex items-center">
                                <i class="fa-solid fa-circle-info text-purple-600 mr-1.5"></i> Panduan Evaluasi & Nilai Mutu
                            </h4>
                            <p class="text-[11px] text-purple-800 mt-0.5">
                                Nilai <strong class="text-emerald-700">A, AB, B, BC, C = LULUS 🟢</strong> | Nilai <strong class="text-rose-700">D, E = TIDAK LULUS 🔴</strong>.
                            </p>
                        </div>
                        <div class="flex items-center space-x-2 shrink-0 flex-wrap gap-y-2">
                            <!-- Download Template -->
                            <button type="button" onclick="downloadTemplateExcelSingle()" class="px-3.5 py-2 text-xs font-bold text-purple-700 bg-white border border-purple-200 hover:bg-purple-100/60 rounded-xl shadow-2xs transition flex items-center cursor-pointer">
                                <i class="fa-solid fa-download mr-1.5 text-purple-600"></i> Unduh Format Excel
                            </button>
                            <!-- Single Student Import Excel -->
                            <button type="button" onclick="openImportModalSingle()" class="px-3.5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition flex items-center cursor-pointer" title="Import Nilai dari Excel">
                                <i class="fa-solid fa-file-excel mr-1.5"></i> Import Excel Nilai
                            </button>
                        </div>
                    </div>

                    <!-- Grading Table -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden shadow-2xs">
                        <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid fa-list-check text-purple-600"></i> Form Nilai Mata Kuliah Kontrak
                            </span>
                            <span class="text-xs font-mono font-bold text-purple-700" id="m2GradingProgressText">0 / <?= count($mk_kontrak) ?> Dinilai</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[620px] text-left text-xs">
                                <thead class="bg-slate-100/70 text-slate-600 font-bold uppercase text-[10px] tracking-wider">
                                    <tr>
                                        <th class="py-3 px-3.5 w-12 text-center">No</th>
                                        <th class="py-3 px-3.5">Kode MK</th>
                                        <th class="py-3 px-3.5">Nama Mata Kuliah</th>
                                        <th class="py-3 px-3.5 text-center w-20">SKS</th>
                                        <th class="py-3 px-3.5 text-center w-44">Nilai Huruf</th>
                                        <th class="py-3 px-3.5 text-center w-44">Status Kelulusan</th>
                                    </tr>
                                </thead>
                                <tbody id="m2GradingTableBody" class="divide-y divide-slate-100 text-slate-700">
                                    <?php if (empty($mk_kontrak)): ?>
                                        <tr>
                                            <td colspan="6" class="py-8 text-center text-slate-400">
                                                <i class="fa-solid fa-book-open text-2xl text-slate-300 mb-2 block"></i>
                                                <p class="font-medium text-slate-600">Belum ada mata kuliah yang dikontrak untuk semester ini.</p>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php $no = 1; foreach ($mk_kontrak as $c): 
                                            $idDet = (int)($c['id'] ?? $c['id_detail'] ?? 0);
                                            $grade = strtoupper(trim($c['nilai_huruf'] ?? ''));
                                            $status = trim($c['status_kelulusan'] ?? '');
                                            if (empty($status) && !empty($grade)) {
                                                $status = in_array($grade, ['A', 'AB', 'B', 'BC', 'C']) ? 'Lulus' : 'Tidak Lulus';
                                            }
                                            $isLulus = ($status === 'Lulus');
                                            $isTidakLulus = ($status === 'Tidak Lulus');
                                            $isLocked = (($perwalian['status_tahap2'] ?? '') === 'Selesai');
                                        ?>
                                            <tr class="hover:bg-purple-50/30 transition" id="m2Row_<?= $idDet ?>">
                                                <td class="py-3 px-3.5 text-center font-mono text-slate-400"><?= $no++ ?></td>
                                                <td class="py-3 px-3.5 font-mono font-bold text-purple-900"><?= htmlspecialchars($c['kode_mk']) ?></td>
                                                <td class="py-3 px-3.5 font-semibold text-slate-800"><?= htmlspecialchars($c['nama_mk']) ?></td>
                                                <td class="py-3 px-3.5 text-center font-bold text-slate-700"><?= (int)$c['sks'] ?> SKS</td>
                                                <td class="py-3 px-3.5 text-center">
                                                    <select onchange="onGradeChange(<?= $idDet ?>, this.value)" <?= $isLocked ? 'disabled' : '' ?> class="w-28 px-3 py-1.5 text-xs font-bold text-center bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500/40 font-mono shadow-2xs cursor-pointer disabled:bg-slate-100 disabled:cursor-not-allowed">
                                                        <option value="" <?= empty($grade) ? 'selected' : '' ?>>- Nilai -</option>
                                                        <option value="A" <?= $grade === 'A' ? 'selected' : '' ?> class="text-emerald-700 font-bold">A</option>
                                                        <option value="AB" <?= $grade === 'AB' ? 'selected' : '' ?> class="text-emerald-700 font-bold">AB</option>
                                                        <option value="B" <?= $grade === 'B' ? 'selected' : '' ?> class="text-emerald-700 font-bold">B</option>
                                                        <option value="BC" <?= $grade === 'BC' ? 'selected' : '' ?> class="text-emerald-700 font-bold">BC</option>
                                                        <option value="C" <?= $grade === 'C' ? 'selected' : '' ?> class="text-emerald-700 font-bold">C</option>
                                                        <option value="D" <?= $grade === 'D' ? 'selected' : '' ?> class="text-rose-700 font-bold">D</option>
                                                        <option value="E" <?= $grade === 'E' ? 'selected' : '' ?> class="text-rose-700 font-bold">E</option>
                                                    </select>
                                                </td>
                                                <td class="py-3 px-3.5 text-center" id="m2StatusCol_<?= $idDet ?>">
                                                    <?php if ($isLulus): ?>
                                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                            <i class="fa-solid fa-circle-check mr-1.5 text-emerald-600"></i> Lulus
                                                        </span>
                                                    <?php elseif ($isTidakLulus): ?>
                                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-full bg-rose-100 text-rose-800 border border-rose-300">
                                                            <i class="fa-solid fa-circle-xmark mr-1.5 text-rose-600"></i> Tidak Lulus
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-500 border border-slate-200">
                                                            <i class="fa-solid fa-minus mr-1 text-slate-400"></i> Belum Dinilai
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Kunci & Selesaikan Evaluasi Tahap 2 Box -->
                    <div class="p-4 sm:p-5 rounded-xl border-2 border-emerald-200 bg-emerald-50/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h4 class="text-xs font-bold text-emerald-950 flex items-center">
                                <i class="fa-solid fa-lock text-emerald-700 mr-2"></i> Kunci & Selesaikan Evaluasi Tahap 2
                            </h4>
                            <p class="text-[11px] text-emerald-800 mt-0.5">
                                Setelah semua nilai terisi, kunci evaluasi agar nilai tersimpan secara permanen ke riwayat akademik.
                            </p>
                        </div>
                        <?php if (($perwalian['status_tahap2'] ?? '') === 'Selesai'): ?>
                            <div class="px-5 py-2.5 bg-emerald-100 text-emerald-800 border border-emerald-300 rounded-xl text-xs font-bold flex items-center justify-center">
                                <i class="fa-solid fa-check-double mr-1.5"></i> Evaluasi Tahap 2 Selesai & Terkunci
                            </div>
                        <?php else: ?>
                            <button type="button" onclick="submitTahap2FinalModal()" class="w-full sm:w-auto px-5 py-2.5 text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 rounded-xl shadow-md transition shrink-0 flex items-center justify-center cursor-pointer">
                                <i class="fa-solid fa-lock mr-1.5"></i> Submit & Kunci Perwalian
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- Ringkasan Arsip Berkas KRS Tahap 1 -->
                    <div class="pt-3 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-file-check text-emerald-600"></i> Bukti KRS Tahap 1: 
                            <strong class="text-slate-700 font-mono"><?= htmlspecialchars($perwalian['file_krs'] ?? '-') ?></strong>
                        </span>
                        <?php if (!empty($perwalian['file_krs'])): ?>
                            <button type="button" onclick="previewKRSModal('<?= base_url('uploads/krs/' . $perwalian['file_krs']) ?>', '<?= htmlspecialchars($perwalian['file_krs']) ?>')" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-semibold transition cursor-pointer">
                                Lihat Berkas KRS
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <!-- =============================================================== -->
            <!-- BAGIAN 1: KONTRAK MATA KULIAH YANG AKAN DIAMBIL (TAHAP 1) -->
            <!-- =============================================================== -->
            <div class="clay-card p-6 sm:p-8 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <span>📌</span> Mata Kuliah yang Akan Diambil (Tahap 1 - KRS)
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Daftar mata kuliah yang dikontrak untuk semester berjalan. Data dapat berasal dari import kolektif Admin Prodi atau dipilih mandiri.
                        </p>
                    </div>

                    <?php if($st1 !== 'Disetujui'): ?>
                        <button onclick="openModalPilihMK()" class="btn-brand-primary px-4 py-2 text-xs flex items-center gap-1.5 self-start sm:self-auto">
                            <span>+</span> Tambah / Pilih MK Mandiri
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Tabel MK Dikontrak -->
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100/80 text-slate-700 uppercase font-bold text-[11px] tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="p-3 text-center w-12">No</th>
                                <th class="p-3">Kode MK</th>
                                <th class="p-3">Nama Mata Kuliah</th>
                                <th class="p-3 text-center">SKS</th>
                                <th class="p-3 text-center">Sumber Pengisian</th>
                                <?php if($st1 !== 'Disetujui'): ?>
                                    <th class="p-3 text-center w-20">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if(empty($mk_kontrak)): ?>
                                <tr>
                                    <td colspan="<?= ($st1 !== 'Disetujui') ? 6 : 5 ?>" class="p-8 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <span class="text-3xl mb-2">📋</span>
                                            <p class="font-semibold text-slate-600">Belum ada mata kuliah yang dikontrak</p>
                                            <p class="text-[11px] text-slate-400 mt-0.5">Klik tombol <strong>"+ Tambah / Pilih MK Mandiri"</strong> di atas atau tunggu import rekap dari Admin Prodi.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php
                                    $groupedKontrak = [];
                                    foreach ($mk_kontrak as $c) {
                                        $sem = (int)($c['semester_mk'] ?? ($c['semester'] ?? 1));
                                        $groupedKontrak[$sem][] = $c;
                                    }
                                    ksort($groupedKontrak);
                                    $colCount = ($st1 !== 'Disetujui') ? 6 : 5;
                                ?>
                                <?php foreach ($groupedKontrak as $sem => $items): 
                                    $subSks = array_sum(array_map(fn($x) => (int)$x['sks'], $items));
                                ?>
                                    <tr class="bg-gradient-to-r from-orange-50/90 via-amber-50/60 to-orange-50/90 border-y border-orange-200/80">
                                        <td colspan="<?= $colCount ?>" class="py-2 px-3.5">
                                            <div class="flex items-center justify-between text-xs font-bold text-orange-950">
                                                <span class="flex items-center gap-1.5 uppercase tracking-wide text-[11px]">
                                                    🎓 Semester <?= (int)$sem ?>
                                                </span>
                                                <span class="text-[11px] font-mono font-bold px-2.5 py-0.5 bg-white text-orange-800 rounded-md border border-orange-200">
                                                    Subtotal: <?= $subSks ?> SKS (<?= count($items) ?> MK)
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php $no = 1; foreach($items as $c): ?>
                                        <tr class="hover:bg-orange-50/30 transition">
                                            <td class="p-3 text-center text-slate-400 font-mono"><?= $no++ ?></td>
                                            <td class="p-3 font-mono font-bold text-brand-700"><?= htmlspecialchars($c['kode_mk']) ?></td>
                                            <td class="p-3 font-semibold text-slate-800"><?= htmlspecialchars($c['nama_mk']) ?></td>
                                            <td class="p-3 text-center font-bold text-slate-700"><?= (int)$c['sks'] ?> SKS</td>
                                            <td class="p-3 text-center">
                                                <?php if(!empty($c['is_imported'])): ?>
                                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded-md font-semibold text-[11px]">Import Prodi</span>
                                                <?php else: ?>
                                                    <span class="px-2 py-0.5 bg-amber-50 text-amber-700 rounded-md font-semibold text-[11px]">Input Mandiri</span>
                                                <?php endif; ?>
                                            </td>
                                            <?php if($st1 !== 'Disetujui'): ?>
                                                <td class="p-3 text-center">
                                                    <button onclick="hapusMKFromKontrak('<?= htmlspecialchars($c['kode_mk']) ?>')" class="p-1 hover:bg-rose-100 text-rose-600 rounded transition" title="Hapus dari kontrak">
                                                        🗑️
                                                    </button>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if(!empty($mk_kontrak)): ?>
                            <tfoot class="bg-slate-50 font-bold border-t border-slate-200 text-slate-700">
                                <tr>
                                    <td colspan="3" class="p-3 text-right">Total SKS Kontrak:</td>
                                    <td class="p-3 text-center text-brand-700"><?= (int)$total_sks_kontrak ?> SKS</td>
                                    <td colspan="<?= ($st1 !== 'Disetujui') ? 2 : 1 ?>"></td>
                                </tr>
                            </tfoot>
                        <?php endif; ?>
                    </table>
                </div>

                <!-- =========================================================== -->
                <!-- BAGIAN 2: UPLOAD BUKTI ASLI KRS DARI SIAKAD -->
                <!-- =========================================================== -->
                <div class="pt-4 border-t border-slate-200">
                    <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2 mb-1">
                        <span>📎</span> Bukti Resmi KRS (SIAKAD / i-Gracias) <span class="text-rose-500">*</span>
                    </h3>
                    <p class="text-xs text-slate-500 mb-3">
                        Sebagai bukti sah pengambilan mata kuliah, unggah berkas KRS resmi (format PDF atau gambar screenshot/foto JPG/PNG maks. 5MB).
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                        <?php if($st1 !== 'Disetujui'): ?>
                            <div>
                                <input type="file" id="fileKRSInput" accept=".pdf, .jpg, .jpeg, .png" class="hidden" onchange="uploadBuktiKRS(event)">
                                <div id="krsDropzone" onclick="document.getElementById('fileKRSInput').click()" class="dropzone-upload p-5 text-center cursor-pointer group">
                                    <div class="pointer-events-none">
                                        <span class="text-3xl block mb-1 group-hover:scale-110 transition-transform">📄</span>
                                        <h4 class="text-xs font-bold text-slate-800">Tarik & Lepas (Drag & Drop) atau <span class="text-brand-600 underline">Pilih Berkas</span></h4>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Format: PDF, PNG, JPG (Maks. 5MB)</p>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="p-5 text-center bg-slate-100 border-2 border-dashed border-slate-200 rounded-2xl">
                                <span class="text-2xl">🔒</span>
                                <h4 class="text-xs font-bold text-slate-600 mt-1">Berkas Terkunci</h4>
                                <p class="text-[11px] text-slate-400 mt-0.5">Perwalian telah disetujui, bukti KRS tidak dapat diubah</p>
                            </div>
                        <?php endif; ?>

                        <!-- Current File Preview Box -->
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-between">
                            <div class="flex items-center gap-3 truncate">
                                <span class="text-2xl">📁</span>
                                <div class="truncate">
                                    <p class="text-xs font-bold text-slate-800 truncate" id="krsFileName">
                                        <?= !empty($perwalian['file_krs']) ? htmlspecialchars($perwalian['file_krs']) : 'Belum ada bukti KRS yang diunggah' ?>
                                    </p>
                                    <p class="text-[11px] text-slate-500 mt-0.5" id="krsFileStatus">
                                        <?= !empty($perwalian['file_krs']) ? 'Berkas tersimpan di sistem' : 'Wajib diunggah sebelum submit' ?>
                                    </p>
                                </div>
                            </div>
                            <?php if(!empty($perwalian['file_krs'])): ?>
                                <button type="button" onclick="previewKRSModal('<?= base_url('uploads/krs/' . $perwalian['file_krs']) ?>', '<?= htmlspecialchars($perwalian['file_krs']) ?>')" id="krsFileLink" class="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-xs font-bold transition flex items-center gap-1 shrink-0 cursor-pointer">
                                    <span>👁️</span> Buka File
                                </button>
                            <?php else: ?>
                                <button type="button" onclick="" id="krsFileLink" class="hidden px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-xs font-bold transition flex items-center gap-1 shrink-0 cursor-pointer">
                                    <span>👁️</span> Buka File
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- =========================================================== -->
                <!-- BAGIAN 3: CATATAN & SUBMIT PERWALIAN KE DOSEN WALI -->
                <!-- =========================================================== -->
                <div class="pt-4 border-t border-slate-200 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan untuk Dosen Wali (Opsional):</label>
                        <textarea id="catatanMhs" rows="2" <?= ($st1 === 'Disetujui') ? 'disabled' : '' ?> placeholder="Tuliskan catatan perwalian Anda di sini jika ada..." class="w-full p-3 text-xs bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-500"><?= htmlspecialchars($perwalian['catatan_mahasiswa'] ?? '') ?></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-2">
                        <div class="text-xs text-slate-500">
                            Pastikan data mata kuliah dan bukti berkas KRS sudah lengkap sebelum diajukan.
                        </div>

                        <?php if($st1 !== 'Disetujui'): ?>
                            <button onclick="submitPerwalian()" id="btnSubmitPerwalian" class="btn-brand-primary px-6 py-2.5 text-xs font-bold flex items-center gap-2 justify-center">
                                <span>🚀</span> Ajukan Kontrak Perwalian ke Dosen Wali
                            </button>
                        <?php else: ?>
                            <div class="px-4 py-2 bg-emerald-50 text-emerald-800 border border-emerald-300 rounded-xl text-xs font-bold flex items-center gap-2">
                                <span>🎉</span> Kontrak KRS Anda Telah Disetujui Dosen Wali
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- =============================================================== -->
            <!-- BAGIAN 4: RIWAYAT MATA KULIAH YANG SUDAH DIAMBIL -->
            <!-- =============================================================== -->
            <div class="clay-card p-6 sm:p-8 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-4 border-b border-slate-200">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <span>🎓</span> Riwayat Mata Kuliah yang Sudah Diambil
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Rekam jejak nilai mata kuliah lampau. Nilai <strong>A s.d. C</strong> berstatus <span class="text-emerald-700 font-bold">LULUS (Hijau)</span>, nilai <strong>D s.d. E</strong> berstatus <span class="text-rose-700 font-bold">TIDAK LULUS (Merah)</span>.
                        </p>
                    </div>
                </div>

                <!-- Tabel Riwayat MK -->
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100/80 text-slate-700 uppercase font-bold text-[11px] tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="p-3 text-center w-12">No</th>
                                <th class="p-3">Kode MK</th>
                                <th class="p-3">Nama Mata Kuliah</th>
                                <th class="p-3 text-center">SKS</th>
                                <th class="p-3 text-center">Nilai Mutu</th>
                                <th class="p-3 text-center">Status Kelulusan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if(empty($mk_sudah)): ?>
                                <tr>
                                    <td colspan="6" class="p-6 text-center text-slate-400">Belum ada data mata kuliah yang sudah diambil.</td>
                                </tr>
                            <?php else: ?>
                                <?php
                                    $groupedSudah = [];
                                    foreach ($mk_sudah as $r) {
                                        $sem = (int)($r['semester_mk'] ?? ($r['semester'] ?? 1));
                                        $groupedSudah[$sem][] = $r;
                                    }
                                    ksort($groupedSudah);
                                ?>
                                <?php foreach ($groupedSudah as $sem => $items): 
                                    $subSks = array_sum(array_map(fn($x) => (int)$x['sks'], $items));
                                ?>
                                    <tr class="bg-gradient-to-r from-orange-50/90 via-amber-50/60 to-orange-50/90 border-y border-orange-200/80">
                                        <td colspan="6" class="py-2 px-3.5">
                                            <div class="flex items-center justify-between text-xs font-bold text-orange-950">
                                                <span class="flex items-center gap-1.5 uppercase tracking-wide text-[11px]">
                                                    🎓 Semester <?= (int)$sem ?>
                                                </span>
                                                <span class="text-[11px] font-mono font-bold px-2.5 py-0.5 bg-white text-orange-800 rounded-md border border-orange-200">
                                                    Subtotal: <?= $subSks ?> SKS (<?= count($items) ?> MK)
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php $no = 1; foreach($items as $row): 
                                        $nilai = strtoupper(trim($row['nilai_huruf'] ?? ''));
                                        $isLulus = in_array($nilai, ['A', 'AB', 'B', 'BC', 'C'], true);
                                        $badgeTone = $isLulus ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200';
                                        $badgeLabel = $isLulus ? 'LULUS' : 'TIDAK LULUS / MENGULANG';
                                    ?>
                                        <tr class="hover:bg-slate-50/60 transition">
                                            <td class="p-3 text-center text-slate-400 font-mono"><?= $no++ ?></td>
                                            <td class="p-3 font-mono font-bold text-slate-800"><?= htmlspecialchars($row['kode_mk']) ?></td>
                                            <td class="p-3 font-semibold text-slate-800"><?= htmlspecialchars($row['nama_mk']) ?></td>
                                            <td class="p-3 text-center font-bold text-slate-700"><?= (int)$row['sks'] ?> SKS</td>
                                            <td class="p-3 text-center font-mono font-extrabold text-sm <?= $isLulus ? 'text-emerald-700' : 'text-rose-700' ?>">
                                                <?= htmlspecialchars($nilai ?: '-') ?>
                                            </td>
                                            <td class="p-3 text-center">
                                                <span class="px-2.5 py-1 rounded-full font-bold text-[11px] border <?= $badgeTone ?>">
                                                    <?= $isLulus ? '🟢 ' : '🔴 ' ?><?= $badgeLabel ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <!-- ======================================================================= -->
    <!-- MODAL CHECKLIST PILIH MK MANDIRI -->
    <!-- ======================================================================= -->
    <div id="modalPilihMK" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-slate-100 animate-in fade-in zoom-in duration-200">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Pilih Mata Kuliah untuk Dikontrak</h3>
                    <p class="text-xs text-slate-500">Pilih dari katalog mata kuliah resmi program studi Anda.</p>
                </div>
                <button onclick="closeModalPilihMK()" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
            </div>

            <!-- Search MK -->
            <div class="mt-4">
                <input type="text" id="searchKatalogMK" onkeyup="filterKatalogMK()" placeholder="Ketik Kode atau Nama Mata Kuliah..." class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <!-- List MK -->
            <div class="mt-3 max-h-80 overflow-y-auto rounded-xl border border-slate-200 divide-y divide-slate-100 text-xs">
                <?php 
                    $kontrakCodes = array_column($mk_kontrak, 'kode_mk');
                    foreach($master_mk as $mk): 
                        $isAlready = in_array($mk['kode_mk'], $kontrakCodes, true);
                ?>
                    <div class="p-3 flex items-center justify-between hover:bg-slate-50 katalog-row" data-search="<?= strtolower(htmlspecialchars($mk['kode_mk'] . ' ' . $mk['nama_mk'])) ?>">
                        <div>
                            <div class="font-bold text-slate-800">
                                <span class="font-mono text-brand-700"><?= htmlspecialchars($mk['kode_mk']) ?></span> - <?= htmlspecialchars($mk['nama_mk']) ?>
                            </div>
                            <div class="text-[11px] text-slate-500 mt-0.5">
                                <?= (int)$mk['sks'] ?> SKS • Semester <?= (int)$mk['semester'] ?> • <?= htmlspecialchars($mk['jenis'] ?: 'Wajib') ?>
                            </div>
                        </div>
                        <div>
                            <?php if($isAlready): ?>
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg font-bold text-[11px]">Sudah Dikontrak</span>
                            <?php else: ?>
                                <button onclick="tambahMKKeKontrak('<?= htmlspecialchars($mk['kode_mk']) ?>')" class="px-3 py-1 bg-orange-100 hover:bg-orange-200 text-brand-700 rounded-lg font-bold text-[11px] transition">
                                    + Ambil MK
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="flex items-center justify-end pt-4 border-t border-slate-100">
                <button type="button" onclick="closeModalPilihMK()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">Selesai</button>
            </div>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- MODAL PREVIEW BUKTI KRS (POPUP) -->
    <!-- ======================================================================= -->
    <div id="modalPreviewKRS" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-5" onclick="if(event.target === this) closePreviewKRSModal()">
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden animate-in fade-in zoom-in duration-200">
            <!-- Header -->
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-2.5 truncate">
                    <span class="text-xl">📄</span>
                    <div class="truncate">
                        <h3 class="text-sm font-bold text-slate-800 truncate" id="krsModalTitle">Preview Bukti Resmi KRS</h3>
                        <span class="text-[11px] font-mono text-slate-500 truncate block" id="krsModalFilename">file</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a id="krsModalDownloadBtn" href="#" download class="px-3 py-1.5 bg-orange-50 hover:bg-orange-100 text-brand-700 text-xs font-bold rounded-lg border border-orange-200 transition flex items-center gap-1.5">
                        <span>⬇️</span> Unduh
                    </a>
                    <button type="button" onclick="closePreviewKRSModal()" class="w-8 h-8 rounded-lg bg-slate-200/80 hover:bg-slate-300 text-slate-600 flex items-center justify-center text-sm font-bold transition cursor-pointer">
                        ✕
                    </button>
                </div>
            </div>

            <!-- Content Area -->
            <div class="flex-1 bg-slate-100 overflow-auto flex items-center justify-center min-h-[50vh] max-h-[75vh] p-2 relative">
                <!-- If Image -->
                <div id="krsPreviewImageContainer" class="hidden w-full h-full flex items-center justify-center p-2">
                    <img id="krsPreviewImage" src="" alt="Bukti KRS" class="max-w-full max-h-[70vh] object-contain rounded-lg shadow-md border border-slate-200">
                </div>
                <!-- If PDF / Iframe -->
                <iframe id="krsPreviewIframe" src="about:blank" class="hidden w-full h-[70vh] border-0 rounded-lg shadow-inner bg-white"></iframe>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: IMPORT NILAI EXCEL (FITUR ALIF)                                    -->
    <!-- ========================================================================= -->
    <div id="modalImportNilai" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-[100010] hidden flex items-center justify-center p-2.5 pb-6 sm:p-4 overflow-y-auto">
        <div class="clay-card w-full max-w-3xl xl:max-w-4xl max-h-[82vh] max-h-[82dvh] sm:max-h-[92vh] flex flex-col overflow-hidden bg-white shadow-2xl rounded-2xl">
            <!-- Modal Fixed Header -->
            <div class="px-5 py-3.5 sm:px-6 sm:py-4 border-b border-slate-100 flex items-center justify-between gap-3 shrink-0 bg-white">
                <div class="flex items-center space-x-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-file-excel text-base"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-bold text-sm text-slate-900 truncate" id="importModalTitle">Import Rekap Nilai Mahasiswa dari Excel</h3>
                        <p class="text-xs text-slate-500 truncate" id="importModalSubtitle">Format: .xlsx, .xls, .csv</p>
                    </div>
                </div>
                <button type="button" onclick="closeImportModal()" class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition shrink-0 cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-4 sm:p-6 overflow-y-auto flex-grow space-y-3.5">
                <!-- Dropzone Input File -->
                <input type="file" id="fileExcelInput" accept=".xlsx, .xls, .csv" class="hidden" onchange="handleExcelSelect(event)">
                <div id="excelDropzone" onclick="document.getElementById('fileExcelInput').click()" class="dropzone-box p-6 text-center select-none transition-all duration-200">
                    <span class="text-3xl text-emerald-600 block pointer-events-none"><i class="fa-solid fa-cloud-arrow-up"></i></span>
                    <h4 class="text-xs font-bold text-slate-800 mt-2 pointer-events-none" id="dropzoneTitleText">Pilih atau Drag & Drop File Excel (.xlsx / .csv)</h4>
                    <p class="text-[11px] text-slate-500 mt-1 pointer-events-none" id="importColumnHints">
                        Header: <code>Kode MK</code>, <code>Nilai Huruf</code> (A, AB, B, BC, C, D, E)
                    </p>
                    <div class="mt-2 text-[10px] text-emerald-700 font-semibold pointer-events-none flex items-center justify-center gap-1">
                        <i class="fa-solid fa-arrow-pointer text-[10px]"></i> Klik untuk memilih file atau tarik & letakkan (drag & drop) file langsung di sini
                    </div>
                </div>

                <!-- Compact File Loaded Bar (Shows when file is active) -->
                <div id="excelSelectedFileBar" class="hidden p-3 bg-emerald-50/70 border border-emerald-200 rounded-xl flex items-center justify-between gap-2 text-xs">
                    <div class="flex items-center space-x-2.5 min-w-0">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-file-excel"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="font-bold text-slate-900 block truncate" id="excelSelectedFileName">file.xlsx</span>
                            <span class="text-[11px] text-emerald-700 block" id="excelSelectedFileInfo">File berhasil dimuat & siap diproses</span>
                        </div>
                    </div>
                    <button type="button" onclick="resetImportFile()" class="px-2.5 py-1 text-slate-500 hover:text-rose-600 text-xs font-semibold hover:bg-rose-50 rounded-lg transition shrink-0 cursor-pointer">
                        <i class="fa-solid fa-arrow-rotate-left mr-1"></i> Ganti File
                    </button>
                </div>

                <!-- Template Download Card -->
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                    <div class="flex items-center space-x-2 min-w-0">
                        <i class="fa-solid fa-circle-info text-emerald-600 text-sm shrink-0"></i>
                        <span class="text-slate-600 truncate" id="importTemplateHintText">Belum punya format Excel yang sesuai?</span>
                    </div>
                    <button type="button" onclick="downloadTemplateExcelSingle()" class="inline-flex items-center justify-center px-3 py-1.5 font-bold text-emerald-700 bg-white hover:bg-emerald-100/80 border border-emerald-300 rounded-lg shadow-2xs transition shrink-0 cursor-pointer">
                        <i class="fa-solid fa-download mr-1.5 text-emerald-600"></i>
                        <span id="btnTemplateDownloadText">Download Template</span>
                    </button>
                </div>

                <!-- Preview Container with Robust Validation Info -->
                <div id="importPreviewContainer" class="hidden space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-bold text-slate-800">Pratinjau Validasi Nilai:</span>
                            <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-md bg-slate-100 text-slate-700 border border-slate-200" id="importBadgeTotal">Total: <span id="importRowCount">0</span></span>
                            <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-md bg-emerald-100 text-emerald-800 border border-emerald-300" id="importBadgeValid">Cocok: <span id="importValidCount">0</span></span>
                            <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-md bg-rose-100 text-rose-800 border border-rose-300" id="importBadgeInvalid">Tidak Cocok: <span id="importInvalidCount">0</span></span>
                        </div>
                    </div>

                    <!-- Alert banner for validation status -->
                    <div id="importWarningBanner" class="hidden p-3 rounded-xl text-xs space-y-1">
                        <div class="font-bold flex items-center gap-1.5" id="importWarningTitle">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span id="importWarningTitleText">Peringatan Validasi File</span>
                        </div>
                        <p class="text-[11px] leading-relaxed" id="importWarningText">
                            Beberapa kode mata kuliah atau format nilai tidak sesuai dengan rencana studi mahasiswa. Baris berwarna merah tidak akan disimpan ke sistem.
                        </p>
                    </div>

                    <!-- Table Preview with clean overflow -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden shadow-2xs bg-white">
                        <div class="max-h-60 overflow-y-auto overflow-x-auto">
                            <table class="w-full text-left text-xs min-w-[550px]">
                                <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-600 sticky top-0 border-b border-slate-200 z-10">
                                    <tr>
                                        <th class="py-2.5 px-3 w-28 whitespace-nowrap">NIM</th>
                                        <th class="py-2.5 px-3 w-28 whitespace-nowrap">Kode MK di File</th>
                                        <th class="py-2.5 px-3 min-w-[140px]">Mata Kuliah di Sistem</th>
                                        <th class="py-2.5 px-3 w-16 text-center whitespace-nowrap">Nilai</th>
                                        <th class="py-2.5 px-3 w-40 text-center whitespace-nowrap">Hasil Validasi</th>
                                    </tr>
                                </thead>
                                <tbody id="importPreviewTableBody" class="divide-y divide-slate-100"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Fixed Footer -->
            <div class="px-5 py-3 sm:px-6 sm:py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end space-x-2 shrink-0">
                <button type="button" onclick="closeImportModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-200/60 rounded-xl transition cursor-pointer">
                    Batal
                </button>
                <button type="button" id="btnProcessImport" onclick="processImportNilai()" disabled class="px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-xl shadow-md transition flex items-center cursor-pointer">
                    <i class="fa-solid fa-check mr-1.5"></i> Simpan Nilai ke Sistem
                </button>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        const idPerwalian = <?= (int)$perwalian['id_perwalian'] ?>;

        function openModalPilihMK() {
            document.getElementById('modalPilihMK').classList.remove('hidden');
        }
        function closeModalPilihMK() {
            document.getElementById('modalPilihMK').classList.add('hidden');
        }

        function filterKatalogMK() {
            const q = (document.getElementById('searchKatalogMK').value || '').toLowerCase();
            document.querySelectorAll('.katalog-row').forEach(row => {
                const s = row.getAttribute('data-search') || '';
                row.style.display = (q === '' || s.includes(q)) ? '' : 'none';
            });
        }

        async function tambahMKKeKontrak(kodeMK) {
            const fd = new FormData();
            fd.append('id_perwalian', idPerwalian);
            fd.append('kode_mk', kodeMK);

            try {
                const res = await fetch('<?= site_url("mahasiswa/perwalian_tambah_mk") ?>', { method: 'POST', body: fd });
                const json = await res.json();
                if (json.status === 'success') {
                    location.reload();
                } else {
                    Swal.fire('Gagal', json.message, 'error');
                }
            } catch (err) {
                Swal.fire('Error', 'Gagal menambahkan mata kuliah', 'error');
            }
        }

        function hapusMKFromKontrak(kodeMK) {
            Swal.fire({
                title: 'Hapus Mata Kuliah?',
                text: `Hapus ${kodeMK} dari daftar kontrak semester ini?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea580c',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const fd = new FormData();
                    fd.append('id_perwalian', idPerwalian);
                    fd.append('kode_mk', kodeMK);

                    const res = await fetch('<?= site_url("mahasiswa/perwalian_hapus_mk") ?>', { method: 'POST', body: fd });
                    const json = await res.json();
                    if (json.status === 'success') {
                        location.reload();
                    } else {
                        Swal.fire('Gagal', json.message, 'error');
                    }
                }
            });
        }

        function uploadBuktiKRS(e) {
            const file = e.target.files ? e.target.files[0] : null;
            if (file) {
                processKRSFile(file);
            }
        }

        async function processKRSFile(file) {
            if (!file) return;

            // Validasi format berkas
            const allowedExts = ['pdf', 'jpg', 'jpeg', 'png'];
            const fileExt = file.name.split('.').pop().toLowerCase();
            if (!allowedExts.includes(fileExt)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Format Tidak Sesuai',
                    text: 'Hanya berkas berformat PDF, JPG, JPEG, atau PNG yang diperbolehkan.'
                });
                return;
            }

            // Validasi ukuran berkas (maks. 5MB)
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Berkas Terlalu Besar',
                    text: 'Ukuran berkas melebihi batas maksimal 5MB.'
                });
                return;
            }

            const fd = new FormData();
            fd.append('id_perwalian', idPerwalian);
            fd.append('file_krs', file);

            Swal.fire({
                title: 'Mengunggah Bukti KRS...',
                text: 'Harap tunggu berkas sedang diproses.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const res = await fetch('<?= site_url("mahasiswa/perwalian_upload_krs") ?>', { method: 'POST', body: fd });
                const json = await res.json();
                if (json.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: json.message, timer: 1500, showConfirmButton: false });
                    document.getElementById('krsFileName').innerText = json.file_name;
                    const statusEl = document.getElementById('krsFileStatus');
                    if (statusEl) statusEl.innerText = 'Berkas tersimpan di sistem';
                    const link = document.getElementById('krsFileLink');
                    if (link) {
                        link.setAttribute('onclick', `previewKRSModal('${json.file_url}', '${json.file_name}')`);
                        link.classList.remove('hidden');
                    }
                } else {
                    Swal.fire('Gagal Unggah', json.message, 'error');
                }
            } catch (err) {
                Swal.fire('Error', 'Terjadi kesalahan sistem saat unggah.', 'error');
            } finally {
                const inputEl = document.getElementById('fileKRSInput');
                if (inputEl) inputEl.value = '';
            }
        }

        // --- DRAG AND DROP SETUP ---
        function setupDropzone(dropzoneEl, fileHandler) {
            if (!dropzoneEl) return;
            
            ['dragenter', 'dragover'].forEach(eventName => {
                dropzoneEl.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzoneEl.classList.add('border-brand-500', 'bg-orange-50', 'ring-4', 'ring-orange-200');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzoneEl.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzoneEl.classList.remove('border-brand-500', 'bg-orange-50', 'ring-4', 'ring-orange-200');
                }, false);
            });

            dropzoneEl.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                if (dt && dt.files && dt.files.length > 0) {
                    fileHandler(dt.files[0]);
                }
            }, false);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const krsDz = document.getElementById('krsDropzone');
            if (krsDz && typeof setupDropzone === 'function') {
                setupDropzone(krsDz, processKRSFile);
            }
            initExcelDropzone();
            refreshModalGradingProgress();
        });

        async function submitPerwalian() {
            const catatan = document.getElementById('catatanMhs').value;

            Swal.fire({
                title: 'Ajukan Kontrak Perwalian?',
                text: 'Kontrak mata kuliah dan bukti KRS akan dikirimkan ke Dosen Wali untuk disetujui.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ea580c',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Ajukan Sekarang'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const fd = new FormData();
                    fd.append('id_perwalian', idPerwalian);
                    fd.append('catatan_mahasiswa', catatan);

                    try {
                        const res = await fetch('<?= site_url("mahasiswa/perwalian_submit") ?>', { method: 'POST', body: fd });
                        const json = await res.json();
                        if (json.status === 'success') {
                            Swal.fire('Sukses!', json.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Belum Lengkap', json.message, 'warning');
                        }
                    } catch (err) {
                        Swal.fire('Error', 'Gagal mengajukan perwalian.', 'error');
                    }
                }
            });
        }
        // --- MODAL PREVIEW KRS POPUP ---
        function previewKRSModal(url, filename) {
            if (!url) return;
            const modal = document.getElementById('modalPreviewKRS');
            const filenameEl = document.getElementById('krsModalFilename');
            const downloadBtn = document.getElementById('krsModalDownloadBtn');
            const imgContainer = document.getElementById('krsPreviewImageContainer');
            const imgEl = document.getElementById('krsPreviewImage');
            const iframeEl = document.getElementById('krsPreviewIframe');

            filenameEl.innerText = filename || 'Bukti_KRS';
            downloadBtn.href = url;

            const cleanName = (filename || url).split('?')[0];
            const ext = cleanName.split('.').pop().toLowerCase();

            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                imgEl.src = url;
                imgContainer.classList.remove('hidden');
                iframeEl.classList.add('hidden');
                iframeEl.src = 'about:blank';
            } else {
                iframeEl.src = url;
                iframeEl.classList.remove('hidden');
                imgContainer.classList.add('hidden');
                imgEl.src = '';
            }

            modal.classList.remove('hidden');
        }

        function closePreviewKRSModal() {
            const modal = document.getElementById('modalPreviewKRS');
            const imgEl = document.getElementById('krsPreviewImage');
            const iframeEl = document.getElementById('krsPreviewIframe');
            if (modal) modal.classList.add('hidden');
            if (imgEl) imgEl.src = '';
            if (iframeEl) iframeEl.src = 'about:blank';
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closePreviewKRSModal();
        });

        // --- DATA & HELPER FITUR TAHAP 2 (ALIF) ---
        const currentPerwalian2 = {
            perwalian: <?= json_encode($perwalian) ?>,
            details: <?= json_encode($mk_kontrak) ?>,
            mata_kuliah: <?= json_encode($mk_kontrak) ?>
        };
        let parsedImportRows = [];

        function escapeHtml(text) {
            if (!text) return "";
            return String(text)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function openImportModalSingle() {
            const mhsNama = <?= json_encode($nama_mhs ?: 'Mahasiswa') ?>;
            const mhsNim = <?= json_encode($nim) ?>;
            document.getElementById('importModalTitle').innerText = `Import Nilai Khusus: ${mhsNama}`;
            document.getElementById('importModalSubtitle').innerText = `NIM: ${mhsNim} (Hanya untuk mata kuliah yang dikontrak)`;
            document.getElementById('importColumnHints').innerHTML = 'Header kolom: <code>Kode MK</code>, <code>Nilai Huruf</code> (A, AB, B, BC, C, D, E)';
            document.getElementById('importTemplateHintText').innerText = 'Format Excel khusus mahasiswa ini:';
            document.getElementById('btnTemplateDownloadText').innerText = 'Unduh Template Mahasiswa Ini';
            resetImportFile();
            document.getElementById('modalImportNilai').classList.remove('hidden');
        }

        function closeImportModal() {
            document.getElementById('modalImportNilai').classList.add('hidden');
            resetImportFile();
        }

        function downloadTemplateExcelSingle() {
            if (!currentPerwalian2 || !currentPerwalian2.details || currentPerwalian2.details.length === 0) {
                Swal.fire('Perhatian', 'Tidak ada daftar mata kuliah yang dikontrak untuk dibuatkan template.', 'info');
                return;
            }
            const sampleData = currentPerwalian2.details.map(d => ({
                'Kode MK': d.kode_mk,
                'Mata Kuliah': d.nama_mk,
                'Nilai Huruf': d.nilai_huruf || ''
            }));
            const ws = XLSX.utils.json_to_sheet(sampleData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Nilai Mahasiswa');
            const safeNim = <?= json_encode($nim ?: 'mahasiswa') ?>;
            XLSX.writeFile(wb, `template_nilai_${safeNim}.xlsx`);
        }

        function resetImportFile() {
            const input = document.getElementById('fileExcelInput');
            if (input) input.value = '';

            const preview = document.getElementById('importPreviewContainer');
            if (preview) preview.classList.add('hidden');

            const dropzone = document.getElementById('excelDropzone');
            if (dropzone) dropzone.classList.remove('hidden');

            const fileBar = document.getElementById('excelSelectedFileBar');
            if (fileBar) fileBar.classList.add('hidden');

            const btn = document.getElementById('btnProcessImport');
            if (btn) {
                btn.setAttribute('disabled', 'true');
                btn.innerHTML = `<i class="fa-solid fa-check mr-1.5"></i> Simpan Nilai ke Sistem`;
            }

            parsedImportRows = [];
        }

        function handleExcelSelect(e) {
            const file = (e.target.files && e.target.files.length > 0) ? e.target.files[0] : null;
            if (file) {
                processExcelFile(file);
            }
        }

        function processExcelFile(file) {
            if (!file) return;

            const fileName = file.name.toLowerCase();
            const validExts = ['.xlsx', '.xls', '.csv'];
            const isValid = validExts.some(ext => fileName.endsWith(ext));
            if (!isValid) {
                Swal.fire('Format File Tidak Sesuai', 'Silakan unggah file dengan format .xlsx, .xls, atau .csv.', 'warning');
                resetImportFile();
                return;
            }

            const selNameEl = document.getElementById('excelSelectedFileName');
            if (selNameEl) selNameEl.innerText = file.name;

            const reader = new FileReader();
            reader.onload = function(evt) {
                try {
                    const data = new Uint8Array(evt.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });
                    const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                    const json = XLSX.utils.sheet_to_json(firstSheet);

                    parsedImportRows = json.map(r => ({
                        nim: String(r['NIM'] || r['nim'] || r['Nim'] || '').trim(),
                        kode_mk: String(r['Kode MK'] || r['kode_mk'] || r['Kode'] || r['KODE MK'] || '').trim().toUpperCase(),
                        nilai_huruf: String(r['Nilai Huruf'] || r['nilai_huruf'] || r['Nilai'] || r['NILAI'] || '').trim().toUpperCase(),
                    })).filter(r => r.kode_mk !== '' && r.nilai_huruf !== '');

                    if (parsedImportRows.length === 0) {
                        Swal.fire('File Kosong', 'Tidak ditemukan baris nilai yang valid pada file.', 'warning');
                        resetImportFile();
                        return;
                    }

                    renderImportPreview(parsedImportRows);
                } catch (err) {
                    Swal.fire('Gagal Baca File', 'Pastikan format file adalah Excel atau CSV valid.', 'error');
                    resetImportFile();
                }
            };
            reader.readAsArrayBuffer(file);
        }

        function initExcelDropzone() {
            const dropzone = document.getElementById('excelDropzone');
            if (!dropzone) return;

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, () => {
                    dropzone.classList.add('drag-active');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, () => {
                    dropzone.classList.remove('drag-active');
                }, false);
            });

            dropzone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt ? dt.files : null;
                if (files && files.length > 0) {
                    processExcelFile(files[0]);
                }
            }, false);
        }

        function renderImportPreview(rows) {
            const rowCountEl = document.getElementById('importRowCount');
            if (rowCountEl) rowCountEl.innerText = rows.length;

            const dropzone = document.getElementById('excelDropzone');
            if (dropzone) dropzone.classList.add('hidden');

            const fileBar = document.getElementById('excelSelectedFileBar');
            if (fileBar) fileBar.classList.remove('hidden');
            const tbody = document.getElementById('importPreviewTableBody');
            tbody.innerHTML = '';

            const VALID_GRADES = ['A', 'AB', 'B', 'BC', 'C', 'D', 'E'];
            let validCount = 0;
            let invalidCount = 0;

            // Mapping kode MK aktif mahasiswa untuk validasi anti-duplikasi file Excel
            const mkMap = {};
            const list = currentPerwalian2.mata_kuliah || currentPerwalian2.details || [];
            list.forEach(m => {
                const code = String(m.kode_mk || '').trim().toUpperCase();
                if (code) {
                    mkMap[code] = m.nama_mk || 'Mata Kuliah Terdaftar';
                }
            });
            const targetNim = String((currentPerwalian2.perwalian ? currentPerwalian2.perwalian.nim : '') || '').trim();

            rows.slice(0, 100).forEach(r => {
                const mkCode = String(r.kode_mk || '').trim().toUpperCase();
                const grade = String(r.nilai_huruf || '').trim().toUpperCase();
                const rowNim = String(r.nim || '').trim();

                let isRowValid = true;
                let errorReason = '';
                let namaMkDisplay = '-';

                // Validasi Nilai Huruf
                if (!VALID_GRADES.includes(grade)) {
                    isRowValid = false;
                    errorReason = 'Nilai tidak sah (harus A, AB, B, BC, C, D, E)';
                }

                // Validasi Kode MK & NIM
                // Validasi kesesuaian NIM agar tidak salah upload file milik orang lain
                if (targetNim && rowNim && rowNim !== targetNim) {
                    isRowValid = false;
                    errorReason = `NIM tidak sesuai (Harus: ${targetNim})`;
                } else if (!mkMap[mkCode]) {
                    isRowValid = false;
                    errorReason = 'Kode MK TIDAK COCOK dengan rencana studi mahasiswa';
                    namaMkDisplay = '<span class="text-rose-600 font-bold text-[11px] flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation"></i> Tidak terdaftar di KRS</span>';
                } else {
                    namaMkDisplay = `<span class="text-slate-900 font-medium">${escapeHtml(mkMap[mkCode])}</span>`;
                }

                r._isValid = isRowValid;

                let rowClass = 'hover:bg-slate-50/60';
                let statusBadge = '';

                if (isRowValid) {
                    validCount++;
                    const isLulus = ['A', 'AB', 'B', 'BC', 'C'].includes(grade);
                    statusBadge = isLulus 
                        ? `<span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 whitespace-nowrap"><i class="fa-solid fa-check mr-1 text-[9px]"></i> Valid (Lulus)</span>`
                        : `<span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-rose-100 text-rose-800 border border-rose-300 whitespace-nowrap"><i class="fa-solid fa-xmark mr-1 text-[9px]"></i> Valid (Tidak Lulus)</span>`;
                } else {
                    invalidCount++;
                    rowClass = 'bg-rose-50/80 hover:bg-rose-100/60 border-l-4 border-l-rose-500';
                    let shortMsg = 'Tidak Cocok';
                    if (errorReason.includes('NIM')) shortMsg = 'NIM Tidak Cocok';
                    else if (errorReason.includes('Kode MK')) shortMsg = 'Kode MK Tidak Cocok';
                    else if (errorReason.includes('Nilai')) shortMsg = 'Format Nilai Salah';

                    statusBadge = `<span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-rose-200 text-rose-900 border border-rose-300 gap-1 whitespace-nowrap" title="${escapeHtml(errorReason)}"><i class="fa-solid fa-circle-xmark text-rose-600"></i> ${escapeHtml(shortMsg)}</span>`;
                }

                tbody.innerHTML += `
                    <tr class="${rowClass}">
                        <td class="py-2 px-3 font-mono font-bold ${rowNim && targetNim && rowNim !== targetNim ? 'text-rose-700 bg-rose-100/50' : 'text-slate-600'}">${escapeHtml(rowNim || '')}</td>
                        <td class="py-2 px-3 font-mono font-black ${!isRowValid && errorReason.includes('Kode MK') ? 'text-rose-700 bg-rose-100/70 font-mono underline decoration-rose-400' : 'text-slate-900'}">${escapeHtml(mkCode || '-')}</td>
                        <td class="py-2 px-3 text-xs">${namaMkDisplay}</td>
                        <td class="py-2 px-3 text-center font-black font-mono ${!VALID_GRADES.includes(grade) ? 'text-rose-600 bg-rose-100' : 'text-slate-900'}">${escapeHtml(grade || '-')}</td>
                        <td class="py-2 px-3 text-center whitespace-nowrap">${statusBadge}</td>
                    </tr>
                `;
            });

            document.getElementById('importValidCount').innerText = validCount;
            document.getElementById('importInvalidCount').innerText = invalidCount;

            const banner = document.getElementById('importWarningBanner');
            const bannerTitle = document.getElementById('importWarningTitle');
            const bannerTitleText = document.getElementById('importWarningTitleText');
            const bannerText = document.getElementById('importWarningText');
            const btnProcess = document.getElementById('btnProcessImport');

            if (invalidCount > 0 && validCount === 0) {
                banner.className = 'p-3 rounded-xl text-xs space-y-1 bg-rose-100 border border-rose-300 text-rose-900';
                bannerTitle.className = 'font-bold flex items-center gap-1.5 text-rose-800';
                bannerTitleText.innerText = 'Semua Data Tidak Cocok!';
                bannerText.innerHTML = 'Seluruh kode mata kuliah pada file Excel Anda <strong>tidak terdaftar pada rencana studi mahasiswa ini</strong>. Silakan periksa kembali atau gunakan tombol <strong>"Unduh Template Mahasiswa Ini"</strong> di atas.';
                banner.classList.remove('hidden');

                btnProcess.setAttribute('disabled', 'true');
                btnProcess.innerHTML = `<i class="fa-solid fa-ban mr-1.5"></i> Tidak Ada Data Cocok`;
            } else if (invalidCount > 0 && validCount > 0) {
                banner.className = 'p-3 rounded-xl text-xs space-y-1 bg-amber-50 border border-amber-300 text-amber-900';
                bannerTitle.className = 'font-bold flex items-center gap-1.5 text-amber-800';
                bannerTitleText.innerText = 'Sebagian Kode MK Tidak Cocok';
                bannerText.innerHTML = `Ditemukan <strong>${invalidCount} baris</strong> dengan Kode MK / Nilai tidak cocok. Hanya <strong>${validCount} mata kuliah yang valid</strong> yang akan disimpan ke sistem. Baris yang tidak cocok akan otomatis dilewati.`;
                banner.classList.remove('hidden');

                btnProcess.removeAttribute('disabled');
                btnProcess.innerHTML = `<i class="fa-solid fa-check mr-1.5"></i> Simpan ${validCount} Nilai Valid`;
            } else {
                banner.classList.add('hidden');
                btnProcess.removeAttribute('disabled');
                btnProcess.innerHTML = `<i class="fa-solid fa-check mr-1.5"></i> Simpan Nilai ke Sistem (${validCount} MK)`;
            }

            document.getElementById('importPreviewContainer').classList.remove('hidden');
        }

        async function processImportNilai() {
            if (parsedImportRows.length === 0) return;

            const validRowsToSubmit = parsedImportRows.filter(r => r._isValid !== false);
            const invalidRowsCount = parsedImportRows.length - validRowsToSubmit.length;

            if (validRowsToSubmit.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Tidak Ada Data Valid',
                    text: 'Tidak ada kode mata kuliah yang cocok dengan rencana studi Anda. Silakan unduh format template yang sesuai.'
                });
                return;
            }

            if (invalidRowsCount > 0) {
                const conf = await Swal.fire({
                    icon: 'warning',
                    title: 'Konfirmasi Penyimpanan Nilai',
                    html: `Terdapat <b>${invalidRowsCount} baris mata kuliah tidak cocok</b> yang akan dilewati.<br><br>Hanya <b>${validRowsToSubmit.length} mata kuliah valid</b> yang akan disimpan ke sistem. Lanjutkan?`,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan yang Valid',
                    confirmButtonColor: '#059669',
                    cancelButtonText: 'Batal & Perbaiki File'
                });
                if (!conf.isConfirmed) return;
            }

            Swal.fire({ title: 'Menyimpan Nilai...', didOpen: () => Swal.showLoading(), allowOutsideClick: false });

            try {
                const res = await fetch(`<?= site_url('mahasiswa/perwalian_import_nilai'); ?>`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        rows: validRowsToSubmit,
                        id_perwalian: idPerwalian
                    })
                });
                const json = await res.json();
                Swal.close();

                if (json.status) {
                    closeImportModal();
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses Import Nilai!',
                        text: json.message || 'Nilai berhasil disimpan ke sistem.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal Import', json.message || 'Gagal memproses import nilai.', 'error');
                }
            } catch (err) {
                Swal.close();
                Swal.fire('Error', 'Terjadi kesalahan server saat memproses import.', 'error');
            }
        }

        async function onGradeChange(idDetail, grade) {
            grade = grade.trim().toUpperCase();
            let autoStatus = 'Belum Ditentukan';
            if (['A', 'AB', 'B', 'BC', 'C'].includes(grade)) {
                autoStatus = 'Lulus';
            } else if (['D', 'E'].includes(grade)) {
                autoStatus = 'Tidak Lulus';
            }

            updateStatusBadgeUI(idDetail, autoStatus);
            refreshModalGradingProgress();

            const fd = new FormData();
            fd.append('id_detail', idDetail);
            fd.append('nilai_huruf', grade);
            fd.append('status_kelulusan', autoStatus);

            try {
                const res = await fetch(`<?= site_url('mahasiswa/perwalian_update_nilai'); ?>`, {
                    method: 'POST',
                    body: fd
                });
                const json = await res.json();
                if (json.status) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'success',
                        title: 'Nilai & status otomatis disimpan'
                    });
                } else {
                    Swal.fire('Gagal', json.message || 'Gagal memperbarui nilai', 'error');
                }
            } catch (err) {
                console.error(err);
            }
        }

        function updateStatusBadgeUI(idDetail, status) {
            const col = document.getElementById(`m2StatusCol_${idDetail}`);
            if (col) {
                if (status === 'Lulus') {
                    col.innerHTML = `<span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300"><i class="fa-solid fa-circle-check mr-1.5 text-emerald-600"></i> Lulus</span>`;
                } else if (status === 'Tidak Lulus') {
                    col.innerHTML = `<span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-full bg-rose-100 text-rose-800 border border-rose-300"><i class="fa-solid fa-circle-xmark mr-1.5 text-rose-600"></i> Tidak Lulus</span>`;
                } else {
                    col.innerHTML = `<span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-500 border border-slate-200"><i class="fa-solid fa-minus mr-1 text-slate-400"></i> Belum Dinilai</span>`;
                }
            }
        }

        function refreshModalGradingProgress() {
            const selects = document.querySelectorAll('#m2GradingTableBody select');
            let graded = 0;
            selects.forEach(s => {
                if (s.value && s.value.trim() !== '') graded++;
            });
            const textEl = document.getElementById('m2GradingProgressText');
            if (textEl) {
                textEl.innerText = `${graded} / ${selects.length} Dinilai`;
            }
        }

        async function submitTahap2FinalModal() {
            const conf = await Swal.fire({
                title: 'Kunci & Selesaikan Evaluasi Tahap 2?',
                text: 'Setelah dikunci, hasil evaluasi perwalian ini akan tersimpan permanen ke riwayat akademik.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                confirmButtonText: 'Ya, Kunci Sekarang!'
            });

            if (!conf.isConfirmed) return;

            Swal.fire({ title: 'Menyimpan & Mengunci...', didOpen: () => Swal.showLoading(), allowOutsideClick: false });

            try {
                const fd = new FormData();
                fd.append('id_perwalian', idPerwalian);

                const res = await fetch(`<?= site_url('mahasiswa/perwalian_submit_tahap2'); ?>`, {
                    method: 'POST',
                    body: fd
                });
                const json = await res.json();
                Swal.close();

                if (json.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Evaluasi Tahap 2 Selesai!',
                        text: json.message || 'Evaluasi perwalian berhasil dikunci.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Gagal', json.message || 'Gagal mengunci perwalian.', 'error');
                }
            } catch (err) {
                console.error(err);
                Swal.close();
                Swal.fire('Error', 'Terjadi kesalahan saat mengunci perwalian.', 'error');
            }
        }

    </script>
</body>
</html>
