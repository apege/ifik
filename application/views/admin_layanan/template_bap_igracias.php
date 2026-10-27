<?php
/**
 * Template Berita Acara Pelaksanaan Sidang Format iGracias - Admin LAA
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'BERITA ACARA PENYELENGGARAAN SIDANG TA/ PA'; ?></title>
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
            font-size: 10.5pt;
            line-height: 1.4;
        }
        .page-container {
            max-width: 750px;
            margin: 0 auto;
            background: #ffffff;
        }
        .main-header {
            text-align: center;
            margin-bottom: 25px;
        }
        .main-header h1 {
            font-size: 13.5pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .info-table td.label-col {
            width: 150px;
            font-weight: bold;
        }
        .info-table td.colon-col {
            width: 15px;
        }
        .section-intro {
            margin: 14px 0 8px 0;
        }
        .sub-info-table {
            width: 100%;
            margin-left: 20px;
            margin-bottom: 14px;
        }
        .sub-info-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .sub-info-table td.label-sub {
            width: 120px;
        }
        .student-table {
            width: 100%;
            margin-left: 20px;
            margin-bottom: 20px;
        }
        .student-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .student-table td.label-std {
            width: 80px;
        }
        .student-table td.colon-std {
            width: 15px;
        }
        .title-box {
            line-height: 1.4;
        }
        .title-lang {
            color: #1e293b;
        }
        .examiners-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 20px 0;
        }
        .examiners-table th, .examiners-table td {
            border: 1px solid #000000;
            padding: 8px 10px;
            vertical-align: middle;
        }
        .examiners-table th {
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
        }
        .examiners-table td.no-col {
            width: 35px;
            text-align: center;
        }
        .examiners-table td.sig-col {
            width: 180px;
            text-align: center;
        }
        .sig-img {
            max-height: 48px;
            width: auto;
            display: block;
            margin: 0 auto;
        }
        .sig-fallback {
            font-family: 'Brush Script MT', cursive, sans-serif;
            font-size: 16pt;
            color: #0284c7;
        }
        .result-section {
            margin: 20px 0 35px 0;
            font-size: 10.5pt;
        }
        .result-option {
            font-weight: bold;
            margin-top: 4px;
        }
        .result-option.active {
            text-decoration: none;
        }
        .result-option.inactive {
            text-decoration: line-through;
            color: #475569;
        }
        .footer-grid {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        .footer-col-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .footer-col-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .notes-box {
            font-size: 10pt;
        }
        .notes-title {
            font-weight: bold;
            margin-bottom: 4px;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="page-container">

        <!-- Judul Berita Acara -->
        <div class="main-header">
            <h1>BERITA ACARA PENYELENGGARAAN SIDANG TA/ PA</h1>
        </div>

        <!-- Meta Fakultas & Prodi -->
        <table class="info-table">
            <tr>
                <td class="label-col">FAKULTAS</td>
                <td class="colon-col">:</td>
                <td><strong><?= htmlspecialchars($bap['fakultas'] ?? 'Fakultas Industri Kreatif'); ?></strong></td>
            </tr>
            <tr>
                <td class="label-col">PROGRAM STUDI</td>
                <td class="colon-col">:</td>
                <td><strong><?= htmlspecialchars($bap['prodi'] ?? 'Desain Komunikasi Visual'); ?></strong></td>
            </tr>
        </table>

        <div class="section-intro">
            Telah diselenggarakan Ujian Skripsi pada,
        </div>

        <!-- Detail Jadwal Sidang -->
        <table class="sub-info-table">
            <tr>
                <td class="label-sub">Hari/ Tanggal</td>
                <td style="width: 15px;">:</td>
                <td><?= htmlspecialchars($bap['tanggal_text'] ?? $bap['tanggal_sidang'] ?? '2026-07-22'); ?></td>
            </tr>
            <tr>
                <td class="label-sub">Waktu</td>
                <td>:</td>
                <td><?= htmlspecialchars($bap['waktu_sidang'] ?? '13:30:00'); ?></td>
            </tr>
            <tr>
                <td class="label-sub">Tempat</td>
                <td>:</td>
                <td><?= htmlspecialchars($bap['tempat'] ?? $bap['ruang_sidang'] ?? '4.08'); ?></td>
            </tr>
        </table>

        <div class="section-intro">
            Terhadap Peserta sidang dengan data sebagai berikut,
        </div>

        <!-- Detail Mahasiswa & Judul TA -->
        <table class="student-table">
            <tr>
                <td class="label-std">Nama</td>
                <td class="colon-std">:</td>
                <td><?= htmlspecialchars($bap['nama']); ?></td>
            </tr>
            <tr>
                <td class="label-std">NIM</td>
                <td class="colon-std">:</td>
                <td><?= htmlspecialchars($bap['nim']); ?></td>
            </tr>
            <tr>
                <td class="label-std">Judul<br>TA/PA</td>
                <td class="colon-std">:</td>
                <td class="title-box">
                    <div class="title-lang">(Bahasa Indonesia)</div>
                    <div><?= htmlspecialchars($bap['judul']); ?></div>
                    <?php if (!empty($bap['judul_en'])): ?>
                        <div class="title-lang" style="margin-top: 4px;">(Bahasa Inggris)</div>
                        <div><?= htmlspecialchars($bap['judul_en']); ?></div>
                    <?php else: ?>
                        <div class="title-lang" style="margin-top: 4px;">(Bahasa Inggris)</div>
                        <div><?= htmlspecialchars($bap['judul']); ?></div>
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <div class="section-intro">
            Oleh Tim Penguji yang namanya tercantum di bawah ini:
        </div>

        <!-- Tabel Tim Penguji -->
        <table class="examiners-table">
            <thead>
                <tr>
                    <th class="no-col">No</th>
                    <th>NAMA</th>
                    <th class="sig-col">TANDA TANGAN</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="no-col">1</td>
                    <td>
                        Penguji 1 :<br>
                        <strong><?= htmlspecialchars($bap['penguji_1_kode'] ?? 'LGM'); ?> - <?= htmlspecialchars($bap['penguji_1']); ?></strong>
                    </td>
                    <td class="sig-col">
                        <?php if (!empty($bap['ttd_penguji_1']) && file_exists(FCPATH . 'uploads/signatures/' . $bap['ttd_penguji_1'])): ?>
                            <img src="<?= base_url('uploads/signatures/' . $bap['ttd_penguji_1']); ?>" class="sig-img">
                        <?php else: ?>
                            <span class="sig-fallback">~ TTD Digital ~</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="no-col">2</td>
                    <td>
                        Penguji 2 :<br>
                        <strong><?= htmlspecialchars($bap['penguji_2_kode'] ?? 'ALW'); ?> - <?= htmlspecialchars($bap['penguji_2']); ?></strong>
                    </td>
                    <td class="sig-col">
                        <?php if (!empty($bap['ttd_penguji_2']) && file_exists(FCPATH . 'uploads/signatures/' . $bap['ttd_penguji_2'])): ?>
                            <img src="<?= base_url('uploads/signatures/' . $bap['ttd_penguji_2']); ?>" class="sig-img">
                        <?php else: ?>
                            <span class="sig-fallback">~ TTD Digital ~</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="no-col">3</td>
                    <td>
                        <?php if (!empty($bap['penguji_3'])): ?>
                            Penguji 3 :<br>
                            <strong><?= htmlspecialchars($bap['penguji_3_kode'] ?? 'ISI'); ?> - <?= htmlspecialchars($bap['penguji_3']); ?></strong>
                        <?php else: ?>
                            Pembimbing 1 :<br>
                            <strong><?= htmlspecialchars($bap['pembimbing_1_kode'] ?? 'ISI'); ?> - <?= htmlspecialchars($bap['pembimbing_1']); ?></strong>
                        <?php endif; ?>
                    </td>
                    <td class="sig-col">
                        <?php 
                        $ttd_p3 = !empty($bap['ttd_penguji_3']) ? $bap['ttd_penguji_3'] : (!empty($bap['ttd_pembimbing_1']) ? $bap['ttd_pembimbing_1'] : null);
                        if (!empty($ttd_p3) && file_exists(FCPATH . 'uploads/signatures/' . $ttd_p3)): 
                        ?>
                            <img src="<?= base_url('uploads/signatures/' . $ttd_p3); ?>" class="sig-img">
                        <?php else: ?>
                            <span class="sig-fallback">~ TTD Digital ~</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Keputusan Hasil Evaluasi (Layak / Tidak Layak) -->
        <div class="result-section">
            <div>Peserta Sidang TA/PA mendapat nilai rata-rata :</div>
            <?php 
                $is_layak = ($bap['indeks_huruf'] ?? 'B') !== 'E' && ($bap['indeks_huruf'] ?? 'B') !== 'D'; 
            ?>
            <div class="result-option <?= $is_layak ? 'active' : 'inactive'; ?>" style="margin-left: 15px; margin-top: 6px;">
                LAYAK dijadikan sebagai Referensi
            </div>
            <div class="result-option <?= !$is_layak ? 'active' : 'inactive'; ?>" style="margin-left: 15px; margin-top: 3px;">
                TIDAK LAYAK dijadikan sebagai Referensi
            </div>
        </div>

        <!-- Footer TTD Ketua Penguji & Catatan Penting -->
        <div class="footer-grid">
            <div class="footer-col-left">
                <div>Ketua Tim Penguji</div>
                <div style="height: 65px; margin: 6px 0; display: flex; align-items: center;">
                    <?php if (!empty($bap['ttd_penguji_1']) && file_exists(FCPATH . 'uploads/signatures/' . $bap['ttd_penguji_1'])): ?>
                        <img src="<?= base_url('uploads/signatures/' . $bap['ttd_penguji_1']); ?>" style="max-height: 60px; width: auto; display: block;">
                    <?php else: ?>
                        <span class="sig-fallback" style="font-size: 18pt;">~ TTD Digital ~</span>
                    <?php endif; ?>
                </div>
                <div>( <?= htmlspecialchars($bap['penguji_1']); ?> )</div>
            </div>

            <div class="footer-col-right">
                <div class="notes-box">
                    <div class="notes-title">CATATAN PENTING KETUA PENGUJI :</div>
                    <div><?= !empty($bap['catatan_penguji']) ? htmlspecialchars($bap['catatan_penguji']) : '-'; ?></div>
                </div>
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
