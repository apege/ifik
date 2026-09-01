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
            padding: 0 24px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            height: 68px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            gap: 12px;
        }

        .gcal-header-left {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .header-left-pane {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .header-pane-enter {
            animation: paneFadeSlideIn 0.26s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        @keyframes paneFadeSlideIn {
            from {
                opacity: 0;
                transform: translateY(-4px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .view-mode-container {
            animation: viewFadeIn 0.28s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        @keyframes viewFadeIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .gcal-btn-today {
            padding: 5px 10px;
            font-size: 0.78rem;
            font-weight: 700;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            cursor: pointer;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .btn-back-home {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: #64748b;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.78rem;
            padding: 6px 12px;
            border-radius: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
            min-width: max-content;
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
            gap: 5px;
            background: var(--primary);
            color: #ffffff;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.78rem;
            padding: 6px 14px;
            border-radius: 8px;
            box-shadow: 0 4px 14px rgba(234, 88, 12, 0.3);
            transition: all 0.2s ease;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
            min-width: max-content;
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
            gap: 8px;
            position: relative;
            flex: 1 1 auto;
            max-width: 480px;
            min-width: 320px;
        }

        .unified-search-pill {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            height: 42px;
            padding: 2px 6px 2px 8px;
            width: 100%;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
            position: relative;
        }
        .unified-search-pill:focus-within {
            border-color: #ea580c;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.12);
        }

        /* ===== CUSTOM STYLED CATEGORY DROPDOWN ===== */
        .custom-cat-dropdown {
            position: relative;
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
            padding: 5px 8px;
            border-radius: 8px;
            outline: none;
            transition: background 0.15s ease;
            white-space: nowrap;
        }
        .custom-cat-trigger:hover {
            background: #f1f5f9;
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
            z-index: 100030 !important;
            padding: 6px;
            min-width: 190px;
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
            padding: 0 12px;
            border-radius: 12px;
            font-size: 0.84rem;
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
        }

        /* DEDICATED AUTOCOMPLETE LIST (Floating Suggestions ONLY) */
        #mainAutocompleteList {
            display: none;
            position: fixed;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 20px 45px rgba(0,0,0,0.18);
            z-index: 99999;
            max-height: 280px;
            overflow-y: auto;
            padding: 6px;
        }

        .autocomplete-item {
            padding: 9px 12px;
            border-radius: 10px;
            font-size: 0.82rem;
            color: #334155;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.15s ease;
        }
        .autocomplete-item:hover {
            background: #fff7ed;
            color: #ea580c;
        }
        .autocomplete-item .cat-badge {
            font-size: 0.7rem;
            background: #f1f5f9;
            color: #64748b;
            padding: 2px 7px;
            border-radius: 6px;
            font-weight: 600;
        }
        .autocomplete-item:hover .cat-badge {
            background: #ffedd5;
            color: #ea580c;
        }

        /* EXTRA FILTER ROWS CARD (Contains Row 2, 3, 4) */
        #extraRowsCard {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 480px;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 16px 40px rgba(0,0,0,0.16);
            z-index: 9999;
            padding: 10px 12px;
        }
        #extraRowsCard.open {
            display: block;
            animation: fadeInDrop 0.18s ease-out;
        }

        .extra-filter-row {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            height: 40px;
            padding: 2px 6px 2px 8px;
            margin-bottom: 8px;
            transition: all 0.2s ease;
            position: relative;
        }
        .extra-filter-row:focus-within {
            border-color: #ea580c;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.1);
        }

        .extra-cat-select {
            border: none;
            background: transparent;
            font-size: 0.78rem;
            font-weight: 700;
            color: #475569;
            outline: none;
            cursor: pointer;
            padding-right: 4px;
            max-width: 130px;
        }

        .extra-input-key {
            flex: 1;
            border: none;
            background: transparent;
            font-size: 0.82rem;
            font-weight: 600;
            color: #0f172a;
            outline: none;
            padding: 4px 6px;
        }

        .btn-remove-row {
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 1.1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 6px;
            transition: all 0.15s ease;
        }
        .btn-remove-row:hover {
            color: #ef4444;
            background: #fee2e2;
        }

        /* FLATPICKR CALENDAR CUSTOMIZATION */
        .flatpickr-calendar {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            border-radius: 16px !important;
            border: 1.5px solid #e2e8f0 !important;
            box-shadow: 0 16px 40px rgba(0,0,0,0.15) !important;
            z-index: 100050 !important;
        }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange {
            background: #ea580c !important;
            border-color: #ea580c !important;
        }
        .flatpickr-day.today {
            border-color: #ea580c !important;
        }
        .flatpickr-months .flatpickr-month {
            color: #0f172a !important;
            font-weight: 800 !important;
        }

        /* ===== MASTER-DETAIL DAILY SEARCH MODAL ===== */
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
        .modal-content.modal-master-detail {
            background: #ffffff;
            width: 95%;
            max-width: 960px;
            height: 85vh;
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.22);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            animation: zoomInModal 0.2s ease-out;
        }

        @keyframes zoomInModal {
            from { opacity: 0; transform: scale(0.96); }
            to { opacity: 1; transform: scale(1); }
        }

        .modal-body-split {
            display: grid;
            grid-template-columns: 360px 1fr;
            flex: 1;
            min-height: 0;
            height: calc(85vh - 70px);
            overflow: hidden;
        }
        @media (max-width: 860px) {
            .modal-content.modal-master-detail {
                height: 90vh;
            }
            .modal-body-split {
                grid-template-columns: 1fr;
                grid-template-rows: 280px 1fr;
                height: calc(90vh - 70px);
            }
            .modal-daily-list-pane {
                border-right: none !important;
                border-bottom: 1.5px solid #e2e8f0;
            }
        }

        .modal-daily-list-pane {
            border-right: 1.5px solid #f1f5f9;
            background: #fafafa;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            height: 100%;
            min-height: 0;
        }
        .modal-daily-search-wrap {
            padding: 14px 16px 10px 16px;
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            position: relative;
            flex-shrink: 0;
        }
        .modal-daily-search-input {
            width: 100%;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 9px 12px 9px 34px;
            font-size: 0.84rem;
            font-weight: 600;
            color: #0f172a;
            outline: none;
            background: #f8fafc;
            transition: all 0.2s ease;
        }
        .modal-daily-search-input:focus {
            background: #ffffff;
            border-color: #ea580c;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.1);
        }
        .modal-daily-list-scroll {
            flex: 1 1 0;
            overflow-y: scroll !important;
            overflow-x: hidden;
            min-height: 0;
            height: 0;
            padding: 10px 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }
        .modal-daily-list-scroll::-webkit-scrollbar {
            width: 6px;
            display: block;
        }
        .modal-daily-list-scroll::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .modal-daily-list-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .modal-daily-list-scroll::-webkit-scrollbar-thumb:hover {
            background: #ea580c;
        }
        .modal-daily-item {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 11px 13px;
            cursor: pointer;
            transition: all 0.15s ease;
            position: relative;
        }
        .modal-daily-item:hover {
            border-color: #cbd5e1;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        .modal-daily-item.active {
            border-color: #ea580c;
            background: #fff7ed;
            box-shadow: 0 4px 14px rgba(234, 88, 12, 0.08);
        }
        .modal-detail-pane {
            padding: 20px 24px;
            overflow-y: auto;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #94a3b8;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            transition: all 0.15s ease;
        }
        .modal-close:hover { 
            color: #1e293b; 
            background: #f1f5f9;
        }

        .swal2-container { z-index: 9999999 !important; }

        /* ===== INTERACTIVE MONTH-YEAR PICKER STYLES ===== */
        .month-year-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: transparent;
            border: 1.5px solid transparent;
            border-radius: 12px;
            padding: 5px 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
        }
        .month-year-btn:hover, .month-year-picker-wrap.open .month-year-btn {
            background: #f8fafc;
            border-color: #e2e8f0;
        }

        .month-year-popover {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            width: 280px;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 18px;
            box-shadow: 0 20px 45px rgba(0,0,0,0.18);
            z-index: 100040 !important;
            padding: 14px;
            animation: fadeInDrop 0.18s ease-out;
        }
        .month-year-picker-wrap.open .month-year-popover {
            display: block;
        }

        .my-year-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            padding: 0 4px;
        }
        .my-year-nav button {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #475569;
            transition: all 0.15s ease;
        }
        .my-year-nav button:hover {
            background: #ea580c;
            color: #ffffff;
            border-color: #ea580c;
        }

        .my-months-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }
        .my-month-item {
            padding: 8px 4px;
            text-align: center;
            border-radius: 10px;
            font-size: 0.82rem;
            font-weight: 700;
            color: #334155;
            cursor: pointer;
            transition: all 0.15s ease;
            background: #f8fafc;
            border: 1px solid transparent;
        }
        .my-month-item:hover {
            background: #fff7ed;
            color: #ea580c;
            border-color: #ffedd5;
        }
        .my-month-item.active {
            background: #ea580c;
            color: #ffffff;
            border-color: #ea580c;
            box-shadow: 0 4px 10px rgba(234, 88, 12, 0.25);
        }

        /* ===== COMPACT SLIDING VIEW SWITCHER (LOCKED FIXED WIDTH) ===== */
        .view-switcher-pill {
            display: inline-flex;
            align-items: center;
            background: #f1f5f9;
            padding: 3px;
            border-radius: 12px;
            gap: 2px;
            border: 1px solid #e2e8f0;
            width: 124px; /* Locked fixed width so neighboring elements NEVER shift */
            height: 36px;
            flex-shrink: 0;
            cursor: pointer;
            user-select: none;
            box-sizing: border-box;
            transition: border-color 0.2s ease;
        }
        .view-switcher-pill:hover {
            border-color: #cbd5e1;
        }
        .view-toggle-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            background: transparent;
            border: none;
            color: #64748b;
            font-size: 0.78rem;
            font-weight: 700;
            height: 100%;
            border-radius: 9px;
            cursor: pointer;
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
            padding: 0;
            flex: 0 0 32px; /* Inactive is exactly 32px icon */
        }
        .view-toggle-btn .view-label {
            display: none;
        }
        .view-toggle-btn.active {
            background: #ffffff;
            color: #ea580c;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            flex: 1 1 auto; /* Active fills exact remaining space */
        }
        .view-toggle-btn.active .view-label {
            display: inline-block;
            animation: fadeInLabel 0.18s ease-out;
        }

        @keyframes fadeInLabel {
            from { opacity: 0; transform: scale(0.96); }
            to { opacity: 1; transform: scale(1); }
        }

        /* ===== FULLSCREEN TABLE VIEW STYLES (CLEAN FLAT LIST) ===== */
        .table-view-container {
            height: calc(100vh - 68px);
            overflow-y: auto;
            background: #fbf7f1;
            padding: 24px 36px 40px;
        }
        .table-view-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .table-stats-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            border: 1.5px solid #e8e2d5;
            border-radius: 16px;
            padding: 10px 18px;
            gap: 16px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.03);
            flex-wrap: wrap;
        }
        .stat-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.82rem;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 10px;
            transition: all 0.15s ease;
        }
        .stat-pill-total {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #1e293b;
        }
        .stat-pill-pending {
            background: #fffbeb;
            border: 1px solid #fef3c7;
            color: #b45309;
        }
        .stat-pill-approved {
            background: #f0fdf4;
            border: 1px solid #dcfce7;
            color: #15803d;
        }
        .stat-label { font-weight: 600; }
        .stat-val { font-weight: 800; }

        /* SLEEK MODERN SELECT DROPDOWNS (NOT BASIC HTML) */
        .custom-table-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-color: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 6px 30px 6px 12px;
            font-size: 0.8rem;
            font-weight: 700;
            color: #1e293b;
            cursor: pointer;
            outline: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 12px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .custom-table-select:hover {
            border-color: #cbd5e1;
            background-color: #ffffff;
        }
        .custom-table-select:focus {
            border-color: #ea580c;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.12);
        }

        /* MODERN CLEAN TABLE COLUMN HEADER */
        .table-column-header {
            display: grid;
            grid-template-columns: 240px 170px 115px 1fr 175px;
            align-items: center;
            gap: 16px;
            padding: 10px 12px;
            background: rgba(255, 255, 255, 0.4);
            border-bottom: 1.8px solid #cbd5e1;
            font-size: 0.74rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 10px;
        }
        .th-col {
            display: flex;
            align-items: center;
        }
        .th-col.th-user-time {
            justify-content: center;
            text-align: center;
        }
        .th-col.th-date {
            justify-content: center;
            text-align: center;
        }
        .th-col.th-status {
            justify-content: flex-start;
            padding-left: 14px;
        }

        /* CLEAN FLAT LIST WITH SUBTLE DIVIDERS (NO HEAVY CARD BOXES) */
        .table-cards-list {
            display: flex;
            flex-direction: column;
        }

        .table-row-card {
            display: grid;
            grid-template-columns: 240px 170px 115px 1fr 175px;
            align-items: center;
            background: transparent;
            border-bottom: 1px solid #e8e2d5;
            padding: 16px 12px;
            gap: 16px;
            cursor: pointer;
            transition: background 0.15s ease, border-radius 0.15s ease;
        }
        .table-row-card:hover {
            background: rgba(255, 255, 255, 0.7);
            border-radius: 12px;
        }

        .tr-room-col {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }
        .tr-room-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: transparent;
            border: 1.8px solid #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1e293b;
            flex-shrink: 0;
        }
        .tr-room-info {
            min-width: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .tr-room-code {
            font-size: 1.02rem;
            font-weight: 800;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
        }
        .tr-room-name {
            font-size: 0.82rem;
            font-weight: 500;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* VERTICALLY STACKED PILLS WITH UNIFORM FIXED WIDTH: USER (TOP) + TIME (BOTTOM) */
        .tr-user-time-col {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
            width: 170px;
            flex-shrink: 0;
        }
        .tr-pill-user {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: #ffffff;
            border: 1.5px solid #334155;
            border-radius: 999px;
            padding: 4px 12px;
            font-size: 0.78rem;
            font-weight: 700;
            color: #1e293b;
            white-space: nowrap;
            width: 160px;
            box-sizing: border-box;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }
        .tr-pill-user span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .tr-pill-time {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            border: 1.5px solid #fb923c;
            border-radius: 999px;
            padding: 4px 12px;
            font-size: 0.78rem;
            font-weight: 700;
            color: #ea580c;
            white-space: nowrap;
            width: 160px;
            box-sizing: border-box;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }

        .tr-date-col {
            font-size: 0.92rem;
            font-weight: 800;
            color: #1e293b;
            text-align: center;
            letter-spacing: -0.2px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* KETERANGAN COLUMN BESIDE DATE */
        .tr-desc-col {
            min-width: 0;
            overflow: hidden;
            display: flex;
            align-items: center;
            padding: 0 8px;
        }
        .tr-desc-text {
            font-size: 0.84rem;
            font-weight: 500;
            color: #475569;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
            max-width: 100%;
        }

        /* STATUS BADGE WITH UNIFORM FIXED WIDTH */
        .tr-status-col {
            display: flex;
            justify-content: flex-end;
            width: 175px;
            flex-shrink: 0;
        }
        .tr-status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: flex-start;
            gap: 8px;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 5px 14px;
            border-radius: 999px;
            white-space: nowrap;
            width: 165px;
            box-sizing: border-box;
            text-align: left;
        }

        .table-pagination-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            padding: 10px 18px;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .pagination-buttons {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .page-nav-btn {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 5px 12px;
            font-size: 0.78rem;
            font-weight: 700;
            color: #334155;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .page-nav-btn:hover:not(:disabled) {
            background: #fff7ed;
            border-color: #ffedd5;
            color: #ea580c;
        }
        .page-nav-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        @media (max-width: 960px) {
            .table-row-card {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            .tr-status-col {
                justify-content: flex-start;
            }
        }
        @media (max-width: 1024px) {
            .unified-search-pill { width: 340px; }
            #mainAutocompleteList { width: 340px; }
        }
        @media (max-width: 640px) {
            .gcal-page-header { flex-wrap: wrap; height: auto; padding: 12px 16px; }
            .search-filter-container { width: 100%; }
            .unified-search-pill { width: 100%; }
            .table-view-container { padding: 12px 14px; }
        }
    </style>
</head>
<body>

    <!-- Header Kalender Full Page (Single Row Height 70px) -->
    <div class="gcal-page-header">
        <div class="gcal-header-left" style="display: flex; align-items: center; gap: 8px; position: relative;">
            <!-- Pane 1: Calendar Navigation (Active on Calendar Mode) -->
            <div id="headerLeftCalendarNav" class="header-left-pane">
                <button class="gcal-btn-today" onclick="goToToday()">Today</button>
                <div class="gcal-nav-arrows" style="display: flex; gap: 4px;">
                    <button onclick="prevWeek()"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg></button>
                    <button onclick="nextWeek()"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg></button>
                </div>

                <!-- Interactive Month-Year Picker Trigger -->
                <div class="month-year-picker-wrap" id="monthYearPickerWrap" style="position: relative;">
                    <button type="button" class="month-year-btn" id="monthYearBtn" onclick="toggleMonthYearPicker(event)" title="Klik untuk memilih bulan & tahun" style="padding: 4px 8px;">
                        <span id="gcalMonthTitle" style="font-size: 0.96rem; font-weight: 800; color: #0f172a; white-space: nowrap;">-</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>

                    <!-- Month-Year Popover Dropdown -->
                    <div class="month-year-popover" id="monthYearPopover">
                        <!-- Year Navigation Header -->
                        <div class="my-year-nav">
                            <button type="button" onclick="changePickerYear(-1, event)" title="Tahun Sebelumnya">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
                            </button>
                            <span id="pickerYearDisplay" style="font-size: 1.05rem; font-weight: 800; color: #0f172a;">2026</span>
                            <button type="button" onclick="changePickerYear(1, event)" title="Tahun Selanjutnya">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </button>
                        </div>

                        <!-- Month Grid (12 Months) -->
                        <div class="my-months-grid" id="pickerMonthsGrid">
                            <!-- Jan - Des buttons rendered via JS -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pane 2: Table Title (Active on Table Mode) -->
            <div id="headerLeftTableTitle" class="header-left-pane" style="display: none;">
                <span style="font-size: 1.05rem; font-weight: 800; color: #0f172a; white-space: nowrap; padding: 4px 6px;">Daftar Peminjaman</span>
            </div>
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
                    <button type="button" onclick="triggerSearchSubmit()" title="Klik untuk Cari (atau tekan Enter)" style="background: none; border: none; padding: 0; margin: 0; position: absolute; left: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #64748b; z-index: 2;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </button>
                    <input type="text" id="mainSearchInput" placeholder="Cari ruangan, peminjam, kode (key)..." 
                           oninput="handleUnifiedMultiSearch(this)" 
                           onkeydown="if(event.key === 'Enter') { triggerSearchSubmit(); }"
                           onfocus="onMainInputFocused()"
                           autocomplete="off" class="unified-input-key main-val-field" style="padding-left: 28px;">
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
                        <div class="status-option" data-val="selesai" onclick="selectStatusOption('main', 'selesai', 'Selesai', '#94a3b8')">
                            <span class="status-dot" style="background: #94a3b8;"></span> Selesai
                        </div>
                    </div>
                </div>

            </div>

            <!-- STANDALONE SEPARATE + 1/4 BUTTON -->
            <button type="button" class="btn-standalone-add" id="standaloneAddBtn" onclick="toggleOrAddFilterRow(event)">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span id="filterCountBadge">1/4</span>
            </button>

            <!-- Clean Dedicated Autocomplete Dropdown List (ONLY Suggestions) -->
            <div id="mainAutocompleteList"></div>

            <!-- Extra Filter Rows Card (Contains Row 2, 3, 4) -->
            <div id="extraRowsCard">
                <div id="additionalFilterRowsContainer">
                    <!-- Additional rows appended via JS -->
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 10px; margin-top: 8px; gap: 8px;">
                    <button type="button" id="cardAddRowBtn" onclick="addExtraFilterRow()" style="background: #fff7ed; border: 1.5px solid #ffedd5; color: #ea580c; border-radius: 8px; font-weight: 700; font-size: 0.76rem; padding: 6px 12px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all 0.15s ease;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Tambah Baris
                    </button>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <button type="button" onclick="resetHeaderMultiSearch()" style="background: none; border: none; color: #dc2626; font-weight: 700; font-size: 0.76rem; cursor: pointer; padding: 6px 8px;">
                            Reset
                        </button>
                        <button type="button" onclick="triggerSearchSubmit()" style="background: #ea580c; border: none; color: #ffffff; border-radius: 8px; font-weight: 700; font-size: 0.78rem; padding: 6px 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; box-shadow: 0 2px 6px rgba(234, 88, 12, 0.25); transition: all 0.15s ease;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            Cari Filter
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <div class="gcal-header-right" style="display: flex; align-items: center; gap: 8px;">
            <!-- Compact Sliding View Switcher (Kalender / Tabel) -->
            <div class="view-switcher-pill" id="viewSwitcherPill" onclick="toggleViewMode(event)" title="Klik untuk ganti tampilan Kalender / Tabel">
                <button type="button" class="view-toggle-btn active" id="viewToggleCalendarBtn" onclick="switchViewMode('calendar', event)" title="Tampilan Kalender">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    <span class="view-label">Kalender</span>
                </button>
                <button type="button" class="view-toggle-btn" id="viewToggleTableBtn" onclick="switchViewMode('table', event)" title="Tampilan Tabel">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                    <span class="view-label">Tabel</span>
                </button>
            </div>

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
    <div class="gcal-body" id="calendarViewContainer">
        <div class="gcal-days-header" id="gcalDaysHeader">
            <!-- Digenerate via JS -->
        </div>
        <div class="gcal-grid-scroll">
            <div class="gcal-grid" id="gcalGrid">
                <!-- Digenerate via JS -->
            </div>
        </div>
    </div>

    <!-- Container Utama Tampilan Tabel (Fullscreen Modern) -->
    <div class="table-view-container" id="tableViewContainer" style="display: none;">
        <div class="table-view-inner">
            <!-- Table Quick Stats Banner -->
            <div class="table-stats-bar">
                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                    <div class="stat-pill stat-pill-total">
                        <span class="stat-label" style="color: #64748b;">Total Data:</span>
                        <span class="stat-val" id="tableStatTotal" style="color: #0f172a;">0</span>
                    </div>
                    <div class="stat-pill stat-pill-pending">
                        <span class="stat-dot" style="width: 7px; height: 7px; border-radius: 50%; background: #f59e0b; display: inline-block;"></span>
                        <span class="stat-label">Menunggu:</span>
                        <span class="stat-val" id="tableStatPending">0</span>
                    </div>
                    <div class="stat-pill stat-pill-approved">
                        <span class="stat-dot" style="width: 7px; height: 7px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                        <span class="stat-label">Disetujui:</span>
                        <span class="stat-val" id="tableStatApproved">0</span>
                    </div>
                </div>
                
                <div style="margin-left: auto; display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <label for="tablePageSizeSelect" style="font-size: 0.78rem; font-weight: 700; color: #64748b;">Tampilkan:</label>
                        <select id="tablePageSizeSelect" class="custom-table-select" onchange="changeTablePageSize(this.value)">
                            <option value="10">10 baris</option>
                            <option value="20" selected>20 baris</option>
                            <option value="50">50 baris</option>
                            <option value="100">100 baris</option>
                        </select>
                    </div>

                    <div style="display: flex; align-items: center; gap: 6px;">
                        <label for="tableSortSelect" style="font-size: 0.78rem; font-weight: 700; color: #64748b;">Urutkan:</label>
                        <select id="tableSortSelect" class="custom-table-select" onchange="renderTableView()">
                            <option value="date_desc">Tanggal (Terbaru)</option>
                            <option value="date_asc">Tanggal (Terlama)</option>
                            <option value="room_asc">Nama Ruangan (A-Z)</option>
                            <option value="time_asc">Jam Mulai (Pagi - Malam)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table Column Header -->
            <div class="table-column-header">
                <div class="th-col th-room">Ruangan</div>
                <div class="th-col th-user-time">Peminjam & Waktu</div>
                <div class="th-col th-date">Tanggal</div>
                <div class="th-col th-desc">Keterangan / Keperluan</div>
                <div class="th-col th-status">Status</div>
            </div>

            <!-- Table Cards List (Scrollable) -->
            <div class="table-cards-list" id="tableCardsList">
                <!-- Rendered dynamically via JS -->
            </div>

            <!-- Table Pagination -->
            <div class="table-pagination-wrap" id="tablePaginationWrap">
                <span id="tablePaginationInfo" style="font-size: 0.82rem; font-weight: 600; color: #64748b;">Menampilkan 1-20 data</span>
                <div class="pagination-buttons" id="tablePaginationBtns">
                    <!-- Pagination buttons rendered via JS -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail & Approval Peminjaman (Master-Detail with Daily Search) -->
    <div class="modal-overlay" id="detailBookingModal">
        <div class="modal-content modal-master-detail">
            <!-- Modal Header -->
            <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; background: #ffffff;">
                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 12px; background: #fff7ed; color: #ea580c;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    </span>
                    <div>
                        <h2 id="modalDailyDateTitle" style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 0;">Jadwal Peminjaman</h2>
                        <span id="modalDailyCountBadge" style="font-size: 0.76rem; font-weight: 600; color: #64748b;">Memuat data...</span>
                    </div>
                </div>
                <button class="modal-close" type="button" onclick="closeDetailBookingModal()">&times;</button>
            </div>

            <!-- Modal Body Split (Left: List + Search | Right: Full Detail + Approval) -->
            <div class="modal-body-split">
                <!-- Left Pane: Search & List -->
                <div class="modal-daily-list-pane">
                    <div class="modal-daily-search-wrap">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" style="position: absolute; left: 28px; top: 25px; pointer-events: none;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <input type="text" id="modalDailySearchInput" class="modal-daily-search-input" placeholder="Cari ruangan, kode, peminjam..." oninput="filterDailyModalList()" autocomplete="off">
                    </div>
                    <div class="modal-daily-list-scroll" id="modalDailyList">
                        <!-- Items rendered dynamically via JS -->
                    </div>
                </div>

                <!-- Right Pane: Active Booking Detail + Actions -->
                <div class="modal-detail-pane" id="modalDetailPane">
                    <div>
                        <input type="hidden" id="detailBookingId">
                        
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px; margin-bottom: 16px;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; gap: 10px; flex-wrap: wrap;">
                                <div>
                                    <span id="detailKodeRuangan" style="display: inline-block; background: #ede9fe; color: #7c3aed; font-size: 0.75rem; font-weight: 700; padding: 3px 10px; border-radius: 20px; margin-bottom: 4px;"></span>
                                    <h3 id="detailNamaRuangan" style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a;"></h3>
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
                    </div>

                    <div>
                        <!-- Panel Aksi Approval -->
                        <div id="approvalActionPanel" style="display: none; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 14px; padding: 16px; margin-bottom: 12px;">
                            <h4 style="margin: 0 0 10px 0; font-size: 0.88rem; font-weight: 700; color: #166534; display: flex; align-items: center; gap: 6px;">
                                ⚡ Persetujuan Peminjaman (<span id="approvalRoleLabel"></span>)
                            </h4>
                            <div style="display: flex; gap: 10px;">
                                <button type="button" onclick="approveBookingAction()" style="flex: 1; background: #16a34a; color: #fff; border: none; padding: 10px 14px; border-radius: 10px; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: background 0.2s;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    Setujui
                                </button>
                                <button type="button" onclick="toggleRejectInput()" style="flex: 1; background: #dc2626; color: #fff; border: none; padding: 10px 14px; border-radius: 10px; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: background 0.2s;">
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
                        <div id="deleteActionPanel" style="display: none;">
                            <button type="button" onclick="deleteBookingAction()" style="width: 100%; background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5; padding: 10px 14px; border-radius: 10px; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: background 0.2s;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                Hapus Jadwal Peminjaman
                            </button>
                        </div>
                    </div>
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
    </script>

    <!-- Unified Calendar & Multi-Search JS Engine -->
    <script>
        let currentWeekStart = new Date();
        // Set to Sunday of current week
        currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay());

        // Status style helper
        function getStatusStyle(status) {
            const s = (status || '').toLowerCase();
            if (s === 'pending' || s === 'menunggu persetujuan') {
                return { bg: '#f59e0b', border: '#d97706', badgeBg: '#fffbeb', badgeColor: '#b45309', dot: '#f59e0b', label: 'Menunggu Persetujuan' };
            } else if (s.includes('ka. ur') || s.includes('kaur')) {
                return { bg: '#10b981', border: '#059669', badgeBg: '#f0fdf4', badgeColor: '#166534', dot: '#10b981', label: 'Disetujui Ka. Ur' };
            } else if (s.includes('laboran')) {
                return { bg: '#3b82f6', border: '#2563eb', badgeBg: '#eff6ff', badgeColor: '#1d4ed8', dot: '#3b82f6', label: 'Disetujui Laboran' };
            } else if (s.includes('admin')) {
                return { bg: '#8b5cf6', border: '#7c3aed', badgeBg: '#f5f3ff', badgeColor: '#6d28d9', dot: '#8b5cf6', label: 'Disetujui Admin' };
            } else if (s.includes('disetujui')) {
                return { bg: '#10b981', border: '#059669', badgeBg: '#f0fdf4', badgeColor: '#166534', dot: '#10b981', label: 'Disetujui' };
            } else if (s === 'ditolak') {
                return { bg: '#ef4444', border: '#dc2626', badgeBg: '#fef2f2', badgeColor: '#991b1b', dot: '#ef4444', label: 'Ditolak' };
            } else if (s === 'selesai') {
                return { bg: '#64748b', border: '#475569', badgeBg: '#f8fafc', badgeColor: '#475569', dot: '#94a3b8', label: 'Selesai' };
            }
            return { bg: '#7c3aed', border: '#6d28d9', badgeBg: '#f5f3ff', badgeColor: '#6d28d9', dot: '#7c3aed', label: status || 'Pending' };
        }

        // ==========================================
        // MULTI-FILTER SEARCH ENGINE (UP TO 4 ROWS)
        // ==========================================
        const MAX_FILTER_ROWS = 4;
        let extraRowCount = 0;
        let activeTargetInput = null;

        function updateFilterCountBadge() {
            const count = 1 + extraRowCount;
            const badge = document.getElementById('filterCountBadge');
            const btn = document.getElementById('standaloneAddBtn');
            const addRowBtn = document.getElementById('cardAddRowBtn');
            if (badge) badge.innerText = `${count}/${MAX_FILTER_ROWS}`;

            if (btn) {
                if (count >= MAX_FILTER_ROWS) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            }

            if (addRowBtn) {
                if (extraRowCount >= (MAX_FILTER_ROWS - 1)) {
                    addRowBtn.style.opacity = '0.5';
                    addRowBtn.style.pointerEvents = 'none';
                } else {
                    addRowBtn.style.opacity = '1';
                    addRowBtn.style.pointerEvents = 'auto';
                }
            }
        }

        function toggleOrAddFilterRow(e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }
            const card = document.getElementById('extraRowsCard');
            if (!card) return;

            if (!card.classList.contains('open')) {
                if (extraRowCount === 0) {
                    addExtraFilterRow();
                }
                card.classList.add('open');
            } else {
                card.classList.remove('open');
            }
        }

        function onMainInputFocused() {
            // Keep extraRowsCard state as is
        }

        function addExtraFilterRow() {
            if (extraRowCount >= (MAX_FILTER_ROWS - 1)) return;
            extraRowCount++;

            const rowId = `extraFilterRow_${Date.now()}`;
            const container = document.getElementById('additionalFilterRowsContainer');

            const rowDiv = document.createElement('div');
            rowDiv.className = 'extra-filter-row';
            rowDiv.id = rowId;

            rowDiv.innerHTML = `
                <!-- CUSTOM STYLED CATEGORY DROPDOWN FOR EXTRA ROW -->
                <div class="custom-cat-dropdown" id="catWrap_${rowId}">
                    <button type="button" class="custom-cat-trigger" onclick="toggleCatDropdown('${rowId}', event)">
                        <span id="catLabel_${rowId}">Pilih Ruangan</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>

                    <input type="hidden" id="catSelect_${rowId}" class="extra-cat-select" value="ruangan">

                    <div class="custom-cat-menu" id="catMenu_${rowId}">
                        <div class="cat-option" data-val="keyword" onclick="selectCatOption('${rowId}', 'keyword', 'Key / Kata Kunci', '🔑')">
                            <span>🔑</span> Key / Kata Kunci
                        </div>
                        <div class="cat-option" data-val="kategori" onclick="selectCatOption('${rowId}', 'kategori', 'Kategori Ruangan', '📁')">
                            <span>📁</span> Kategori Ruangan
                        </div>
                        <div class="cat-option active" data-val="ruangan" onclick="selectCatOption('${rowId}', 'ruangan', 'Pilih Ruangan', '🏢')">
                            <span>🏢</span> Pilih Ruangan
                        </div>
                        <div class="cat-option" data-val="status" onclick="selectCatOption('${rowId}', 'status', 'Status Peminjaman', '⚡')">
                            <span>⚡</span> Status Peminjaman
                        </div>
                        <div class="cat-option" data-val="tanggal" onclick="selectCatOption('${rowId}', 'tanggal', 'Lompat Tanggal', '📅')">
                            <span>📅</span> Lompat Tanggal
                        </div>
                    </div>
                </div>

                <div class="unified-divider"></div>

                <!-- Text Search Container -->
                <div style="position: relative; flex: 1; display: flex; align-items: center;" id="valContainer_${rowId}">
                    <input type="text" class="extra-input-key" placeholder="Ketik kode / nama ruangan..." 
                           oninput="handleUnifiedMultiSearch(this)" 
                           onkeydown="if(event.key === 'Enter') { handleUnifiedMultiSearch(this, true); hideAutocomplete(); }"
                           autocomplete="off">
                </div>

                <!-- Custom Styled Status Selector Dropdown (Hidden initially for extra row) -->
                <div class="custom-status-dropdown" id="statusWrap_${rowId}" style="display: none; flex: 1;">
                    <button type="button" class="custom-status-trigger" onclick="toggleStatusDropdown('${rowId}', event)">
                        <span id="statusLabel_${rowId}" style="display: flex; align-items: center; gap: 6px;">
                            <span class="status-dot" style="background: #94a3b8;"></span> Semua Status
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    
                    <input type="hidden" id="statusValue_${rowId}" class="extra-input-key" value="">

                    <div class="custom-status-menu" id="statusMenu_${rowId}">
                        <div class="status-option active" data-val="" onclick="selectStatusOption('${rowId}', '', 'Semua Status', '#94a3b8')">
                            <span class="status-dot" style="background: #94a3b8;"></span> Semua Status
                        </div>
                        <div class="status-option" data-val="pending" onclick="selectStatusOption('${rowId}', 'pending', 'Menunggu Persetujuan', '#f59e0b')">
                            <span class="status-dot" style="background: #f59e0b;"></span> Menunggu Persetujuan
                        </div>
                        <div class="status-option" data-val="disetujui" onclick="selectStatusOption('${rowId}', 'disetujui', 'Disetujui', '#10b981')">
                            <span class="status-dot" style="background: #10b981;"></span> Disetujui
                        </div>
                        <div class="status-option" data-val="ditolak" onclick="selectStatusOption('${rowId}', 'ditolak', 'Ditolak', '#ef4444')">
                            <span class="status-dot" style="background: #ef4444;"></span> Ditolak
                        </div>
                        <div class="status-option" data-val="selesai" onclick="selectStatusOption('${rowId}', 'selesai', 'Selesai', '#94a3b8')">
                            <span class="status-dot" style="background: #94a3b8;"></span> Selesai
                        </div>
                    </div>
                </div>

                <button type="button" class="btn-remove-row" onclick="removeExtraFilterRow('${rowId}')" title="Hapus filter ini">&times;</button>
            `;

            container.appendChild(rowDiv);
            updateFilterCountBadge();

            // Focus new row's input
            const input = rowDiv.querySelector('.extra-input-key');
            if (input) input.focus();
        }

        function removeExtraFilterRow(rowId) {
            const row = document.getElementById(rowId);
            if (row) {
                row.remove();
                extraRowCount--;
                updateFilterCountBadge();
                applyMultiFilters();
            }
            if (extraRowCount === 0) {
                const card = document.getElementById('extraRowsCard');
                if (card) card.classList.remove('open');
            }
        }

        function resetHeaderMultiSearch() {
            // Reset main input
            const mainInput = document.getElementById('mainSearchInput');
            if (mainInput) mainInput.value = '';
            
            // Reset main cat to keyword
            selectCatOption('main', 'keyword', 'Key / Kata Kunci', '🔑');
            selectStatusOption('main', '', 'Semua Status', '#94a3b8');

            // Clear extra rows
            const container = document.getElementById('additionalFilterRowsContainer');
            if (container) container.innerHTML = '';
            extraRowCount = 0;
            updateFilterCountBadge();

            const card = document.getElementById('extraRowsCard');
            if (card) card.classList.remove('open');

            hideAutocomplete();
            applyMultiFilters();
        }

        // ==========================================
        // CUSTOM DROPDOWNS CONTROLLER
        // ==========================================
        function toggleCatDropdown(id, e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }
            const wrap = document.getElementById(id === 'main' ? 'mainCatWrap' : `catWrap_${id}`);
            if (!wrap) return;
            const wasOpen = wrap.classList.contains('open');
            closeAllCustomMenus();
            if (!wasOpen) {
                wrap.classList.add('open');
            }
        }

        function selectCatOption(id, val, label, icon) {
            const wrap = document.getElementById(id === 'main' ? 'mainCatWrap' : `catWrap_${id}`);
            const select = document.getElementById(id === 'main' ? 'mainCategorySelect' : `catSelect_${id}`);
            const lblSpan = document.getElementById(id === 'main' ? 'mainCatLabel' : `catLabel_${id}`);

            if (select) select.value = val;
            if (lblSpan) lblSpan.innerText = label;

            // Highlight active option
            if (wrap) {
                const options = wrap.querySelectorAll('.cat-option');
                options.forEach(opt => {
                    if (opt.getAttribute('data-val') === val) opt.classList.add('active');
                    else opt.classList.remove('active');
                });
                wrap.classList.remove('open');
            }

            // Handle UI toggle (Text Input vs Custom Status Dropdown vs Datepicker)
            const valContainer = document.getElementById(id === 'main' ? 'mainValueContainer' : `valContainer_${id}`);
            const statusWrap = document.getElementById(id === 'main' ? 'mainStatusWrap' : `statusWrap_${id}`);
            const inputField = valContainer ? valContainer.querySelector('input') : null;

            if (val === 'status') {
                if (valContainer) valContainer.style.display = 'none';
                if (statusWrap) statusWrap.style.display = 'block';
            } else {
                if (valContainer) valContainer.style.display = 'flex';
                if (statusWrap) statusWrap.style.display = 'none';
            }

            if (inputField) {
                inputField.value = '';
                if (val === 'keyword') inputField.placeholder = "Cari ruangan, peminjam, kode (key)...";
                else if (val === 'kategori') inputField.placeholder = "Ketik nama kategori (e.g. Lab Komputer)...";
                else if (val === 'ruangan') inputField.placeholder = "Ketik kode ruangan (e.g. IK.01.10)...";
                else if (val === 'tanggal') inputField.placeholder = "Pilih 1 tanggal / rentang tanggal...";

                setupDatePickerIfNeeded(inputField, val);
                inputField.focus();
            }

            hideAutocomplete();
        }

        function setupDatePickerIfNeeded(inputEl, catVal) {
            if (!inputEl) return;
            if (inputEl._flatpickr) {
                inputEl._flatpickr.destroy();
            }

            if (catVal === 'tanggal') {
                flatpickr(inputEl, {
                    mode: "range",
                    dateFormat: "Y-m-d",
                    disableMobile: "true",
                    onChange: function(selectedDates, dateStr) {
                        if (selectedDates && selectedDates.length > 0) {
                            const d = new Date(selectedDates[0]);
                            currentWeekStart = new Date(d);
                            currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay());
                            renderCalendar();
                            applyMultiFilters();
                        }
                    }
                });
            }
        }

        function toggleStatusDropdown(id, e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }
            const wrap = document.getElementById(id === 'main' ? 'mainStatusWrap' : `statusWrap_${id}`);
            if (!wrap) return;
            const wasOpen = wrap.classList.contains('open');
            closeAllCustomMenus();
            if (!wasOpen) {
                wrap.classList.add('open');
            }
        }

        function selectStatusOption(id, val, label, dotColor) {
            const wrap = document.getElementById(id === 'main' ? 'mainStatusWrap' : `statusWrap_${id}`);
            const input = document.getElementById(id === 'main' ? 'mainStatusValue' : `statusValue_${id}`);
            const lblSpan = document.getElementById(id === 'main' ? 'mainStatusLabel' : `statusLabel_${id}`);

            if (input) input.value = val;
            if (lblSpan) {
                lblSpan.innerHTML = `<span class="status-dot" style="background: ${dotColor};"></span> ${label}`;
            }

            if (wrap) {
                const options = wrap.querySelectorAll('.status-option');
                options.forEach(opt => {
                    if (opt.getAttribute('data-val') === val) opt.classList.add('active');
                    else opt.classList.remove('active');
                });
                wrap.classList.remove('open');
            }

            applyMultiFilters();
        }

        // ==========================================
        // MONTH-YEAR PICKER CONTROLLER
        // ==========================================
        let pickerCurrentYear = (new Date()).getFullYear();

        function toggleMonthYearPicker(e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }
            const wrap = document.getElementById('monthYearPickerWrap');
            if (!wrap) return;
            const wasOpen = wrap.classList.contains('open');
            closeAllCustomMenus();
            if (!wasOpen) {
                pickerCurrentYear = currentWeekStart.getFullYear();
                renderMonthYearPicker();
                wrap.classList.add('open');
            }
        }

        function changePickerYear(delta, e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }
            pickerCurrentYear += delta;
            renderMonthYearPicker();
        }

        function renderMonthYearPicker() {
            const yearDisplay = document.getElementById('pickerYearDisplay');
            if (yearDisplay) yearDisplay.innerText = pickerCurrentYear;

            const grid = document.getElementById('pickerMonthsGrid');
            if (!grid) return;

            const monthShorts = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            const activeMonth = currentWeekStart.getMonth();
            const activeYear = currentWeekStart.getFullYear();

            let html = '';
            monthShorts.forEach((mName, idx) => {
                const isActive = (idx === activeMonth && pickerCurrentYear === activeYear);
                html += `
                    <div class="my-month-item ${isActive ? 'active' : ''}" onclick="selectMonthYear(${idx}, ${pickerCurrentYear}, event)">
                        ${mName}
                    </div>
                `;
            });
            grid.innerHTML = html;
        }

        function selectMonthYear(monthIndex, year, e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }
            // Set currentWeekStart to 1st of selected month
            const d = new Date(year, monthIndex, 1);
            currentWeekStart = new Date(d);
            currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay());

            const wrap = document.getElementById('monthYearPickerWrap');
            if (wrap) wrap.classList.remove('open');

            renderCalendar();
            applyMultiFilters();
        }

        function closeAllCustomMenus() {
            document.querySelectorAll('.custom-cat-dropdown').forEach(d => d.classList.remove('open'));
            document.querySelectorAll('.custom-status-dropdown').forEach(d => d.classList.remove('open'));
            const myWrap = document.getElementById('monthYearPickerWrap');
            if (myWrap) myWrap.classList.remove('open');
        }

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.custom-cat-dropdown') && 
                !e.target.closest('.custom-status-dropdown') && 
                !e.target.closest('#monthYearPickerWrap')) {
                closeAllCustomMenus();
            }
            if (!e.target.closest('#unifiedSearchPill') && 
                !e.target.closest('#extraRowsCard') && 
                !e.target.closest('#standaloneAddBtn') &&
                !e.target.closest('#mainAutocompleteList') &&
                !e.target.closest('.flatpickr-calendar')) {
                
                const card = document.getElementById('extraRowsCard');
                if (card) card.classList.remove('open');
                hideAutocomplete();
            }
        });

        // ==========================================
        // AUTOCOMPLETE & SEARCH LOGIC
        // ==========================================
        function triggerSearchSubmit() {
            const mainInput = document.getElementById('mainSearchInput');
            if (mainInput) {
                handleUnifiedMultiSearch(mainInput, true);
            } else {
                applyMultiFilters();
            }
            hideAutocomplete();
        }

        function handleUnifiedMultiSearch(inputEl, isImmediate = false) {
            activeTargetInput = inputEl;
            const clearBtn = document.getElementById('clearMainSearchBtn');
            if (clearBtn && inputEl.id === 'mainSearchInput') {
                clearBtn.style.display = inputEl.value.trim() ? 'block' : 'none';
            }

            const row = inputEl.closest('.unified-search-pill') || inputEl.closest('.extra-filter-row');
            let catType = 'keyword';
            if (row) {
                const select = row.querySelector('.extra-cat-select');
                if (select) catType = select.value;
            }

            const query = inputEl.value.trim().toLowerCase();

            // Handle Autocomplete Suggestions for Kategori and Ruangan (Saran saat mengetik)
            if (query.length > 0 && (catType === 'kategori' || catType === 'ruangan' || catType === 'keyword')) {
                showAutocomplete(inputEl, query, catType);
            } else {
                hideAutocomplete();
            }

            // HANYA FILTER SAAT ENTER / KLIK CARI / PILIH DROPDOWN / HAPUS INPUT
            // (Mencegah beban komputasi berat saat ribuan data diketik)
            if (isImmediate || query.length === 0) {
                applyMultiFilters();
            }
        }

        function clearMainSearch() {
            const mainInput = document.getElementById('mainSearchInput');
            if (mainInput) {
                mainInput.value = '';
                document.getElementById('clearMainSearchBtn').style.display = 'none';
                mainInput.focus();
            }
            hideAutocomplete();
            applyMultiFilters();
        }

        function showAutocomplete(targetInput, query, catType) {
            const autoList = document.getElementById('mainAutocompleteList');
            if (!autoList) return;

            let suggestions = [];
            
            // Check if there is already an active category filter in another row
            let activeCategoryFilter = null;
            const allFilters = getActiveFilterRules();
            allFilters.forEach(f => {
                if (f.category === 'kategori' && f.value) {
                    activeCategoryFilter = f.value.toLowerCase().trim();
                }
            });

            if (catType === 'kategori') {
                // Pure category suggestions only
                if (typeof kategoriList !== 'undefined') {
                    kategoriList.forEach(k => {
                        const nama = k.nama_kategori || '';
                        if (nama.toLowerCase().includes(query)) {
                            suggestions.push({ type: 'kategori', text: nama, badge: 'Kategori' });
                        }
                    });
                }
            } else if (catType === 'ruangan') {
                // Room Code + Name suggestions
                if (typeof ruanganList !== 'undefined') {
                    ruanganList.forEach(r => {
                        const kode = r.kode_ruangan || '';
                        const nama = r.nama_ruangan || '';
                        const kat  = (r.nama_kategori || '').toLowerCase().trim();

                        // If dependent category filter is active, only show rooms in that category
                        if (activeCategoryFilter && !kat.includes(activeCategoryFilter) && !activeCategoryFilter.includes(kat)) {
                            return;
                        }

                        if (kode.toLowerCase().includes(query) || nama.toLowerCase().includes(query)) {
                            suggestions.push({ 
                                type: 'ruangan', 
                                fillValue: kode, 
                                text: `${kode} — ${nama}`, 
                                badge: r.nama_kategori || 'Ruangan' 
                            });
                        }
                    });
                }
            } else {
                // Keyword mode
                if (typeof ruanganList !== 'undefined') {
                    ruanganList.forEach(r => {
                        const kode = r.kode_ruangan || '';
                        const nama = r.nama_ruangan || '';
                        if (kode.toLowerCase().includes(query) || nama.toLowerCase().includes(query)) {
                            suggestions.push({ type: 'keyword', fillValue: kode, text: `${kode} — ${nama}`, badge: 'Ruangan' });
                        }
                    });
                }
                if (typeof kategoriList !== 'undefined') {
                    kategoriList.forEach(k => {
                        const nama = k.nama_kategori || '';
                        if (nama.toLowerCase().includes(query)) {
                            suggestions.push({ type: 'keyword', fillValue: nama, text: nama, badge: 'Kategori' });
                        }
                    });
                }
            }

            if (suggestions.length === 0) {
                hideAutocomplete();
                return;
            }

            // Position autocomplete list below the target input
            const rect = targetInput.getBoundingClientRect();
            autoList.style.top = `${rect.bottom + 6}px`;
            autoList.style.left = `${rect.left}px`;
            autoList.style.width = `${rect.width > 240 ? rect.width : 280}px`;

            let html = '';
            suggestions.slice(0, 8).forEach(s => {
                const fillVal = (s.fillValue || s.text).replace(/"/g, '&quot;');
                html += `
                    <div class="autocomplete-item" onclick="applySuggestion('${fillVal}', event)">
                        <span>${s.text}</span>
                        <span class="cat-badge">${s.badge}</span>
                    </div>
                `;
            });

            autoList.innerHTML = html;
            autoList.style.display = 'block';
        }

        function hideAutocomplete() {
            const autoList = document.getElementById('mainAutocompleteList');
            if (autoList) autoList.style.display = 'none';
        }

        function applySuggestion(val, e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }
            if (activeTargetInput) {
                activeTargetInput.value = val;
                activeTargetInput.focus();
            }
            if (searchDebounceTimer) clearTimeout(searchDebounceTimer);
            hideAutocomplete();
            applyMultiFilters();
        }

        function getActiveFilterRules() {
            const rules = [];

            // Row 1 (Main)
            const mainCat = document.getElementById('mainCategorySelect') ? document.getElementById('mainCategorySelect').value : 'keyword';
            let mainVal = '';
            if (mainCat === 'status') {
                mainVal = document.getElementById('mainStatusValue') ? document.getElementById('mainStatusValue').value.trim() : '';
            } else {
                mainVal = document.getElementById('mainSearchInput') ? document.getElementById('mainSearchInput').value.trim() : '';
            }
            if (mainVal) {
                rules.push({ category: mainCat, value: mainVal });
            }

            // Extra Rows
            const extraRows = document.querySelectorAll('.extra-filter-row');
            extraRows.forEach(row => {
                const select = row.querySelector('.extra-cat-select');
                const cat = select ? select.value : 'keyword';
                let val = '';

                if (cat === 'status') {
                    const statusInput = row.querySelector('.custom-status-dropdown input[type="hidden"]');
                    val = statusInput ? statusInput.value.trim() : '';
                } else {
                    const txtInput = row.querySelector('input[type="text"]');
                    val = txtInput ? txtInput.value.trim() : '';
                }

                if (val) {
                    rules.push({ category: cat, value: val });
                }
            });

            return rules;
        }

        // ==========================================
        // VIEW SWITCHER CONTROLLER (KALENDER / TABEL)
        // ==========================================
        window.currentViewMode = 'calendar';
        let currentTablePage = 1;
        let tablePageSize = 20;
        let lastFilteredData = null;

        function changeTablePageSize(size) {
            tablePageSize = parseInt(size) || 20;
            try { localStorage.setItem('ifik_table_page_size', size); } catch (e) {}
            currentTablePage = 1;
            renderTableView(lastFilteredData || window.bookingData);
        }

        function toggleViewMode(e) {
            if (e) e.stopPropagation();
            if (window.currentViewMode === 'calendar') {
                switchViewMode('table');
            } else {
                switchViewMode('calendar');
            }
        }

        // ==========================================
        // INDONESIAN DATE FORMATTING UTILITIES
        // ==========================================
        const INDO_MONTHS = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const INDO_MONTHS_SHORT = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

        function formatIndoDate(dateInput, short = false) {
            if (!dateInput) return '-';
            const parts = String(dateInput).split('T')[0].split('-');
            if (parts.length < 3) return dateInput;
            const year = parseInt(parts[0], 10);
            const month = parseInt(parts[1], 10) - 1;
            const day = parseInt(parts[2], 10);
            if (isNaN(year) || isNaN(month) || isNaN(day) || month < 0 || month > 11) return dateInput;
            const monthName = short ? INDO_MONTHS_SHORT[month] : INDO_MONTHS[month];
            return `${day} ${monthName} ${year}`;
        }

        function formatIndoDateRange(startStr, endStr, short = false) {
            if (!startStr) return '-';
            if (!endStr || startStr === endStr) {
                return formatIndoDate(startStr, short);
            }
            const p1 = String(startStr).split('-');
            const p2 = String(endStr).split('-');
            if (p1.length < 3 || p2.length < 3) return `${startStr} - ${endStr}`;
            
            const y1 = parseInt(p1[0], 10), m1 = parseInt(p1[1], 10) - 1, d1 = parseInt(p1[2], 10);
            const y2 = parseInt(p2[0], 10), m2 = parseInt(p2[1], 10) - 1, d2 = parseInt(p2[2], 10);

            const mName1 = short ? INDO_MONTHS_SHORT[m1] : INDO_MONTHS[m1];
            const mName2 = short ? INDO_MONTHS_SHORT[m2] : INDO_MONTHS[m2];

            if (y1 === y2 && m1 === m2) {
                return `${d1} - ${d2} ${mName2} ${y2}`;
            } else if (y1 === y2) {
                return `${d1} ${mName1} - ${d2} ${mName2} ${y2}`;
            } else {
                return `${d1} ${mName1} ${y1} - ${d2} ${mName2} ${y2}`;
            }
        }

        function updateHeaderMonthTitle() {
            const monthTitle = document.getElementById('gcalMonthTitle');
            if (monthTitle) {
                const endOfWeek = new Date(currentWeekStart);
                endOfWeek.setDate(endOfWeek.getDate() + 6);
                if (currentWeekStart.getMonth() === endOfWeek.getMonth()) {
                    monthTitle.innerText = `${INDO_MONTHS[currentWeekStart.getMonth()]} ${currentWeekStart.getFullYear()}`;
                } else {
                    monthTitle.innerText = `${INDO_MONTHS_SHORT[currentWeekStart.getMonth()]} - ${INDO_MONTHS_SHORT[endOfWeek.getMonth()]} ${endOfWeek.getFullYear()}`;
                }
            }
        }

        function switchViewMode(mode, e) {
            if (e) e.stopPropagation();
            window.currentViewMode = mode;
            try { localStorage.setItem('ifik_view_mode', mode); } catch (err) {}

            const btnCal = document.getElementById('viewToggleCalendarBtn');
            const btnTbl = document.getElementById('viewToggleTableBtn');
            const calView = document.getElementById('calendarViewContainer');
            const tblView = document.getElementById('tableViewContainer');
            const calNav = document.getElementById('headerLeftCalendarNav');
            const tblTitle = document.getElementById('headerLeftTableTitle');

            if (mode === 'table') {
                if (btnCal) btnCal.classList.remove('active');
                if (btnTbl) btnTbl.classList.add('active');

                if (calNav) calNav.style.display = 'none';
                if (tblTitle) {
                    tblTitle.style.display = 'flex';
                    tblTitle.classList.remove('header-pane-enter');
                    void tblTitle.offsetWidth; // trigger reflow
                    tblTitle.classList.add('header-pane-enter');
                }

                if (calView) calView.style.display = 'none';
                if (tblView) {
                    tblView.style.display = 'block';
                    tblView.classList.remove('view-mode-container');
                    void tblView.offsetWidth; // trigger reflow
                    tblView.classList.add('view-mode-container');
                }

                currentTablePage = 1;
                renderTableView(lastFilteredData || window.bookingData);
            } else {
                if (btnCal) btnCal.classList.add('active');
                if (btnTbl) btnTbl.classList.remove('active');

                if (tblTitle) tblTitle.style.display = 'none';
                if (calNav) {
                    calNav.style.display = 'flex';
                    calNav.classList.remove('header-pane-enter');
                    void calNav.offsetWidth; // trigger reflow
                    calNav.classList.add('header-pane-enter');
                    updateHeaderMonthTitle();
                }

                if (tblView) tblView.style.display = 'none';
                if (calView) {
                    calView.style.display = 'block';
                    calView.classList.remove('view-mode-container');
                    void calView.offsetWidth; // trigger reflow
                    calView.classList.add('view-mode-container');
                }

                renderCalendar(lastFilteredData || window.bookingData);
            }
        }

        function renderTableView(customData) {
            const rawData = (typeof customData !== 'undefined') ? customData : (lastFilteredData || window.bookingData || []);
            let data = [...rawData];

            // Sort data according to selector
            const sortVal = document.getElementById('tableSortSelect') ? document.getElementById('tableSortSelect').value : 'date_desc';
            if (sortVal === 'date_desc') {
                data.sort((a, b) => {
                    const c = (b.tanggal_mulai || '').localeCompare(a.tanggal_mulai || '');
                    return c !== 0 ? c : (b.jam_mulai || '').localeCompare(a.jam_mulai || '');
                });
            } else if (sortVal === 'date_asc') {
                data.sort((a, b) => {
                    const c = (a.tanggal_mulai || '').localeCompare(b.tanggal_mulai || '');
                    return c !== 0 ? c : (a.jam_mulai || '').localeCompare(b.jam_mulai || '');
                });
            } else if (sortVal === 'room_asc') {
                data.sort((a, b) => (a.nama_ruangan || '').localeCompare(b.nama_ruangan || ''));
            } else if (sortVal === 'time_asc') {
                data.sort((a, b) => (a.jam_mulai || '').localeCompare(b.jam_mulai || ''));
            }

            // Stats update
            const totalCount = data.length;
            const pendingCount = data.filter(b => (b.status || '').toLowerCase().includes('pending') || (b.status || '').toLowerCase().includes('menunggu')).length;
            const approvedCount = data.filter(b => (b.status || '').toLowerCase().includes('setuju')).length;

            const statTotal = document.getElementById('tableStatTotal');
            const statPending = document.getElementById('tableStatPending');
            const statApproved = document.getElementById('tableStatApproved');
            if (statTotal) statTotal.innerText = totalCount;
            if (statPending) statPending.innerText = pendingCount;
            if (statApproved) statApproved.innerText = approvedCount;

            const cardsList = document.getElementById('tableCardsList');
            if (!cardsList) return;

            if (totalCount === 0) {
                cardsList.innerHTML = `
                    <div style="text-align: center; padding: 60px 20px; background: #ffffff; border-radius: 16px; border: 1.5px solid #e2e8f0; margin-top: 16px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" style="margin-bottom: 10px;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 4px;">Tidak Ada Data Peminjaman</h3>
                        <p style="font-size: 0.82rem; color: #64748b;">Tidak ada data peminjaman yang cocok dengan filter aktif.</p>
                    </div>
                `;
                const pagWrap = document.getElementById('tablePaginationWrap');
                if (pagWrap) pagWrap.style.display = 'none';
                return;
            }

            const pagWrap = document.getElementById('tablePaginationWrap');
            if (pagWrap) pagWrap.style.display = 'flex';

            // Pagination calculation
            const totalPages = Math.ceil(totalCount / tablePageSize);
            if (currentTablePage > totalPages) currentTablePage = totalPages;
            if (currentTablePage < 1) currentTablePage = 1;

            const startIndex = (currentTablePage - 1) * tablePageSize;
            const endIndex = Math.min(startIndex + tablePageSize, totalCount);
            const pageData = data.slice(startIndex, endIndex);

            // Render table rows
            let html = '';
            pageData.forEach(b => {
                const st = getStatusStyle(b.status);
                const jMulai = b.jam_mulai ? b.jam_mulai.substring(0, 5) : '00:00';
                const jSelesai = b.jam_selesai ? b.jam_selesai.substring(0, 5) : '00:00';

                html += `
                    <div class="table-row-card" onclick="openDetailBookingModal(${b.id})" title="Klik untuk melihat detail & approval">
                        <div class="tr-room-col">
                            <div class="tr-room-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1e293b" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            </div>
                            <div class="tr-room-info">
                                <div class="tr-room-code">${b.kode_ruangan || '-'}</div>
                                <div class="tr-room-name">${b.nama_ruangan || '-'}</div>
                            </div>
                        </div>

                        <div class="tr-user-time-col">
                            <div class="tr-pill-user" title="${b.nama_lengkap || '-'}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#1e293b" stroke-width="2.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                <span>${b.nama_lengkap || '-'}</span>
                            </div>
                            <div class="tr-pill-time">
                                <span>${jMulai} - ${jSelesai}</span>
                            </div>
                        </div>

                        <div class="tr-date-col">
                            ${formatIndoDateRange(b.tanggal_mulai, b.tanggal_selesai)}
                        </div>

                        <div class="tr-desc-col" title="${b.keterangan || '-'}">
                            <span class="tr-desc-text">${b.keterangan || '-'}</span>
                        </div>

                        <div class="tr-status-col">
                            <span class="tr-status-badge" style="color: ${st.badgeColor}; background: ${st.badgeBg};">
                                <span style="width: 7px; height: 7px; border-radius: 50%; background: ${st.dot};"></span>
                                ${st.label}
                            </span>
                        </div>
                    </div>
                `;
            });

            cardsList.innerHTML = html;

            // Render Pagination Info & Buttons
            const infoEl = document.getElementById('tablePaginationInfo');
            if (infoEl) {
                infoEl.innerText = `Menampilkan ${startIndex + 1} - ${endIndex} dari ${totalCount} data`;
            }

            const btnsEl = document.getElementById('tablePaginationBtns');
            if (btnsEl) {
                let pBtns = '';
                pBtns += `<button type="button" class="page-nav-btn" onclick="changeTablePage(${currentTablePage - 1})" ${currentTablePage === 1 ? 'disabled' : ''}>&larr; Prev</button>`;
                
                // Show up to 5 page numbers
                let startP = Math.max(1, currentTablePage - 2);
                let endP = Math.min(totalPages, startP + 4);
                if (endP - startP < 4) {
                    startP = Math.max(1, endP - 4);
                }

                for (let p = startP; p <= endP; p++) {
                    const isCur = (p === currentTablePage);
                    pBtns += `<button type="button" class="page-nav-btn ${isCur ? 'active' : ''}" onclick="changeTablePage(${p})" style="${isCur ? 'background:#ea580c; color:#fff; border-color:#ea580c;' : ''}">${p}</button>`;
                }

                pBtns += `<button type="button" class="page-nav-btn" onclick="changeTablePage(${currentTablePage + 1})" ${currentTablePage === totalPages ? 'disabled' : ''}>Next &rarr;</button>`;
                btnsEl.innerHTML = pBtns;
            }
        }

        function changeTablePage(page) {
            currentTablePage = page;
            renderTableView(lastFilteredData || window.bookingData);
            const container = document.getElementById('tableViewContainer');
            if (container) container.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function applyMultiFilters() {
            const rules = getActiveFilterRules();

            if (rules.length === 0) {
                lastFilteredData = null;
                if (window.currentViewMode === 'table') {
                    renderTableView(window.bookingData);
                } else {
                    renderCalendar(window.bookingData);
                }
                return;
            }

            const filtered = (window.bookingData || []).filter(booking => {
                // ALL active filter rules must match (AND condition)
                return rules.every(rule => {
                    const val = rule.value.toLowerCase();
                    const cat = rule.category;

                    if (cat === 'keyword') {
                        const kode = (booking.kode_ruangan || '').toLowerCase();
                        const nama = (booking.nama_ruangan || '').toLowerCase();
                        const user = (booking.nama_lengkap || '').toLowerCase();
                        const ket  = (booking.keterangan || '').toLowerCase();
                        const stat = (booking.status || '').toLowerCase();
                        return kode.includes(val) || nama.includes(val) || user.includes(val) || ket.includes(val) || stat.includes(val);
                    } else if (cat === 'kategori') {
                        const kat = (booking.nama_kategori || '').toLowerCase();
                        return kat.includes(val);
                    } else if (cat === 'ruangan') {
                        const kode = (booking.kode_ruangan || '').toLowerCase();
                        const nama = (booking.nama_ruangan || '').toLowerCase();
                        return kode.includes(val) || nama.includes(val);
                    } else if (cat === 'status') {
                        const stat = (booking.status || '').toLowerCase();
                        return stat.includes(val);
                    } else if (cat === 'tanggal') {
                        if (val.includes(' to ')) {
                            const parts = val.split(' to ');
                            const tStart = parts[0].trim();
                            const tEnd = (parts[1] || parts[0]).trim();
                            return booking.tanggal_mulai <= tEnd && booking.tanggal_selesai >= tStart;
                        } else {
                            return booking.tanggal_mulai <= val && booking.tanggal_selesai >= val;
                        }
                    }
                    return true;
                });
            });

            lastFilteredData = filtered;
            currentTablePage = 1;
            if (window.currentViewMode === 'table') {
                renderTableView(filtered);
            } else {
                renderCalendar(filtered);
            }
        }

        // ==========================================
        // RENDER CALENDAR GRID (FULLSCREEN)
        // ==========================================
        function renderCalendar(customData) {
            const dataToUse = (typeof customData !== 'undefined') ? customData : (window.bookingData || []);
            const daysHeader = document.getElementById('gcalDaysHeader');
            const grid = document.getElementById('gcalGrid');
            if (!daysHeader || !grid) return;

            // Month title update
            updateHeaderMonthTitle();

            // Generate Days Header (matching info_ruangan.css structure)
            const days = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
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
            daysHeader.innerHTML = headerHTML;

            // Generate Grid Scroll Body (matching info_ruangan.css structure)
            const startHour = 7;
            const endHour = 22;
            const pxPerHour = 48;

            let timeColHTML = `<div class="gcal-time-col">`;
            for (let i = startHour; i <= endHour; i++) {
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
                
                // Render events for this day
                dataToUse.forEach(booking => {
                    if (booking.tanggal_mulai <= dateString && booking.tanggal_selesai >= dateString) {
                        let sHour = 0, sMin = 0, eHour = 24, eMin = 0;

                        if (booking.tanggal_mulai === dateString) {
                            const p = (booking.jam_mulai || '00:00').split(':');
                            sHour = parseInt(p[0]);
                            sMin  = parseInt(p[1]);
                        }
                        if (booking.tanggal_selesai === dateString) {
                            const p = (booking.jam_selesai || '00:00').split(':');
                            eHour = parseInt(p[0]);
                            eMin  = parseInt(p[1]);
                        }

                        const gridStartHour = 7;
                        const topPx    = ((sHour - gridStartHour + 1) + sMin / 60) * pxPerHour;
                        const endPx    = ((eHour - gridStartHour + 1) + eMin / 60) * pxPerHour;
                        const heightPx = Math.max(endPx - topPx, 24);

                        const st = getStatusStyle(booking.status);
                        const timeLabel = `${sHour}:${sMin.toString().padStart(2,'0')} - ${eHour}:${eMin.toString().padStart(2,'0')}`;

                        dayColsHTML += `
                            <div class="gcal-event" onclick="openDetailBookingModal(${booking.id})" style="top:${topPx}px; height:${heightPx}px; background:${st.bg}; border-left:3px solid ${st.border}; cursor:pointer;"
                                 title="${booking.nama_ruangan} — ${booking.nama_lengkap} (${st.label})">
                                <div class="gcal-event-title">${booking.nama_ruangan}</div>
                                <div class="gcal-event-time">${timeLabel}</div>
                                <div class="gcal-event-status">${st.label}</div>
                            </div>
                        `;
                    }
                });

                dayColsHTML += `</div>`;
            }
            dayColsHTML += `</div>`;

            grid.innerHTML = timeColHTML + dayColsHTML;
        }

        // ==========================================
        // MASTER-DETAIL DAILY MODAL LOGIC
        // ==========================================
        let activeDailyBookings = [];
        let selectedDailyBookingId = null;
        let currentModalTargetDate = null;

        function openDetailBookingModal(id) {
            if (typeof bookingData === 'undefined' || !bookingData) return;
            const booking = bookingData.find(b => parseInt(b.id) === parseInt(id));
            if (!booking) return;

            // Target date from the clicked booking
            currentModalTargetDate = booking.tanggal_mulai;
            
            // Format nice Indonesian date title
            const dateObj = new Date(currentModalTargetDate + 'T00:00:00');
            const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const dateTitle = `${dayNames[dateObj.getDay()]}, ${dateObj.getDate()} ${monthNames[dateObj.getMonth()]} ${dateObj.getFullYear()}`;
            
            document.getElementById('modalDailyDateTitle').innerText = dateTitle;

            // Filter all bookings that cover this target date
            activeDailyBookings = bookingData.filter(b => b.tanggal_mulai <= currentModalTargetDate && b.tanggal_selesai >= currentModalTargetDate);

            // Sort by jam_mulai
            activeDailyBookings.sort((a, b) => (a.jam_mulai || '').localeCompare(b.jam_mulai || ''));

            // Clear search input
            const searchInput = document.getElementById('modalDailySearchInput');
            if (searchInput) searchInput.value = '';

            // Render list
            renderDailyModalList(activeDailyBookings, id);

            // Select clicked booking
            selectBookingInDailyModal(id);

            // Show modal
            document.getElementById('detailBookingModal').classList.add('show');
        }

        function renderDailyModalList(list, activeId) {
            const listEl = document.getElementById('modalDailyList');
            const countEl = document.getElementById('modalDailyCountBadge');
            if (!listEl) return;

            if (countEl) {
                countEl.innerText = `${list.length} Peminjaman Ruangan`;
            }

            if (!list || list.length === 0) {
                listEl.innerHTML = `
                    <div style="text-align: center; padding: 30px 16px; color: #94a3b8; font-size: 0.84rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 6px; opacity: 0.6;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <div>Tidak ada ruangan yang cocok</div>
                    </div>
                `;
                return;
            }

            let html = '';
            list.forEach(b => {
                const isActive = parseInt(b.id) === parseInt(activeId);
                const st = getStatusStyle(b.status);
                const jMulai = b.jam_mulai ? b.jam_mulai.substring(0, 5) : '00:00';
                const jSelesai = b.jam_selesai ? b.jam_selesai.substring(0, 5) : '00:00';

                html += `
                    <div class="modal-daily-item ${isActive ? 'active' : ''}" onclick="selectBookingInDailyModal(${b.id})">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px; gap: 6px;">
                            <span style="font-size: 0.72rem; font-weight: 700; color: #7c3aed; background: #ede9fe; padding: 2px 7px; border-radius: 6px;">
                                ${b.kode_ruangan || '-'}
                            </span>
                            <span style="display:inline-flex; align-items:center; gap:4px; font-size:0.7rem; font-weight:700; color:${st.badgeColor};">
                                <span style="width:6px; height:6px; border-radius:50%; background:${st.dot};"></span>
                                ${st.label}
                            </span>
                        </div>
                        <div style="font-size: 0.86rem; font-weight: 800; color: #0f172a; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            ${b.nama_ruangan || '-'}
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: #64748b;">
                            <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px;">
                                👤 ${b.nama_lengkap || '-'}
                            </span>
                            <span style="font-weight: 700; color: #ea580c;">
                                ⏰ ${jMulai} - ${jSelesai}
                            </span>
                        </div>
                    </div>
                `;
            });

            listEl.innerHTML = html;
        }

        function filterDailyModalList() {
            const query = (document.getElementById('modalDailySearchInput').value || '').toLowerCase().trim();
            if (!query) {
                renderDailyModalList(activeDailyBookings, selectedDailyBookingId);
                return;
            }

            const filtered = activeDailyBookings.filter(b => {
                const kode = (b.kode_ruangan || '').toLowerCase();
                const nama = (b.nama_ruangan || '').toLowerCase();
                const kat  = (b.nama_kategori || '').toLowerCase();
                const user = (b.nama_lengkap || '').toLowerCase();
                const ket  = (b.keterangan || '').toLowerCase();
                const stat = (b.status || '').toLowerCase();
                return kode.includes(query) || nama.includes(query) || kat.includes(query) || user.includes(query) || ket.includes(query) || stat.includes(query);
            });

            renderDailyModalList(filtered, selectedDailyBookingId);
        }

        function selectBookingInDailyModal(id) {
            selectedDailyBookingId = parseInt(id);
            const booking = bookingData.find(b => parseInt(b.id) === selectedDailyBookingId);
            if (!booking) return;

            // Highlight in list
            const items = document.querySelectorAll('.modal-daily-item');
            items.forEach(item => {
                if (item.getAttribute('onclick') && item.getAttribute('onclick').includes(`(${id})`)) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            });

            // Populate detail pane
            document.getElementById('detailBookingId').value = booking.id;
            document.getElementById('detailKodeRuangan').innerText = booking.kode_ruangan || '';
            document.getElementById('detailNamaRuangan').innerText = booking.nama_ruangan || '';
            document.getElementById('detailNamaLengkap').innerText = booking.nama_lengkap || '-';

            document.getElementById('detailTanggal').innerText = formatIndoDateRange(booking.tanggal_mulai, booking.tanggal_selesai);

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
        }

        function closeDetailBookingModal() {
            document.getElementById('detailBookingModal').classList.remove('show');
        }

        function reloadBookingData() {
            fetch(window.getUpdatedBookingsUrl)
            .then(r => r.json())
            .then(data => {
                window.bookingData = data;
                applyMultiFilters();

                // If modal is open, refresh daily list and active booking
                if (document.getElementById('detailBookingModal').classList.contains('show') && currentModalTargetDate) {
                    activeDailyBookings = window.bookingData.filter(b => b.tanggal_mulai <= currentModalTargetDate && b.tanggal_selesai >= currentModalTargetDate);
                    activeDailyBookings.sort((a, b) => (a.jam_mulai || '').localeCompare(b.jam_mulai || ''));
                    filterDailyModalList();
                    if (selectedDailyBookingId) {
                        selectBookingInDailyModal(selectedDailyBookingId);
                    }
                }
            }).catch(e => console.error(e));
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
                            Swal.fire({
                                icon: 'success',
                                title: 'Disetujui!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
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
                    Swal.fire({
                        icon: 'success',
                        title: 'Ditolak',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
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
                text: 'Apakah Anda yakin ingin menghapus jadwal peminjaman ini secara permanen?',
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
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            
                            reloadBookingData();
                            setTimeout(() => {
                                if (activeDailyBookings.length > 0) {
                                    selectBookingInDailyModal(activeDailyBookings[0].id);
                                } else {
                                    closeDetailBookingModal();
                                }
                            }, 300);
                        } else {
                            Swal.fire('Gagal', data.message, 'error');
                        }
                    }).catch(err => Swal.fire('Error', 'Terjadi kesalahan pada server', 'error'));
                }
            });
        }

        function nextWeek() { currentWeekStart.setDate(currentWeekStart.getDate() + 7); renderCalendar(); applyMultiFilters(); }
        function prevWeek() { currentWeekStart.setDate(currentWeekStart.getDate() - 7); renderCalendar(); applyMultiFilters(); }
        function goToToday() { currentWeekStart = new Date(); currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay()); renderCalendar(); applyMultiFilters(); }

        // Initial render & restore saved view mode + page size
        document.addEventListener('DOMContentLoaded', () => {
            try {
                const savedPageSize = localStorage.getItem('ifik_table_page_size');
                if (savedPageSize) {
                    tablePageSize = parseInt(savedPageSize) || 20;
                    const sizeSelect = document.getElementById('tablePageSizeSelect');
                    if (sizeSelect) sizeSelect.value = savedPageSize;
                }

                const savedViewMode = localStorage.getItem('ifik_view_mode');
                if (savedViewMode === 'table') {
                    switchViewMode('table');
                } else {
                    renderCalendar();
                }
            } catch (e) {
                renderCalendar();
            }
        });
    </script>
</body>
</html>