<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IFIK Newsroom — Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Quill.js WYSIWYG Editor -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <style>
        :root {
            --bg:      #fbf7f1;
            --orange:  #ea580c;
            --orange2: #c2410c;
            --dark:    #1e293b;
            --muted:   #64748b;
            --card-bg: #ffffff;
            --border:  rgba(234,88,12,0.15);
            --shadow:  0 4px 20px rgba(0,0,0,0.07);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--dark);
            min-height: 100vh;
        }

        /* ─── TOPBAR ─── */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 100;
            height: 64px;
            background: rgba(251,247,241,0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 32px;
            gap: 16px;
        }
        .topbar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 900;
            font-size: 1.1rem;
            color: var(--dark);
            text-decoration: none;
        }
        .topbar-logo .icon-badge {
            width: 34px;
            height: 34px;
            background: var(--orange);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }
        .topbar-sep { width: 1px; height: 24px; background: var(--border); }
        .topbar-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--orange);
            letter-spacing: 0.5px;
        }
        .topbar-back {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--muted);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 50px;
            border: 1.5px solid var(--border);
            transition: all 0.2s ease;
        }
        .topbar-back:hover { border-color: var(--orange); color: var(--orange); }

        /* ─── LAYOUT ─── */
        .newsroom-layout {
            display: flex;
            align-items: flex-start;
            gap: 0px;
            padding: 28px 32px;
            max-width: 1500px;
            margin: 0 auto;
            transition: gap 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }
        .newsroom-layout.editor-open {
            gap: 28px;
        }

        /* ─── PANEL KIRI: Daftar Berita ─── */
        .panel-list {
            flex: 1 1 0%;
            min-width: 0;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 16px;
            transition: flex 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .panel-title {
            font-size: 1.5rem;
            font-weight: 900;
            color: var(--dark);
        }
        .panel-subtitle { font-size: 0.85rem; color: var(--muted); margin-top: 2px; }

        .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--orange);
            color: #fff;
            font-weight: 800;
            font-size: 0.9rem;
            padding: 10px 20px;
            border-radius: 50px;
            border: none;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(234,88,12,0.35);
        }
        .btn-add:hover {
            background: var(--orange2);
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(234,88,12,0.4);
        }

        /* ─── SEARCH BAR & AUTOCOMPLETE ─── */
        .list-search {
            position: relative;
        }
        .list-search-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }
        .list-search input {
            width: 100%;
            padding: 11px 40px 11px 42px;
            border: 2px solid var(--border);
            border-radius: 16px;
            background: #fff;
            font-family: inherit;
            font-size: 0.9rem;
            color: var(--dark);
            outline: none;
            transition: all 0.2s ease;
        }
        .list-search input:focus {
            border-color: var(--orange);
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.1);
        }
        .list-search .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--orange);
            font-size: 1rem;
            pointer-events: none;
        }
        .list-search .search-clear-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #e2e8f0;
            color: #64748b;
            border: none;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            transition: all 0.2s ease;
        }
        .list-search .search-clear-btn:hover {
            background: #cbd5e1;
            color: #1e293b;
        }

        /* Autocomplete Dropdown */
        .search-autocomplete {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid rgba(234, 88, 12, 0.18);
            box-shadow: 0 12px 36px rgba(0,0,0,0.12);
            overflow: hidden;
            z-index: 50;
            display: none;
            flex-direction: column;
            max-height: 320px;
            overflow-y: auto;
        }
        .search-autocomplete.open {
            display: flex;
        }
        .search-autocomplete-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 16px;
            cursor: pointer;
            transition: background 0.15s ease;
            border-bottom: 1px solid #f8fafc;
        }
        .search-autocomplete-item:last-child {
            border-bottom: none;
        }
        .search-autocomplete-item:hover,
        .search-autocomplete-item.active {
            background: #fff7ed;
        }
        .search-autocomplete-left {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }
        .search-autocomplete-thumb {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0;
            background: #fed7aa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }
        .search-autocomplete-title {
            font-size: 0.86rem;
            font-weight: 700;
            color: var(--dark);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .search-autocomplete-title mark {
            background: rgba(234, 88, 12, 0.18);
            color: var(--orange);
            font-weight: 800;
            padding: 0 2px;
            border-radius: 4px;
        }
        .search-autocomplete-badge {
            font-size: 0.68rem;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .search-autocomplete-no-match {
            padding: 16px;
            text-align: center;
            font-size: 0.85rem;
            color: var(--muted);
        }

        /* ─── TABEL BERITA ─── */
        .news-table-wrap {
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
        }
        .news-table {
            width: 100%;
            border-collapse: collapse;
        }
        .news-table th {
            background: #fff8f3;
            padding: 14px 16px;
            text-align: left;
            font-size: 0.78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--orange);
            border-bottom: 1px solid var(--border);
            cursor: pointer;
            user-select: none;
            transition: background 0.2s;
            white-space: nowrap;
        }
        .news-table th:hover { background: #fff0e6; }
        .news-table th .sort-icon { margin-left: 4px; opacity: 0.5; }
        .news-table th.sorted .sort-icon { opacity: 1; color: var(--orange); }

        .news-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.88rem;
            vertical-align: middle;
        }
        .news-table tr:last-child td { border-bottom: none; }
        .news-table tr:hover td { background: #fef6f0; }

        /* Foto thumbnail */
        .tbl-thumb {
            width: 52px;
            height: 40px;
            border-radius: 10px;
            object-fit: cover;
            background: #e2e8f0;
            display: block;
            border: 1px solid rgba(234,88,12,0.15);
        }
        .tbl-thumb-placeholder {
            width: 52px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #fff7ed, #ffedd5);
            border: 1.5px dashed rgba(234, 88, 12, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ea580c;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.03);
            transition: all 0.2s ease;
        }
        .tbl-thumb-placeholder:hover {
            border-color: #ea580c;
            background: #ffedd5;
        }

        .tbl-title {
            font-weight: 700;
            color: var(--dark);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            max-width: 320px;
            line-height: 1.4;
        }
        .tbl-date { color: var(--muted); font-size: 0.82rem; white-space: nowrap; }

        /* Kategori badge */
        .kategori-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 114px;
            height: 26px;
            box-sizing: border-box;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            white-space: nowrap;
            text-align: center;
        }
        .cat-BeritaAcara    { background: #fff7ed; color: #ea580c; border: 1px solid rgba(234, 88, 12, 0.15); }
        .cat-Workshop       { background: #f0fdf4; color: #16a34a; border: 1px solid rgba(22, 163, 74, 0.15); }
        .cat-Prestasi       { background: #fdf4ff; color: #9333ea; border: 1px solid rgba(147, 51, 234, 0.15); }
        .cat-Seminar        { background: #eff6ff; color: #2563eb; border: 1px solid rgba(37, 99, 235, 0.15); }
        .cat-Pengumuman     { background: #fefce8; color: #ca8a04; border: 1px solid rgba(202, 138, 4, 0.15); }
        .cat-Kegiatan       { background: #fff0fb; color: #db2777; border: 1px solid rgba(219, 39, 119, 0.15); }
        .cat-Kerjasama      { background: #f0fdfa; color: #0d9488; border: 1px solid rgba(13, 148, 136, 0.15); }
        .cat-Fasilitas      { background: #f0f9ff; color: #0284c7; border: 1px solid rgba(2, 132, 199, 0.15); }
        .cat-default        { background: #f1f5f9; color: #64748b; border: 1px solid rgba(100, 116, 139, 0.15); }

        /* Published toggle */
        .pub-toggle {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
            cursor: pointer;
            flex-shrink: 0;
        }
        .pub-toggle input { display: none; }
        .pub-toggle-slider {
            position: absolute;
            inset: 0;
            background: #e2e8f0;
            border-radius: 50px;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .pub-toggle-slider::before {
            content: '';
            position: absolute;
            width: 18px;
            height: 18px;
            left: 3px;
            top: 3px;
            background: #fff;
            border-radius: 50%;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .pub-toggle input:checked + .pub-toggle-slider { background: var(--orange); }
        .pub-toggle input:checked + .pub-toggle-slider::before { transform: translateX(20px); }

        /* Action buttons */
        .tbl-actions { display: flex; align-items: center; gap: 8px; }
        .btn-edit, .btn-del {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            border: 1.5px solid;
            background: transparent;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }
        .btn-edit { border-color: rgba(234,88,12,0.3); color: var(--orange); }
        .btn-edit:hover { background: var(--orange); color: #fff; border-color: var(--orange); }
        .btn-del { border-color: rgba(239,68,68,0.3); color: #ef4444; }
        .btn-del:hover { background: #ef4444; color: #fff; border-color: #ef4444; }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--muted);
        }
        .empty-state .empty-icon { font-size: 3rem; margin-bottom: 12px; }
        .empty-state p { font-size: 0.95rem; }

        /* ─── PANEL KANAN: Editor ─── */
        .panel-editor {
            flex: 0 0 0px;
            width: 0;
            max-width: 0;
            max-height: 0;
            opacity: 0;
            transform: translateX(40px);
            overflow: hidden;
            pointer-events: none;
            visibility: hidden;
            transition: flex-basis 0.45s cubic-bezier(0.16, 1, 0.3, 1),
                        max-width 0.45s cubic-bezier(0.16, 1, 0.3, 1),
                        width 0.45s cubic-bezier(0.16, 1, 0.3, 1),
                        max-height 0.45s cubic-bezier(0.16, 1, 0.3, 1),
                        opacity 0.35s ease,
                        visibility 0.45s ease,
                        transform 0.45s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .newsroom-layout.editor-open .panel-editor {
            flex: 0 0 440px;
            width: 440px;
            max-width: 440px;
            max-height: 5000px;
            opacity: 1;
            transform: translateX(0);
            pointer-events: auto;
            visibility: visible;
            overflow: visible;
        }
        .editor-inner {
            width: 440px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .editor-card {
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            padding: 24px;
        }
        .editor-card-title {
            font-size: 1rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .editor-card-title .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--orange);
        }

        /* ─── DRAG & DROP FOTO ─── */
        .photo-drop-area {
            border: 2px dashed rgba(234,88,12,0.4);
            border-radius: 16px;
            padding: 0;
            cursor: pointer;
            transition: all 0.25s ease;
            overflow: hidden;
            position: relative;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff8f3;
            margin-bottom: 20px;
        }
        .photo-drop-area:hover, .photo-drop-area.drag-over {
            border-color: var(--orange);
            background: #fff0e6;
        }
        .photo-drop-area.has-image {
            border-style: solid;
            border-color: var(--orange);
        }
        .photo-drop-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            pointer-events: none;
        }
        .photo-drop-placeholder .drop-icon { font-size: 2.5rem; }
        .photo-drop-placeholder p { font-size: 0.85rem; color: var(--muted); font-weight: 600; }
        .photo-drop-placeholder span { font-size: 0.75rem; color: #94a3b8; }
        #photoPreview {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }
        .photo-remove-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(0,0,0,0.5);
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 5;
            transition: background 0.2s;
        }
        .photo-remove-btn:hover { background: #ef4444; }
        #photoInput { display: none; }

        /* ─── FORM FIELDS ─── */
        .form-group { margin-bottom: 16px; }
        .form-group label {
            display: block;
            font-size: 0.78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--muted);
            margin-bottom: 6px;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 11px 14px;
            border: 2px solid var(--border);
            border-radius: 12px;
            background: #fff;
            font-family: inherit;
            font-size: 0.9rem;
            color: var(--dark);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--orange);
            box-shadow: 0 0 0 3px rgba(234,88,12,0.08);
        }
        .form-group textarea { resize: vertical; min-height: 80px; line-height: 1.6; }
        .form-group textarea.konten-area { min-height: 140px; font-size: 0.85rem; }

        /* ─── QUILL EDITOR STYLING ─── */
        .ql-toolbar.ql-snow {
            border: 2px solid var(--border) !important;
            border-bottom: 1px solid var(--border) !important;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            background: #fff8f5;
            font-family: inherit;
        }
        .ql-container.ql-snow {
            border: 2px solid var(--border) !important;
            border-top: none !important;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
            background: #fff;
            font-family: inherit;
            font-size: 0.95rem;
            min-height: 200px;
        }
        .ql-editor {
            min-height: 200px;
            line-height: 1.7;
            color: var(--dark);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .ql-editor.ql-blank::before {
            color: #94a3b8;
            font-style: normal;
            font-size: 0.88rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .ql-snow .ql-stroke { stroke: #64748b; }
        .ql-snow .ql-fill { fill: #64748b; }
        .ql-snow .ql-picker { color: #64748b; font-weight: 600; }
        .ql-snow.ql-toolbar button:hover .ql-stroke,
        .ql-snow.ql-toolbar button.ql-active .ql-stroke {
            stroke: var(--orange);
        }
        .ql-snow.ql-toolbar button:hover .ql-fill,
        .ql-snow.ql-toolbar button.ql-active .ql-fill {
            fill: var(--orange);
        }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

        /* Karakter counter untuk excerpt */
        .field-footer {
            display: flex;
            justify-content: flex-end;
            margin-top: 4px;
        }
        .char-counter { font-size: 0.75rem; color: #94a3b8; font-weight: 600; }
        .char-counter.warn { color: #f59e0b; }
        .char-counter.danger { color: #ef4444; }

        /* Publish toggle dalam form */
        .pub-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 14px;
            border: 2px solid var(--border);
            border-radius: 12px;
            background: #fff;
            margin-bottom: 16px;
        }
        .pub-row-label { font-size: 0.88rem; font-weight: 700; color: var(--dark); }
        .pub-row-desc { font-size: 0.75rem; color: var(--muted); }

        /* Tombol aksi form */
        .form-actions {
            display: flex;
            gap: 10px;
        }
        .btn-save {
            flex: 1;
            padding: 13px;
            background: var(--orange);
            color: #fff;
            font-weight: 800;
            font-size: 0.95rem;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(234,88,12,0.3);
        }
        .btn-save:hover { background: var(--orange2); transform: translateY(-1px); }
        .btn-save:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
        .btn-cancel {
            padding: 13px 20px;
            background: transparent;
            color: var(--muted);
            font-weight: 700;
            font-size: 0.95rem;
            border: 2px solid var(--border);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-cancel:hover { border-color: var(--orange); color: var(--orange); }

        /* ─── LIVE PREVIEW KARTU ─── */
        .preview-card {
            width: 100%;
            max-width: 280px;
            height: 320px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 12px 40px rgba(0,0,0,0.12);
            overflow: hidden;
            margin: 0 auto;
            transition: transform 0.3s ease;
            cursor: default;
        }
        .preview-card:hover { transform: translateY(-4px); }
        .preview-img {
            width: 100%;
            height: 45%;
            background: linear-gradient(135deg, #fed7aa, #fb923c);
            background-size: cover;
            background-position: center 25%;
            position: relative;
        }
        .preview-img::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 24px;
            background: linear-gradient(to top, #fff, transparent);
        }
        .preview-content {
            padding: 16px 18px;
        }
        .preview-date {
            font-size: 0.7rem;
            color: var(--orange);
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .preview-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: var(--dark);
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 8px;
        }
        .preview-excerpt {
            font-size: 0.78rem;
            color: var(--muted);
            line-height: 1.55;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* ─── PILIHAN BINGKAI KARTU ─── */
        .frame-selector-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 10px;
            margin-top: 6px;
        }
        .frame-opt {
            cursor: pointer;
            position: relative;
        }
        .frame-opt input[type="radio"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
        .frame-opt-card {
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 10px 8px;
            background: #fff;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            transition: all 0.2s ease;
        }
        .frame-opt:hover .frame-opt-card {
            border-color: rgba(234, 88, 12, 0.4);
            transform: translateY(-2px);
        }
        .frame-opt input[type="radio"]:checked + .frame-opt-card {
            border-color: var(--orange);
            background: #fff8f5;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.15);
        }
        .frame-badge-preview {
            width: 36px;
            height: 24px;
            border-radius: 6px;
            margin-bottom: 2px;
            background: #f1f5f9;
        }
        .frame-prev-none {
            border: 1.5px solid #ea580c;
            background: #fff;
        }
        .frame-prev-swirl {
            border: 4px solid #18181b;
            border-image: url('<?= base_url("assets/images/frame-border-swirl.svg") ?>') 20 round;
            background: #fff;
        }
        .frame-prev-geometric {
            border: 4px solid #18181b;
            border-image: url('<?= base_url("assets/images/frame-border-geometric.svg") ?>') 20 round;
            background: #fff;
        }
        .frame-prev-polaroid {
            border: 3px solid #e2e8f0;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .frame-prev-neon {
            border: 2px solid #ea580c;
            box-shadow: 0 0 8px rgba(234,88,12,0.6);
            background: #fff;
        }
        .frame-opt-title {
            font-size: 0.78rem;
            font-weight: 800;
            color: var(--dark);
        }
        .frame-opt-desc {
            font-size: 0.68rem;
            color: var(--muted);
        }

        /* Preview Card Frame Variations */
        .preview-card.frame-none {
            border: 1.5px solid rgba(234, 88, 12, 0.2);
        }
        .preview-card.frame-swirl {
            position: relative;
        }
        .preview-card.frame-swirl::before {
            content: '';
            position: absolute;
            inset: 0;
            border: 10px solid #18181b;
            border-image: url('<?= base_url("assets/images/frame-border-swirl.svg") ?>') 20 round;
            pointer-events: none;
            z-index: 15;
            border-radius: 20px;
        }
        .preview-card.frame-geometric {
            position: relative;
        }
        .preview-card.frame-geometric::before {
            content: '';
            position: absolute;
            inset: 0;
            border: 10px solid #18181b;
            border-image: url('<?= base_url("assets/images/frame-border-geometric.svg") ?>') 20 round;
            pointer-events: none;
            z-index: 15;
            border-radius: 20px;
        }
        .preview-card.frame-polaroid {
            padding: 8px 8px 12px;
            border: 2px solid #e2e8f0;
        }
        .preview-card.frame-polaroid .preview-img {
            border-radius: 12px;
        }
        .preview-card.frame-neon {
            border: 2px solid #ea580c;
            box-shadow: 0 0 15px rgba(234, 88, 12, 0.5);
        }

        /* ─── TOAST NOTIFIKASI ─── */
        #toast-container {
            position: fixed;
            bottom: 28px;
            right: 28px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }
        .toast {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 20px;
            border-radius: 14px;
            background: #1e293b;
            color: #fff;
            font-size: 0.9rem;
            font-weight: 600;
            box-shadow: 0 8px 30px rgba(0,0,0,0.2);
            animation: toastIn 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            pointer-events: auto;
            min-width: 260px;
        }
        .toast.toast-success { background: #065f46; }
        .toast.toast-error   { background: #7f1d1d; }
        .toast.toast-out     { animation: toastOut 0.3s ease forwards; }
        @keyframes toastIn  { from { opacity:0; transform: translateY(20px) scale(0.95); } to { opacity:1; transform: none; } }
        @keyframes toastOut { from { opacity:1; } to { opacity:0; transform: translateY(10px); } }

        /* ─── LOADING OVERLAY ─── */
        .btn-save .spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 1100px) {
            .newsroom-layout {
                flex-direction: column;
            }
            .newsroom-layout.editor-open {
                gap: 24px;
            }
            .panel-editor {
                width: 100%;
                max-width: 100%;
                transform: translateY(-20px);
                order: -1;
            }
            .newsroom-layout.editor-open .panel-editor {
                flex: 1 1 auto;
                width: 100%;
                max-width: 100%;
                transform: translateY(0);
            }
            .editor-inner {
                width: 100%;
            }
        }
        @media (max-width: 600px) {
            .newsroom-layout { padding: 16px; gap: 16px; }
            .form-row { grid-template-columns: 1fr; }
            .topbar { padding: 0 16px; }
        }
    </style>
</head>
<body>

    <!-- TOPBAR -->
    <header class="topbar">
        <a href="<?= base_url('index.php/news') ?>" class="topbar-logo">
            <span class="icon-badge">📰</span>
            IFIK
        </a>
        <div class="topbar-sep"></div>
        <span class="topbar-title">NEWSROOM</span>
        <a href="<?= base_url('index.php/dashboard') ?>" class="topbar-back">
            ← Kembali ke Dashboard
        </a>
    </header>

    <!-- LAYOUT -->
    <main class="newsroom-layout">

        <!-- ─── PANEL KIRI: Daftar Berita ─── -->
        <section class="panel-list">
            <div class="panel-header">
                <div>
                    <h1 class="panel-title">Semua Berita</h1>
                    <p class="panel-subtitle" id="newsCount">
                        <?= count($all_berita) ?> artikel tersimpan
                    </p>
                </div>
                <button class="btn-add" onclick="openEditor()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Tambah Berita
                </button>
            </div>

            <!-- Search & Autocomplete -->
            <div class="list-search">
                <div class="list-search-input-wrap">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="tableSearch" placeholder="Cari judul atau kategori..." autocomplete="off">
                    <button type="button" class="search-clear-btn" id="searchClearBtn" title="Hapus pencarian">✕</button>
                </div>
                <div class="search-autocomplete" id="searchAutocomplete"></div>
            </div>

            <!-- Tabel -->
            <div class="news-table-wrap">
                <table class="news-table" id="newsTable">
                    <thead>
                        <tr>
                            <th style="width:60px;">Foto</th>
                            <th class="sortable sorted" data-col="1">
                                Judul <span class="sort-icon">↕</span>
                            </th>
                            <th class="sortable" data-col="2" style="width:110px;">
                                Kategori <span class="sort-icon">↕</span>
                            </th>
                            <th class="sortable" data-col="3" style="width:110px;">
                                Tanggal <span class="sort-icon">↕</span>
                            </th>
                            <th style="width:70px;">Publish</th>
                            <th style="width:80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="newsTableBody">
                    <?php if (empty($all_berita)): ?>
                        <tr><td colspan="6">
                            <div class="empty-state">
                                <div class="empty-icon">📭</div>
                                <p>Belum ada berita. Tambahkan yang pertama!</p>
                            </div>
                        </td></tr>
                    <?php else: ?>
                        <?php foreach ($all_berita as $b): ?>
                        <?php
                            $bulan = ['01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'Mei','06'=>'Jun','07'=>'Jul','08'=>'Ags','09'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'];
                            $tgl_parts = explode('-', $b->tanggal);
                            $tgl_fmt = isset($tgl_parts[2]) ? (int)$tgl_parts[2].' '.($bulan[$tgl_parts[1]] ?? '').' '.$tgl_parts[0] : $b->tanggal;
                            $cat_class = 'cat-'.str_replace([' ','/'],'',$b->kategori);
                        ?>
                        <tr data-id="<?= $b->id ?>" data-judul="<?= htmlspecialchars($b->judul) ?>">
                            <td>
                                <?php if ($b->gambar && file_exists(FCPATH.$b->gambar)): ?>
                                    <img class="tbl-thumb" src="<?= base_url($b->gambar) ?>" alt="<?= htmlspecialchars($b->judul) ?>">
                                <?php else: ?>
                                    <div class="tbl-thumb-placeholder" title="Foto belum diunggah">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                          <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                                          <path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="tbl-title"><?= htmlspecialchars($b->judul) ?></div>
                            </td>
                            <td>
                                <span class="kategori-badge <?= $cat_class ?>">
                                    <?= htmlspecialchars($b->kategori) ?>
                                </span>
                            </td>
                            <td class="tbl-date"><?= $tgl_fmt ?></td>
                            <td>
                                <label class="pub-toggle" title="<?= $b->published ? 'Published' : 'Draft' ?>">
                                    <input type="checkbox" <?= $b->published ? 'checked' : '' ?> onchange="togglePublish(<?= $b->id ?>, this)">
                                    <span class="pub-toggle-slider"></span>
                                </label>
                            </td>
                            <td>
                                <div class="tbl-actions">
                                    <button class="btn-edit" title="Edit" onclick='editBerita(<?= json_encode($b) ?>)'>✏️</button>
                                    <button class="btn-del" title="Hapus" onclick="hapusBerita(<?= $b->id ?>, `<?= addslashes(htmlspecialchars($b->judul)) ?>`)">🗑️</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- ─── PANEL KANAN: Editor + Preview ─── -->
        <section class="panel-editor">
            <div class="editor-inner">

            <!-- EDITOR CARD -->
            <div class="editor-card">
                <h2 class="editor-card-title" style="justify-content: space-between;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span class="dot"></span>
                        <span id="editorCardTitle">✍️ Tambah Berita Baru</span>
                    </div>
                    <button type="button" onclick="hidePanel()" title="Tutup" style="
                        width:30px; height:30px; border-radius:50%; border:1.5px solid rgba(234,88,12,0.2);
                        background:transparent; color:#94a3b8; font-size:1rem; cursor:pointer;
                        display:flex; align-items:center; justify-content:center;
                        transition: all 0.2s ease; flex-shrink:0;
                    " onmouseover="this.style.background='#ef4444';this.style.color='#fff';this.style.borderColor='#ef4444';"
                       onmouseout="this.style.background='transparent';this.style.color='#94a3b8';this.style.borderColor='rgba(234,88,12,0.2)';">✕</button>
                </h2>

                <form id="newsForm" enctype="multipart/form-data">
                    <input type="hidden" id="newsId" name="id" value="">

                    <!-- Foto Drop Area -->
                    <div class="photo-drop-area" id="photoDropArea" onclick="document.getElementById('photoInput').click()">
                        <img id="photoPreview" alt="Preview Foto">
                        <button type="button" class="photo-remove-btn" id="photoRemoveBtn" onclick="removePhoto(event)">✕</button>
                        <div class="photo-drop-placeholder" id="photoPlaceholder">
                            <span class="drop-icon">📸</span>
                            <p>Drop foto di sini atau klik untuk upload</p>
                            <span>JPG, PNG, WebP — Maks 10MB</span>
                        </div>
                    </div>
                    <input type="file" id="photoInput" name="gambar" accept="image/jpeg,image/png,image/webp,image/gif">
                    <input type="hidden" id="existingGambar" name="existing_gambar" value="">

                    <!-- Judul -->
                    <div class="form-group">
                        <label>📌 Judul Berita *</label>
                        <input type="text" id="fJudul" name="judul" placeholder="Tulis judul yang menarik..." maxlength="200">
                    </div>

                    <!-- Kategori & Tanggal -->
                    <div class="form-row">
                        <div class="form-group" style="margin-bottom:0">
                            <label>🏷️ Kategori</label>
                            <select id="fKategori" name="kategori">
                                <option value="Berita Acara">Berita Acara</option>
                                <option value="Workshop">Workshop</option>
                                <option value="Prestasi">Prestasi</option>
                                <option value="Seminar">Seminar</option>
                                <option value="Pengumuman">Pengumuman</option>
                                <option value="Kegiatan">Kegiatan</option>
                                <option value="Kerjasama">Kerjasama</option>
                                <option value="Fasilitas">Fasilitas</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:0">
                            <label>📅 Tanggal *</label>
                            <input type="date" id="fTanggal" name="tanggal">
                        </div>
                    </div>

                    <div style="margin-bottom:16px;"></div>

                    <!-- Excerpt -->
                    <div class="form-group">
                        <label>📝 Ringkasan</label>
                        <textarea id="fExcerpt" name="excerpt" placeholder="Tulis ringkasan singkat yang menarik pembaca..." maxlength="300"></textarea>
                        <div class="field-footer">
                            <span class="char-counter" id="excerptCounter">0 / 300</span>
                        </div>
                    </div>

                    <!-- Konten -->
                    <div class="form-group">
                        <label>📄 Konten Artikel</label>
                        <div id="quillEditor"></div>
                        <textarea id="fKonten" name="konten" style="display: none;"></textarea>
                    </div>

                    <!-- Pilihan Bingkai Kartu -->
                    <div class="form-group">
                        <label>🎨 Desain Bingkai Kartu</label>
                        <div class="frame-selector-grid">
                            <label class="frame-opt" title="Tanpa bingkai motif (standar modern)">
                                <input type="radio" name="border_style" value="none" checked onchange="updatePreviewFrame()">
                                <div class="frame-opt-card">
                                    <span class="frame-badge-preview frame-prev-none"></span>
                                    <span class="frame-opt-title">Polos</span>
                                    <span class="frame-opt-desc">Standar</span>
                                </div>
                            </label>
                            <label class="frame-opt" title="Motif doodle spiral artistik">
                                <input type="radio" name="border_style" value="swirl" onchange="updatePreviewFrame()">
                                <div class="frame-opt-card">
                                    <span class="frame-badge-preview frame-prev-swirl"></span>
                                    <span class="frame-opt-title">Spiral Swirl</span>
                                    <span class="frame-opt-desc">Doodle Artistik</span>
                                </div>
                            </label>
                            <label class="frame-opt" title="Motif batik wajik oranye">
                                <input type="radio" name="border_style" value="geometric" onchange="updatePreviewFrame()">
                                <div class="frame-opt-card">
                                    <span class="frame-badge-preview frame-prev-geometric"></span>
                                    <span class="frame-opt-title">Geometrik</span>
                                    <span class="frame-opt-desc">Batik Etnik</span>
                                </div>
                            </label>
                            <label class="frame-opt" title="Frame foto polaroid vintage">
                                <input type="radio" name="border_style" value="polaroid" onchange="updatePreviewFrame()">
                                <div class="frame-opt-card">
                                    <span class="frame-badge-preview frame-prev-polaroid"></span>
                                    <span class="frame-opt-title">Polaroid</span>
                                    <span class="frame-opt-desc">Foto Klasik</span>
                                </div>
                            </label>
                            <label class="frame-opt" title="Glow neon oranye menyala">
                                <input type="radio" name="border_style" value="neon" onchange="updatePreviewFrame()">
                                <div class="frame-opt-card">
                                    <span class="frame-badge-preview frame-prev-neon"></span>
                                    <span class="frame-opt-title">Glow Neon</span>
                                    <span class="frame-opt-desc">Garis Menyala</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Publish Toggle -->
                    <div class="pub-row">
                        <div>
                            <div class="pub-row-label">🌐 Publikasikan</div>
                            <div class="pub-row-desc">Berita akan tampil di dashboard publik</div>
                        </div>
                        <label class="pub-toggle">
                            <input type="checkbox" id="fPublished" name="published" value="1" checked>
                            <span class="pub-toggle-slider"></span>
                        </label>
                    </div>

                    <!-- Actions -->
                    <div class="form-actions">
                        <button type="button" class="btn-save" id="btnSave" onclick="submitForm()">
                            <span class="spinner" id="btnSpinner"></span>
                            <span id="btnSaveText">💾 Simpan Berita</span>
                        </button>
                        <button type="button" class="btn-cancel" onclick="resetForm()">Batal</button>
                    </div>
                </form>
            </div>

            <!-- LIVE PREVIEW CARD -->
            <div class="editor-card" style="padding:20px;">
                <h2 class="editor-card-title" style="margin-bottom:16px;">
                    <span class="dot" style="background:#10b981;"></span>
                    👁️ Live Preview Kartu
                </h2>
                <div class="preview-card">
                    <div class="preview-img" id="previewImg" style="background-image: url('<?= base_url('assets/images/background.png') ?>');"></div>
                    <div class="preview-content">
                        <div class="preview-date" id="previewDate">📅 Pilih tanggal...</div>
                        <div class="preview-title" id="previewTitle">Judul berita akan muncul di sini</div>
                        <div class="preview-excerpt" id="previewExcerpt">Ringkasan berita akan tampil di sini setelah kamu mulai mengetik...</div>
                    </div>
                </div>
            </div>

            </div>
        </section>
    </main>

    <!-- Toast Container -->
    <div id="toast-container"></div>

<script>
const BASE_URL = '<?= base_url() ?>';
const SAVE_URL = '<?= base_url('index.php/news/save') ?>';
const DEL_URL  = '<?= base_url('index.php/news/delete') ?>';
const TOG_URL  = '<?= base_url('index.php/news/toggle') ?>';

// ─── INISIALISASI QUILL RICH TEXT EDITOR ─────────────────────────────────────
const quill = new Quill('#quillEditor', {
    theme: 'snow',
    placeholder: 'Tulis isi artikel di sini (bisa gunakan toolbar untuk tebal, miring, kutipan quote, list, dsb)...',
    modules: {
        toolbar: [
            [{ 'header': [2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['link', 'clean']
        ]
    }
});

// ─── LIVE PREVIEW ─────────────────────────────────────────────────────────────
const bulanFull = {'01':'Januari','02':'Februari','03':'Maret','04':'April','05':'Mei','06':'Juni','07':'Juli','08':'Agustus','09':'September','10':'Oktober','11':'November','12':'Desember'};

function formatTanggal(val) {
    if (!val) return '📅 Pilih tanggal...';
    const p = val.split('-');
    if (p.length === 3) return `${parseInt(p[2])} ${bulanFull[p[1]]||''} ${p[0]}`;
    return val;
}

function updatePreview() {
    const judul   = document.getElementById('fJudul').value.trim()   || 'Judul berita akan muncul di sini';
    const excerpt = document.getElementById('fExcerpt').value.trim() || 'Ringkasan berita akan tampil di sini setelah kamu mulai mengetik...';
    const tgl     = document.getElementById('fTanggal').value;

    document.getElementById('previewTitle').textContent   = judul;
    document.getElementById('previewExcerpt').textContent = excerpt;
    document.getElementById('previewDate').textContent    = '📅 ' + formatTanggal(tgl);
}

function updatePreviewFrame() {
    const selected = document.querySelector('input[name="border_style"]:checked')?.value || 'none';
    const previewCard = document.querySelector('.preview-card');
    if (previewCard) {
        previewCard.className = 'preview-card frame-' + selected;
    }
}

['fJudul','fExcerpt','fTanggal'].forEach(id => {
    document.getElementById(id).addEventListener('input', updatePreview);
});

// ─── KARAKTER COUNTER (EXCERPT) ───────────────────────────────────────────────
document.getElementById('fExcerpt').addEventListener('input', function() {
    const len = this.value.length;
    const counter = document.getElementById('excerptCounter');
    counter.textContent = `${len} / 300`;
    counter.className = 'char-counter' + (len > 270 ? ' danger' : len > 220 ? ' warn' : '');
});

// ─── PHOTO DRAG & DROP ────────────────────────────────────────────────────────
const dropArea    = document.getElementById('photoDropArea');
const photoInput  = document.getElementById('photoInput');
const photoPreview= document.getElementById('photoPreview');
const photoPlaceholder = document.getElementById('photoPlaceholder');
const photoRemoveBtn   = document.getElementById('photoRemoveBtn');

function setPhotoPreview(src) {
    photoPreview.src = src;
    photoPreview.style.display = 'block';
    photoPlaceholder.style.display = 'none';
    photoRemoveBtn.style.display = 'flex';
    dropArea.classList.add('has-image');
    document.getElementById('previewImg').style.backgroundImage = `url('${src}')`;
}

function removePhoto(e) {
    e.stopPropagation();
    photoPreview.src = '';
    photoPreview.style.display = 'none';
    photoPlaceholder.style.display = 'flex';
    photoRemoveBtn.style.display = 'none';
    dropArea.classList.remove('has-image');
    photoInput.value = '';
    document.getElementById('existingGambar').value = '';
    document.getElementById('previewImg').style.backgroundImage = `url('${BASE_URL}assets/images/background.png')`;
}

photoInput.addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => setPhotoPreview(e.target.result);
        reader.readAsDataURL(file);
    }
});

dropArea.addEventListener('dragover', e => { e.preventDefault(); dropArea.classList.add('drag-over'); });
dropArea.addEventListener('dragleave', () => dropArea.classList.remove('drag-over'));
dropArea.addEventListener('drop', e => {
    e.preventDefault();
    dropArea.classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        // Set ke input
        const dt = new DataTransfer();
        dt.items.add(file);
        photoInput.files = dt.files;
        const reader = new FileReader();
        reader.onload = ev => setPhotoPreview(ev.target.result);
        reader.readAsDataURL(file);
    }
});

// ─── BUKA EDITOR (MODE TAMBAH) ────────────────────────────────────────────────
function showPanel() {
    const layout = document.querySelector('.newsroom-layout');
    layout.classList.add('editor-open');
    if (window.innerWidth <= 1100) {
        document.querySelector('.panel-editor').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function hidePanel() {
    const layout = document.querySelector('.newsroom-layout');
    layout.classList.remove('editor-open');
}

function openEditor() {
    resetForm(true); // true = jangan tutup panel
    document.getElementById('editorCardTitle').textContent = '✍️ Tambah Berita Baru';
    document.getElementById('btnSaveText').textContent = '💾 Simpan Berita';
    document.getElementById('fTanggal').value = new Date().toISOString().slice(0,10);
    updatePreview();
    updatePreviewFrame();
    showPanel();
}

// ─── EDIT: ISI FORM DARI DATA ─────────────────────────────────────────────────
function editBerita(b) {
    document.getElementById('newsId').value            = b.id;
    document.getElementById('fJudul').value            = b.judul;
    document.getElementById('fKategori').value         = b.kategori;
    document.getElementById('fTanggal').value          = b.tanggal;
    document.getElementById('fExcerpt').value          = b.excerpt || '';
    
    // Set isi editor Quill
    const kontenVal = b.konten || '';
    quill.root.innerHTML = kontenVal;
    document.getElementById('fKonten').value           = kontenVal;

    // Set pilihan bingkai
    const borderStyle = b.border_style || 'none';
    const rBorder = document.querySelector(`input[name="border_style"][value="${borderStyle}"]`);
    if (rBorder) {
        rBorder.checked = true;
    } else {
        const rDefault = document.querySelector('input[name="border_style"][value="none"]');
        if (rDefault) rDefault.checked = true;
    }
    updatePreviewFrame();

    document.getElementById('fPublished').checked      = b.published == 1;
    document.getElementById('existingGambar').value    = b.gambar || '';

    const excerptEv = new Event('input');
    document.getElementById('fExcerpt').dispatchEvent(excerptEv);

    if (b.gambar) {
        setPhotoPreview(BASE_URL + b.gambar);
    } else {
        removePhoto({ stopPropagation: () => {} });
    }

    document.getElementById('editorCardTitle').textContent = '✏️ Edit Berita';
    document.getElementById('btnSaveText').textContent = '💾 Simpan Perubahan';
    updatePreview();
    showPanel();
}

// ─── RESET FORM ───────────────────────────────────────────────────────────────
function resetForm(keepOpen = false) {
    document.getElementById('newsId').value         = '';
    document.getElementById('fJudul').value         = '';
    document.getElementById('fKategori').value      = 'Berita Acara';
    document.getElementById('fTanggal').value       = new Date().toISOString().slice(0,10);
    document.getElementById('fExcerpt').value       = '';
    
    // Reset editor Quill
    quill.setContents([]);
    document.getElementById('fKonten').value        = '';

    // Reset pilihan bingkai
    const rNone = document.querySelector('input[name="border_style"][value="none"]');
    if (rNone) rNone.checked = true;
    updatePreviewFrame();

    document.getElementById('fPublished').checked   = true;
    document.getElementById('existingGambar').value = '';
    document.getElementById('excerptCounter').textContent = '0 / 300';
    document.getElementById('excerptCounter').className  = 'char-counter';
    removePhoto({ stopPropagation: () => {} });
    document.getElementById('editorCardTitle').textContent = '✍️ Tambah Berita Baru';
    document.getElementById('btnSaveText').textContent = '💾 Simpan Berita';
    updatePreview();
    if (!keepOpen) hidePanel();
}



// ─── SUBMIT FORM (AJAX) ───────────────────────────────────────────────────────
function submitForm() {
    const judul = document.getElementById('fJudul').value.trim();
    const tgl   = document.getElementById('fTanggal').value;

    if (!judul || !tgl) {
        showToast('error', '⚠️ Judul dan Tanggal wajib diisi!');
        return;
    }

    // Sinkronisasi isi Quill ke fKonten sebelum submit
    const isQuillEmpty = quill.getText().trim().length === 0 && !quill.root.innerHTML.includes('<img');
    document.getElementById('fKonten').value = isQuillEmpty ? '' : quill.root.innerHTML;

    const btn     = document.getElementById('btnSave');
    const spinner = document.getElementById('btnSpinner');
    const btnText = document.getElementById('btnSaveText');
    btn.disabled  = true;
    spinner.style.display = 'block';
    btnText.textContent   = 'Menyimpan...';

    const fd = new FormData(document.getElementById('newsForm'));
    fd.set('published', document.getElementById('fPublished').checked ? '1' : '0');

    fetch(SAVE_URL, { method: 'POST', body: fd })
        .then(async r => {
            const text = await r.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Server response:', text);
                throw new Error(text || 'Format respons tidak valid.');
            }
        })
        .then(res => {
            if (res.status === 'success') {
                showToast('success', '✅ ' + res.message);
                setTimeout(() => location.reload(), 900);
            } else {
                showToast('error', '❌ ' + res.message);
                btn.disabled = false;
                spinner.style.display = 'none';
                btnText.textContent = '💾 Simpan Berita';
            }
        })
        .catch(err => {
            console.error(err);
            showToast('error', '❌ Terjadi kesalahan saat menyimpan berita.');
            btn.disabled = false;
            spinner.style.display = 'none';
            btnText.textContent = '💾 Simpan Berita';
        });
}

// ─── HAPUS BERITA ─────────────────────────────────────────────────────────────
function hapusBerita(id, judul) {
    Swal.fire({
        title: '🗑️ Hapus Berita?',
        html: `Yakin mau menghapus:<br><strong>"${judul}"</strong><br><small style="color:#94a3b8">Tindakan ini tidak bisa dibatalkan.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        background: '#fff',
        borderRadius: '20px',
    }).then(result => {
        if (result.isConfirmed) {
            fetch(`${DEL_URL}/${id}`, { method: 'POST' })
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        showToast('success', '✅ Berita berhasil dihapus!');
                        const row = document.querySelector(`tr[data-id="${id}"]`);
                        if (row) {
                            row.style.transition = 'opacity 0.4s, transform 0.4s';
                            row.style.opacity = '0';
                            row.style.transform = 'translateX(30px)';
                            setTimeout(() => { row.remove(); updateCount(); }, 450);
                        }
                    } else {
                        showToast('error', '❌ ' + res.message);
                    }
                });
        }
    });
}

// ─── TOGGLE PUBLISH ───────────────────────────────────────────────────────────
function togglePublish(id, checkbox) {
    fetch(`${TOG_URL}/${id}`, { method: 'POST' })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                showToast('success', res.published ? '🌐 Berita dipublikasikan!' : '🔒 Berita disembunyikan!');
            } else {
                checkbox.checked = !checkbox.checked; // revert
                showToast('error', '❌ Gagal mengubah status.');
            }
        });
}

// ─── DATA LIST FOR AUTOCOMPLETE ───────────────────────────────────────────────
const newsDataList = <?= json_encode(array_map(function($b) {
    return [
        'id' => $b->id,
        'judul' => $b->judul,
        'kategori' => $b->kategori,
        'gambar' => ($b->gambar && file_exists(FCPATH.$b->gambar)) ? base_url($b->gambar) : '',
        'tanggal' => $b->tanggal
    ];
}, $all_berita ?? []), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

// ─── TABLE SEARCH & AUTOCOMPLETE ─────────────────────────────────────────────
const searchInput      = document.getElementById('tableSearch');
const searchClearBtn   = document.getElementById('searchClearBtn');
const searchAutocomplete = document.getElementById('searchAutocomplete');
let selectedAutocIndex = -1;

function highlightMatch(text, query) {
    if (!query) return text;
    const escaped = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const regex = new RegExp(`(${escaped})`, 'gi');
    return text.replace(regex, '<mark>$1</mark>');
}

function filterTable(q) {
    const query = q.toLowerCase().trim();
    document.querySelectorAll('#newsTableBody tr[data-id]').forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = (query === '' || text.includes(query)) ? '' : 'none';
    });
    updateCount();
}

function renderAutocomplete(query) {
    const q = query.trim().toLowerCase();
    selectedAutocIndex = -1;

    if (!q) {
        searchAutocomplete.classList.remove('open');
        searchAutocomplete.innerHTML = '';
        searchClearBtn.style.display = 'none';
        return;
    }

    searchClearBtn.style.display = 'flex';

    const matches = newsDataList.filter(item => 
        item.judul.toLowerCase().includes(q) || 
        item.kategori.toLowerCase().includes(q)
    ).slice(0, 6);

    if (matches.length === 0) {
        searchAutocomplete.innerHTML = '<div class="search-autocomplete-no-match">🔍 Tidak ada berita yang cocok</div>';
        searchAutocomplete.classList.add('open');
        return;
    }

    searchAutocomplete.innerHTML = '';
    matches.forEach((item, index) => {
        const el = document.createElement('div');
        el.className = 'search-autocomplete-item';
        el.dataset.index = index;

        const catClass = 'cat-' + item.kategori.replace(/[\s\/]/g, '');
        const thumbHtml = item.gambar 
            ? `<img class="search-autocomplete-thumb" src="${item.gambar}" alt="" />`
            : `<div class="search-autocomplete-thumb">📰</div>`;

        el.innerHTML = `
            <div class="search-autocomplete-left">
                ${thumbHtml}
                <span class="search-autocomplete-title">${highlightMatch(item.judul, q)}</span>
            </div>
            <span class="kategori-badge search-autocomplete-badge ${catClass}">${item.kategori}</span>
        `;

        el.addEventListener('click', () => {
            searchInput.value = item.judul;
            filterTable(item.judul);
            searchAutocomplete.classList.remove('open');

            const targetRow = document.querySelector(`tr[data-id="${item.id}"]`);
            if (targetRow) {
                targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                targetRow.style.transition = 'background 0.4s ease';
                targetRow.style.background = '#fed7aa';
                setTimeout(() => { targetRow.style.background = ''; }, 1200);
            }
        });

        searchAutocomplete.appendChild(el);
    });

    searchAutocomplete.classList.add('open');
}

searchInput.addEventListener('input', function() {
    filterTable(this.value);
    renderAutocomplete(this.value);
});

searchInput.addEventListener('focus', function() {
    if (this.value.trim()) {
        renderAutocomplete(this.value);
    }
});

// Keyboard Navigation for Autocomplete
searchInput.addEventListener('keydown', function(e) {
    const items = searchAutocomplete.querySelectorAll('.search-autocomplete-item');
    if (!items.length || !searchAutocomplete.classList.contains('open')) return;

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        selectedAutocIndex = (selectedAutocIndex + 1) % items.length;
        updateActiveAutocItem(items);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedAutocIndex = (selectedAutocIndex - 1 + items.length) % items.length;
        updateActiveAutocItem(items);
    } else if (e.key === 'Enter') {
        if (selectedAutocIndex >= 0 && items[selectedAutocIndex]) {
            e.preventDefault();
            items[selectedAutocIndex].click();
        }
    } else if (e.key === 'Escape') {
        searchAutocomplete.classList.remove('open');
    }
});

function updateActiveAutocItem(items) {
    items.forEach((it, idx) => {
        it.classList.toggle('active', idx === selectedAutocIndex);
    });
}

// Clear Search Button
searchClearBtn.addEventListener('click', function() {
    searchInput.value = '';
    filterTable('');
    searchAutocomplete.classList.remove('open');
    searchClearBtn.style.display = 'none';
    searchInput.focus();
});

// Close Autocomplete on Click Outside
document.addEventListener('click', function(e) {
    if (!searchInput.contains(e.target) && !searchAutocomplete.contains(e.target)) {
        searchAutocomplete.classList.remove('open');
    }
});

function updateCount() {
    const visible = document.querySelectorAll('#newsTableBody tr[data-id]:not([style*="none"])').length;
    document.getElementById('newsCount').textContent = `${visible} artikel tersimpan`;
}

// ─── TABLE SORT ───────────────────────────────────────────────────────────────
let sortDir = {};
document.querySelectorAll('.sortable').forEach(th => {
    th.addEventListener('click', () => {
        const col = parseInt(th.dataset.col);
        const asc = !sortDir[col];
        sortDir[col] = asc;

        document.querySelectorAll('.sortable').forEach(t => t.classList.remove('sorted'));
        th.classList.add('sorted');
        th.querySelector('.sort-icon').textContent = asc ? '↑' : '↓';

        const tbody = document.getElementById('newsTableBody');
        const rows  = Array.from(tbody.querySelectorAll('tr[data-id]'));
        rows.sort((a, b) => {
            const av = a.cells[col]?.textContent.trim() || '';
            const bv = b.cells[col]?.textContent.trim() || '';
            return asc ? av.localeCompare(bv) : bv.localeCompare(av);
        });
        rows.forEach(r => tbody.appendChild(r));
    });
});

// ─── TOAST ────────────────────────────────────────────────────────────────────
function showToast(type, msg) {
    const tc   = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = msg;
    tc.appendChild(toast);
    setTimeout(() => {
        toast.classList.add('toast-out');
        setTimeout(() => toast.remove(), 350);
    }, 3000);
}

// ─── INIT ─────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('fTanggal').value = new Date().toISOString().slice(0, 10);
    updatePreview();
});
</script>

</body>
</html>
