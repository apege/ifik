<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalender Peminjaman Ruangan - IFIK</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- jQuery & SweetAlert2 & Flatpickr -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- Info Ruangan & Calendar CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/info_ruangan.css?v=' . filemtime(FCPATH . 'assets/css/info_ruangan.css')) ?>">

    <style>
        :root {
            --bg-color: #fbf7f1;
            --text-color: #1e293b;
            --primary: #ea580c;
            --primary-hover: #c2410c;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            min-height: 100vh;
            overflow: hidden;
        }

        .gcal-page-header {
            position: relative;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 28px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            height: 70px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            gap: 16px;
        }

        .btn-back-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.88rem;
            padding: 8px 18px;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .btn-back-home:hover {
            color: var(--primary);
            border-color: var(--primary);
            background: #fff;
            transform: translateX(-2px);
        }

        .btn-ajukan-booking {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary);
            color: #ffffff;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.88rem;
            padding: 9px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(234, 88, 12, 0.3);
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .btn-ajukan-booking:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(234, 88, 12, 0.4);
        }

        /* ===== UNIFIED PAIRED SEARCH PILL STYLES ===== */
        .search-filter-container {
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
        }

        .unified-search-pill {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            padding: 3px 10px;
            width: 450px;
            transition: all 0.2s ease;
            position: relative;
            overflow: visible !important;
        }
        .unified-search-pill:focus-within, .unified-search-pill.active {
            border-color: #ea580c !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.12) !important;
        }

        /* ===== CUSTOM STYLED CATEGORY DROPDOWN ===== */
        .custom-cat-dropdown {
            position: relative;
            display: inline-block;
            flex-shrink: 0;
            z-index: 10;
        }
        .custom-cat-dropdown.open {
            z-index: 100020 !important;
        }

        .custom-cat-trigger {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: transparent;
            border: none;
            font-size: 0.82rem;
            font-weight: 700;
            color: #1e293b;
            cursor: pointer;
            padding: 6px 4px;
            outline: none;
            white-space: nowrap;
            transition: color 0.2s ease;
        }
        .custom-cat-trigger:hover {
            color: #ea580c;
        }
        .custom-cat-trigger svg {
            transition: transform 0.2s ease;
        }
        .custom-cat-dropdown.open .custom-cat-trigger svg {
            transform: rotate(180deg);
            stroke: #ea580c;
        }

        .custom-cat-menu {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 16px 40px rgba(0,0,0,0.18);
            min-width: 200px;
            z-index: 100030 !important;
            padding: 6px;
            overflow: hidden;
        }
        .custom-cat-dropdown.open .custom-cat-menu {
            display: block;
            animation: fadeInDrop 0.15s ease-out;
        }

        @keyframes fadeInDrop {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .cat-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 10px;
            font-size: 0.82rem;
            font-weight: 700;
            color: #334155;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .cat-option:hover {
            background: #fff7ed;
            color: #ea580c;
        }
        .cat-option.active {
            background: #ea580c;
            color: #ffffff;
        }

        /* ===== CUSTOM STYLED STATUS SELECTOR DROPDOWN ===== */
        .custom-status-dropdown {
            position: relative;
            z-index: 10;
        }
        .custom-status-dropdown.open {
            z-index: 100020 !important;
        }

        .custom-status-trigger {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            background: transparent;
            border: none;
            font-size: 0.84rem;
            font-weight: 700;
            color: #1e293b;
            cursor: pointer;
            padding: 6px 10px;
            outline: none;
        }

        .custom-status-menu {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 16px 40px rgba(0,0,0,0.18);
            z-index: 100030 !important;
            padding: 6px;
            min-width: 220px;
        }
        .custom-status-dropdown.open .custom-status-menu {
            display: block;
            animation: fadeInDrop 0.15s ease-out;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
        }

        .status-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 10px;
            font-size: 0.82rem;
            font-weight: 700;
            color: #334155;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .status-option:hover {
            background: #fff7ed;
            color: #ea580c;
        }
        .status-option.active {
            background: #fff7ed;
            color: #ea580c;
            border: 1px solid #ffedd5;
        }

        .unified-divider {
            width: 1px;
            height: 22px;
            background: #cbd5e1;
            margin: 0 6px;
            flex-shrink: 0;
        }

        .unified-input-key {
            width: 100%;
            border: none !important;
            background: transparent !important;
            font-size: 0.84rem;
            font-weight: 600;
            color: #0f172a;
            outline: none;
            padding: 6px 6px 6px 28px;
        }
        .unified-input-key::placeholder {
            color: #94a3b8;
            font-weight: 500;
        }

        /* SEPARATE STANDALONE + TAMBAH BUTTON BESIDE SEARCH PILL */
        .btn-standalone-add {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: #fff7ed;
            border: 1.5px solid #ffedd5;
            color: #ea580c;
            height: 42px;
            padding: 0 14px;
            border-radius: 12px;
            font-size: 0.86rem;
            font-weight: 800;
            cursor: pointer !important;
            pointer-events: auto !important;
            transition: all 0.2s ease;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .btn-standalone-add:hover, .btn-standalone-add.active {
            background: #ea580c;
            color: #ffffff;
            border-color: #ea580c;
            box-shadow: 0 4px 14px rgba(234, 88, 12, 0.3);
        }
        .badge-standalone-count {
            background: #ea580c;
            color: #ffffff;
            font-size: 0.76rem;
            font-weight: 800;
            padding: 2px 7px;
            border-radius: 99px;
            transition: all 0.2s ease;
        }
        .btn-standalone-add:hover .badge-standalone-count, .btn-standalone-add.active .badge-standalone-count {
            background: #ffffff;
            color: #ea580c;
        }

        /* DEDICATED AUTOCOMPLETE DROPDOWN (PURE SUGGESTIONS ONLY) */
        #mainAutocompleteList {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            width: 450px;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 18px;
            box-shadow: 0 16px 45px rgba(0,0,0,0.18);
            max-height: 360px;
            overflow-y: auto;
            z-index: 100050 !important;
            padding: 6px;
        }

        /* EXTRA FILTER ROWS CARD (CONTAINS ROW 2,3,4) */
        #extraRowsCard {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 18px;
            box-shadow: 0 16px 40px rgba(0,0,0,0.14);
            z-index: 100000;
            padding: 14px;
            overflow: visible !important;
        }

        #additionalFilterRowsContainer {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 10px;
            overflow: visible !important;
        }

        .extra-filter-row {
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
            z-index: 10;
            overflow: visible !important;
        }
        .extra-filter-row.dropdown-active {
            z-index: 100010 !important;
        }

        .btn-remove-extra-row {
            background: #fef2f2;
            color: #ef4444;
            border: 1.5px solid #fca5a5;
            border-radius: 10px;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.1rem;
            line-height: 1;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }
        .btn-remove-extra-row:hover {
            background: #ef4444;
            color: #ffffff;
            border-color: #ef4444;
        }

        .search-autocomplete-item {
            padding: 10px 14px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.15s ease;
            border-bottom: 1px solid #f1f5f9;
        }
        .search-autocomplete-item:last-child {
            border-bottom: none;
        }
        .search-autocomplete-item:hover {
            background: #fff7ed;
        }

        .search-autocomplete-item .match-highlight {
            background: #ffedd5;
            color: #ea580c;
            font-weight: 700;
            border-radius: 2px;
            padding: 0 2px;
        }

        /* FLATPICKR BEAUTIFUL CUSTOM CALENDAR STYLING */
        .flatpickr-calendar {
            border-radius: 18px !important;
            box-shadow: 0 18px 45px rgba(0,0,0,0.16) !important;
            border: 1.5px solid #e2e8f0 !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            z-index: 100060 !important;
            padding: 8px !important;
        }
        .flatpickr-day.selected, .flatpickr-day.selected:hover {
            background: #ea580c !important;
            border-color: #ea580c !important;
            font-weight: 700 !important;
        }
        .flatpickr-day:hover {
            background: #fff7ed !important;
        }
        .flatpickr-months .flatpickr-month {
            color: #0f172a !important;
            font-weight: 800 !important;
        }

        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 999999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }
        .modal-overlay.show {
            opacity: 1;
            pointer-events: auto;
        }
        .modal-content {
            background: #ffffff;
            width: 100%;
            max-width: 520px;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
            overflow: hidden;
        }
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #94a3b8;
            cursor: pointer;
        }
        .modal-close:hover { color: #1e293b; }

        .swal2-container { z-index: 9999999 !important; }

        @media (max-width: 1024px) {
            .unified-search-pill { width: 340px; }
            #mainAutocompleteList { width: 340px; }
        }
        @media (max-width: 640px) {
            .gcal-page-header { flex-wrap: wrap; height: auto; padding: 12px 16px; }
            .search-filter-container { width: 100%; }
            .unified-search-pill { width: 100%; }
        }
    </style>
</head>
<body>

    <!-- Header Kalender Full Page (Single Row Height 70px) -->
    <div class="gcal-page-header">
        <div class="gcal-header-left" style="display: flex; align-items: center; gap: 16px;">
            <button class="gcal-btn-today" onclick="goToToday()">Today</button>
            <div class="gcal-nav-arrows" style="display: flex; gap: 4px;">
                <button onclick="prevWeek()"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg></button>
                <button onclick="nextWeek()"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg></button>
            </div>
            <h2 class="gcal-month-title" id="gcalMonthTitle" style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0;">-</h2>
        </div>

        <!-- UNIFIED SEARCH PILL & SEPARATE STANDALONE + BUTTON IN HEADER -->
        <div class="search-filter-container">
            
            <!-- Main Row 1 Pill (Kategori + Key Text Search / Custom Status Select) -->
            <div class="unified-search-pill" id="unifiedSearchPill">
                
                <!-- CUSTOM STYLED CATEGORY DROPDOWN -->
                <div class="custom-cat-dropdown" id="mainCatWrap">
                    <button type="button" class="custom-cat-trigger" onclick="toggleCatDropdown('main', event)">
                        <span id="mainCatLabel">Key / Kata Kunci</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>

                    <input type="hidden" id="mainCategorySelect" class="extra-cat-select" value="keyword">

                    <div class="custom-cat-menu" id="mainCatMenu">
                        <div class="cat-option active" data-val="keyword" onclick="selectCatOption('main', 'keyword', 'Key / Kata Kunci', '🔑')">
                            <span>🔑</span> Key / Kata Kunci
                        </div>
                        <div class="cat-option" data-val="kategori" onclick="selectCatOption('main', 'kategori', 'Kategori Ruangan', '📁')">
                            <span>📁</span> Kategori Ruangan
                        </div>
                        <div class="cat-option" data-val="ruangan" onclick="selectCatOption('main', 'ruangan', 'Pilih Ruangan', '🏢')">
                            <span>🏢</span> Pilih Ruangan
                        </div>
                        <div class="cat-option" data-val="status" onclick="selectCatOption('main', 'status', 'Status Peminjaman', '⚡')">
                            <span>⚡</span> Status Peminjaman
                        </div>
                        <div class="cat-option" data-val="tanggal" onclick="selectCatOption('main', 'tanggal', 'Lompat Tanggal', '📅')">
                            <span>📅</span> Lompat Tanggal
                        </div>
                    </div>
                </div>

                <div class="unified-divider"></div>

                <!-- Text Search Container -->
                <div style="position: relative; flex: 1; display: flex; align-items: center;" id="mainValueContainer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.5" style="position: absolute; left: 8px; pointer-events: none;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" id="mainSearchInput" placeholder="Cari ruangan, peminjam, kode (key)..." 
                           oninput="handleUnifiedMultiSearch(this)" 
                           onfocus="onMainInputFocused()"
                           autocomplete="off" class="unified-input-key main-val-field">
                    <button id="clearMainSearchBtn" onclick="clearMainSearch()" style="display: none; position: absolute; right: 6px; background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 1rem;">&times;</button>
                </div>

                <!-- Custom Styled Status Selector Dropdown (Shown when Status Peminjaman is selected) -->
                <div class="custom-status-dropdown" id="mainStatusWrap" style="display: none; flex: 1;">
                    <button type="button" class="custom-status-trigger" onclick="toggleStatusDropdown('main', event)">
                        <span id="mainStatusLabel" style="display: flex; align-items: center; gap: 6px;">
                            <span class="status-dot" style="background: #94a3b8;"></span> Semua Status
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    
                    <input type="hidden" id="mainStatusValue" class="extra-input-key main-val-field" value="">

                    <div class="custom-status-menu" id="mainStatusMenu">
                        <div class="status-option active" data-val="" onclick="selectStatusOption('main', '', 'Semua Status', '#94a3b8')">
                            <span class="status-dot" style="background: #94a3b8;"></span> Semua Status
                        </div>
                        <div class="status-option" data-val="pending" onclick="selectStatusOption('main', 'pending', 'Menunggu Persetujuan', '#f59e0b')">
                            <span class="status-dot" style="background: #f59e0b;"></span> Menunggu Persetujuan
                        </div>
                        <div class="status-option" data-val="disetujui" onclick="selectStatusOption('main', 'disetujui', 'Disetujui', '#10b981')">
                            <span class="status-dot" style="background: #10b981;"></span> Disetujui
                        </div>
                        <div class="status-option" data-val="ditolak" onclick="selectStatusOption('main', 'ditolak', 'Ditolak', '#ef4444')">
                            <span class="status-dot" style="background: #ef4444;"></span> Ditolak
                        </div>
                        <div class="status-option" data-val="selesai" onclick="selectStatusOption('main', 'selesai', 'Selesai', '#64748b')">
                            <span class="status-dot" style="background: #64748b;"></span> Selesai
                        </div>
                    </div>
                </div>

            </div>

            <!-- SEPARATE STANDALONE + BUTTON RIGHT BESIDE SEARCH PILL -->
            <button type="button" id="standaloneAddBtn" onclick="toggleOrAddFilterRow(event)" class="btn-standalone-add" title="Buka / Tutup / Tambah Filter Baru (Maks 4)">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span id="filterCountBadge" class="badge-standalone-count">1/4</span>
            </button>

            <!-- Clean Dedicated Autocomplete Dropdown List (ONLY Suggestions) -->
            <div id="mainAutocompleteList"></div>

            <!-- Extra Filter Rows Card (Contains Row 2, 3, 4) -->
            <div id="extraRowsCard">
                <div id="additionalFilterRowsContainer">
                    <!-- Additional rows appended via JS -->
                </div>
                
                <div style="display: flex; justify-content: flex-end; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 8px; margin-top: 6px;">
                    <button type="button" onclick="resetHeaderMultiSearch()" style="background: none; border: none; color: #dc2626; font-weight: 700; font-size: 0.76rem; cursor: pointer;">
                        Reset All Filters
                    </button>
                </div>
            </div>

        </div>

        <div class="gcal-header-right" style="display: flex; align-items: center; gap: 12px;">
            <a href="<?= base_url() ?>" class="btn-back-home">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Kembali
            </a>

            <?php if ($this->session->userdata('logged_in')): ?>
                <a href="<?= base_url('ajukan-booking') ?>" class="btn-ajukan-booking">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Ajukan Booking
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Container Utama Grid Kalender (Full Height) -->
    <div class="gcal-body" style="height: calc(100vh - 70px);">
        <div class="gcal-days-header" id="gcalDaysHeader">
            <!-- Digenerate via JS -->
        </div>
        <div class="gcal-grid-scroll">
            <div class="gcal-grid" id="gcalGrid">
                <!-- Digenerate via JS -->
            </div>
        </div>
    </div>

    <!-- Modal Detail & Approval Peminjaman -->
    <div class="modal-overlay" id="detailBookingModal">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0;">Detail Peminjaman Ruangan</h2>
                <button class="modal-close" type="button" onclick="closeDetailBookingModal()">&times;</button>
            </div>
            <div class="modal-body" style="padding: 20px 24px; max-height: 80vh; overflow-y: auto;">
                <input type="hidden" id="detailBookingId">
                
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 16px; margin-bottom: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; gap: 10px;">
                        <div>
                            <span id="detailKodeRuangan" style="display: inline-block; background: #ede9fe; color: #7c3aed; font-size: 0.75rem; font-weight: 700; padding: 3px 10px; border-radius: 20px; margin-bottom: 4px;"></span>
                            <h3 id="detailNamaRuangan" style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #0f172a;"></h3>
                        </div>
                        <div id="detailStatusBadge"></div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 10px; font-size: 0.88rem; color: #334155; border-top: 1px solid #e2e8f0; padding-top: 12px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            <strong>Peminjam:</strong> <span id="detailNamaLengkap"></span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            <strong>Tanggal:</strong> <span id="detailTanggal"></span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <strong>Waktu:</strong> <span id="detailWaktu"></span>
                        </div>
                        <div style="display: flex; align-items: flex-start; gap: 8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" style="margin-top: 2px; flex-shrink:0;"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                            <strong>Keterangan:</strong> <span id="detailKeterangan" style="color: #475569;"></span>
                        </div>
                        <div id="detailAlasanContainer" style="display: none; background: #fef2f2; border-left: 3px solid #ef4444; padding: 8px 12px; border-radius: 6px; margin-top: 4px;">
                            <strong style="color: #991b1b;">Alasan Penolakan:</strong> <span id="detailAlasanPenolakan" style="color: #7f1d1d;"></span>
                        </div>
                    </div>
                </div>

                <!-- Panel Aksi Approval -->
                <div id="approvalActionPanel" style="display: none; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 14px; padding: 16px; margin-bottom: 12px;">
                    <h4 style="margin: 0 0 10px 0; font-size: 0.88rem; font-weight: 700; color: #166534; display: flex; align-items: center; gap: 6px;">
                        ⚡ Persetujuan Peminjaman (<span id="approvalRoleLabel"></span>)
                    </h4>
                    <div style="display: flex; gap: 10px;">
                        <button type="button" onclick="approveBookingAction()" style="flex: 1; background: #16a34a; color: #fff; border: none; padding: 10px 14px; border-radius: 10px; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            Setujui
                        </button>
                        <button type="button" onclick="toggleRejectInput()" style="flex: 1; background: #dc2626; color: #fff; border: none; padding: 10px 14px; border-radius: 10px; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            Tolak
                        </button>
                    </div>

                    <div id="rejectReasonBox" style="display: none; margin-top: 12px; border-top: 1px dashed #cbd5e1; padding-top: 12px;">
                        <label style="font-size: 0.8rem; font-weight: 600; color: #991b1b; display: block; margin-bottom: 6px;">Alasan Penolakan (Opsional):</label>
                        <textarea id="rejectReasonInput" rows="2" style="width: 100%; font-size: 0.85rem; padding: 8px 12px; border: 1px solid #fca5a5; border-radius: 8px; margin-bottom: 8px; box-sizing: border-box;" placeholder="Tuliskan alasan penolakan..."></textarea>
                        <button type="button" onclick="rejectBookingAction()" style="width: 100%; background: #991b1b; color: #fff; border: none; padding: 8px; border-radius: 8px; font-weight: 700; font-size: 0.8rem; cursor: pointer;">Konfirmasi Penolakan</button>
                    </div>
                </div>

                <!-- Panel Aksi Hapus Jadwal -->
                <div id="deleteActionPanel" style="display: none; margin-top: 8px;">
                    <button type="button" onclick="deleteBookingAction()" style="width: 100%; background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5; padding: 10px 14px; border-radius: 10px; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        Hapus Jadwal Peminjaman
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Data Pass -->
    <script>
        window.bookingData = <?= json_encode($jadwal_peminjaman ? $jadwal_peminjaman : []) ?: '[]' ?>;
        window.kategoriList = <?= json_encode($kategori ? $kategori : []) ?: '[]' ?>;
        window.ruanganList = <?= json_encode($ruangan ? $ruangan : []) ?: '[]' ?>;

        window.isLoggedIn = <?= $this->session->userdata('logged_in') ? 'true' : 'false' ?>;
        window.userRoleId = <?= json_encode($this->session->userdata('role_id')) ?>;

        window.ajukanBookingUrl = '<?= base_url('ajukan-booking') ?>';
        window.approveBookingUrl = '<?= base_url('dashboard/approve_booking') ?>';
        window.rejectBookingUrl = '<?= base_url('dashboard/reject_booking') ?>';
        window.deleteBookingUrl = '<?= base_url('dashboard/delete_booking') ?>';
        window.getUpdatedBookingsUrl = '<?= base_url('dashboard/get_updated_bookings') ?>';

        let currentWeekStart = new Date();
        currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay());
        let extraRowCounter = 0;
        let activeInputTarget = null;

        function renderCalendar() {
            renderHeaderAndDays();
            renderTimeGridAndEvents();
        }

        function renderHeaderAndDays() {
            const daysHeaderContainer = document.getElementById('gcalDaysHeader');
            const monthTitle = document.getElementById('gcalMonthTitle');
            const days = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];

            let monthName = currentWeekStart.toLocaleString('en-US', { month: 'long' });
            let year = currentWeekStart.getFullYear();
            monthTitle.innerText = `${monthName} ${year}`;

            const today = new Date();
            let headerHTML = '';

            for (let i = 0; i < 7; i++) {
                let dayDate = new Date(currentWeekStart);
                dayDate.setDate(currentWeekStart.getDate() + i);

                let isToday = (dayDate.toDateString() === today.toDateString()) ? 'active' : '';

                headerHTML += `
                    <div class="gcal-day-header">
                        <span class="gcal-day-name">${days[i]}</span>
                        <span class="gcal-day-num ${isToday}">${dayDate.getDate()}</span>
                    </div>
                `;
            }
            daysHeaderContainer.innerHTML = headerHTML;
        }

        function renderTimeGridAndEvents() {
            const gridContainer = document.getElementById('gcalGrid');

            let timeColHTML = `<div class="gcal-time-col">`;
            for (let i = 7; i <= 22; i++) {
                const hourStr = i.toString().padStart(2, '0') + ':00';
                timeColHTML += `<div class="gcal-time-label"><span>${hourStr}</span></div>`;
            }
            timeColHTML += `</div>`;

            let dayColsHTML = `<div class="gcal-day-cols">`;
            for (let i = 0; i < 7; i++) {
                let dayDate = new Date(currentWeekStart);
                dayDate.setDate(currentWeekStart.getDate() + i);
                const y = dayDate.getFullYear();
                const m = String(dayDate.getMonth() + 1).padStart(2, '0');
                const d = String(dayDate.getDate()).padStart(2, '0');
                let dateString = `${y}-${m}-${d}`;

                dayColsHTML += `<div class="gcal-day-col" id="col-${dateString}">`;
                dayColsHTML += generateEventsForDay(dateString);
                dayColsHTML += `</div>`;
            }
            dayColsHTML += `</div>`;

            gridContainer.innerHTML = timeColHTML + dayColsHTML;
        }

        function getStatusStyle(status) {
            const s = (status || '').toLowerCase();
            if (s === 'pending') {
                return { bg: '#f59e0b', border: '#d97706', badgeBg: '#fffbeb', badgeColor: '#b45309', dot: '#f59e0b', label: 'Menunggu Persetujuan' };
            } else if (s.includes('ka. ur')) {
                return { bg: '#10b981', border: '#059669', badgeBg: '#f0fdf4', badgeColor: '#166534', dot: '#22c55e', label: 'Disetujui Ka. Ur' };
            } else if (s.includes('laboran')) {
                return { bg: '#3b82f6', border: '#2563eb', badgeBg: '#eff6ff', badgeColor: '#1d4ed8', dot: '#3b82f6', label: 'Disetujui Laboran' };
            } else if (s.includes('admin')) {
                return { bg: '#8b5cf6', border: '#7c3aed', badgeBg: '#f5f3ff', badgeColor: '#6d28d9', dot: '#8b5cf6', label: 'Disetujui Admin' };
            } else if (s.includes('disetujui')) {
                return { bg: '#10b981', border: '#059669', badgeBg: '#f0fdf4', badgeColor: '#166534', dot: '#22c55e', label: 'Disetujui' };
            } else if (s === 'ditolak') {
                return { bg: '#ef4444', border: '#dc2626', badgeBg: '#fef2f2', badgeColor: '#991b1b', dot: '#ef4444', label: 'Ditolak' };
            } else if (s === 'selesai') {
                return { bg: '#64748b', border: '#475569', badgeBg: '#f8fafc', badgeColor: '#475569', dot: '#94a3b8', label: 'Selesai' };
            }
            return { bg: '#7c3aed', border: '#6d28d9', badgeBg: '#f5f3ff', badgeColor: '#6d28d9', dot: '#7c3aed', label: status };
        }

        function generateEventsForDay(targetDateStr) {
            if (typeof bookingData === 'undefined') return '';
            let eventsHTML = '';
            const pxPerHour = 48;

            let activeFilters = [];

            // Main Row 1
            const mainCrit = $('#mainCategorySelect').val();
            let mainVal = (mainCrit === 'status') ? $('#mainStatusValue').val() : $('#mainSearchInput').val();
            if (mainVal && mainVal.trim() !== '') {
                activeFilters.push({ criterion: mainCrit, value: mainVal.trim() });
            }

            // Extra Rows (2, 3, 4)
            $('.extra-filter-row').each(function() {
                const crit = $(this).find('.extra-cat-select').val();
                let val = (crit === 'status') ? $(this).find('.extra-status-value').val() : $(this).find('.extra-input-key').val();
                if (val && val.trim() !== '') {
                    activeFilters.push({ criterion: crit, value: val.trim() });
                }
            });

            const isFilteringActive = (activeFilters.length > 0);

            bookingData.forEach(booking => {
                if (booking.tanggal_mulai <= targetDateStr && booking.tanggal_selesai >= targetDateStr) {
                    
                    let isMatched = true;

                    activeFilters.forEach(f => {
                        if (!isMatched) return;

                        const q = f.value.toLowerCase();
                        if (f.criterion === 'kategori') {
                            const katName = (booking.nama_kategori || '').toLowerCase();
                            const kode = (booking.kode_ruangan || '').toLowerCase();
                            const namaR = (booking.nama_ruangan || '').toLowerCase();
                            if (!katName.includes(q) && !kode.includes(q) && !namaR.includes(q)) isMatched = false;
                        } else if (f.criterion === 'ruangan') {
                            const kode = (booking.kode_ruangan || '').toLowerCase();
                            const namaR = (booking.nama_ruangan || '').toLowerCase();
                            if (!kode.includes(q) && !namaR.includes(q)) isMatched = false;
                        } else if (f.criterion === 'status') {
                            const statusRaw = (booking.status || '').toLowerCase();
                            const statusLabel = getStatusStyle(booking.status).label.toLowerCase();
                            if (!statusRaw.includes(q) && !statusLabel.includes(q)) isMatched = false;
                        } else if (f.criterion === 'tanggal') {
                            if (!(booking.tanggal_mulai <= f.value && booking.tanggal_selesai >= f.value)) isMatched = false;
                        } else if (f.criterion === 'keyword') {
                            const kode = (booking.kode_ruangan || '').toLowerCase();
                            const namaR = (booking.nama_ruangan || '').toLowerCase();
                            const namaL = (booking.nama_lengkap || '').toLowerCase();
                            const ket = (booking.keterangan || '').toLowerCase();
                            const katName = (booking.nama_kategori || '').toLowerCase();
                            const statusRaw = (booking.status || '').toLowerCase();
                            const statusLabel = getStatusStyle(booking.status).label.toLowerCase();
                            if (!kode.includes(q) && !namaR.includes(q) && !namaL.includes(q) && !ket.includes(q) && !katName.includes(q) && !statusRaw.includes(q) && !statusLabel.includes(q)) {
                                isMatched = false;
                            }
                        }
                    });

                    let opacityStyle = (isFilteringActive && !isMatched) ? 'opacity: 0.12; filter: grayscale(95%); scale(0.97);' : 'opacity: 1;';
                    let highlightStyle = (isFilteringActive && isMatched) ? 'box-shadow: 0 0 0 3px #ea580c; z-index: 25;' : '';

                    let startHour = 0, startMin = 0, endHour = 24, endMin = 0;
                    if (booking.tanggal_mulai === targetDateStr) {
                        const p = booking.jam_mulai.split(':');
                        startHour = parseInt(p[0]);
                        startMin  = parseInt(p[1]);
                    }
                    if (booking.tanggal_selesai === targetDateStr) {
                        const p = booking.jam_selesai.split(':');
                        endHour = parseInt(p[0]);
                        endMin  = parseInt(p[1]);
                    }

                    const gridStartHour = 7;
                    const topPx    = ((startHour - gridStartHour + 1) + startMin / 60) * pxPerHour;
                    const endPx    = ((endHour   - gridStartHour + 1) + endMin   / 60) * pxPerHour;
                    const heightPx = Math.max(endPx - topPx, 24);

                    const st = getStatusStyle(booking.status);
                    const timeLabel = `${startHour}:${startMin.toString().padStart(2,'0')} - ${endHour}:${endMin.toString().padStart(2,'0')}`;

                    eventsHTML += `
                        <div class="gcal-event" onclick="openDetailBookingModal(${booking.id})" 
                             style="top:${topPx}px; height:${heightPx}px; background:${st.bg}; border-left:3px solid ${st.border}; cursor:pointer; ${opacityStyle} ${highlightStyle}"
                             title="${booking.nama_ruangan} — ${booking.nama_lengkap} (${st.label})">
                            <div class="gcal-event-title">${booking.nama_ruangan}</div>
                            <div class="gcal-event-time">${timeLabel}</div>
                            <div class="gcal-event-status">${st.label}</div>
                        </div>
                    `;
                }
            });

            return eventsHTML;
        }

        // ===== CUSTOM DROPDOWN ENGINE FOR CATEGORY SELECTOR =====
        function toggleCatDropdown(prefix, e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }
            const wrap = (prefix === 'main') ? $('#mainCatWrap') : $(`#extraCatWrap_${prefix}`);
            const row = (prefix === 'main') ? null : $(`#extraRow_${prefix}`);
            const wasOpen = wrap.hasClass('open');
            
            $('.custom-cat-dropdown, .custom-status-dropdown').removeClass('open');
            $('.extra-filter-row').removeClass('dropdown-active');
            
            if (!wasOpen) {
                wrap.addClass('open');
                if (row) row.addClass('dropdown-active');
            }
        }

        function selectCatOption(prefix, val, label, icon) {
            let hiddenInput, labelSpan, menu;
            if (prefix === 'main') {
                hiddenInput = $('#mainCategorySelect');
                labelSpan = $('#mainCatLabel');
                menu = $('#mainCatMenu');
                $('#mainCatWrap').removeClass('open');
            } else {
                hiddenInput = $(`#extraCatSelect_${prefix}`);
                labelSpan = $(`#extraCatLabel_${prefix}`);
                menu = $(`#extraCatMenu_${prefix}`);
                $(`#extraCatWrap_${prefix}`).removeClass('open');
                $(`#extraRow_${prefix}`).removeClass('dropdown-active');
            }

            hiddenInput.val(val);
            labelSpan.text(label);

            menu.find('.cat-option').removeClass('active');
            menu.find(`.cat-option[data-val="${val}"]`).addClass('active');

            if (prefix === 'main') {
                updateMainPlaceholder();
            } else {
                updateExtraPlaceholder(prefix);
            }
        }

        // ===== CUSTOM DROPDOWN ENGINE FOR STATUS SELECTOR =====
        function toggleStatusDropdown(prefix, e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }
            const wrap = (prefix === 'main') ? $('#mainStatusWrap') : $(`#extraStatusWrap_${prefix}`);
            const row = (prefix === 'main') ? null : $(`#extraRow_${prefix}`);
            const wasOpen = wrap.hasClass('open');

            $('.custom-cat-dropdown, .custom-status-dropdown').removeClass('open');
            $('.extra-filter-row').removeClass('dropdown-active');

            if (!wasOpen) {
                wrap.addClass('open');
                if (row) row.addClass('dropdown-active');
            }
        }

        function selectStatusOption(prefix, val, label, color) {
            let hiddenInput, labelSpan, menu;
            if (prefix === 'main') {
                hiddenInput = $('#mainStatusValue');
                labelSpan = $('#mainStatusLabel');
                menu = $('#mainStatusMenu');
                $('#mainStatusWrap').removeClass('open');
            } else {
                hiddenInput = $(`#extraStatusValue_${prefix}`);
                labelSpan = $(`#extraStatusLabel_${prefix}`);
                menu = $(`#extraStatusMenu_${prefix}`);
                $(`#extraStatusWrap_${prefix}`).removeClass('open');
                $(`#extraRow_${prefix}`).removeClass('dropdown-active');
            }

            hiddenInput.val(val);
            labelSpan.html(`<span class="status-dot" style="background: ${color};"></span> ${label}`);

            menu.find('.status-option').removeClass('active');
            menu.find(`.status-option[data-val="${val}"]`).addClass('active');

            renderCalendar();
        }

        // ===== SETUP FLATPICKR CALENDAR POPUP WHEN LOMPAT TANGGAL IS ACTIVE =====
        function setupDatePickerIfNeeded(inputEl, crit) {
            if (inputEl._flatpickr) {
                inputEl._flatpickr.destroy();
            }

            if (crit === 'tanggal') {
                flatpickr(inputEl, {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "j F Y",
                    altInputClass: inputEl.className + ' flatpickr-custom-trigger',
                    allowInput: false,
                    defaultDate: "today",
                    onChange: function(selectedDates, dateStr) {
                        if (dateStr) {
                            onTanggalFilterChanged(dateStr);
                        }
                    }
                });
            }
        }

        // ===== ALWAYS CLICKABLE TOGGLE / ADD FILTER ROW LOGIC =====
        function toggleOrAddFilterRow(e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }

            const extraCard = $('#extraRowsCard');
            const hasExtraRows = $('.extra-filter-row').length > 0;

            // If extra filter rows exist and card is hidden, OPEN IT!
            if (hasExtraRows && extraCard.is(':hidden')) {
                $('#mainAutocompleteList').hide();
                extraCard.css({'display': 'block'}).show();
                return;
            }

            // If maximum 4 filter rows reached, toggle open / close!
            if ($('.extra-filter-row').length + 1 >= 4) {
                if (extraCard.is(':visible')) {
                    extraCard.hide();
                } else {
                    $('#mainAutocompleteList').hide();
                    extraCard.css({'display': 'block'}).show();
                }
                return;
            }

            // Otherwise, add a new row and make sure card is shown
            addAdditionalFilterRow(e);
        }

        function onMainInputFocused() {
            activeInputTarget = document.getElementById('mainSearchInput');
            if ($('.extra-filter-row').length > 0) {
                $('#extraRowsCard').css({'display': 'block'}).show();
            }
        }

        function updateMainPlaceholder() {
            const crit = $('#mainCategorySelect').val();
            const textContainer = $('#mainValueContainer');
            const statusWrap = $('#mainStatusWrap');
            const inputEl = document.getElementById('mainSearchInput');
            
            if (crit === 'status') {
                textContainer.hide();
                statusWrap.css({'display': 'flex'}).show();
            } else {
                statusWrap.hide();
                textContainer.css({'display': 'flex'}).show();
                
                let ph = "Cari ruangan, peminjam, kode (key)...";
                if (crit === 'kategori') ph = "Cari nama kategori / ruangan (misal: Lab Batik)...";
                else if (crit === 'ruangan') ph = "Cari kode ruangan (misal: IK.01.10)...";
                else if (crit === 'tanggal') ph = "Klik untuk pilih tanggal di kalender...";
                $(inputEl).attr('placeholder', ph);

                setupDatePickerIfNeeded(inputEl, crit);
            }

            handleUnifiedMultiSearch();
        }

        function addAdditionalFilterRow(e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }
            $('#mainAutocompleteList').hide();

            const currentRows = $('.extra-filter-row').length + 1;
            if (currentRows >= 4) {
                Swal.fire({
                    icon: 'info',
                    title: 'Maksimal 4 Filter',
                    text: 'Maksimal 4 kriteria filter pencarian yang dapat aktif.',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            extraRowCounter++;
            const rowId = extraRowCounter;

            const availableCriteria = ['kategori', 'ruangan', 'status', 'tanggal', 'keyword'];
            const usedCriteria = [$('#mainCategorySelect').val()];
            $('.extra-cat-select').each(function() { usedCriteria.push($(this).val()); });
            const defaultCrit = availableCriteria.find(c => !usedCriteria.includes(c)) || 'kategori';

            let defaultLabel = "Kategori Ruangan";
            let defaultIcon = "📁";
            if (defaultCrit === 'ruangan') { defaultLabel = "Pilih Ruangan"; defaultIcon = "🏢"; }
            else if (defaultCrit === 'status') { defaultLabel = "Status Peminjaman"; defaultIcon = "⚡"; }
            else if (defaultCrit === 'tanggal') { defaultLabel = "Lompat Tanggal"; defaultIcon = "📅"; }
            else if (defaultCrit === 'keyword') { defaultLabel = "Key / Kata Kunci"; defaultIcon = "🔑"; }

            let ph = "Cari nama kategori (misal: Lab Batik)...";
            if (defaultCrit === 'ruangan') ph = "Cari kode ruangan (misal: IK.01.10)...";
            else if (defaultCrit === 'tanggal') ph = "Klik untuk pilih tanggal di kalender...";
            else if (defaultCrit === 'keyword') ph = "Ketik kata kunci pencarian (key)...";

            const rowHTML = `
                <div class="extra-filter-row" id="extraRow_${rowId}">
                    <div class="unified-search-pill" style="flex:1; width:auto;">
                        
                        <div class="custom-cat-dropdown" id="extraCatWrap_${rowId}">
                            <button type="button" class="custom-cat-trigger" onclick="toggleCatDropdown(${rowId}, event)">
                                <span id="extraCatLabel_${rowId}">${defaultLabel}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </button>

                            <input type="hidden" id="extraCatSelect_${rowId}" class="extra-cat-select" value="${defaultCrit}">

                            <div class="custom-cat-menu" id="extraCatMenu_${rowId}">
                                <div class="cat-option ${defaultCrit === 'kategori' ? 'active' : ''}" data-val="kategori" onclick="selectCatOption(${rowId}, 'kategori', 'Kategori Ruangan', '📁')">
                                    <span>📁</span> Kategori Ruangan
                                </div>
                                <div class="cat-option ${defaultCrit === 'ruangan' ? 'active' : ''}" data-val="ruangan" onclick="selectCatOption(${rowId}, 'ruangan', 'Pilih Ruangan', '🏢')">
                                    <span>🏢</span> Pilih Ruangan
                                </div>
                                <div class="cat-option ${defaultCrit === 'status' ? 'active' : ''}" data-val="status" onclick="selectCatOption(${rowId}, 'status', 'Status Peminjaman', '⚡')">
                                    <span>⚡</span> Status Peminjaman
                                </div>
                                <div class="cat-option ${defaultCrit === 'tanggal' ? 'active' : ''}" data-val="tanggal" onclick="selectCatOption(${rowId}, 'tanggal', 'Lompat Tanggal', '📅')">
                                    <span>📅</span> Lompat Tanggal
                                </div>
                                <div class="cat-option ${defaultCrit === 'keyword' ? 'active' : ''}" data-val="keyword" onclick="selectCatOption(${rowId}, 'keyword', 'Key / Kata Kunci', '🔑')">
                                    <span>🔑</span> Key / Kata Kunci
                                </div>
                            </div>
                        </div>

                        <div class="unified-divider"></div>

                        <!-- Text Search Container for Extra Row -->
                        <div style="position:relative; flex:1; display:${defaultCrit === 'status' ? 'none' : 'flex'}; align-items:center;" id="extraValueContainer_${rowId}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.5" style="position: absolute; left: 8px; pointer-events: none;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            <input type="text" id="extraInput_${rowId}" class="unified-input-key extra-input-key" placeholder="${ph}" oninput="handleUnifiedMultiSearch(this)" onfocus="activeInputTarget = this" autocomplete="off">
                        </div>

                        <!-- Custom Status Selector Dropdown for Extra Row -->
                        <div class="custom-status-dropdown" id="extraStatusWrap_${rowId}" style="display: ${defaultCrit === 'status' ? 'flex' : 'none'}; flex: 1;">
                            <button type="button" class="custom-status-trigger" onclick="toggleStatusDropdown(${rowId}, event)">
                                <span id="extraStatusLabel_${rowId}" style="display: flex; align-items: center; gap: 6px;">
                                    <span class="status-dot" style="background: #94a3b8;"></span> Semua Status
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </button>
                            
                            <input type="hidden" id="extraStatusValue_${rowId}" class="extra-status-value" value="">

                            <div class="custom-status-menu" id="extraStatusMenu_${rowId}">
                                <div class="status-option active" data-val="" onclick="selectStatusOption(${rowId}, '', 'Semua Status', '#94a3b8')">
                                    <span class="status-dot" style="background: #94a3b8;"></span> Semua Status
                                </div>
                                <div class="status-option" data-val="pending" onclick="selectStatusOption(${rowId}, 'pending', 'Menunggu Persetujuan', '#f59e0b')">
                                    <span class="status-dot" style="background: #f59e0b;"></span> Menunggu Persetujuan
                                </div>
                                <div class="status-option" data-val="disetujui" onclick="selectStatusOption(${rowId}, 'disetujui', 'Disetujui', '#10b981')">
                                    <span class="status-dot" style="background: #10b981;"></span> Disetujui
                                </div>
                                <div class="status-option" data-val="ditolak" onclick="selectStatusOption(${rowId}, 'ditolak', 'Ditolak', '#ef4444')">
                                    <span class="status-dot" style="background: #ef4444;"></span> Ditolak
                                </div>
                                <div class="status-option" data-val="selesai" onclick="selectStatusOption(${rowId}, 'selesai', 'Selesai', '#64748b')">
                                    <span class="status-dot" style="background: #64748b;"></span> Selesai
                                </div>
                            </div>
                        </div>

                    </div>

                    <button type="button" onclick="removeExtraFilterRow(${rowId}, event)" class="btn-remove-extra-row" title="Hapus filter ini">&times;</button>
                </div>
            `;

            $('#additionalFilterRowsContainer').append(rowHTML);
            $('#extraRowsCard').css({'display': 'block'}).show();
            updateFilterCountBadge();
            
            const newEl = document.getElementById(`extraInput_${rowId}`);
            if (newEl) setupDatePickerIfNeeded(newEl, defaultCrit);

            renderCalendar();
        }

        function updateExtraPlaceholder(rowId) {
            const row = $(`#extraRow_${rowId}`);
            const crit = row.find('.extra-cat-select').val();
            const textContainer = $(`#extraValueContainer_${rowId}`);
            const statusWrap = $(`#extraStatusWrap_${rowId}`);
            const inputEl = document.getElementById(`extraInput_${rowId}`);

            if (crit === 'status') {
                textContainer.hide();
                statusWrap.css({'display': 'flex'}).show();
            } else {
                statusWrap.hide();
                textContainer.css({'display': 'flex'}).show();

                let ph = "Ketik kata kunci pencarian (key)...";
                if (crit === 'kategori') ph = "Cari nama kategori (misal: Lab Batik)...";
                else if (crit === 'ruangan') ph = "Cari kode ruangan (misal: IK.01.10)...";
                else if (crit === 'tanggal') ph = "Klik untuk pilih tanggal di kalender...";
                if (inputEl) {
                    $(inputEl).attr('placeholder', ph);
                    setupDatePickerIfNeeded(inputEl, crit);
                }
            }

            renderCalendar();
        }

        function removeExtraFilterRow(rowId, e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }

            $(`#extraRow_${rowId}`).remove();
            
            if ($('.extra-filter-row').length === 0) {
                $('#extraRowsCard').hide();
            } else {
                $('#extraRowsCard').css({'display': 'block'}).show();
            }

            updateFilterCountBadge();
            renderCalendar();
        }

        function updateFilterCountBadge() {
            const total = $('.extra-filter-row').length + 1;
            $('#filterCountBadge').text(`${total}/4`);

            // ALWAYS keep pointer-events: auto so user can click to re-open/close the panel!
            $('#standaloneAddBtn').css({ opacity: 1, pointerEvents: 'auto' });
            
            if (total >= 4) {
                $('#standaloneAddBtn').attr('title', 'Buka / Tutup Daftar Filter (Maksimal 4 Active Filter)');
            } else {
                $('#standaloneAddBtn').attr('title', 'Tambah Filter Baru / Tampilkan Filter');
            }
        }

        // ===== CONTEXT-AWARE AUTOCOMPLETE SEARCH ENGINE WITH DEPENDENT ROOM FILTERING =====
        function handleUnifiedMultiSearch(inputEl) {
            if (inputEl) activeInputTarget = inputEl;
            const mainInput = document.getElementById('mainSearchInput');
            const qMain = mainInput ? mainInput.value.trim().toLowerCase() : '';
            const clearBtn = document.getElementById('clearMainSearchBtn');
            if (clearBtn) clearBtn.style.display = (qMain.length > 0) ? 'block' : 'none';

            renderCalendar();

            const autoList = document.getElementById('mainAutocompleteList');
            const targetInput = inputEl || activeInputTarget || mainInput;
            const targetVal = targetInput ? targetInput.value.trim().toLowerCase() : '';

            if (!targetVal || !targetInput) {
                if (autoList) autoList.style.display = 'none';
                return;
            }

            // Detect current criterion for this active row
            const parentRow = $(targetInput).closest('.unified-search-pill, .extra-filter-row');
            let currentCrit = 'keyword';
            if (parentRow.length) {
                const catSelect = parentRow.find('.extra-cat-select, #mainCategorySelect');
                if (catSelect.length) currentCrit = catSelect.val();
            }

            // Skip autocomplete for 'tanggal' or 'status'
            if (currentCrit === 'tanggal' || currentCrit === 'status') {
                if (autoList) autoList.style.display = 'none';
                return;
            }

            // Check if ANY row has selected 'kategori'
            let activeCategoryFilterVal = '';
            const mainCrit = $('#mainCategorySelect').val();
            const mainValText = $('#mainSearchInput').val().trim().toLowerCase();
            if (mainCrit === 'kategori' && mainValText) {
                activeCategoryFilterVal = mainValText;
            }
            $('.extra-filter-row').each(function() {
                const crit = $(this).find('.extra-cat-select').val();
                const val = $(this).find('.extra-input-key').val().trim().toLowerCase();
                if (crit === 'kategori' && val) {
                    activeCategoryFilterVal = val;
                }
            });

            // Position autoList directly relative to targetInput
            const rect = targetInput.getBoundingClientRect();
            const containerRect = document.querySelector('.search-filter-container').getBoundingClientRect();
            const topOffset = rect.bottom - containerRect.top + 6;
            const leftOffset = rect.left - containerRect.left;

            $(autoList).css({
                'top': topOffset + 'px',
                'left': leftOffset + 'px',
                'width': Math.max(rect.width, 440) + 'px'
            });

            let suggestions = [];

            // 1. IF CURRENT ROW IS 'kategori' (Kategori Ruangan): ONLY SUGGEST PURE NAMES! NO CODES!
            if (currentCrit === 'kategori') {
                if (window.kategoriList) {
                    window.kategoriList.forEach(k => {
                        const namaKat = k.nama_kategori || '';
                        if (namaKat.toLowerCase().includes(targetVal)) {
                            suggestions.push({
                                type: 'kategori',
                                title: namaKat,
                                subtitle: 'Kategori Ruangan',
                                queryVal: namaKat // PURE NAME!
                            });
                        }
                    });
                }
                if (window.ruanganList) {
                    window.ruanganList.forEach(r => {
                        const nama = r.nama_ruangan || '';
                        if (nama.toLowerCase().includes(targetVal)) {
                            suggestions.push({
                                type: 'kategori',
                                title: nama,
                                subtitle: 'Nama Ruangan',
                                queryVal: nama // PURE NAME!
                            });
                        }
                    });
                }
            }

            // 2. IF CURRENT ROW IS 'ruangan' (Pilih Ruangan): SUGGEST ROOM CODES (DEPENDENT ON KATEGORI!)
            else if (currentCrit === 'ruangan') {
                if (window.ruanganList) {
                    window.ruanganList.forEach(r => {
                        const nama = r.nama_ruangan || '';
                        const kode = r.kode_ruangan || '';
                        const katName = r.nama_kategori || '';

                        // DEPENDENT FILTER: Only show room codes matching activeCategoryFilterVal if set!
                        if (activeCategoryFilterVal) {
                            if (!nama.toLowerCase().includes(activeCategoryFilterVal) &&
                                !katName.toLowerCase().includes(activeCategoryFilterVal) &&
                                !kode.toLowerCase().includes(activeCategoryFilterVal)) {
                                return; // Skip rooms outside the selected category!
                            }
                        }

                        if (nama.toLowerCase().includes(targetVal) || kode.toLowerCase().includes(targetVal)) {
                            suggestions.push({
                                type: 'ruangan',
                                title: kode ? `${kode} — ${nama}` : nama,
                                subtitle: `Kode Ruangan: ${kode || '-'}`,
                                queryVal: kode ? kode : nama // FILLS ROOM CODE!
                            });
                        }
                    });
                }
            }

            // 3. KEYWORD OR OTHER CRITERIA
            else {
                if (window.ruanganList) {
                    window.ruanganList.forEach(r => {
                        const nama = r.nama_ruangan || '';
                        const kode = r.kode_ruangan || '';
                        const katName = r.nama_kategori || '';

                        if (activeCategoryFilterVal) {
                            if (!nama.toLowerCase().includes(activeCategoryFilterVal) &&
                                !katName.toLowerCase().includes(activeCategoryFilterVal) &&
                                !kode.toLowerCase().includes(activeCategoryFilterVal)) {
                                return;
                            }
                        }

                        if (nama.toLowerCase().includes(targetVal) || kode.toLowerCase().includes(targetVal)) {
                            suggestions.push({
                                type: 'ruangan',
                                title: kode ? `${kode} — ${nama}` : nama,
                                subtitle: `Ruangan • ${nama}`,
                                queryVal: nama
                            });
                        }
                    });
                }
                if (window.kategoriList) {
                    window.kategoriList.forEach(k => {
                        const namaKat = k.nama_kategori || '';
                        if (namaKat.toLowerCase().includes(targetVal)) {
                            suggestions.push({
                                type: 'kategori',
                                title: namaKat,
                                subtitle: 'Kategori Ruangan',
                                queryVal: namaKat
                            });
                        }
                    });
                }
                if (window.bookingData) {
                    window.bookingData.forEach(b => {
                        const peminjam = b.nama_lengkap || '';
                        if (peminjam.toLowerCase().includes(targetVal)) {
                            suggestions.push({
                                type: 'peminjam',
                                title: peminjam,
                                subtitle: `Peminjam • ${b.nama_ruangan}`,
                                bookingId: b.id,
                                tgl: b.tanggal_mulai,
                                queryVal: peminjam
                            });
                        }
                    });
                }
            }

            if (suggestions.length === 0) {
                autoList.innerHTML = `
                    <div style="padding: 10px; text-align: center; color: #94a3b8; font-size: 0.82rem; font-weight: 600;">
                        🔍 Tidak ditemukan hasil yang cocok untuk "${escapeHtml(targetVal)}"
                    </div>
                `;
                autoList.style.display = 'block';
                return;
            }

            const uniqueSuggestions = [];
            const seenTitles = new Set();
            for (const item of suggestions) {
                if (!seenTitles.has(item.title.toLowerCase())) {
                    seenTitles.add(item.title.toLowerCase());
                    uniqueSuggestions.push(item);
                }
                if (uniqueSuggestions.length >= 8) break;
            }

            let html = '';
            uniqueSuggestions.forEach(s => {
                let icon = '🏢';
                if (s.type === 'kategori') icon = '📁';
                else if (s.type === 'peminjam') icon = '👤';

                html += `
                    <div class="search-autocomplete-item" onclick="applySuggestion(event, '${escapeHtml(s.queryVal)}', ${s.bookingId || 'null'}, '${s.tgl || ''}')">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 0.9rem;">${icon}</span>
                                <div>
                                    <strong style="font-size: 0.86rem; color: #0f172a;">${highlightMatchText(s.title, targetVal)}</strong>
                                    <span style="font-size: 0.74rem; color: #64748b; margin-left: 6px;">${s.subtitle}</span>
                                </div>
                            </div>
                            <span style="font-size: 0.75rem; color: #ea580c; font-weight: 700;">↵</span>
                        </div>
                    </div>
                `;
            });

            autoList.innerHTML = html;
            autoList.style.display = 'block';
        }

        function applySuggestion(e, queryVal, bookingId, targetDateStr) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }

            const targetInput = activeInputTarget || document.getElementById('mainSearchInput');
            if (targetInput) targetInput.value = queryVal;

            document.getElementById('mainAutocompleteList').style.display = 'none';

            // Ensure #extraRowsCard stays visible if extra filter rows are active!
            if ($('.extra-filter-row').length > 0) {
                $('#extraRowsCard').css({'display': 'block'}).show();
            }

            if (bookingId && targetDateStr) {
                selectSearchResult(bookingId, targetDateStr);
            } else {
                renderCalendar();
            }
        }

        function onTanggalFilterChanged(tglV) {
            if (tglV) {
                const parts = tglV.split('-');
                if (parts.length === 3) {
                    const targetDate = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
                    currentWeekStart = new Date(targetDate);
                    currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay());
                    renderCalendar();
                }
            }
        }

        function resetHeaderMultiSearch() {
            $('#additionalFilterRowsContainer').empty();
            extraRowCounter = 0;
            $('#extraRowsCard').hide();
            selectCatOption('main', 'keyword', 'Key / Kata Kunci', '🔑');
            selectStatusOption('main', '', 'Semua Status', '#94a3b8');
            updateFilterCountBadge();
            goToToday();
        }

        function clearMainSearch() {
            const input = document.getElementById('mainSearchInput');
            if (input) input.value = '';
            handleUnifiedMultiSearch();
        }

        function highlightMatchText(text, query) {
            if (!query) return escapeHtml(text);
            const escapedQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            const regex = new RegExp(`(${escapedQuery})`, 'gi');
            return escapeHtml(text).replace(regex, '<span class="match-highlight">$1</span>');
        }

        function escapeHtml(str) {
            return (str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        function selectSearchResult(bookingId, targetDateStr) {
            document.getElementById('mainAutocompleteList').style.display = 'none';

            if (targetDateStr) {
                const parts = targetDateStr.split('-');
                if (parts.length === 3) {
                    const targetDate = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
                    currentWeekStart = new Date(targetDate);
                    currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay());
                    renderCalendar();
                }
            }

            openDetailBookingModal(bookingId);
        }

        // Close popovers on click outside
        document.addEventListener('click', function(e) {
            const wrap = document.querySelector('.search-filter-container');
            if (wrap && !wrap.contains(e.target) && !$(e.target).closest('.flatpickr-calendar').length) {
                const autoList = document.getElementById('mainAutocompleteList');
                const extraCard = document.getElementById('extraRowsCard');
                const pill = document.getElementById('unifiedSearchPill');
                if (autoList) autoList.style.display = 'none';
                if (extraCard) extraCard.style.display = 'none';
                if (pill) pill.classList.remove('active');
            }

            // Close custom dropdown menus on click outside
            if (!$(e.target).closest('.custom-cat-dropdown, .custom-status-dropdown, .flatpickr-calendar').length) {
                $('.custom-cat-dropdown, .custom-status-dropdown').removeClass('open');
                $('.extra-filter-row').removeClass('dropdown-active');
            }
        });

        function nextWeek() { currentWeekStart.setDate(currentWeekStart.getDate() + 7); renderCalendar(); }
        function prevWeek() { currentWeekStart.setDate(currentWeekStart.getDate() - 7); renderCalendar(); }
        function goToToday() { currentWeekStart = new Date(); currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay()); renderCalendar(); }

        function openDetailBookingModal(id) {
            if (typeof bookingData === 'undefined') return;
            const booking = bookingData.find(b => parseInt(b.id) === parseInt(id));
            if (!booking) return;

            document.getElementById('detailBookingId').value = booking.id;
            document.getElementById('detailKodeRuangan').innerText = booking.kode_ruangan || '';
            document.getElementById('detailNamaRuangan').innerText = booking.nama_ruangan || '';
            document.getElementById('detailNamaLengkap').innerText = booking.nama_lengkap || '-';

            let tglStr = booking.tanggal_mulai;
            if (booking.tanggal_selesai && booking.tanggal_selesai !== booking.tanggal_mulai) {
                tglStr += ' s/d ' + booking.tanggal_selesai;
            }
            document.getElementById('detailTanggal').innerText = tglStr;

            const jMulai = booking.jam_mulai ? booking.jam_mulai.substring(0, 5) : '00:00';
            const jSelesai = booking.jam_selesai ? booking.jam_selesai.substring(0, 5) : '00:00';
            document.getElementById('detailWaktu').innerText = jMulai + ' - ' + jSelesai;
            document.getElementById('detailKeterangan').innerText = booking.keterangan || '-';

            const st = getStatusStyle(booking.status);
            document.getElementById('detailStatusBadge').innerHTML = `
                <span style="display:inline-flex; align-items:center; gap:6px; background:${st.badgeBg}; color:${st.badgeColor}; border-radius:999px; padding:5px 13px; font-size:0.76rem; font-weight:700; white-space:nowrap;">
                    <span style="width:7px;height:7px;border-radius:50%;background:${st.dot};flex-shrink:0;"></span>
                    ${st.label}
                </span>
            `;

            const alasBox = document.getElementById('detailAlasanContainer');
            if (booking.status === 'Ditolak' && booking.alasan_penolakan) {
                document.getElementById('detailAlasanPenolakan').innerText = booking.alasan_penolakan;
                alasBox.style.display = 'block';
            } else {
                alasBox.style.display = 'none';
            }

            const roleId = parseInt(window.userRoleId);
            const approvePanel = document.getElementById('approvalActionPanel');
            const deletePanel = document.getElementById('deleteActionPanel');
            const rejectBox = document.getElementById('rejectReasonBox');
            if (rejectBox) rejectBox.style.display = 'none';

            const isAuthorized = [1, 2, 3].includes(roleId);
            const statusLower = (booking.status || '').toLowerCase();

            // Status yang bisa diapprove:
            // 1. Pending (untuk Admin, Laboran, Ka. Ur)
            // 2. Disetujui Laboran (bisa di-approve / difinalisasi oleh Ka. Ur dan Admin)
            const canApprove = (
                statusLower === 'pending' ||
                ((roleId === 3 || roleId === 1) && statusLower.includes('laboran'))
            );

            if (isAuthorized && canApprove) {
                let roleName = 'Admin';
                if (roleId === 3) roleName = 'Ka. Ur';
                else if (roleId === 2) roleName = 'Laboran';

                document.getElementById('approvalRoleLabel').innerText = roleName;
                approvePanel.style.display = 'block';
            } else {
                approvePanel.style.display = 'none';
            }

            if (isAuthorized && deletePanel) {
                deletePanel.style.display = 'block';
            } else if (deletePanel) {
                deletePanel.style.display = 'none';
            }

            document.getElementById('detailBookingModal').classList.add('show');
        }

        function closeDetailBookingModal() {
            document.getElementById('detailBookingModal').classList.remove('show');
        }

        function approveBookingAction() {
            const id = document.getElementById('detailBookingId').value;
            if (!id) return;

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
                if (result.isConfirmed) {
                    fetch(window.approveBookingUrl + '/' + id, { method: 'POST' })
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire('Disetujui!', data.message, 'success');
                            closeDetailBookingModal();
                            reloadBookingData();
                        } else {
                            Swal.fire('Gagal', data.message, 'error');
                        }
                    }).catch(err => Swal.fire('Error', 'Terjadi kesalahan pada server', 'error'));
                }
            });
        }

        function toggleRejectInput() {
            const box = document.getElementById('rejectReasonBox');
            box.style.display = (box.style.display === 'none') ? 'block' : 'none';
        }

        function rejectBookingAction() {
            const id = document.getElementById('detailBookingId').value;
            const alasan = document.getElementById('rejectReasonInput').value;
            if (!id) return;

            const formData = new FormData();
            formData.append('alasan_penolakan', alasan);

            fetch(window.rejectBookingUrl + '/' + id, { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('Ditolak', data.message, 'success');
                    closeDetailBookingModal();
                    reloadBookingData();
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            }).catch(err => Swal.fire('Error', 'Terjadi kesalahan pada server', 'error'));
        }

        function deleteBookingAction() {
            const id = document.getElementById('detailBookingId').value;
            if (!id) return;

            Swal.fire({
                title: 'Hapus Jadwal',
                text: 'Apakah Anda yakin ingin menghapus jadwal peminjaman me secara permanen?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(window.deleteBookingUrl + '/' + id, { method: 'POST' })
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire('Terhapus!', data.message, 'success');
                            closeDetailBookingModal();
                            reloadBookingData();
                        } else {
                            Swal.fire('Gagal', data.message, 'error');
                        }
                    }).catch(err => Swal.fire('Error', 'Terjadi kesalahan pada server', 'error'));
                }
            });
        }

        function reloadBookingData() {
            fetch(window.getUpdatedBookingsUrl)
            .then(r => r.json())
            .then(data => {
                window.bookingData = data;
                renderCalendar();
            }).catch(err => console.error(err));
        }

        $(document).ready(function() {
            renderCalendar();
        });
    </script>

</body>
</html>