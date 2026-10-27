<?php
/**
 * Template Cetak PDF Berita Acara Sidang - Admin LAA
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dokumen BAP Sidang'; ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 20mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111827;
            background: #ffffff;
            margin: 0;
            padding: 20px 40px;
            font-size: 11pt;
            line-height: 1.35;
        }
        .page-container {
            max-width: 780px;
            margin: 0 auto;
            background: #ffffff;
        }
        .page-break {
            page-break-before: always;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px dashed #cbd5e1;
        }
        @media print {
            body { padding: 0; }
            .page-break { border-top: none; margin-top: 0; padding-top: 0; }
            .no-print { display: none !important; }
        }
        .header-logo {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 15px;
            margin-bottom: 12px;
        }
        .logo-box {
            font-family: 'Arial', sans-serif;
            font-weight: 900;
            font-size: 20pt;
            color: #e11d48;
            letter-spacing: -1px;
            line-height: 1;
        }
        .logo-sub {
            font-family: 'Arial', sans-serif;
            font-size: 13pt;
            color: #334155;
            font-weight: 700;
        }
        .title-header {
            text-align: center;
            margin-bottom: 18px;
        }
        .title-header h2 {
            font-size: 12pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .title-header h3 {
            font-size: 12pt;
            font-weight: bold;
            margin: 2px 0 0 0;
            text-transform: uppercase;
        }
        .title-header h4 {
            font-size: 11pt;
            font-weight: bold;
            margin: 2px 0 0 0;
            text-transform: uppercase;
        }
        p {
            margin: 6px 0;
            text-align: justify;
        }
        .data-table {
            width: 100%;
            margin: 8px 0;
            border-collapse: collapse;
        }
        .data-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .data-table td.label {
            width: 140px;
        }
        .data-table td.colon {
            width: 15px;
        }
        .notes-lines {
            margin: 10px 0 16px 0;
        }
        .notes-lines .line {
            border-bottom: 1px solid #94a3b8;
            height: 22px;
            width: 100%;
        }
        .signatures-table {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
        }
        .signatures-table td {
            vertical-align: middle;
            padding: 4px 6px;
            height: 44px;
        }
        .sig-box {
            font-family: 'Brush Script MT', cursive, sans-serif;
            font-size: 16pt;
            color: #0284c7;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }
        /* Page 2 Styles */
        .eval-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 15px 0;
        }
        .eval-table th, .eval-table td {
            border: 1px solid #111827;
            padding: 4px 8px;
            font-size: 10.5pt;
        }
        .eval-table th {
            background: #f8fafc;
            text-align: center;
            font-weight: bold;
        }
        .checkbox-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin: 5px 0;
        }
        .checkbox-box {
            width: 14px;
            height: 14px;
            border: 1.5px solid #111827;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 10pt;
            margin-top: 2px;
            flex-shrink: 0;
        }
        .range-table {
            width: 60%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .range-table th, .range-table td {
            border: 1px solid #111827;
            padding: 2.5px 6px;
            font-size: 9.5pt;
            text-align: center;
        }
        .range-table th {
            background: #f8fafc;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="page-container">

        <!-- ============================================================= -->
        <!-- HALAMAN 1: BERITA ACARA PENYELENGGARAAN SIDANG (Foto 4 & 5)   -->
        <!-- ============================================================= -->
        <div class="page-1">
            <!-- Header Logo & Identity -->
            <div class="header-logo">
                <img src="<?= base_url('assets/images/logo_telkom_university.png'); ?>" alt="Telkom University Logo" style="height: 52px; width: auto; display: block;">
            </div>

            <div class="title-header">
                <h2>BERITA ACARA</h2>
                <h3>PENYELENGGARAAN SIDANG TUGAS AKHIR</h3>
                <h4>FAKULTAS INDUSTRI KREATIF</h4>
            </div>

            <p>
                Pada hari <strong><?= htmlspecialchars($bap['hari']); ?></strong>, <strong><?= htmlspecialchars($bap['tanggal_text']); ?></strong> bertempat di <?= htmlspecialchars($bap['tempat']); ?> telah diselenggarakan Sidang Tugas Akhir Semester <?= htmlspecialchars($bap['semester']); ?> Tahun Akademik <?= htmlspecialchars($bap['tahun_akademik']); ?>:
            </p>

            <table class="data-table">
                <tr>
                    <td class="label" style="width:20px;">1.</td>
                    <td class="label">Nama</td>
                    <td class="colon">:</td>
                    <td><strong><?= htmlspecialchars($bap['nama']); ?></strong></td>
                </tr>
                <tr>
                    <td></td>
                    <td class="label">NIM</td>
                    <td class="colon">:</td>
                    <td><strong><?= htmlspecialchars($bap['nim']); ?></strong></td>
                </tr>
                <tr>
                    <td></td>
                    <td class="label">Program Studi</td>
                    <td class="colon">:</td>
                    <td><?= htmlspecialchars($bap['prodi']); ?></td>
                </tr>
                <tr>
                    <td class="label" style="width:20px; padding-top:6px;">2.</td>
                    <td class="label" colspan="3" style="padding-top:6px;"><strong>Topik Tugas Akhir *)</strong></td>
                </tr>
                <tr>
                    <td></td>
                    <td class="label">Judul</td>
                    <td class="colon">:</td>
                    <td style="font-style:italic;"><?= htmlspecialchars($bap['judul']); ?></td>
                </tr>
            </table>

            <p style="margin-top:12px;"><strong>3. Catatan Penting Selama Kegiatan Berlangsung :</strong></p>
            
            <div class="notes-lines">
                <div class="line" style="font-size:10pt; color:#334155; padding-left:4px;"><?= htmlspecialchars($bap['catatan_penguji']); ?></div>
                <div class="line"></div>
                <div class="line"></div>
                <div class="line"></div>
                <div class="line"></div>
            </div>

            <div style="text-align:right; margin-top:12px; margin-right:20px;">
                Bandung, <?= htmlspecialchars($bap['tanggal_text']); ?>
            </div>

            <!-- Tim Penguji Signatures Table (Matching Photo 5) -->
            <table class="signatures-table">
                <tr>
                    <td style="width:160px; height:auto;"><strong>Tim Penguji</strong></td>
                    <td style="width:260px; height:auto;"><strong>Nama</strong></td>
                    <td style="width:200px; text-align:center; height:auto;"><strong>Tanda Tangan</strong></td>
                </tr>
                <tr>
                    <td>Pembimbing 1</td>
                    <td>: <?= htmlspecialchars($bap['pembimbing_1']); ?></td>
                    <td align="center">
                        <?php if (!empty($bap['ttd_pembimbing_1']) && file_exists(FCPATH . 'uploads/signatures/' . $bap['ttd_pembimbing_1'])): ?>
                            <img src="<?= base_url('uploads/signatures/' . $bap['ttd_pembimbing_1']); ?>" style="max-height: 42px; width: auto; display: block; margin: 0 auto;">
                        <?php else: ?>
                            <div class="sig-box">~ TTD Digital ~</div>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>Pembimbing 2</td>
                    <td>: <?= htmlspecialchars($bap['pembimbing_2']); ?></td>
                    <td align="center">
                        <?php if (!empty($bap['ttd_pembimbing_2']) && file_exists(FCPATH . 'uploads/signatures/' . $bap['ttd_pembimbing_2'])): ?>
                            <img src="<?= base_url('uploads/signatures/' . $bap['ttd_pembimbing_2']); ?>" style="max-height: 42px; width: auto; display: block; margin: 0 auto;">
                        <?php else: ?>
                            <div class="sig-box">~ TTD Digital ~</div>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>Penguji 1/Ketua Sidang</td>
                    <td>: <?= htmlspecialchars($bap['penguji_1']); ?></td>
                    <td align="center">
                        <?php if (!empty($bap['ttd_penguji_1']) && file_exists(FCPATH . 'uploads/signatures/' . $bap['ttd_penguji_1'])): ?>
                            <img src="<?= base_url('uploads/signatures/' . $bap['ttd_penguji_1']); ?>" style="max-height: 42px; width: auto; display: block; margin: 0 auto;">
                        <?php else: ?>
                            <div class="sig-box">~ TTD Digital ~</div>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>Penguji 2</td>
                    <td>: <?= htmlspecialchars($bap['penguji_2']); ?></td>
                    <td align="center">
                        <?php if (!empty($bap['ttd_penguji_2']) && file_exists(FCPATH . 'uploads/signatures/' . $bap['ttd_penguji_2'])): ?>
                            <img src="<?= base_url('uploads/signatures/' . $bap['ttd_penguji_2']); ?>" style="max-height: 42px; width: auto; display: block; margin: 0 auto;">
                        <?php else: ?>
                            <div class="sig-box">~ TTD Digital ~</div>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>

            <p style="font-size:9pt; font-style:italic; margin-top:15px;">
                *) Wajib diisi oleh ketua sidang
            </p>
        </div>

        <!-- ============================================================= -->
        <!-- HALAMAN 2: NILAI SIDANG KOMPREHENSIF (Foto 6)                 -->
        <!-- ============================================================= -->
        <div class="page-break"></div>

        <div class="page-2">
            <p><strong>4. Nilai Sidang Tugas Akhir Komprehensif</strong></p>
            <p>
                Berdasarkan hasil evaluasi dari para Anggota Sidang Tugas Akhir, meliputi aspek-aspek yang dinilai dengan skor rata-rata sebagai berikut:
            </p>

            <!-- Tabel Nilai & Bobot (Matching Photo 6) -->
            <table class="eval-table">
                <thead>
                    <tr>
                        <th style="text-align:left; width:180px;">Tim Penilai</th>
                        <th style="width:90px;">Nilai</th>
                        <th style="width:90px;">Bobot</th>
                        <th style="width:110px;">Nilai*Bobot</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bap['evaluasi_nilai'] as $ev): ?>
                        <tr>
                            <td><?= htmlspecialchars($ev['peran']); ?></td>
                            <td align="center"><?= $ev['nilai']; ?></td>
                            <td align="center"><?= $ev['bobot']; ?></td>
                            <td align="center"><?= number_format($ev['nilai_bobot'], 1); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr style="font-weight:bold; background:#f8fafc;">
                        <td colspan="3" align="right">Jumlah Rata-Rata :</td>
                        <td align="center"><?= number_format($bap['nilai_akhir'], 1); ?></td>
                    </tr>
                    <tr style="font-weight:bold; background:#f8fafc;">
                        <td colspan="3" align="right">Dengan Huruf :</td>
                        <td align="center" style="font-size:12pt; color:#e11d48;"><?= htmlspecialchars($bap['indeks_huruf']); ?></td>
                    </tr>
                </tbody>
            </table>

            <p style="margin-top:14px;">
                Atas nama Tim Penguji menetapkan hasil akhir penilaian pada Sidang Tugas Akhir adalah:
            </p>

            <div class="checkbox-item">
                <div class="checkbox-box">&#10003;</div>
                <div><strong>Lulus</strong></div>
            </div>
            <div class="checkbox-item">
                <div class="checkbox-box"></div>
                <div>Lulus, dengan keharusan untuk memperbaiki selama 1 Minggu</div>
            </div>
            <div class="checkbox-item">
                <div class="checkbox-box"></div>
                <div>Ditangguhkan dengan keharusan untuk memperbaiki Karya Seni/Desain selama 1 Minggu</div>
            </div>

            <!-- Signature of Ketua Sidang -->
            <div style="margin-top:18px; margin-left:10px;">
                <div>Bandung, <?= htmlspecialchars($bap['tanggal_text']); ?></div>
                <div style="margin-top:2px;">Ketua Sidang,</div>
                <div class="sig-box" style="height:55px; margin:6px 0; display:flex; align-items:center;">
                    <?php 
                    $ttd_ks = !empty($bap['ttd_ketua_sidang']) ? $bap['ttd_ketua_sidang'] : (!empty($bap['ttd_penguji_1']) ? $bap['ttd_penguji_1'] : null);
                    if (!empty($ttd_ks) && file_exists(FCPATH . 'uploads/signatures/' . $ttd_ks)): 
                    ?>
                        <img src="<?= base_url('uploads/signatures/' . $ttd_ks); ?>" style="max-height: 52px; width: auto; display: block;">
                    <?php else: ?>
                        <div style="font-family: 'Brush Script MT', cursive, sans-serif; font-size: 20pt; color: #0284c7;">
                            ~ TTD Digital ~
                        </div>
                    <?php endif; ?>
                </div>
                <div><strong><?= htmlspecialchars($bap['ketua_sidang'] ?? $bap['penguji_1']); ?></strong></div>
            </div>

            <!-- Standar Range Nilai Table (Matching Photo 6) -->
            <div style="margin-top:20px;">
                <p style="font-size:9.5pt; font-weight:bold; margin-bottom:4px;">Range Nilai :</p>
                <table class="range-table">
                    <thead>
                        <tr>
                            <th>Nilai Skor Matakuliah (NSM)</th>
                            <th>Nilai Mata Kuliah (NMK)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>85 &lt; NSM</td><td><strong>A</strong></td></tr>
                        <tr><td>75 &lt; NSM &lt;= 85</td><td><strong>AB</strong></td></tr>
                        <tr><td>65 &lt; NSM &lt;= 75</td><td><strong>B</strong></td></tr>
                        <tr><td>60 &lt; NSM &lt;= 65</td><td><strong>BC</strong></td></tr>
                        <tr><td>50 &lt; NSM &lt;= 60</td><td><strong>C</strong></td></tr>
                        <tr><td>40 &lt; NSM &lt;= 50</td><td><strong>D</strong></td></tr>
                        <tr><td>NSM &lt;= 40</td><td><strong>E</strong></td></tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <?php if (!empty($auto_print)): ?>
    <script>
        window.addEventListener('load', function() {
            window.print();
        });
    </script>
    <?php endif; ?>
</body>
</html>
