<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Peminjaman Ruangan - IFIK</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- jQuery & Flatpickr & SweetAlert2 & Select2 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Timepicker Styles -->
    <link rel="stylesheet" href="<?= base_url('assets/css/timepicker.css?v=' . filemtime(FCPATH . 'assets/css/timepicker.css')) ?>">

    <style>
        :root {
            --bg-color: #fbf7f1;
            --text-color: #1e293b;
            --primary: #ea580c;
            --primary-hover: #c2410c;
            --border-color: #e2e8f0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            min-height: 100vh;
            padding: 40px 20px;
            background-image:
                radial-gradient(at 0% 0%, rgba(234, 88, 12, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(234, 88, 12, 0.08) 0px, transparent 50%);
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .booking-page-container {
            width: 100%;
            max-width: 820px;
            background: rgba(255,255,255,0.96);
            border: 1px solid rgba(234,88,12,0.15);
            border-radius: 28px;
            padding: 36px 44px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.06);
        }

        .page-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
            padding-bottom: 18px;
            border-bottom: 1px solid rgba(226,232,240,0.8);
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.88rem;
            padding: 8px 16px;
            border-radius: 12px;
            background: #fff;
            border: 1px solid #e2e8f0;
            transition: all 0.25s ease;
        }
        .btn-back:hover { color: var(--primary); border-color: var(--primary); transform: translateX(-3px); }

        .page-title { font-size: 1.55rem; font-weight: 800; color: #0f172a; letter-spacing: -0.5px; }
        .page-subtitle { font-size: 0.85rem; color: #64748b; margin-top: 4px; }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-group.full-width { grid-column: 1 / -1; }

        .form-label {
            font-size: 0.84rem;
            font-weight: 700;
            color: #334155;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .form-control {
            width: 100%;
            padding: 13px 16px;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 500;
            color: #1e293b;
            transition: all 0.2s ease;
            outline: none;
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(234,88,12,0.12);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 95px;
            line-height: 1.5;
        }
        textarea.form-control::placeholder {
            color: #94a3b8;
            font-size: 0.86rem;
            font-weight: 400;
        }

        /* ===== SELECT2 BEAUTIFUL CUSTOM STYLING ===== */
        .select2-container--default .select2-selection--single {
            height: 48px !important;
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 12px !important;
            background: #ffffff !important;
            display: flex !important;
            align-items: center !important;
            padding: 0 12px !important;
            transition: all 0.2s ease !important;
        }
        .select2-container--default.select2-container--open .select2-selection--single,
        .select2-container--default .select2-selection--single:focus {
            border-color: #ea580c !important;
            box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.12) !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1e293b !important;
            font-size: 0.9rem !important;
            font-weight: 600 !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            padding-left: 4px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #94a3b8 !important;
            font-weight: 500 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px !important;
            right: 12px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #ea580c transparent transparent transparent !important;
            border-width: 6px 5px 0 5px !important;
        }
        .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #ea580c transparent !important;
            border-width: 0 5px 6px 5px !important;
        }

        /* Select2 Floating Popup Panel */
        .select2-dropdown {
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 16px !important;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.08) !important;
            overflow: hidden !important;
            background: #ffffff !important;
            z-index: 9999 !important;
            padding: 6px !important;
            margin-top: 6px !important;
        }

        .select2-results__option {
            padding: 10px 14px !important;
            font-size: 0.88rem !important;
            font-weight: 600 !important;
            color: #334155 !important;
            border-radius: 10px !important;
            margin-bottom: 2px !important;
            transition: all 0.15s ease !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #fff7ed !important;
            color: #ea580c !important;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #ede9fe !important;
            color: #7c3aed !important;
            font-weight: 700 !important;
        }

        .select2-results__options::-webkit-scrollbar { width: 6px; }
        .select2-results__options::-webkit-scrollbar-track { background: #f8fafc; border-radius: 10px; }
        .select2-results__options::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .select2-results__options::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        .btn-submit-booking {
            width: 100%;
            padding: 15px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 14px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(234,88,12,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .btn-submit-booking:hover { background: var(--primary-hover); transform: translateY(-2px); box-shadow: 0 12px 25px rgba(234,88,12,0.4); }
        .btn-submit-booking:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        /* Tab Jam/Menit Switcher */
        .tp-tab-wrap {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
            background: #f1f5f9;
            border-radius: 12px;
            padding: 4px;
            width: 100%;
        }
        .tp-tab-wrap div {
            flex: 1;
            text-align: center;
            padding: 8px 12px;
            border-radius: 9px;
            color: #64748b;
            font-size: 0.82rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .tp-tab-wrap div.active {
            background: #7c3aed;
            color: #fff;
            box-shadow: 0 2px 8px rgba(124,58,237,0.25);
        }

        /* Clock Hand Purple Circle Tip + Selected Number Highlight */
        #tpClockHand::after {
            content: '';
            position: absolute;
            top: -14px;
            left: -13px;
            width: 28px;
            height: 28px;
            background: #7c3aed;
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(124, 58, 237, 0.4);
            z-index: 1;
        }

        .tp-clock-number {
            position: absolute;
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
            border-radius: 50%;
            font-size: 0.85rem;
            font-weight: 600;
            color: #1e293b;
            cursor: pointer;
            transform: translate(-50%, -50%);
            transition: background 0.15s ease, color 0.15s ease;
        }

        .tp-clock-number.inner {
            font-size: 0.72rem;
            color: #64748b;
            font-weight: 500;
        }

        .tp-clock-number.active {
            background: #7c3aed !important;
            color: #ffffff !important;
            border-radius: 50% !important;
            font-weight: 800 !important;
            box-shadow: 0 2px 10px rgba(124, 58, 237, 0.45) !important;
            z-index: 20 !important;
        }

        .swal2-container { z-index: 99999 !important; }

        @media (max-width: 640px) {
            .booking-page-container { padding: 24px 20px; border-radius: 20px; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div class="booking-page-container">
        <div class="page-topbar">
            <div>
                <h1 class="page-title">Ajukan Peminjaman Ruangan</h1>
                <p class="page-subtitle">Silakan lengkapi form berikut untuk mengajukan peminjaman ruangan.</p>
            </div>
            <a href="<?= base_url('kalender') ?>" class="btn-back">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Kembali
            </a>
        </div>

        <form id="formAjukanBooking" onsubmit="submitBooking(event)">
            <div class="form-grid">

                <!-- Nama Lengkap / NIM (Row 1 Col 1) -->
                <div class="form-group">
                    <label class="form-label">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        Nama Lengkap / NIM
                    </label>
                    <input type="text" class="form-control" name="nama_lengkap"
                           value="<?= htmlspecialchars($this->session->userdata('name') ?: '') ?>"
                           <?= $this->session->userdata('name') ? 'readonly style="background-color: #f1f5f9; cursor: not-allowed; color: #64748b; font-weight: 600; border-color: #e2e8f0;"' : 'placeholder="Masukkan nama peminjam"' ?>
                           required>
                </div>

                <!-- Kategori Ruangan (Row 1 Col 2) -->
                <div class="form-group">
                    <label class="form-label">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                        Kategori Ruangan
                    </label>
                    <select class="form-control" id="kategoriSelectPublic" name="id_kategori" required>
                        <option value="">Pilih Kategori</option>
                        <?php if(!empty($kategori)): ?>
                            <?php foreach($kategori as $k): ?>
                                <option value="<?= $k->id ?>"><?= htmlspecialchars($k->nama_kategori) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Pilih Ruangan (Row 2 Col 1) -->
                <div class="form-group">
                    <label class="form-label">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        Pilih Ruangan
                    </label>
                    <select class="form-control" id="ruanganSelectPublic" name="id_ruangan" required>
                        <option value="">Pilih Kategori Dahulu</option>
                    </select>
                </div>

                <!-- Tanggal Peminjaman (Row 2 Col 2) -->
                <div class="form-group">
                    <label class="form-label">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        Tanggal Peminjaman
                    </label>
                    <input type="text" class="form-control" name="tanggal_peminjaman"
                           id="tanggalPeminjaman" placeholder="Pilih Tanggal..." required>
                </div>

                <!-- Keterangan / Keperluan Peminjaman (Full Width Row 3) -->
                <div class="form-group full-width">
                    <label class="form-label">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                        Keterangan / Keperluan Peminjaman
                    </label>
                    <textarea class="form-control" name="keterangan" rows="3"
                              placeholder="Tuliskan keterangan atau keperluan peminjaman ruangan (contoh: Praktikum Mata Kuliah Pemrograman Web / Ujian Sidang TA)..."
                              required></textarea>
                </div>

                <!-- Waktu Peminjaman + Interactive Radial Clock Picker (Row 4 Full Width) -->
                <div class="form-group full-width" id="timeSelectionGroup" style="display: none;">
                    <label class="form-label">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        Waktu Peminjaman
                    </label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 4px;">
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 4px; display: block;">JAM MULAI</label>
                            <input type="text" class="form-control" name="jam_mulai" id="inputJamMulai"
                                   placeholder="-- : --" readonly style="cursor: pointer; background: #fff;"
                                   onclick="openInlinePicker('mulai')" required>
                        </div>
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 4px; display: block;">JAM SELESAI</label>
                            <input type="text" class="form-control" name="jam_selesai" id="inputJamSelesai"
                                   placeholder="-- : --" readonly style="cursor: pointer; background: #fff;"
                                   onclick="openInlinePicker('selesai')" required>
                        </div>
                    </div>

                    <!-- Inline Radial Clock Picker Panel -->
                    <div id="inlineClockPanel" style="display:none; margin-top: 16px; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 18px; padding: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.03);">
                        <div style="display: flex; gap: 20px; align-items: flex-start; flex-wrap: wrap;">

                            <!-- Kiri: Display Waktu & Quick Drag Slots -->
                            <div style="flex: 1.1; min-width: 250px; background: #ffffff; border-radius: 14px; padding: 20px; border: 1px solid #f1f5f9; display: flex; flex-direction: column; align-items: center;">
                                <div id="inlineTpLabel" style="font-size: 0.72rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">WAKTU DIPALIKAN</div>
                                <div style="font-size: 2.8rem; font-weight: 800; color: #1e293b; line-height: 1; margin-bottom: 8px;">
                                    <span id="tpDisplayHour" onclick="setMode('hour')" style="cursor:pointer;">14</span><span style="color:#cbd5e1; margin:0 2px;">:</span><span id="tpDisplayMinute" onclick="setMode('minute')" style="cursor:pointer; color:#94a3b8;">30</span>
                                </div>
                                <div style="display:inline-block; background:#ede9fe; color:#7c3aed; font-size:0.72rem; font-weight:700; border-radius:20px; padding:3px 12px; margin-bottom:16px;">24 Jam</div>

                                <div style="font-size: 0.78rem; color: #7c3aed; font-weight: 700; margin-bottom: 8px; width:100%; text-align:left; display:flex; align-items:center; gap:6px;">
                                    <span>⚡ Slot Waktu Cepat</span>
                                    <span style="font-size:0.68rem; color:#94a3b8; font-weight:500;">(drag untuk rentang)</span>
                                </div>
                                <div id="tpTimeSlots" style="display:grid; grid-template-columns:1fr 1fr; gap:6px; user-select:none; width:100%;">
                                    <div class="tp-slot" data-start="08:00" data-end="09:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">08:00 – 09:00</div>
                                    <div class="tp-slot" data-start="09:00" data-end="10:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">09:00 – 10:00</div>
                                    <div class="tp-slot" data-start="10:00" data-end="11:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">10:00 – 11:00</div>
                                    <div class="tp-slot" data-start="11:00" data-end="12:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">11:00 – 12:00</div>
                                    <div class="tp-slot" data-start="12:00" data-end="13:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">12:00 – 13:00</div>
                                    <div class="tp-slot" data-start="13:00" data-end="14:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">13:00 – 14:00</div>
                                    <div class="tp-slot" data-start="14:00" data-end="15:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">14:00 – 15:00</div>
                                    <div class="tp-slot" data-start="15:00" data-end="16:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">15:00 – 16:00</div>
                                    <div class="tp-slot" data-start="16:00" data-end="17:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">16:00 – 17:00</div>
                                    <div class="tp-slot" data-start="17:00" data-end="18:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">17:00 – 18:00</div>
                                </div>
                            </div>

                            <!-- Kanan: Radial Analog Clock (Fix Dimensions: 240px x 240px) -->
                            <div style="flex: 1.2; min-width: 260px; display:flex; flex-direction:column; align-items:center; background:#ffffff; border-radius:14px; padding:20px; border:1px solid #f1f5f9;">
                                <div class="tp-tab-wrap">
                                    <div id="tpTabHour" class="active" onclick="setMode('hour')">🕐 Jam</div>
                                    <div id="tpTabMinute" onclick="setMode('minute')">⏱ Menit</div>
                                </div>
                                <div id="tpClockContainer" style="position:relative; width:240px; height:240px; border-radius:50%; background:#f8fafc; border:2px solid #e2e8f0; box-shadow:inset 0 2px 6px rgba(0,0,0,0.03); flex-shrink:0; margin:0 auto;">
                                    <div id="tpClockHand" style="position:absolute; bottom:50%; left:50%; width:2px; height:95px; background:#7c3aed; border-radius:2px; transform-origin:bottom center; transform:translateX(-50%) rotate(0deg); transition:transform 0.15s ease; z-index:5;"></div>
                                    <div style="position:absolute; top:50%; left:50%; width:10px; height:10px; background:#7c3aed; border-radius:50%; transform:translate(-50%,-50%); z-index:10;"></div>
                                    <div id="tpClockNumbers"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:16px; padding-top:14px; border-top:1px solid #e2e8f0;">
                            <button type="button" onclick="closeInlinePicker()" style="padding:9px 20px; border-radius:10px; border:1.5px solid #e2e8f0; background:#fff; color:#64748b; font-size:0.88rem; font-weight:700; cursor:pointer;">Batal</button>
                            <button type="button" onclick="applyInlinePicker()" style="padding:9px 24px; border-radius:10px; border:none; background:#7c3aed; color:#fff; font-size:0.88rem; font-weight:700; cursor:pointer; box-shadow:0 4px 12px rgba(124,58,237,0.3);">✔ Terapkan</button>
                        </div>
                    </div>
                </div>

            </div><!-- /form-grid -->

            <!-- Submit Button (Row 5 Full Width) -->
            <div style="margin-top: 24px;">
                <button type="submit" id="btnSubmitPage" class="btn-submit-booking">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                    Kirim Pengajuan Peminjaman
                </button>
            </div>
        </form>
    </div>

    <!-- Timepicker Core Logic -->
    <script src="<?= base_url('assets/js/timepicker.js?v=' . filemtime(FCPATH . 'assets/js/timepicker.js')) ?>"></script>

    <script>
        var inlinePickerTarget = 'mulai';

        function openInlinePicker(target) {
            inlinePickerTarget = target;
            document.getElementById('inlineTpLabel').innerText = (target === 'mulai') ? 'PILIH JAM MULAI' : 'PILIH JAM SELESAI';

            var existingVal = (target === 'mulai')
                ? document.getElementById('inputJamMulai').value
                : document.getElementById('inputJamSelesai').value;

            if (existingVal && existingVal.includes(':')) {
                var parts = existingVal.split(':');
                selectedHour   = parseInt(parts[0]) || 14;
                selectedMinute = parseInt(parts[1]) || 0;
            } else {
                selectedHour = 14;
                selectedMinute = 0;
            }

            isSelectingHour = true;
            document.getElementById('tpTabHour').classList.add('active');
            document.getElementById('tpTabMinute').classList.remove('active');

            updateDisplay();
            document.getElementById('inlineClockPanel').style.display = 'block';

            setTimeout(function() {
                document.getElementById('inlineClockPanel').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }, 50);
        }

        function closeInlinePicker() {
            document.getElementById('inlineClockPanel').style.display = 'none';
        }

        function applyInlinePicker() {
            var hh = selectedHour.toString().padStart(2, '0');
            var mm = selectedMinute.toString().padStart(2, '0');
            var timeStr = hh + ':' + mm;
            if (inlinePickerTarget === 'mulai') {
                document.getElementById('inputJamMulai').value = timeStr;
            } else {
                document.getElementById('inputJamSelesai').value = timeStr;
            }
            closeInlinePicker();
        }

        function submitBooking(e) {
            e.preventDefault();
            var jMulai   = document.getElementById('inputJamMulai').value;
            var jSelesai = document.getElementById('inputJamSelesai').value;

            if (!jMulai || !jSelesai) {
                Swal.fire({ title: 'Waktu Belum Dipilih', text: 'Silakan pilih jam mulai dan jam selesai!', icon: 'warning', confirmButtonColor: '#ea580c' });
                return;
            }
            if (jMulai >= jSelesai) {
                Swal.fire({ title: 'Jam Tidak Valid', text: 'Jam mulai harus lebih awal dari jam selesai!', icon: 'warning', confirmButtonColor: '#ea580c' });
                return;
            }

            var $btn = $('#btnSubmitPage');
            $btn.prop('disabled', true).text('Memproses Pengajuan...');

            $.ajax({
                url: "<?= base_url('dashboard/ajukan_booking') ?>",
                type: "POST",
                data: $('#formAjukanBooking').serialize(),
                dataType: "json",
                success: function(data) {
                    if (data.status === 'success') {
                        Swal.fire({ title: 'Berhasil!', text: data.message, icon: 'success', confirmButtonColor: '#ea580c', confirmButtonText: 'Lihat Kalender' })
                        .then(function() { window.location.href = "<?= base_url('kalender') ?>"; });
                    } else {
                        Swal.fire({ title: 'Pengajuan Gagal', text: data.message, icon: 'error', confirmButtonColor: '#ea580c' });
                        $btn.prop('disabled', false).html('<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg> Kirim Pengajuan Peminjaman');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Terjadi kesalahan pada server', 'error');
                    $btn.prop('disabled', false).html('<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg> Kirim Pengajuan Peminjaman');
                }
            });
        }

        $(document).ready(function() {
            // Inisialisasi Select2 untuk Kategori dan Ruangan
            $('#kategoriSelectPublic').select2({
                minimumResultsForSearch: -1,
                width: '100%',
                placeholder: 'Pilih Kategori'
            });

            $('#ruanganSelectPublic').select2({
                minimumResultsForSearch: -1,
                width: '100%',
                placeholder: 'Pilih Ruangan'
            });

            var fpMode = "<?= ($this->session->userdata('role_id') == 1) ? 'range' : 'single' ?>";
            flatpickr("#tanggalPeminjaman", {
                mode: fpMode,
                dateFormat: "Y-m-d",
                minDate: "today",
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length > 0) {
                        $('#timeSelectionGroup').slideDown();
                    } else {
                        $('#timeSelectionGroup').slideUp();
                        closeInlinePicker();
                    }
                }
            });

            // Dependent Dropdown Ajax + Select2 Trigger
            $('#kategoriSelectPublic').on('change', function() {
                var id_kategori = $(this).val();
                if (id_kategori != '') {
                    $.ajax({
                        url: "<?= base_url('kelolabooking/get_ruangan') ?>",
                        type: "POST",
                        data: {id_kategori: id_kategori},
                        dataType: "json",
                        success: function(data) {
                            var html = '';
                            if (data.length === 1) {
                                var room = data[0];
                                var roomLabel = room.kode_ruangan ? (room.nama_ruangan + ' (Ruang: ' + room.kode_ruangan + ')') : room.nama_ruangan;
                                html = '<option value="' + room.id + '" selected>' + roomLabel + '</option>';
                            } else {
                                html = '<option value="">Pilih Fasilitas / Ruangan</option>';
                                $.each(data, function(i, room) {
                                    var roomLabel = room.kode_ruangan ? (room.nama_ruangan + ' (Ruang: ' + room.kode_ruangan + ')') : room.nama_ruangan;
                                    html += '<option value="' + room.id + '">' + roomLabel + '</option>';
                                });
                            }
                            $('#ruanganSelectPublic').html(html).trigger('change');
                        }
                    });
                } else {
                    $('#ruanganSelectPublic').html('<option value="">Pilih Kategori Dahulu</option>').trigger('change');
                }
            });
        });
    </script>

</body>
</html>