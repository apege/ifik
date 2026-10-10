<?php
/** @var array $riwayat */
$session_role = strtolower((string) $this->session->userdata('role'));
$display_nama = ($session_role === 'admin') ? 'Laboran' : ($this->session->userdata('nama') ?: $this->session->userdata('username') ?: 'Pengguna FIK');
$user_role_id = (int)($this->session->userdata('role_id') ?? 0);
$role_names = [
    1  => 'Admin System',
    2  => 'Kepala Urusan',
    3  => 'Dosen',
    4  => 'Mahasiswa',
    5  => 'Admin LAA',
    6  => 'Koordinator TA',
    7  => 'PIC KK',
    9  => 'Ketua KK',
    21 => 'Laboran',
    22 => 'Super Admin'
];
$user_role_label = $role_names[$user_role_id] ?? ($this->session->userdata('role') ?: 'PORTAL IFIK');
$notif_items = isset($notifikasi) && is_array($notifikasi) ? $notifikasi : [];
$notif_count = (int) ($unread_notifikasi ?? 0);
$history_pagination = isset($pagination) && is_array($pagination) ? $pagination : ['page' => 1, 'per_page' => 10, 'total' => count($riwayat ?? []), 'total_pages' => 1];
$history_page = (int) $history_pagination['page'];
$history_total_pages = (int) $history_pagination['total_pages'];
$history_total = (int) $history_pagination['total'];
$history_per_page = (int) $history_pagination['per_page'];
$history_query = $_GET;
$history_query['per_page'] = $history_per_page;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman Barang - IFIK</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/loan-progress.css'); ?>?v=<?= @filemtime(FCPATH . 'assets/css/loan-progress.css'); ?>">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }

        /* Palette FIK */
        .text-fik-orange { color: #ea5b1a !important; }
        .bg-fik-orange { background-color: #ea5b1a !important; }
        .text-fik-brown { color: #5d3315 !important; }

        /* Navbar */
        .navbar-custom { background-color: #ffffff; padding: 12px 0; border-bottom: 2px solid #ea5b1a; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); }
        .navbar-dark .navbar-nav .nav-link { color: #333333; font-weight: 500; font-size: 0.95rem; margin: 0 12px; transition: 0.3s; position: relative; }
        .navbar-dark .navbar-nav .nav-link:hover, .navbar-dark .navbar-nav .nav-link.active { color: #ea5b1a; }
        .navbar-dark .navbar-nav .nav-link::after { content: ''; position: absolute; width: 0; height: 2px; display: block; margin-top: 5px; right: 0; background: #ea5b1a; transition: width 0.3s ease; }
        .navbar-dark .navbar-nav .nav-link:hover::after { width: 100%; left: 0; background: #ea5b1a; }
        .btn-user { background: linear-gradient(45deg, #c24a13, #ea5b1a); color: white; font-weight: 600; border: none; border-radius: 8px; padding: 8px 20px; }
        .notif-bell { width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center; }
        .notif-menu { width: min(380px, calc(100vw - 32px)); max-height: min(420px, calc(100vh - 110px)); overflow-y: auto; }

        /* Custom Table Styling */
        .table-custom { border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .table-custom thead th { background-color: #5d3315; color: white; font-weight: 500; border: none; padding: 15px; letter-spacing: 0.5px;}
        .table-custom tbody td { padding: 15px; vertical-align: middle; border-bottom: 1px solid #eee; background: white; }
        .table-custom tbody tr:hover td { background-color: #fafafa; }
        
        .table-custom { width: 100%; table-layout: fixed; }
        .badge-status { display:inline-flex; align-items:center; justify-content:center; gap:.4rem; width:300px; min-width:300px; max-width:300px; height:42px; min-height:42px; padding:7px 14px; border-radius:999px; font-weight:600; font-size:.76rem; line-height:1.2; white-space:normal; text-align:center; }
        .history-search { width: 100%; max-width: 100%; margin: 0 0 1.5rem; }
        .history-date { display:grid; width:100%; grid-template-columns:24px minmax(0, 1fr); align-items:center; gap:.55rem; padding:.5rem .6rem; border:1px solid transparent; border-radius:10px; cursor:help; transition:background-color .18s ease, border-color .18s ease; }
        .history-date:hover { background:#fff3eb; }
        .history-date > i { width:24px; font-size:1rem; text-align:center; }
        .history-date__range { display:flex; min-width:0; flex-direction:column; gap:.12rem; }
        .history-date__line { display:block; overflow-wrap:normal !important; word-break:normal !important; white-space:nowrap !important; }
        .history-date__line--start { color:#252a31; font-size:.8rem; font-weight:600; }
        .history-date__line--end { color:#6c757d; font-size:.75rem; }
        .history-date__connector { display:inline-block; min-width:2.15rem; color:#9aa1aa; }
        @media (min-width: 1200px) {
            .table-custom.scm-responsive-table th:nth-child(1),
            .table-custom.scm-responsive-table td:nth-child(1) { width:145px !important; }
            .table-custom.scm-responsive-table th:nth-child(3),
            .table-custom.scm-responsive-table td:nth-child(3) { width:235px !important; min-width:235px !important; }
            .table-custom.scm-responsive-table th:nth-child(4),
            .table-custom.scm-responsive-table td:nth-child(4) { width:320px !important; min-width:320px !important; }
            .table-custom.scm-responsive-table th:nth-child(5),
            .table-custom.scm-responsive-table td:nth-child(5) { width:155px !important; }
        }
        .history-empty-filter { display:none; }
        .history-pagination { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-top:1.25rem; }
        .history-pagination__info { margin:0; color:#6c757d; font-size:.85rem; }
        .history-pagination .pagination { flex-wrap:wrap; }
        .history-pagination .page-link {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-width:38px;
            height:38px;
            padding:.4rem .7rem;
            border-color:#e2e5e9;
            color:#5d3315;
            font-weight:600;
            box-shadow:none;
        }
        .history-pagination .page-item:first-child .page-link,
        .history-pagination .page-item:last-child .page-link { border-radius:10px; }
        .history-pagination .page-item.active .page-link { background:#ea5b1a; border-color:#ea5b1a; color:#fff; }
        .history-pagination .page-item:not(.active):not(.disabled) .page-link:hover { background:#fff3eb; border-color:#ea5b1a; color:#c44810; }
        .history-pagination .page-item.disabled .page-link { color:#adb5bd; background:#f3f4f6; }
        .history-list-summary { margin-bottom:0; }
        @media (max-width: 575.98px) {
            .history-pagination { flex-direction:column; justify-content:center; }
            .history-pagination__info { text-align:center; }
            .history-pagination .page-link { min-width:36px; height:36px; padding:.35rem .6rem; }
        }

        /* Unified Search Pill & Custom Multi-Filter (Exact Admin LAA & Koordinator TA Style) */
        .unified-search-pill {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(12px);
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            padding: 3px 14px;
            height: 48px;
            transition: border-color 0.25s ease, box-shadow 0.25s ease, background-color 0.25s ease;
            position: relative;
        }
        .unified-search-pill:focus-within, .unified-search-pill.active {
            border-color: #ea580c !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.14), 0 10px 25px -5px rgba(234, 88, 12, 0.12) !important;
        }
        .unified-divider {
            width: 1px;
            height: 24px;
            background: #e2e8f0;
            margin: 0 10px;
        }
        .custom-dropdown-menu {
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            transform-origin: top left;
        }
        .custom-dropdown-menu.hidden {
            display: none !important;
        }
        .dropdown-item-opt {
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 500;
            color: #334155;
            transition: background-color 0.15s ease, color 0.15s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .dropdown-item-opt:hover, .dropdown-item-opt.active {
            background-color: #fff7ed;
            color: #ea580c;
            font-weight: 600;
        }
        .autocomplete-box {
            max-height: 340px;
            overflow-y: auto;
            border-radius: 18px;
            background: #ffffff !important;
            box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.25), 0 8px 24px -4px rgba(234, 88, 12, 0.2) !important;
            z-index: 2050 !important;
            position: absolute !important;
            transition: opacity 0.25s ease, transform 0.25s ease;
        }
        .autocomplete-item-row {
            padding: 10px 16px;
            transition: all 0.18s ease;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
        }
        .autocomplete-item-row:last-child {
            border-bottom: none;
        }
        .autocomplete-item-row:hover, .autocomplete-item-row.active-nav {
            background-color: #fff7ed;
            color: #ea580c;
        }
        .autocomplete-item-row mark {
            background: #ffedd5;
            color: #ea580c;
            font-weight: 800;
            border-radius: 4px;
            padding: 0 3px;
        }
        .btn-standalone-add {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff7ed;
            border: 1.5px solid #ffedd5;
            border-radius: 16px;
            padding: 6px 14px;
            height: 48px;
            font-size: 0.82rem;
            font-weight: 700;
            color: #ea580c;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(234, 88, 12, 0.06);
        }
        .btn-standalone-add:hover {
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
        .btn-remove-row {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            background: #fff1f2;
            border: 1.5px solid #fecdd3;
            border-radius: 14px;
            color: #e11d48;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }
        .btn-remove-row:hover {
            background: #ffe4e6;
            border-color: #fda4af;
            color: #be123c;
            transform: scale(1.05);
        }
        .extra-rows-card {
            display: none;
            position: relative;
            margin-top: 12px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            padding: 14px;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
            transition: all 0.25s ease;
        }
        .extra-rows-card.open {
            display: block !important;
        }
        .extra-filter-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
    <?php include APPPATH . 'views/shared/theme_assets.php'; ?>
</head>
<body>

    <!-- Dedicated Sidebar Component (Pola Admin LAA) -->
    <?php $this->load->view('peminjaman_barang/sidebar'); ?>

<div id="laaMainContentWrapper">
    <!-- Sub Navigation Page Title Bar (Admin LAA style) -->
    <header class="glass-header-ifik mb-4">
        <div class="container d-flex flex-wrap align-items-center justify-content-between gap-3 header-inner-pad">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center shadow-xs flex-shrink-0" style="width: 44px; height: 44px; background: rgba(234, 91, 26, 0.12); color: #ea5b1a; font-size: 1.35rem;">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h1 class="h5 fw-bold text-dark mb-0 tracking-tight">Riwayat Peminjaman Barang</h1>
                        <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(234, 91, 26, 0.12); color: #ea5b1a; font-weight: 700; font-size: 11px;">Status &amp; Tracking</span>
                    </div>
                    <p class="text-muted small mb-0 d-none d-sm-block" style="font-size: 12px;">Pantau progress pengajuan, status verifikasi, dan QR serah terima barang.</p>
                </div>
            </div>

            <!-- Profile & Quick Action -->
            <div class="d-flex align-items-center gap-2 ms-auto">
                <a href="<?= site_url('peminjaman_barang'); ?>" class="btn-ifik-action" title="Kembali ke Katalog Barang">
                    <i class="bi bi-box-seam"></i>
                    <span>Katalog Alat</span>
                </a>
                
                <div class="dropdown">
                    <button class="btn-ifik-profile" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="profile-avatar-box">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div class="profile-text-group d-none d-sm-flex">
                            <span class="profile-name"><?= html_escape($display_nama); ?></span>
                            <span class="profile-role"><?= html_escape($user_role_label); ?></span>
                        </div>
                        <i class="bi bi-chevron-down profile-chevron"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-ifik mt-2">
                        <li class="px-3 py-2 border-bottom mb-1">
                            <span class="d-block text-muted" style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Login Sebagai</span>
                            <span class="fw-bold text-dark d-block text-truncate" style="font-size: 13px;"><?= html_escape($this->session->userdata('username') ?: $display_nama); ?></span>
                            <span class="badge rounded-pill mt-1" style="background: #fff7ed; color: #ea580c; font-size: 10px; font-weight: 700;"><?= html_escape($user_role_label); ?></span>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= site_url('peminjaman_barang/riwayat') ?>">
                                <i class="bi bi-clock-history text-primary"></i>
                                <span>Riwayat Pinjam</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= site_url('peminjaman_barang') ?>">
                                <i class="bi bi-grid text-warning"></i>
                                <span>Katalog Alat</span>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item text-danger fw-bold" href="<?= site_url('login/logout') ?>">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Keluar</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <!-- CONTENT -->
    <div class="container py-3">

        <!-- Notifikasi Sukses -->
        <?php if($this->session->flashdata('success')): ?>
            <div class="alert alert-success border-0 shadow-sm d-flex align-items-center rounded-3 mb-4" data-aos="zoom-in">
                <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                <div><?= $this->session->flashdata('success'); ?></div>
            </div>
        <?php endif; ?>

        <!-- Unified Multi-Search Bar (Admin LAA Pendaftaran TA Style) -->
        <?php
            $cat_labels = [
                'all'     => '🔍 Semua Riwayat',
                'barang'  => '📦 Nama Barang',
                'kode'    => '🏷️ Kode Aset',
                'status'  => '⏳ Status Approval',
                'tanggal' => '📅 Tanggal Pinjam'
            ];
            $cat_placeholders = [
                'all'     => 'Cari nama barang, status, kode aset, atau tanggal...',
                'barang'  => 'Cari nama barang...',
                'kode'    => 'Cari kode aset (misal: AST-DKV-01)...',
                'status'  => 'Cari status (misal: Disetujui, Dipinjam)...',
                'tanggal' => 'Cari tanggal (YYYY-MM-DD)...'
            ];
            $current_main_cat = $filter_rows[0]['field'] ?? ($cat ?? 'all');
            $current_main_val = $filter_rows[0]['value'] ?? ($search ?? '');
            $extra_rows = array_slice($filter_rows ?? [], 1);
            $total_active_rows = 1 + count($extra_rows);
        ?>
        <div class="card border-0 shadow-sm p-3 mb-4 rounded-4 position-relative bg-white" data-aos="fade-up" style="position: relative; z-index: 1050;">
            <form action="<?= site_url('peminjaman_barang/riwayat'); ?>" method="GET" id="formSearchRiwayat" class="position-relative" style="position: relative; z-index: 1051;">
                <input type="hidden" name="per_page" value="<?= (int)$history_per_page; ?>">
                <input type="hidden" name="sort_by" value="<?= htmlspecialchars($history_sort ?? ''); ?>">
                <input type="hidden" name="sort_dir" value="<?= htmlspecialchars($history_dir ?? 'desc'); ?>">
                <input type="hidden" name="filter_field[]" id="mainCategorySelectRiwayat" value="<?= htmlspecialchars($current_main_cat); ?>">
                
                <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2">
                    <!-- Main Search Pill -->
                    <div class="unified-search-pill flex-grow-1 d-flex align-items-center justify-content-between min-w-0" id="mainSearchPillRiwayat">
                        <!-- Main Category Selector Dropdown -->
                        <div class="position-relative flex-shrink-0">
                            <button type="button" onclick="toggleRiwayatCustomDropdown('main-cat', event)" class="d-flex align-items-center gap-1 bg-transparent border-0 text-dark fw-bold cursor-pointer py-1 px-1" style="font-size: 0.8rem;">
                                <span id="label-filter-main-cat" class="text-truncate" style="max-width: 135px;">
                                    <?= $cat_labels[$current_main_cat] ?? '🔍 Semua Riwayat'; ?>
                                </span>
                                <i class="fa-solid fa-chevron-down text-secondary ms-1 dropdown-arrow transition-all" id="arrow-filter-main-cat" style="font-size: 9px;"></i>
                            </button>
                            <div id="menu-filter-main-cat" class="custom-dropdown-menu hidden position-absolute top-100 start-0 mt-2 bg-white border border-light-subtle rounded-3 shadow-lg p-1" style="width: 215px; z-index: 1050;">
                                <?php foreach($cat_labels as $ck => $clabel): ?>
                                    <div onclick="selectRiwayatMainCategory('<?= $ck ?>', '<?= htmlspecialchars($clabel, ENT_QUOTES) ?>', '<?= htmlspecialchars($cat_placeholders[$ck], ENT_QUOTES) ?>', this)" 
                                         class="dropdown-item-opt <?= $current_main_cat === $ck ? 'active' : '' ?>">
                                        <span><?= $clabel ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="unified-divider flex-shrink-0"></div>

                        <!-- Input Text Container -->
                        <div id="mainValueContainerRiwayat" class="flex-grow-1 d-flex align-items-center min-w-0 px-1">
                            <i id="searchIconRiwayat" class="fa-solid fa-magnifying-glass text-secondary me-2 flex-shrink-0" style="font-size: 13px;"></i>
                            <input type="text" name="filter_value[]" id="inputSearchRiwayat" autocomplete="off" value="<?= htmlspecialchars($current_main_val); ?>" 
                                   placeholder="<?= htmlspecialchars($cat_placeholders[$current_main_cat] ?? $cat_placeholders['all']); ?>" 
                                   class="form-control border-0 shadow-none bg-transparent p-0 text-dark fw-semibold" style="font-size: 0.82rem;">
                            
                            <button type="button" id="btnClearSearchRiwayat" onclick="clearRiwayatSearch()" class="<?= empty($current_main_val) ? 'd-none' : ''; ?> btn btn-link p-0 text-secondary text-decoration-none me-2" title="Hapus pencarian">
                                <i class="fa-solid fa-circle-xmark fs-6"></i>
                            </button>
                        </div>

                        <!-- Tombol Cari -->
                        <button type="submit" id="btnSubmitSearchRiwayat" class="btn text-white fw-bold d-flex align-items-center gap-1 rounded-3 px-3 py-1 flex-shrink-0 shadow-sm" style="background: linear-gradient(135deg, #ea580c, #f97316); font-size: 0.78rem;">
                            <i class="fa-solid fa-magnifying-glass" style="font-size: 11px;"></i>
                            <span class="d-none d-sm-inline">Cari</span>
                        </button>
                    </div>

                    <!-- Standalone Add Filter Button (+ 1/4) -->
                    <button type="button" id="standaloneAddBtnRiwayat" onclick="toggleRiwayatMultiFilter(event)" class="btn-standalone-add flex-shrink-0 justify-content-center" title="Buka / Tambah Filter Baru (Maks 4)">
                        <i class="fa-solid fa-plus fs-6"></i>
                        <span id="filterCountBadgeRiwayat" class="badge-standalone-count"><?= min(4, max(1, $total_active_rows)) ?>/4</span>
                    </button>
                </div>

                <!-- Autocomplete Dropdown -->
                <div id="autocompleteDropdownRiwayat" class="d-none position-absolute start-0 end-0 top-100 mt-2 bg-white border border-light-subtle rounded-4 shadow-lg overflow-hidden autocomplete-box" style="z-index: 1060;">
                    <div id="autocompleteResultsRiwayat" class="p-0"></div>
                </div>

                <!-- Extra Filter Rows Card Popover -->
                <div id="extraRowsCardRiwayat" class="extra-rows-card <?= !empty($extra_rows) ? 'open' : '' ?>">
                    <div id="additionalFilterRowsContainerRiwayat" class="d-flex flex-column gap-2 mb-2">
                        <?php if(!empty($extra_rows)): ?>
                            <?php foreach($extra_rows as $idx => $erow): 
                                $erow_id = 'extra-row-' . ($idx + 1);
                                $erow_cat = $erow['field'] ?? 'barang';
                                $erow_val = $erow['value'] ?? '';
                            ?>
                                <div class="extra-filter-row" id="<?= $erow_id ?>">
                                    <input type="hidden" name="filter_field[]" id="field-<?= $erow_id ?>" value="<?= htmlspecialchars($erow_cat) ?>">
                                    <div class="unified-search-pill flex-grow-1 d-flex align-items-center justify-content-between min-w-0" style="height: 44px;">
                                        <div class="position-relative flex-shrink-0">
                                            <button type="button" onclick="toggleRiwayatCustomDropdown('<?= $erow_id ?>', event)" class="d-flex align-items-center gap-1 bg-transparent border-0 text-dark fw-bold cursor-pointer py-1 px-1" style="font-size: 0.8rem;">
                                                <span id="label-filter-<?= $erow_id ?>" class="text-truncate" style="max-width: 135px;"><?= $cat_labels[$erow_cat] ?? '📦 Nama Barang' ?></span>
                                                <i class="fa-solid fa-chevron-down text-secondary ms-1 dropdown-arrow transition-all" id="arrow-filter-<?= $erow_id ?>" style="font-size: 9px;"></i>
                                            </button>
                                            <div id="menu-filter-<?= $erow_id ?>" class="custom-dropdown-menu hidden position-absolute top-100 start-0 mt-2 bg-white border border-light-subtle rounded-3 shadow-lg p-1" style="width: 215px; z-index: 1050;">
                                                <?php foreach($cat_labels as $ck => $clabel): ?>
                                                    <div onclick="selectRiwayatExtraCategory('<?= $erow_id ?>', '<?= $ck ?>', '<?= htmlspecialchars($clabel, ENT_QUOTES) ?>', '<?= htmlspecialchars($cat_placeholders[$ck], ENT_QUOTES) ?>', this)" 
                                                         class="dropdown-item-opt <?= $erow_cat === $ck ? 'active' : '' ?>">
                                                        <span><?= $clabel ?></span>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <div class="unified-divider flex-shrink-0"></div>
                                        <div class="flex-grow-1 d-flex align-items-center min-w-0 px-1">
                                            <i class="fa-solid fa-magnifying-glass text-secondary me-2 flex-shrink-0" style="font-size: 12px;"></i>
                                            <input type="text" name="filter_value[]" value="<?= htmlspecialchars($erow_val) ?>" placeholder="<?= htmlspecialchars($cat_placeholders[$erow_cat] ?? '') ?>" class="form-control border-0 shadow-none bg-transparent p-0 text-dark fw-semibold extra-row-input" style="font-size: 0.82rem;">
                                        </div>
                                    </div>
                                    <button type="button" onclick="removeRiwayatFilterRow(this)" class="btn-remove-row" title="Hapus Kriteria Ini">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-flex align-items-center justify-content-between border-top border-light-subtle pt-2 mt-2" style="font-size: 0.78rem;">
                        <span class="text-muted"><i class="fa-solid fa-circle-info me-1"></i>Gunakan kombinasi kriteria untuk mempersempit pencarian riwayat peminjaman.</span>
                        <button type="button" onclick="resetRiwayatMultiSearch()" class="btn btn-link btn-sm text-danger fw-bold text-decoration-none p-0">
                            Reset All Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabel Riwayat -->
        <?php if(!empty($riwayat)): ?>
        <div class="scm-pagination-top history-list-summary" aria-label="Pengaturan jumlah riwayat">
            <div class="scm-pagination-top__summary">
                <label for="historyPageSize">Tampilkan:</label>
                <select id="historyPageSize" name="per_page" class="form-select form-select-sm" aria-label="Jumlah riwayat per halaman">
                    <?php foreach ([10, 25, 50, 100] as $size): ?><option value="<?= $size ?>" <?= $history_per_page === $size ? 'selected' : '' ?>><?= $size ?></option><?php endforeach; ?>
                </select>
                <span>Total item: <span id="historyTotalItems"><?= number_format($history_total, 0, ',', '.') ?></span></span>
            </div>
        </div>
        <?php endif; ?>
        <div class="table-responsive history-table-shell" data-aos="fade-up">
            <table class="table table-custom history-table mb-0">
                <thead>
                    <tr>
                        <?php foreach (['tanggal' => 'Tgl Pengajuan', 'barang' => 'Nama Barang', 'masa' => 'Masa Pinjam', 'status' => 'Status Approval', 'qr' => 'Status QR'] as $sort_key => $sort_label): ?>
                        <th class="<?= $sort_key === 'qr' ? 'text-center' : '' ?>" aria-sort="<?= scm_sort_aria($sort_key, $history_sort ?? '', $history_dir ?? 'desc') ?>"><a class="scm-sort-control <?= ($history_sort ?? '') === $sort_key ? 'is-active' : '' ?>" href="<?= scm_sort_url($sort_key, $history_sort ?? '', $history_dir ?? 'desc') ?>"><?= html_escape($sort_label) ?><i class="bi <?= scm_sort_icon_class($sort_key, $history_sort ?? '', $history_dir ?? 'desc') ?>" aria-hidden="true"></i></a></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($riwayat)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-folder2-open text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2 mb-0">Belum ada riwayat peminjaman.</p>
                                <a href="<?= base_url('index.php/peminjaman_barang') ?>" class="btn btn-sm btn-outline-secondary mt-2">Buka Katalog</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($riwayat as $r): ?>
                        <?php $history_dates = implode(' ', [substr((string)($r->created_at ?? ''), 0, 10), substr((string)($r->tanggal_pinjam ?? ''), 0, 10), substr((string)($r->tanggal_kembali_rencana ?? ''), 0, 10), tanggal_indonesia($r->created_at ?? null), tanggal_indonesia($r->tanggal_pinjam ?? null), tanggal_indonesia($r->tanggal_kembali_rencana ?? null)]); $search_label = strtolower(implode(' ', [$r->nama_aset ?? '', $r->kode_aset ?? '', $r->status ?? '', $history_dates])); ?>
                        <tr data-history-row data-search="<?= html_escape($search_label) ?>" data-filter-all="<?= html_escape($search_label) ?>" data-filter-barang="<?= html_escape($r->nama_aset ?? '') ?>" data-filter-kode="<?= html_escape($r->kode_aset ?? '') ?>" data-filter-status="<?= html_escape($r->status ?? '') ?>" data-filter-tanggal="<?= html_escape($history_dates) ?>">
                            <td>
                                <div class="fw-semibold text-dark"><?= tanggal_indonesia($r->created_at) ?></div>
                                <div class="text-muted small"><?= html_escape(jam_indonesia($r->created_at)) ?></div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= $r->nama_aset ?></div>
                                <div class="text-muted small">Kode: <?= $r->kode_aset ?> &bull; Jml: <span class="text-fik-orange fw-bold"><?= $r->jumlah_pinjam ?></span></div>
                                <?php if (($r->jenis_peminjaman ?? '') === 'luar_kampus'): ?>
                                    <span class="badge rounded-pill bg-purple-subtle text-primary border border-primary-subtle mt-1" style="font-size: 10px;">🚀 Luar Kampus</span>
                                <?php else: ?>
                                    <span class="badge rounded-pill bg-warning-subtle text-fik-orange border border-warning-subtle mt-1" style="font-size: 10px;">🏛️ Internal</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="history-date" tabindex="0" data-bs-toggle="tooltip" data-bs-placement="top" title="Masa pinjam: <?= html_escape(masa_pinjam_indonesia($r->tanggal_pinjam, $r->tanggal_kembali_rencana)) ?>">
                                    <i class="bi bi-calendar-range text-fik-orange"></i>
                                    <span class="history-date__range">
                                        <span class="history-date__line history-date__line--start"><span class="visually-hidden">Mulai: </span><time datetime="<?= html_escape(substr((string) $r->tanggal_pinjam, 0, 10)) ?>"><?= html_escape(tanggal_indonesia($r->tanggal_pinjam)) ?></time></span>
                                        <span class="history-date__line history-date__line--end"><span class="history-date__connector" aria-hidden="true">s.d.</span><span class="visually-hidden">Selesai: </span><time datetime="<?= html_escape(substr((string) $r->tanggal_kembali_rencana, 0, 10)) ?>"><?= html_escape(tanggal_indonesia($r->tanggal_kembali_rencana)) ?></time></span>
                                    </span>
                                </span>
                            </td>
                            <td>
                                <?php $loan_progress_item = $r; $loan_progress_compact = true; include APPPATH . 'views/shared/loan_progress.php'; ?>
                            </td>
                            <td class="text-center">
                                <?php
                                    $is_kaprodi_valid = (($r->status_kaprodi ?? '') === 'Disetujui') 
                                        || !in_array($r->status, ['Menunggu ACC Kaprodi', 'Ditolak', 'Kedaluwarsa / Ditolak Otomatis'], true);
                                    $is_not_rejected = !in_array($r->status, ['Ditolak', 'Kedaluwarsa / Ditolak Otomatis'], true);
                                    $show_qr = $is_kaprodi_valid && $is_not_rejected;
                                ?>
                                <?php if($show_qr): ?>
                                    <button class="btn btn-sm btn-outline-dark fw-semibold px-3" data-bs-toggle="modal" data-bs-target="#qrModal<?= $r->id_peminjaman ?>">
                                        <i class="bi bi-qr-code-scan me-1"></i> QR Transaksi
                                    </button>
                                <?php else: ?>
                                    <span class="small text-muted">QR belum aktif</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if(!empty($riwayat)): ?>
        <div id="historyPaginationWrap" class="history-pagination" aria-live="polite">
            <?php $history_first = $history_total ? (($history_page - 1) * $history_per_page) + 1 : 0; $history_last = min($history_total, $history_page * $history_per_page); ?>
            <p id="historyPageInfo" class="history-pagination__info">Menampilkan <?= number_format($history_first, 0, ',', '.') ?>–<?= number_format($history_last, 0, ',', '.') ?> dari <?= number_format($history_total, 0, ',', '.') ?> data</p>
            <nav id="historyPaginationNav" aria-label="Navigasi halaman riwayat peminjaman">
                <ul id="historyPagination" class="pagination pagination-sm mb-0">
                    <?php $history_query['page'] = max(1, $history_page - 1); ?><li class="page-item <?= $history_page <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="<?= current_url() . '?' . http_build_query($history_query) ?>" aria-label="Halaman sebelumnya">Previous</a></li>
                    <?php foreach (scm_pagination_tokens($history_page, $history_total_pages) as $token): ?>
                        <?php if (is_string($token)): ?><li class="page-item disabled" aria-hidden="true"><span class="page-link">&hellip;</span></li>
                        <?php else: $history_query['page'] = $token; ?><li class="page-item <?= $token === $history_page ? 'active' : '' ?>"><a class="page-link" href="<?= current_url() . '?' . http_build_query($history_query) ?>" <?= $token === $history_page ? 'aria-current="page"' : '' ?>><?= $token ?></a></li><?php endif; ?>
                    <?php endforeach; ?>
                    <?php $history_query['page'] = min($history_total_pages, $history_page + 1); ?><li class="page-item <?= $history_page >= $history_total_pages ? 'disabled' : '' ?>"><a class="page-link" href="<?= current_url() . '?' . http_build_query($history_query) ?>" aria-label="Halaman berikutnya">Next</a></li>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>

    <!-- MODAL TIKET QR CODE (DIPINDAHKAN KELUAR DARI TABLE AGAR TIDAK BUG/KEPOTONG) -->
    <?php if(!empty($riwayat)): ?>
        <?php foreach($riwayat as $r): ?>
        <?php
            $is_kaprodi_valid = (($r->status_kaprodi ?? '') === 'Disetujui') 
                || !in_array($r->status, ['Menunggu ACC Kaprodi', 'Ditolak', 'Kedaluwarsa / Ditolak Otomatis'], true);
            $is_not_rejected = !in_array($r->status, ['Ditolak', 'Kedaluwarsa / Ditolak Otomatis'], true);
            $show_qr = $is_kaprodi_valid && $is_not_rejected;
            if(!$show_qr) { continue; }
            $show_return_qr = in_array($r->status, ['Sedang Dipinjam', 'Dipinjam'], true);
            $qr_url = site_url('peminjamanbarang/serah_terima/'.rawurlencode($r->group_id));
        ?>
        <div class="modal fade" id="qrModal<?= $r->id_peminjaman ?>" tabindex="-1" aria-labelledby="qrModalLabel<?= $r->id_peminjaman ?>" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content text-center p-4 border-0 shadow-lg" style="border-radius: 20px;">
                    <h5 class="fw-bold text-fik-orange mb-1" id="qrModalLabel<?= $r->id_peminjaman ?>">QR Transaksi Laboratorium</h5>
                    <p class="small text-muted mb-4"><?= $show_return_qr ? 'Tunjukkan QR yang sama kepada Laboran saat mengembalikan barang.' : 'Tunjukkan QR ini kepada Laboran saat serah terima barang. QR yang sama dipakai kembali saat pengembalian.' ?></p>
                    
                    <!-- QR transaksi tunggal: dipakai untuk serah barang dan pengembalian -->
                    <div class="bg-white p-3 rounded-4 mb-3 mx-auto shadow-sm border" style="display: inline-block;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=<?= rawurlencode($qr_url) ?>" alt="QR Code" class="img-fluid">
                    </div>
                    
                    <div class="font-monospace fs-6 fw-bold bg-light border px-3 py-2 rounded-3 text-secondary mb-3">
                        <?= $r->group_id ?>
                    </div>

                    <div class="alert alert-info py-2 small mb-4 text-start">
                        <strong>Barang:</strong> <?= $r->nama_aset ?><br>
                        <strong>Status:</strong> <?= $r->status ?>
                    </div>
                    
                    <button type="button" class="btn btn-secondary w-100 rounded-pill" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="bg-dark text-center py-4 mt-5">
        <div class="container">
            <p class="small text-white opacity-50 m-0">
                &copy; <?= date('Y') ?> SCM Fakultas Industri Kreatif - Telkom University. All rights reserved.
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="<?= base_url('assets/js/loan-progress.js'); ?>?v=<?= @filemtime(FCPATH . 'assets/js/loan-progress.js'); ?>"></script>
    <script>
        AOS.init({ once: true, offset: 20 });
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));

        // Server-side page size selector
        const historyServerPageSize = document.getElementById('historyPageSize');
        historyServerPageSize?.addEventListener('change', function () {
            const allowedPageSizes = ['10', '25', '50', '100'];
            const selectedPageSize = allowedPageSizes.includes(this.value) ? this.value : '10';
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', selectedPageSize);
            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        });

        // ==========================================
        // UNIFIED SEARCH & MULTI-FILTER (ADMIN LAA STYLE)
        // ==========================================
        const riwayatCatLabels = {
            'all': '🔍 Semua Riwayat',
            'barang': '📦 Nama Barang',
            'kode': '🏷️ Kode Aset',
            'status': '⏳ Status Approval',
            'tanggal': '📅 Tanggal Pinjam'
        };

        const riwayatCatPlaceholders = {
            'all': 'Cari nama barang, status, kode aset, atau tanggal...',
            'barang': 'Cari nama barang...',
            'kode': 'Cari kode aset (misal: AST-DKV-01)...',
            'status': 'Cari status (misal: Disetujui, Dipinjam)...',
            'tanggal': 'Cari tanggal (YYYY-MM-DD)...'
        };

        function toggleRiwayatCustomDropdown(id, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            const menu = document.getElementById('menu-filter-' + id);
            const arrow = document.getElementById('arrow-filter-' + id);
            
            // Tutup dropdown lain
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => {
                if (m !== menu) m.classList.add('hidden');
            });
            document.querySelectorAll('.dropdown-arrow').forEach(a => {
                if (a !== arrow) a.classList.remove('rotate-180');
            });

            if (menu) {
                menu.classList.toggle('hidden');
                if (arrow) arrow.classList.toggle('rotate-180');
            }
        }

        function selectRiwayatMainCategory(catKey, catLabel, placeholder, el) {
            const inputCat = document.getElementById('mainCategorySelectRiwayat');
            const labelEl = document.getElementById('label-filter-main-cat');
            const inputSearch = document.getElementById('inputSearchRiwayat');

            if (inputCat) inputCat.value = catKey;
            if (labelEl) labelEl.textContent = catLabel;
            if (inputSearch) {
                inputSearch.placeholder = placeholder;
                inputSearch.focus();
            }

            document.querySelectorAll('#menu-filter-main-cat .dropdown-item-opt').forEach(opt => opt.classList.remove('active'));
            if (el) el.classList.add('active');

            const menu = document.getElementById('menu-filter-main-cat');
            const arrow = document.getElementById('arrow-filter-main-cat');
            if (menu) menu.classList.add('hidden');
            if (arrow) arrow.classList.remove('rotate-180');
        }

        function updateRiwayatFilterBadge() {
            const container = document.getElementById('additionalFilterRowsContainerRiwayat');
            const rowsCount = container ? container.querySelectorAll('.extra-filter-row').length : 0;
            const total = 1 + rowsCount;
            const badge = document.getElementById('filterCountBadgeRiwayat');
            if (badge) {
                badge.textContent = Math.min(4, total) + '/4';
            }
        }

        function toggleRiwayatMultiFilter(event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            const extraCard = document.getElementById('extraRowsCardRiwayat');
            const container = document.getElementById('additionalFilterRowsContainerRiwayat');
            const currentRows = container ? container.querySelectorAll('.extra-filter-row').length : 0;

            if (extraCard) {
                if (!extraCard.classList.contains('open')) {
                    extraCard.classList.add('open');
                    if (currentRows === 0) {
                        addRiwayatFilterRow();
                    }
                } else {
                    if (currentRows < 3) {
                        addRiwayatFilterRow();
                    } else {
                        extraCard.classList.remove('open');
                    }
                }
            }
            updateRiwayatFilterBadge();
        }

        let riwayatExtraRowSeq = Date.now();

        function addRiwayatFilterRow() {
            const container = document.getElementById('additionalFilterRowsContainerRiwayat');
            if (!container) return;

            const existingRows = container.querySelectorAll('.extra-filter-row').length;
            if (existingRows >= 3) return; // Total max 4 rows (1 main + 3 extra)

            riwayatExtraRowSeq++;
            const rowId = 'extra-row-' + riwayatExtraRowSeq;
            
            // Cari kategori yang belum dipakai jika memungkinkan
            const defaultCat = existingRows === 0 ? 'barang' : (existingRows === 1 ? 'kode' : 'status');

            let dropdownOptsHtml = '';
            for (const [key, label] of Object.entries(riwayatCatLabels)) {
                const isActive = (key === defaultCat) ? 'active' : '';
                dropdownOptsHtml += `
                    <div onclick="selectRiwayatExtraCategory('${rowId}', '${key}', '${label}', '${riwayatCatPlaceholders[key]}', this)" class="dropdown-item-opt ${isActive}">
                        <span>${label}</span>
                    </div>
                `;
            }

            const rowDiv = document.createElement('div');
            rowDiv.className = 'extra-filter-row';
            rowDiv.id = rowId;
            rowDiv.innerHTML = `
                <input type="hidden" name="filter_field[]" id="field-${rowId}" value="${defaultCat}">
                <div class="unified-search-pill flex-grow-1 d-flex align-items-center justify-content-between min-w-0" style="height: 44px;">
                    <div class="position-relative flex-shrink-0">
                        <button type="button" onclick="toggleRiwayatCustomDropdown('${rowId}', event)" class="d-flex align-items-center gap-1 bg-transparent border-0 text-dark fw-bold cursor-pointer py-1 px-1" style="font-size: 0.8rem;">
                            <span id="label-filter-${rowId}" class="text-truncate" style="max-width: 135px;">${riwayatCatLabels[defaultCat]}</span>
                            <i class="fa-solid fa-chevron-down text-secondary ms-1 dropdown-arrow transition-all" id="arrow-filter-${rowId}" style="font-size: 9px;"></i>
                        </button>
                        <div id="menu-filter-${rowId}" class="custom-dropdown-menu hidden position-absolute top-100 start-0 mt-2 bg-white border border-light-subtle rounded-3 shadow-lg p-1" style="width: 215px; z-index: 1050;">
                            ${dropdownOptsHtml}
                        </div>
                    </div>
                    <div class="unified-divider flex-shrink-0"></div>
                    <div class="flex-grow-1 d-flex align-items-center min-w-0 px-1">
                        <i class="fa-solid fa-magnifying-glass text-secondary me-2 flex-shrink-0" style="font-size: 12px;"></i>
                        <input type="text" name="filter_value[]" placeholder="${riwayatCatPlaceholders[defaultCat]}" class="form-control border-0 shadow-none bg-transparent p-0 text-dark fw-semibold extra-row-input" style="font-size: 0.82rem;">
                    </div>
                </div>
                <button type="button" onclick="removeRiwayatFilterRow(this)" class="btn-remove-row" title="Hapus Kriteria Ini">
                    <i class="fa-solid fa-trash text-xs"></i>
                </button>
            `;

            container.appendChild(rowDiv);
            updateRiwayatFilterBadge();

            const newInput = rowDiv.querySelector('input.extra-row-input');
            if (newInput) newInput.focus();
        }

        function removeRiwayatFilterRow(btn) {
            const row = btn.closest('.extra-filter-row');
            if (row) row.remove();

            const extraCard = document.getElementById('extraRowsCardRiwayat');
            const remainingRows = document.querySelectorAll('.extra-filter-row').length;
            if (remainingRows === 0 && extraCard) {
                extraCard.classList.remove('open');
            }

            updateRiwayatFilterBadge();
        }

        function selectRiwayatExtraCategory(rowId, catKey, catLabel, placeholder, el) {
            const fieldEl = document.getElementById('field-' + rowId);
            const labelEl = document.getElementById('label-filter-' + rowId);
            const inputEl = document.querySelector(`#${rowId} input.extra-row-input`);

            if (fieldEl) fieldEl.value = catKey;
            if (labelEl) labelEl.textContent = catLabel;
            if (inputEl) {
                inputEl.placeholder = placeholder;
                inputEl.focus();
            }

            document.querySelectorAll(`#menu-filter-${rowId} .dropdown-item-opt`).forEach(m => m.classList.remove('active'));
            if (el) el.classList.add('active');

            const menu = document.getElementById('menu-filter-' + rowId);
            const arrow = document.getElementById('arrow-filter-' + rowId);
            if (menu) menu.classList.add('hidden');
            if (arrow) arrow.classList.remove('rotate-180');
        }

        function clearRiwayatSearch() {
            const input = document.getElementById('inputSearchRiwayat');
            const btnClear = document.getElementById('btnClearSearchRiwayat');
            if (input) {
                input.value = '';
                input.focus();
            }
            if (btnClear) btnClear.classList.add('d-none');
            
            const form = document.getElementById('formSearchRiwayat');
            if (form) form.submit();
        }

        function resetRiwayatMultiSearch() {
            window.location.href = '<?= site_url("peminjaman_barang/riwayat"); ?>';
        }

        // Event listener Autocomplete & Keyboard Navigation
        document.addEventListener('DOMContentLoaded', () => {
            const inputSearch = document.getElementById('inputSearchRiwayat');
            const btnClear = document.getElementById('btnClearSearchRiwayat');
            const autoDropdown = document.getElementById('autocompleteDropdownRiwayat');
            const autoResults = document.getElementById('autocompleteResultsRiwayat');
            const searchIcon = document.getElementById('searchIconRiwayat');
            const formSearch = document.getElementById('formSearchRiwayat');

            let autoDebounceTimer = null;
            let currentFocusIdx = -1;

            if (inputSearch) {
                inputSearch.addEventListener('input', function() {
                    const q = this.value.trim();
                    const currentCat = document.getElementById('mainCategorySelectRiwayat')?.value || 'all';

                    if (btnClear) {
                        if (q.length > 0) {
                            btnClear.classList.remove('d-none');
                        } else {
                            btnClear.classList.add('d-none');
                        }
                    }

                    if (searchIcon) {
                        searchIcon.className = 'fa-solid fa-spinner fa-spin text-orange-500 text-xs me-2 flex-shrink-0';
                    }

                    clearTimeout(autoDebounceTimer);
                    autoDebounceTimer = setTimeout(() => {
                        if (searchIcon) {
                            searchIcon.className = 'fa-solid fa-magnifying-glass text-secondary me-2 flex-shrink-0';
                        }

                        if (q.length >= 1 && autoDropdown && autoResults) {
                            fetch(`<?= site_url('peminjaman_barang/autocomplete'); ?>?type=riwayat&q=${encodeURIComponent(q)}&cat=${encodeURIComponent(currentCat)}`)
                                .then(res => res.json())
                                .then(data => {
                                    if (data && data.length > 0) {
                                        let html = '';
                                        data.forEach((item) => {
                                            const regex = new RegExp(`(${q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                                            const highlightedBarang = (item.nama_aset || '').replace(regex, '<mark>$1</mark>');
                                            const highlightedKode = (item.kode_aset || '').replace(regex, '<mark>$1</mark>');
                                            const highlightedStatus = (item.status || '').replace(regex, '<mark>$1</mark>');

                                            html += `
                                                <div class="autocomplete-item-row d-flex align-items-center justify-content-between gap-3" data-name="${item.nama_aset}">
                                                    <div class="d-flex align-items-center gap-2.5 min-w-0">
                                                        <span class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; background: rgba(234, 91, 26, 0.12); color: #ea5b1a; font-size: 13px;">
                                                            <i class="fa-solid fa-box"></i>
                                                        </span>
                                                        <div class="min-w-0">
                                                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.82rem;">
                                                                ${highlightedBarang} <span class="badge bg-light text-secondary border ms-1" style="font-size: 10px;">${highlightedKode}</span>
                                                            </div>
                                                            <div class="text-muted text-truncate" style="font-size: 11px;">
                                                                <i class="fa-solid fa-location-dot me-1 text-secondary"></i>${item.ruangan || 'Umum'} &bull; <i class="fa-solid fa-receipt ms-1 me-1 text-secondary"></i>${item.group_id}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span class="badge bg-orange-subtle text-fik-orange border border-warning-subtle flex-shrink-0" style="font-size: 10.5px; border-radius: 99px;">
                                                        ${highlightedStatus}
                                                    </span>
                                                </div>
                                            `;
                                        });
                                        autoResults.innerHTML = html;
                                        autoDropdown.classList.remove('d-none');
                                        autoDropdown.style.display = 'block';
                                        currentFocusIdx = -1;

                                        // Click item handler
                                        autoResults.querySelectorAll('.autocomplete-item-row').forEach(row => {
                                            row.onclick = function() {
                                                const selectedName = this.getAttribute('data-name');
                                                inputSearch.value = selectedName;
                                                autoDropdown.classList.add('d-none');
                                                autoDropdown.style.display = 'none';
                                                if (formSearch) formSearch.submit();
                                            };
                                        });
                                    } else {
                                        autoResults.innerHTML = `
                                            <div class="px-4 py-3 text-muted text-center italic" style="font-size: 0.8rem;">
                                                Tidak ada riwayat untuk "<strong>${q}</strong>"
                                            </div>
                                        `;
                                        autoDropdown.classList.remove('d-none');
                                        autoDropdown.style.display = 'block';
                                    }
                                })
                                .catch(() => {
                                    autoDropdown.classList.add('d-none');
                                    autoDropdown.style.display = 'none';
                                });
                        } else {
                            if (autoDropdown) {
                                autoDropdown.classList.add('d-none');
                                autoDropdown.style.display = 'none';
                            }
                        }
                    }, 220);
                });

                // Keyboard navigation
                inputSearch.addEventListener('keydown', function(e) {
                    if (!autoDropdown || autoDropdown.classList.contains('d-none')) return;
                    const items = autoResults.querySelectorAll('.autocomplete-item-row');
                    if (!items.length) return;

                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        currentFocusIdx = (currentFocusIdx + 1) % items.length;
                        items.forEach((it, i) => it.classList.toggle('active-nav', i === currentFocusIdx));
                        items[currentFocusIdx]?.scrollIntoView({ block: 'nearest' });
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        currentFocusIdx = (currentFocusIdx - 1 + items.length) % items.length;
                        items.forEach((it, i) => it.classList.toggle('active-nav', i === currentFocusIdx));
                        items[currentFocusIdx]?.scrollIntoView({ block: 'nearest' });
                    } else if (e.key === 'Enter') {
                        if (currentFocusIdx >= 0 && items[currentFocusIdx]) {
                            e.preventDefault();
                            items[currentFocusIdx].click();
                        }
                    } else if (e.key === 'Escape') {
                        autoDropdown.classList.add('d-none');
                    }
                });
            }

            // Outside click closer
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.custom-dropdown-container') && !e.target.closest('.custom-dropdown-menu')) {
                    document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
                    document.querySelectorAll('.dropdown-arrow').forEach(a => a.classList.remove('rotate-180'));
                }
                if (!e.target.closest('#autocompleteDropdownRiwayat') && !e.target.closest('#inputSearchRiwayat')) {
                    if (autoDropdown) autoDropdown.classList.add('d-none');
                }
            });
        });
    </script>
</div><!-- /#laaMainContentWrapper -->
</body>
</html>


