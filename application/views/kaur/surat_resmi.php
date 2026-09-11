<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Surat Resmi Peminjaman Ruangan') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Times New Roman', Times, serif;
            background: #e2e8f0;
            color: #000;
            padding: 30px;
        }

        .paper {
            width: 210mm;
            min-height: 297mm;
            background: #ffffff;
            margin: 0 auto;
            padding: 25mm 25mm;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            position: relative;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .paper { width: 100%; min-height: 100%; box-shadow: none; padding: 15mm; margin: 0; }
            .no-print { display: none !important; }
        }

        /* Kop Surat */
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 24px;
            gap: 20px;
        }

        .kop-logo { width: 75px; height: 75px; object-fit: contain; }
        .kop-text { text-align: center; flex: 1; }
        .kop-text h2 { font-size: 14pt; font-weight: bold; text-transform: uppercase; line-height: 1.2; }
        .kop-text h3 { font-size: 12pt; font-weight: bold; text-transform: uppercase; }
        .kop-text p { font-size: 9pt; font-style: italic; margin-top: 4px; }

        .surat-title { text-align: center; margin-bottom: 20px; }
        .surat-title h4 { font-size: 12pt; text-transform: uppercase; text-decoration: underline; }
        .surat-title p { font-size: 10pt; margin-top: 2px; }

        .content { font-size: 11pt; line-height: 1.6; text-align: justify; }
        .content p { margin-bottom: 12px; }

        table.detail-table { width: 100%; border-collapse: collapse; margin: 12px 0 20px 20px; font-size: 11pt; }
        table.detail-table td { padding: 4px 6px; vertical-align: top; }
        table.detail-table td:first-child { width: 180px; font-weight: bold; }

        /* Tanda Tangan & QR Code */
        .signature-section {
            margin-top: 36px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .qr-box {
            text-align: center;
            border: 1px dashed #64748b;
            padding: 10px;
            border-radius: 8px;
            display: inline-block;
        }

        .qr-box span { font-size: 8pt; display: block; margin-top: 4px; color: #475569; font-family: 'Plus Jakarta Sans', sans-serif; }

        .signature-box {
            text-align: center;
            width: 260px;
        }

        .signature-box p { margin-bottom: 6px; }
        .signature-img-wrap {
            height: 75px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 4px auto;
        }
        .signature-img-wrap img {
            max-height: 70px;
            max-width: 200px;
            object-fit: contain;
        }
        .signature-name { font-weight: bold; text-decoration: underline; margin-top: 4px; }

        .print-btn-bar {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-print {
            background: #16a34a;
            color: #fff;
            padding: 10px 24px;
            border-radius: 8px;
            border: none;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            font-family: 'Plus Jakarta Sans', sans-serif;
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
        }
    </style>
</head>
<body>

    <div class="print-btn-bar no-print">
        <button type="button" class="btn-print" onclick="window.print()">🖨️ Cetak / Download Surat (PDF)</button>
    </div>

    <div class="paper">
        <!-- Kop Surat -->
        <div class="kop-surat">
            <img src="<?= base_url('assets/img/telu.png') ?>" alt="Logo" class="kop-logo" onerror="this.style.display='none'">
            <div class="kop-text">
                <h2>Fakultas Industri Kreatif</h2>
                <h3>Unit Laboratorium & Fasilitas Akademik</h3>
                <p>Gedung Sebatik (Gedung Industri Kreatif) • Jl. Telekomunikasi No. 1, Terusan Buahbatu, Bandung</p>
            </div>
        </div>

        <!-- Judul Surat -->
        <div class="surat-title">
            <h4>Surat Persetujuan Peminjaman Ruangan</h4>
            <p>Nomor: <?= $nomor_surat ?></p>
        </div>

        <!-- Isi Surat -->
        <div class="content">
            <p>Berdasarkan permohonan peminjaman ruangan yang telah diverifikasi oleh petugas laboratorium, dengan ini <?= htmlspecialchars($penandatangan['jabatan_resmi'] ?? 'Kepala Urusan / Kepala Laboratorium') ?> Fakultas Industri Kreatif memberikan <strong>Persetujuan Resmi (ACC)</strong> atas penggunaan fasilitas laboratorium kepada:</p>

            <table class="detail-table">
                <tr>
                    <td>Nama Peminjam</td>
                    <td>: <?= htmlspecialchars($booking->nama_lengkap) ?></td>
                </tr>
                <tr>
                    <td>Ruangan / Laboratorium</td>
                    <td>: <?= htmlspecialchars(($booking->kode_ruangan ? $booking->kode_ruangan . ' - ' : '') . $booking->nama_ruangan) ?></td>
                </tr>
                <tr>
                    <td>Lokasi Fasilitas</td>
                    <td>: <?= htmlspecialchars($booking->lokasi ?: 'Gedung Industri Kreatif') ?></td>
                </tr>
                <tr>
                    <td>Kapasitas Ruangan</td>
                    <td>: <?= htmlspecialchars($booking->kapasitas ? $booking->kapasitas . ' Orang' : '-') ?></td>
                </tr>
                <tr>
                    <td>Tanggal Pelaksanaan</td>
                    <td>: <?= ($booking->tanggal_mulai === $booking->tanggal_selesai) ? date('d F Y', strtotime($booking->tanggal_mulai)) : date('d F Y', strtotime($booking->tanggal_mulai)) . ' s/d ' . date('d F Y', strtotime($booking->tanggal_selesai)) ?></td>
                </tr>
                <tr>
                    <td>Waktu Peminjaman</td>
                    <td>: <?= substr($booking->jam_mulai, 0, 5) ?> - <?= substr($booking->jam_selesai, 0, 5) ?> WIB</td>
                </tr>
                <tr>
                    <td>Agenda / Keperluan</td>
                    <td>: <?= htmlspecialchars($booking->keterangan ?: '-') ?></td>
                </tr>
                <tr>
                    <td>Status Keabsahan</td>
                    <td>: <strong style="color: #16a34a;">RESMI DISETUJUI (VALID)</strong></td>
                </tr>
            </table>

            <p>Demikian surat persetujuan ini diterbitkan untuk dipergunakan sebagaimana mestinya. Peminjam wajib menaati seluruh tata tertib dan SOP penggunaan fasilitas laboratorium Fakultas Industri Kreatif.</p>
        </div>

        <!-- Tanda Tangan & QR Code Verifikasi -->
        <div class="signature-section">
            <div class="qr-box">
                <div id="qrcode"></div>
                <span>Pindai QR untuk Verifikasi Keabsahan</span>
            </div>

            <div class="signature-box">
                <p>Bandung, <?= date('d F Y', strtotime($booking->updated_at ?? $booking->created_at ?? date('Y-m-d'))) ?><br><?= htmlspecialchars($penandatangan['jabatan'] ?? 'Kepala Urusan Laboratorium') ?>,</p>
                <div class="signature-img-wrap">
                    <?php if (!empty($penandatangan['tanda_tangan']) && file_exists(FCPATH . 'uploads/signatures/' . $penandatangan['tanda_tangan'])): ?>
                        <img src="<?= base_url('uploads/signatures/' . $penandatangan['tanda_tangan']) ?>" alt="Tanda Tangan Digital">
                    <?php endif; ?>
                </div>
                <div class="signature-name"><?= htmlspecialchars($penandatangan['nama'] ?? 'Kaur / Ka. Lab FIK') ?></div>
                <div style="font-size: 9pt;">NIP. <?= htmlspecialchars($penandatangan['nip'] ?? '198203152010121002') ?></div>
            </div>
        </div>
    </div>

    <script>
        new QRCode(document.getElementById("qrcode"), {
            text: "<?= $qr_data ?>",
            width: 85,
            height: 85,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    </script>
</body>
</html>