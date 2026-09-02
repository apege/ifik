<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($title) ? $title : 'Pengaturan Portal & Kelola Ruangan — Admin FIK' ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- TinyMCE — Self-hosted via cdnjs -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
          colors: { brand: '#ea580c', brandHover: '#c2410c' }
        }
      }
    }
  </script>
  <style>
    :root {
      --primary: #ea580c;
      --primary-hover: #c2410c;
      --bg-color: #fbf7f1;
      --text-main: #0f172a;
      --text-muted: #64748b;
      --border-color: #e2e8f0;
    }

    body {
      background-color: var(--bg-color);
      color: #1e293b;
      font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .glass-card {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(16px);
      border: 1px solid rgba(234, 88, 12, 0.15);
      box-shadow: 0 10px 30px rgba(0,0,0,0.04);
      border-radius: 20px;
    }

    .btn-brand {
      background: #ea580c;
      color: #fff;
      transition: all 0.25s ease;
    }
    .btn-brand:hover {
      background: #c2410c;
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(234, 88, 12, 0.35);
    }
    .btn-secondary {
        background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;
        transition: all 0.3s;
    }
    .btn-secondary:hover { background: #e2e8f0; color: #1e293b; }
    .form-input {
      width: 100%;
      padding: 11px 16px;
      border-radius: 10px;
      border: 1px solid #cbd5e1;
      background: #fff;
      transition: all 0.2s;
      outline: none;
    }
    .form-input:focus {
      border-color: #ea580c;
      box-shadow: 0 0 0 3px rgba(234,88,12,0.12);
    }

    /* === TOP TAB NAVIGATION === */
    .tab-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 22px;
      border-radius: 14px;
      font-size: 0.92rem;
      font-weight: 700;
      color: #64748b;
      background: rgba(255, 255, 255, 0.8);
      border: 1.5px solid #e2e8f0;
      cursor: pointer;
      transition: all 0.25s cubic-bezier(0.25, 1, 0.5, 1);
      box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }
    .tab-btn:hover {
      color: #ea580c;
      border-color: #ea580c;
      background: #ffffff;
      transform: translateY(-1px);
    }
    .tab-btn.active {
      background: #ea580c;
      color: #ffffff;
      border-color: #ea580c;
      box-shadow: 0 4px 14px rgba(234, 88, 12, 0.35);
    }
    .tab-btn.active .tab-badge {
      background: #ffffff;
      color: #ea580c;
    }
    .dropzone {
      border: 2px dashed #cbd5e1;
      border-radius: 12px;
      padding: 24px 20px;
      text-align: center;
      background: #f8fafc;
      transition: all 0.25s;
      position: relative;
      cursor: pointer;
    }
    .dropzone.dragover {
      border-color: #ea580c;
      background: #fff7ed;
    }
    .dropzone input[type="file"] {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      opacity: 0;
      cursor: pointer;
    }
    .preview-container img, .preview-container video {
      max-height: 140px;
      border-radius: 8px;
      margin: 10px auto;
      object-fit: cover;
    }

    /* === TOGGLE SWITCH === */
    .toggle-wrapper { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
    .toggle-switch { position: relative; width: 52px; height: 28px; flex-shrink: 0; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
      position: absolute; cursor: pointer;
      top: 0; left: 0; right: 0; bottom: 0;
      background: #cbd5e1; border-radius: 28px;
      transition: 0.3s;
    }
    .toggle-slider::before {
      content: ""; position: absolute;
      height: 20px; width: 20px;
      left: 4px; bottom: 4px;
      background: white; border-radius: 50%;
      transition: 0.3s;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }
    .toggle-switch input:checked + .toggle-slider { background: #ea580c; }
    .toggle-switch input:checked + .toggle-slider::before { transform: translateX(24px); }

    .slide-text-fields {
      display: none;
      flex-direction: column;
      gap: 12px;
      padding: 16px;
      background: #fff7ed;
      border-radius: 12px;
      border: 1px solid rgba(234,88,12,0.2);
      margin-bottom: 16px;
    }
    .slide-text-fields.visible { display: flex; }

    /* === RUANGAN MANAGEMENT STYLES === */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 18px;
      margin-bottom: 28px;
    }
    @media(max-width: 768px) {
      .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
    .stat-card {
      background: #ffffff;
      padding: 20px 22px;
      border-radius: 16px;
      border: 1px solid var(--border-color);
      box-shadow: 0 4px 15px rgba(0,0,0,0.02);
      display: flex;
      flex-direction: column;
      gap: 6px;
    }
    .stat-label {
      font-size: 0.76rem;
      font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .stat-value {
      font-size: 1.7rem;
      font-weight: 800;
      color: #0f172a;
    }

    .filter-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 20px;
      gap: 16px;
      flex-wrap: wrap;
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
    .search-input:focus { border-color: var(--primary); }
    .search-icon {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      width: 16px;
      height: 16px;
      fill: #94a3b8;
    }

    .table-card {
      background: #ffffff;
      border-radius: 18px;
      border: 1px solid var(--border-color);
      box-shadow: 0 8px 30px rgba(0,0,0,0.04);
      overflow-x: auto;
    }
    .table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
    }
    .table th {
      background: #f8fafc;
      padding: 16px 20px;
      font-size: 0.74rem;
      font-weight: 800;
      color: #475569;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      border-bottom: 1px solid var(--border-color);
      white-space: nowrap;
    }
    .table td {
      padding: 16px 20px;
      font-size: 0.86rem;
      color: #1e293b;
      border-bottom: 1px solid #f1f5f9;
      vertical-align: middle;
    }
    .table tbody tr:hover { background: #fcfbf9; }

    .code-badge {
      display: inline-block;
      padding: 3px 8px;
      background: #0f172a;
      color: #ffffff;
      font-size: 0.72rem;
      font-weight: 800;
      border-radius: 6px;
      letter-spacing: 0.5px;
    }
    .room-thumbnail {
      width: 48px;
      height: 48px;
      border-radius: 10px;
      object-fit: cover;
      border: 1.5px solid var(--border-color);
      background: #f1f5f9;
    }
    .file-pill {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 4px 8px;
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
      padding: 4px 10px;
      border-radius: 999px;
      font-size: 0.74rem;
      font-weight: 700;
    }
    .status-tersedia { background: #f0fdf4; color: #166534; }
    .status-tersedia .dot { background: #22c55e; }
    .status-tidaksedia { background: #fef2f2; color: #991b1b; }
    .status-tidaksedia .dot { background: #ef4444; }
    .status-perbaikan { background: #fffbeb; color: #b45309; }
    .status-perbaikan .dot { background: #f59e0b; }
    .status-badge .dot { width: 7px; height: 7px; border-radius: 50%; }

    .btn-action-group { display: flex; align-items: center; gap: 8px; }
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

    /* === MODAL OVERLAY (Ruangan) === */
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
    .modal-overlay.active { display: flex; }
    .modal-card {
      width: 100%;
      max-width: 780px;
      max-height: 90vh;
      background: #ffffff;
      border-radius: 20px;
      box-shadow: 0 25px 50px rgba(0,0,0,0.25);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      margin: auto;
      box-sizing: border-box;
    }
    .modal-card form {
      display: flex;
      flex-direction: column;
      flex: 1;
      overflow: hidden;
      min-height: 0;
      width: 100%;
      box-sizing: border-box;
    }
    .modal-header {
      padding: 18px 26px;
      background: #0f172a;
      color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-shrink: 0;
      width: 100%;
      box-sizing: border-box;
    }
    .modal-header h3 { font-size: 1.05rem; font-weight: 800; }
    .modal-close {
      background: none; border: none;
      color: #94a3b8; font-size: 1.5rem;
      cursor: pointer;
      transition: color 0.15s;
    }
    .modal-close:hover { color: #ffffff; }
    .modal-body {
      padding: 24px 28px;
      overflow-y: auto;
      overflow-x: hidden;
      flex: 1;
      min-height: 0;
      display: flex;
      flex-direction: column;
      gap: 16px;
      width: 100%;
      box-sizing: border-box;
    }
    .modal-footer {
      padding: 16px 26px;
      background: #f8fafc;
      border-top: 1px solid #e2e8f0;
      display: flex;
      justify-content: flex-end;
      gap: 12px;
      flex-shrink: 0;
      width: 100%;
      box-sizing: border-box;
    }
    .form-grid-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
      width: 100%;
      box-sizing: border-box;
    }
    .form-grid-2 > div {
      min-width: 0;
      width: 100%;
      box-sizing: border-box;
    }
    @media(max-width: 640px) {
      .form-grid-2 { grid-template-columns: 1fr; }
    }
    .form-group { display: flex; flex-direction: column; gap: 6px; width: 100%; box-sizing: border-box; }
    .form-group label {
      font-size: 0.78rem;
      font-weight: 800;
      color: #334155;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .day-select-pill {
      padding: 5px 12px;
      border-radius: 20px;
      border: 1.5px solid #cbd5e1;
      background: #ffffff;
      color: #64748b;
      font-size: 0.74rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .day-select-pill.active {
      background: #ea580c;
      color: #ffffff;
      border-color: #c2410c;
    }
    .custom-dropdown { position: relative; }
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
      cursor: pointer;
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
    }
    .custom-dropdown-menu.show { display: block; }
    .custom-dropdown-item {
      padding: 6px 12px;
      font-size: 0.8rem;
      font-weight: 700;
      color: #334155;
      cursor: pointer;
      transition: all 0.15s ease;
    }
    .custom-dropdown-item:hover, .custom-dropdown-item.active {
      background: #ea580c;
      color: #ffffff;
    }
    .upload-dropzone {
      background: #f8fafc;
      border: 2px dashed #cbd5e1;
      border-radius: 14px;
      padding: 16px 12px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      position: relative;
      cursor: pointer;
      transition: all 0.25s ease;
      min-height: 110px;
      width: 100%;
      box-sizing: border-box;
    }
    .upload-dropzone:hover { border-color: #ea580c; background: #fffaf5; }
    .upload-dropzone.dragover { border-color: #ea580c; background: #fff7ed; }
    .upload-dropzone input[type="file"] {
      position: absolute; top: 0; left: 0; width: 100%; height: 100%;
      opacity: 0; cursor: pointer; z-index: 5;
    }
    .dropzone-preview {
      width: 100%;
      box-sizing: border-box;
      display: flex;
      align-items: center;
      gap: 12px;
      background: #ffffff;
      padding: 10px 14px;
      border-radius: 12px;
      border: 1.5px solid #e2e8f0;
      margin-top: 10px;
      min-width: 0;
      box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    }
    .dropzone-preview img {
      width: 42px; height: 42px; border-radius: 10px; object-fit: cover; flex-shrink: 0; border: 1px solid #e2e8f0;
    }
    .dropzone-preview .preview-info {
      display: flex;
      flex-direction: column;
      overflow: hidden;
      flex: 1;
      min-width: 0;
      gap: 2px;
    }
    .dropzone-preview .preview-name {
      font-size: 0.78rem;
      font-weight: 700;
      color: #0f172a;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      display: block;
    }
    .dropzone-preview .preview-size {
      font-size: 0.7rem;
      font-weight: 600;
      color: #64748b;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      display: block;
    }
    .btn-remove-file {
      width: 26px;
      height: 26px;
      border-radius: 50%;
      background: #fee2e2;
      color: #ef4444;
      border: none;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 0.95rem;
      margin-left: auto;
      flex-shrink: 0;
      transition: all 0.2s ease;
    }
    .btn-remove-file:hover {
      background: #fecaca;
      transform: scale(1.1);
    }
    .swal2-container { z-index: 99999999 !important; }

    /* Staging Table (Tambah Slide - multi file upload) */
    .staging-table th, .staging-table td { padding: 8px 12px; text-align: left; }
    .staging-table th { background: #f8fafc; border-bottom: 2px solid #e2e8f0; font-size: 0.85rem; }
    .staging-table td { border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }
    .staging-preview-thumb {
      width: 56px;
      height: 56px;
      border-radius: 10px;
      object-fit: cover;
      border: 1.5px solid #e2e8f0;
      background: #0f172a;
      flex-shrink: 0;
    }
  </style>
</head>
<body class="p-6 md:p-10">

  <div class="max-w-6xl mx-auto">
    <!-- Top Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Pengaturan Portal FIK</h1>
            <p class="text-gray-500 mt-1">Kelola teks, slider carousel, dan ketersediaan data fasilitas &amp; laboratorium.</p>
        </div>
        <a href="<?= base_url('dashboard') ?>" class="inline-flex items-center gap-1 text-brand font-bold hover:underline">
            &larr; Kembali ke Dashboard
        </a>
    </div>

    <!-- TAB MENU NAVIGATION -->
    <div class="flex items-center gap-3 mb-8 border-b border-orange-200/60 pb-4">
        <button type="button" id="tabBtnHeader" onclick="switchAdminTab('header')" class="tab-btn <?= (!isset($active_tab) || $active_tab !== 'fasilitas') ? 'active' : '' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <span>Pengaturan Header &amp; Slide</span>
        </button>

        <button type="button" id="tabBtnFasilitas" onclick="switchAdminTab('fasilitas')" class="tab-btn <?= (isset($active_tab) && $active_tab === 'fasilitas') ? 'active' : '' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            <span>Kelola Fasilitas &amp; Ruangan</span>
            <span class="tab-badge ml-1 px-2.5 py-0.5 text-xs rounded-full bg-orange-100 text-orange-800 font-extrabold"><?= count($ruangan ?? []) ?></span>
        </button>
    </div>

    <!-- Flash Messages -->
    <?php if ($this->session->flashdata('success')): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-xl shadow-sm">
        <?= $this->session->flashdata('success') ?>
    </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-xl shadow-sm">
        <?= $this->session->flashdata('error') ?>
    </div>
    <?php endif; ?>

    <!-- TAB 1: PENGATURAN HEADER & SLIDE -->
    <div id="tabContentHeader" class="<?= (isset($active_tab) && $active_tab === 'fasilitas') ? 'hidden' : '' ?>">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- KOLOM 1: TAMBAH SLIDE BARU -->
        <div class="glass-card p-6 md:p-8 lg:col-span-8">
            <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                Tambah Slide Baru
            </h2>

            <form action="<?= base_url('adminheader/add_slide') ?>" method="POST" enctype="multipart/form-data" id="formAddSlide">
                
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">Label Indikator (Judul Tombol Bawah)</label>
                    <input type="text" name="label" id="slideLabel" class="form-input" placeholder="Contoh: Prestasi" required>
                    <p class="text-xs text-gray-500 mt-1">Kolom ini akan terkunci setelah Anda menambahkan file ke tabel di bawah.</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-orange-800 mb-2">Judul Utama Slide</label>
                    <input type="text" name="overlay_title" class="form-input text-sm" placeholder="Contoh: Inovasi &amp; Kreativitas FIK" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-bold text-orange-800 mb-2">Deskripsi Slide</label>
                    <textarea id="overlayDescription" name="overlay_description" class="form-input text-sm" rows="5" placeholder="Tulis deskripsi untuk slide ini..."></textarea>
                    <p class="text-xs text-orange-600 mt-1">&#9998; Editor TinyMCE aktif.</p>
                </div>

                <!-- STAGING AREA (1 file per upload, preview di dalam card) -->
                <div class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <h3 class="font-bold text-sm mb-3">Upload File Media (Maksimal Satu File per Upload)</h3>
                    
                    <!-- Dropzone dengan preview internal -->
                    <div class="dropzone mb-3" id="dropzoneStaging">
                        <input type="file" id="stagedFile" accept="image/*,video/*">
                        <!-- Konten default dropzone (instruksi) -->
                        <div id="dropzoneDefault" class="text-gray-400 pointer-events-none">
                            <svg class="w-10 h-10 mx-auto mb-2 text-brand opacity-50" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"></path></svg>
                            <p class="font-semibold text-gray-700">Tarik &amp; Lepas File Media di Sini</p>
                            <p class="text-xs mt-1">atau klik untuk memilih file (Maksimal 1 file)</p>
                        </div>
                        <!-- Preview file yang dipilih -->
                        <div id="singleFilePreview" class="hidden pointer-events-none">
                            <img id="previewSingleImg" class="max-h-32 mx-auto rounded-lg object-contain" src="" alt="">
                            <video id="previewSingleVideo" class="max-h-32 mx-auto rounded-lg hidden" controls muted></video>
                            <p id="previewSingleName" class="text-xs mt-2 font-bold text-gray-700"></p>
                            <p id="previewSingleSize" class="text-xs text-gray-500"></p>
                        </div>
                    </div>
                    
                    <!-- Tombol konfirmasi untuk memasukkan file ke tabel -->
                    <button type="button" id="btnAddToTable" class="btn-brand py-2 px-4 rounded-lg font-bold hidden w-full">
                        ➕ Tambahkan File ke Tabel
                    </button>
                    
                    <!-- Tabel staging untuk file yang sudah dikonfirmasi -->
                    <div class="overflow-x-auto border border-gray-200 rounded-lg bg-white mt-3">
                        <table class="w-full staging-table" id="stagingTable">
                            <thead>
                                <tr>
                                    <th>Preview</th>
                                    <th>Durasi (s)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="stagingTableBody">
                                <tr><td colspan="3" class="text-center text-gray-400 py-4">Belum ada file yang ditambahkan.</td></tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Hidden inputs untuk submit final -->
                    <input type="file" name="media_files[]" id="finalFiles" multiple style="display: none;">
                    <div id="hiddenDurationsContainer"></div>
                </div>

                <button type="submit" class="btn-brand w-full py-3 rounded-xl font-bold text-lg" id="btnSubmitSlide" disabled>Tambah Slide (Pilih File Dulu)</button>
            </form>
        </div>

        <!-- KOLOM 2: Manajemen Slide & Dekanat -->
        <div class="flex flex-col gap-8 lg:col-span-4">
            
            <!-- DAFTAR SLIDE -->
            <div class="glass-card p-6 md:p-8">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Daftar Slide Background
                </h2>

                <div class="flex flex-col gap-4">
                    <?php if(!empty($slides)): foreach($slides as $slide): ?>
                    <div class="flex flex-col p-4 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <?php 
                                    $is_multi = false;
                                    $first_img = '';
                                    $media_type = $slide->media_type;
                                    
                                    if (!empty($slide->media_path) && strpos($slide->media_path, '[') === 0) {
                                        $decoded = json_decode($slide->media_path, true);
                                        if (is_array($decoded) && count($decoded) > 0) {
                                            $is_multi = true;
                                            $first_img = $decoded[0]['file'] ?? '';
                                        }
                                    } else {
                                        $first_img = $slide->media_path;
                                    }
                                ?>
                                <?php if($media_type == 'video'): ?>
                                    <div class="w-16 h-12 bg-gray-900 rounded flex items-center justify-center text-white text-xs font-bold overflow-hidden">VIDEO</div>
                                <?php else: ?>
                                    <img src="<?= base_url('assets/images/' . $first_img) ?>" class="w-16 h-12 object-cover rounded border border-gray-100">
                                <?php endif; ?>
                                <div>
                                    <h4 class="font-bold text-gray-800"><?= htmlspecialchars($slide->label) ?></h4>
                                    <p class="text-xs text-gray-400 uppercase tracking-wide">
                                        <?= $is_multi ? 'Multi-Image' : $media_type ?>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <!-- Tombol edit dengan data attributes (aman) -->
                                <button type="button" 
                                        class="btn-edit-slide w-8 h-8 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center hover:bg-blue-500 hover:text-white transition-colors"
                                        data-id="<?= $slide->id ?>"
                                        data-label="<?= htmlspecialchars($slide->label, ENT_QUOTES, 'UTF-8') ?>"
                                        data-overlay-title="<?= htmlspecialchars($slide->overlay_title ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                        data-overlay-description="<?= htmlspecialchars($slide->overlay_description ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                        title="Edit Slide">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                <a href="<?= base_url('adminheader/delete_slide/'.$slide->id) ?>" onclick="return confirm('Yakin ingin menghapus slide ini?')" class="w-8 h-8 rounded-full bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors" title="Hapus Slide">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; else: ?>
                    <p class="text-gray-500 text-sm">Belum ada slide. Silakan tambahkan.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- PENGATURAN TEKS UTAMA & DEKANAT -->
            <div class="glass-card p-6 md:p-8">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Pengaturan Teks Utama & Dekanat
                </h2>

                <form action="<?= base_url('adminheader/update_settings') ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2">Gambar Dekanat Saat Ini</label>
                        <?php if(!empty($settings->dekanat_image)): ?>
                            <div class="mb-3 bg-gray-50 rounded-lg p-4 border border-gray-200 inline-block">
                                <img src="<?= base_url('assets/images/' . $settings->dekanat_image) ?>" class="h-32 object-contain" alt="Dekanat">
                            </div>
                        <?php endif; ?>
                        <label class="block text-sm font-semibold mb-2 mt-3">Upload Gambar Dekanat Baru (Opsional)</label>
                        <div class="dropzone" id="dropzoneDekanat">
                            <input type="file" name="dekanat_image" id="dekanatImageInput" accept="image/*">
                            <div class="preview-container text-gray-400" id="previewDekanat">
                                <p class="font-semibold text-gray-700">Tarik &amp; Lepas Gambar di Sini</p>
                                <p class="text-xs mt-1">atau klik untuk mencari file (JPG, PNG, WEBP)</p>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-brand w-full py-3 rounded-xl font-bold text-lg">Simpan Gambar</button>
                </form>
            </div>
        </div>
    </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 2: KELOLA FASILITAS & RUANGAN          -->
    <!-- ========================================== -->
    <div id="tabContentFasilitas" class="<?= (!isset($active_tab) || $active_tab !== 'fasilitas') ? 'hidden' : '' ?>">
        
        <!-- Header Actions & Add Button -->
        <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight flex items-center gap-2">
                    🏢 Daftar Laboratorium &amp; Fasilitas
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Kelola data ruangan, spesifikasi perangkat, jam operasional, foto &amp; model 3D.</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="openModalTambah()" class="btn-brand inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm shadow-md">
                    + Tambah Ruangan Baru
                </button>
            </div>
        </div>

        <!-- Stat Cards Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-label">Total Ruangan / Lab</span>
                <span class="stat-value"><?= count($ruangan ?? []) ?></span>
            </div>
            <div class="stat-card">
                <span class="stat-label">Lab Komputer</span>
                <span class="stat-value">
                    <?= count(array_filter($ruangan ?? [], function($r){ return $r->id_kategori == 1; })) ?>
                </span>
            </div>
            <div class="stat-card">
                <span class="stat-label">Lab Desain &amp; Studio</span>
                <span class="stat-value">
                    <?= count(array_filter($ruangan ?? [], function($r){ return $r->id_kategori == 2; })) ?>
                </span>
            </div>
            <div class="stat-card">
                <span class="stat-label">Rapat &amp; Auditorium</span>
                <span class="stat-value">
                    <?= count(array_filter($ruangan ?? [], function($r){ return $r->id_kategori == 3; })) ?>
                </span>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="filter-bar">
            <div class="search-box">
                <svg class="search-icon" viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Cari kode atau nama ruangan..." class="search-input">
            </div>
        </div>

        <!-- Main Data Table Card -->
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
                                        <div class="room-thumbnail flex items-center justify-center text-xl">🖼️</div>
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
                                        <a href="<?= base_url($r->model_3d) ?>" target="_blank" class="file-pill has-file" title="Klik untuk mengunduh/melihat berkas 3D">
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

    </div>
  </div>

  <!-- MODAL EDIT SLIDE (dengan AJAX) -->
  <div id="editSlideModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center opacity-0 transition-opacity duration-300">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 transform scale-95 transition-transform duration-300">
          <h3 class="text-xl font-bold mb-4">Edit Slide</h3>
          <form id="editSlideForm" action="" method="POST">
              <!-- data-id akan diisi via JS -->
              <input type="hidden" id="editSlideId" name="id">
              <div class="mb-4">
                  <label class="block text-sm font-bold text-gray-700 mb-2">Label Indikator</label>
                  <input type="text" id="editLabel" name="label" class="form-input" required>
              </div>
              <div class="mb-4">
                  <label class="block text-sm font-bold text-orange-800 mb-2">Judul Utama Slide</label>
                  <input type="text" id="editOverlayTitle" name="overlay_title" class="form-input" required>
              </div>
              <div class="mb-6">
                  <label class="block text-sm font-bold text-orange-800 mb-2">Deskripsi Slide</label>
                  <textarea id="editOverlayDescription" name="overlay_description" class="form-input text-sm" rows="5" placeholder="Tulis deskripsi untuk slide ini..."></textarea>
              </div>
              <div class="flex justify-end gap-3">
                  <button type="button" onclick="closeEditModal()" class="btn-secondary px-5 py-2 rounded-xl font-bold">Batal</button>
                  <button type="submit" class="btn-brand px-5 py-2 rounded-xl font-bold">Simpan</button>
              </div>
          </form>
      </div>
  </div>

  <!-- ========================================== -->
  <!-- MODAL FORM TAMBAH / EDIT RUANGAN           -->
  <!-- ========================================== -->
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
                          <input type="text" id="inputNama" name="nama_ruangan" placeholder="Contoh: Lab Multimedia & Game" required class="form-input">
                      </div>
                      <div class="form-group">
                          <label>🔑 Kode Ruangan *</label>
                          <input type="text" id="inputKode" name="kode_ruangan" placeholder="Contoh: IK.02.17" required class="form-input">
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

                  <!-- Section 2: Upload Files (Foto & 3D Model) with Drag & Drop -->
                  <div class="form-grid-2">
                      <!-- Foto Dropzone -->
                      <div>
                          <label style="font-size:0.78rem; font-weight:800; color:#334155; text-transform:uppercase; display:block; margin-bottom:6px;">📷 Foto Utama Ruangan</label>
                          <div class="upload-dropzone" id="dropzoneFoto">
                              <input type="file" id="inputFoto" name="foto" accept="image/png, image/jpeg, image/webp">
                              <div class="text-2xl mb-1">🖼️</div>
                              <div style="font-size:0.8rem; font-weight:700; color:#1e293b;">Tarik &amp; lepas foto ke sini</div>
                              <div style="font-size:0.72rem; color:#64748b;">atau <span style="color:#ea580c; font-weight:700;">pilih file</span></div>
                              <span style="font-size:0.68rem; color:#94a3b8; margin-top:2px;">JPG, PNG, WEBP (Maks 5MB)</span>
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
                          <label style="font-size:0.78rem; font-weight:800; color:#ea580c; text-transform:uppercase; display:block; margin-bottom:6px;">🧊 3D Model (.GLB / .FBX)</label>
                          <div class="upload-dropzone" id="dropzoneModel3D">
                              <input type="file" id="inputModel3D" name="model_3d" accept=".glb, .fbx, .gltf, .obj">
                              <div class="text-2xl mb-1">🧊</div>
                              <div style="font-size:0.8rem; font-weight:700; color:#1e293b;">Tarik &amp; lepas file 3D</div>
                              <div style="font-size:0.72rem; color:#64748b;">atau <span style="color:#ea580c; font-weight:700;">pilih file</span></div>
                              <span style="font-size:0.68rem; color:#94a3b8; margin-top:2px;">.GLB, .FBX, .GLTF, .OBJ (Maks 50MB)</span>
                          </div>
                          <div id="previewModelBox" class="dropzone-preview" style="display:none;">
                              <div style="width:42px; height:42px; border-radius:10px; background:#fff7ed; border:1px solid #ffedd5; display:flex; align-items:center; justify-content:center; font-size:1.3rem; flex-shrink:0;">🧊</div>
                              <div class="preview-info">
                                  <span id="previewModelText" class="preview-name">model.glb</span>
                                  <span id="previewModelSize" class="preview-size">Model 3D Siap</span>
                              </div>
                              <button type="button" class="btn-remove-file" onclick="clearFileModel(event)" title="Hapus berkas 3D">&times;</button>
                          </div>
                      </div>
                  </div>

                  <!-- Section 3: Informasi Operasional & Lokasi -->
                  <div class="form-grid-2">
                      <div class="form-group">
                          <label>📍 Lokasi Gedung / Lantai</label>
                          <input type="text" id="inputLokasi" name="lokasi" value="Gedung Sebatik (FIK)" class="form-input">
                      </div>
                      <div class="form-group">
                          <label>👥 Kapasitas (Orang)</label>
                          <input type="number" id="inputKapasitas" name="kapasitas" value="35" min="1" class="form-input">
                      </div>
                  </div>

                  <div class="form-grid-2">
                      <div class="form-group">
                          <label>✨ Tagline / Keunggulan Lab</label>
                          <input type="text" id="inputTagline" name="tagline" placeholder="Contoh: 36 Workstation PC RTX GPU" class="form-input">
                      </div>
                      <div class="form-group">
                          <label>💻 Jumlah Unit / Perangkat</label>
                          <input type="text" id="inputJumlahUnit" name="jumlah_unit" placeholder="Contoh: 36 Unit PC High-End" class="form-input">
                      </div>
                  </div>

                  <!-- Jam Operasional Interaktif -->
                  <div class="form-group">
                      <label>⏰ Jam Operasional</label>
                      <div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:8px;">
                          <?php foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $day): ?>
                              <span class="day-select-pill <?= in_array($day, ['Senin','Selasa','Rabu','Kamis','Jumat']) ? 'active' : '' ?>" data-day="<?= $day ?>" onclick="toggleDayPill(this)"><?= $day ?></span>
                          <?php endforeach; ?>
                      </div>

                      <div style="display:grid; grid-template-columns: 1fr 1fr 2fr; gap:10px; align-items:center; background:#f8fafc; padding:12px; border-radius:14px; border:1px solid #e2e8f0;">
                          <div>
                              <span style="font-size:0.7rem; font-weight:700; color:#64748b; display:block; margin-bottom:4px;">Jam Buka</span>
                              <div style="display:flex; gap:4px; align-items:center;">
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

                          <div>
                              <span style="font-size:0.7rem; font-weight:700; color:#64748b; display:block; margin-bottom:4px;">Jam Tutup</span>
                              <div style="display:flex; gap:4px; align-items:center;">
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

                          <div>
                              <span style="font-size:0.7rem; font-weight:700; color:#64748b; display:block; margin-bottom:4px;">Hasil Text (Tersimpan)</span>
                              <input type="text" id="inputJamOperasional" name="jam_operasional" value="Senin - Jumat | 08:00 - 17:00 WIB" class="form-input" style="background:#fff; font-weight:700; color:#ea580c; padding:6px 10px; font-size:0.8rem;">
                          </div>
                      </div>
                  </div>

                  <!-- Section 4: Deskripsi Lengkap -->
                  <div class="form-group">
                      <label>📝 Deskripsi Ruangan / Lab</label>
                      <textarea id="inputDeskripsi" name="deskripsi" placeholder="Tuliskan deskripsi lengkap peruntukan lab untuk mahasiswa..." class="form-input" style="min-height:75px;"></textarea>
                  </div>

                  <!-- Section 5: Spesifikasi Perangkat -->
                  <div class="form-group">
                      <label>🛠️ Fasilitas &amp; Spesifikasi Perangkat</label>
                      <textarea id="inputSpesifikasi" name="spesifikasi_fasilitas" placeholder="Contoh:&#10;- Wacom Cintiq Pro 24&#10;- Software Drawing&#10;- RTX 4080 GPU" class="form-input" style="min-height:75px;"></textarea>
                  </div>

                  <!-- Section 6: Tata Tertib -->
                  <div class="form-group">
                      <label>⚠️ Tata Tertib &amp; Ketentuan Pengguna</label>
                      <textarea id="inputTataTertib" name="tata_tertib" placeholder="Contoh:&#10;- Dilarang membawa makanan/minuman ke area perangkat.&#10;- Rapikan kembali meja setelah selesai." class="form-input" style="min-height:70px;"></textarea>
                  </div>

              </div>
              <div class="modal-footer">
                  <button type="button" onclick="closeModalRuangan()" class="px-4 py-2 bg-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-300">Batal</button>
                  <button type="submit" class="btn-brand px-6 py-2 rounded-xl font-bold">Simpan Ruangan</button>
              </div>
          </form>
      </div>
  </div>

  <script>
      // ===== TAB SWITCHING LOGIC =====
      function switchAdminTab(tabName) {
          const tabBtnHeader = document.getElementById('tabBtnHeader');
          const tabBtnFasilitas = document.getElementById('tabBtnFasilitas');
          const tabContentHeader = document.getElementById('tabContentHeader');
          const tabContentFasilitas = document.getElementById('tabContentFasilitas');

          if (tabName === 'fasilitas') {
              tabBtnFasilitas.classList.add('active');
              tabBtnHeader.classList.remove('active');
              tabContentFasilitas.classList.remove('hidden');
              tabContentHeader.classList.add('hidden');
              window.history.replaceState(null, null, '?tab=fasilitas');
          } else {
              tabBtnHeader.classList.add('active');
              tabBtnFasilitas.classList.remove('active');
              tabContentHeader.classList.remove('hidden');
              tabContentFasilitas.classList.add('hidden');
              window.history.replaceState(null, null, '?tab=header');
          }
      }

      // Check URL params on load
      document.addEventListener('DOMContentLoaded', () => {
          const urlParams = new URLSearchParams(window.location.search);
          if (urlParams.get('tab') === 'fasilitas' || window.location.hash === '#fasilitas') {
              switchAdminTab('fasilitas');
          }
      });
  </script>

  <script>
      // ===== TINYMCE: Editor untuk Deskripsi Slide (form Tambah) =====
      tinymce.init({
          selector: '#overlayDescription',
          plugins: 'lists link autolink',
          toolbar: 'bold italic underline | bullist numlist | link | removeformat',
          menubar: false,
          height: 180,
          skin: 'oxide',
          branding: false,
          setup: function(editor) {
              editor.on('change', function() { editor.save(); });
          }
      });

      document.getElementById('formAddSlide').addEventListener('submit', function() {
          tinymce.triggerSave();
          document.getElementById('slideLabel').disabled = false;
      });

      // --- STAGING TABLE LOGIC (1 file per upload, preview dalam dropzone) ---
      const dataTransfer = new DataTransfer();
      const stagingFiles = []; // file yang sudah dikonfirmasi
      let selectedFile = null; // file yang sedang dipilih untuk ditambahkan
      const fileInput = document.getElementById('stagedFile');
      const finalFilesInput = document.getElementById('finalFiles');
      const hiddenDurationsContainer = document.getElementById('hiddenDurationsContainer');
      const tableBody = document.getElementById('stagingTableBody');
      const labelInput = document.getElementById('slideLabel');
      const btnSubmit = document.getElementById('btnSubmitSlide');
      const dropzoneStaging = document.getElementById('dropzoneStaging');
      const btnAddToTable = document.getElementById('btnAddToTable');
      const dropzoneDefault = document.getElementById('dropzoneDefault');
      const singleFilePreview = document.getElementById('singleFilePreview');

      // Fungsi format ukuran file (jika belum ada)
      function formatFileSize(bytes) {
          if (!bytes || bytes === 0) return '0 B';
          const k = 1024;
          const sizes = ['B', 'KB', 'MB', 'GB'];
          const i = Math.floor(Math.log(bytes) / Math.log(k));
          return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
      }

      // Render tabel file yang sudah dikonfirmasi
      function renderTable() {
          if (stagingFiles.length === 0) {
              tableBody.innerHTML = '<tr><td colspan="3" class="text-center text-gray-400 py-4">Belum ada file yang ditambahkan.</td></tr>';
              labelInput.disabled = false;
              btnSubmit.disabled = true;
              btnSubmit.innerText = "Tambah Slide (Pilih File Dulu)";
              return;
          }

          labelInput.disabled = true;
          btnSubmit.disabled = false;
          btnSubmit.innerText = "Tambah Slide";

          tableBody.innerHTML = '';
          hiddenDurationsContainer.innerHTML = '';

          stagingFiles.forEach((item, index) => {
              const tr = document.createElement('tr');
              const isVideo = item.file.type.startsWith('video/');
              const previewEl = isVideo
                  ? `<video src="${item.url}" class="staging-preview-thumb" muted playsinline></video>`
                  : `<img src="${item.url}" class="staging-preview-thumb" alt="Preview">`;

              tr.innerHTML = `
                  <td>
                      <div class="flex items-center gap-3">
                          ${previewEl}
                          <span class="text-xs text-gray-500 truncate max-w-[140px]" title="${item.file.name}">${item.file.name}</span>
                      </div>
                  </td>
                  <td><input type="number" class="form-input py-1 px-2 text-sm w-20" min="1" value="${item.duration}" onchange="updateDuration(${index}, this.value)"></td>
                  <td><button type="button" onclick="removeStagedFile(${index})" class="text-red-500 font-bold hover:underline text-sm">Hapus</button></td>
              `;
              tableBody.appendChild(tr);

              const hiddenDuration = document.createElement('input');
              hiddenDuration.type = 'hidden';
              hiddenDuration.name = 'durations[]';
              hiddenDuration.value = item.duration;
              hiddenDurationsContainer.appendChild(hiddenDuration);
          });

          // Update finalFiles input
          finalFilesInput.files = dataTransfer.files;
      }

      // Update durasi file
      window.updateDuration = function(index, val) {
          stagingFiles[index].duration = parseInt(val) || 3;
          renderTable();
      };

      // Hapus file dari staging
      window.removeStagedFile = function(index) {
          if (stagingFiles[index] && stagingFiles[index].url) {
              URL.revokeObjectURL(stagingFiles[index].url);
          }
          stagingFiles.splice(index, 1);
          dataTransfer.items.remove(index);
          renderTable();
      };

      // Menampilkan preview file tunggal di dalam dropzone
      function showSinglePreview(file) {
          if (!file) return;
          
          dropzoneDefault.classList.add('hidden');
          singleFilePreview.classList.remove('hidden');
          btnAddToTable.classList.remove('hidden');

          const isVideo = file.type.startsWith('video/');
          const img = document.getElementById('previewSingleImg');
          const video = document.getElementById('previewSingleVideo');
          
          if (isVideo) {
              img.classList.add('hidden');
              video.classList.remove('hidden');
              video.src = URL.createObjectURL(file);
          } else {
              video.classList.add('hidden');
              img.classList.remove('hidden');
              img.src = URL.createObjectURL(file);
          }
          
          document.getElementById('previewSingleName').innerText = file.name;
          document.getElementById('previewSingleSize').innerText = formatFileSize(file.size);
      }

      // Reset area preview ke kondisi awal
      function resetSinglePreview() {
          selectedFile = null;
          dropzoneDefault.classList.remove('hidden');
          singleFilePreview.classList.add('hidden');
          btnAddToTable.classList.add('hidden');
          // Reset input file
          fileInput.value = '';
          // Hentikan video jika ada
          const video = document.getElementById('previewSingleVideo');
          video.pause();
          video.src = '';
          document.getElementById('previewSingleImg').src = '';
      }

      // Event listener saat file dipilih
      fileInput.addEventListener('change', function(e) {
          if (this.files && this.files.length > 0) {
              selectedFile = this.files[0];
              showSinglePreview(selectedFile);
          }
          this.value = ''; // reset input agar bisa memilih file yang sama lagi
      });

      // Dropzone drag & drop (hanya ambil file pertama)
      dropzoneStaging.addEventListener('dragover', e => { e.preventDefault(); dropzoneStaging.classList.add('dragover'); });
      dropzoneStaging.addEventListener('dragleave', e => { dropzoneStaging.classList.remove('dragover'); });
      dropzoneStaging.addEventListener('drop', e => {
          e.preventDefault(); dropzoneStaging.classList.remove('dragover');
          if (e.dataTransfer.files.length) {
              selectedFile = e.dataTransfer.files[0];
              showSinglePreview(selectedFile);
          }
      });

      // Tombol konfirmasi: masukkan file ke staging
      btnAddToTable.addEventListener('click', function() {
          if (selectedFile) {
              // Tambahkan ke dataTransfer dan stagingFiles
              dataTransfer.items.add(selectedFile);
              stagingFiles.push({
                  file: selectedFile,
                  duration: 3,
                  url: URL.createObjectURL(selectedFile) // URL baru untuk preview di tabel
              });
              
              // Reset area preview
              resetSinglePreview();
              
              // Update finalFiles input dan render tabel
              finalFilesInput.files = dataTransfer.files;
              renderTable();
          }
      });

      // --- DROPZONE DEKANAT ---
      function setupDropzoneDekanat() {
          const dropzone = document.getElementById('dropzoneDekanat');
          if (!dropzone) return;
          const input = document.getElementById('dekanatImageInput');
          const preview = document.getElementById('previewDekanat');

          dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.classList.add('dragover'); });
          dropzone.addEventListener('dragleave', e => { dropzone.classList.remove('dragover'); });
          dropzone.addEventListener('drop', e => {
              e.preventDefault(); dropzone.classList.remove('dragover');
              if (e.dataTransfer.files.length) {
                  input.files = e.dataTransfer.files;
                  handleDekanatPreview(input.files[0], preview);
              }
          });
          input.addEventListener('change', () => {
              if (input.files.length) handleDekanatPreview(input.files[0], preview);
          });
      }

      function handleDekanatPreview(file, container) {
          container.innerHTML = '';
          const img = document.createElement('img');
          img.src = URL.createObjectURL(file);
          container.appendChild(img);
          const txt = document.createElement('p');
          txt.className = 'text-xs mt-2 font-bold text-gray-700';
          txt.innerText = file.name;
          container.appendChild(txt);
      }
  </script>

  <script>
      // ===== EDIT SLIDE MODAL (dengan AJAX) =====
      function openEditSlideModal(id, label, overlayTitle, overlayDescription) {
          document.getElementById('editSlideId').value = id;
          document.getElementById('editLabel').value = label || '';
          document.getElementById('editOverlayTitle').value = overlayTitle || '';
          document.getElementById('editOverlayDescription').value = overlayDescription || '';

          const modal = document.getElementById('editSlideModal');
          modal.classList.remove('hidden');
          // setTimeout for transition
          setTimeout(() => {
              modal.classList.remove('opacity-0');
              modal.querySelector('div').classList.remove('scale-95');
          }, 10);
      }

      function closeEditModal() {
          const modal = document.getElementById('editSlideModal');
          modal.classList.add('opacity-0');
          modal.querySelector('div').classList.add('scale-95');
          setTimeout(() => {
              modal.classList.add('hidden');
          }, 300);
      }

      // Event listener untuk tombol edit slide (data-attributes)
      document.addEventListener('DOMContentLoaded', function() {
          document.querySelectorAll('.btn-edit-slide').forEach(btn => {
              btn.addEventListener('click', function() {
                  const id = this.getAttribute('data-id');
                  const label = this.getAttribute('data-label');
                  const overlayTitle = this.getAttribute('data-overlay-title');
                  const overlayDescription = this.getAttribute('data-overlay-description');
                  openEditSlideModal(id, label, overlayTitle, overlayDescription);
              });
          });
      });

      // Submit form edit slide via AJAX
      document.getElementById('editSlideForm').addEventListener('submit', function(e) {
          e.preventDefault();
          const formData = new FormData(this);
          const id = document.getElementById('editSlideId').value;
          formData.append('id', id);

          Swal.fire({
              title: 'Menyimpan...',
              text: 'Mohon tunggu',
              allowOutsideClick: false,
              didOpen: () => Swal.showLoading()
          });

          fetch('<?= base_url("adminheader/edit_slide_ajax") ?>', {
              method: 'POST',
              body: formData
          })
          .then(res => res.json())
          .then(data => {
              if (data.status === 'success') {
                  Swal.fire('Berhasil!', data.message, 'success').then(() => {
                      location.reload();
                  });
              } else {
                  Swal.fire('Gagal', data.message, 'error');
              }
          })
          .catch(err => {
              Swal.fire('Error', 'Terjadi kesalahan jaringan', 'error');
          });
      });
  </script>

  <script>
      // ===== RUANGAN MANAGEMENT JAVASCRIPT =====
      let isEditMode = false;
      const allDaysOrdered = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

      function toggleDayPill(pill) {
          pill.classList.toggle('active');
          updateJamOperasionalText();
      }

      function getFormattedSelectedDays() {
          const activePills = Array.from(document.querySelectorAll('.day-select-pill.active')).map(p => p.getAttribute('data-day'));
          if (activePills.length === 0) return 'Senin - Jumat';
          if (activePills.length === 7) return 'Setiap Hari';
          const selected = allDaysOrdered.filter(d => activePills.includes(d));
          if (selected.length === 5 && selected[0] === 'Senin' && selected[4] === 'Jumat') return 'Senin - Jumat';
          if (selected.length === 6 && selected[0] === 'Senin' && selected[5] === 'Sabtu') return 'Senin - Sabtu';
          if (selected.length === 2 && selected[0] === 'Sabtu' && selected[1] === 'Minggu') return 'Sabtu & Minggu';
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
          document.getElementById('inputJamOperasional').value = `${formattedDays} | ${startH}:${startM} - ${endH}:${endM} WIB`;
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
          document.getElementById('previewModelText').innerText = file.name;
          document.getElementById('previewModelSize').innerText = `${ext.toUpperCase()} • ${formatFileSize(file.size)}`;
          document.getElementById('previewModelBox').style.display = 'flex';
      }

      function initRuanganDropzones() {
          const dropFoto = document.getElementById('dropzoneFoto');
          const inputFoto = document.getElementById('inputFoto');
          const dropModel = document.getElementById('dropzoneModel3D');
          const inputModel = document.getElementById('inputModel3D');

          if (dropFoto && inputFoto) {
              ['dragenter', 'dragover'].forEach(name => dropFoto.addEventListener(name, e => { e.preventDefault(); dropFoto.classList.add('dragover'); }));
              ['dragleave', 'drop'].forEach(name => dropFoto.addEventListener(name, e => { e.preventDefault(); dropFoto.classList.remove('dragover'); }));
              dropFoto.addEventListener('drop', e => {
                  if (e.dataTransfer && e.dataTransfer.files.length) {
                      inputFoto.files = e.dataTransfer.files;
                      handleFotoFile(inputFoto.files[0]);
                  }
              });
              inputFoto.addEventListener('change', () => {
                  if (inputFoto.files && inputFoto.files.length) handleFotoFile(inputFoto.files[0]);
              });
          }

          if (dropModel && inputModel) {
              ['dragenter', 'dragover'].forEach(name => dropModel.addEventListener(name, e => { e.preventDefault(); dropModel.classList.add('dragover'); }));
              ['dragleave', 'drop'].forEach(name => dropModel.addEventListener(name, e => { e.preventDefault(); dropModel.classList.remove('dragover'); }));
              dropModel.addEventListener('drop', e => {
                  if (e.dataTransfer && e.dataTransfer.files.length) {
                      inputModel.files = e.dataTransfer.files;
                      handleModelFile(inputModel.files[0]);
                  }
              });
              inputModel.addEventListener('change', () => {
                  if (inputModel.files && inputModel.files.length) handleModelFile(inputModel.files[0]);
              });
          }
      }

      function openModalTambah() {
          isEditMode = false;
          document.body.style.overflow = 'hidden';
          document.getElementById('modalTitle').innerText = '🏢 Tambah Ruangan / Lab Baru';
          document.getElementById('formRuangan').reset();
          document.getElementById('ruanganId').value = '';
          document.getElementById('inputJamOperasional').value = 'Senin - Jumat | 08:00 - 17:00 WIB';
          document.getElementById('inputLokasi').value = 'Gedung Sebatik (FIK)';
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
          document.getElementById('inputJamOperasional').value = data.jam_operasional || 'Senin - Jumat | 08:00 - 17:00 WIB';
          document.getElementById('inputDeskripsi').value = data.deskripsi || '';
          document.getElementById('inputSpesifikasi').value = data.spesifikasi_fasilitas || '';
          document.getElementById('inputTataTertib').value = data.tata_tertib || '';

          if (data.foto) {
              const fotoPath = data.foto.startsWith('http') ? data.foto : `<?= base_url() ?>${data.foto}`;
              document.getElementById('previewFotoBox').style.display = 'flex';
              document.getElementById('previewFotoImg').src = fotoPath;
              document.getElementById('previewFotoText').innerText = data.foto.split('/').pop();
              document.getElementById('previewFotoSize').innerText = 'Foto Terpasang';
          } else {
              clearFileFoto();
          }

          if (data.model_3d) {
              const ext = data.model_3d.split('.').pop().toUpperCase();
              document.getElementById('previewModelBox').style.display = 'flex';
              document.getElementById('previewModelText').innerText = data.model_3d.split('/').pop();
              document.getElementById('previewModelSize').innerText = `Berkas 3D (${ext}) Terpasang`;
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
              text: 'Harap tunggu, data ruangan dan berkas sedang diproses...',
              allowOutsideClick: false,
              didOpen: () => { Swal.showLoading(); }
          });

          fetch(targetUrl, {
              method: 'POST',
              body: formData,
              headers: { 'X-Requested-With': 'XMLHttpRequest' }
          })
          .then(res => res.text().then(text => ({ ok: res.ok, status: res.status, text: text })))
          .then(({ ok, status, text }) => {
              try {
                  const data = JSON.parse(text);
                  if (data.status === 'success') {
                      Swal.fire('Berhasil!', data.message, 'success').then(() => {
                          window.location.href = '<?= base_url('adminheader?tab=fasilitas') ?>';
                      });
                  } else if (data.redirect) {
                      Swal.fire('Sesi Login Berakhir', data.message || 'Silakan login kembali.', 'warning').then(() => {
                          window.location.href = data.redirect;
                      });
                  } else {
                      Swal.fire('Gagal Menyimpan', data.message || 'Terjadi kesalahan pada data ruangan.', 'error');
                  }
              } catch(e) {
                  console.error('Raw Server Output:', text);
                  Swal.fire('Error Server', 'Respon server tidak valid: ' + text.substring(0, 200), 'error');
              }
          })
          .catch(err => {
              console.error('Fetch Error:', err);
              Swal.fire('Koneksi Terputus', 'Gagal terhubung ke server.', 'error');
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
                              window.location.href = '<?= base_url('adminheader?tab=fasilitas') ?>';
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

      // Inisialisasi dropzone dekanat & dropzone ruangan (foto/3D) setelah DOM siap
      setupDropzoneDekanat();
      initRuanganDropzones();
  </script>
</body>
</html>