<?php
/**
 * Template Berita Acara Pelaksanaan Sidang Versi Fakultas - Admin LAA
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'BERITA ACARA PENYELENGGARAAN SIDANG TUGAS AKHIR'; ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 20mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #000000;
            background: #ffffff;
            margin: 0;
            padding: 20px 35px;
            font-size: 10pt;
            line-height: 1.4;
        }
        .page-container {
            max-width: 750px;
            margin: 0 auto;
            background: #ffffff;
        }
        .page-break {
            page-break-before: always;
            margin-top: 30px;
            padding-top: 20px;
        }
        @media print {
            body { padding: 0; }
            .page-break { page-break-before: always; border-top: none; margin-top: 0; padding-top: 0; }
            .no-print { display: none !important; }
        }

        /* Header Style */
        .header-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        .logo-col {
            display: table-cell;
            width: 180px;
            vertical-align: middle;
        }
        .logo-img {
            max-height: 55px;
            width: auto;
            display: block;
        }
        .title-col {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }
        .title-col h2 {
            font-size: 11pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .title-col h3 {
            font-size: 11pt;
            font-weight: bold;
            margin: 3px 0 0 0;
            text-transform: uppercase;
        }
        .title-col h4 {
            font-size: 10.5pt;
            font-weight: bold;
            margin: 3px 0 0 0;
            text-transform: uppercase;
        }

        p {
            margin: 8px 0;
            text-align: justify;
        }

        .data-table {
            width: 100%;
            margin: 10px 0;
            border-collapse: collapse;
        }
        .data-table td {
            padding: 2.5px 0;
            vertical-align: top;
        }
        .data-table td.num-col {
            width: 25px;
            font-weight: bold;
        }
        .data-table td.label-col {
            width: 130px;
        }
        .data-table td.colon-col {
            width: 15px;
        }

        /* Catatan Section */
        .notes-section {
            margin: 15px 0 25px 0;
        }
        .notes-title {
            font-weight: bold;
            margin-bottom: 6px;
        }
        .note-content {
            font-size: 10pt;
            margin-bottom: 8px;
            min-height: 20px;
        }
        .underline-line {
            border-bottom: 1px solid #000000;
            height: 24px;
            width: 100%;
        }

        /* Signatures Table */
        .date-right {
            text-align: right;
            margin-bottom: 10px;
            margin-right: 40px;
        }
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .sig-table th, .sig-table td {
            padding: 6px 4px;
            vertical-align: middle;
        }
        .sig-table th {
            text-align: left;
            font-weight: bold;
        }
        .sig-table td.role-col {
            width: 180px;
        }
        .sig-table td.name-col {
            width: 300px;
        }
        .sig-table td.sig-col {
            text-align: center;
        }
        .sig-img {
            max-height: 44px;
            width: auto;
            display: block;
            margin: 0 auto;
        }
        .sig-fallback {
            font-family: 'Brush Script MT', cursive, sans-serif;
            font-size: 16pt;
            color: #0284c7;
        }

        /* Page 2 Styles */
        .eval-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0 20px 0;
        }
        .eval-table th, .eval-table td {
            border: 1px solid #000000;
            padding: 5px 8px;
            font-size: 10pt;
        }
        .eval-table th {
            text-align: center;
            font-weight: bold;
        }
        .checkbox-group {
            margin: 15px 0 25px 0;
        }
        .checkbox-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin: 6px 0;
        }
        .checkbox-box {
            width: 15px;
            height: 15px;
            border: 1.5px solid #000000;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 10pt;
            margin-top: 1px;
            flex-shrink: 0;
        }
        .range-table {
            width: 60%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .range-table th, .range-table td {
            border: 1px solid #000000;
            padding: 4px 8px;
            font-size: 9.5pt;
            text-align: center;
        }
        .range-table th {
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="page-container">

        <!-- ============================================================= -->
        <!-- HALAMAN 1: BERITA ACARA PENYELENGGARAAN SIDANG                -->
        <!-- ============================================================= -->
        <div class="page-1">

            <!-- Logo & Header -->
            <div class="header-grid">
                <div class="logo-col">
                    <img src="<?= base_url('assets/images/logo_telkom_university.png'); ?>" alt="Telkom University" class="logo-img" onerror="this.src='https://upload.wikimedia.org/wikipedia/id/8/89/Telkom_University_Logo.svg';">
                </div>
                <div class="title-col">
                    <h2>BERITA ACARA</h2>
                    <h3>PENYELENGGARAAN SIDANG TUGAS AKHIR</h3>
                    <h4>FAKULTAS INDUSTRI KREATIF</h4>
                </div>
            </div>

            <!-- Intro Paragraph -->
            <p>
                Pada hari <strong><?= htmlspecialchars($bap['hari'] ?? 'Rabu'); ?></strong>, <strong><?= htmlspecialchars($bap['tanggal_text'] ?? '22 Juli 2026'); ?></strong> bertempat di <?= htmlspecialchars($bap['tempat'] ?? $bap['ruang_sidang'] ?? 'Kampus Fakultas Industri Kreatif Jl. Telekomunikasi, Ters. Buah Batu Bandung'); ?> telah diselenggarakan Sidang Tugas Akhir Semester <?= htmlspecialchars($bap['semester'] ?? 'Genap'); ?> Tahun Akademik <?= htmlspecialchars($bap['tahun_akademik'] ?? '2018/2019'); ?>
            </p>

            <!-- 1. Data Peserta -->
            <table class="data-table">
                <tr>
                    <td class="num-col">1.</td>
                    <td class="label-col">Nama</td>
                    <td class="colon-col">:</td>
                    <td><?= htmlspecialchars($bap['nama']); ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td class="label-col">NIM</td>
                    <td class="colon-col">:</td>
                    <td><?= htmlspecialchars($bap['nim']); ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td class="label-col">Program Studi</td>
                    <td class="colon-col">:</td>
                    <td><?= htmlspecialchars($bap['prodi']); ?></td>
                </tr>
                <tr>
                    <td class="num-col" style="padding-top: 8px;">2.</td>
                    <td class="label-col" colspan="3" style="padding-top: 8px;"><strong>Topik Tugas Akhir *)</strong></td>
                </tr>
                <tr>
                    <td></td>
                    <td class="label-col">Judul</td>
                    <td class="colon-col">:</td>
                    <td><?= htmlspecialchars($bap['judul']); ?></td>
                </tr>
            </table>

            <!-- 3. Catatan Penting -->
            <div class="notes-section">
                <div class="notes-title">3. Catatan Penting Selama Kegiatan Berlangsung :</div>
                <div class="note-content">
                    <?= !empty($bap['catatan_penguji']) ? htmlspecialchars($bap['catatan_penguji']) : ''; ?>
                </div>
                <div class="underline-line"></div>
                <div class="underline-line"></div>
                <div class="underline-line"></div>
                <div class="underline-line"></div>
            </div>

            <!-- Date & Signatures Table -->
            <div class="date-right">
                Bandung, <?= htmlspecialchars($bap['tanggal_text'] ?? '22 Juli 2026'); ?>
            </div>

            <table class="sig-table">
                <thead>
                    <tr>
                        <th class="role-col">Tim Penguji</th>
                        <th class="name-col">Nama</th>
                        <th class="sig-col">Tanda Tangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="role-col">Pembimbing 1</td>
                        <td class="name-col">: <?= htmlspecialchars($bap['pembimbing_1']); ?></td>
                        <td class="sig-col">
                            <?php if (!empty($bap['ttd_pembimbing_1']) && file_exists(FCPATH . 'uploads/signatures/' . $bap['ttd_pembimbing_1'])): ?>
                                <img src="<?= base_url('uploads/signatures/' . $bap['ttd_pembimbing_1']); ?>" class="sig-img">
                            <?php else: ?>
                                <span class="sig-fallback">~ TTD Digital ~</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="role-col">Pembimbing 2</td>
                        <td class="name-col">: <?= htmlspecialchars($bap['pembimbing_2']); ?></td>
                        <td class="sig-col">
                            <?php if (!empty($bap['ttd_pembimbing_2']) && file_exists(FCPATH . 'uploads/signatures/' . $bap['ttd_pembimbing_2'])): ?>
                                <img src="<?= base_url('uploads/signatures/' . $bap['ttd_pembimbing_2']); ?>" class="sig-img">
                            <?php else: ?>
                                <span class="sig-fallback">~ TTD Digital ~</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="role-col">Penguji 1/Ketua Sidang</td>
                        <td class="name-col">: <?= htmlspecialchars($bap['penguji_1']); ?></td>
                        <td class="sig-col">
                            <?php if (!empty($bap['ttd_penguji_1']) && file_exists(FCPATH . 'uploads/signatures/' . $bap['ttd_penguji_1'])): ?>
                                <img src="<?= base_url('uploads/signatures/' . $bap['ttd_penguji_1']); ?>" class="sig-img">
                            <?php else: ?>
                                <span class="sig-fallback">~ TTD Digital ~</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="role-col">Penguji 2</td>
                        <td class="name-col">: <?= htmlspecialchars($bap['penguji_2']); ?></td>
                        <td class="sig-col">
                            <?php if (!empty($bap['ttd_penguji_2']) && file_exists(FCPATH . 'uploads/signatures/' . $bap['ttd_penguji_2'])): ?>
                                <img src="<?= base_url('uploads/signatures/' . $bap['ttd_penguji_2']); ?>" class="sig-img">
                            <?php else: ?>
                                <span class="sig-fallback">~ TTD Digital ~</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <p style="font-size: 9pt; font-style: italic; margin-top: 10px;">
                *) Wajib diisi oleh ketua sidang
            </p>

        </div>

        <!-- ============================================================= -->
        <!-- HALAMAN 2: NILAI SIDANG KOMPREHENSIF                          -->
        <!-- ============================================================= -->
        <div class="page-break"></div>

        <div class="page-2">

            <p><strong>4. Nilai Sidang Tugas Akhir Komprehensif</strong></p>
            <p>
                Berdasarkan hasil evaluasi dari para Anggota Sidang Tugas Akhir, meliputi aspek-aspek yang dinilai dengan skor rata-rata sebagai berikut:
            </p>

            <!-- Tabel Evualuasi Nilai (Matching PDF 2 Page 2) -->
            <table class="eval-table">
                <thead>
                    <tr>
                        <th style="border: none; background: transparent;"></th>
                        <th style="width: 100px;">Nilai</th>
                        <th style="width: 100px;">Bobot</th>
                        <th style="width: 120px;">Nilai*Bobot</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($bap['evaluasi_nilai'])): ?>
                        <?php foreach ($bap['evaluasi_nilai'] as $ev): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($ev['peran']); ?></strong></td>
                                <td align="center"><?= $ev['nilai']; ?></td>
                                <td align="center"><?= $ev['bobot']; ?></td>
                                <td align="center"><?= number_format($ev['nilai_bobot'], 1); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td><strong>Pembimbing 1</strong></td>
                            <td align="center">70</td>
                            <td align="center">0.4</td>
                            <td align="center">28</td>
                        </tr>
                        <tr>
                            <td><strong>Pembimbing 2</strong></td>
                            <td align="center">72</td>
                            <td align="center">0.2</td>
                            <td align="center">14.4</td>
                        </tr>
                        <tr>
                            <td><strong>Penguji 1</strong></td>
                            <td align="center">65.5</td>
                            <td align="center">0.2</td>
                            <td align="center">13.1</td>
                        </tr>
                        <tr>
                            <td><strong>Penguji 2</strong></td>
                            <td align="center">65.5</td>
                            <td align="center">0.2</td>
                            <td align="center">13.1</td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td><strong>Jumlah</strong></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="3"><strong>Rata-Rata :</strong></td>
                        <td align="center"><strong><?= number_format($bap['nilai_akhir'] ?? 68.6, 1); ?></strong></td>
                    </tr>
                    <tr>
                        <td colspan="3"><strong>Dengan Huruf :</strong></td>
                        <td align="center"><strong><?= htmlspecialchars($bap['indeks_huruf'] ?? 'B'); ?></strong></td>
                    </tr>
                </tbody>
            </table>

            <p style="margin-top: 15px;">
                Atas nama Tim Penguji menetapkan hasil akhir penilaian pada Sidang Tugas Akhir adalah:
            </p>

            <!-- Checkbox Keputusan Kelulusan -->
            <?php 
                $indeks = $bap['indeks_huruf'] ?? 'B';
                $status_lulus = ($indeks !== 'E' && $indeks !== 'D');
            ?>
            <div class="checkbox-group">
                <div class="checkbox-item">
                    <div class="checkbox-box"><?= $status_lulus ? '&#10003;' : ''; ?></div>
                    <div><strong>Lulus</strong></div>
                </div>
                <div class="checkbox-item">
                    <div class="checkbox-box"></div>
                    <div>Lulus, dengan keharusan untuk memperbaiki selama 1 Minggu</div>
                </div>
                <div class="checkbox-item">
                    <div class="checkbox-box"><?= !$status_lulus ? '&#10003;' : ''; ?></div>
                    <div>Ditangguhkan dengan keharusan untuk memperbaiki Karya Seni/Desain selama 1 Minggu</div>
                </div>
            </div>

            <!-- Tanggal & TTD Ketua Sidang -->
            <div style="margin-top: 20px;">
                <div>Bandung, <?= htmlspecialchars($bap['tanggal_text'] ?? '22 Juli 2026'); ?></div>
                <div style="margin-top: 3px;">Ketua Sidang,</div>
                <div style="height: 60px; margin: 6px 0; display: flex; align-items: center;">
                    <?php 
                    $ttd_ks = !empty($bap['ttd_ketua_sidang']) ? $bap['ttd_ketua_sidang'] : (!empty($bap['ttd_penguji_1']) ? $bap['ttd_penguji_1'] : null);
                    if (!empty($ttd_ks) && file_exists(FCPATH . 'uploads/signatures/' . $ttd_ks)): 
                    ?>
                        <img src="<?= base_url('uploads/signatures/' . $ttd_ks); ?>" class="sig-img" style="margin: 0;">
                    <?php else: ?>
                        <span class="sig-fallback" style="font-size: 18pt;">~ TTD Digital ~</span>
                    <?php endif; ?>
                </div>
                <div><strong><?= htmlspecialchars($bap['ketua_sidang'] ?? $bap['penguji_1']); ?></strong></div>
            </div>

            <!-- Tabel Range Nilai -->
            <div style="margin-top: 25px;">
                <div style="font-weight: bold; margin-bottom: 6px;">Range Nilai :</div>
                <table class="range-table">
                    <thead>
                        <tr>
                            <th>Nilai Skor Matakuliah (NSM) :</th>
                            <th>Nilai Mata Kuliah (NMK) :</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>85&lt;NSM</td><td>A</td></tr>
                        <tr><td>75&lt;NSM&lt;=85</td><td>AB</td></tr>
                        <tr><td>65&lt;NSM&lt;=75</td><td>B</td></tr>
                        <tr><td>60&lt;NSM&lt;=65</td><td>BC</td></tr>
                        <tr><td>50&lt;NSM&lt;=60</td><td>C</td></tr>
                        <tr><td>40&lt;NSM&lt;=50</td><td>D</td></tr>
                        <tr><td>NSM&lt;=40</td><td>E</td></tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <?php if (!empty($auto_print)): ?>
    <script>
        window.addEventListener('load', function() { window.print(); });
    </script>
    <?php endif; ?>
</body>
</html>
