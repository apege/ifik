<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Kelola Data Ruangan — Admin FIK' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root {
            --primary: #ea580c;
            --primary-hover: #c2410c;
            --bg-color: #fcfbf9;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            padding: 40px 60px;
        }

        /* Top Header Navigation & Title */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .btn-back-dashboard {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 999px;
            background: #ffffff;
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            font-size: 0.82rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.04);
        }

        .btn-back-dashboard:hover {
            color: var(--primary);
            border-color: var(--primary);
            transform: translateX(-3px);
        }

        .page-title {
            font-size: 1.65rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            background: var(--primary);
            color: #ffffff;
            font-size: 0.88rem;
            font-weight: 700;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 15px rgba(234, 88, 12, 0.35);
            text-decoration: none;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(234, 88, 12, 0.45);
        }

        /* Stat Cards Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 35px;
        }

        .stat-card {
            background: #ffffff;
            padding: 22px 24px;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .stat-label {
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: #0f172a;
        }

        /* Filter Controls */
        .filter-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 16px;
        }

        .search-box {
            position: relative;
            flex: 1;
            max-width: 380px;
        }

        .search-input {
            width: 100%;
            height: 42px;
            padding: 0 16px 0 40px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            background: #ffffff;
            font-size: 0.88rem;
            color: #0f172a;
            outline: none;
            transition: border-color 0.2s;
        }

        .search-input:focus {
            border-color: var(--primary);
        }

        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            fill: #94a3b8;
        }

        /* Main Data Table Card */
        .table-card {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid var(--border-color);
            box-shadow: 0 8px 30px rgba(0,0,0,0.04);
            overflow: hidden;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .table th {
            background: #f8fafc;
            padding: 16px 24px;
            font-size: 0.75rem;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border-bottom: 1px solid var(--border-color);
        }

        .table td {
            padding: 18px 24px;
            font-size: 0.88rem;
            color: #1e293b;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .table tbody tr:hover {
            background: #fcfcfd;
        }

        /* Badges & Media */
        .code-badge {
            display: inline-block;
            padding: 4px 10px;
            background: #0f172a;
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 800;
            border-radius: 6px;
            letter-spacing: 0.5px;
        }

        .room-thumbnail {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            object-fit: cover;
            border: 1.5px solid var(--border-color);
            background: #f1f5f9;
        }

        .file-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 700;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .file-pill.has-file {
            background: #ffedd5;
            color: #c2410c;
            border-color: #fdba74;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .status-tersedia { background: #f0fdf4; color: #166534; }
        .status-tersedia .dot { background: #22c55e; }

        .status-tidaksedia { background: #fef2f2; color: #991b1b; }
        .status-tidaksedia .dot { background: #ef4444; }

        .status-perbaikan { background: #fffbeb; color: #b45309; }
        .status-perbaikan .dot { background: #f59e0b; }

        .status-badge .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
        }

        /* Action Buttons */
        .btn-action-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-edit { background: #eff6ff; color: #1d4ed8; }
        .btn-edit:hover { background: #dbeafe; }

        .btn-delete { background: #fef2f2; color: #991b1b; }
        .btn-delete:hover { background: #fee2e2; }

        /* Ensure SweetAlert2 appears in front of modal overlays */
        .swal2-container {
            z-index: 99999999 !important;
        }

        /* Modal Overlay */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(6px);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px 12px;
            overflow-y: auto;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-card {
            width: 100%;
            max-width: 680px;
            max-height: 88vh;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.25);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            margin: auto;
        }

        .modal-card form {
            display: flex;
            flex-direction: column;
            flex: 1;
            overflow: hidden;
            min-height: 0;
        }

        .modal-header {
            padding: 20px 26px;
            background: #0f172a;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .modal-header h3 {
            font-size: 1.1rem;
            font-weight: 800;
        }

        .modal-close {
            background: none; border: none;
            color: #94a3b8; font-size: 1.5rem;
            cursor: pointer;
        }

        .modal-body {
            padding: 24px;
            overflow-y: auto;
            overflow-x: hidden;
            flex: 1;
            min-height: 0;
            display: flex;
            flex-direction: column;
            gap: 18px;
            overscroll-behavior: contain;
            -webkit-overflow-scrolling: touch;
            width: 100%;
            box-sizing: border-box;
        }

        .modal-footer {
            padding: 16px 24px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            flex-shrink: 0;
        }

        .day-select-pill {
            padding: 6px 14px;
            border-radius: 20px;
            border: 1.5px solid #cbd5e1;
            background: #ffffff;
            color: #64748b;
            font-size: 0.75rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.25, 1, 0.5, 1);
            user-select: none;
        }

        .day-select-pill:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }

        .day-select-pill.active {
            background: #ea580c;
            color: #ffffff;
            border-color: #c2410c;
            box-shadow: 0 4px 10px rgba(234, 88, 12, 0.35);
        }

        .time-pill-btn {
            padding: 5px 12px;
            border-radius: 8px;
            border: 1.5px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.25, 1, 0.5, 1);
            user-select: none;
        }

        .time-pill-btn:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }

        .time-pill-btn.active {
            background: #0f172a;
            color: #ffffff;
            border-color: #0f172a;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.25);
        }

        /* Custom HTML Dropdown Component (Zero OS Surface, 100% Custom Cursor) */
        .custom-dropdown {
            position: relative;
            user-select: none;
        }
        .custom-dropdown-btn {
            width: 100%;
            padding: 8px 10px;
            border-radius: 10px;
            border: 1.5px solid #cbd5e1;
            background: #ffffff;
            color: #1e293b;
            font-size: 0.8rem;
            font-weight: 800;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: none !important;
        }
        .custom-dropdown-menu {
            position: absolute;
            top: 108%;
            left: 0;
            width: 100%;
            max-height: 160px;
            overflow-y: auto;
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            z-index: 999999;
            display: none;
            overscroll-behavior: contain;
        }
        .custom-dropdown-menu.show {
            display: block;
        }
        .custom-dropdown-item {
            padding: 6px 12px;
            font-size: 0.8rem;
            font-weight: 700;
            color: #334155;
            cursor: none !important;
            transition: all 0.15s ease;
        }
        .custom-dropdown-item:hover, .custom-dropdown-item.active {
            background: #ea580c;
            color: #ffffff;
        }

        .form-grid-2 {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 14px;
            width: 100%;
            box-sizing: border-box;
        }

        .form-grid-2 > div {
            min-width: 0;
            max-width: 100%;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group label {
            font-size: 0.78rem;
            font-weight: 800;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input, .form-textarea {
            width: 100%;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1.5px solid #cbd5e1;
            font-size: 0.88rem;
            color: #0f172a;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-input:focus, .form-textarea:focus {
            border-color: var(--primary);
        }

        .form-textarea {
            min-height: 80px;
            resize: vertical;
        }

        .upload-field-box {
            background: #f8fafc;
            border: 1.5px dashed #cbd5e1;
            border-radius: 12px;
            padding: 14px 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .upload-field-box span.hint {
            font-size: 0.75rem;
            color: #64748b;
        }

        /* Modern Drag & Drop Dropzone Styles */
        .upload-dropzone {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 18px 14px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            cursor: pointer;
            transition: all 0.25s ease;
            min-height: 115px;
        }

        .upload-dropzone:hover {
            border-color: #ea580c;
            background: #fffaf5;
        }

        .upload-dropzone.dragover {
            border-color: #ea580c;
            background: #fff7ed;
            transform: scale(1.01);
            box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.12);
        }

        .upload-dropzone input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            z-index: 5;
        }

        .dropzone-icon {
            font-size: 1.8rem;
            margin-bottom: 4px;
            transition: transform 0.2s ease;
        }

        .upload-dropzone:hover .dropzone-icon {
            transform: translateY(-2px);
        }

        .dropzone-title {
            font-size: 0.82rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 2px;
        }

        .dropzone-sub {
            font-size: 0.72rem;
            color: #64748b;
        }

        .dropzone-sub span {
            color: #ea580c;
            font-weight: 700;
            text-decoration: underline;
        }

        .dropzone-preview {
            width: 100%;
            box-sizing: border-box;
            min-width: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            background: #ffffff;
            padding: 8px 12px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            margin-top: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
            position: relative;
            z-index: 10;
        }

        .dropzone-preview img {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #cbd5e1;
            flex-shrink: 0;
        }

        .dropzone-preview .preview-info {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            text-align: left;
            flex: 1;
            min-width: 0;
        }

        .dropzone-preview .preview-name {
            font-size: 0.76rem;
            font-weight: 700;
            color: #1e293b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
            width: 100%;
        }

        .dropzone-preview .preview-size {
            font-size: 0.68rem;
            color: #64748b;
        }

        .dropzone-preview .btn-remove-file {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #fee2e2;
            color: #ef4444;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            font-weight: 800;
            line-height: 1;
            transition: all 0.2s ease;
            flex-shrink: 0;
            padding: 0;
        }

        .dropzone-preview .btn-remove-file:hover {
            background: #ef4444;
            color: #ffffff;
            transform: scale(1.1);
        }

        .modal-footer {
            padding: 16px 26px 20px 26px;
            background: #f8fafc;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .btn-cancel {
            padding: 10px 20px;
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #64748b;
            font-weight: 700;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <!-- Header Page -->
    <div class="page-header">
        <div class="header-left">
            <a href="<?= base_url('dashboard') ?>" class="btn-back-dashboard">
                ← Kembali ke Portal
            </a>
            <h1 class="page-title">🏢 Kelola Data Ruangan &amp; Lab</h1>
        </div>
        <div class="header-actions">
            <a href="<?= base_url('kelolabooking') ?>" class="btn-back-dashboard" style="background:#f1f5f9; color:#0f172a; border:none;">
                📅 Kelola Booking Peminjaman
            </a>
            <button class="btn-primary" onclick="openModalTambah()">
                + Tambah Ruangan Baru
            </button>
        </div>
    </div>

    <!-- Stat Cards Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-label">Total Ruangan / Lab</span>
            <span class="stat-value"><?= count($ruangan) ?></span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Lab Komputer</span>
            <span class="stat-value">
                <?= count(array_filter($ruangan, function($r){ return $r->id_kategori == 1; })) ?>
            </span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Lab Desain &amp; Studio</span>
            <span class="stat-value">
                <?= count(array_filter($ruangan, function($r){ return $r->id_kategori == 2; })) ?>
            </span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Ruang Rapat &amp; Auditorium</span>
            <span class="stat-value">
                <?= count(array_filter($ruangan, function($r){ return $r->id_kategori == 3; })) ?>
            </span>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <div class="search-box">
            <svg class="search-icon" viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
            <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Cari kode atau nama ruangan..." class="search-input">
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="table-card">
        <table class="table" id="ruanganTable">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Kode &amp; Nama Ruangan</th>
                    <th>Kategori &amp; Tagline</th>
                    <th>3D Model</th>
                    <th>Fasilitas &amp; Jam</th>
                    <th>Status</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($ruangan)): ?>
                    <?php foreach ($ruangan as $r): ?>
                        <tr>
                            <td>
                                <?php if (isset($r->foto) && !empty($r->foto)): ?>
                                    <img src="<?= base_url($r->foto) ?>" alt="Foto" class="room-thumbnail">
                                <?php else: ?>
                                    <div class="room-thumbnail" style="display:flex;align-items:center;justify-content:center;font-size:1.2rem;">🖼️</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="code-badge"><?= htmlspecialchars(isset($r->kode_ruangan) ? $r->kode_ruangan : '') ?></span>
                                <div style="font-weight: 800; font-size: 0.95rem; margin-top: 4px;"><?= htmlspecialchars(isset($r->nama_ruangan) ? $r->nama_ruangan : '') ?></div>
                                <div style="font-size: 0.75rem; color: #64748b;">📍 <?= htmlspecialchars(isset($r->lokasi) ? $r->lokasi : '') ?></div>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: #ea580c; font-size: 0.8rem;"><?= htmlspecialchars(isset($r->nama_kategori) && !empty($r->nama_kategori) ? $r->nama_kategori : 'Umum') ?></div>
                                <div style="font-size: 0.78rem; color: #475569;"><?= htmlspecialchars(isset($r->tagline) && !empty($r->tagline) ? $r->tagline : '-') ?></div>
                            </td>
                            <td>
                                <?php if (isset($r->model_3d) && !empty($r->model_3d)): ?>
                                    <a href="<?= base_url($r->model_3d) ?>" target="_blank" class="file-pill has-file" title="Klik untuk mengunduh/melihat berkas 3D" style="text-decoration:none;">
                                        🧊 <?= strtoupper(pathinfo($r->model_3d, PATHINFO_EXTENSION)) ?> File
                                    </a>
                                <?php else: ?>
                                    <span class="file-pill">Tidak Ada 3D</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-size: 0.8rem; font-weight: 700;">💻 <?= htmlspecialchars(isset($r->jumlah_unit) && !empty($r->jumlah_unit) ? $r->jumlah_unit : (isset($r->kapasitas) ? $r->kapasitas . ' Orang' : '30 Orang')) ?></div>
                                <div style="font-size: 0.75rem; color: #64748b;">⏰ <?= htmlspecialchars(isset($r->jam_operasional) && !empty($r->jam_operasional) ? $r->jam_operasional : '08:00 - 17:00 WIB') ?></div>
                            </td>
                            <td>
                                <?php
                                    $st = strtolower(isset($r->status) ? $r->status : 'tersedia');
                                    if ($st === 'tersedia') {
                                        $badgeClass = 'status-tersedia';
                                    } elseif ($st === 'perbaikan') {
                                        $badgeClass = 'status-perbaikan';
                                    } else {
                                        $badgeClass = 'status-tidaksedia';
                                    }
                                ?>
                                <span class="status-badge <?= $badgeClass ?>">
                                    <span class="dot"></span>
                                    <?= htmlspecialchars(isset($r->status) ? $r->status : 'Tersedia') ?>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div class="btn-action-group" style="justify-content: flex-end;">
                                    <button class="btn-action btn-edit" title="Edit Ruangan" onclick='openModalEdit(<?= json_encode($r) ?>)'>
                                        ✏️
                                    </button>
                                    <button class="btn-action btn-delete" title="Hapus Ruangan" onclick="confirmDeleteRuangan(<?= $r->id ?>, '<?= htmlspecialchars($r->nama_ruangan) ?>')">
                                        🗑️
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">
                            Belum ada data ruangan yang tersimpan.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Form Tambah / Edit Ruangan (Multi-field lengkap sesuai detail page) -->
    <div id="modalRuangan" class="modal-overlay" onclick="if(event.target===this)closeModalRuangan()">
        <div class="modal-card">
            <div class="modal-header">
                <h3 id="modalTitle">🏢 Tambah Ruangan / Lab Baru</h3>
                <button type="button" class="modal-close" onclick="closeModalRuangan()">&times;</button>
            </div>
            <form id="formRuangan" onsubmit="handleFormSubmit(event)" enctype="multipart/form-data">
                <input type="hidden" id="ruanganId" name="id">
                <div class="modal-body">
                    
                    <!-- Section 1: Data Utama Ruangan -->
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>🏢 Nama Ruangan / Lab *</label>
                            <input type="text" id="inputNama" name="nama_ruangan" placeholder="Contoh: Lab Tablet Cintiq" required class="form-input">
                        </div>
                        <div class="form-group">
                            <label>🔑 Kode Ruangan *</label>
                            <input type="text" id="inputKode" name="kode_ruangan" placeholder="Contoh: FIK-205" required class="form-input">
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>🏷️ Kategori Ruangan *</label>
                            <select id="inputKategori" name="id_kategori" required class="form-input">
                                <?php foreach ($kategori as $k): ?>
                                    <option value="<?= $k->id ?>"><?= htmlspecialchars($k->nama_kategori) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>🟢 Status Ketersediaan</label>
                            <select id="inputStatus" name="status" class="form-input">
                                <option value="Tersedia">Tersedia</option>
                                <option value="Tidak Tersedia">Tidak Tersedia</option>
                                <option value="Perbaikan">Perbaikan</option>
                            </select>
                        </div>
                    </div>

                    <!-- Section 2: Upload Files (Foto & 3D Model .glb/.fbx) with Drag & Drop -->
                    <div class="form-grid-2">
                        <!-- Foto Utama Dropzone -->
                        <div>
                            <label style="font-size:0.78rem; font-weight:800; color:#334155; text-transform:uppercase; display:block; margin-bottom:6px;">📷 Foto Utama Ruangan</label>
                            <div class="upload-dropzone" id="dropzoneFoto">
                                <input type="file" id="inputFoto" name="foto" accept="image/png, image/jpeg, image/webp">
                                <div class="dropzone-icon">🖼️</div>
                                <div class="dropzone-title">Tarik &amp; lepas foto ke sini</div>
                                <div class="dropzone-sub">atau <span>pilih file</span> dari komputer</div>
                                <span class="hint" style="margin-top:4px;">JPG, PNG, WEBP (Maks 5MB)</span>
                            </div>
                            <div id="previewFotoBox" class="dropzone-preview" style="display:none;">
                                <img id="previewFotoImg" src="" alt="Preview Foto">
                                <div class="preview-info">
                                    <span id="previewFotoText" class="preview-name">foto.jpg</span>
                                    <span id="previewFotoSize" class="preview-size">File Siap</span>
                                </div>
                                <button type="button" class="btn-remove-file" onclick="clearFileFoto(event)" title="Hapus berkas foto">&times;</button>
                            </div>
                        </div>

                        <!-- 3D Model Dropzone -->
                        <div>
                            <label style="font-size:0.78rem; font-weight:800; color:#ea580c; text-transform:uppercase; display:block; margin-bottom:6px;">🧊 3D Model Ruangan (.GLB / .FBX)</label>
                            <div class="upload-dropzone" id="dropzoneModel3D">
                                <input type="file" id="inputModel3D" name="model_3d" accept=".glb, .fbx, .gltf, .obj">
                                <div class="dropzone-icon">🧊</div>
                                <div class="dropzone-title">Tarik &amp; lepas file 3D ke sini</div>
                                <div class="dropzone-sub">atau <span>pilih file</span> dari komputer</div>
                                <span class="hint" style="margin-top:4px;">.GLB, .FBX, .GLTF, .OBJ (Maks 50MB)</span>
                            </div>
                            <div id="previewModelBox" class="dropzone-preview" style="display:none;">
                                <div style="width:42px; height:42px; border-radius:8px; background:#fff7ed; border:1px solid #ffedd5; display:flex; align-items:center; justify-content:center; font-size:1.3rem; flex-shrink:0;">🧊</div>
                                <div class="preview-info">
                                    <span id="previewModelText" class="preview-name">model.glb</span>
                                    <span id="previewModelSize" class="preview-size">Model 3D Siap</span>
                                </div>
                                <button type="button" class="btn-remove-file" onclick="clearFileModel(event)" title="Hapus berkas 3D">&times;</button>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Tagline & Perangkat -->
                    <div class="form-group">
                        <label>📌 Sub-Judul / Tagline Deskripsi</label>
                        <input type="text" id="inputTagline" name="tagline" placeholder="Contoh: Studio Digital Illustration, Concept Art, Komik, & 2D Animation" class="form-input">
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>💻 Jumlah Unit / Peralatan Utama</label>
                            <input type="text" id="inputJumlahUnit" name="jumlah_unit" placeholder="Contoh: 30 Unit Cintiq Pro 24" class="form-input">
                        </div>
                        <div class="form-group">
                            <label>📍 Detail Lokasi &amp; Gedung</label>
                            <input type="text" id="inputLokasi" name="lokasi" placeholder="Contoh: Gedung Industri Kreatif - Lantai 2 (FIK-205)" class="form-input">
                        </div>
                    </div>

                    <!-- Section 3b: Interactive Operational Hours Builder & Kapasitas -->
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>👥 Kapasitas Orang</label>
                            <input type="number" id="inputKapasitas" name="kapasitas" value="35" min="1" class="form-input">
                        </div>
                    </div>

                    <div class="form-group">
                        <label style="display:flex; justify-content:space-between; align-items:center;">
                            <span>⏰ Jam Operasional &amp; Hari Kerja (Klik / Blok Hari)</span>
                            <span style="font-size:0.7rem; color:#64748b;">Klik hari untuk mengaktifkan / menonaktifkan</span>
                        </label>
                        
                        <!-- Interactive 7-Day Clickable Multi-Select Pills -->
                        <div style="display:flex; gap:6px; flex-wrap:wrap; align-items:center; margin-top:6px; margin-bottom:10px;">
                            <button type="button" class="day-select-pill active" data-day="Senin" onclick="toggleDayPill(this)">Senin</button>
                            <button type="button" class="day-select-pill active" data-day="Selasa" onclick="toggleDayPill(this)">Selasa</button>
                            <button type="button" class="day-select-pill active" data-day="Rabu" onclick="toggleDayPill(this)">Rabu</button>
                            <button type="button" class="day-select-pill active" data-day="Kamis" onclick="toggleDayPill(this)">Kamis</button>
                            <button type="button" class="day-select-pill active" data-day="Jumat" onclick="toggleDayPill(this)">Jumat</button>
                            <button type="button" class="day-select-pill" data-day="Sabtu" onclick="toggleDayPill(this)">Sabtu</button>
                            <button type="button" class="day-select-pill" data-day="Minggu" onclick="toggleDayPill(this)">Minggu</button>
                            <div style="margin-left:auto; display:flex; gap:4px;">
                                <button type="button" style="font-size:0.7rem; font-weight:700; padding:4px 10px; border-radius:12px; border:1px solid #cbd5e1; background:#fff; color:#475569; cursor:pointer;" onclick="selectPresetDays(['Senin','Selasa','Rabu','Kamis','Jumat'])">5 Hari Kerja</button>
                                <button type="button" style="font-size:0.7rem; font-weight:700; padding:4px 10px; border-radius:12px; border:1px solid #cbd5e1; background:#fff; color:#475569; cursor:pointer;" onclick="selectPresetDays(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'])">Semua Hari</button>
                            </div>
                        </div>

                        <!-- Custom HTML Dropdown Time Selectors & Output Display -->
                        <div style="display:grid; grid-template-columns: 1fr 1fr 2fr; gap:12px; align-items:center; background:#f8fafc; padding:12px; border-radius:14px; border:1px solid #e2e8f0; margin-top:6px;">
                            <!-- Jam Buka Custom Select -->
                            <div>
                                <span style="font-size:0.7rem; font-weight:700; color:#64748b; display:block; margin-bottom:4px;">Jam Buka</span>
                                <div style="display:flex; gap:4px; align-items:center;">
                                    <!-- Jam Hour -->
                                    <div class="custom-dropdown" style="flex:1;">
                                        <div class="custom-dropdown-btn" onclick="toggleCustomDropdown('ddJamBukaHour')">
                                            <span id="txtJamBukaHour">08</span> ▾
                                        </div>
                                        <div class="custom-dropdown-menu" id="ddJamBukaHour">
                                            <?php for($i=6; $i<=22; $i++): $h=sprintf("%02d", $i); ?>
                                                <div class="custom-dropdown-item <?= $h=='08'?'active':'' ?>" onclick="selectCustomItem('BukaHour', '<?= $h ?>', this)"><?= $h ?></div>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <span style="font-weight:800; color:#64748b;">:</span>
                                    <!-- Jam Minute -->
                                    <div class="custom-dropdown" style="flex:1;">
                                        <div class="custom-dropdown-btn" onclick="toggleCustomDropdown('ddJamBukaMin')">
                                            <span id="txtJamBukaMin">00</span> ▾
                                        </div>
                                        <div class="custom-dropdown-menu" id="ddJamBukaMin">
                                            <?php foreach(['00','15','30','45'] as $m): ?>
                                                <div class="custom-dropdown-item <?= $m=='00'?'active':'' ?>" onclick="selectCustomItem('BukaMin', '<?= $m ?>', this)"><?= $m ?></div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Jam Tutup Custom Select -->
                            <div>
                                <span style="font-size:0.7rem; font-weight:700; color:#64748b; display:block; margin-bottom:4px;">Jam Tutup</span>
                                <div style="display:flex; gap:4px; align-items:center;">
                                    <!-- Jam Hour -->
                                    <div class="custom-dropdown" style="flex:1;">
                                        <div class="custom-dropdown-btn" onclick="toggleCustomDropdown('ddJamTutupHour')">
                                            <span id="txtJamTutupHour">17</span> ▾
                                        </div>
                                        <div class="custom-dropdown-menu" id="ddJamTutupHour">
                                            <?php for($i=6; $i<=23; $i++): $h=sprintf("%02d", $i); ?>
                                                <div class="custom-dropdown-item <?= $h=='17'?'active':'' ?>" onclick="selectCustomItem('TutupHour', '<?= $h ?>', this)"><?= $h ?></div>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <span style="font-weight:800; color:#64748b;">:</span>
                                    <!-- Jam Minute -->
                                    <div class="custom-dropdown" style="flex:1;">
                                        <div class="custom-dropdown-btn" onclick="toggleCustomDropdown('ddJamTutupMin')">
                                            <span id="txtJamTutupMin">00</span> ▾
                                        </div>
                                        <div class="custom-dropdown-menu" id="ddJamTutupMin">
                                            <?php foreach(['00','15','30','45'] as $m): ?>
                                                <div class="custom-dropdown-item <?= $m=='00'?'active':'' ?>" onclick="selectCustomItem('TutupMin', '<?= $m ?>', this)"><?= $m ?></div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Output Text -->
                            <div>
                                <span style="font-size:0.7rem; font-weight:700; color:#64748b; display:block; margin-bottom:4px;">Hasil Text (Tersimpan di DB)</span>
                                <input type="text" id="inputJamOperasional" name="jam_operasional" value="Senin – Jumat | 08:00 – 17:00 WIB" class="form-input" style="background:#fff; font-weight:700; color:#ea580c; padding:8px 10px; font-size:0.8rem;">
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Deskripsi Lengkap -->
                    <div class="form-group">
                        <label>📝 Deskripsi Ruangan / Lab</label>
                        <textarea id="inputDeskripsi" name="deskripsi" placeholder="Tuliskan deskripsi lengkap peruntukan lab untuk mahasiswa..." class="form-textarea"></textarea>
                    </div>

                    <!-- Section 5: Fasilitas & Spesifikasi Perangkat -->
                    <div class="form-group">
                        <label>🛠️ Fasilitas &amp; Spesifikasi Perangkat (Multi-Item)</label>
                        <textarea id="inputSpesifikasi" name="spesifikasi_fasilitas" placeholder="Contoh:&#10;- Wacom Cintiq Pro 24 (Pen Display 4K Ultra HD)&#10;- Wacom Pro Pen 2 (Stylus pen 8192 pressure level)&#10;- Software Drawing (Clip Studio Paint EX, Adobe Photoshop CC)" class="form-textarea" style="min-height:100px;"></textarea>
                    </div>

                    <!-- Section 6: Tata Tertib & Ketentuan -->
                    <div class="form-group">
                        <label>⚠️ Tata Tertib &amp; Ketentuan Pengguna</label>
                        <textarea id="inputTataTertib" name="tata_tertib" placeholder="Contoh:&#10;- Simpan Stylus Pen dan Kabel di tempat semula setelah bekerja.&#10;- Gunakan kain micro-fiber khusus saat membersihkan layar.&#10;- Dilarang menggunakan benda tajam." class="form-textarea" style="min-height:90px;"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModalRuangan()" class="btn-cancel">Batal</button>
                    <button type="submit" class="btn-primary" style="padding: 10px 24px;">Simpan Ruangan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let isEditMode = false;
        const allDaysOrdered = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        function toggleDayPill(pill) {
            pill.classList.toggle('active');
            updateJamOperasionalText();
        }

        function selectPresetDays(daysArray) {
            document.querySelectorAll('.day-select-pill').forEach(pill => {
                const day = pill.getAttribute('data-day');
                if (daysArray.includes(day)) {
                    pill.classList.add('active');
                } else {
                    pill.classList.remove('active');
                }
            });
            updateJamOperasionalText();
        }

        function getFormattedSelectedDays() {
            const activePills = Array.from(document.querySelectorAll('.day-select-pill.active')).map(p => p.getAttribute('data-day'));
            if (activePills.length === 0) return 'Senin – Jumat';
            if (activePills.length === 7) return 'Setiap Hari';

            const selected = allDaysOrdered.filter(d => activePills.includes(d));
            
            if (selected.length === 5 && selected[0] === 'Senin' && selected[4] === 'Jumat') {
                return 'Senin – Jumat';
            }
            if (selected.length === 6 && selected[0] === 'Senin' && selected[5] === 'Sabtu') {
                return 'Senin – Sabtu';
            }
            if (selected.length === 2 && selected[0] === 'Sabtu' && selected[1] === 'Minggu') {
                return 'Sabtu & Minggu';
            }
            
            const indices = selected.map(d => allDaysOrdered.indexOf(d));
            let isContiguous = true;
            for (let i = 1; i < indices.length; i++) {
                if (indices[i] !== indices[i - 1] + 1) {
                    isContiguous = false;
                    break;
                }
            }

            if (isContiguous && selected.length > 2) {
                return `${selected[0]} – ${selected[selected.length - 1]}`;
            }

            if (selected.length === 2) {
                return `${selected[0]} & ${selected[1]}`;
            }

            return selected.join(', ');
        }

        function toggleCustomDropdown(menuId) {
            const target = document.getElementById(menuId);
            const isOpen = target.classList.contains('show');
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.remove('show'));
            if (!isOpen) target.classList.add('show');
        }

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.custom-dropdown')) {
                document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.remove('show'));
            }
        });

        function selectCustomItem(type, val, el) {
            document.getElementById('txtJam' + type).innerText = val;
            el.parentElement.querySelectorAll('.custom-dropdown-item').forEach(i => i.classList.remove('active'));
            el.classList.add('active');
            el.parentElement.classList.remove('show');
            updateJamOperasionalText();
        }

        function updateJamOperasionalText() {
            const formattedDays = getFormattedSelectedDays();
            const startH = document.getElementById('txtJamBukaHour').innerText;
            const startM = document.getElementById('txtJamBukaMin').innerText;
            const endH = document.getElementById('txtJamTutupHour').innerText;
            const endM = document.getElementById('txtJamTutupMin').innerText;
            document.getElementById('inputJamOperasional').value = `${formattedDays} | ${startH}:${startM} – ${endH}:${endM} WIB`;
        }

        function formatFileSize(bytes) {
            if (!bytes || bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function clearFileFoto(e) {
            if (e) { e.preventDefault(); e.stopPropagation(); }
            const input = document.getElementById('inputFoto');
            if (input) input.value = '';
            const previewBox = document.getElementById('previewFotoBox');
            if (previewBox) previewBox.style.display = 'none';
            const previewImg = document.getElementById('previewFotoImg');
            if (previewImg) previewImg.src = '';
        }

        function clearFileModel(e) {
            if (e) { e.preventDefault(); e.stopPropagation(); }
            const input = document.getElementById('inputModel3D');
            if (input) input.value = '';
            const previewBox = document.getElementById('previewModelBox');
            if (previewBox) previewBox.style.display = 'none';
        }

        function handleFotoFile(file) {
            if (!file) return;
            const maxMb = 5;
            if (file.size > maxMb * 1024 * 1024) {
                Swal.fire('File Terlalu Besar', `Ukuran foto (${formatFileSize(file.size)}) melebihi batas maksimum ${maxMb}MB.`, 'warning');
                clearFileFoto();
                return;
            }

            const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
            const ext = file.name.split('.').pop().toLowerCase();
            if (!validTypes.includes(file.type) && !['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
                Swal.fire('Format Tidak Didukung', 'Harap unggah file foto dengan format JPG, PNG, atau WEBP.', 'warning');
                clearFileFoto();
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewFotoImg').src = e.target.result;
                document.getElementById('previewFotoText').innerText = file.name;
                document.getElementById('previewFotoSize').innerText = formatFileSize(file.size);
                document.getElementById('previewFotoBox').style.display = 'flex';
            };
            reader.readAsDataURL(file);
        }

        function handleModelFile(file) {
            if (!file) return;
            const maxMb = 50;
            if (file.size > maxMb * 1024 * 1024) {
                Swal.fire('File Terlalu Besar', `Ukuran berkas 3D (${formatFileSize(file.size)}) melebihi batas maksimum ${maxMb}MB.`, 'warning');
                clearFileModel();
                return;
            }

            const ext = file.name.split('.').pop().toLowerCase();
            const validExts = ['glb', 'fbx', 'gltf', 'obj'];
            if (!validExts.includes(ext)) {
                Swal.fire('Format Tidak Didukung', 'Harap unggah berkas 3D dengan format .GLB, .FBX, .GLTF, atau .OBJ.', 'warning');
                clearFileModel();
                return;
            }

            document.getElementById('previewModelText').innerText = file.name;
            document.getElementById('previewModelSize').innerText = `${ext.toUpperCase()} • ${formatFileSize(file.size)}`;
            document.getElementById('previewModelBox').style.display = 'flex';
        }

        function initDropzoneEvents() {
            const dropFoto = document.getElementById('dropzoneFoto');
            const inputFoto = document.getElementById('inputFoto');
            const dropModel = document.getElementById('dropzoneModel3D');
            const inputModel = document.getElementById('inputModel3D');

            if (dropFoto && inputFoto) {
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropFoto.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropFoto.classList.add('dragover');
                    });
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropFoto.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropFoto.classList.remove('dragover');
                    });
                });

                dropFoto.addEventListener('drop', (e) => {
                    if (e.dataTransfer && e.dataTransfer.files.length > 0) {
                        inputFoto.files = e.dataTransfer.files;
                        handleFotoFile(inputFoto.files[0]);
                    }
                });

                inputFoto.addEventListener('change', () => {
                    if (inputFoto.files && inputFoto.files.length > 0) {
                        handleFotoFile(inputFoto.files[0]);
                    }
                });
            }

            if (dropModel && inputModel) {
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropModel.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropModel.classList.add('dragover');
                    });
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropModel.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropModel.classList.remove('dragover');
                    });
                });

                dropModel.addEventListener('drop', (e) => {
                    if (e.dataTransfer && e.dataTransfer.files.length > 0) {
                        inputModel.files = e.dataTransfer.files;
                        handleModelFile(inputModel.files[0]);
                    }
                });

                inputModel.addEventListener('change', () => {
                    if (inputModel.files && inputModel.files.length > 0) {
                        handleModelFile(inputModel.files[0]);
                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', initDropzoneEvents);

        function openModalTambah() {
            isEditMode = false;
            document.body.style.overflow = 'hidden';
            document.getElementById('modalTitle').innerText = '🏢 Tambah Ruangan / Lab Baru';
            document.getElementById('formRuangan').reset();
            document.getElementById('ruanganId').value = '';
            document.getElementById('inputJamOperasional').value = 'Senin – Jumat | 08:00 – 17:00 WIB';
            document.getElementById('inputLokasi').value = 'Gedung Industri Kreatif (FIK)';
            document.getElementById('inputKapasitas').value = '35';
            clearFileFoto();
            clearFileModel();
            document.getElementById('modalRuangan').classList.add('active');
        }

        function openModalEdit(data) {
            isEditMode = true;
            document.body.style.overflow = 'hidden';
            document.getElementById('modalTitle').innerText = '✏️ Edit Data Ruangan & Berkas';
            document.getElementById('ruanganId').value = data.id;
            document.getElementById('inputNama').value = data.nama_ruangan || '';
            document.getElementById('inputKode').value = data.kode_ruangan || '';
            document.getElementById('inputKategori').value = data.id_kategori || 1;
            document.getElementById('inputStatus').value = data.status || 'Tersedia';
            document.getElementById('inputTagline').value = data.tagline || '';
            document.getElementById('inputJumlahUnit').value = data.jumlah_unit || '';
            document.getElementById('inputLokasi').value = data.lokasi || '';
            document.getElementById('inputKapasitas').value = data.kapasitas || 30;
            document.getElementById('inputJamOperasional').value = data.jam_operasional || 'Senin – Jumat | 08:00 – 17:00 WIB';
            document.getElementById('inputDeskripsi').value = data.deskripsi || '';
            document.getElementById('inputSpesifikasi').value = data.spesifikasi_fasilitas || '';
            document.getElementById('inputTataTertib').value = data.tata_tertib || '';

            // Render existing Foto indicator
            if (data.foto) {
                const fotoPath = data.foto.startsWith('http') ? data.foto : `<?= base_url() ?>${data.foto}`;
                document.getElementById('previewFotoBox').style.display = 'flex';
                document.getElementById('previewFotoImg').src = fotoPath;
                document.getElementById('previewFotoText').innerText = data.foto.split('/').pop();
                const fotoSizeEl = document.getElementById('previewFotoSize');
                if (fotoSizeEl) fotoSizeEl.innerText = 'Foto Terpasang';
            } else {
                clearFileFoto();
            }

            // Render existing 3D Model indicator
            if (data.model_3d) {
                const ext = data.model_3d.split('.').pop().toUpperCase();
                document.getElementById('previewModelBox').style.display = 'flex';
                document.getElementById('previewModelText').innerText = data.model_3d.split('/').pop();
                const modelSizeEl = document.getElementById('previewModelSize');
                if (modelSizeEl) modelSizeEl.innerText = `Berkas 3D (${ext}) Terpasang`;
            } else {
                clearFileModel();
            }

            document.getElementById('modalRuangan').classList.add('active');
        }

        function closeModalRuangan() {
            document.body.style.overflow = '';
            document.getElementById('modalRuangan').classList.remove('active');
        }

        function handleFormSubmit(e) {
            e.preventDefault();
            const form = document.getElementById('formRuangan');
            const formData = new FormData(form);
            const id = document.getElementById('ruanganId').value;
            const targetUrl = isEditMode ? `<?= base_url('kelolaruangan/update/') ?>${id}` : '<?= base_url('kelolaruangan/tambah') ?>';

            Swal.fire({
                title: 'Menyimpan Data...',
                text: 'Harap tunggu, berkas foto & 3D model sedang diunggah.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(targetUrl, {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.status === 'success') {
                        Swal.fire('Berhasil!', data.message, 'success').then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                } catch(e) {
                    console.error('Raw Server Output:', text);
                    Swal.fire('Error Server', 'Respon server tidak valid: ' + text.substring(0, 200), 'error');
                }
            })
            .catch(err => {
                Swal.fire('Error', 'Gagal terhubung ke server.', 'error');
            });
        }

        function confirmDeleteRuangan(id, name) {
            Swal.fire({
                title: 'Hapus Ruangan?',
                text: `Apakah Anda yakin ingin menghapus ruangan "${name}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`<?= base_url('kelolaruangan/delete/') ?>${id}`, { method: 'POST' })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire('Terhapus!', data.message, 'success').then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', data.message, 'error');
                        }
                    });
                }
            });
        }

        function filterTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('ruanganTable');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                let show = false;
                const tdCode = tr[i].getElementsByTagName('td')[1];
                const tdName = tr[i].getElementsByTagName('td')[2];
                if (tdCode || tdName) {
                    const codeTxt = tdCode ? tdCode.textContent || tdCode.innerText : '';
                    const nameTxt = tdName ? tdName.textContent || tdName.innerText : '';
                    if (codeTxt.toLowerCase().indexOf(filter) > -1 || nameTxt.toLowerCase().indexOf(filter) > -1) {
                        show = true;
                    }
                }
                tr[i].style.display = show ? '' : 'none';
            }
        }
    </script>

    <!-- Global Custom Circle Cursor -->
    <?php $this->load->view('partials/custom_cursor'); ?>
</body>
</html>
