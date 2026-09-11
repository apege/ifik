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

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Info Ruangan & Calendar CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/curved_sidebar.css?v=' . filemtime(FCPATH . 'assets/css/curved_sidebar.css')) ?>">
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
            gap: 8px;
            flex: 0 0 120px;
            white-space: nowrap;
        }

        .gcal-header-center {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
            min-width: 0;
        }

        .gcal-header-right {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex: 0 0 120px;
        }

        @media (max-width: 768px) {
            .gcal-header-left { flex: 0 0 auto; }
            .gcal-header-right { display: none; }
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
            max-width: 580px;
            min-width: 320px;
        }

        .unified-search-pill {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            height: 42px;
            padding: 2px 4px 2px 8px;
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
            font-size: 0.82rem;
            font-weight: 600;
            color: #0f172a;
            outline: none;
            padding: 6px 8px;
            min-width: 0;
        }
        .unified-input-key::placeholder {
            color: #94a3b8;
            font-weight: 500;
        }

        .btn-submit-search-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            background: #ea580c;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 6px 14px;
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.18s ease;
            box-shadow: 0 2px 6px rgba(234, 88, 12, 0.25);
            flex-shrink: 0;
            margin-left: 4px;
            user-select: none;
            white-space: nowrap;
        }
        .btn-submit-search-pill:hover {
            background: #c2410c;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(234, 88, 12, 0.35);
        }
        .btn-submit-search-pill:active {
            transform: translateY(0);
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
            transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
            user-select: none;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }
        .stat-pill:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
        }
        .stat-pill:active {
            transform: translateY(0);
        }
        .stat-pill-total {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            color: #1e293b;
        }
        .stat-pill-total.active {
            background: #0f172a;
            border-color: #0f172a;
            color: #ffffff;
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.15);
        }
        .stat-pill-total.active .stat-label {
            color: #94a3b8 !important;
        }
        .stat-pill-total.active .stat-val {
            color: #ffffff !important;
        }

        .stat-pill-pending {
            background: #fffbeb;
            border: 1.5px solid #fef3c7;
            color: #b45309;
        }
        .stat-pill-pending.active {
            background: #fef3c7;
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.22);
        }

        .stat-pill-approved {
            background: #f0fdf4;
            border: 1.5px solid #dcfce7;
            color: #15803d;
        }
        .stat-pill-approved.active {
            background: #dcfce7;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.22);
        }

        .stat-pill-rejected {
            background: #fef2f2;
            border: 1.5px solid #fee2e2;
            color: #b91c1c;
        }
        .stat-pill-rejected.active {
            background: #fee2e2;
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.22);
        }
        .stat-label { font-weight: 600; }
        .stat-val { font-weight: 800; }

        /* SUB-MENU DROPDOWN FOR DISETUJUI BREAKDOWN */
        .approved-sub-menu {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            padding: 6px;
            box-shadow: 0 12px 28px -4px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.06);
            z-index: 1100;
            min-width: 220px;
            display: none;
            flex-direction: column;
            gap: 3px;
            animation: fadeInSubMenu 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes fadeInSubMenu {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .approved-sub-menu.show {
            display: flex;
        }
        .approved-sub-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 12px;
            border-radius: 9px;
            font-size: 0.78rem;
            font-weight: 700;
            color: #334155;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .approved-sub-item:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
        .approved-sub-item.active {
            background: #ecfdf5;
            color: #15803d;
        }
        .approved-sub-item .sub-count {
            font-size: 0.74rem;
            font-weight: 800;
            background: rgba(0, 0, 0, 0.05);
            padding: 2px 8px;
            border-radius: 999px;
            color: inherit;
        }

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
            grid-template-columns: 230px 170px 150px 1fr 170px;
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
        .th-col.th-desc {
            justify-content: flex-start;
            padding-left: 20px;
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
            grid-template-columns: 230px 170px 150px 1fr 170px;
            align-items: center;
            background: transparent;
            border-bottom: 1px solid #e8e2d5;
            padding: 16px 12px;
            gap: 16px;
            cursor: pointer;
            transition: background 0.15s ease, border-radius 0.15s ease;
            position: relative;
        }
        .table-row-card:hover {
            background: rgba(255, 255, 255, 0.7);
            border-radius: 12px;
            z-index: 50;
        }

        .tr-room-col {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
            position: relative;
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
            position: relative;
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

        /* FLOATING RICH ROOM DETAIL TOOLTIP ON HOVER (OPENS DOWNWARD) */
        .room-hover-tooltip {
            position: absolute;
            top: calc(100% + 8px);
            bottom: auto;
            left: 56px;
            background: #0f172a;
            color: #ffffff;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 0.78rem;
            box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.45), 0 8px 12px -6px rgba(15, 23, 42, 0.35);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-6px);
            transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s ease;
            pointer-events: none;
            z-index: 100;
            min-width: 220px;
            max-width: 320px;
            white-space: normal;
            line-height: 1.4;
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
        }
        .room-hover-tooltip::after {
            content: '';
            position: absolute;
            bottom: 100%;
            top: auto;
            left: 20px;
            border-width: 6px;
            border-style: solid;
            border-color: transparent transparent #0f172a transparent;
        }
        .tr-room-col:hover .room-hover-tooltip {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .rht-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 4px;
        }
        .rht-code {
            font-weight: 800;
            color: #fb923c;
            font-size: 0.82rem;
            letter-spacing: 0.02em;
        }
        .rht-cat {
            font-size: 0.68rem;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.15);
            color: #e2e8f0;
            padding: 2px 7px;
            border-radius: 6px;
            white-space: nowrap;
        }
        .rht-title {
            font-size: 0.84rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 4px;
            line-height: 1.3;
        }
        .rht-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.72rem;
            color: #94a3b8;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            padding-top: 5px;
            margin-top: 4px;
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
            overflow: visible;
            display: flex;
            align-items: center;
            padding: 0 8px 0 20px;
            position: relative;
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

        /* FLOATING RICH KETERANGAN TOOLTIP ON HOVER (OPENS DOWNWARD) */
        .desc-hover-tooltip {
            position: absolute;
            top: calc(100% + 8px);
            bottom: auto;
            left: 20px;
            background: #0f172a;
            color: #ffffff;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 0.78rem;
            box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.45), 0 8px 12px -6px rgba(15, 23, 42, 0.35);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-6px);
            transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s ease;
            pointer-events: none;
            z-index: 100;
            min-width: 200px;
            max-width: 360px;
            white-space: normal;
            line-height: 1.45;
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            word-break: break-word;
        }
        .desc-hover-tooltip::after {
            content: '';
            position: absolute;
            bottom: 100%;
            top: auto;
            left: 24px;
            border-width: 6px;
            border-style: solid;
            border-color: transparent transparent #0f172a transparent;
        }
        .tr-desc-col:hover .desc-hover-tooltip {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .dht-badge {
            font-size: 0.68rem;
            font-weight: 700;
            color: #fb923c;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 4px;
            display: block;
        }
        .dht-content {
            font-size: 0.8rem;
            font-weight: 500;
            color: #f8fafc;
            line-height: 1.4;
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

        @media (max-width: 1024px) {
            .unified-search-pill { width: 340px; }
            #mainAutocompleteList { width: 340px; }
        }
        @media (max-width: 768px) {
            .gcal-page-header {
                height: 56px;
                padding: 0 10px;
                flex-wrap: nowrap;
                gap: 6px;
            }
            .gcal-header-left {
                flex: 0 0 auto;
            }
            .gcal-header-center {
                flex: 1 1 auto;
                min-width: 0;
            }
            .search-filter-container {
                width: 100%;
                min-width: 0;
                max-width: 100%;
                gap: 4px;
            }
            .unified-search-pill {
                width: 100%;
                height: 38px;
                padding: 2px 4px 2px 6px;
            }
            .custom-cat-trigger {
                padding: 0 4px;
                font-size: 0.72rem;
                gap: 3px;
                max-width: 95px;
            }
            .custom-cat-trigger span {
                max-width: 75px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            .unified-search-input {
                font-size: 0.76rem;
                padding: 2px 4px;
                min-width: 0;
            }
            .btn-search-trigger {
                height: 30px;
                padding: 0 8px;
                font-size: 0.72rem;
                border-radius: 8px;
                gap: 4px;
            }
            .btn-standalone-add {
                height: 38px;
                padding: 0 8px;
                font-size: 0.72rem;
                flex-shrink: 0;
            }
            #extraRowsCard {
                width: calc(100vw - 20px);
                max-width: 400px;
                right: 0;
                top: calc(100% + 6px);
            }
            #mainAutocompleteList {
                width: calc(100vw - 20px);
                max-width: 400px;
                right: 0;
            }

            /* Responsive Calendar Body & Horizontal Scroll */
            .gcal-body {
                height: calc(100vh - 56px);
                overflow: hidden;
                display: flex;
                flex-direction: column;
                position: relative;
            }
            .gcal-days-header-wrapper {
                overflow: hidden;
                width: 100%;
                flex-shrink: 0;
                border-bottom: 1px solid rgba(30, 41, 59, 0.1);
                background: #ffffff;
            }
            .gcal-days-header {
                display: flex;
                min-width: 818px;
                width: 818px;
                padding-left: 48px;
                padding-right: 0;
                box-sizing: border-box;
                border-bottom: none;
                will-change: transform;
            }
            .gcal-day-header {
                min-width: 110px;
                width: 110px;
                flex: 0 0 110px;
                padding: 8px 0;
                box-sizing: border-box;
            }
            .gcal-day-name {
                font-size: 0.68rem;
                letter-spacing: 0.5px;
            }
            .gcal-day-num {
                font-size: 1.15rem;
                width: 36px;
                height: 36px;
            }
            .gcal-grid-scroll {
                overflow-x: auto !important;
                overflow-y: auto !important;
                -webkit-overflow-scrolling: touch;
                width: 100%;
                flex: 1 1 auto;
                scrollbar-width: thin;
                scrollbar-color: rgba(234, 88, 12, 0.4) transparent;
            }
            .gcal-grid {
                min-width: 818px;
                width: 818px;
                display: flex;
                position: relative;
            }
            .gcal-time-col {
                width: 48px;
                min-width: 48px;
                flex: 0 0 48px;
                position: sticky;
                left: 0;
                z-index: 25;
                background: #fbf7f1;
                border-right: 1px solid rgba(30, 41, 59, 0.1);
                box-shadow: 2px 0 6px rgba(0, 0, 0, 0.04);
            }
            .gcal-time-label span {
                font-size: 0.64rem;
                right: 6px;
            }
            .gcal-day-cols {
                min-width: 770px;
                width: 770px;
                flex: 0 0 770px;
                display: flex;
            }
            .gcal-day-col {
                min-width: 110px;
                width: 110px;
                flex: 0 0 110px;
                border-left: 1px solid rgba(30, 41, 59, 0.08);
                position: relative;
            }
            .gcal-event {
                left: 3px;
                right: 3px;
                padding: 4px 6px;
                border-radius: 8px;
                border-left-width: 3px;
                box-shadow: 0 2px 6px rgba(0,0,0,0.12);
            }
            .gcal-event-title {
                font-size: 0.72rem;
                font-weight: 800;
                line-height: 1.2;
                white-space: normal;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .gcal-event-time {
                font-size: 0.64rem;
                font-weight: 700;
                opacity: 0.9;
                margin-top: 2px;
                white-space: nowrap;
            }
            .gcal-event-status {
                font-size: 0.60rem;
                font-weight: 700;
                opacity: 0.85;
                margin-top: 1px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            /* HIDE DESKTOP TABLE COLUMN HEADER ON MOBILE */
            .table-column-header {
                display: none !important;
            }

            /* HIDE DESKTOP TABLE TITLE IN TOPBAR ON MOBILE TO GIVE MAXIMUM SPACE FOR SEARCH */
            #headerLeftTableTitle {
                display: none !important;
            }

            .table-view-container {
                padding: 14px 12px 30px;
                height: calc(100vh - 56px);
            }
            .table-view-inner {
                gap: 12px;
            }

            .table-cards-list {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            /* TRANSFORM EACH TABLE ROW INTO A CLEAN MODERN CARD */
            .table-row-card {
                display: grid;
                grid-template-columns: 1fr auto;
                gap: 8px 10px;
                padding: 14px 14px;
                background: #ffffff;
                border: 1px solid rgba(234, 88, 12, 0.16);
                border-radius: 16px;
                box-shadow: 0 3px 12px rgba(0, 0, 0, 0.03);
                min-height: auto;
                box-sizing: border-box;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
                border-bottom: 1px solid rgba(234, 88, 12, 0.16);
            }
            .table-row-card:active, .table-row-card:hover {
                background: #ffffff;
                transform: translateY(-2px);
                box-shadow: 0 6px 18px rgba(234, 88, 12, 0.12);
            }

            /* ROW 1 LEFT: Ruangan Icon & Code/Name */
            .tr-room-col {
                grid-column: 1 / 2;
                order: 1;
                display: flex;
                align-items: center;
                gap: 10px;
                min-width: 0;
            }
            .tr-room-icon {
                width: 36px;
                height: 36px;
                border-radius: 10px;
                border-width: 1.5px;
                flex-shrink: 0;
            }
            .tr-room-icon svg {
                width: 17px;
                height: 17px;
            }
            .tr-room-info {
                flex: 1;
                min-width: 0;
            }
            .tr-room-code {
                font-size: 0.92rem;
                font-weight: 800;
                color: #0f172a;
                line-height: 1.25;
                white-space: normal;
                word-break: break-word;
            }
            .tr-room-name {
                font-size: 0.76rem;
                color: #64748b;
                margin-top: 2px;
                line-height: 1.25;
                white-space: normal;
                word-break: break-word;
            }

            /* ROW 1 RIGHT: Status Badge */
            .tr-status-col {
                grid-column: 2 / 3;
                order: 2;
                width: auto;
                justify-content: flex-end;
                align-items: flex-start;
                flex-shrink: 0;
                padding-left: 0;
            }
            .tr-status-badge {
                width: auto;
                height: 24px;
                padding: 2px 10px;
                font-size: 0.70rem;
                font-weight: 700;
                border-radius: 999px;
                gap: 5px;
                white-space: nowrap;
                display: inline-flex;
                align-items: center;
            }

            /* ROW 2: User & Time Badges Side-by-Side */
            .tr-user-time-col {
                grid-column: 1 / 3;
                order: 3;
                display: flex;
                flex-direction: row;
                flex-wrap: wrap;
                align-items: center;
                width: 100%;
                gap: 6px;
                margin-top: 2px;
            }
            .tr-pill-user {
                width: auto;
                flex: 0 0 auto;
                max-width: 100%;
                height: 26px;
                padding: 0 10px;
                font-size: 0.74rem;
                font-weight: 600;
                border-radius: 999px;
                box-sizing: border-box;
                justify-content: flex-start;
            }
            .tr-pill-user span {
                max-width: 150px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            .tr-pill-time {
                width: auto;
                flex: 0 0 auto;
                height: 26px;
                padding: 0 10px;
                font-size: 0.74rem;
                font-weight: 700;
                border-radius: 999px;
                box-sizing: border-box;
                justify-content: flex-start;
            }

            /* ROW 3: Date */
            .tr-date-col {
                grid-column: 1 / 3;
                order: 4;
                font-size: 0.76rem;
                font-weight: 700;
                text-align: left;
                color: #334155;
                display: flex;
                align-items: center;
                gap: 5px;
                margin-top: 1px;
            }
            .tr-date-col::before {
                content: '📅 ';
                font-size: 0.74rem;
            }

            /* ROW 4: Description */
            .tr-desc-col {
                grid-column: 1 / 3;
                order: 5;
                padding: 0;
                width: 100%;
                margin-top: 1px;
            }
            .tr-desc-text {
                font-size: 0.76rem;
                color: #64748b;
                line-height: 1.35;
                white-space: normal;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            /* Hide tooltips on mobile touch */
            .table-row-card .room-hover-tooltip,
            .table-row-card .desc-hover-tooltip {
                display: none !important;
            }

            .table-pagination-wrap {
                flex-direction: column;
                gap: 10px;
                align-items: center;
                justify-content: center;
            }
        }
            /* ===== CURVED ANIMATED SIDEBAR & CHIPS STYLING ===== */
        .curved-sidebar-toggle-btn {
            position: relative !important;
            top: auto !important;
            left: auto !important;
            margin-right: 4px;
            flex-shrink: 0;
        }
        .sb-chip {
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #475569;
            cursor: pointer;
            transition: all 0.15s ease;
            white-space: nowrap;
        }
        .sb-chip:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
        }
        .sb-chip.active {
            background: #ea580c;
            border-color: #ea580c;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(234, 88, 12, 0.25);
        }

        .btn-sidebar-kembali {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 8px 12px;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            color: #334155;
            font-size: 0.78rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            margin-bottom: 12px;
            box-sizing: border-box;
        }
        .btn-sidebar-kembali:hover {
            background: #fff7ed;
            border-color: #ea580c;
            color: #ea580c;
            transform: translateX(-3px);
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.12);
        }

        /* SIDEBAR CALENDAR NAV CONTROLS */
        .sb-btn-today {
            padding: 3px 10px;
            font-size: 0.74rem;
            font-weight: 700;
            border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            background: #ffffff;
            color: #334155;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .sb-btn-today:hover {
            background: #ea580c;
            color: #ffffff;
            border-color: #ea580c;
            box-shadow: 0 2px 6px rgba(234, 88, 12, 0.2);
        }

        .sb-nav-arrow {
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            background: #ffffff;
            color: #475569;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .sb-nav-arrow:hover {
            background: #ea580c;
            color: #ffffff;
            border-color: #ea580c;
        }

        .sb-month-year-btn {
            background: #ffffff !important;
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 9px !important;
            padding: 4px 8px !important;
        }
        .sb-month-year-btn:hover {
            border-color: #ea580c !important;
            background: #fff7ed !important;
        }

        /* Category Section Headers & Role Badge in Sidebar */
        .curved-nav-category {
            font-size: 0.68rem;
            font-weight: 800;
            color: #78350f;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 8px 10px 4px 10px;
            margin-top: 4px;
            user-select: none;
        }
        .curved-nav-category.has-divider {
            border-top: 1px solid rgba(245, 158, 11, 0.2);
            margin-top: 8px;
            padding-top: 10px;
        }

        .curved-header-role-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            background: rgba(245, 158, 11, 0.22);
            border: 1px solid rgba(245, 158, 11, 0.38);
            border-radius: 9999px;
            font-size: 0.65rem;
            font-weight: 800;
            color: #78350f;
            letter-spacing: 0.02em;
        }

        .curved-nav-item.is-current {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }
        .curved-nav-item.is-current .curved-nav-heading,
        .curved-nav-item.is-current .curved-nav-letter {
            color: #ea580c !important;
            font-weight: 800 !important;
        }
        .curved-nav-item.is-current .curved-nav-3d-wrap {
            transform: translateY(-1px) scale(1.08);
            filter: drop-shadow(0 4px 8px rgba(234, 88, 12, 0.22));
        }
    </style>
</head>
<body>
    <!-- =========================================================
         CURVED ANIMATED SIDEBAR (CONTROL & NAVIGATION CENTER)
         ========================================================= -->
    <!-- Backdrop Blur Overlay -->
    <div id="curvedSidebarBackdrop" class="curved-sidebar-backdrop"></div>

    <?php
    $sessionRoleId = (int)$this->session->userdata('role_id');
    $isLoggedIn = $this->session->userdata('logged_in');

    switch ($sessionRoleId) {
        case 3: // Kaur / Ka Lab
            $backUrl = site_url('kaur/approval');
            $backLabel = 'Menu Utama Kaur';
            break;
        case 2: // Laboran
            $backUrl = site_url('laboran/booking');
            $backLabel = 'Dashboard Laboran';
            break;
        case 1: // Admin
            $backUrl = site_url('admin');
            $backLabel = 'Dashboard Admin';
            break;
        case 4: // Dosen
            $backUrl = site_url('dosen/bimbingan');
            $backLabel = 'Menu Dosen Pembimbing';
            break;
        case 6: // Koordinator TA
            $backUrl = site_url('koordinatorta');
            $backLabel = 'Dashboard Koordinator TA';
            break;
        default: // Mahasiswa / Guest
            $backUrl = site_url('dashboard');
            $backLabel = 'Beranda Utama';
            break;
    }
    ?>

    <!-- Sliding Sidebar Panel with Morphing Curved SVG (Left Side) -->
    <aside id="curvedSidebarPanel" class="curved-sidebar-panel is-active" aria-label="Sidebar Navigasi & Kontrol" style="width: 320px;">
        <div class="curved-sidebar-inner" style="padding-top: 55px; gap: 14px;">
            
            <div>
                <!-- 1. Header Control with Close (X) Button -->
                <div class="curved-sidebar-header" style="justify-content: flex-start; gap: 10px;">
                    <button type="button" class="curved-sidebar-close-btn" id="curvedSidebarCloseBtn" aria-label="Tutup Sidebar" title="Tutup Sidebar (Esc)">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    <p style="margin: 0;"><i class="fa-solid fa-sliders" style="color: #ea580c;"></i> Kontrol & Navigasi</p>
                </div>

                <!-- Tombol Kembali Cepat ke Dashboard (Auto-scroll ke Informasi Ruangan) -->
                <a href="<?= site_url('dashboard#info_ruangan'); ?>" class="btn-sidebar-kembali" title="Kembali ke Dashboard - Informasi Ruangan">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Kembali ke Dashboard</span>
                </a>
                
                <!-- 2. Mode Tampilan Switcher -->
                <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 4px; display: flex; gap: 4px; margin-bottom: 14px;">
                    <button type="button" id="sbViewCalBtn" class="btn-sb-mode" onclick="switchViewMode('calendar', event)" style="flex: 1; padding: 7px 10px; border-radius: 8px; border: none; font-size: 0.76rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s; background: #0f172a; color: #fff;">
                        <i class="fa-solid fa-calendar-days"></i> Kalender
                    </button>
                    <button type="button" id="sbViewTblBtn" class="btn-sb-mode" onclick="switchViewMode('table', event)" style="flex: 1; padding: 7px 10px; border-radius: 8px; border: none; font-size: 0.76rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s; background: transparent; color: #64748b;">
                        <i class="fa-solid fa-table-list"></i> Tabel
                    </button>
                </div>

                <!-- 3. Navigasi Jadwal Kalender (Tampil Hanya Saat Mode Kalender) -->
                <div id="sbCalendarNavSection" style="margin-bottom: 14px; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 10px 12px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                        <label style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; display: flex; align-items: center; gap: 6px; margin: 0;">
                            <i class="fa-solid fa-calendar-week" style="color: #ea580c;"></i> Navigasi Kalender
                        </label>
                        <button type="button" onclick="goToToday()" class="sb-btn-today" title="Lompat ke Hari Ini">
                            Today
                        </button>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px; position: relative;">
                        <!-- Navigasi Panah < > -->
                        <div style="display: flex; gap: 4px; flex-shrink: 0;">
                            <button type="button" onclick="prevWeek()" class="sb-nav-arrow" title="Minggu Sebelumnya">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
                            </button>
                            <button type="button" onclick="nextWeek()" class="sb-nav-arrow" title="Minggu Berikutnya">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </button>
                        </div>

                        <!-- Month Year Picker Trigger in Sidebar -->
                        <div class="month-year-picker-wrap" id="sbMonthYearPickerWrap" style="position: relative; flex: 1; display: flex; justify-content: flex-end;">
                            <button type="button" class="month-year-btn sb-month-year-btn" id="sbMonthYearBtn" onclick="toggleMonthYearPicker(event, 'sb')" title="Pilih Bulan & Tahun">
                                <span id="sbMonthTitle" style="font-size: 0.86rem; font-weight: 800; color: #0f172a; white-space: nowrap;">-</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </button>

                            <!-- Sidebar Month-Year Popover Dropdown -->
                            <div class="month-year-popover sb-month-popover" id="sbMonthYearPopover" style="right: 0; left: auto; width: 250px;">
                                <!-- Year Navigation Header -->
                                <div class="my-year-nav">
                                    <button type="button" onclick="changePickerYear(-1, event, 'sb')" title="Tahun Sebelumnya">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
                                    </button>
                                    <span id="sbPickerYearDisplay" style="font-size: 1rem; font-weight: 800; color: #0f172a;">2026</span>
                                    <button type="button" onclick="changePickerYear(1, event, 'sb')" title="Tahun Selanjutnya">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                    </button>
                                </div>

                                <!-- Month Grid (12 Months) -->
                                <div class="my-months-grid" id="sbPickerMonthsGrid">
                                    <!-- Jan - Des buttons rendered via JS -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3B. Kontrol Filter & Urutan Tabel (Tampil Khusus Mode Tabel) -->
                <div id="sbTableControlsSection" style="display: none; margin-bottom: 14px; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 12px; flex-direction: column; gap: 10px;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <label style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; display: flex; align-items: center; gap: 6px; margin: 0;">
                            <i class="fa-solid fa-chart-pie" style="color: #ea580c;"></i> Ringkasan & Filter Status
                        </label>
                    </div>

                    <!-- Quick Status Filter Pills in Sidebar -->
                    <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                        <div class="stat-pill stat-pill-total active" id="statPillTotal" onclick="filterByStatPill('all')" title="Klik untuk menampilkan semua data" style="flex: 1 1 calc(50% - 3px); justify-content: center; padding: 5px 8px; font-size: 0.76rem;">
                            <span class="stat-label" style="color: #64748b;">Total:</span>
                            <span class="stat-val" id="tableStatTotal" style="color: #0f172a;">0</span>
                        </div>

                        <div class="stat-pill stat-pill-pending" id="statPillPending" onclick="filterByStatPill('pending')" title="Klik untuk memfilter status Menunggu" style="flex: 1 1 calc(50% - 3px); justify-content: center; padding: 5px 8px; font-size: 0.76rem;">
                            <span class="stat-dot" style="width: 7px; height: 7px; border-radius: 50%; background: #f59e0b; display: inline-block;"></span>
                            <span class="stat-label">Menunggu:</span>
                            <span class="stat-val" id="tableStatPending">0</span>
                        </div>

                        <!-- Disetujui with interactive dropdown options -->
                        <div class="stat-pill-approved-wrap" style="position: relative; width: 100%;">
                            <div class="stat-pill stat-pill-approved" id="statPillApproved" onclick="toggleApprovedSubMenu(event)" title="Klik untuk memilih filter status Disetujui" style="width: 100%; justify-content: space-between; padding: 6px 10px; font-size: 0.76rem; box-sizing: border-box;">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <span class="stat-dot" style="width: 7px; height: 7px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                                    <span class="stat-label" id="approvedStatLabel">Disetujui:</span>
                                    <span class="stat-val" id="tableStatApproved">0</span>
                                </div>
                                <svg id="approvedStatChevron" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-left: 2px; transition: transform 0.2s ease;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>

                            <!-- Dropdown Sub-Menu Disetujui -->
                            <div class="approved-sub-menu" id="approvedSubMenu" style="width: 100%; top: calc(100% + 4px); z-index: 100050; box-sizing: border-box;">
                                <div class="approved-sub-item active" id="subOptAllApproved" onclick="selectApprovedSub('all_approved', event)">
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <span class="stat-dot" style="width: 7px; height: 7px; border-radius: 50%; background: #10b981;"></span>
                                        <span>Semua Disetujui</span>
                                    </div>
                                    <span class="sub-count" id="subCountAllApproved">0</span>
                                </div>
                                <div class="approved-sub-item" id="subOptLaboran" onclick="selectApprovedSub('laboran', event)">
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <span class="stat-dot" style="width: 7px; height: 7px; border-radius: 50%; background: #3b82f6;"></span>
                                        <span>Disetujui Laboran</span>
                                    </div>
                                    <span class="sub-count" id="subCountLaboran">0</span>
                                </div>
                                <div class="approved-sub-item" id="subOptKaur" onclick="selectApprovedSub('kaur', event)">
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <span class="stat-dot" style="width: 7px; height: 7px; border-radius: 50%; background: #22c55e;"></span>
                                        <span>Disetujui Ka. Ur</span>
                                    </div>
                                    <span class="sub-count" id="subCountKaur">0</span>
                                </div>
                                <div class="approved-sub-item" id="subOptAdmin" onclick="selectApprovedSub('admin', event)">
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <span class="stat-dot" style="width: 7px; height: 7px; border-radius: 50%; background: #8b5cf6;"></span>
                                        <span>Disetujui Admin</span>
                                    </div>
                                    <span class="sub-count" id="subCountAdmin">0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Ditolak Stat Pill -->
                        <div class="stat-pill stat-pill-rejected" id="statPillRejected" onclick="filterByStatPill('rejected')" title="Klik untuk memfilter status Ditolak" style="width: 100%; justify-content: space-between; padding: 6px 10px; font-size: 0.76rem; box-sizing: border-box;">
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <span class="stat-dot" style="width: 7px; height: 7px; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
                                <span class="stat-label">Ditolak:</span>
                            </div>
                            <span class="stat-val" id="tableStatRejected">0</span>
                        </div>
                    </div>

                    <!-- Tampilkan & Urutkan Selects -->
                    <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 4px; border-top: 1px solid #e2e8f0; padding-top: 8px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                            <label for="tablePageSizeSelect" style="font-size: 0.74rem; font-weight: 700; color: #64748b; white-space: nowrap;">Tampilkan:</label>
                            <select id="tablePageSizeSelect" class="custom-table-select" onchange="changeTablePageSize(this.value)" style="flex: 1; padding: 4px 26px 4px 10px; font-size: 0.76rem;">
                                <option value="10">10 baris</option>
                                <option value="20" selected>20 baris</option>
                                <option value="50">50 baris</option>
                                <option value="100">100 baris</option>
                            </select>
                        </div>

                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                            <label for="tableSortSelect" style="font-size: 0.74rem; font-weight: 700; color: #64748b; white-space: nowrap;">Urutkan:</label>
                            <select id="tableSortSelect" class="custom-table-select" onchange="renderTableView()" style="flex: 1; padding: 4px 26px 4px 10px; font-size: 0.76rem;">
                                <option value="date_desc">Tanggal (Terbaru)</option>
                                <option value="date_asc">Tanggal (Terlama)</option>
                                <option value="room_asc">Nama Ruangan (A-Z)</option>
                                <option value="time_asc">Jam Mulai (Pagi - Malam)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 4. Quick Navigasi Halaman Sistem (Matching 3D Icons, Categories & Role System) -->
                <?php
                $sessionRoleId = (int)$this->session->userdata('role_id');
                $isLoggedIn = $this->session->userdata('logged_in');

                // Fallback cerdas jika role belum ada di session (misal saat direct link/preview)
                $currentUri = trim(uri_string(), '/');
                if ($sessionRoleId === 0) {
                    if (strpos($currentUri, 'laboran') === 0) {
                        $sessionRoleId = 2; // Laboran
                    } elseif (strpos($currentUri, 'kaur') === 0) {
                        $sessionRoleId = 3; // Kaur / Ka Lab
                    } elseif (strpos($currentUri, 'dosen') === 0 || strpos($currentUri, 'dosenwali') === 0) {
                        $sessionRoleId = 4; // Dosen
                    } elseif (strpos($currentUri, 'koordinatorta') === 0 || strpos($currentUri, 'koordinator') === 0) {
                        $sessionRoleId = 6; // Koordinator TA
                    } elseif (strpos($currentUri, 'admin') === 0 || strpos($currentUri, 'kelolabooking') === 0) {
                        $sessionRoleId = 1; // Admin
                    }
                }

                $roleBadgeMap = [
                    1 => 'Admin Panel',
                    2 => 'Laboran',
                    3 => 'Ka. Ur / Ka Lab',
                    4 => 'Portal Dosen',
                    5 => 'Mahasiswa',
                    6 => 'Koordinator TA',
                    7 => 'Ketua KK'
                ];
                $activeRoleBadge = $roleBadgeMap[$sessionRoleId] ?? 'Portal IFIK';

                switch ($sessionRoleId) {
                    case 2: // Laboran (Staff Operasional Laboratorium)
                        $defaultNavItems = [
                            ['category' => 'Operasional Laboratorium'],
                            ['heading' => 'Approval Peminjaman', 'href' => site_url('laboran/booking'), 'icon_3d' => 'assets/images/icons_3d/approval.png'],
                            ['heading' => 'Ajukan Peminjaman Ruangan', 'href' => site_url('ajukan-booking'), 'icon_3d' => 'assets/images/icons_3d/ruangan.png'],
                            ['heading' => 'Tanda Tangan Digital', 'href' => site_url('laboran/tanda-tangan'), 'icon_3d' => 'assets/images/icons_3d/preview.png'],
                            ['heading' => 'Import Email & Token', 'href' => site_url('laboran/import-email'), 'icon_3d' => 'assets/images/icons_3d/email_token.png'],

                            ['category' => 'Layanan Ticketing', 'has_divider' => true],
                            ['heading' => 'Respon Ticketing Lab', 'href' => site_url('laboran/respon-ticketing'), 'icon_3d' => 'assets/images/icons_3d/unit_ticketing.png'],
                            ['heading' => 'Buat Tiket Kendala', 'href' => site_url('laboran/ticketing/input'), 'icon_3d' => 'assets/images/icons_3d/ticketing.png'],
                            ['heading' => 'Riwayat Tiket Saya', 'href' => site_url('laboran/ticketing/riwayat'), 'icon_3d' => 'assets/images/icons_3d/preview.png'],

                            ['category' => 'Informasi & Jadwal', 'has_divider' => true],
                            ['heading' => 'Kalender Jadwal', 'href' => site_url('kalender'), 'icon_3d' => 'assets/images/icons_3d/kalender.png'],
                            ['heading' => 'Keluar', 'href' => site_url('login/logout'), 'icon_3d' => 'assets/images/icons_3d/logout.png'],
                        ];
                        break;

                    case 3: // Kaur / Ka Lab (Kepala Urusan / Kepala Lab & Dosen)
                        $defaultNavItems = [
                            ['category' => 'Persetujuan Resmi & Lab'],
                            ['heading' => 'Approval Peminjaman', 'href' => site_url('kaur/approval'), 'icon_3d' => 'assets/images/icons_3d/approval.png'],
                            ['heading' => 'Ajukan Peminjaman Ruangan', 'href' => site_url('ajukan-booking'), 'icon_3d' => 'assets/images/icons_3d/ruangan.png'],

                            ['category' => 'Portal Akademik & Dosen', 'has_divider' => true],
                            ['heading' => 'Dosen Pembimbing', 'href' => site_url('dosen/bimbingan'), 'icon_3d' => 'assets/images/icons_3d/daftar.png'],
                            ['heading' => 'Dosen Penguji', 'href' => site_url('dosen/penguji'), 'icon_3d' => 'assets/images/icons_3d/sidang.png'],
                            ['heading' => 'Dosen Wali', 'href' => site_url('dosen/wali'), 'icon_3d' => 'assets/images/icons_3d/home.png'],
                            ['heading' => 'Tanda Tangan Digital', 'href' => site_url('dosen/tanda-tangan'), 'icon_3d' => 'assets/images/icons_3d/preview.png'],

                            ['category' => 'Layanan Ticketing', 'has_divider' => true],
                            ['heading' => 'Respon Ticketing', 'href' => site_url('dosen/respon-ticketing'), 'icon_3d' => 'assets/images/icons_3d/unit_ticketing.png'],
                            ['heading' => 'Buat Tiket Kendala', 'href' => site_url('dosen/ticketing/input'), 'icon_3d' => 'assets/images/icons_3d/ticketing.png'],
                            ['heading' => 'Riwayat Tiket Saya', 'href' => site_url('dosen/ticketing/riwayat'), 'icon_3d' => 'assets/images/icons_3d/preview.png'],

                            ['category' => 'Informasi & Jadwal', 'has_divider' => true],
                            ['heading' => 'Kalender Jadwal', 'href' => site_url('kalender'), 'icon_3d' => 'assets/images/icons_3d/kalender.png'],
                            ['heading' => 'Keluar', 'href' => site_url('login/logout'), 'icon_3d' => 'assets/images/icons_3d/logout.png'],
                        ];
                        break;

                    case 4: // Dosen
                        $defaultNavItems = [
                            ['category' => 'Bimbingan & Pengujian'],
                            ['heading' => 'Dosen Pembimbing', 'href' => site_url('dosen/bimbingan'), 'icon_3d' => 'assets/images/icons_3d/daftar.png'],
                            ['heading' => 'Dosen Penguji', 'href' => site_url('dosen/penguji'), 'icon_3d' => 'assets/images/icons_3d/sidang.png'],
                            ['heading' => 'Dosen Wali', 'href' => site_url('dosen/wali'), 'icon_3d' => 'assets/images/icons_3d/approval.png'],
                            ['heading' => 'Tanda Tangan Digital', 'href' => site_url('dosen/tanda-tangan'), 'icon_3d' => 'assets/images/icons_3d/preview.png'],

                            ['category' => 'Layanan Ticketing', 'has_divider' => true],
                            ['heading' => 'Respon Ticketing', 'href' => site_url('dosen/respon-ticketing'), 'icon_3d' => 'assets/images/icons_3d/unit_ticketing.png'],
                            ['heading' => 'Buat Tiket Kendala', 'href' => site_url('dosen/ticketing/input'), 'icon_3d' => 'assets/images/icons_3d/ticketing.png'],
                            ['heading' => 'Riwayat Tiket Saya', 'href' => site_url('dosen/ticketing/riwayat'), 'icon_3d' => 'assets/images/icons_3d/preview.png'],

                            ['category' => 'Fasilitas & Jadwal', 'has_divider' => true],
                            ['heading' => 'Ajukan Peminjaman Ruangan', 'href' => site_url('ajukan-booking'), 'icon_3d' => 'assets/images/icons_3d/ruangan.png'],
                            ['heading' => 'Kalender Jadwal', 'href' => site_url('kalender'), 'icon_3d' => 'assets/images/icons_3d/kalender.png'],
                            ['heading' => 'Keluar', 'href' => site_url('login/logout'), 'icon_3d' => 'assets/images/icons_3d/logout.png'],
                        ];
                        break;

                    case 1: // Admin System
                        $defaultNavItems = [
                            ['category' => 'Pusat Kendali'],
                            ['heading' => 'Dashboard Control', 'href' => site_url('admin'), 'icon_3d' => 'assets/images/icons_3d/home.png'],
                            ['heading' => 'Approval Peminjaman', 'href' => site_url('kelolabooking'), 'icon_3d' => 'assets/images/icons_3d/approval.png'],
                            ['heading' => 'Ajukan Peminjaman Ruangan', 'href' => site_url('ajukan-booking'), 'icon_3d' => 'assets/images/icons_3d/ruangan.png'],

                            ['category' => 'Manajemen Sistem', 'has_divider' => true],
                            ['heading' => 'Import Email & Token', 'href' => site_url('admin/import-email'), 'icon_3d' => 'assets/images/icons_3d/email_token.png'],
                            ['heading' => 'Pengaturan Unit Ticketing', 'href' => site_url('admin#unit-ticketing'), 'icon_3d' => 'assets/images/icons_3d/unit_ticketing.png'],
                            ['heading' => 'Respon Ticketing Lab', 'href' => site_url('laboran/respon-ticketing'), 'icon_3d' => 'assets/images/icons_3d/ticketing.png'],
                            ['heading' => 'Riwayat Log History', 'href' => site_url('admin/log_history'), 'icon_3d' => 'assets/images/icons_3d/preview.png'],

                            ['category' => 'Informasi & Jadwal', 'has_divider' => true],
                            ['heading' => 'Kalender Jadwal', 'href' => site_url('kalender'), 'icon_3d' => 'assets/images/icons_3d/kalender.png'],
                            ['heading' => 'Keluar', 'href' => site_url('login/logout'), 'icon_3d' => 'assets/images/icons_3d/logout.png'],
                        ];
                        break;

                    case 6: // Koordinator TA
                        $defaultNavItems = [
                            ['category' => 'Pengelolaan Tugas Akhir'],
                            ['heading' => 'Dashboard Utama', 'href' => site_url('koordinatorta'), 'icon_3d' => 'assets/images/icons_3d/home.png'],
                            ['heading' => 'Pendaftaran TA', 'href' => site_url('koordinatorta#pendaftaran'), 'icon_3d' => 'assets/images/icons_3d/daftar.png'],
                            ['heading' => 'Tahap Preview 2', 'href' => site_url('koordinatorta#preview2'), 'icon_3d' => 'assets/images/icons_3d/preview.png'],
                            ['heading' => 'Jadwal Sidang TA', 'href' => site_url('koordinatorta#sidang'), 'icon_3d' => 'assets/images/icons_3d/sidang.png'],

                            ['category' => 'Fasilitas & Jadwal', 'has_divider' => true],
                            ['heading' => 'Ajukan Peminjaman Ruangan', 'href' => site_url('ajukan-booking'), 'icon_3d' => 'assets/images/icons_3d/ruangan.png'],
                            ['heading' => 'Kalender Jadwal', 'href' => site_url('kalender'), 'icon_3d' => 'assets/images/icons_3d/kalender.png'],
                            ['heading' => 'Keluar', 'href' => site_url('login/logout'), 'icon_3d' => 'assets/images/icons_3d/logout.png'],
                        ];
                        break;

                    default: // Mahasiswa (5) / Tamu
                        $defaultNavItems = [
                            ['category' => 'Menu Mahasiswa'],
                            ['heading' => 'Dashboard Utama', 'href' => site_url('dashboard'), 'icon_3d' => 'assets/images/icons_3d/home.png'],
                            ['heading' => 'Ajukan Peminjaman Ruangan', 'href' => site_url('ajukan-booking'), 'icon_3d' => 'assets/images/icons_3d/ruangan.png'],
                            ['heading' => 'Kalender Jadwal', 'href' => site_url('kalender'), 'icon_3d' => 'assets/images/icons_3d/kalender.png'],
                            ['heading' => $isLoggedIn ? 'Keluar' : 'Masuk', 'href' => site_url($isLoggedIn ? 'login/logout' : 'login'), 'icon_3d' => 'assets/images/icons_3d/logout.png'],
                        ];
                        break;
                }
                ?>

                <div class="curved-sidebar-header" style="margin-top: 10px; display: flex; align-items: center; justify-content: space-between;">
                    <p style="margin: 0;">Navigation</p>
                    <div class="curved-header-role-badge">
                        <span><?= htmlspecialchars($activeRoleBadge); ?></span>
                    </div>
                </div>
                <nav class="curved-sidebar-nav">
                    <?php 
                    $curr_uri = trim(uri_string(), '/');
                    $navCount = 1;
                    foreach ($defaultNavItems as $idx => $item): 
                        if (isset($item['category'])):
                    ?>
                        <div class="curved-nav-category <?= !empty($item['has_divider']) ? 'has-divider' : '' ?>">
                            <?= htmlspecialchars($item['category']); ?>
                        </div>
                    <?php 
                        continue;
                        endif;

                        $num = isset($item['index']) ? sprintf('%02d', $item['index']) : sprintf('%02d', $navCount++);
                        $icon = isset($item['icon']) ? $item['icon'] : null;
                        $icon3d = isset($item['icon_3d']) ? $item['icon_3d'] : null;

                        $cleanHref = trim(str_replace([site_url(), base_url()], '', $item['href']), '/');
                        $cleanHrefUri = strtok($cleanHref, '#');
                        $isCurrent = (!empty($cleanHrefUri) && ($curr_uri === $cleanHrefUri));
                        if (!$isCurrent && !empty($cleanHrefUri) && !in_array($cleanHrefUri, ['dashboard', 'admin', 'laboran', 'kaur', 'dosen', 'koordinatorta'])) {
                            $isCurrent = (strpos($curr_uri, $cleanHrefUri) === 0);
                        }
                    ?>
                        <a href="<?= htmlspecialchars($item['href']); ?>" class="curved-nav-item <?= $isCurrent ? 'is-current' : '' ?>">
                            <div class="curved-nav-content">
                                <?php if (!empty($icon3d)): ?>
                                    <div class="curved-nav-3d-wrap">
                                        <img src="<?= base_url($icon3d); ?>" alt="" class="curved-nav-3d-img" loading="lazy" />
                                    </div>
                                <?php elseif (!empty($icon)): ?>
                                    <span class="curved-nav-icon"><i class="<?= htmlspecialchars($icon); ?>"></i></span>
                                <?php else: ?>
                                    <span class="curved-nav-index"><?= $num ?>.</span>
                                <?php endif; ?>
                                <div class="curved-nav-text">
                                    <span class="curved-nav-heading"><?= htmlspecialchars($item['heading']); ?></span>
                                    <?php if (!empty($item['subheading'])): ?>
                                        <div class="curved-nav-subheading"><?= htmlspecialchars($item['subheading']); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </div>

            <!-- Footer -->
            <div class="curved-sidebar-footer" style="padding-top: 10px; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
                <div class="curved-sidebar-footer-brand" style="display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-graduation-cap text-orange-500"></i>
                    <span><?= htmlspecialchars($this->session->userdata('name') ?: 'Portal Tugas Akhir • IFIK') ?></span>
                </div>
                <span class="curved-sidebar-footer-version" style="background: rgba(234, 88, 12, 0.1); color: #ea580c; padding: 2px 8px; border-radius: 6px; font-weight: 700; font-size: 0.72rem;"><?= htmlspecialchars($activeRoleBadge) ?></span>
            </div>
        </div>

        <!-- Morphing Bezier Curve SVG -->
        <svg id="curvedSidebarSvg" class="curved-sidebar-svg">
            <path id="curvedSidebarPath" />
        </svg>
    </aside>

    <!-- Header Kalender Full Page (Single Row Height 70px) -->
    <div class="gcal-page-header">
        <div class="gcal-header-left" style="display: flex; align-items: center; gap: 8px; position: relative;">
            <!-- Curved Sidebar Burger Toggle Button -->
            <button type="button" id="curvedSidebarToggle" class="curved-sidebar-toggle-btn is-active" aria-expanded="true" aria-label="Toggle Sidebar Menu" title="Buka Menu Navigasi & Kontrol">
                <div class="curved-sidebar-burger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </button>
            <!-- Pane 1: Calendar Navigation (Dipindahkan ke Curved Sidebar sesuai permintaan pembimbing) -->
            <!--
            <div id="headerLeftCalendarNav" class="header-left-pane">
                <button class="gcal-btn-today" onclick="goToToday()">Today</button>
                <div class="gcal-nav-arrows" style="display: flex; gap: 4px;">
                    <button onclick="prevWeek()"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg></button>
                    <button onclick="nextWeek()"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg></button>
                </div>

                <div class="month-year-picker-wrap" id="monthYearPickerWrap" style="position: relative;">
                    <button type="button" class="month-year-btn" id="monthYearBtn" onclick="toggleMonthYearPicker(event)" title="Klik untuk memilih bulan & tahun" style="padding: 4px 8px;">
                        <span id="gcalMonthTitle" style="font-size: 0.96rem; font-weight: 800; color: #0f172a; white-space: nowrap;">-</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>

                    <div class="month-year-popover" id="monthYearPopover">
                        <div class="my-year-nav">
                            <button type="button" onclick="changePickerYear(-1, event)" title="Tahun Sebelumnya">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
                            </button>
                            <span id="pickerYearDisplay" style="font-size: 1.05rem; font-weight: 800; color: #0f172a;">2026</span>
                            <button type="button" onclick="changePickerYear(1, event)" title="Tahun Selanjutnya">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </button>
                        </div>

                        <div class="my-months-grid" id="pickerMonthsGrid">
                        </div>
                    </div>
                </div>
            </div>
            -->

            <!-- Pane 2: Table Title (Active on Table Mode) -->
            <div id="headerLeftTableTitle" class="header-left-pane" style="display: none;">
                <span style="font-size: 1.05rem; font-weight: 800; color: #0f172a; white-space: nowrap; padding: 4px 6px;">Daftar Peminjaman</span>
            </div>
        </div>

        <!-- UNIFIED SEARCH PILL & SEPARATE STANDALONE + BUTTON IN HEADER (CENTERED) -->
        <div class="gcal-header-center">
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
                    <input type="text" id="mainSearchInput" placeholder="Ketik kata kunci lalu tekan Enter atau klik Cari..." 
                           oninput="handleUnifiedMultiSearch(this)" 
                           onkeydown="if(event.key === 'Enter') { triggerSearchSubmit(); }"
                           onfocus="onMainInputFocused()"
                           autocomplete="off" class="unified-input-key main-val-field">
                    <button id="clearMainSearchBtn" onclick="clearMainSearch()" style="display: none; position: absolute; right: 78px; background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 1.1rem; line-height: 1;" title="Hapus pencarian">&times;</button>
                    <button type="button" class="btn-submit-search-pill" onclick="triggerSearchSubmit()" title="Cari (Enter)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        Cari
                    </button>
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

        </div> <!-- end search-filter-container -->
        </div> <!-- end gcal-header-center -->

        <!-- Right Placeholder to balance center alignment -->
        <div class="gcal-header-right"></div>
    </div>

    <!-- Container Utama Grid Kalender (Full Height) -->
    <div class="gcal-body" id="calendarViewContainer">
        <div class="gcal-days-header-wrapper">
            <div class="gcal-days-header" id="gcalDaysHeader">
                <!-- Digenerate via JS -->
            </div>
        </div>
        <div class="gcal-grid-scroll" id="gcalGridScroll">
            <div class="gcal-grid" id="gcalGrid">
                <!-- Digenerate via JS -->
            </div>
        </div>
    </div>

    <!-- Container Utama Tampilan Tabel (Fullscreen Modern) -->
    <div class="table-view-container" id="tableViewContainer" style="display: none;">
        <div class="table-view-inner">
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
            } else if (s.includes('ditolak') || s.includes('reject')) {
                return { bg: '#ef4444', border: '#dc2626', badgeBg: '#fef2f2', badgeColor: '#991b1b', dot: '#ef4444', label: 'Ditolak' };
            } else if (s.includes('ka. ur') || s.includes('kaur')) {
                return { bg: '#10b981', border: '#059669', badgeBg: '#f0fdf4', badgeColor: '#166534', dot: '#10b981', label: 'Disetujui Ka. Ur' };
            } else if (s.includes('laboran')) {
                return { bg: '#3b82f6', border: '#2563eb', badgeBg: '#eff6ff', badgeColor: '#1d4ed8', dot: '#3b82f6', label: 'Disetujui Laboran' };
            } else if (s.includes('admin')) {
                return { bg: '#8b5cf6', border: '#7c3aed', badgeBg: '#f5f3ff', badgeColor: '#6d28d9', dot: '#8b5cf6', label: 'Disetujui Admin' };
            } else if (s.includes('disetujui')) {
                return { bg: '#10b981', border: '#059669', badgeBg: '#f0fdf4', badgeColor: '#166534', dot: '#10b981', label: 'Disetujui' };
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
                if (val === 'keyword') inputField.placeholder = "Ketik kata kunci lalu tekan Enter atau klik Cari...";
                else if (val === 'kategori') inputField.placeholder = "Ketik nama kategori lalu tekan Enter atau klik Cari...";
                else if (val === 'ruangan') inputField.placeholder = "Ketik kode/nama ruangan lalu tekan Enter atau klik Cari...";
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
                    disableMobile: "true"
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
        }

        // ==========================================
        // MONTH-YEAR PICKER CONTROLLER
        // ==========================================
        // ==========================================
        // MONTH-YEAR PICKER CONTROLLER
        // ==========================================
        let pickerCurrentYear = (new Date()).getFullYear();

        function toggleMonthYearPicker(e, source = 'main') {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }
            const wrapId = source === 'sb' ? 'sbMonthYearPickerWrap' : 'monthYearPickerWrap';
            const wrap = document.getElementById(wrapId);
            if (!wrap) return;
            const wasOpen = wrap.classList.contains('open');
            closeAllCustomMenus();
            if (!wasOpen) {
                pickerCurrentYear = currentWeekStart.getFullYear();
                renderMonthYearPicker(source);
                wrap.classList.add('open');
            }
        }

        function changePickerYear(delta, e, source = 'main') {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }
            pickerCurrentYear += delta;
            renderMonthYearPicker(source);
        }

        function renderMonthYearPicker(source = 'main') {
            const yearDisplayId = source === 'sb' ? 'sbPickerYearDisplay' : 'pickerYearDisplay';
            const gridId = source === 'sb' ? 'sbPickerMonthsGrid' : 'pickerMonthsGrid';

            const yearDisplay = document.getElementById(yearDisplayId);
            if (yearDisplay) yearDisplay.innerText = pickerCurrentYear;

            const grid = document.getElementById(gridId);
            if (!grid) return;

            const monthShorts = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            const activeMonth = currentWeekStart.getMonth();
            const activeYear = currentWeekStart.getFullYear();

            let html = '';
            monthShorts.forEach((mName, idx) => {
                const isActive = (idx === activeMonth && pickerCurrentYear === activeYear);
                html += `
                    <div class="my-month-item ${isActive ? 'active' : ''}" onclick="selectMonthYear(${idx}, ${pickerCurrentYear}, event, '${source}')">
                        ${mName}
                    </div>
                `;
            });
            grid.innerHTML = html;
        }

        function selectMonthYear(monthIndex, year, e, source = 'main') {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }
            // Set currentWeekStart to 1st of selected month
            const d = new Date(year, monthIndex, 1);
            currentWeekStart = new Date(d);
            currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay());

            const wrap = document.getElementById(source === 'sb' ? 'sbMonthYearPickerWrap' : 'monthYearPickerWrap');
            if (wrap) wrap.classList.remove('open');

            renderCalendar();
            applyMultiFilters();
        }

        function closeAllCustomMenus() {
            document.querySelectorAll('.custom-cat-dropdown').forEach(d => d.classList.remove('open'));
            document.querySelectorAll('.custom-status-dropdown').forEach(d => d.classList.remove('open'));
            const myWrap = document.getElementById('monthYearPickerWrap');
            if (myWrap) myWrap.classList.remove('open');
            const sbWrap = document.getElementById('sbMonthYearPickerWrap');
            if (sbWrap) sbWrap.classList.remove('open');
        }

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.custom-cat-dropdown') && 
                !e.target.closest('.custom-status-dropdown') && 
                !e.target.closest('#monthYearPickerWrap') &&
                !e.target.closest('#sbMonthYearPickerWrap')) {
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

            // HANYA FILTER SAAT ENTER / KLIK TOMBOL CARI (isImmediate === true)
            // (Mencegah beban komputasi berat saat ribuan data diketik)
            if (isImmediate) {
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
            hideAutocomplete();
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
            let formattedTitle = '-';
            const endOfWeek = new Date(currentWeekStart);
            endOfWeek.setDate(endOfWeek.getDate() + 6);
            if (currentWeekStart.getMonth() === endOfWeek.getMonth()) {
                formattedTitle = `${INDO_MONTHS[currentWeekStart.getMonth()]} ${currentWeekStart.getFullYear()}`;
            } else {
                formattedTitle = `${INDO_MONTHS_SHORT[currentWeekStart.getMonth()]} - ${INDO_MONTHS_SHORT[endOfWeek.getMonth()]} ${endOfWeek.getFullYear()}`;
            }

            const monthTitle = document.getElementById('gcalMonthTitle');
            if (monthTitle) monthTitle.innerText = formattedTitle;

            const sbMonthTitle = document.getElementById('sbMonthTitle');
            if (sbMonthTitle) sbMonthTitle.innerText = formattedTitle;
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

            const sbCal = document.getElementById('sbViewCalBtn');
            const sbTbl = document.getElementById('sbViewTblBtn');
            const sbCalNav = document.getElementById('sbCalendarNavSection');
            const sbTblControls = document.getElementById('sbTableControlsSection');

            if (mode === 'table') {
                if (btnCal) btnCal.classList.remove('active');
                if (btnTbl) btnTbl.classList.add('active');

                if (sbCal) { sbCal.style.background = 'transparent'; sbCal.style.color = '#64748b'; }
                if (sbTbl) { sbTbl.style.background = '#0f172a'; sbTbl.style.color = '#ffffff'; }
                if (sbCalNav) sbCalNav.style.display = 'none';
                if (sbTblControls) sbTblControls.style.display = 'flex';

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

                if (sbCal) { sbCal.style.background = '#0f172a'; sbCal.style.color = '#ffffff'; }
                if (sbTbl) { sbTbl.style.background = 'transparent'; sbTbl.style.color = '#64748b'; }
                if (sbCalNav) sbCalNav.style.display = 'block';
                if (sbTblControls) sbTblControls.style.display = 'none';

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

        window.activeStatPillFilter = 'all'; // 'all', 'pending', 'all_approved', 'laboran', 'kaur', 'admin', 'rejected'

        function filterByStatPill(type) {
            closeApprovedSubMenu();
            if (type === 'all') {
                window.activeStatPillFilter = 'all';
            } else if (type === 'pending') {
                if (window.activeStatPillFilter === 'pending') {
                    window.activeStatPillFilter = 'all';
                } else {
                    window.activeStatPillFilter = 'pending';
                }
            } else if (type === 'rejected') {
                if (window.activeStatPillFilter === 'rejected') {
                    window.activeStatPillFilter = 'all';
                } else {
                    window.activeStatPillFilter = 'rejected';
                }
            }
            currentTablePage = 1;
            renderTableView();
        }

        function toggleApprovedSubMenu(e) {
            if (e) e.stopPropagation();
            const menu = document.getElementById('approvedSubMenu');
            const chev = document.getElementById('approvedStatChevron');
            if (menu) {
                const isShowing = menu.classList.contains('show');
                if (isShowing) {
                    closeApprovedSubMenu();
                } else {
                    menu.classList.add('show');
                    if (chev) chev.style.transform = 'rotate(180deg)';
                }
            }
        }

        function closeApprovedSubMenu() {
            const menu = document.getElementById('approvedSubMenu');
            const chev = document.getElementById('approvedStatChevron');
            if (menu) menu.classList.remove('show');
            if (chev) chev.style.transform = 'rotate(0deg)';
        }

        function selectApprovedSub(subType, e) {
            if (e) e.stopPropagation();
            closeApprovedSubMenu();
            window.activeStatPillFilter = subType;
            currentTablePage = 1;
            renderTableView();
        }

        document.addEventListener('click', function(e) {
            const wrap = document.querySelector('.stat-pill-approved-wrap');
            if (wrap && !wrap.contains(e.target)) {
                closeApprovedSubMenu();
            }
        });

        function renderTableView(customData) {
            const rawBase = (typeof customData !== 'undefined') ? customData : (lastFilteredData || window.bookingData || []);

            // 1. Calculate overall stats from rawBase
            const totalCount = rawBase.length;
            const pendingCount = rawBase.filter(b => (b.status || '').toLowerCase().includes('pending') || (b.status || '').toLowerCase().includes('menunggu')).length;
            const allApprovedCount = rawBase.filter(b => (b.status || '').toLowerCase().includes('setuju')).length;
            const rejectedCount = rawBase.filter(b => (b.status || '').toLowerCase().includes('ditolak') || (b.status || '').toLowerCase().includes('reject')).length;
            const laboranCount = rawBase.filter(b => (b.status || '').toLowerCase().includes('laboran')).length;
            const kaurCount = rawBase.filter(b => (b.status || '').toLowerCase().includes('ka. ur') || (b.status || '').toLowerCase().includes('kaur')).length;
            const adminCount = rawBase.filter(b => (b.status || '').toLowerCase().includes('admin')).length;

            // Update DOM counters
            const statTotal = document.getElementById('tableStatTotal');
            const statPending = document.getElementById('tableStatPending');
            const statApproved = document.getElementById('tableStatApproved');
            const statRejected = document.getElementById('tableStatRejected');
            if (statTotal) statTotal.innerText = totalCount;
            if (statPending) statPending.innerText = pendingCount;
            if (statApproved) statApproved.innerText = allApprovedCount;
            if (statRejected) statRejected.innerText = rejectedCount;

            const scAll = document.getElementById('subCountAllApproved');
            const scLab = document.getElementById('subCountLaboran');
            const scKaur = document.getElementById('subCountKaur');
            const scAdm = document.getElementById('subCountAdmin');
            if (scAll) scAll.innerText = allApprovedCount;
            if (scLab) scLab.innerText = laboranCount;
            if (scKaur) scKaur.innerText = kaurCount;
            if (scAdm) scAdm.innerText = adminCount;

            // Update Active UI States
            const pillTot = document.getElementById('statPillTotal');
            const pillPen = document.getElementById('statPillPending');
            const pillApp = document.getElementById('statPillApproved');
            const pillRej = document.getElementById('statPillRejected');
            const labelApp = document.getElementById('approvedStatLabel');

            if (pillTot) pillTot.classList.remove('active');
            if (pillPen) pillPen.classList.remove('active');
            if (pillApp) pillApp.classList.remove('active');
            if (pillRej) pillRej.classList.remove('active');

            ['subOptAllApproved', 'subOptLaboran', 'subOptKaur', 'subOptAdmin'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.remove('active');
            });

            // 2. Filter data by activeStatPillFilter
            let data = [...rawBase];
            const activeFilter = window.activeStatPillFilter || 'all';

            if (activeFilter === 'pending') {
                data = data.filter(b => (b.status || '').toLowerCase().includes('pending') || (b.status || '').toLowerCase().includes('menunggu'));
                if (pillPen) pillPen.classList.add('active');
                if (labelApp) labelApp.innerText = 'Disetujui:';
            } else if (activeFilter === 'rejected') {
                data = data.filter(b => (b.status || '').toLowerCase().includes('ditolak') || (b.status || '').toLowerCase().includes('reject'));
                if (pillRej) pillRej.classList.add('active');
                if (labelApp) labelApp.innerText = 'Disetujui:';
            } else if (activeFilter === 'all_approved') {
                data = data.filter(b => (b.status || '').toLowerCase().includes('setuju'));
                if (pillApp) pillApp.classList.add('active');
                if (labelApp) labelApp.innerText = 'Disetujui:';
                const el = document.getElementById('subOptAllApproved');
                if (el) el.classList.add('active');
            } else if (activeFilter === 'laboran') {
                data = data.filter(b => (b.status || '').toLowerCase().includes('laboran'));
                if (pillApp) pillApp.classList.add('active');
                if (labelApp) labelApp.innerText = 'Laboran:';
                const el = document.getElementById('subOptLaboran');
                if (el) el.classList.add('active');
            } else if (activeFilter === 'kaur') {
                data = data.filter(b => (b.status || '').toLowerCase().includes('ka. ur') || (b.status || '').toLowerCase().includes('kaur'));
                if (pillApp) pillApp.classList.add('active');
                if (labelApp) labelApp.innerText = 'Ka. Ur:';
                const el = document.getElementById('subOptKaur');
                if (el) el.classList.add('active');
            } else if (activeFilter === 'admin') {
                data = data.filter(b => (b.status || '').toLowerCase().includes('admin'));
                if (pillApp) pillApp.classList.add('active');
                if (labelApp) labelApp.innerText = 'Admin:';
                const el = document.getElementById('subOptAdmin');
                if (el) el.classList.add('active');
            } else {
                if (pillTot) pillTot.classList.add('active');
                if (labelApp) labelApp.innerText = 'Disetujui:';
                const el = document.getElementById('subOptAllApproved');
                if (el) el.classList.add('active');
            }

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

            const cardsList = document.getElementById('tableCardsList');
            if (!cardsList) return;

            const filteredCount = data.length;
            if (filteredCount === 0) {
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
            const totalPages = Math.ceil(filteredCount / tablePageSize);
            if (currentTablePage > totalPages) currentTablePage = totalPages;
            if (currentTablePage < 1) currentTablePage = 1;

            const startIndex = (currentTablePage - 1) * tablePageSize;
            const endIndex = Math.min(startIndex + tablePageSize, filteredCount);
            const pageData = data.slice(startIndex, endIndex);

            // Render table rows
            let html = '';
            pageData.forEach(b => {
                const st = getStatusStyle(b.status);
                const jMulai = b.jam_mulai ? b.jam_mulai.substring(0, 5) : '00:00';
                const jSelesai = b.jam_selesai ? b.jam_selesai.substring(0, 5) : '00:00';
                const lokasi = (b.lokasi || '').replace(/"/g, '&quot;');
                const kapasitas = b.kapasitas || '';
                const namaKategori = (b.nama_kategori || 'Ruangan').replace(/"/g, '&quot;');

                let metaHtml = '';
                if (lokasi || kapasitas) {
                    metaHtml = `
                        <div class="rht-meta">
                            ${lokasi ? `<span>📍 ${lokasi}</span>` : ''}
                            ${kapasitas ? `<span>👥 ${kapasitas} Orang</span>` : ''}
                        </div>
                    `;
                }

                const roomCodesBadge = (b.kode_ruangan || '').split(',').map(c => c.trim()).filter(Boolean).map(c => `<span style="display:inline-block; background:#f1f5f9; border:1px solid #cbd5e1; border-radius:6px; padding:1px 6px; font-size:0.68rem; font-weight:700; color:#334155; margin-right:3px;">${c}</span>`).join('');

                html += `
                    <div class="table-row-card" onclick="openDetailBookingModal(${b.id})" title="Klik untuk melihat detail peminjaman">
                        <div class="tr-room-col">
                            <div class="tr-room-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1e293b" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            </div>
                            <div class="tr-room-info" title="${(b.nama_ruangan || '') + (b.kode_ruangan ? ' (' + b.kode_ruangan + ')' : '')}">
                                <div class="tr-room-name" style="font-weight:700; color:#0f172a;">${b.nama_ruangan || '-'}</div>
                                <div class="tr-room-code" style="margin-top:2px;">${roomCodesBadge || '<span style="color:#94a3b8; font-size:0.75rem;">-</span>'}</div>
                            </div>

                            <!-- Floating Room Detail Tooltip on Hover (Direct child of tr-room-col) -->
                            <div class="room-hover-tooltip">
                                <div class="rht-header">
                                    <span class="rht-code">${b.kode_ruangan || '-'}</span>
                                    <span class="rht-cat">${namaKategori}</span>
                                </div>
                                <div class="rht-title">${b.nama_ruangan || '-'}</div>
                                ${metaHtml}
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

                            <!-- Floating Keterangan Detail Tooltip on Hover -->
                            <div class="desc-hover-tooltip">
                                <span class="dht-badge">📝 Keterangan / Keperluan</span>
                                <div class="dht-content">${b.keterangan || '-'}</div>
                            </div>
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
                infoEl.innerText = `Menampilkan ${startIndex + 1} - ${endIndex} dari ${filteredCount} data`;
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

            // Jump calendar week if a date rule is selected
            const dateRule = rules.find(r => r.category === 'tanggal' && r.value);
            if (dateRule) {
                const datePart = dateRule.value.split(' to ')[0].trim();
                const d = new Date(datePart);
                if (!isNaN(d.getTime())) {
                    currentWeekStart = new Date(d);
                    currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay());
                }
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

            // Mobile Days Header Sync & Auto-scroll to today
            const gridScroll = document.getElementById('gcalGridScroll');
            if (gridScroll && daysHeader) {
                gridScroll.onscroll = function() {
                    daysHeader.style.transform = `translateX(-${gridScroll.scrollLeft}px)`;
                };

                if (window.innerWidth <= 768) {
                    const today = new Date();
                    const diffDays = Math.round((new Date(today.getFullYear(), today.getMonth(), today.getDate()) - new Date(currentWeekStart.getFullYear(), currentWeekStart.getMonth(), currentWeekStart.getDate())) / (1000 * 60 * 60 * 24));
                    if (diffDays >= 0 && diffDays < 7) {
                        setTimeout(() => {
                            const targetX = Math.max(0, (diffDays * 110) - ((window.innerWidth - 48) / 2) + 55);
                            gridScroll.scrollTo({ left: targetX, behavior: 'smooth' });
                        }, 60);
                    }
                }
            }
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
            document.getElementById('detailKodeRuangan').innerText = booking.kode_ruangan ? 'Ruang: ' + booking.kode_ruangan : '';
            document.getElementById('detailKodeRuangan').style.display = booking.kode_ruangan ? 'inline-block' : 'none';
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
    <!-- Curved Sidebar Script -->
    <script src="<?= base_url('assets/js/curved_sidebar.js?v=' . time()); ?>"></script>

    <script>
        // Initialize Flatpickr Jump Date in Sidebar
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('sbJumpDatePicker')) {
                flatpickr("#sbJumpDatePicker", {
                    dateFormat: "Y-m-d",
                    onChange: function(selectedDates, dateStr) {
                        if (dateStr) {
                            const d = new Date(dateStr);
                            currentWeekStart = new Date(d);
                            currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay());
                            renderCalendar();
                            applyMultiFilters();
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>