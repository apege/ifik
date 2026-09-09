<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <!-- Google Fonts & SweetAlert2 -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .swal2-container {
            z-index: 99999 !important;
        }

        :root {

            --bg-color: #fbf7f1; 
            --text-color: #1e293b;
            --border-color: rgba(30, 41, 59, 0.1);
            --primary: #ea580c;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .btn-primary {
            background-color: var(--primary);
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.4);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(4px);
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: #fff;
            width: 100%;
            max-width: 600px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            max-height: 90vh;
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.1rem;
            color: rgba(30, 41, 59, 0.7);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: rgba(30, 41, 59, 0.5);
        }

        .modal-body {
            padding: 24px;
            overflow-y: auto;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-label {
            font-size: 0.75rem;
            color: rgba(30, 41, 59, 0.5);
            position: absolute;
            top: 10px;
            left: 16px;
            z-index: 1;
        }

        .form-control {
            width: 100%;
            padding: 28px 16px 10px;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            font-family: inherit;
            font-size: 0.95rem;
            color: var(--text-color);
            background: #f8fafc;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary);
            background: #fff;
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        /* Time slot grid */
        .time-slots-container {
            margin-top: 10px;
        }

        .time-slots-label {
            font-size: 0.85rem;
            color: rgba(30, 41, 59, 0.5);
            margin-bottom: 10px;
        }

        .time-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        .time-slot {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 10px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            background: #f8fafc;
            font-size: 0.75rem;
            color: rgba(30, 41, 59, 0.6);
        }

        .time-slot.active {
            border-color: var(--primary);
            background: rgba(234, 88, 12, 0.05);
            color: var(--primary);
        }

        .time-slot input[type="checkbox"] {
            display: none;
        }

        /* Checkbox pseudo-element */
        .custom-checkbox {
            width: 14px;
            height: 14px;
            border: 1px solid var(--border-color);
            border-radius: 3px;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #fff;
        }

        .time-slot.active .custom-checkbox {
            background: var(--primary);
            border-color: var(--primary);
        }

        .time-slot.active .custom-checkbox::after {
            content: "✓";
            color: #fff;
            font-size: 10px;
        }

        .modal-footer {
            padding: 20px 24px;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
        }
        
        /* Dashboard Table */
        .table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .table th, .table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        .table th {
            background: #f8fafc;
            font-size: 0.85rem;
            color: rgba(30, 41, 59, 0.6);
            text-transform: uppercase;
        }
        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-pending { background: #fef08a; color: #854d0e; }
        .badge-approved { background: #dcfce7; color: #166534; }
        
        /* Action Buttons */
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-approve { background: #dcfce7; color: #166534; }
        .btn-approve:hover { background: #bbf7d0; }
        .btn-reject { background: #fee2e2; color: #991b1b; }
        .btn-reject:hover { background: #fecaca; }
        .btn-delete { background: #f1f5f9; color: #64748b; }
        .btn-delete:hover { background: #e2e8f0; color: #0f172a; }
        
        /* Custom Flatpickr Theme */
        .flatpickr-calendar { font-family: 'Plus Jakarta Sans', sans-serif !important; border: 1px solid var(--border-color) !important; border-radius: 12px !important; box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important; }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, .flatpickr-day.selected.inRange, .flatpickr-day.startRange.inRange, .flatpickr-day.endRange.inRange, .flatpickr-day.selected:focus, .flatpickr-day.startRange:focus, .flatpickr-day.endRange:focus, .flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, .flatpickr-day.endRange:hover, .flatpickr-day.selected.prevMonthDay, .flatpickr-day.startRange.prevMonthDay, .flatpickr-day.endRange.prevMonthDay, .flatpickr-day.selected.nextMonthDay, .flatpickr-day.startRange.nextMonthDay, .flatpickr-day.endRange.nextMonthDay { background: var(--primary) !important; border-color: var(--primary) !important; }
    </style>
    <link rel="stylesheet" href="<?= base_url('assets/css/timepicker.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>

    <div class="header" style="display: flex; align-items: center; justify-content: space-between;">
        <h1>Dashboard Peminjaman</h1>
        <div style="display: flex; gap: 10px;">
            <?php if ($this->session->userdata('role_id') == 1): ?>
                <button class="btn-primary" onclick="openTambahRuanganModalAdmin()" style="background: #0f172a;">
                    🏢 + Tambah Ruangan
                </button>
            <?php endif; ?>
            <button class="btn-primary" onclick="openAdminBookingModal()">
                + Buat Peminjaman
            </button>
        </div>
    </div>

    <!-- Data Table -->
    <table class="table">
        <thead>
            <tr>
                <th>Nama Lengkap</th>
                <th>Ruangan</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($peminjaman as $p): ?>
            <tr>
                <td><?= $p->nama_lengkap ?></td>
                <td>
                    <div style="font-weight: 700; color: #0f172a;"><?= htmlspecialchars($p->nama_ruangan ?? '') ?></div>
                    <?php if (!empty($p->kode_ruangan)): ?>
                        <div style="font-size: 0.75rem; color: #ea580c; font-weight: 600; margin-top: 2px;">Ruang: <?= htmlspecialchars($p->kode_ruangan) ?></div>
                    <?php endif; ?>
                </td>
                <td>
                    <?php 
                        if ($p->tanggal_mulai == $p->tanggal_selesai) {
                            echo date('d M Y', strtotime($p->tanggal_mulai));
                        } else {
                            echo date('d M', strtotime($p->tanggal_mulai)) . ' - ' . date('d M Y', strtotime($p->tanggal_selesai));
                        }
                    ?> 
                    <br>
                    <small style="color: #64748b;"><?= substr($p->jam_mulai, 0, 5) ?> - <?= substr($p->jam_selesai, 0, 5) ?></small>
                </td>
                <td><?= $p->keterangan ?></td>
                <td>
                    <?php
                        $s = $p->status;
                        if ($s === 'Pending') {
                            $dot = '#f59e0b'; $bg = '#fffbeb'; $color = '#b45309'; $label = 'Menunggu Persetujuan';
                        } elseif (strpos($s, 'Ka. Ur') !== false) {
                            $dot = '#22c55e'; $bg = '#f0fdf4'; $color = '#166534'; $label = 'Disetujui Ka. Ur';
                        } elseif (strpos($s, 'Laboran') !== false) {
                            $dot = '#3b82f6'; $bg = '#eff6ff'; $color = '#1d4ed8'; $label = 'Disetujui Laboran';
                        } elseif (strpos($s, 'Admin') !== false) {
                            $dot = '#8b5cf6'; $bg = '#f5f3ff'; $color = '#6d28d9'; $label = 'Disetujui Admin';
                        } elseif (strpos($s, 'Disetujui') !== false) {
                            $dot = '#22c55e'; $bg = '#f0fdf4'; $color = '#166534'; $label = 'Disetujui';
                        } elseif ($s === 'Ditolak') {
                            $dot = '#ef4444'; $bg = '#fef2f2'; $color = '#991b1b'; $label = 'Ditolak';
                        } elseif ($s === 'Selesai') {
                            $dot = '#94a3b8'; $bg = '#f8fafc'; $color = '#475569'; $label = 'Selesai';
                        } else {
                            $dot = '#94a3b8'; $bg = '#f8fafc'; $color = '#475569'; $label = htmlspecialchars($s);
                        }
                    ?>
                    <span style="display:inline-flex;align-items:center;gap:5px;background:<?= $bg ?>;color:<?= $color ?>;border-radius:999px;padding:4px 11px;font-size:0.72rem;font-weight:700;white-space:nowrap;letter-spacing:0.01em;">
                        <span style="width:6px;height:6px;border-radius:50%;background:<?= $dot ?>;flex-shrink:0;"></span>
                        <?= $label ?>
                    </span>

                </td>
                <td>
                    <div style="display: flex; gap: 8px;">
                        <?php if($p->status == 'Pending'): ?>
                            <button class="btn-action btn-approve" onclick="approveBooking(<?= $p->id ?>)" title="Setujui">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </button>
                            <button class="btn-action btn-reject" onclick="openRejectModal(<?= $p->id ?>)" title="Tolak">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                        <?php endif; ?>
                        <button class="btn-action btn-delete" onclick="deleteBooking(<?= $p->id ?>)" title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Modal Form -->
    <div class="modal-overlay" id="bookingModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Buat Peminjaman</h2>
                <button class="modal-close" onclick="closeAdminBookingModal()">&times;</button>

            </div>
            <div class="modal-body">
                <form id="formBooking">
                    
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama_lengkap" value="<?= $this->session->userdata('name') ?>" readonly style="background-color: #e2e8f0; cursor: not-allowed; color: #64748b;" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kategori Ruangan</label>
                        <select class="form-control" id="kategoriSelect" name="id_kategori" required>
                            <option value="">Pilih Kategori</option>
                            <?php foreach($kategori as $k): ?>
                                <option value="<?= $k->id ?>"><?= $k->nama_kategori ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Ruangan</label>
                        <select class="form-control" id="ruanganSelect" name="id_ruangan" required>
                            <option value="">Pilih Ruangan</option>
                            <!-- Diisi via AJAX -->
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" required></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Peminjaman</label>
                        <input type="text" class="form-control" name="tanggal_peminjaman" id="tanggalPeminjaman" placeholder="Pilih Rentang Tanggal" required>
                    </div>

                    <div class="form-group" id="timeSelectionGroup" style="display: none;">
                        <label class="form-label">Waktu Peminjaman</label>
                        <div style="display: flex; gap: 15px; margin-top: 25px;">
                            <input type="text" class="form-control" name="jam_mulai" id="inputJamMulai" placeholder="Jam Mulai" readonly style="cursor: pointer;" onclick="openInlinePicker('mulai')" required>
                            <input type="text" class="form-control" name="jam_selesai" id="inputJamSelesai" placeholder="Jam Selesai" readonly style="cursor: pointer;" onclick="openInlinePicker('selesai')" required>
                        </div>

                        <!-- Inline Clock Picker (di dalam modal) -->
                        <div id="inlineClockPanel" style="display:none; margin-top: 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 16px;">
                            <div style="display: flex; gap: 16px; align-items: flex-start; flex-wrap: wrap;">

                                <!-- Kiri: Display waktu & Pilih Cepat -->
                                <div style="flex: 1; min-width: 160px;">
                                    <div id="inlineTpLabel" style="font-size: 0.72rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Waktu dipilih</div>
                                    <div style="font-size: 2.5rem; font-weight: 800; color: #1e293b; line-height: 1; margin-bottom: 6px;">
                                        <span id="tpDisplayHour" onclick="setMode('hour')" style="cursor:pointer;">14</span><span style="color:#e2e8f0;">:</span><span id="tpDisplayMinute" onclick="setMode('minute')" style="cursor:pointer; color:#cbd5e1;">30</span>
                                    </div>
                                    <div style="display:inline-block; background:#ede9fe; color:#7c3aed; font-size:0.7rem; font-weight:600; border-radius:20px; padding:2px 10px; margin-bottom:12px;">24 Jam</div>

                                    <div style="font-size: 0.72rem; color: #7c3aed; font-weight: 700; margin-bottom: 6px;">⚡ Pilih Cepat <span style="font-size:0.65rem;color:#94a3b8;font-weight:500;">(drag untuk rentang)</span></div>
                                    <div id="tpTimeSlots" style="display:grid; grid-template-columns:1fr 1fr; gap:6px; user-select:none; margin-top:4px;">
                                        <div class="tp-slot" data-start="08:00" data-end="09:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.06);">08:00 – 09:00</div>
                                        <div class="tp-slot" data-start="09:00" data-end="10:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.06);">09:00 – 10:00</div>
                                        <div class="tp-slot" data-start="10:00" data-end="11:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.06);">10:00 – 11:00</div>
                                        <div class="tp-slot" data-start="11:00" data-end="12:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.06);">11:00 – 12:00</div>
                                        <div class="tp-slot" data-start="12:00" data-end="13:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.06);">12:00 – 13:00</div>
                                        <div class="tp-slot" data-start="13:00" data-end="14:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.06);">13:00 – 14:00</div>
                                        <div class="tp-slot" data-start="14:00" data-end="15:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.06);">14:00 – 15:00</div>
                                        <div class="tp-slot" data-start="15:00" data-end="16:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.06);">15:00 – 16:00</div>
                                        <div class="tp-slot" data-start="16:00" data-end="17:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.06);">16:00 – 17:00</div>
                                        <div class="tp-slot" data-start="17:00" data-end="18:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.06);">17:00 – 18:00</div>
                                    </div>
                                </div>

                                <!-- Kanan: Clock -->
                                <div style="flex: 1; min-width: 200px;">
                                    <div class="tp-tabs" style="margin-bottom: 12px;">
                                        <div class="tp-tab active" id="tpTabHour" onclick="setMode('hour')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> Jam
                                        </div>
                                        <div class="tp-tab" id="tpTabMinute" onclick="setMode('minute')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle></svg> Menit
                                        </div>
                                    </div>
                                    <div class="tp-clock-container" id="tpClockContainer">
                                        <div class="tp-clock-center"></div>
                                        <div class="tp-clock-hand" id="tpClockHand"></div>
                                        <div id="tpClockNumbers"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:14px; border-top: 1px solid #e2e8f0; padding-top:12px;">
                                <button type="button" onclick="closeInlinePicker()" style="padding: 8px 18px; border-radius: 8px; border: 1px solid #e2e8f0; background:#fff; color:#64748b; font-size:0.85rem; cursor:pointer; font-weight:600;">Batal</button>
                                <button type="button" onclick="applyInlinePicker()" style="padding: 8px 18px; border-radius: 8px; border: none; background: #7c3aed; color:#fff; font-size:0.85rem; cursor:pointer; font-weight:600;">✔ Terapkan</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-primary" onclick="submitForm()">Simpan Peminjaman</button>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal-overlay" id="rejectModal">
        <div class="modal">
            <div class="modal-header">
                <h2>Alasan Penolakan</h2>
                <button class="modal-close" onclick="closeRejectModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Tuliskan alasan penolakan</label>
                    <textarea class="form-control" id="alasanPenolakan" placeholder="Ruangan sedang direnovasi..."></textarea>
                    <input type="hidden" id="rejectBookingId">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-primary" style="background: #ef4444;" onclick="submitReject()">Tolak Peminjaman</button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="<?= base_url('assets/js/timepicker.js?v=' . filemtime(FCPATH . 'assets/js/timepicker.js')) ?>"></script>
    <script>
        let currentTarget = '';

        function openInlinePicker(target) {
            currentTarget = target;
            document.getElementById('inlineClockPanel').style.display = 'block';
            document.getElementById('inlineTpLabel').innerText = (target === 'mulai') ? 'Waktu Mulai' : 'Waktu Selesai';
        }

        function closeInlinePicker() {
            document.getElementById('inlineClockPanel').style.display = 'none';
        }

        function applyInlinePicker() {
            let time = document.getElementById('tpDisplayHour').innerText.padStart(2, '0') + ':' + document.getElementById('tpDisplayMinute').innerText.padStart(2, '0');
            if (currentTarget === 'mulai') {
                document.getElementById('inputJamMulai').value = time;
            } else {
                document.getElementById('inputJamSelesai').value = time;
            }
            closeInlinePicker();
        }

        $(document).ready(function() {
            // Initialize Flatpickr for date range/single
            var fpMode = "<?= ($this->session->userdata('role_id') == 1) ? 'range' : 'single' ?>";
            
            flatpickr("#tanggalPeminjaman", {
                mode: fpMode,
                dateFormat: "Y-m-d",
                minDate: "today",
                onChange: function(selectedDates, dateStr, instance) {
                    // Jika range tanggal sudah terisi (baik beda hari atau hari yang sama)
                    if (selectedDates.length > 0) {
                        $('#timeSelectionGroup').slideDown();
                        
                        // Set the date display in time picker modal
                        let displayStr = selectedDates[0].toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'});
                        if(selectedDates.length === 2 && selectedDates[0].getTime() !== selectedDates[1].getTime()) {
                            displayStr += " - " + selectedDates[1].toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'});
                        }
                        $('#tpDateDisplay').text(displayStr);
                        
                    } else {
                        $('#timeSelectionGroup').slideUp();
                    }
                }
            });

            // Dependent Dropdown for Ruangan
            $('#kategoriSelect').change(function() {
                let id_kategori = $(this).val();
                if(id_kategori != '') {
                    $.ajax({
                        url: "<?= base_url('kelolabooking/get_ruangan') ?>",
                        method: "POST",
                        data: {id_kategori: id_kategori},
                        dataType: "json",
                        success: function(data) {
                            if(data.length === 1) {
                                // Hanya ada 1 ruangan, otomatis pilih dan beri style disabled
                                let room = data[0];
                                let roomLabel = room.kode_ruangan ? `${room.nama_ruangan} (Ruang: ${room.kode_ruangan})` : room.nama_ruangan;
                                let html = `<option value="${room.id}" selected>${roomLabel}</option>`;
                                $('#ruanganSelect').html(html);
                                $('#ruanganSelect').css({'background-color': '#e2e8f0', 'pointer-events': 'none', 'color': '#64748b'});
                            } else {
                                // Lebih dari 1 ruangan, munculkan dropdown normal
                                let html = '<option value="">Pilih Ruangan</option>';
                                $.each(data, function(i, room) {
                                    let roomLabel = room.kode_ruangan ? `${room.nama_ruangan} (Ruang: ${room.kode_ruangan})` : room.nama_ruangan;
                                    html += `<option value="${room.id}">${roomLabel}</option>`;
                                });
                                $('#ruanganSelect').html(html);
                                $('#ruanganSelect').css({'background-color': '#f8fafc', 'pointer-events': 'auto', 'color': 'var(--text-color)'});
                            }
                        }
                    });
                } else {
                    $('#ruanganSelect').html('<option value="">Pilih Kategori Dahulu</option>');
                    $('#ruanganSelect').css({'background-color': '#f8fafc', 'pointer-events': 'auto', 'color': 'var(--text-color)'});
                }
            });
        });

        function resetAdminBookingForm() {
            const form = document.getElementById('formBooking');
            if (form) form.reset();
            const roomSelect = $('#ruanganSelect');
            if (roomSelect.length) {
                roomSelect.html('<option value="">Pilih Ruangan</option>');
                roomSelect.css({'background-color': '#f8fafc', 'pointer-events': 'auto', 'color': 'var(--text-color)'});
            }
            $('#timeSelectionGroup').hide();
            if (document.getElementById('tanggalPeminjaman')) document.getElementById('tanggalPeminjaman').value = '';
            if (document.getElementById('inputJamMulai')) document.getElementById('inputJamMulai').value = '';
            if (document.getElementById('inputJamSelesai')) document.getElementById('inputJamSelesai').value = '';
            if (typeof closeInlinePicker === 'function') closeInlinePicker();
        }

        function openAdminBookingModal() {
            resetAdminBookingForm();
            document.getElementById('bookingModal').classList.add('active');
        }

        function closeAdminBookingModal() {
            document.getElementById('bookingModal').classList.remove('active');
            resetAdminBookingForm();
        }

        // Submit form via AJAX

        function submitForm() {
            const form = document.getElementById('formBooking');
            if(!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            
            $.ajax({
                url: '<?= base_url('kelolabooking/submit_booking') ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if(res.status == 'success') {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: res.message,
                            icon: 'success',
                            confirmButtonColor: '#ea580c'
                        }).then(() => window.location.reload());
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Terjadi kesalahan pada server', 'error');
                }
            });
        }

        // Aksi Approve
        function approveBooking(id) {
            Swal.fire({
                title: 'Setujui Peminjaman',
                text: 'Apakah Anda yakin ingin menyetujui peminjaman ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if(result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('kelolabooking/approve/') ?>' + id,
                        type: 'POST',
                        success: function(response) {
                            const res = JSON.parse(response);
                            if(res.status === 'success') {
                                window.location.reload();
                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }
                        }
                    });
                }
            });
        }

        // Aksi Reject
        function openRejectModal(id) {
            document.getElementById('rejectBookingId').value = id;
            document.getElementById('alasanPenolakan').value = '';
            document.getElementById('rejectModal').classList.add('active');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.remove('active');
        }

        function submitReject() {
            const id = document.getElementById('rejectBookingId').value;
            const alasan = document.getElementById('alasanPenolakan').value;
            
            $.ajax({
                url: '<?= base_url('kelolabooking/reject/') ?>' + id,
                type: 'POST',
                data: { alasan_penolakan: alasan },
                success: function(response) {
                    const res = JSON.parse(response);
                    if(res.status === 'success') {
                        window.location.reload();
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                }
            });
        }

        // Aksi Delete
        function deleteBooking(id) {
            Swal.fire({
                title: 'Hapus Peminjaman',
                text: 'Data akan dihapus permanen. Apakah Anda yakin?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if(result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('kelolabooking/delete/') ?>' + id,
                        type: 'POST',
                        success: function(response) {
                            const res = JSON.parse(response);
                            if(res.status === 'success') {
                                window.location.reload();
                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }
                        }
                    });
                }
            });
        }

        // Modal Tambah Ruangan Admin
        function openTambahRuanganModalAdmin() {
            document.getElementById('modalTambahRuanganAdmin').classList.add('active');
        }

        function closeTambahRuanganModalAdmin() {
            document.getElementById('modalTambahRuanganAdmin').classList.remove('active');
        }

        function handleTambahRuanganSubmitAdmin(e) {
            e.preventDefault();
            const form = document.getElementById('formTambahRuanganAdmin');
            const formData = new FormData(form);

            fetch('<?= base_url("dashboard/tambah_ruangan") ?>', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('Berhasil!', data.message, 'success').then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            })
            .catch(err => {
                Swal.fire('Error', 'Terjadi kesalahan koneksi server.', 'error');
            });
        }
    </script>

    <!-- Modal Popup Form Tambah Ruangan Khusus Admin -->
    <div id="modalTambahRuanganAdmin" class="modal-backdrop" onclick="if(event.target===this)closeTambahRuanganModalAdmin()">
        <div class="modal-card" style="max-width: 480px; padding: 0; overflow: hidden;">
            <div style="padding: 18px 24px; background: #0f172a; color: #fff; display: flex; align-items: center; justify-content: space-between;">
                <h3 style="margin: 0; font-size: 1.05rem; font-weight: 800;">🏢 Tambah Ruangan / Lab Baru</h3>
                <button type="button" onclick="closeTambahRuanganModalAdmin()" style="background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            <form id="formTambahRuanganAdmin" onsubmit="handleTambahRuanganSubmitAdmin(event)" style="padding: 20px 24px;">
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    <div class="form-group">
                        <label style="font-size: 0.75rem; font-weight: 700; color: #334155; text-transform: uppercase;">Nama Ruangan / Lab *</label>
                        <input type="text" name="nama_ruangan" placeholder="Contoh: Lab AR/VR &amp; Metaverse" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label style="font-size: 0.75rem; font-weight: 700; color: #334155; text-transform: uppercase;">Ruangan Fisik (Nomor LK) *</label>
                        <input type="text" name="kode_ruangan" placeholder="Contoh: LK.01.01, LK.01.02" required class="form-control">
                        <small style="font-size: 0.72rem; color: #64748b; margin-top: 4px; display: block;">Dapat diisi lebih dari satu ruangan, pisahkan dengan koma.</small>
                    </div>
                    <div class="form-group">
                        <label style="font-size: 0.75rem; font-weight: 700; color: #334155; text-transform: uppercase;">Kategori Ruangan *</label>
                        <select name="id_kategori" required class="form-control">
                            <option value="1">Laboratorium Komputer</option>
                            <option value="2">Laboratorium Desain</option>
                            <option value="3">Ruang Rapat &amp; Seminar</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="font-size: 0.75rem; font-weight: 700; color: #334155; text-transform: uppercase;">Kapasitas (Orang)</label>
                        <input type="number" name="kapasitas" value="35" min="1" class="form-control">
                    </div>
                    <div class="form-group">
                        <label style="font-size: 0.75rem; font-weight: 700; color: #334155; text-transform: uppercase;">Lokasi Ruangan</label>
                        <input type="text" name="lokasi" value="Gedung Sebatik (FIK)" class="form-control">
                    </div>
                    <div class="form-group">
                        <label style="font-size: 0.75rem; font-weight: 700; color: #334155; text-transform: uppercase;">Status Ketersediaan</label>
                        <select name="status" class="form-control">
                            <option value="Tersedia">Tersedia</option>
                            <option value="Tidak Tersedia">Tidak Tersedia</option>
                            <option value="Perbaikan">Perbaikan</option>
                        </select>
                    </div>
                </div>
                <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" onclick="closeTambahRuanganModalAdmin()" style="padding: 8px 18px; border-radius: 8px; border: 1px solid #cbd5e1; background: #fff; color: #64748b; font-weight: 700; cursor: pointer;">Batal</button>
                    <button type="submit" style="padding: 8px 22px; border-radius: 8px; border: none; background: #ea580c; color: #fff; font-weight: 700; cursor: pointer;">Simpan Ruangan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Global Custom Circle Cursor -->
    <?php $this->load->view('partials/custom_cursor'); ?>
</body>
</html>
