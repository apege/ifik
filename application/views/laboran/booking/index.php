<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Approval & Kelola Peminjaman - Panel Laboran') ?></title>
    
    <!-- Google Fonts & FontAwesome & SweetAlert2 -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="<?= base_url('assets/css/timepicker.css') ?>">


    <style>
        :root {
            --bg-color: #f8fafc;
            --surface-color: #ffffff;
            --text-color: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --primary: #ea580c;
            --primary-hover: #c2410c;
            --accent-blue: #2563eb;
            --accent-green: #16a34a;
            --accent-red: #dc2626;
            --radius-md: 12px;
            --radius-lg: 16px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            min-height: 100vh;
            padding: 24px 32px 100px 76px; /* space for fixed left sidebar burger */
        }

        .main-container {
            max-width: 1440px;
            margin: 0 auto;
        }

        /* Top Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
        }

        .header-title-wrap h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-title-wrap h1 .role-badge {
            font-size: 0.72rem;
            font-weight: 700;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            padding: 3px 10px;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .header-title-wrap p {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .header-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: var(--radius-md);
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-primary {
            background-color: var(--primary);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.2);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: #ffffff;
            color: #334155;
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background-color: #f1f5f9;
        }

        /* Stat Cards Bar */
        /* =========================================================
           3D CLAYMORPHIC / GLASS STAT CARDS (IMPORT-EMAIL STYLE)
           ========================================================= */
        .stat-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 18px;
            margin-bottom: 28px;
        }

        .stat-card-highlight {
            position: relative;
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 20px;
            padding: 20px 22px 16px 22px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
            backdrop-filter: blur(12px);
            overflow: hidden;
            cursor: default;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .stat-card-highlight:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 30px -8px rgba(15, 23, 42, 0.06), 0 1px 3px rgba(0,0,0,0.02);
        }

        /* Ambient Glow Backdrop */
        .stat-card-glow {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
            z-index: 0;
        }
        .stat-card-glow .glow-bubble {
            position: absolute;
            bottom: -40px;
            right: -40px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: var(--card-accent-soft, rgba(234, 88, 12, 0.12));
            filter: blur(28px);
            transition: transform 0.5s ease;
        }
        .stat-card-highlight:hover .glow-bubble {
            transform: scale(1.4);
        }

        /* Top Row Content */
        .stat-card-top {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .stat-card-meta {
            flex: 1;
            min-width: 0;
        }

        .stat-card-label {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #94a3b8;
            margin-bottom: 4px;
            transition: color 0.2s ease;
        }
        .stat-card-highlight:hover .stat-card-label {
            color: var(--card-accent, #ea580c);
        }

        .stat-card-val {
            font-size: 1.85rem;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.1;
            letter-spacing: -0.02em;
            display: flex;
            align-items: baseline;
            gap: 6px;
        }

        .stat-card-percent {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--card-accent, #ea580c);
        }

        .stat-card-desc {
            font-size: 0.76rem;
            font-weight: 500;
            color: #64748b;
            margin-top: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* 3D Claymorphic Icon Box */
        .stat-card-3d-icon {
            position: relative;
            flex-shrink: 0;
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            background: var(--card-icon-bg, linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%));
            border: 1px solid var(--card-icon-border, #fed7aa);
            color: var(--card-accent, #ea580c);
            box-shadow: inset 0 2px 4px rgba(255, 255, 255, 0.9), 0 6px 16px var(--card-icon-shadow, rgba(234, 88, 12, 0.16));
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.4s ease;
        }
        .stat-card-highlight:hover .stat-card-3d-icon {
            transform: rotate(6deg) scale(1.1);
        }

        /* Bottom Row Divider & Pulse Dots */
        .stat-card-bottom {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 14px;
            padding-top: 8px;
            border-top: 1px solid rgba(241, 245, 249, 0.9);
        }

        .stat-card-bar {
            width: 38%;
            height: 2.5px;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--card-accent, #ea580c), transparent);
            transition: width 0.4s ease;
        }
        .stat-card-highlight:hover .stat-card-bar {
            width: 65%;
        }

        .stat-card-dots {
            display: flex;
            align-items: center;
            gap: 4px;
            opacity: 0.55;
            transition: opacity 0.3s ease;
        }
        .stat-card-highlight:hover .stat-card-dots {
            opacity: 1;
        }

        .stat-card-dots span {
            width: 4.5px;
            height: 4.5px;
            border-radius: 50%;
            background: var(--card-accent, #ea580c);
            display: inline-block;
        }

        /* Theme Variants */
        .theme-orange {
            --card-accent: #ea580c;
            --card-accent-soft: rgba(234, 88, 12, 0.14);
            --card-icon-bg: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            --card-icon-border: #fed7aa;
            --card-icon-shadow: rgba(234, 88, 12, 0.18);
        }
        .theme-amber {
            --card-accent: #d97706;
            --card-accent-soft: rgba(245, 158, 11, 0.14);
            --card-icon-bg: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            --card-icon-border: #fde68a;
            --card-icon-shadow: rgba(245, 158, 11, 0.18);
        }
        .theme-sky {
            --card-accent: #0284c7;
            --card-accent-soft: rgba(2, 132, 199, 0.14);
            --card-icon-bg: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            --card-icon-border: #bae6fd;
            --card-icon-shadow: rgba(2, 132, 199, 0.18);
        }
        .theme-emerald {
            --card-accent: #059669;
            --card-accent-soft: rgba(16, 185, 129, 0.14);
            --card-icon-bg: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            --card-icon-border: #a7f3d0;
            --card-icon-shadow: rgba(16, 185, 129, 0.18);
        }
        .theme-rose {
            --card-accent: #e11d48;
            --card-accent-soft: rgba(225, 29, 72, 0.14);
            --card-icon-bg: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%);
            --card-icon-border: #fecdd3;
            --card-icon-shadow: rgba(225, 29, 72, 0.18);
        }

        /* Filter & Search Bar */
        .toolbar-card {
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 16px 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 14px;
        }

        .filter-pills-wrap {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .filter-pill {
            padding: 7px 14px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            border: 1.5px solid var(--border-color);
            background: #ffffff;
            color: #475569;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .filter-pill:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
        }

        .filter-pill.active {
            background: #0f172a;
            border-color: #0f172a;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.15);
        }

        .filter-pill.active .pill-count {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }

        .pill-count {
            background: #f1f5f9;
            color: #475569;
            border-radius: 999px;
            padding: 1px 7px;
            font-size: 0.72rem;
            font-weight: 800;
        }

        /* Unified Multi-Search Pill Component (Koordinator TA Style) */
        .search-pill-container {
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
        }

        .unified-search-pill {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            padding: 2px 12px;
            flex: 1;
            height: 44px;
            transition: all 0.2s ease;
            position: relative;
        }
        .unified-search-pill:focus-within, .unified-search-pill.active {
            border-color: #ea580c !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.12) !important;
        }

        .unified-divider {
            width: 1.5px;
            height: 20px;
            background-color: #cbd5e1;
            margin: 0 10px;
            flex-shrink: 0;
        }

        .btn-standalone-add {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff7ed;
            border: 1.5px solid #ffedd5;
            border-radius: 14px;
            padding: 6px 14px;
            height: 44px;
            font-size: 0.8rem;
            font-weight: 700;
            color: #ea580c;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(234, 88, 12, 0.06);
        }
        .btn-standalone-add:hover, .btn-standalone-add.active {
            background: #ffedd5;
            border-color: #fdba74;
            transform: scale(1.02);
        }

        .badge-standalone-count {
            background: #ea580c;
            color: #ffffff;
            font-size: 0.72rem;
            font-weight: 800;
            padding: 1.5px 8px;
            border-radius: 99px;
        }

        .extra-rows-card {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 100%;
            max-width: 660px;
            background: #ffffff;
            border: 1.5px solid #fed7aa;
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 16px 36px -6px rgba(15, 23, 42, 0.16);
            z-index: 1000;
        }

        .extra-filter-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }

        .btn-remove-row {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: #fff1f2;
            border: 1.5px solid #fecdd3;
            border-radius: 12px;
            color: #e11d48;
            cursor: pointer;
            transition: all 0.15s ease;
            flex-shrink: 0;
        }
        .btn-remove-row:hover {
            background: #ffe4e6;
            transform: scale(1.05);
        }

        .custom-dropdown-container {
            position: relative;
        }

        .custom-dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            margin-top: 6px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 12px 30px -5px rgba(15, 23, 42, 0.16);
            z-index: 2000;
            padding: 6px;
            min-width: 200px;
            display: none;
        }
        .custom-dropdown-menu.show {
            display: block;
        }

        .dropdown-item {
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.75rem;
            font-weight: 600;
            color: #475569;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.15s ease;
        }
        .dropdown-item:hover, .dropdown-item.active {
            background: #fff7ed;
            color: #ea580c;
            font-weight: 700;
        }

        /* Pagination Bar */
        .pagination-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 20px;
            background: #f8fafc;
            border-top: 1px solid var(--border-color);
            font-size: 0.78rem;
            color: #64748b;
        }

        .pagination-controls {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .page-btn {
            height: 32px;
            min-width: 32px;
            padding: 0 8px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #334155;
            font-size: 0.75rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
        }
        .page-btn:hover:not(:disabled) {
            border-color: #ea580c;
            color: #ea580c;
            background: #fff7ed;
        }
        .page-btn.active {
            background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
            border-color: #ea580c;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(234, 88, 12, 0.25);
        }
        .page-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        /* Table Card */
        .table-card {
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
            width: 100%;
            overflow: visible;
        }

        .table-responsive {
            width: 100%;
            overflow: visible;
        }

        table.laboran-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            table-layout: fixed;
        }

        table.laboran-table th {
            background: #f8fafc;
            padding: 12px 10px;
            font-size: 0.74rem;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-bottom: 1.5px solid var(--border-color);
            white-space: nowrap;
        }

        table.laboran-table td {
            padding: 11px 10px;
            font-size: 0.82rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            overflow: visible;
        }

        table.laboran-table tr:last-child td {
            border-bottom: none;
        }

        table.laboran-table tr:hover td {
            background: #f8fafc;
        }
        table.laboran-table tr:hover {
            position: relative;
            z-index: 1000;
        }

        /* ROOM COLUMN STYLES MATCHING INFORMASI RUANGAN */
        .tr-room-col {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            position: relative;
        }
        .tr-room-icon {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            background: transparent;
            border: 1.6px solid #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1e293b;
            flex-shrink: 0;
        }
        .tr-room-icon svg {
            width: 17px;
            height: 17px;
        }
        .tr-room-info {
            min-width: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 1px;
            position: relative;
        }
        .tr-room-code {
            font-size: 0.88rem;
            font-weight: 800;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
        }
        .tr-room-name {
            font-size: 0.74rem;
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
            left: 46px;
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
            z-index: 9999;
            min-width: 220px;
            max-width: 300px;
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

        /* VERTICALLY STACKED PILLS: USER (TOP) + TIME (BOTTOM) */
        .tr-user-time-col {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
            width: 145px;
            max-width: 100%;
        }
        .tr-pill-user {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: #ffffff;
            border: 1.5px solid #334155;
            border-radius: 999px;
            padding: 3px 8px;
            font-size: 0.73rem;
            font-weight: 700;
            color: #1e293b;
            white-space: nowrap;
            width: 100%;
            height: 24px;
            box-sizing: border-box;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
            overflow: hidden;
        }
        .tr-pill-user svg {
            flex-shrink: 0;
        }
        .tr-pill-user span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .tr-pill-time {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: #ffffff;
            border: 1.5px solid #fb923c;
            border-radius: 999px;
            padding: 3px 8px;
            font-size: 0.73rem;
            font-weight: 700;
            color: #ea580c;
            white-space: nowrap;
            width: 100%;
            height: 24px;
            box-sizing: border-box;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
            overflow: hidden;
        }
        .tr-pill-time svg {
            flex-shrink: 0;
        }
        .tr-pill-time span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .tr-date-col {
            font-size: 0.82rem;
            font-weight: 800;
            color: #1e293b;
            text-align: left;
            letter-spacing: -0.2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* KETERANGAN COLUMN */
        .tr-desc-col {
            min-width: 0;
            position: relative;
            width: 100%;
        }
        .tr-desc-text {
            font-size: 0.78rem;
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
            left: 0;
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
            z-index: 9999;
            min-width: 200px;
            max-width: 340px;
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
            left: 20px;
            border-width: 6px;
            border-style: solid;
            border-color: transparent transparent #0f172a transparent;
        }
        .tr-desc-col:hover .desc-hover-tooltip {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        /* Custom Checkbox */
        .custom-checkbox {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            border: 1.5px solid #cbd5e1;
            cursor: pointer;
            accent-color: var(--primary);
            display: block;
            margin: 0 auto;
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 145px;
            max-width: 100%;
            height: 26px;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            white-space: nowrap;
            box-sizing: border-box;
            text-align: center;
        }

        .status-badge span.status-text {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        /* 3-DOTS ACTION BUTTON & DROPDOWN */
        .action-dropdown-wrap {
            position: relative;
            display: inline-block;
        }

        .btn-action-dots {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #475569;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.18s ease;
            font-size: 0.95rem;
        }

        .btn-action-dots:hover, .btn-action-dots.active {
            background: #f8fafc;
            color: #0f172a;
            border-color: #cbd5e1;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
        }

        .action-dropdown-menu {
            position: absolute;
            top: calc(100% + 4px);
            right: 0;
            min-width: 190px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.18), 0 4px 8px -2px rgba(15, 23, 42, 0.06);
            padding: 6px;
            z-index: 1000;
            display: none;
            text-align: left;
        }

        .action-dropdown-menu.show {
            display: block;
            animation: fadeInDown 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .action-dropdown-item {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            padding: 8px 12px;
            font-size: 0.76rem;
            font-weight: 600;
            color: #334155;
            background: transparent;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            box-sizing: border-box;
            transition: all 0.15s ease;
            white-space: nowrap;
        }

        .action-dropdown-item:hover {
            background: #f8fafc;
            color: #0f172a;
        }

        .action-dropdown-item.item-acc {
            color: #16a34a;
        }
        .action-dropdown-item.item-acc:hover {
            background: #f0fdf4;
            color: #15803d;
        }

        .action-dropdown-item.item-rej {
            color: #dc2626;
        }
        .action-dropdown-item.item-rej:hover {
            background: #fef2f2;
            color: #b91c1c;
        }

        .action-dropdown-item.item-del {
            color: #ef4444;
        }
        .action-dropdown-item.item-del:hover {
            background: #fef2f2;
            color: #dc2626;
        }

        .action-dropdown-item.item-qr {
            color: #0284c7;
        }
        .action-dropdown-item.item-qr:hover {
            background: #f0f9ff;
            color: #0369a1;
        }

        .action-dropdown-divider {
            height: 1px;
            background: #f1f5f9;
            margin: 4px 0;
        }

        /* Floating Multiple Select Action Bar */
        .floating-batch-bar {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%) translateY(120px);
            background: #0f172a;
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.35);
            z-index: 9999;
            transition: transform 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
        }

        .floating-batch-bar.show {
            transform: translateX(-50%) translateY(0);
        }

        .batch-count-badge {
            font-size: 0.85rem;
            font-weight: 700;
            color: #f8fafc;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .batch-count-badge span {
            background: var(--primary);
            color: #ffffff;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 0.76rem;
            font-weight: 800;
        }

        .btn-batch-acc {
            background: #16a34a;
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.82rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
        }

        .btn-batch-acc:hover {
            background: #15803d;
        }

        .btn-batch-rej {
            background: #dc2626;
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.82rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
        }

        .btn-batch-rej:hover {
            background: #b91c1c;
        }

        .btn-batch-cancel {
            background: transparent;
            color: #94a3b8;
            border: none;
            padding: 6px 10px;
            font-size: 0.8rem;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-batch-cancel:hover {
            color: #ffffff;
        }

        /* Modals */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 100000;
            backdrop-filter: blur(4px);
            padding: 16px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-card {
            background: #ffffff;
            width: 100%;
            max-width: 540px;
            border-radius: var(--radius-lg);
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.2);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            animation: modalFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .modal-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            font-size: 1.1rem;
            font-weight: 800;
            color: #0f172a;
        }

        .modal-close-btn {
            background: none;
            border: none;
            font-size: 1.3rem;
            cursor: pointer;
            color: #94a3b8;
            transition: color 0.2s;
        }

        .modal-close-btn:hover {
            color: #0f172a;
        }

        .modal-body {
            padding: 24px;
            max-height: 75vh;
            overflow-y: auto;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            background: #f8fafc;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 0.82rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border-radius: var(--radius-md);
            border: 1.5px solid var(--border-color);
            font-size: 0.85rem;
            font-family: inherit;
            color: var(--text-color);
            outline: none;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: var(--primary);
        }

        /* Detail Modal Rows */
        .detail-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.85rem;
        }

        .detail-label {
            width: 140px;
            font-weight: 700;
            color: #64748b;
            flex-shrink: 0;
        }

        .detail-val {
            color: #0f172a;
            font-weight: 500;
            flex: 1;
        }
    </style>
</head>
<body>

    <!-- =========================================================
         CURVED SIDEBAR INTEGRATION (ROLE LABORAN)
         ========================================================= -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <div class="main-container">
        
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-title-wrap">
                <h1>
                    <span>Persetujuan & Kelola Peminjaman</span>
                    <span class="role-badge">Panel Laboran</span>
                </h1>
                <p>Kelola verifikasi permohonan ruangan, persetujuan operasional (ACC), dan penolakan massal.</p>
            </div>
        </div>

        <?php
            // Calculate summary statistics
            $totalCount = count($peminjaman);
            $pendingCount = 0;
            $approvedLaboranCount = 0;
            $approvedKaurCount = 0;
            $approvedAdminCount = 0;
            $rejectedCount = 0;

            foreach ($peminjaman as $p) {
                $st = $p->status;
                if ($st === 'Pending') $pendingCount++;
                elseif (stripos($st, 'Laboran') !== false) $approvedLaboranCount++;
                elseif (stripos($st, 'Ka. Ur') !== false || stripos($st, 'Kaur') !== false) $approvedKaurCount++;
                elseif (stripos($st, 'Admin') !== false) $approvedAdminCount++;
                elseif ($st === 'Ditolak') $rejectedCount++;
            }
        ?>

        <!-- Stat Cards Grid (3D Claymorphic Highlight Style) -->
        <div class="stat-cards-grid">
            <!-- 1. Total Pengajuan -->
            <div class="stat-card-highlight theme-orange">
                <div class="stat-card-glow"><div class="glow-bubble"></div></div>
                <div class="stat-card-top">
                    <div class="stat-card-meta">
                        <div class="stat-card-label">Total Pengajuan</div>
                        <div class="stat-card-val" id="statTotalCount"><?= $totalCount ?></div>
                        <div class="stat-card-desc">Semua permohonan masuk</div>
                    </div>
                    <div class="stat-card-3d-icon">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                </div>
                <div class="stat-card-bottom">
                    <div class="stat-card-bar"></div>
                    <div class="stat-card-dots"><span></span><span></span><span></span></div>
                </div>
            </div>

            <!-- 2. Menunggu Pending -->
            <div class="stat-card-highlight theme-amber">
                <div class="stat-card-glow"><div class="glow-bubble"></div></div>
                <div class="stat-card-top">
                    <div class="stat-card-meta">
                        <div class="stat-card-label">Menunggu (Pending)</div>
                        <div class="stat-card-val">
                            <span id="statPendingCount"><?= $pendingCount ?></span>
                            <?php if ($totalCount > 0): ?>
                                <span class="stat-card-percent">(<?= round(($pendingCount / $totalCount) * 100) ?>%)</span>
                            <?php endif; ?>
                        </div>
                        <div class="stat-card-desc">Perlu review Laboran</div>
                    </div>
                    <div class="stat-card-3d-icon">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                </div>
                <div class="stat-card-bottom">
                    <div class="stat-card-bar"></div>
                    <div class="stat-card-dots"><span></span><span></span><span></span></div>
                </div>
            </div>

            <!-- 3. Disetujui Laboran -->
            <div class="stat-card-highlight theme-sky">
                <div class="stat-card-glow"><div class="glow-bubble"></div></div>
                <div class="stat-card-top">
                    <div class="stat-card-meta">
                        <div class="stat-card-label">Disetujui Laboran</div>
                        <div class="stat-card-val">
                            <?= $approvedLaboranCount ?>
                            <?php if ($totalCount > 0): ?>
                                <span class="stat-card-percent">(<?= round(($approvedLaboranCount / $totalCount) * 100) ?>%)</span>
                            <?php endif; ?>
                        </div>
                        <div class="stat-card-desc">Menunggu validasi Ka. Ur</div>
                    </div>
                    <div class="stat-card-3d-icon">
                        <i class="fa-solid fa-flask"></i>
                    </div>
                </div>
                <div class="stat-card-bottom">
                    <div class="stat-card-bar"></div>
                    <div class="stat-card-dots"><span></span><span></span><span></span></div>
                </div>
            </div>

            <!-- 4. Disetujui Ka. Ur -->
            <div class="stat-card-highlight theme-emerald">
                <div class="stat-card-glow"><div class="glow-bubble"></div></div>
                <div class="stat-card-top">
                    <div class="stat-card-meta">
                        <div class="stat-card-label">Disetujui Ka. Ur</div>
                        <div class="stat-card-val">
                            <?= $approvedKaurCount ?>
                            <?php if ($totalCount > 0): ?>
                                <span class="stat-card-percent">(<?= round(($approvedKaurCount / $totalCount) * 100) ?>%)</span>
                            <?php endif; ?>
                        </div>
                        <div class="stat-card-desc">Surat QR resmi terbit</div>
                    </div>
                    <div class="stat-card-3d-icon">
                        <i class="fa-solid fa-certificate"></i>
                    </div>
                </div>
                <div class="stat-card-bottom">
                    <div class="stat-card-bar"></div>
                    <div class="stat-card-dots"><span></span><span></span><span></span></div>
                </div>
            </div>

            <!-- 5. Ditolak -->
            <div class="stat-card-highlight theme-rose">
                <div class="stat-card-glow"><div class="glow-bubble"></div></div>
                <div class="stat-card-top">
                    <div class="stat-card-meta">
                        <div class="stat-card-label">Ditolak</div>
                        <div class="stat-card-val">
                            <?= $rejectedCount ?>
                            <?php if ($totalCount > 0): ?>
                                <span class="stat-card-percent">(<?= round(($rejectedCount / $totalCount) * 100) ?>%)</span>
                            <?php endif; ?>
                        </div>
                        <div class="stat-card-desc">Permohonan tidak disetujui</div>
                    </div>
                    <div class="stat-card-3d-icon">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                </div>
                <div class="stat-card-bottom">
                    <div class="stat-card-bar"></div>
                    <div class="stat-card-dots"><span></span><span></span><span></span></div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar (Koordinator TA Style) -->
        <div class="toolbar-card" style="display: flex; flex-direction: column; gap: 14px; background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 18px; padding: 18px 20px; box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);">
            <div class="filter-pills-wrap">
                <button type="button" class="filter-pill active" data-status="all" onclick="setFilterStatus('all')">
                    <span>Semua</span>
                    <span class="pill-count"><?= $totalCount ?></span>
                </button>
                <button type="button" class="filter-pill" data-status="pending" onclick="setFilterStatus('pending')">
                    <span>⏳ Menunggu ACC</span>
                    <span class="pill-count"><?= $pendingCount ?></span>
                </button>
                <button type="button" class="filter-pill" data-status="laboran" onclick="setFilterStatus('laboran')">
                    <span>🔵 Disetujui Laboran</span>
                    <span class="pill-count"><?= $approvedLaboranCount ?></span>
                </button>
                <button type="button" class="filter-pill" data-status="kaur" onclick="setFilterStatus('kaur')">
                    <span>🟢 Disetujui Ka. Ur</span>
                    <span class="pill-count"><?= $approvedKaurCount ?></span>
                </button>
                <button type="button" class="filter-pill" data-status="admin" onclick="setFilterStatus('admin')">
                    <span>🟣 Disetujui Admin</span>
                    <span class="pill-count"><?= $approvedAdminCount ?></span>
                </button>
                <button type="button" class="filter-pill" data-status="rejected" onclick="setFilterStatus('rejected')">
                    <span>🔴 Ditolak</span>
                    <span class="pill-count"><?= $rejectedCount ?></span>
                </button>
            </div>

            <!-- Row 1: Unified Multi-Search Pill Component & Standalone Add Button (+ 1/4) -->
            <div class="search-pill-container">
                <div class="unified-search-pill" id="mainSearchPill">
                    <!-- Category Dropdown Container -->
                    <div class="custom-dropdown-container">
                        <input type="hidden" id="mainCategoryVal" value="query">
                        <button type="button" onclick="toggleCustomDropdown('main-cat', event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-0.5 hover:text-orange-600 focus:outline-none" style="display:flex;align-items:center;gap:6px;background:none;border:none;cursor:pointer;font-weight:700;font-size:0.75rem;color:#1e293b;">
                            <span id="label-filter-main-cat">Cari Kata Kunci</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow" id="arrow-filter-main-cat"></i>
                        </button>
                        <div id="menu-filter-main-cat" class="custom-dropdown-menu">
                            <div onclick="selectMainCategory('query', '🔍 Kata Kunci (Semua)', this)" class="dropdown-item active"><span>🔍 Kata Kunci (Semua)</span></div>
                            <div onclick="selectMainCategory('nama', '🏷️ Nama Peminjam', this)" class="dropdown-item"><span>🏷️ Nama Peminjam</span></div>
                            <div onclick="selectMainCategory('ruangan', '🚪 Nama / Kode Ruangan', this)" class="dropdown-item"><span>🚪 Nama / Kode Ruangan</span></div>
                            <div onclick="selectMainCategory('tanggal', '📅 Tanggal Peminjaman', this)" class="dropdown-item"><span>📅 Tanggal Peminjaman</span></div>
                            <div onclick="selectMainCategory('keterangan', '📝 Keterangan / Keperluan', this)" class="dropdown-item"><span>📝 Keterangan / Keperluan</span></div>
                            <div onclick="selectMainCategory('status', '⚡ Status Permohonan', this)" class="dropdown-item"><span>⚡ Status Permohonan</span></div>
                        </div>
                    </div>

                    <div class="unified-divider"></div>

                    <!-- Input Text Value Container (3 modes: text, tanggal, status) -->
                    <div id="mainValueContainer" style="flex: 1; display: flex; align-items: center; min-width: 0; position: relative;">
                        <!-- MODE 1: Text Search (default) -->
                        <div id="modeText" style="flex:1;display:flex;align-items:center;min-width:0;">
                            <i class="fa-solid fa-magnifying-glass" style="color: #94a3b8; font-size: 0.75rem; margin-right: 8px; flex-shrink:0;"></i>
                            <input type="text" id="mainSearchInput" onkeydown="if(event.key === 'Enter'){ event.preventDefault(); filterTable(); }" placeholder="Ketik kata kunci lalu tekan Enter atau klik Cari..." style="width: 100%; font-size: 0.78rem; font-weight: 500; background: transparent; border: none; outline: none; color: #1e293b;">
                        </div>
                        <!-- MODE 2: Date Range Picker (single input, range mode) -->
                        <div id="modeTanggal" style="flex:1;display:none;align-items:center;min-width:0;gap:6px;">
                            <i class="fa-solid fa-calendar-days" style="color: #94a3b8; font-size: 0.75rem; flex-shrink:0; margin-right:6px;"></i>
                            <input type="text" id="dateRangePicker" placeholder="Pilih rentang tanggal..." readonly style="flex:1;font-size:0.78rem;font-weight:500;background:transparent;border:none;outline:none;color:#1e293b;cursor:pointer;min-width:0;">
                            <button type="button" id="btnClearDateRange" onclick="clearDateRange()" style="display:none;background:none;border:none;color:#94a3b8;cursor:pointer;padding:2px 4px;font-size:0.75rem;flex-shrink:0;" title="Hapus filter tanggal">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <!-- MODE 3: Status Dropdown -->
                        <div id="modeStatus" style="flex:1;display:none;align-items:center;min-width:0;position:relative;">
                            <i class="fa-solid fa-circle-half-stroke" style="color: #94a3b8; font-size: 0.75rem; margin-right: 8px; flex-shrink:0;"></i>
                            <button type="button" id="statusDropdownTrigger" onclick="toggleMainStatusDropdown(event)" style="flex:1;display:flex;align-items:center;justify-content:space-between;background:transparent;border:none;outline:none;cursor:pointer;font-size:0.78rem;font-weight:600;color:#1e293b;padding:0;">
                                <span id="statusDropdownLabel" style="display:flex;align-items:center;gap:6px;">
                                    <span id="statusDropdownDot" style="width:8px;height:8px;border-radius:50%;background:#94a3b8;display:inline-block;flex-shrink:0;"></span>
                                    <span id="statusDropdownText">Semua Status</span>
                                </span>
                                <i class="fa-solid fa-chevron-down" style="font-size:0.65rem;color:#94a3b8;margin-right:4px;"></i>
                            </button>
                            <input type="hidden" id="statusDropdownVal" value="">
                            <!-- Status Dropdown Menu -->
                            <div id="statusDropdownMenu" style="display:none;position:absolute;top:calc(100% + 8px);left:-60px;min-width:240px;background:#fff;border:1.5px solid #e2e8f0;border-radius:16px;box-shadow:0 16px 40px rgba(0,0,0,0.18);z-index:100030;padding:6px;">
                                <div onclick="selectStatusFilter('','Semua Status','#94a3b8',this)" class="status-filter-opt active" style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:10px;font-size:0.82rem;font-weight:700;color:#334155;cursor:pointer;transition:all 0.15s;">
                                    <span style="width:8px;height:8px;border-radius:50%;background:#94a3b8;display:inline-block;flex-shrink:0;"></span> Semua Status
                                </div>
                                <div onclick="selectStatusFilter('pending','Menunggu ACC','#f59e0b',this)" class="status-filter-opt" style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:10px;font-size:0.82rem;font-weight:700;color:#334155;cursor:pointer;transition:all 0.15s;">
                                    <span style="width:8px;height:8px;border-radius:50%;background:#f59e0b;display:inline-block;flex-shrink:0;"></span> Menunggu ACC
                                </div>
                                <div onclick="selectStatusFilter('laboran','Disetujui Laboran','#3b82f6',this)" class="status-filter-opt" style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:10px;font-size:0.82rem;font-weight:700;color:#334155;cursor:pointer;transition:all 0.15s;">
                                    <span style="width:8px;height:8px;border-radius:50%;background:#3b82f6;display:inline-block;flex-shrink:0;"></span> Disetujui Laboran
                                </div>
                                <div onclick="selectStatusFilter('kaur','Disetujui Ka. Ur','#22c55e',this)" class="status-filter-opt" style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:10px;font-size:0.82rem;font-weight:700;color:#334155;cursor:pointer;transition:all 0.15s;">
                                    <span style="width:8px;height:8px;border-radius:50%;background:#22c55e;display:inline-block;flex-shrink:0;"></span> Disetujui Ka. Ur
                                </div>
                                <div onclick="selectStatusFilter('admin','Disetujui Admin','#8b5cf6',this)" class="status-filter-opt" style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:10px;font-size:0.82rem;font-weight:700;color:#334155;cursor:pointer;transition:all 0.15s;">
                                    <span style="width:8px;height:8px;border-radius:50%;background:#8b5cf6;display:inline-block;flex-shrink:0;"></span> Disetujui Admin
                                </div>
                                <div onclick="selectStatusFilter('rejected','Ditolak','#ef4444',this)" class="status-filter-opt" style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:10px;font-size:0.82rem;font-weight:700;color:#334155;cursor:pointer;transition:all 0.15s;">
                                    <span style="width:8px;height:8px;border-radius:50%;background:#ef4444;display:inline-block;flex-shrink:0;"></span> Ditolak
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Tombol Cari -->
                    <button type="button" onclick="filterTable()" class="btn-search-cari" style="padding: 6px 14px; background: linear-gradient(135deg, #ea580c, #f97316); color: #fff; font-size: 0.75rem; font-weight: 700; border: none; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: 6px; flex-shrink: 0; box-shadow: 0 2px 6px rgba(234,88,12,0.25);">
                        <i class="fa-solid fa-magnifying-glass text-[11px]"></i> Cari
                    </button>
                </div>

                <!-- Standalone Add Filter Button (+ 1/4) -->
                <button type="button" id="standaloneAddBtn" onclick="toggleOrAddFilterRow(event)" class="btn-standalone-add" title="Buka / Tutup / Tambah Filter Baru (Maks 4)">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span id="filterCountBadge" class="badge-standalone-count">1/4</span>
                </button>

                <!-- Extra Filter Rows Card Popover -->
                <div id="extraRowsCard" class="extra-rows-card">
                    <div id="additionalFilterRowsContainer" style="display: flex; flex-direction: column; gap: 8px;"></div>
                    
                    <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #f1f5f9; padding-top: 10px; margin-top: 10px; font-size: 0.72rem;">
                        <span style="color: #94a3b8;">Gunakan kombinasi kriteria untuk mempersempit pencarian data.</span>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <button type="button" onclick="resetMultiSearch()" style="background: none; border: none; color: #dc2626; font-weight: 700; cursor: pointer;">
                                Reset All
                            </button>
                            <button type="button" onclick="filterTable()" style="padding: 5px 12px; background: #ea580c; color: #fff; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                                <i class="fa-solid fa-magnifying-glass text-[10px]"></i> Terapkan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2: Page Size & Records Count -->
            <div style="padding-top: 10px; border-top: 1px solid #f1f5f9; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px;">
                <div style="font-size: 0.76rem; color: #64748b; font-weight: 500;">
                    <span>Kelola & telusuri data peminjaman ruangan secara langsung.</span>
                </div>

                <!-- Page Size & Counter Right -->
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 0.75rem; color: #475569; background: #f8fafc; border: 1px solid #e2e8f0; padding: 4px 12px; border-radius: 10px;">
                        <span style="font-weight: 500;">Tampilkan</span>
                        <select id="selectPerPage" onchange="changePageSize(this.value)" style="height: 24px; padding: 0 4px; font-size: 0.75rem; font-weight: 700; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; color: #1e293b; cursor: pointer; outline: none;">
                            <option value="10" selected>10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span style="font-weight: 500;">data/hal</span>
                        <span style="color: #cbd5e1;">|</span>
                        <span>Total: <strong class="total-rows-count" id="toolbarTotalCount" style="color: #0f172a; font-weight: 800;"><?= $totalCount ?></strong></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="table-card">
            <div class="table-responsive">
                <table class="laboran-table" id="laboranBookingTable">
                    <colgroup>
                        <col style="width: 44px;">
                        <col style="width: 25%;">
                        <col style="width: 18%;">
                        <col style="width: 16%;">
                        <col style="width: 23%;">
                        <col style="width: 13%;">
                        <col style="width: 50px;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th style="width: 44px; text-align: center; padding: 12px 6px;">
                                <input type="checkbox" id="selectAllCheckbox" class="custom-checkbox" onchange="toggleSelectAll(this)">
                            </th>
                            <th style="width: 25%;">Ruangan</th>
                            <th style="width: 18%;">Peminjam & Waktu</th>
                            <th style="width: 16%;">Tanggal</th>
                            <th style="width: 23%;">Keterangan / Keperluan</th>
                            <th style="width: 13%;">Status</th>
                            <th style="width: 50px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($peminjaman)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">
                                    <i class="fa-solid fa-inbox" style="font-size: 2rem; margin-bottom: 8px; display: block;"></i>
                                    Belum ada data peminjaman ruangan.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            if (!function_exists('format_indo_date_php')) {
                                function format_indo_date_php($date1, $date2 = null) {
                                    if (empty($date1)) return '-';
                                    $mNames = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                    $t1 = strtotime($date1);
                                    if (!$t1) return $date1;
                                    $d1 = date('j', $t1);
                                    $m1 = (int)date('n', $t1);
                                    $y1 = date('Y', $t1);

                                    if (empty($date2) || $date1 === $date2) {
                                        return "{$d1} {$mNames[$m1]} {$y1}";
                                    }

                                    $t2 = strtotime($date2);
                                    if (!$t2) return "{$d1} {$mNames[$m1]} {$y1}";
                                    $d2 = date('j', $t2);
                                    $m2 = (int)date('n', $t2);
                                    $y2 = date('Y', $t2);

                                    if ($y1 === $y2 && $m1 === $m2) {
                                        return "{$d1} - {$d2} {$mNames[$m1]} {$y1}";
                                    } elseif ($y1 === $y2) {
                                        return "{$d1} {$mNames[$m1]} - {$d2} {$mNames[$m2]} {$y1}";
                                    } else {
                                        return "{$d1} {$mNames[$m1]} {$y1} - {$d2} {$mNames[$m2]} {$y2}";
                                    }
                                }
                            }

                            foreach($peminjaman as $idx => $p): 
                                $s = $p->status;
                                $isPending = ($s === 'Pending');
                                $statusCategory = 'other';
                                
                                if ($isPending) {
                                    $dot = '#f59e0b'; $bg = '#fffbeb'; $color = '#b45309'; $label = 'Menunggu Persetujuan'; $statusCategory = 'pending';
                                } elseif (stripos($s, 'Ka. Ur') !== false || stripos($s, 'Kaur') !== false) {
                                    $dot = '#22c55e'; $bg = '#f0fdf4'; $color = '#166534'; $label = 'Disetujui Ka. Ur'; $statusCategory = 'kaur';
                                } elseif (stripos($s, 'Laboran') !== false) {
                                    $dot = '#3b82f6'; $bg = '#eff6ff'; $color = '#1d4ed8'; $label = 'Disetujui Laboran'; $statusCategory = 'laboran';
                                } elseif (stripos($s, 'Admin') !== false) {
                                    $dot = '#8b5cf6'; $bg = '#f5f3ff'; $color = '#6d28d9'; $label = 'Disetujui Admin'; $statusCategory = 'admin';
                                } elseif ($s === 'Ditolak') {
                                    $dot = '#ef4444'; $bg = '#fef2f2'; $color = '#991b1b'; $label = 'Ditolak'; $statusCategory = 'rejected';
                                } else {
                                    $dot = '#94a3b8'; $bg = '#f8fafc'; $color = '#475569'; $label = htmlspecialchars($s);
                                }

                                $dateFormatted = format_indo_date_php($p->tanggal_mulai, $p->tanggal_selesai);
                                $timeFormatted = substr($p->jam_mulai, 0, 5) . ' - ' . substr($p->jam_selesai, 0, 5);
                            ?>
                            <tr class="booking-row" 
                                <?= $idx >= 10 ? 'style="display: none;"' : '' ?>
                                data-id="<?= $p->id ?>" 
                                data-status-category="<?= $statusCategory ?>" 
                                data-nama="<?= strtolower(htmlspecialchars($p->nama_lengkap ?? '')) ?>"
                                data-ruangan="<?= strtolower(htmlspecialchars(($p->kode_ruangan ?? '') . ' ' . ($p->nama_ruangan ?? '') . ' ' . ($p->nama_kategori ?? ''))) ?>"
                                data-tanggal="<?= strtolower(htmlspecialchars($dateFormatted . ' ' . ($p->tanggal_mulai ?? '') . ' ' . ($p->tanggal_selesai ?? ''))) ?>"
                                data-tanggal-mulai="<?= htmlspecialchars($p->tanggal_mulai ?? '') ?>"
                                data-tanggal-selesai="<?= htmlspecialchars($p->tanggal_selesai ?? '') ?>"
                                data-keterangan="<?= strtolower(htmlspecialchars($p->keterangan ?? '')) ?>"
                                data-status="<?= strtolower(htmlspecialchars($s . ' ' . $label)) ?>"
                                data-search="<?= strtolower(htmlspecialchars(($p->nama_lengkap ?? '') . ' ' . ($p->kode_ruangan ?? '') . ' ' . ($p->nama_ruangan ?? '') . ' ' . ($p->keterangan ?? '') . ' ' . ($p->status ?? '') . ' ' . $dateFormatted)) ?>">
                                <td style="text-align: center;">
                                    <input type="checkbox" class="custom-checkbox row-checkbox" data-id="<?= $p->id ?>" data-status="<?= $p->status ?>" onchange="updateBatchBar()">
                                </td>
                                <td>
                                    <div class="tr-room-col">
                                        <div class="tr-room-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1e293b" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                        </div>
                                        <div class="tr-room-info" title="<?= htmlspecialchars(($p->nama_ruangan ?: '') . ' (' . ($p->kode_ruangan ?: '') . ')') ?>">
                                            <div class="tr-room-code"><?= htmlspecialchars($p->kode_ruangan ?: '-') ?></div>
                                            <div class="tr-room-name"><?= htmlspecialchars($p->nama_ruangan ?: '-') ?></div>
                                        </div>

                                        <!-- Floating Room Detail Tooltip on Hover -->
                                        <div class="room-hover-tooltip">
                                            <div class="rht-header">
                                                <span class="rht-code"><?= htmlspecialchars($p->kode_ruangan ?: '-') ?></span>
                                                <span class="rht-cat"><?= htmlspecialchars($p->nama_kategori ?: 'Ruangan') ?></span>
                                            </div>
                                            <div class="rht-title"><?= htmlspecialchars($p->nama_ruangan ?: '-') ?></div>
                                            <?php if (!empty($p->lokasi) || !empty($p->kapasitas)): ?>
                                            <div class="rht-meta">
                                                <?php if (!empty($p->lokasi)): ?><span>📍 <?= htmlspecialchars($p->lokasi) ?></span><?php endif; ?>
                                                <?php if (!empty($p->kapasitas)): ?><span>👥 <?= htmlspecialchars($p->kapasitas) ?> Orang</span><?php endif; ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tr-user-time-col">
                                        <div class="tr-pill-user" title="<?= htmlspecialchars($p->nama_lengkap) ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#1e293b" stroke-width="2.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                            <span><?= htmlspecialchars($p->nama_lengkap) ?></span>
                                        </div>
                                        <div class="tr-pill-time" title="<?= $timeFormatted ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                            <span><?= $timeFormatted ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tr-date-col">
                                        <?= $dateFormatted ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="tr-desc-col" title="<?= htmlspecialchars($p->keterangan ?: '-') ?>">
                                        <span class="tr-desc-text"><?= htmlspecialchars($p->keterangan ?: '-') ?></span>

                                        <!-- Floating Keterangan Detail Tooltip on Hover -->
                                        <div class="desc-hover-tooltip">
                                            <span class="dht-badge">📝 Keterangan / Keperluan</span>
                                            <div class="dht-content"><?= htmlspecialchars($p->keterangan ?: '-') ?></div>
                                        </div>
                                        <?php if ($p->status === 'Ditolak' && !empty($p->alasan_penolakan)): ?>
                                            <div style="font-size: 0.75rem; color: #dc2626; margin-top: 4px;">
                                                <i class="fa-solid fa-triangle-exclamation"></i> Alasan: <?= htmlspecialchars($p->alasan_penolakan) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge" style="background: <?= $bg ?>; color: <?= $color ?>;" title="<?= htmlspecialchars($label) ?>">
                                        <span class="status-dot" style="background: <?= $dot ?>;"></span>
                                        <span class="status-text"><?= $label ?></span>
                                    </span>
                                </td>
                                <td style="text-align: center; overflow: visible; position: relative;">
                                    <div class="action-dropdown-wrap">
                                        <button type="button" class="btn-action-dots" onclick="toggleActionDropdown(<?= $p->id ?>, event)" title="Menu Aksi">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <div class="action-dropdown-menu" id="actionMenu_<?= $p->id ?>">
                                            <?php if ($isPending): ?>
                                                <button type="button" class="action-dropdown-item item-acc" onclick="singleApprove(<?= $p->id ?>)">
                                                    <i class="fa-solid fa-check"></i> Setujui (Laboran)
                                                </button>
                                                <button type="button" class="action-dropdown-item item-rej" onclick="openSingleRejectModal(<?= $p->id ?>)">
                                                    <i class="fa-solid fa-ban"></i> Tolak Peminjaman
                                                </button>
                                            <?php endif; ?>
                                            <?php if ($statusCategory === 'laboran' || $statusCategory === 'kaur' || $statusCategory === 'admin'): ?>
                                                <button type="button" class="action-dropdown-item item-qr" onclick="openSuratModal(<?= $p->id ?>)">
                                                    <i class="fa-solid fa-qrcode"></i> Cetak Surat QR
                                                </button>
                                            <?php endif; ?>
                                            <button type="button" class="action-dropdown-item" onclick="openDetailModal(<?= htmlspecialchars(json_encode($p)) ?>)">
                                                <i class="fa-solid fa-eye text-blue-500"></i> Detail Permohonan
                                            </button>
                                            <div class="action-dropdown-divider"></div>
                                            <button type="button" class="action-dropdown-item item-del" onclick="deleteBooking(<?= $p->id ?>)">
                                                <i class="fa-solid fa-trash-can"></i> Hapus Data
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <!-- Dynamic Bottom Pagination Bar -->
            <div class="pagination-bar">
                <div id="paginationInfo" style="font-size: 0.78rem; font-weight: 500; color: #64748b;">
                    Menampilkan <span id="pageInfoStart"><?= !empty($peminjaman) ? 1 : 0 ?></span> - <span id="pageInfoEnd"><?= min(10, count($peminjaman ?? [])) ?></span> dari <strong id="pageInfoTotal" style="color: #0f172a; font-weight: 800;"><?= count($peminjaman ?? []) ?></strong> data
                </div>
                <div class="pagination-controls" id="paginationControls"></div>
            </div>
        </div>

    </div>

    <!-- =========================================================
         FLOATING BATCH ACTION BAR (MULTIPLE SELECT ACC / TOLAK)
         ========================================================= -->
    <div id="floatingBatchBar" class="floating-batch-bar">
        <div class="batch-count-badge">
            <i class="fa-solid fa-circle-check"></i>
            <span id="selectedCountBadge">0</span> Data Terpilih
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="button" class="btn-batch-acc" onclick="submitBatchApprove()">
                <i class="fa-solid fa-check-double"></i> ACC Massal (Laboran)
            </button>
            <button type="button" class="btn-batch-rej" onclick="openBatchRejectModal()">
                <i class="fa-solid fa-ban"></i> Tolak Massal
            </button>
            <button type="button" class="btn-batch-cancel" onclick="deselectAll()">
                Batal
            </button>
        </div>
    </div>

    <!-- =========================================================
         MODAL CATATAN PENOLAKAN (WAJIB ALASAN)
         ========================================================= -->
    <div class="modal-overlay" id="rejectModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="fa-solid fa-triangle-exclamation text-red-500"></i> Catatan Penolakan Peminjaman</h3>
                <button type="button" class="modal-close-btn" onclick="closeRejectModal()">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="rejectTargetIds">
                <input type="hidden" id="rejectIsBatch" value="0">
                <div class="form-group">
                    <label style="color: #991b1b;">Alasan Penolakan (Wajib Diisi) <span style="color:red;">*</span></label>
                    <textarea id="alasanPenolakanTextarea" rows="3" class="form-control" placeholder="Tuliskan alasan penolakan secara jelas (contoh: Ruangan sedang dalam pemeliharaan berkala / Jadwal praktikum reguler)..." required></textarea>
                    <small style="color: #64748b; font-size: 0.75rem; margin-top: 4px; display: block;">Catatan ini akan dikirimkan dan ditampilkan pada status permohonan peminjam.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeRejectModal()">Batal</button>
                <button type="button" class="btn" style="background: #dc2626; color: #fff;" onclick="submitRejectConfirm()">
                    <i class="fa-solid fa-ban"></i> Konfirmasi Penolakan
                </button>
            </div>
        </div>
    </div>

    <!-- =========================================================
         MODAL DETAIL PEMINJAMAN
         ========================================================= -->
    <div class="modal-overlay" id="detailModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="fa-solid fa-circle-info text-blue-500"></i> Detail Permohonan Peminjaman</h3>
                <button type="button" class="modal-close-btn" onclick="closeDetailModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="detail-row">
                    <div class="detail-label">Nama Peminjam</div>
                    <div class="detail-val" id="dtlNama">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Ruangan</div>
                    <div class="detail-val" id="dtlRuangan">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Tanggal</div>
                    <div class="detail-val" id="dtlTanggal">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Waktu</div>
                    <div class="detail-val" id="dtlWaktu">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Keperluan</div>
                    <div class="detail-val" id="dtlKeterangan">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Status</div>
                    <div class="detail-val" id="dtlStatus">-</div>
                </div>
                <div class="detail-row" id="dtlAlasanRow" style="display: none;">
                    <div class="detail-label" style="color: #dc2626;">Alasan Tolak</div>
                    <div class="detail-val" id="dtlAlasan" style="color: #dc2626;">-</div>
                </div>
            </div>
            <div class="modal-footer" style="display: flex; justify-content: space-between; align-items: center;">
                <button type="button" id="dtlSuratBtn" onclick="openSuratModalFromDetail()" class="btn btn-primary" style="display: none; background: #16a34a; border-color: #16a34a; align-items: center; gap: 6px; cursor: pointer;">
                    <i class="fa-solid fa-qrcode"></i> Cetak Surat Resmi (QR)
                </button>
                <button type="button" class="btn btn-secondary" onclick="closeDetailModal()">Tutup</button>
            </div>
        </div>
    </div>

    <!-- =========================================================
         MODAL POPUP PRATINJAU & CETAK SURAT RESMI QR
         ========================================================= -->
    <div class="modal-overlay" id="suratModal" style="z-index: 100050;">
        <div class="modal-card surat-modal-card" style="max-width: 860px; width: 95%; height: 90vh; display: flex; flex-direction: column; border-radius: 18px; overflow: hidden; box-shadow: 0 25px 60px rgba(15, 23, 42, 0.35);">
            <div class="modal-header" style="padding: 14px 20px; background: #ffffff; border-bottom: 1.5px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #ecfdf5; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                        <i class="fa-solid fa-file-circle-check"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin: 0;">Surat Resmi Ber-QR Code</h3>
                        <p style="font-size: 0.75rem; color: #64748b; margin: 0;">Pratinjau dokumen legalitas peminjaman laboratorium</p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <button type="button" onclick="printSuratIframe()" style="padding: 7px 14px; background: linear-gradient(135deg, #16a34a, #15803d); color: #ffffff; border: none; border-radius: 10px; font-weight: 700; font-size: 0.8rem; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 2px 8px rgba(22, 163, 74, 0.25);">
                        <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
                    </button>
                    <a id="suratNewTabBtn" href="#" target="_blank" title="Buka di Tab Baru" style="width: 34px; height: 34px; border-radius: 8px; border: 1px solid #cbd5e1; background: #f8fafc; color: #475569; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                        <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.75rem;"></i>
                    </a>
                    <button type="button" onclick="closeSuratModal()" style="width: 34px; height: 34px; border-radius: 8px; border: none; background: #f1f5f9; color: #64748b; font-size: 1.2rem; cursor: pointer; display: flex; align-items: center; justify-content: center;">&times;</button>
                </div>
            </div>
            <div class="modal-body" style="flex: 1; padding: 0; background: #525659; position: relative; overflow: hidden;">
                <iframe id="suratIframe" src="about:blank" style="width: 100%; height: 100%; border: none; display: block;" onload="hideSuratLoader()"></iframe>
                <div id="suratLoader" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: #f8fafc; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; z-index: 10;">
                    <i class="fa-solid fa-circle-notch fa-spin" style="font-size: 2rem; color: #16a34a;"></i>
                    <span style="font-size: 0.85rem; font-weight: 600; color: #475569;">Memuat Surat Resmi...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================
         MODAL BUAT PEMINJAMAN MANUAL
         ========================================================= -->
    <div class="modal-overlay" id="bookingModal">
        <div class="modal-card" style="max-width: 600px;">
            <div class="modal-header">
                <h3><i class="fa-solid fa-plus-circle text-orange-500"></i> Buat Peminjaman Ruangan</h3>
                <button type="button" class="modal-close-btn" onclick="closeAdminBookingModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formBooking">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama_lengkap" value="<?= $this->session->userdata('name') ?>" readonly style="background: #f1f5f9; color: #64748b;">
                    </div>

                    <div class="form-group">
                        <label>Kategori Ruangan</label>
                        <select class="form-control" id="kategoriSelect" name="id_kategori" required>
                            <option value="">Pilih Kategori</option>
                            <?php foreach($kategori as $k): ?>
                                <option value="<?= $k->id ?>"><?= htmlspecialchars($k->nama_kategori) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Ruangan</label>
                        <select class="form-control" id="ruanganSelect" name="id_ruangan" required>
                            <option value="">Pilih Ruangan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Keterangan / Keperluan</label>
                        <textarea class="form-control" name="keterangan" rows="2" placeholder="Contoh: Praktikum Pemrograman Web / Ujian Praktikum..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Tanggal Peminjaman</label>
                        <input type="text" class="form-control" name="tanggal_peminjaman" id="tanggalPeminjaman" placeholder="Pilih Rentang Tanggal..." required>
                    </div>

                    <div class="form-group" id="timeSelectionGroup" style="display: none;">
                        <label>Waktu Peminjaman</label>
                        <div style="display: flex; gap: 12px;">
                            <input type="text" class="form-control" name="jam_mulai" id="inputJamMulai" placeholder="Jam Mulai" readonly style="cursor: pointer;" onclick="openInlinePicker('mulai')" required>
                            <input type="text" class="form-control" name="jam_selesai" id="inputJamSelesai" placeholder="Jam Selesai" readonly style="cursor: pointer;" onclick="openInlinePicker('selesai')" required>
                        </div>

                        <!-- Inline Clock Picker Panel -->
                        <div id="inlineClockPanel" style="display:none; margin-top: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px;">
                            <div style="display: flex; gap: 14px; flex-wrap: wrap;">
                                <div style="flex: 1; min-width: 140px;">
                                    <div style="font-size: 0.72rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Pilih Cepat</div>
                                    <div id="tpTimeSlots" style="display:grid; grid-template-columns:1fr 1fr; gap:6px; margin-top:6px;">
                                        <div class="tp-slot" data-start="08:00" data-end="10:00" style="padding:7px 4px; border:1px solid #e2e8f0; border-radius:8px; background:#fff; font-size:0.72rem; font-weight:700; text-align:center; cursor:pointer;">08:00–10:00</div>
                                        <div class="tp-slot" data-start="10:00" data-end="12:00" style="padding:7px 4px; border:1px solid #e2e8f0; border-radius:8px; background:#fff; font-size:0.72rem; font-weight:700; text-align:center; cursor:pointer;">10:00–12:00</div>
                                        <div class="tp-slot" data-start="13:00" data-end="15:00" style="padding:7px 4px; border:1px solid #e2e8f0; border-radius:8px; background:#fff; font-size:0.72rem; font-weight:700; text-align:center; cursor:pointer;">13:00–15:00</div>
                                        <div class="tp-slot" data-start="15:00" data-end="17:00" style="padding:7px 4px; border:1px solid #e2e8f0; border-radius:8px; background:#fff; font-size:0.72rem; font-weight:700; text-align:center; cursor:pointer;">15:00–17:00</div>
                                    </div>
                                </div>
                                <div style="flex: 1; min-width: 180px;">
                                    <div class="tp-tabs" style="margin-bottom: 8px;">
                                        <div class="tp-tab active" id="tpTabHour" onclick="setMode('hour')">Jam</div>
                                        <div class="tp-tab" id="tpTabMinute" onclick="setMode('minute')">Menit</div>
                                    </div>
                                    <div class="tp-clock-container" id="tpClockContainer">
                                        <div class="tp-clock-center"></div>
                                        <div class="tp-clock-hand" id="tpClockHand"></div>
                                        <div id="tpClockNumbers"></div>
                                    </div>
                                </div>
                            </div>
                            <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:12px; border-top:1px solid #e2e8f0; padding-top:10px;">
                                <button type="button" onclick="closeInlinePicker()" class="btn btn-secondary" style="padding:6px 12px; font-size:0.78rem;">Batal</button>
                                <button type="button" onclick="applyInlinePicker()" class="btn btn-primary" style="padding:6px 12px; font-size:0.78rem;">Terapkan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeAdminBookingModal()">Batal</button>
                <button type="button" class="btn btn-primary" onclick="submitForm()">Simpan Peminjaman</button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="<?= base_url('assets/js/timepicker.js?v=' . time()) ?>"></script>

    <script>
        const BASE_URL = '<?= base_url() ?>';
        let currentFilterStatus = 'all';
        let pageSize = 10;
        let currentPage = 1;
        let extraRowCounter = 0;

        const SEARCH_CATEGORIES = [
            { key: 'query', label: '🔍 Kata Kunci (Semua)' },
            { key: 'nama', label: '🏷️ Nama Peminjam' },
            { key: 'ruangan', label: '🚪 Nama / Kode Ruangan' },
            { key: 'tanggal', label: '📅 Tanggal Peminjaman' },
            { key: 'keterangan', label: '📝 Keterangan / Keperluan' },
            { key: 'status', label: '⚡ Status Permohonan' }
        ];

        // ==========================================
        // MULTI-SEARCH & DROPDOWNS
        // ==========================================
        function toggleCustomDropdown(id, e) {
            if (e) e.stopPropagation();
            const menu = document.getElementById('menu-filter-' + id);
            const arrow = document.getElementById('arrow-filter-' + id);
            const isShown = menu.classList.contains('show');
            closeAllCustomDropdowns();
            if (!isShown) {
                menu.classList.add('show');
                if (arrow) arrow.style.transform = 'rotate(180deg)';
            }
        }

        function closeAllCustomDropdowns() {
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.remove('show'));
            document.querySelectorAll('.dropdown-arrow').forEach(a => a.style.transform = 'rotate(0deg)');
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.custom-dropdown-container') && !e.target.closest('.extra-rows-card') && !e.target.closest('#standaloneAddBtn')) {
                closeAllCustomDropdowns();
                const card = document.getElementById('extraRowsCard');
                if (card && !e.target.closest('.extra-rows-card')) {
                    card.style.display = 'none';
                    const btn = document.getElementById('standaloneAddBtn');
                    if (btn) btn.classList.remove('active');
                }
            }
        });

        // =============================================
        // DATE RANGE & STATUS DROPDOWN HELPERS
        // =============================================
        let fpRange = null;

        function initDatePickers() {
            if (fpRange) return;
            fpRange = flatpickr('#dateRangePicker', {
                mode: 'range',
                locale: { firstDayOfWeek: 1 },
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd M Y',
                allowInput: false,
                disableMobile: true,
                onClose: function(selectedDates) {
                    const btn = document.getElementById('btnClearDateRange');
                    if (btn) btn.style.display = selectedDates.length > 0 ? 'inline-flex' : 'none';
                }
            });
        }

        function clearDateRange() {
            if (fpRange) fpRange.clear();
            const btn = document.getElementById('btnClearDateRange');
            if (btn) btn.style.display = 'none';
        }

        function toggleMainStatusDropdown(e) {
            if (e) e.stopPropagation();
            const menu = document.getElementById('statusDropdownMenu');
            const isOpen = menu.style.display === 'block';
            menu.style.display = isOpen ? 'none' : 'block';
        }

        function selectStatusFilter(val, label, color, el) {
            document.getElementById('statusDropdownVal').value = val;
            document.getElementById('statusDropdownDot').style.background = color;
            document.getElementById('statusDropdownText').innerText = label;
            document.querySelectorAll('.status-filter-opt').forEach(o => {
                o.style.background = '';
                o.style.color = '#334155';
            });
            if (el) {
                el.style.background = '#fff7ed';
                el.style.color = '#ea580c';
            }
            document.getElementById('statusDropdownMenu').style.display = 'none';
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('#modeStatus')) {
                const m = document.getElementById('statusDropdownMenu');
                if (m) m.style.display = 'none';
            }
        }, true);

        function switchMainMode(mode) {
            document.getElementById('modeText').style.display = (mode === 'text') ? 'flex' : 'none';
            document.getElementById('modeTanggal').style.display = (mode === 'tanggal') ? 'flex' : 'none';
            document.getElementById('modeStatus').style.display = (mode === 'status') ? 'flex' : 'none';
            if (mode === 'tanggal') initDatePickers();
        }

        function selectMainCategory(catKey, catLabel, el) {
            document.getElementById('mainCategoryVal').value = catKey;
            document.getElementById('label-filter-main-cat').innerText = catLabel.replace(/^[^\s]+\s*/, '');
            if (el) {
                el.parentElement.querySelectorAll('.dropdown-item').forEach(d => d.classList.remove('active'));
                el.classList.add('active');
            }
            closeAllCustomDropdowns();
            // Switch input mode
            if (catKey === 'tanggal') {
                switchMainMode('tanggal');
            } else if (catKey === 'status') {
                switchMainMode('status');
            } else {
                switchMainMode('text');
                document.getElementById('mainSearchInput').focus();
            }
        }


        function toggleOrAddFilterRow(e) {
            if (e) e.stopPropagation();
            const card = document.getElementById('extraRowsCard');
            const btn = document.getElementById('standaloneAddBtn');
            const isOpen = card.style.display === 'block';

            if (isOpen) {
                card.style.display = 'none';
                if (btn) btn.classList.remove('active');
            } else {
                card.style.display = 'block';
                if (btn) btn.classList.add('active');
                const container = document.getElementById('additionalFilterRowsContainer');
                if (container.children.length === 0) {
                    addFilterRow();
                }
            }
        }

        function addFilterRow(defaultKey = 'ruangan', defaultVal = '') {
            const container = document.getElementById('additionalFilterRowsContainer');
            if (container.children.length >= 3) {
                Swal.fire({
                    title: 'Batas Maksimal Filter',
                    text: 'Maksimal 4 kriteria pencarian kombinasi (1 utama + 3 filter tambahan).',
                    icon: 'info',
                    confirmButtonColor: '#ea580c'
                });
                return;
            }

            extraRowCounter++;
            const rowId = 'extra-row-' + extraRowCounter;

            let dropdownItems = '';
            SEARCH_CATEGORIES.forEach(c => {
                const isActive = (c.key === defaultKey) ? 'active' : '';
                dropdownItems += `<div onclick="selectExtraCategory('${rowId}', '${c.key}', '${c.label}', this)" class="dropdown-item ${isActive}"><span>${c.label}</span></div>`;
            });

            const catObj = SEARCH_CATEGORIES.find(c => c.key === defaultKey) || SEARCH_CATEGORIES[0];
            const cleanLabel = catObj.label.replace(/^[^\s]+\s*/, '');

            const rowHtml = document.createElement('div');
            rowHtml.className = 'extra-filter-row';
            rowHtml.id = rowId;
            rowHtml.innerHTML = `
                <div class="unified-search-pill" style="height: 40px;">
                    <div class="custom-dropdown-container">
                        <input type="hidden" class="extra-category-val" value="${defaultKey}">
                        <button type="button" onclick="toggleCustomDropdown('${rowId}', event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-0.5 hover:text-orange-600 focus:outline-none" style="display:flex;align-items:center;gap:5px;background:none;border:none;cursor:pointer;font-weight:700;font-size:0.75rem;color:#1e293b;">
                            <span class="extra-category-label">${cleanLabel}</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow" id="arrow-filter-${rowId}"></i>
                        </button>
                        <div id="menu-filter-${rowId}" class="custom-dropdown-menu">
                            ${dropdownItems}
                        </div>
                    </div>
                    <div class="unified-divider" style="height: 16px;"></div>
                    <div style="flex: 1; display: flex; align-items: center; min-width: 0;">
                        <input type="text" class="extra-search-input" value="${defaultVal}" onkeydown="if(event.key === 'Enter'){ event.preventDefault(); filterTable(); }" placeholder="Ketik filter tambahan..." style="width: 100%; font-size: 0.78rem; font-weight: 500; background: transparent; border: none; outline: none; color: #1e293b;">
                    </div>
                </div>
                <button type="button" onclick="removeFilterRow('${rowId}')" class="btn-remove-row" title="Hapus kriteria ini">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                </button>
            `;

            container.appendChild(rowHtml);
            updateFilterCountBadge();
        }

        function selectExtraCategory(rowId, catKey, catLabel, el) {
            const row = document.getElementById(rowId);
            if (!row) return;
            row.querySelector('.extra-category-val').value = catKey;
            row.querySelector('.extra-category-label').innerText = catLabel.replace(/^[^\s]+\s*/, '');
            if (el) {
                el.parentElement.querySelectorAll('.dropdown-item').forEach(d => d.classList.remove('active'));
                el.classList.add('active');
            }
            closeAllCustomDropdowns();
        }

        function removeFilterRow(rowId) {
            const row = document.getElementById(rowId);
            if (row) {
                row.remove();
                updateFilterCountBadge();
                filterTable();
            }
        }

        function updateFilterCountBadge() {
            const container = document.getElementById('additionalFilterRowsContainer');
            const count = 1 + (container ? container.children.length : 0);
            const badge = document.getElementById('filterCountBadge');
            if (badge) badge.innerText = count + '/4';
        }

        function resetMultiSearch() {
            document.getElementById('mainSearchInput').value = '';
            if (fpRange) fpRange.clear();
            const btnClear = document.getElementById('btnClearDateRange');
            if (btnClear) btnClear.style.display = 'none';
            selectStatusFilter('', 'Semua Status', '#94a3b8', null);
            selectMainCategory('query', '🔍 Kata Kunci (Semua)', null);
            const container = document.getElementById('additionalFilterRowsContainer');
            if (container) container.innerHTML = '';
            updateFilterCountBadge();
            const card = document.getElementById('extraRowsCard');
            if (card) card.style.display = 'none';
            const btn2 = document.getElementById('standaloneAddBtn');
            if (btn2) btn2.classList.remove('active');
            filterTable();
        }

        function getActiveFilters() {
            const filters = [];
            const mainKey = document.getElementById('mainCategoryVal').value || 'query';

            if (mainKey === 'tanggal') {
                const dates = fpRange ? fpRange.selectedDates : [];
                const fromVal = dates[0] || null;
                const toVal = dates[1] || dates[0] || null;
                if (fromVal) {
                    filters.push({ key: 'tanggal_range', from: fromVal, to: toVal });
                }
            } else if (mainKey === 'status') {
                const statusVal = (document.getElementById('statusDropdownVal').value || '').trim();
                if (statusVal) {
                    filters.push({ key: 'status_category', val: statusVal });
                }
            } else {
                const mainVal = (document.getElementById('mainSearchInput').value || '').toLowerCase().trim();
                if (mainVal) {
                    filters.push({ key: mainKey, val: mainVal });
                }
            }

            document.querySelectorAll('#additionalFilterRowsContainer .extra-filter-row').forEach(row => {
                const key = row.querySelector('.extra-category-val').value;
                const val = (row.querySelector('.extra-search-input').value || '').toLowerCase().trim();
                if (val) {
                    filters.push({ key: key, val: val });
                }
            });

            return filters;
        }

        // ==========================================
        // FILTER & PAGINATION ENGINE
        // ==========================================
        function setFilterStatus(status) {
            currentFilterStatus = status;

            document.querySelectorAll('.filter-pill').forEach(btn => {
                btn.classList.toggle('active', btn.getAttribute('data-status') === status);
            });

            currentPage = 1;
            filterTable();
        }

        function filterTable() {
            const filters = getActiveFilters();
            const allRows = Array.from(document.querySelectorAll('.booking-row'));
            
            // 1. Filter matching rows
            const matchingRows = allRows.filter(row => {
                const rowStatusCat = row.getAttribute('data-status-category') || '';
                const matchesStatusPill = (currentFilterStatus === 'all') || (rowStatusCat === currentFilterStatus);
                if (!matchesStatusPill) return false;

                if (filters.length === 0) return true;

                return filters.every(f => {
                    if (f.key === 'tanggal_range') {
                        const rowFrom = row.getAttribute('data-tanggal-mulai') || '';
                        const rowTo = row.getAttribute('data-tanggal-selesai') || rowFrom;
                        if (!rowFrom) return false;
                        const dFrom = new Date(rowFrom);
                        const dTo = new Date(rowTo);
                        if (f.from && dTo < f.from) return false;
                        if (f.to && dFrom > f.to) return false;
                        return true;
                    } else if (f.key === 'status_category') {
                        return (row.getAttribute('data-status-category') || '') === f.val;
                    } else if (f.key === 'query') {
                        const fieldText = row.getAttribute('data-search') || '';
                        return fieldText.includes(f.val);
                    } else {
                        const fieldText = row.getAttribute('data-' + f.key) || row.getAttribute('data-search') || '';
                        return fieldText.includes(f.val);
                    }
                });
            });


            const totalItems = matchingRows.length;
            const totalPages = Math.ceil(totalItems / pageSize) || 1;
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const startIndex = (currentPage - 1) * pageSize;
            const endIndex = startIndex + pageSize;

            // 2. Show/Hide DOM elements based on page slice
            allRows.forEach(row => {
                row.style.display = 'none';
            });

            matchingRows.slice(startIndex, endIndex).forEach(row => {
                row.style.display = '';
            });

            // 3. Update Toolbar and Pagination Info Counters
            const toolbarCount = document.getElementById('toolbarTotalCount');
            if (toolbarCount) toolbarCount.innerText = totalItems;

            const pageInfoStart = document.getElementById('pageInfoStart');
            const pageInfoEnd = document.getElementById('pageInfoEnd');
            const pageInfoTotal = document.getElementById('pageInfoTotal');

            if (pageInfoStart) pageInfoStart.innerText = totalItems > 0 ? (startIndex + 1) : 0;
            if (pageInfoEnd) pageInfoEnd.innerText = Math.min(endIndex, totalItems);
            if (pageInfoTotal) pageInfoTotal.innerText = totalItems;

            renderPaginationControls(totalPages);
            updateBatchBar();
        }

        function renderPaginationControls(totalPages) {
            const container = document.getElementById('paginationControls');
            if (!container) return;

            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let pages = [];
            if (totalPages <= 7) {
                for (let i = 1; i <= totalPages; i++) pages.push(i);
            } else {
                if (currentPage <= 4) {
                    pages = [1, 2, 3, 4, 5, '...', totalPages];
                } else if (currentPage >= totalPages - 3) {
                    pages = [1, '...', totalPages - 4, totalPages - 3, totalPages - 2, totalPages - 1, totalPages];
                } else {
                    pages = [1, '...', currentPage - 1, currentPage, currentPage + 1, '...', totalPages];
                }
            }

            let html = `
                <button type="button" onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} class="page-btn" title="Halaman Sebelumnya">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </button>
            `;

            pages.forEach(p => {
                if (p === '...') {
                    html += `<span style="padding: 0 4px; font-weight: bold; color: #94a3b8;">...</span>`;
                } else if (p === currentPage) {
                    html += `<button type="button" class="page-btn active">${p}</button>`;
                } else {
                    html += `<button type="button" onclick="goToPage(${p})" class="page-btn">${p}</button>`;
                }
            });

            html += `
                <button type="button" onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} class="page-btn" title="Halaman Selanjutnya">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
            `;

            container.innerHTML = html;
        }

        function goToPage(p) {
            currentPage = p;
            filterTable();
        }

        function changePageSize(sz) {
            pageSize = parseInt(sz) || 10;
            currentPage = 1;
            filterTable();
        }

        // Initialize table on load
        $(document).ready(function() {
            filterTable();
        });

        // Backward compatibility
        function filterTableRows() {
            filterTable();
        }

        // ==========================================
        // MULTIPLE SELECT (BATCH ACTION) LOGIC
        // ==========================================
        function toggleSelectAll(masterCb) {
            const visibleRows = document.querySelectorAll('.booking-row:not([style*="display: none"]) .row-checkbox');
            visibleRows.forEach(cb => {
                cb.checked = masterCb.checked;
            });
            updateBatchBar();
        }

        function deselectAll() {
            const masterCb = document.getElementById('selectAllCheckbox');
            if (masterCb) masterCb.checked = false;

            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.checked = false;
            });
            updateBatchBar();
        }

        function getSelectedIds() {
            const selected = [];
            document.querySelectorAll('.row-checkbox:checked').forEach(cb => {
                selected.push(cb.getAttribute('data-id'));
            });
            return selected;
        }

        function updateBatchBar() {
            const selectedIds = getSelectedIds();
            const bar = document.getElementById('floatingBatchBar');
            const countBadge = document.getElementById('selectedCountBadge');

            if (selectedIds.length > 0) {
                if (countBadge) countBadge.innerText = selectedIds.length;
                if (bar) bar.classList.add('show');
            } else {
                if (bar) bar.classList.remove('show');
                const masterCb = document.getElementById('selectAllCheckbox');
                if (masterCb) masterCb.checked = false;
            }
        }

        // ==========================================
        // SINGLE APPROVE & REJECT ACTIONS
        // ==========================================
        function singleApprove(id) {
            closeAllActionDropdowns();
            Swal.fire({
                title: 'Setujui Peminjaman?',
                text: 'Peminjaman ini akan disetujui atas nama Laboran.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Batal'
            }).then((res) => {
                if (res.isConfirmed) {
                    $.post(BASE_URL + 'laboran/approve/' + id, function(resp) {
                        if (resp.status === 'success') {
                            Swal.fire({ 
                                title: 'Disetujui!', 
                                text: resp.message, 
                                icon: 'success', 
                                showCancelButton: true,
                                confirmButtonColor: '#16a34a',
                                cancelButtonColor: '#64748b',
                                confirmButtonText: '<i class="fa-solid fa-qrcode"></i> Cetak Surat QR',
                                cancelButtonText: 'Tutup'
                            }).then((r) => {
                                if (r.isConfirmed) {
                                    openSuratModal(id);
                                } else {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire('Gagal', resp.message, 'error');
                        }
                    }, 'json').fail(() => Swal.fire('Error', 'Terjadi kesalahan pada server', 'error'));
                }
            });
        }

        // ==========================================
        // SURAT POPUP MODAL & PRINT FUNCTIONS
        // ==========================================
        function openSuratModal(id) {
            closeAllActionDropdowns();
            const url = BASE_URL + 'laboran/surat/' + id;
            const iframe = document.getElementById('suratIframe');
            const loader = document.getElementById('suratLoader');
            const newTabBtn = document.getElementById('suratNewTabBtn');

            if (loader) loader.style.display = 'flex';
            if (newTabBtn) newTabBtn.href = url;
            if (iframe) iframe.src = url;

            const modal = document.getElementById('suratModal');
            if (modal) modal.classList.add('active');
        }

        function closeSuratModal() {
            const modal = document.getElementById('suratModal');
            if (modal) modal.classList.remove('active');
            const iframe = document.getElementById('suratIframe');
            if (iframe) iframe.src = 'about:blank';
        }

        function hideSuratLoader() {
            const loader = document.getElementById('suratLoader');
            if (loader) loader.style.display = 'none';
        }

        function printSuratIframe() {
            const iframe = document.getElementById('suratIframe');
            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            }
        }

        function openSingleRejectModal(id) {
            document.getElementById('rejectTargetIds').value = JSON.stringify([id]);
            document.getElementById('rejectIsBatch').value = '0';
            document.getElementById('alasanPenolakanTextarea').value = '';
            document.getElementById('rejectModal').classList.add('active');
        }

        // ==========================================
        // BATCH APPROVE & REJECT ACTIONS
        // ==========================================
        function submitBatchApprove() {
            const ids = getSelectedIds();
            if (ids.length === 0) return;

            Swal.fire({
                title: `ACC ${ids.length} Data Sekaligus?`,
                text: `Semua ${ids.length} peminjaman terpilih akan disetujui (Disetujui Laboran).`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, ACC Semua',
                cancelButtonText: 'Batal'
            }).then((res) => {
                if (res.isConfirmed) {
                    $.post(BASE_URL + 'laboran/batch_approve', { ids: ids }, function(resp) {
                        if (resp.status === 'success') {
                            Swal.fire({ title: 'Berhasil!', text: resp.message, icon: 'success', confirmButtonColor: '#16a34a' })
                            .then(() => location.reload());
                        } else {
                            Swal.fire('Gagal', resp.message, 'error');
                        }
                    }, 'json').fail(() => Swal.fire('Error', 'Terjadi kesalahan pada server', 'error'));
                }
            });
        }

        function openBatchRejectModal() {
            const ids = getSelectedIds();
            if (ids.length === 0) return;

            document.getElementById('rejectTargetIds').value = JSON.stringify(ids);
            document.getElementById('rejectIsBatch').value = '1';
            document.getElementById('alasanPenolakanTextarea').value = '';
            document.getElementById('rejectModal').classList.add('active');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.remove('active');
        }

        function submitRejectConfirm() {
            const ids = JSON.parse(document.getElementById('rejectTargetIds').value || '[]');
            const alasan = document.getElementById('alasanPenolakanTextarea').value.trim();
            const isBatch = document.getElementById('rejectIsBatch').value === '1';

            if (!alasan) {
                Swal.fire({
                    title: 'Catatan Penolakan Wajib',
                    text: 'Harap tuliskan alasan penolakan pada kolom yang tersedia.',
                    icon: 'warning',
                    confirmButtonColor: '#ea580c'
                });
                return;
            }

            if (isBatch) {
                $.post(BASE_URL + 'laboran/batch_reject', { ids: ids, alasan_penolakan: alasan }, function(resp) {
                    if (resp.status === 'success') {
                        closeRejectModal();
                        Swal.fire({ title: 'Ditolak!', text: resp.message, icon: 'success', confirmButtonColor: '#dc2626' })
                        .then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', resp.message, 'error');
                    }
                }, 'json').fail(() => Swal.fire('Error', 'Terjadi kesalahan pada server', 'error'));
            } else {
                const singleId = ids[0];
                $.post(BASE_URL + 'laboran/reject/' + singleId, { alasan_penolakan: alasan }, function(resp) {
                    if (resp.status === 'success') {
                        closeRejectModal();
                        Swal.fire({ title: 'Ditolak!', text: resp.message, icon: 'success', confirmButtonColor: '#dc2626' })
                        .then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', resp.message, 'error');
                    }
                }, 'json').fail(() => Swal.fire('Error', 'Terjadi kesalahan pada server', 'error'));
            }
        }

        // ==========================================
        // DETAIL MODAL
        // ==========================================
        let currentDetailBookingId = null;
        function openDetailModal(data) {
            currentDetailBookingId = data.id;
            document.getElementById('dtlNama').innerText = data.nama_lengkap || '-';
            document.getElementById('dtlRuangan').innerText = (data.kode_ruangan ? data.kode_ruangan + ' - ' : '') + (data.nama_ruangan || '-');
            
            const tgl = (data.tanggal_mulai === data.tanggal_selesai) 
                ? data.tanggal_mulai 
                : `${data.tanggal_mulai} s/d ${data.tanggal_selesai}`;
            document.getElementById('dtlTanggal').innerText = tgl;
            document.getElementById('dtlWaktu').innerText = `${(data.jam_mulai || '').substring(0,5)} - ${(data.jam_selesai || '').substring(0,5)}`;
            document.getElementById('dtlKeterangan').innerText = data.keterangan || '-';
            document.getElementById('dtlStatus').innerText = data.status || '-';

            const alasanRow = document.getElementById('dtlAlasanRow');
            if (data.status === 'Ditolak' && data.alasan_penolakan) {
                document.getElementById('dtlAlasan').innerText = data.alasan_penolakan;
                alasanRow.style.display = 'flex';
            } else {
                alasanRow.style.display = 'none';
            }

            const suratBtn = document.getElementById('dtlSuratBtn');
            const st = data.status || '';
            if (st.includes('Laboran') || st.includes('Ka. Ur') || st.includes('Kaur') || st.includes('Admin')) {
                suratBtn.style.display = 'inline-flex';
            } else {
                suratBtn.style.display = 'none';
            }

            document.getElementById('detailModal').classList.add('active');
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.remove('active');
        }

        function openSuratModalFromDetail() {
            if (currentDetailBookingId) {
                closeDetailModal();
                openSuratModal(currentDetailBookingId);
            }
        }

        // ==========================================
        // DELETE BOOKING
        // ==========================================
        function deleteBooking(id) {
            Swal.fire({
                title: 'Hapus Data Peminjaman?',
                text: 'Data yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((res) => {
                if (res.isConfirmed) {
                    $.post(BASE_URL + 'laboran/delete/' + id, function(resp) {
                        if (resp.status === 'success') {
                            Swal.fire({ title: 'Terhapus!', text: resp.message, icon: 'success', confirmButtonColor: '#ea580c' })
                            .then(() => location.reload());
                        } else {
                            Swal.fire('Gagal', resp.message, 'error');
                        }
                    }, 'json').fail(() => Swal.fire('Error', 'Terjadi kesalahan pada server', 'error'));
                }
            });
        }

        // ==========================================
        // CREATE BOOKING MODAL LOGIC
        // ==========================================
        function openAdminBookingModal() {
            document.getElementById('bookingModal').classList.add('active');
        }

        function closeAdminBookingModal() {
            document.getElementById('bookingModal').classList.remove('active');
        }

        // Dependent Dropdown
        $('#kategoriSelect').change(function(){
            var id_kategori = $(this).val();
            if(id_kategori != ''){
                $.ajax({
                    url: BASE_URL + 'laboran/get_ruangan',
                    method: 'POST',
                    data: {id_kategori: id_kategori},
                    dataType: 'json',
                    success: function(data){
                        var html = '<option value="">Pilih Ruangan</option>';
                        for(var i=0; i<data.length; i++){
                            if (data[i].kode_ruangan && data[i].kode_ruangan.indexOf(',') > -1) {
                                var codes = data[i].kode_ruangan.split(',');
                                for(var c=0; c<codes.length; c++){
                                    var codeTrim = codes[c].trim();
                                    if(codeTrim) {
                                        html += '<option value="'+data[i].id+'">'+codeTrim+' - '+data[i].nama_ruangan+'</option>';
                                    }
                                }
                            } else {
                                html += '<option value="'+data[i].id+'">'+(data[i].kode_ruangan ? data[i].kode_ruangan + ' - ' : '')+data[i].nama_ruangan+'</option>';
                            }
                        }
                        $('#ruanganSelect').html(html);
                    }
                });
            } else {
                $('#ruanganSelect').html('<option value="">Pilih Ruangan</option>');
            }
        });

        // Flatpickr setup
        flatpickr("#tanggalPeminjaman", {
            mode: "range",
            dateFormat: "Y-m-d",
            minDate: "today",
            onChange: function(selectedDates) {
                const group = document.getElementById('timeSelectionGroup');
                if (selectedDates.length === 1 || selectedDates.length === 2) {
                    group.style.display = 'block';
                } else {
                    group.style.display = 'none';
                }
            }
        });

        // Timepicker Integration
        let currentTarget = '';
        function openInlinePicker(target) {
            currentTarget = target;
            document.getElementById('inlineClockPanel').style.display = 'block';
        }

        function closeInlinePicker() {
            document.getElementById('inlineClockPanel').style.display = 'none';
        }

        function applyInlinePicker() {
            const h = document.getElementById('tpDisplayHour').innerText;
            const m = document.getElementById('tpDisplayMinute').innerText;
            const val = `${h}:${m}`;
            if (currentTarget === 'mulai') {
                document.getElementById('inputJamMulai').value = val;
            } else {
                document.getElementById('inputJamSelesai').value = val;
            }
            closeInlinePicker();
        }

        $(document).on('click', '.tp-slot', function() {
            const start = $(this).data('start');
            const end = $(this).data('end');
            $('#inputJamMulai').val(start);
            $('#inputJamSelesai').val(end);
            closeInlinePicker();
        });

        function submitForm() {
            $.ajax({
                url: BASE_URL + 'laboran/submit_booking',
                method: 'POST',
                data: $('#formBooking').serialize(),
                dataType: 'json',
                success: function(resp) {
                    if (resp.status === 'success') {
                        closeAdminBookingModal();
                        Swal.fire({ title: 'Berhasil!', text: resp.message, icon: 'success', confirmButtonColor: '#16a34a' })
                        .then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', resp.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                }
            });
        }

        // ==========================================
        // 3-DOTS ACTION DROPDOWN LOGIC
        // ==========================================
        function toggleActionDropdown(id, e) {
            if (e) e.stopPropagation();
            const menu = document.getElementById('actionMenu_' + id);
            const isOpen = menu && menu.classList.contains('show');
            closeAllActionDropdowns();
            if (!isOpen && menu) {
                menu.classList.add('show');
                const btn = menu.previousElementSibling;
                if (btn) btn.classList.add('active');
            }
        }

        function closeAllActionDropdowns() {
            document.querySelectorAll('.action-dropdown-menu').forEach(m => m.classList.remove('show'));
            document.querySelectorAll('.btn-action-dots').forEach(b => b.classList.remove('active'));
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.action-dropdown-wrap')) {
                closeAllActionDropdowns();
            }
        });
    </script>
</body>
</html>