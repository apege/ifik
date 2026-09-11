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
            .qr-box { border: 1.5px solid #000 !important; box-shadow: none !important; }
            #qrcode { width: 105px !important; height: 105px !important; }
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

        .qr-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
        }

        .qr-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }

        #qrcode {
            width: 105px;
            height: 105px;
            margin: 0 auto;
            background: #ffffff;
        }

        #qrcode canvas {
            display: none !important;
        }

        #qrcode img {
            width: 100% !important;
            height: 100% !important;
            display: block !important;
            margin: 0 auto;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
            image-rendering: pixelated;
        }

        .qr-label {
            font-size: 7pt;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #64748b;
            text-align: center;
            max-width: 130px;
            line-height: 1.2;
            font-weight: 600;
        }

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
        @media screen and (max-width: 768px) {
            body {
                padding: 10px 8px;
                background: #f1f5f9;
            }

            .print-btn-bar {
                margin-bottom: 12px;
            }

            .btn-print {
                width: 100%;
                padding: 10px 14px;
                font-size: 13px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }

            .paper {
                width: 100% !important;
                min-height: auto !important;
                max-width: 100% !important;
                padding: 18px 14px !important;
                box-shadow: 0 4px 16px rgba(15, 23, 42, 0.08) !important;
                border-radius: 12px;
                box-sizing: border-box !important;
                overflow-x: hidden;
            }

            .kop-surat {
                gap: 10px;
                padding-bottom: 8px;
                margin-bottom: 14px;
                border-bottom: 2px double #000;
            }

            .kop-logo {
                width: 44px;
                height: 44px;
                flex-shrink: 0;
            }

            .kop-text h2 {
                font-size: 9.5pt;
                line-height: 1.2;
            }

            .kop-text h3 {
                font-size: 8.5pt;
                line-height: 1.2;
            }

            .kop-text p {
                font-size: 6.8pt;
                line-height: 1.2;
                margin-top: 2px;
            }

            .surat-title {
                margin-bottom: 12px;
            }

            .surat-title h4 {
                font-size: 10pt;
                line-height: 1.3;
            }

            .surat-title p {
                font-size: 8pt;
            }

            .content {
                font-size: 8.8pt;
                line-height: 1.5;
                text-align: left;
            }

            .content p {
                margin-bottom: 8px;
            }

            table.detail-table {
                margin: 8px 0 12px 0;
                font-size: 8.5pt;
                width: 100%;
            }

            table.detail-table td {
                padding: 3px 2px;
                word-break: break-word;
            }

            table.detail-table td:first-child {
                width: 110px;
                font-size: 8.5pt;
                flex-shrink: 0;
            }

            .signature-section {
                margin-top: 18px;
                display: flex;
                flex-direction: row;
                justify-content: space-between;
                align-items: flex-end;
                gap: 8px;
            }

            .qr-box {
                padding: 4px;
                flex-shrink: 0;
            }

            .signature-box {
                width: auto;
                max-width: 60%;
                font-size: 8pt;
            }

            .signature-box p {
                font-size: 7.8pt;
                margin-bottom: 2px;
                line-height: 1.2;
            }

            .signature-img-wrap {
                height: 48px;
                margin: 2px auto;
            }

            .signature-img-wrap img {
                max-height: 45px;
                max-width: 100%;
            }

            .signature-name {
                font-size: 8pt;
            }
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
            <div class="qr-section">
                <div class="qr-box">
                    <div id="qrcode"></div>
                </div>
                <div class="qr-label">Scan untuk Verifikasi Dokumen</div>
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
        (function() {
            const qrContainer = document.getElementById("qrcode");
            const qrText = <?= json_encode($qr_data) ?>;
            if (!qrContainer || !qrText) return;

            const tempDiv = document.createElement('div');
            new QRCode(tempDiv, {
                text: qrText,
                width: 260,
                height: 260,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.M
            });

            function renderQRWithQuietZone(attempts) {
                if (attempts <= 0) return;
                const srcCanvas = tempDiv.querySelector('canvas');
                if (srcCanvas && srcCanvas.width > 0) {
                    const margin = 24; // 4-module quiet zone in px
                    const outCanvas = document.createElement('canvas');
                    outCanvas.width = srcCanvas.width + (margin * 2);
                    outCanvas.height = srcCanvas.height + (margin * 2);
                    const ctx = outCanvas.getContext('2d');
                    
                    ctx.fillStyle = "#ffffff";
                    ctx.fillRect(0, 0, outCanvas.width, outCanvas.height);
                    ctx.drawImage(srcCanvas, margin, margin);
                    
                    const img = document.createElement('img');
                    img.src = outCanvas.toDataURL('image/png');
                    img.alt = 'QR Code';
                    img.style.cssText = 'width: 100%; height: 100%; display: block; image-rendering: pixelated;';
                    qrContainer.innerHTML = '';
                    qrContainer.appendChild(img);
                } else {
                    setTimeout(() => renderQRWithQuietZone(attempts - 1), 30);
                }
            }

            renderQRWithQuietZone(20);
        })();
    </script>
</body>
</html>