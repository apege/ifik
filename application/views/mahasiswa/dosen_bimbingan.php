<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard Bimbingan Dosen — IFIK Portal'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>?v=<?= time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <style>
        body, button, input, textarea, select { font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important; }

        .search-pill-container { position: relative; display: flex; align-items: center; gap: 8px; width: 100%; }
        .unified-search-pill { display: flex; align-items: center; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 2px 12px; flex: 1; height: 46px; transition: all 0.2s ease; }
        .unified-search-pill:focus-within { border-color: #ea580c !important; background: #ffffff !important; box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.12) !important; }
        .unified-divider { width: 1.5px; height: 20px; background-color: #cbd5e1; margin: 0 10px; flex-shrink: 0; }
        .search-cat-btn { display: flex; align-items: center; gap: 5px; background: transparent; border: none; font-size: 0.75rem; font-weight: 700; color: #1e293b; cursor: pointer; padding: 4px 2px; white-space: nowrap; flex-shrink: 0; }
        .search-cat-btn:hover { color: #ea580c; }
        .search-cat-menu { position: absolute; top: calc(100% + 8px); left: 0; width: 220px; background: #fff; border: 1.5px solid #e2e8f0; border-radius: 14px; box-shadow: 0 16px 40px -8px rgba(15,23,42,0.16); z-index: 200; padding: 6px; display: none; }
        .search-cat-menu.open { display: block; }
        .search-cat-item { padding: 8px 12px; border-radius: 10px; cursor: pointer; font-size: 0.75rem; font-weight: 600; color: #475569; display: flex; align-items: center; gap: 8px; }
        .search-cat-item:hover, .search-cat-item.active { background: #fff7ed; color: #ea580c; font-weight: 700; }
        .btn-search-cari { padding: 6px 16px; background: linear-gradient(135deg, #ea580c, #f97316); color: #fff; font-size: 0.75rem; font-weight: 700; border: none; border-radius: 12px; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all 0.2s ease; flex-shrink: 0; height: 36px; box-shadow: 0 2px 8px rgba(234,88,12,0.25); }
        .btn-search-cari:hover { transform: scale(1.03); box-shadow: 0 4px 14px rgba(234,88,12,0.4); }

        @keyframes spinRotatingBorder { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .table-rotating-border-wrap { position: relative; border-radius: 16px; padding: 2px; overflow: hidden; box-shadow: 0 12px 36px -8px rgba(234, 88, 12, 0.15), 0 4px 16px rgba(71, 85, 105, 0.06); background: #ffffff; }
        .table-rotating-border-spin { position: absolute; inset: -350%; pointer-events: none; opacity: 0.95; background: conic-gradient(from 90deg at 50% 50%, #ea580c 0%, #f97316 12%, #ffffff 22%, #cbd5e1 35%, #475569 48%, #1e293b 58%, #ea580c 68%, #ffffff 80%, #94a3b8 90%, #ea580c 100%); animation: spinRotatingBorder 7s linear infinite; }
        .table-rotating-border-inner { position: relative; z-index: 10; width: 100%; background: #ffffff; border-radius: 14px; overflow: hidden; }
        .table-custom-rounded { border-collapse: separate !important; border-spacing: 0 !important; width: 100%; }
        .table-custom-rounded thead tr th:first-child { border-top-left-radius: 14px; }
        .table-custom-rounded thead tr th:last-child { border-top-right-radius: 14px; }

        .badge { display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 700; font-size: 0.75rem; gap: 0.375rem; border: 1px solid transparent; }
        .badge-success { background-color: #d1fae5; color: #065f46; border-color: #34d399; }
        .badge-warning { background-color: #fef3c7; color: #92400e; border-color: #fbbf24; }
        .badge-danger { background-color: #ffe4e6; color: #9f1239; border-color: #fb7185; }
        .badge-secondary { background-color: #f1f5f9; color: #475569; border-color: #cbd5e1; }
        .badge-info { background-color: #dbeafe; color: #1e40af; border-color: #93c5fd; }
        .badge-purple { background-color: #ede9fe; color: #5b21b6; border-color: #c4b5fd; }

        .pagination-btn { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; border: 1.5px solid #e2e8f0; background: #fff; color: #475569; font-size: 0.72rem; font-weight: 800; cursor: pointer; transition: all 0.15s ease; }
        .pagination-btn:hover:not(:disabled):not(.active) { background: #fff7ed; border-color: #fed7aa; color: #ea580c; }
        .pagination-btn.active { background: linear-gradient(135deg, #ea580c, #f97316); border-color: #ea580c; color: #fff; box-shadow: 0 3px 10px rgba(234,88,12,0.3); cursor: default; }
        .pagination-btn:disabled { opacity: 0.35; cursor: not-allowed; }
        .pagination-ellipsis { padding: 0 4px; color: #94a3b8; font-weight: 800; font-size: 0.75rem; }

        @media (max-width: 768px) {
            .table-rotating-border-inner { overflow-x: visible !important; }
            .table-custom-rounded thead { display: none !important; }
            .table-custom-rounded, .table-custom-rounded tbody { display: block !important; width: 100% !important; }
            .table-custom-rounded tr { display: block !important; margin: 0 0 0.85rem 0 !important; border: 1px solid #e2e8f0 !important; border-radius: 16px !important; padding: 0.85rem 0.75rem 0.6rem !important; background: #fff !important; box-shadow: 0 4px 14px -4px rgba(15,23,42,0.08) !important; position: relative !important; }
            .table-custom-rounded tbody tr:last-child { margin-bottom: 0 !important; }
            .table-custom-rounded td { display: block !important; width: 100% !important; padding: 0.45rem 0.4rem !important; text-align: left !important; border-bottom: 1px dashed #f1f5f9 !important; position: relative !important; padding-left: 40% !important; min-height: 36px !important; font-size: 0.8rem !important; vertical-align: top !important; }
            .table-custom-rounded td:last-child { border-bottom: none !important; padding-bottom: 0.2rem !important; }
            .table-custom-rounded td::before { content: attr(data-label); position: absolute; left: 0.4rem; top: 0.5rem; width: 36%; font-weight: 800; font-size: 0.62rem; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; line-height: 1.2; }
            .table-custom-rounded td.dosen-cb-cell { position: absolute !important; top: 0.7rem !important; right: 0.7rem !important; width: auto !important; padding: 0 !important; border: none !important; padding-left: 0 !important; min-height: 0 !important; z-index: 2 !important; }
            .table-custom-rounded td.dosen-cb-cell::before { display: none !important; }
            .table-custom-rounded td.mhs-info-cell { padding: 0.2rem 2.6rem 0.6rem 0.4rem !important; border-bottom: 1px solid #e2e8f0 !important; margin-bottom: 0.3rem; }
            .table-custom-rounded td.mhs-info-cell::before { display: none !important; }
            .table-custom-rounded td.aksi-cell { text-align: right !important; padding: 0.6rem 0.4rem 0.2rem !important; padding-left: 0.4rem !important; border-top: 1px solid #f1f5f9 !important; border-bottom: none !important; margin-top: 0.3rem; }
            .table-custom-rounded td.aksi-cell::before { display: none !important; }
            .table-custom-rounded td[colspan] { display: block !important; padding: 2rem 1rem !important; padding-left: 1rem !important; text-align: center !important; border: none !important; min-height: 0 !important; }
            .table-custom-rounded td[colspan]::before { display: none !important; }
            .table-custom-rounded tbody tr:has(td[colspan]) { padding: 0 !important; border: none !important; box-shadow: none !important; background: transparent !important; }
            .table-custom-rounded td .badge { font-size: 0.7rem; }
            .table-custom-rounded td button { font-size: 0.7rem !important; }
            .table-custom-rounded td.aksi-cell > div { justify-content: flex-end !important; flex-wrap: wrap; }
        }

        #hoverPreviewPanel { position: fixed; z-index: 999; width: 520px; max-width: 95vw; background: #fff; border-radius: 20px; box-shadow: 0 24px 64px -12px rgba(234,88,12,0.18), 0 8px 24px rgba(71,85,105,0.10); border: 1.5px solid #fed7aa; overflow: hidden; display: none; pointer-events: auto; transition: opacity 0.18s, transform 0.18s; }
        #hoverPreviewPanel.visible { display: block; }
        #hoverPreviewPanel .panel-header { background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%); color: #fff; padding: 14px 18px; font-weight: 800; font-size: 13px; display: flex; align-items: center; gap: 8px; }
        #hoverPreviewPanel .pdf-frame-wrap { background: #f1f5f9; border-bottom: 1px solid #e2e8f0; height: 200px; position: relative; overflow: hidden; }
        #hoverPreviewPanel .pdf-frame-wrap iframe { width: 100%; height: 100%; border: none; }
        #hoverPreviewPanel .pdf-no-file { display: flex; align-items: center; justify-content: center; height: 200px; color: #94a3b8; font-size: 13px; font-weight: 600; flex-direction: column; gap: 8px; }
        #hoverPreviewPanel .panel-body { padding: 16px 18px 18px; max-height: 400px; overflow-y: auto; }
        #hoverPreviewPanel .panel-label { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: #94a3b8; margin-bottom: 6px; }

        @keyframes popInCard { 0% { opacity: 0; transform: scale(0.9) translateY(24px); } 100% { opacity: 1; transform: scale(1) translateY(0); } }
        @keyframes fadeInSlideRight { 0% { opacity: 0; transform: scale(0.95) translateX(60px); } 100% { opacity: 1; transform: scale(1) translateX(0); } }
        @keyframes fadeInDownSmooth { 0% { opacity: 0; transform: translateY(-16px); } 100% { opacity: 1; transform: translateY(0); } }
        .animate-pop-in { animation: popInCard 0.8s cubic-bezier(0.2, 0.9, 0.2, 1) forwards; }
        .animate-preview-in { animation: fadeInSlideRight 1.1s cubic-bezier(0.2, 0.9, 0.2, 1) forwards; }
        .animate-bar-in { animation: fadeInDownSmooth 0.8s cubic-bezier(0.2, 0.9, 0.2, 1) forwards; }

        #lihatBerkasContainer { position: fixed; inset: 0; pointer-events: none; z-index: 50; display: none; align-items: stretch; justify-content: center; padding: 1rem; gap: 1.5rem; overflow: hidden; background: rgba(15, 23, 42, 0.15); backdrop-filter: blur(0.5px); }
        #lihatBerkasContainer.active { display: flex; }

        #wrapperDaftarMhs { display: flex; flex-direction: column; gap: 1rem; width: 30%; min-width: 320px; max-width: 420px; overflow-y: auto; overflow-x: hidden; flex-shrink: 0; padding: 0.25rem; max-height: 90vh; pointer-events: none; }
        #wrapperDaftarMhs::-webkit-scrollbar { width: 4px; }
        #wrapperDaftarMhs::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.5); border-radius: 9999px; }

        #wrapperPreviewBerkas { display: none; flex: 1; overflow-x: auto; overflow-y: hidden; gap: 1rem; padding: 0.25rem; align-items: stretch; max-height: 90vh; pointer-events: none; }
        #wrapperPreviewBerkas.active { display: flex; }
        #wrapperPreviewBerkas::-webkit-scrollbar { height: 4px; }
        #wrapperPreviewBerkas::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.5); border-radius: 9999px; }

        .student-card-item { pointer-events: auto; background: white; border-radius: 1.5rem; box-shadow: 0 20px 40px -12px rgba(0,0,0,0.25); border: 1px solid #e2e8f0; overflow: hidden; display: flex; flex-direction: column; flex-shrink: 0; width: 100%; max-height: 90vh; }

        .preview-card-item { pointer-events: auto; background: white; border-radius: 1.5rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3); border: 1px solid #e2e8f0; overflow: hidden; display: flex; flex-direction: column; flex-shrink: 0; width: 520px; max-width: 70vw; height: 85vh; max-height: 90vh; }
        .preview-card-item .preview-body { flex: 1; min-height: 0; position: relative; background: #e2e8f0; overflow: hidden; }
        .preview-card-item .preview-body iframe { width: 100%; height: 100%; border: 0; position: relative; z-index: 10; }
        .preview-card-item .preview-body .loader { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; z-index: 5; background: #e2e8f0; color: #94a3b8; font-size: 0.75rem; font-weight: 600; gap: 0.5rem; }
        .preview-card-item .preview-body .loader i { font-size: 1.5rem; animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .preview-card-item .preview-header { flex-shrink: 0; }
        .preview-card-item .preview-footer { flex-shrink: 0; gap: 0.5rem; flex-wrap: wrap; }
        .btn-3d-orange { background: linear-gradient(135deg, #ea580c, #f97316); color: #fff; border: none; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(234,88,12,0.3); }
        .btn-3d-orange:hover { transform: scale(1.03); box-shadow: 0 6px 20px rgba(234,88,12,0.4); }

        #commentActionModal { position: fixed; inset: 0; z-index: 1000; display: none; align-items: center; justify-content: center; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); padding: 1rem; }
        #commentActionModal.active { display: flex; }
        #commentActionModal .modal-box { background: white; border-radius: 2rem; max-width: 600px; width: 100%; max-height: 90vh; overflow: hidden; box-shadow: 0 40px 80px -20px rgba(0,0,0,0.4); display: flex; flex-direction: column; }
        #commentActionModal .modal-header { padding: 1.25rem 1.5rem; background: #1e293b; color: white; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; transition: background 0.25s ease; }
        #commentActionModal .modal-header.p2-theme { background: linear-gradient(135deg, #4338ca, #6366f1); }
        #commentActionModal .modal-header.p1-approve-theme { background: linear-gradient(135deg, #047857, #10b981); }
        #commentActionModal .modal-header.p1-revision-theme { background: linear-gradient(135deg, #9f1239, #f43f5e); }
        #commentActionModal .modal-header.u1-theme { background: linear-gradient(135deg, #047857, #10b981); }
        #commentActionModal .modal-header.u2-theme { background: linear-gradient(135deg, #6d28d9, #a855f7); }
        #commentActionModal .modal-body { padding: 1.5rem; overflow-y: auto; flex: 1; }
        #commentActionModal .modal-footer { padding: 1rem 1.5rem; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 0.75rem; flex-shrink: 0; }

        .p3-sub-badge { display: inline-flex; align-items: center; gap: 3px; padding: 2px 7px; border-radius: 6px; font-size: 10px; font-weight: 800; line-height: 1.2; letter-spacing: 0.02em; }
        .p3-sub-approved { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
        .p3-sub-pending  { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
        .p3-sub-revision { background: #ffe4e6; color: #9f1239; border: 1px solid #fda4af; }
        .p3-sub-none     { background: #f1f5f9; color: #94a3b8; border: 1px solid #cbd5e1; }

        @media (max-width: 768px) {
            #lihatBerkasContainer { flex-direction: column !important; align-items: stretch !important; justify-content: flex-start !important; overflow-y: auto !important; overflow-x: hidden !important; padding: 0.75rem !important; gap: 0.75rem !important; background: rgba(15, 23, 42, 0.25) !important; }
            #wrapperDaftarMhs { width: 100% !important; min-width: 0 !important; max-width: 100% !important; max-height: none !important; overflow-y: visible !important; padding: 0 !important; }
            #wrapperPreviewBerkas { width: 100% !important; flex: none !important; max-height: none !important; overflow-x: auto !important; overflow-y: visible !important; padding: 0 !important; }
            .preview-card-item { width: 86vw !important; max-width: 86vw !important; height: 55vh !important; max-height: 55vh !important; }
            .student-card-item { max-height: none !important; }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-amber-50/40 via-orange-50/25 to-slate-100 min-h-screen text-slate-800 antialiased flex flex-col justify-between selection:bg-orange-500 selection:text-white">

    <?php $this->load->view('partials/dosen_sidebar'); ?>

    <main class="w-full px-4 sm:px-6 lg:px-10 py-6 sm:py-8 flex-grow space-y-7">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="p-5 rounded-3xl bg-emerald-50 border-2 border-emerald-300 text-emerald-900 text-sm font-semibold flex items-center justify-between shadow-md shadow-emerald-500/10">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg shrink-0 box-3d"><i class="bi bi-check-circle-fill"></i></div>
                    <span><?= $this->session->flashdata('success'); ?></span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold text-2xl leading-none">&times;</button>
            </div>
        <?php endif; ?>

        <div class="bg-gradient-to-r from-[#9a3412] via-[#ea580c] to-[#c2410c] rounded-3xl p-7 sm:p-9 relative overflow-hidden shadow-2xl text-white">
            <div class="relative z-10 flex flex-col xl:flex-row items-start xl:items-center justify-between gap-8">
                <div class="space-y-4 max-w-3xl">
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">Dashboard Bimbingan Dosen</h1>
                    <p class="text-sm sm:text-base text-orange-100/95 font-normal leading-relaxed">Kelola dan evaluasi dokumen Tugas Akhir mahasiswa bimbingan Anda.</p>
                </div>
                <div class="w-full xl:w-[400px] bg-black/25 backdrop-blur-xl rounded-3xl p-6 border border-white/20 shadow-2xl space-y-4 text-white">
                    <div class="flex gap-4">
                        <a href="<?= site_url('dosen/bimbingan?posisi=1') ?>" class="flex-1 py-3 px-4 rounded-2xl font-bold text-center border <?= $posisi == 1 ? 'bg-orange-500 border-orange-400 text-white shadow-lg' : 'bg-white/10 border-white/20 hover:bg-white/20' ?> transition">
                            <i class="bi bi-person-fill mr-2"></i> Sebagai P1
                        </a>
                        <a href="<?= site_url('dosen/bimbingan?posisi=2') ?>" class="flex-1 py-3 px-4 rounded-2xl font-bold text-center border <?= $posisi == 2 ? 'bg-orange-500 border-orange-400 text-white shadow-lg' : 'bg-white/10 border-white/20 hover:bg-white/20' ?> transition">
                            <i class="bi bi-person mr-2"></i> Sebagai P2
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-7 sm:p-9 shadow-lg shadow-orange-500/5 space-y-7 border border-slate-200">
            <div class="flex flex-wrap items-center justify-between border-b border-orange-100 pb-5">
                <div>
                    <h3 class="text-xl sm:text-2xl font-bold text-slate-900"><i class="bi bi-people-fill text-orange-500 text-xl"></i> Daftar Mahasiswa Bimbingan (Pembimbing <?= $posisi ?>)</h3>
                </div>
            </div>

            <div class="flex flex-wrap gap-4 border-b border-slate-200 pb-4">
                <button onclick="switchDosenTab('preview1')" id="dosenTab1" class="px-5 py-2.5 rounded-xl font-bold text-sm bg-orange-100 text-orange-700 border border-orange-300">Preview 1</button>
                <button onclick="switchDosenTab('preview2')" id="dosenTab2" class="px-5 py-2.5 rounded-xl font-bold text-sm bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200">Preview 2</button>
                <button onclick="switchDosenTab('preview3')" id="dosenTab3" class="px-5 py-2.5 rounded-xl font-bold text-sm bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200">Preview 3</button>
                <button onclick="switchDosenTab('preview4')" id="dosenTab4" class="px-5 py-2.5 rounded-xl font-bold text-sm bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200">Preview 4 / Sidang</button>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mt-4 mb-2" id="filterCardsContainer">
                <div onclick="setDosenFilter('all')" id="fCard_all" class="p-3 rounded-xl border border-orange-300 bg-orange-50 cursor-pointer transition text-center shadow-xs">
                    <div class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Semua</div>
                    <div class="text-xl font-black text-slate-800" id="fCount_all">0</div>
                </div>
                <div onclick="setDosenFilter('approved')" id="fCard_approved" class="p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-emerald-50 hover:border-emerald-200 cursor-pointer transition text-center">
                    <div class="text-[10px] text-emerald-600 font-bold uppercase tracking-wider">Disetujui</div>
                    <div class="text-xl font-black text-emerald-700" id="fCount_approved">0</div>
                </div>
                <div onclick="setDosenFilter('pending')" id="fCard_pending" class="p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-amber-50 hover:border-amber-200 cursor-pointer transition text-center">
                    <div class="text-[10px] text-amber-600 font-bold uppercase tracking-wider">Pending</div>
                    <div class="text-xl font-black text-amber-700" id="fCount_pending">0</div>
                </div>
                <div onclick="setDosenFilter('revision')" id="fCard_revision" class="p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-rose-50 hover:border-rose-200 cursor-pointer transition text-center">
                    <div class="text-[10px] text-rose-600 font-bold uppercase tracking-wider">Revisi</div>
                    <div class="text-xl font-black text-rose-700" id="fCount_revision">0</div>
                </div>
                <div onclick="setDosenFilter('empty')" id="fCard_empty" class="p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 cursor-pointer transition text-center">
                    <div class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Kosong</div>
                    <div class="text-xl font-black text-slate-700" id="fCount_empty">0</div>
                </div>
            </div>

            <div id="dosenTableContainer" class="space-y-4">
                <div class="relative search-pill-container" id="multiSearchWrapper">
                    <div class="unified-search-pill" style="position: relative;">
                        <div class="relative" id="searchCatWrap">
                            <button type="button" class="search-cat-btn" id="searchCatBtn" onclick="toggleSearchCatMenu()">
                                <span id="searchCatLabel">🔍 Kata Kunci</span>
                                <i class="bi bi-chevron-down text-[10px] text-slate-400"></i>
                            </button>
                            <div class="search-cat-menu" id="searchCatMenu">
                                <div class="search-cat-item active" data-val="all" onclick="setSearchCat(this, 'all', '🔍 Kata Kunci')">🔍 Kata Kunci (Semua)</div>
                                <div class="search-cat-item" data-val="nama" onclick="setSearchCat(this, 'nama', '🏷️ Nama')">🏷️ Nama Mahasiswa</div>
                                <div class="search-cat-item" data-val="nim" onclick="setSearchCat(this, 'nim', '🆔 NIM')">🆔 NIM Mahasiswa</div>
                                <div class="search-cat-item" data-val="judul" onclick="setSearchCat(this, 'judul', '📖 Judul TA')">📖 Judul Tugas Akhir</div>
                            </div>
                        </div>
                        <div class="unified-divider"></div>
                        <div class="flex-1 flex items-center min-w-0">
                            <i class="bi bi-search text-slate-400 text-sm mr-2 shrink-0"></i>
                            <input type="text" id="searchInput" placeholder="Ketik kata kunci lalu klik Cari atau tekan Enter..." onkeydown="if(event.key==='Enter'){doSearch();}" class="w-full text-sm font-medium bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400">
                        </div>
                        <button type="button" class="btn-search-cari ml-2" onclick="doSearch()"><i class="bi bi-search text-xs"></i> Cari</button>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-1 px-1" id="paginationControls">
                    <div class="flex items-center gap-2 flex-wrap">
                        <label class="text-[11px] font-bold text-slate-600 whitespace-nowrap">Tampilkan:</label>
                        <select id="perPageSelect" onchange="changePerPage()" class="px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs font-bold bg-white focus:ring-orange-500 focus:border-orange-500 cursor-pointer">
                            <option value="10">10</option>
                            <option value="25" selected>25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="all">Semua</option>
                        </select>
                        <span class="text-[11px] text-slate-500 font-medium" id="paginationInfo"></span>
                    </div>
                    <div class="flex items-center gap-1 flex-wrap" id="paginationButtons"></div>
                </div>

                <div class="table-rotating-border-wrap mt-2">
                    <span class="table-rotating-border-spin"></span>
                    <div class="table-rotating-border-inner overflow-x-auto">
                        <table class="table-custom-rounded text-left text-sm w-full">
                            <thead class="bg-slate-50 text-slate-700 font-semibold text-xs uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="py-4 px-4 text-center w-12">
                                        <input type="checkbox" id="checkAllDosenStudents" class="w-4 h-4 text-orange-600 rounded border-slate-300 focus:ring-orange-500 cursor-pointer">
                                    </th>
                                    <th class="py-4 px-4 font-bold">Mahasiswa & Judul TA</th>
                                    <th class="py-4 px-4">Berkas Terbaru</th>
                                    <th class="py-4 px-4 text-center">Waktu Upload</th>
                                    <th class="py-4 px-4 text-center">Status Review</th>
                                    <?php if($posisi == 1): ?><th class="py-4 px-4 text-center">Rekomendasi</th><?php endif; ?>
                                    <th class="py-4 px-4 text-center">Komentar / Revisi</th>
                                    <th class="py-4 px-4 pr-6 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium bg-white" id="bimbinganTableBody">
                                <tr><td colspan="<?= $posisi == 1 ? 8 : 7 ?>" class="text-center py-10 text-slate-500"><i class="bi bi-arrow-repeat animate-spin mr-2"></i> Memuat data...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="reviewModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeReviewModal()"></div>
                <div class="relative bg-white rounded-3xl p-6 sm:p-8 w-full max-w-2xl shadow-2xl transform transition-all">
                    <button onclick="closeReviewModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 text-2xl leading-none">&times;</button>
                    <h3 class="text-xl font-bold text-slate-900 mb-4 flex items-center gap-2 border-b pb-4"><i class="bi bi-pencil-square text-orange-500"></i> <span id="modalTahapTitle">Review Berkas</span></h3>
                    <div class="mb-5 bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <p class="text-sm font-bold text-slate-800 mb-1" id="modalStudentName">Nama Mahasiswa (NIM)</p>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Judul: <span id="modalJudul" class="text-slate-700 font-bold normal-case"></span></p>
                        <p class="text-sm text-slate-600 italic mb-3 border-l-2 border-orange-300 pl-3">Catatan Mahasiswa: "<span id="modalStudentNotes"></span>"</p>
                    </div>
                    <form id="formReview" action="<?= site_url('mahasiswa/review_preview') ?>" method="POST" class="space-y-4">
                        <input type="hidden" name="id_preview" id="modalIdPreview" value="">
                        <input type="hidden" name="posisi" value="<?= $posisi ?>">
                        <?php if($posisi == 1): ?>
                        <div class="bg-blue-50 p-4 rounded-xl border border-blue-200 mb-4 hidden" id="modalRiwayatContainer">
                            <p class="text-sm font-bold text-blue-900 mb-1"><i class="bi bi-clock-history"></i> Catatan Sebelumnya:</p>
                            <p class="text-xs text-blue-800 italic" id="modalCatatanSebelumnya"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Status Penilaian (P1)</label>
                            <select name="status_pembimbing" id="modalStatus" class="w-full p-3 rounded-xl border border-slate-300 focus:ring-orange-500 focus:border-orange-500 text-sm font-semibold">
                                <option value="Pending">Menunggu Review</option>
                                <option value="Approved">Disetujui (ACC)</option>
                                <option value="Revision">Perlu Revisi</option>
                            </select>
                        </div>
                        <?php endif; ?>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Catatan / Feedback Anda</label>
                            <textarea name="catatan_pembimbing" id="modalCatatan" rows="4" class="w-full p-3 rounded-xl border border-slate-300 focus:ring-orange-500 focus:border-orange-500 text-sm font-medium" placeholder="Tuliskan feedback atau arahan revisi di sini..."></textarea>
                        </div>
                        <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                            <button type="button" onclick="closeReviewModal()" class="px-5 py-2.5 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition">Batal</button>
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold shadow-md transition transform hover:scale-105 active:scale-95">Simpan Review</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="lihatBerkasContainer" class="fixed inset-0 pointer-events-none z-50 flex items-stretch p-3 sm:p-5 gap-4 overflow-hidden" style="display: none;">
                <div id="wrapperDaftarMhs" class="flex flex-col gap-3 w-[30%] min-w-[320px] max-w-[420px] overflow-y-auto flex-shrink-0"></div>
                <div id="wrapperPreviewBerkas" class="flex-1 overflow-x-auto overflow-y-hidden gap-4 flex items-stretch hidden"></div>
            </div>

            <div id="commentModal" class="hidden fixed inset-0 z-[200] items-center justify-center p-4">
                <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeCommentModal()"></div>
                <div class="relative bg-white rounded-3xl p-6 sm:p-8 w-full max-w-lg shadow-2xl flex flex-col max-h-[90vh]">
                    <button onclick="closeCommentModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700 text-2xl leading-none">&times;</button>
                    <h3 class="text-lg font-bold text-slate-900 mb-1 flex items-center gap-2" id="commentModalTitle"><i class="bi bi-chat-quote-fill text-indigo-500"></i> Komentar Dosen</h3>
                    <p class="text-xs text-slate-500 font-semibold mb-4" id="commentModalName">Nama Mahasiswa</p>
                    <div class="flex flex-wrap gap-1.5 mb-4 border-b border-slate-200 pb-3" id="commentTabsContainer">
                        <button onclick="switchCommentTab('p1')" id="commentTabP1" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-orange-100 text-orange-700 border border-orange-300 transition">Pembimbing 1</button>
                        <button onclick="switchCommentTab('p2')" id="commentTabP2" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200 transition">Pembimbing 2</button>
                        <button onclick="switchCommentTab('u1')" id="commentTabU1" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200 transition">Penguji 1</button>
                        <button onclick="switchCommentTab('u2')" id="commentTabU2" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200 transition">Penguji 2</button>
                    </div>
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-800 font-medium leading-relaxed overflow-y-auto flex-1" id="commentModalContent"></div>
                </div>
            </div>

            <div id="commentActionModal" class="fixed inset-0 z-[1000] items-center justify-center p-4" style="display:none;">
                <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeCommentActionModal()"></div>
                <div class="modal-box relative">
                    <div class="modal-header" id="commentActionHeader">
                        <h3 class="text-sm font-extrabold flex items-center gap-2">
                            <i class="bi bi-chat-text text-orange-400" id="commentActionIcon"></i>
                            <span id="commentActionTitle">Beri Komentar</span>
                        </h3>
                        <button type="button" onclick="closeCommentActionModal()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition cursor-pointer">
                            <i class="bi bi-x-lg text-sm"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="text-xs text-slate-500 font-medium mb-3" id="commentActionSubtitle">Komentar bersifat opsional. Kosongkan jika tidak perlu.</p>
                        <textarea id="commentActionTextarea" rows="6" class="w-full p-3 rounded-xl border border-slate-300 focus:ring-orange-500 focus:border-orange-500 text-sm font-medium" placeholder="Tuliskan komentar atau alasan persetujuan/revisi di sini..."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="closeCommentActionModal()" class="px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded-xl transition cursor-pointer">Batal</button>
                        <button type="button" id="commentActionSubmit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold shadow-md transition flex items-center gap-2 cursor-pointer">
                            <i class="bi bi-check-lg"></i> Simpan & Proses
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <div id="hoverPreviewPanel">
        <div class="panel-header">
            <i class="bi bi-person-circle"></i>
            <span id="hoverPanelName">Nama Mahasiswa</span>
        </div>
        <div class="pdf-frame-wrap" id="hoverPdfWrap">
            <div class="pdf-no-file"><i class="bi bi-file-earmark-x text-3xl"></i><span>Belum ada berkas</span></div>
        </div>
        <div class="panel-body">
            <div id="hoverFormWrap"></div>
        </div>
    </div>

    <div id="dosenBatchActionBar" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 bg-slate-900/95 text-white px-5 py-3 rounded-2xl shadow-2xl backdrop-blur-md border border-slate-700 hidden flex-wrap items-center gap-4 transition-all duration-300">
        <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-orange-500 text-white font-black text-xs flex items-center justify-center shadow-xs" id="dosenSelectedCountBadge">0</span>
            <span class="text-xs font-bold tracking-tight">Mahasiswa Terpilih</span>
        </div>
        <div class="h-5 w-px bg-slate-700 hidden sm:block"></div>
        <div class="flex items-center gap-2.5">
            <button type="button" onclick="openDosenBatchModal()" class="px-4 py-2 bg-gradient-to-r from-orange-500 to-amber-600 hover:from-orange-600 hover:to-amber-700 text-white rounded-xl text-xs font-extrabold shadow-md flex items-center gap-2 transition-all active:scale-95 cursor-pointer">
                <i class="bi bi-files"></i> Preview Massal
            </button>
            <button type="button" onclick="submitDosenBatchApprove()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md flex items-center gap-1.5 transition-all active:scale-95 cursor-pointer">
                <i class="bi bi-check2-all"></i> Approve Massal
            </button>
            <button type="button" onclick="unselectAllDosenStudents()" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold transition-all cursor-pointer">
                <i class="bi bi-x-lg"></i> Batal
            </button>
        </div>
    </div>

    <div id="dosenBatchReviewModal" class="hidden fixed inset-0 z-[100] overflow-y-auto p-4 sm:p-6">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeDosenBatchModal()"></div>
        <div class="relative bg-white rounded-3xl w-full max-w-6xl mx-auto flex flex-col overflow-hidden shadow-2xl my-4 sm:my-8">
            <div class="p-4 px-6 bg-slate-900 text-white flex items-center justify-between shrink-0">
                <h3 class="text-sm font-extrabold flex items-center gap-2"><i class="bi bi-files text-orange-500"></i> Review Preview Massal</h3>
                <button type="button" onclick="closeDosenBatchModal()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition-all cursor-pointer">
                    <i class="bi bi-x-lg text-sm"></i>
                </button>
            </div>
            <div class="p-5 sm:p-6 flex-1 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5" id="dosenBatchModalBody"></div>
            <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between shrink-0">
                <span class="text-xs text-slate-500 font-medium">Simpan setiap kartu secara individual.</span>
                <button type="button" onclick="closeDosenBatchModal()" class="px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded-xl transition cursor-pointer">Tutup</button>
            </div>
        </div>
    </div>

    <?php $this->load->view('partials/modal_rekomendasi_sidang'); ?>

    <!-- ========================================================= -->
    <!-- MODAL PENILAIAN SIDANG TA (Preview 4)                     -->
    <!-- ========================================================= -->
    <div id="modalPenilaianSidang" class="fixed inset-0 z-[100000] items-center justify-center p-4 sm:p-6 overflow-hidden" style="display:none;">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" onclick="closeModalPenilaianSidang()"></div>

        <div class="relative z-10 bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-2xl max-w-4xl w-full max-h-[92vh] flex flex-col overflow-hidden">
            <div class="p-5 sm:p-6 px-7 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-amber-50/90 via-orange-50/40 to-white shrink-0">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-600 text-white flex items-center justify-center font-bold text-xl shadow-md shadow-amber-500/25 shrink-0">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-base sm:text-lg font-extrabold text-slate-900">Form Penilaian Akhir Sidang Tugas Akhir</h3>
                            <span id="penilaianProdiBadge" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">DKV</span>
                        </div>
                        <p class="text-xs font-medium text-slate-500 mt-0.5">Penilaian resmi disesuaikan dengan kriteria Program Studi &amp; Peminatan.</p>
                    </div>
                </div>
                <button type="button" onclick="closeModalPenilaianSidang()" class="w-9 h-9 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 hover:bg-slate-200 flex items-center justify-center transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form id="formPenilaianSidang" onsubmit="submitPenilaianSidang(event)" class="p-6 sm:p-8 space-y-6 overflow-y-auto flex-1">
                <input type="hidden" name="nim" id="penilaianNim">

                <div class="bg-slate-50/80 rounded-2xl p-4 sm:p-5 border border-slate-200/80 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200/60 pb-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 id="penilaianNamaMhs" class="text-sm font-extrabold text-slate-900">-</h4>
                                <span id="penilaianVersiBadge" class="px-2 py-0.5 rounded-full text-[10px] font-black bg-indigo-100 text-indigo-800 border border-indigo-200">v1</span>
                            </div>
                            <p class="text-xs font-mono font-bold text-slate-500" id="penilaianNimMhs">-</p>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap text-[11px]">
                            <span class="px-3 py-1 rounded-xl bg-white border border-slate-200 text-slate-700 font-semibold flex items-center gap-1.5 shadow-2xs">
                                <i class="fa-solid fa-calendar-day text-amber-500"></i> <span id="penilaianTglText">Belum Ada Jadwal</span>
                            </span>
                            <span class="px-3 py-1 rounded-xl bg-cyan-50 border border-cyan-200 text-cyan-800 font-bold flex items-center gap-1.5 shadow-2xs">
                                <i class="fa-solid fa-door-open text-cyan-600"></i> <span id="penilaianRuanganText">-</span>
                            </span>
                        </div>
                    </div>

                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Judul Tugas Akhir:</p>
                        <p id="penilaianJudulTa" class="text-xs font-medium text-slate-800 mt-0.5 leading-relaxed">-</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 text-[11px]">
                        <div class="p-2.5 rounded-xl bg-white border border-slate-200/70 space-y-1">
                            <span class="font-bold text-orange-800 block text-[10px] uppercase tracking-wider"><i class="fa-solid fa-user-tie text-orange-600 mr-1"></i> Dosen Pembimbing</span>
                            <p class="text-slate-700 truncate" id="penilaianPembimbing1">Pembimbing 1: -</p>
                            <p class="text-slate-700 truncate" id="penilaianPembimbing2">Pembimbing 2: -</p>
                        </div>
                        <div class="p-2.5 rounded-xl bg-white border border-slate-200/70 space-y-1">
                            <span class="font-bold text-indigo-800 block text-[10px] uppercase tracking-wider"><i class="fa-solid fa-chalkboard-user text-indigo-600 mr-1"></i> Dewan Penguji</span>
                            <p class="text-slate-700 truncate" id="penilaianPenguji1">Penguji 1: -</p>
                            <p class="text-slate-700 truncate" id="penilaianPenguji2">Penguji 2: -</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="text-xs font-extrabold uppercase tracking-wider text-slate-700 block mb-1.5">Program Studi (Prodi) <span class="text-rose-500">*</span></label>
                        <select name="prodi" id="penilaianProdiSelect" onchange="onPenilaianProdiChange(this.value)" class="w-full px-3.5 py-3 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none shadow-2xs cursor-pointer">
                            <option value="DKV">DKV - Desain Komunikasi Visual</option>
                            <option value="DI">DI - Desain Interior</option>
                            <option value="DIB">DIB - Desain Interior Bisnis</option>
                            <option value="DP">DP - Desain Produk</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-extrabold uppercase tracking-wider text-slate-700 block mb-1.5">Peminatan / Konsentrasi <span class="text-rose-500">*</span></label>
                        <select name="peminatan" id="penilaianPeminatanSelect" onchange="onPenilaianPeminatanChange(this.value)" class="w-full px-3.5 py-3 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none shadow-2xs cursor-pointer"></select>
                    </div>

                    <div>
                        <label class="text-xs font-extrabold uppercase tracking-wider text-slate-700 block mb-1.5">Tahun Akademik <span class="text-rose-500">*</span></label>
                        <?php
                            $cYear = (int)date('Y');
                            $cMonth = (int)date('n');
                            $defaultActiveTa = ($cMonth >= 8) ? "{$cYear}/" . ($cYear + 1) : ($cYear - 1) . "/{$cYear}";
                        ?>
                        <input type="text" name="tahun_akademik" id="penilaianTahunAkademik" list="listTahunAkademik" value="<?= $defaultActiveTa; ?>" placeholder="Contoh: 2026/2027" class="w-full px-3.5 py-3 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none shadow-2xs" required>
                        <datalist id="listTahunAkademik"></datalist>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <label class="text-xs font-extrabold uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
                            <i class="fa-solid fa-list-check text-amber-600"></i> Rubrik Soal &amp; Kriteria Penilaian:
                        </label>
                        <span class="text-[11px] font-semibold text-slate-400">Total Bobot: <strong class="text-slate-700" id="penilaianTotalBobotLabel">100%</strong></span>
                    </div>

                    <div id="penilaianRubrikContainer" class="space-y-3"></div>
                </div>

                <div class="bg-gradient-to-br from-amber-500/10 via-orange-500/5 to-white rounded-2xl p-5 border border-amber-300/80 shadow-xs">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
                        <div class="text-center sm:text-left">
                            <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider block">Nilai Akhir Sidang</span>
                            <div class="flex items-baseline gap-1.5 justify-center sm:justify-start mt-0.5">
                                <span id="penilaianTotalScore" class="text-3xl font-black text-slate-900">0.00</span>
                                <span class="text-xs font-bold text-slate-400">/ 100</span>
                            </div>
                        </div>
                        <div class="text-center">
                            <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider block">Grade Mutu</span>
                            <div class="mt-1 flex items-center justify-center">
                                <span id="penilaianGradeBadge" class="px-4 py-1 rounded-xl text-lg font-black bg-slate-100 text-slate-600 border border-slate-200 shadow-2xs">-</span>
                            </div>
                        </div>
                        <div>
                            <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider block mb-1">Status Kelulusan</span>
                            <select name="status_kelulusan" id="penilaianStatusKelulusan" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-extrabold text-slate-800 focus:ring-2 focus:ring-amber-500 outline-none cursor-pointer">
                                <option value="Lulus">✅ Lulus</option>
                                <option value="Lulus dengan Revisi">⚠️ Lulus dengan Revisi</option>
                                <option value="Tidak Lulus">❌ Tidak Lulus (Sidang Ulang)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-indigo-50/50 via-slate-50 to-white rounded-2xl p-5 border border-indigo-100 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-indigo-100/80 pb-3">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-bullhorn text-indigo-600"></i>
                            <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Pengaturan Publikasi Nilai</h4>
                        </div>
                        <span id="currentPublishStatusPill" class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700 border border-slate-200">Draft (Privat)</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[11px] font-extrabold text-slate-700 block mb-1.5 uppercase tracking-wider">Status Publikasi <span class="text-rose-500">*</span></label>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 bg-white hover:border-slate-300 cursor-pointer transition text-xs font-semibold text-slate-800">
                                    <input type="radio" name="status_publish" value="Draft" id="publishRadioDraft" checked onchange="onPublishModeChange('Draft')" class="text-indigo-600 focus:ring-indigo-500">
                                    <div>
                                        <span class="font-bold text-slate-900 block">Simpan sebagai Draft</span>
                                        <span class="text-[10px] text-slate-500 font-normal">Hanya terlihat oleh Anda, belum dibagikan ke mahasiswa.</span>
                                    </div>
                                </label>
                                <label class="flex items-center gap-2.5 p-2.5 rounded-xl border border-emerald-200 bg-emerald-50/30 hover:bg-emerald-50/60 cursor-pointer transition text-xs font-semibold text-slate-800">
                                    <input type="radio" name="status_publish" value="Published" id="publishRadioInstant" onchange="onPublishModeChange('Published')" class="text-emerald-600 focus:ring-emerald-500">
                                    <div>
                                        <span class="font-bold text-emerald-900 block">Publikasikan Sekarang (Live)</span>
                                        <span class="text-[10px] text-emerald-700 font-normal">Nilai langsung dapat dilihat oleh mahasiswa.</span>
                                    </div>
                                </label>
                                <label class="flex items-center gap-2.5 p-2.5 rounded-xl border border-sky-200 bg-sky-50/30 hover:bg-sky-50/60 cursor-pointer transition text-xs font-semibold text-slate-800">
                                    <input type="radio" name="status_publish" value="Scheduled" id="publishRadioScheduled" onchange="onPublishModeChange('Scheduled')" class="text-sky-600 focus:ring-sky-500">
                                    <div>
                                        <span class="font-bold text-sky-900 block">Jadwalkan Publikasi (Start Date)</span>
                                        <span class="text-[10px] text-sky-700 font-normal">Nilai baru akan terbuka otomatis setelah tanggal yang ditentukan.</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <label class="text-[11px] font-extrabold text-slate-700 block mb-1.5 uppercase tracking-wider">Tanggal &amp; Jam Mulai Rilis</label>
                                <input type="datetime-local" name="tgl_publish" id="penilaianTglPublish" class="w-full px-3.5 py-3 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none shadow-2xs">
                                <p class="text-[10px] text-slate-500 mt-1" id="publishDateHint">Hanya aktif saat mode "Jadwalkan Publikasi" dipilih.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-extrabold uppercase tracking-wider text-slate-700 block mb-1.5">Catatan Revisi / Berita Acara (Opsional)</label>
                    <textarea name="catatan_sidang" id="penilaianCatatan" rows="2" placeholder="Masukkan poin-poin revisi naskah/karya atau catatan berita acara sidang..." class="w-full text-xs p-3 border border-slate-300 rounded-xl bg-white focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 resize-none shadow-2xs"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3 flex-wrap">
                    <button type="button" onclick="closeModalPenilaianSidang()" class="px-5 py-3 bg-white border border-slate-300 text-slate-700 font-bold text-xs sm:text-sm rounded-2xl hover:bg-slate-50 transition cursor-pointer">Batal</button>
                    <button type="submit" id="btnSubmitPenilaianSidang" class="px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold text-xs sm:text-sm rounded-2xl shadow-md shadow-amber-500/20 transition flex items-center gap-2 cursor-pointer active:scale-95">
                        <i class="fa-solid fa-floppy-disk text-xs sm:text-sm"></i> Simpan Penilaian Sidang TA
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ============================================================
        // GLOBAL VARIABLES
        // ============================================================
        let currentTahap = 'Preview 1';
        let bimbinganData = [];
        let currentDosenFilter = 'all';
        let searchCategory = 'all';
        let activeKeyword = '';

        let currentPage = 1;
        let perPage = 25;

        window.activeLihatBerkasIndices = [];
        window.activePreviews = [];

        let pendingAction = null;

        // ============================================================
        // SEARCH
        // ============================================================
        function toggleSearchCatMenu() { document.getElementById('searchCatMenu').classList.toggle('open'); }
        function setSearchCat(el, val, label) {
            searchCategory = val;
            document.getElementById('searchCatLabel').textContent = label;
            document.querySelectorAll('.search-cat-item').forEach(i => i.classList.remove('active'));
            el.classList.add('active');
            document.getElementById('searchCatMenu').classList.remove('open');
            document.getElementById('searchInput').focus();
        }
        function doSearch() { activeKeyword = document.getElementById('searchInput').value.trim().toLowerCase(); currentPage = 1; renderTable(); }

        // ============================================================
        // TINYMCE
        // ============================================================
        let commentEditor = null;
        function initCommentTinyMCE() {
            if (commentEditor) { try { commentEditor.destroy(); } catch (e) {} commentEditor = null; }
            if (typeof tinymce !== 'undefined' && tinymce.get('commentActionTextarea')) { try { tinymce.get('commentActionTextarea').remove(); } catch (e) {} }
            tinymce.init({
                selector: '#commentActionTextarea',
                menubar: false, statusbar: false,
                plugins: 'lists link',
                toolbar: 'bold italic underline | bullist numlist | link',
                height: 200, skin: 'oxide',
                setup: function (editor) {
                    commentEditor = editor;
                    editor.on('change', function () { tinymce.triggerSave(); });
                }
            });
        }

        // ============================================================
        // PAGINATION
        // ============================================================
        function changePerPage() {
            const val = document.getElementById('perPageSelect').value;
            perPage = val === 'all' ? 999999 : parseInt(val);
            currentPage = 1; renderTable();
        }
        function goToPage(page) {
            if (page < 1) return;
            currentPage = page; renderTable();
            const top = document.getElementById('dosenTableContainer');
            if (top) top.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        function renderPagination(totalItems, startIdx, endIdx) {
            const info = document.getElementById('paginationInfo');
            const btns = document.getElementById('paginationButtons');
            if (!info || !btns) return;
            if (totalItems === 0) { info.textContent = 'Tidak ada data'; btns.innerHTML = ''; return; }
            info.textContent = `Menampilkan ${startIdx + 1}-${endIdx} dari ${totalItems}`;
            if (perPage >= 999999) { btns.innerHTML = ''; return; }
            const totalPages = Math.ceil(totalItems / perPage);
            let html = `<button class="pagination-btn" onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}><i class="bi bi-chevron-left"></i></button>`;
            const maxButtons = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
            let endPage = Math.min(totalPages, startPage + maxButtons - 1);
            if (endPage - startPage + 1 < maxButtons) startPage = Math.max(1, endPage - maxButtons + 1);
            if (startPage > 1) {
                html += `<button class="pagination-btn" onclick="goToPage(1)">1</button>`;
                if (startPage > 2) html += `<span class="pagination-ellipsis">…</span>`;
            }
            for (let i = startPage; i <= endPage; i++) html += `<button class="pagination-btn ${i === currentPage ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) html += `<span class="pagination-ellipsis">…</span>`;
                html += `<button class="pagination-btn" onclick="goToPage(${totalPages})">${totalPages}</button>`;
            }
            html += `<button class="pagination-btn" onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}><i class="bi bi-chevron-right"></i></button>`;
            btns.innerHTML = html;
        }

        // ============================================================
        // FETCH DATA
        // ============================================================
        document.addEventListener('DOMContentLoaded', function() {
            fetchBimbinganData();
            document.addEventListener('click', function(e) {
                const wrap = document.getElementById('searchCatWrap');
                if (wrap && !wrap.contains(e.target)) document.getElementById('searchCatMenu').classList.remove('open');
            });
        });

        function switchDosenTab(tab) {
            const btnClassInactive = 'px-5 py-2.5 rounded-xl font-bold text-sm bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200'.split(' ');
            const btnClassActive = 'px-5 py-2.5 rounded-xl font-bold text-sm bg-orange-100 text-orange-700 border border-orange-300'.split(' ');

            ['dosenTab1', 'dosenTab2', 'dosenTab3', 'dosenTab4'].forEach(id => {
                const el = document.getElementById(id);
                if (el) { el.classList.remove(...btnClassActive); el.classList.add(...btnClassInactive); }
            });

            if (tab === 'preview1') { currentTahap = 'Preview 1'; document.getElementById('dosenTab1').classList.remove(...btnClassInactive); document.getElementById('dosenTab1').classList.add(...btnClassActive); }
            else if (tab === 'preview2') { currentTahap = 'Preview 2'; document.getElementById('dosenTab2').classList.remove(...btnClassInactive); document.getElementById('dosenTab2').classList.add(...btnClassActive); }
            else if (tab === 'preview3') { currentTahap = 'Preview 3'; document.getElementById('dosenTab3').classList.remove(...btnClassInactive); document.getElementById('dosenTab3').classList.add(...btnClassActive); }
            else if (tab === 'preview4') { currentTahap = 'Preview 4'; document.getElementById('dosenTab4').classList.remove(...btnClassInactive); document.getElementById('dosenTab4').classList.add(...btnClassActive); }

            unselectAllDosenStudents();
            closeLihatBerkasPanel();

            currentPage = 1;
            fetchBimbinganData();
        }

        function fetchBimbinganData(silent = false) {
            const tbody = document.getElementById('bimbinganTableBody');
            const posisi = <?= $posisi ?>;
            const totalCols = posisi == 1 ? 8 : 7;
            if (!silent) tbody.innerHTML = `<tr><td colspan="${totalCols}" class="text-center py-10 text-slate-500"><i class="bi bi-arrow-repeat animate-spin text-xl"></i> Memuat data...</td></tr>`;

            const fetchTahap = currentTahap === 'Preview 4' ? 'Preview 4' : currentTahap;

            fetch(`<?= site_url('mahasiswa/ajax_get_dosen_bimbingan') ?>?posisi=${posisi}&tahap=${encodeURIComponent(fetchTahap)}`)
                .then(res => { if (!res.ok) throw new Error('HTTP ' + res.status); return res.text(); })
                .then(text => {
                    let res;
                    try { res = JSON.parse(text); } catch(e) {
                        console.error('Server response (not JSON):', text);
                        tbody.innerHTML = `<tr><td colspan="${totalCols}" class="text-center py-10 text-rose-500 text-xs">Server mengembalikan response tidak valid.</td></tr>`;
                        return;
                    }
                    if (res.status) {
                        bimbinganData = res.data;
                        bimbinganData.sort((a, b) => {
                            let dateA = a.latest_preview ? new Date(a.latest_preview.created_at).getTime() : 0;
                            let dateB = b.latest_preview ? new Date(b.latest_preview.created_at).getTime() : 0;
                            return dateB - dateA;
                        });
                        updateFilterCounts();
                        renderTable();
                    } else {
                        tbody.innerHTML = `<tr><td colspan="${totalCols}" class="text-center py-10 text-rose-500">${res.message}</td></tr>`;
                    }
                })
                .catch(err => {
                    console.error('Fetch error:', err);
                    tbody.innerHTML = `<tr><td colspan="${totalCols}" class="text-center py-10 text-rose-500">Terjadi kesalahan koneksi: ${err.message}</td></tr>`;
                });
        }

        function setDosenFilter(filter) {
            currentDosenFilter = filter;
            currentPage = 1;
            ['all', 'approved', 'pending', 'revision', 'empty'].forEach(f => {
                let card = document.getElementById('fCard_' + f);
                card.classList.remove('border-orange-300', 'bg-orange-50', 'shadow-xs');
                if (f === filter) card.classList.add('border-orange-300', 'bg-orange-50', 'shadow-xs');
                else card.classList.add('border-slate-200', 'bg-slate-50');
            });
            renderTable();
        }

        // ============================================================
        // HELPER: SUB-STATUS UNTUK PREVIEW 3
        // ============================================================
        function getP3SubStatus(preview, stage) {
            if (!preview) return 'None';
            if (stage === 'bimbingan') return preview.status_bimbingan_p1 || 'None';
            if (stage === 'sitasi') return preview.status_sitasi_p1 || 'None';
            if (stage === 'persyaratan') return preview.status_persyaratan_p1 || 'None';
            return 'None';
        }
        function getP3SubCatatan(preview, stage) {
            if (!preview) return '';
            if (stage === 'bimbingan') return preview.catatan_bimbingan_p1 || '';
            if (stage === 'sitasi') return preview.catatan_sitasi_p1 || '';
            if (stage === 'persyaratan') return preview.catatan_persyaratan_p1 || '';
            return '';
        }
        function isP3AllApproved(preview) {
            if (!preview) return false;
            return (preview.status_bimbingan_p1 === 'Approved')
                && (preview.status_sitasi_p1 === 'Approved')
                && (preview.status_persyaratan_p1 === 'Approved');
        }
        function p3SubBadgeHtml(st, label) {
            const cls = st === 'Approved' ? 'p3-sub-approved'
                      : st === 'Revision' ? 'p3-sub-revision'
                      : st === 'Pending'  ? 'p3-sub-pending'
                      : 'p3-sub-none';
            const icon = st === 'Approved' ? '✓'
                       : st === 'Revision' ? '✗'
                       : st === 'Pending'  ? '⏳'
                       : '•';
            return `<span class="p3-sub-badge ${cls}" title="${label}: ${st}">${label.charAt(0).toUpperCase()}: ${icon}</span>`;
        }

        function updateFilterCounts() {
            let counts = { all: 0, approved: 0, pending: 0, revision: 0, empty: 0 };
            bimbinganData.forEach(mhs => {
                counts.all++;
                if (!mhs.latest_preview) { counts.empty++; return; }

                if (currentTahap === 'Preview 3') {
                    const bimb = mhs.latest_preview.status_bimbingan_p1 || 'None';
                    const sit  = mhs.latest_preview.status_sitasi_p1 || 'None';
                    const per  = mhs.latest_preview.status_persyaratan_p1 || 'None';
                    if (bimb === 'Revision' || sit === 'Revision' || per === 'Revision') { counts.revision++; return; }
                    if (bimb === 'Approved' && sit === 'Approved' && per === 'Approved') { counts.approved++; return; }
                    counts.pending++;
                    return;
                }
                if (currentTahap === 'Preview 4') {
                    const st = mhs.latest_preview.status_pembimbing || 'Pending';
                    if (st === 'Approved') counts.approved++;
                    else if (st === 'Revision') counts.revision++;
                    else counts.pending++;
                    return;
                }
                let st = mhs.latest_preview.status_pembimbing;
                if (st === 'Approved') counts.approved++;
                else if (st === 'Revision') counts.revision++;
                else counts.pending++;
            });
            for (const [key, val] of Object.entries(counts)) {
                document.getElementById('fCount_' + key).textContent = val;
            }
        }

        // ============================================================
        // RENDER TABLE
        // ============================================================
        function renderTable() {
            const tbody = document.getElementById('bimbinganTableBody');
            const keyword = activeKeyword;
            const isP1 = <?= $posisi ?> == 1;
            const isPreview3 = currentTahap === 'Preview 3';
            const isPreview4 = currentTahap === 'Preview 4';

            const filteredData = [];
            bimbinganData.forEach((mhs, originalIndex) => {
                let match = false;
                if (!keyword) match = true;
                else if (searchCategory === 'nim') match = mhs.nim.toLowerCase().includes(keyword);
                else if (searchCategory === 'nama') match = mhs.nama_mahasiswa.toLowerCase().includes(keyword);
                else if (searchCategory === 'judul') match = (mhs.judul && mhs.judul.toLowerCase().includes(keyword));
                else match = mhs.nim.toLowerCase().includes(keyword) || mhs.nama_mahasiswa.toLowerCase().includes(keyword) || (mhs.judul && mhs.judul.toLowerCase().includes(keyword));
                if (!match) return;

                if (currentDosenFilter !== 'all') {
                    if (currentDosenFilter === 'empty' && mhs.latest_preview) return;
                    if (currentDosenFilter !== 'empty' && !mhs.latest_preview) return;
                    if (currentDosenFilter !== 'empty' && mhs.latest_preview) {
                        if (isPreview3) {
                            const bimb = mhs.latest_preview.status_bimbingan_p1 || 'None';
                            const sit  = mhs.latest_preview.status_sitasi_p1 || 'None';
                            const per  = mhs.latest_preview.status_persyaratan_p1 || 'None';
                            if (currentDosenFilter === 'approved' && !(bimb === 'Approved' && sit === 'Approved' && per === 'Approved')) return;
                            if (currentDosenFilter === 'revision' && !(bimb === 'Revision' || sit === 'Revision' || per === 'Revision')) return;
                            if (currentDosenFilter === 'pending' && (bimb === 'Approved' && sit === 'Approved' && per === 'Approved')) return;
                            if (currentDosenFilter === 'pending' && (bimb === 'Revision' || sit === 'Revision' || per === 'Revision')) return;
                        } else {
                            let st = mhs.latest_preview.status_pembimbing;
                            if (currentDosenFilter === 'approved' && st !== 'Approved') return;
                            if (currentDosenFilter === 'revision' && st !== 'Revision') return;
                            if (currentDosenFilter === 'pending' && (st === 'Approved' || st === 'Revision')) return;
                        }
                    }
                }
                filteredData.push({ mhs, originalIndex });
            });

            const totalItems = filteredData.length;
            let startIdx = 0, endIdx = totalItems;
            if (perPage < 999999 && totalItems > 0) {
                const totalPages = Math.ceil(totalItems / perPage);
                if (currentPage > totalPages) currentPage = totalPages;
                if (currentPage < 1) currentPage = 1;
                startIdx = (currentPage - 1) * perPage;
                endIdx = Math.min(startIdx + perPage, totalItems);
            }
            const pageData = filteredData.slice(startIdx, endIdx);

            let html = '';
            pageData.forEach(({ mhs, originalIndex }) => {
                const index = originalIndex;

                let previewHtml = `<span class="text-slate-400 italic text-xs">Belum ada berkas</span>`;
                let timeHtml = `-`;
                let statusBadge = `<span class="badge badge-secondary"><i class="bi bi-dash"></i> Kosong</span>`;
                let rekomenBadge = `<button onclick="openRekomendasiModal('${mhs.nim}', '${mhs.latest_preview ? mhs.latest_preview.id : ''}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer whitespace-nowrap"><i class="bi bi-plus-circle-fill"></i> Rekomendasi</button>`;

                if (mhs.rekomendasi) {
                    if (mhs.rekomendasi.recommendation_type === 'sidang') {
                        rekomenBadge = `<span onclick="openRekomendasiModal('${mhs.nim}', '${mhs.latest_preview ? mhs.latest_preview.id : ''}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-100 text-purple-800 border border-purple-300 rounded-xl text-xs font-extrabold cursor-pointer hover:bg-purple-200 transition whitespace-nowrap shadow-2xs" title="Klik untuk ubah rekomendasi"><i class="bi bi-mortarboard-fill text-purple-600"></i> Sidang TA</span>`;
                    } else if (mhs.rekomendasi.recommendation_type === 'non_sidang') {
                        const titleText = mhs.rekomendasi.jalur_title || 'Non-Sidang';
                        rekomenBadge = `<span onclick="openRekomendasiModal('${mhs.nim}', '${mhs.latest_preview ? mhs.latest_preview.id : ''}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-100 text-amber-900 border border-amber-300 rounded-xl text-xs font-extrabold cursor-pointer hover:bg-amber-200 transition whitespace-nowrap shadow-2xs" title="Jalur: ${titleText}"><i class="bi bi-award-fill text-amber-600 text-sm"></i> ${titleText}</span>`;
                    }
                }

                let btnHtml = `<button disabled class="px-3 py-1.5 bg-slate-100 text-slate-400 rounded-lg text-xs font-bold cursor-not-allowed border border-slate-200">Belum ada file</button>`;
                let subStatusHtml = '';

                if (mhs.latest_preview) {
                    const latest = mhs.latest_preview;
                    const btnClass = latest.file_missing ? 'bg-rose-100 text-rose-700 opacity-80' : 'btn-3d-orange scale-95 hover:scale-100';
                    const icon = latest.file_missing ? 'bi-exclamation-triangle-fill' : 'fa-solid fa-folder-open';
                    const label = latest.file_missing ? 'File Hilang' : 'Lihat Berkas';

                    let fileCount = 0;
                    if (isPreview3) {
                        if (latest.file_bimbingan) fileCount++;
                        if (latest.file_sitasi) fileCount++;
                        if (latest.file_persyaratan) fileCount++;
                    } else if (isPreview4) {
                        fileCount = latest.file_sidang ? 1 : 0;
                    } else {
                        fileCount = latest.file_draft ? 1 : 0;
                    }
                    const countBadge = fileCount > 1 ? ` <span class="bg-white/30 text-white px-1.5 py-0.5 rounded-md ml-1 text-[10px] font-black">${fileCount}</span>` : '';

                    previewHtml = `<button onclick="toggleLihatBerkasPanel(${index})" class="${btnClass} inline-flex items-center gap-1.5 text-white font-bold px-3 py-1.5 rounded-xl text-xs cursor-pointer shadow-md transition-transform"><i class="${icon} text-xs"></i> ${label}${countBadge}</button>`;

                    const dt = new Date(latest.created_at);
                    timeHtml = `<div class="text-xs font-semibold text-slate-700">${dt.toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'})}</div><div class="text-[10px] text-slate-500">${dt.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'})} WIB</div>`;

                    if (isPreview3) {
                        const stB = getP3SubStatus(latest, 'bimbingan');
                        const stS = getP3SubStatus(latest, 'sitasi');
                        const stP = getP3SubStatus(latest, 'persyaratan');
                        const appCount = [stB, stS, stP].filter(s => s === 'Approved').length;

                        if (appCount === 3) statusBadge = `<span class="badge badge-success"><i class="bi bi-check-circle-fill"></i> 3/3 Disetujui</span>`;
                        else if (stB === 'Revision' || stS === 'Revision' || stP === 'Revision') statusBadge = `<span class="badge badge-danger"><i class="bi bi-x-circle-fill"></i> Ada Revisi</span>`;
                        else statusBadge = `<span class="badge badge-warning"><i class="bi bi-clock-fill"></i> ${appCount}/3 Disetujui</span>`;

                        subStatusHtml = `<div class="flex flex-wrap gap-1 mt-1.5 justify-center">
                            ${p3SubBadgeHtml(stB, 'Bimb')}
                            ${p3SubBadgeHtml(stS, 'Sitasi')}
                            ${p3SubBadgeHtml(stP, 'Persyrt')}
                        </div>`;

                        if (isP1 && !latest.file_missing) {
                            btnHtml = `<button onclick="toggleLihatBerkasPanel(${index})" class="px-3 py-1.5 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 border border-indigo-200 rounded-lg text-xs font-bold transition shadow-2xs flex items-center gap-1 whitespace-nowrap">
                                <i class="bi bi-list-check"></i> Review Per File
                            </button>`;
                        } else if (!isP1) {
                            const hasP2 = (latest.catatan_pembimbing_2 || '').trim().length > 0;
                            btnHtml = `<button onclick="openSingleBatchModal(${index})" class="px-3 py-1.5 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 border border-indigo-200 rounded-lg text-xs font-bold transition shadow-2xs flex items-center gap-1">
                                <i class="bi bi-chat-dots-fill"></i> ${hasP2 ? 'Edit Komentar' : 'Komentari'}
                            </button>`;
                        } else if (latest.file_missing) {
                            btnHtml = `<button disabled class="px-3 py-1.5 bg-slate-100 text-rose-500 rounded-lg text-xs font-bold cursor-not-allowed border border-rose-200 whitespace-nowrap"><i class="bi bi-exclamation-triangle"></i> File Hilang</button>`;
                        }
                    } else if (isPreview4) {
                        let st = latest.status_pembimbing;
                        if (st === 'Approved') statusBadge = `<span class="badge badge-success"><i class="bi bi-check-circle-fill"></i> Disetujui</span>`;
                        else if (st === 'Revision') statusBadge = `<span class="badge badge-danger"><i class="bi bi-x-circle-fill"></i> Revisi</span>`;
                        else statusBadge = `<span class="badge badge-warning"><i class="bi bi-clock-fill"></i> Pending</span>`;

                        if (latest.file_missing) {
                            btnHtml = `<button disabled class="px-3 py-1.5 bg-slate-100 text-rose-500 rounded-lg text-xs font-bold cursor-not-allowed border border-rose-200 whitespace-nowrap"><i class="bi bi-exclamation-triangle"></i> File Hilang</button>`;
                        } else {
                            btnHtml = `<div class="flex items-center justify-end gap-1.5 whitespace-nowrap"><button type="button" onclick="openModalPenilaianSidang('${mhs.nim}')" class="px-3 py-1.5 bg-amber-100 hover:bg-amber-200 text-amber-800 border-amber-300 border rounded-lg text-xs font-bold transition shadow-2xs flex items-center gap-1"><i class="fa-solid fa-clipboard-check"></i> Nilai</button></div>`;
                        }
                    } else {
                        let st = latest.status_pembimbing;
                        if (st === 'Approved') statusBadge = `<span class="badge badge-success"><i class="bi bi-check-circle-fill"></i> Disetujui</span>`;
                        else if (st === 'Revision') statusBadge = `<span class="badge badge-danger"><i class="bi bi-x-circle-fill"></i> Revisi</span>`;
                        else statusBadge = `<span class="badge badge-warning"><i class="bi bi-clock-fill"></i> Pending</span>`;

                        const aksiBtnText = isP1 ? 'Review' : 'Komentari';

                        if (latest.file_missing) {
                            btnHtml = `<button disabled class="px-3 py-1.5 bg-slate-100 text-rose-500 rounded-lg text-xs font-bold cursor-not-allowed border border-rose-200 whitespace-nowrap"><i class="bi bi-exclamation-triangle"></i> File Hilang</button>`;
                        } else if (st === 'Approved') {
                            if (isP1) {
                                btnHtml = `<button disabled class="px-3 py-1.5 bg-slate-100 text-emerald-600 rounded-lg text-xs font-bold cursor-not-allowed border border-emerald-200 whitespace-nowrap"><i class="bi bi-check-all"></i> Sudah Disetujui</button>`;
                            } else {
                                const hasP2Comment = (latest.catatan_pembimbing_2 || '').trim().length > 0;
                                btnHtml = `<div class="flex items-center justify-end gap-1.5 whitespace-nowrap"><button onclick="openSingleBatchModal(${index})" class="px-3 py-1.5 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 border border-indigo-200 rounded-lg text-xs font-bold transition shadow-2xs flex items-center gap-1"><i class="bi bi-chat-dots-fill"></i> ${hasP2Comment ? 'Edit Komentar' : 'Komentari'}</button></div>`;
                            }
                        } else {
                            btnHtml = `<div class="flex items-center justify-end gap-1.5 whitespace-nowrap"><button onclick="openSingleBatchModal(${index})" class="px-3 py-1.5 ${isP1 ? 'bg-orange-100 hover:bg-orange-200 text-orange-700 border-orange-200' : 'bg-indigo-100 hover:bg-indigo-200 text-indigo-700 border-indigo-200'} border rounded-lg text-xs font-bold transition shadow-2xs flex items-center gap-1"><i class="bi ${isP1 ? 'bi-pencil-square' : 'bi-chat-dots-fill'}"></i> ${aksiBtnText}</button></div>`;
                        }
                    }
                }

                let checkboxHtml = '';
                if (mhs.latest_preview && !mhs.latest_preview.file_missing) {
                    if (isPreview3) {
                        checkboxHtml = `<input type="checkbox" disabled class="w-4 h-4 rounded border-slate-200 cursor-not-allowed opacity-40" title="Preview 3 di-review per file (buka panel Lihat Berkas)">`;
                    } else if (mhs.latest_preview.status_pembimbing !== 'Approved') {
                        const catatanField = isPreview4 ? (mhs.latest_preview.catatan_pembimbing || '') : (mhs.latest_preview.catatan_pembimbing || '');
                        const fileField = isPreview4 ? (mhs.latest_preview.file_sidang || '') : (mhs.latest_preview.file_draft || '');
                        checkboxHtml = `<input type="checkbox" value="${mhs.latest_preview.id}" data-name="${mhs.nama_mahasiswa}" data-file="${fileField}" data-id="${mhs.latest_preview.id}" data-status="${mhs.latest_preview.status_pembimbing || 'Pending'}" data-catatan="${encodeURIComponent(catatanField)}" data-catatan2="${encodeURIComponent(mhs.latest_preview.catatan_pembimbing_2 || '')}" class="dosen-student-cb w-4 h-4 text-orange-600 rounded border-slate-300 focus:ring-orange-500 cursor-pointer">`;
                    } else {
                        checkboxHtml = `<input type="checkbox" disabled class="w-4 h-4 rounded border-slate-200 cursor-not-allowed opacity-50" title="Sudah Disetujui">`;
                    }
                } else {
                    checkboxHtml = `<input type="checkbox" disabled class="w-4 h-4 rounded border-slate-200 cursor-not-allowed opacity-50" title="Belum ada berkas atau file hilang">`;
                }

                let p1Comment = mhs.latest_preview ? (mhs.latest_preview.catatan_pembimbing || '') : '';
                let p2Comment = mhs.latest_preview ? (mhs.latest_preview.catatan_pembimbing_2 || '') : '';
                let u1Comment = mhs.latest_preview ? (mhs.latest_preview.catatan_penguji_1 || '') : '';
                let u2Comment = mhs.latest_preview ? (mhs.latest_preview.catatan_penguji_2 || '') : '';
                let hasAnyComment = p1Comment.trim().length > 0 || p2Comment.trim().length > 0 || u1Comment.trim().length > 0 || u2Comment.trim().length > 0;
                let commentCountParts = [];
                if (p1Comment.trim()) commentCountParts.push('P1');
                if (p2Comment.trim()) commentCountParts.push('P2');
                if (u1Comment.trim()) commentCountParts.push('U1');
                if (u2Comment.trim()) commentCountParts.push('U2');
                let commentBtnHtml = hasAnyComment
                    ? `<button onclick="showUnifiedCommentModal('${mhs.nama_mahasiswa}', '${encodeURIComponent(p1Comment)}', '${encodeURIComponent(p2Comment)}', '${encodeURIComponent(u1Comment)}', '${encodeURIComponent(u2Comment)}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-violet-100 hover:bg-violet-200 text-violet-700 border border-violet-200 rounded-lg text-xs font-bold transition cursor-pointer"><i class="bi bi-chat-quote-fill"></i> ${commentCountParts.join(', ')}</button>`
                    : `<span class="text-slate-400 text-xs italic">-</span>`;

                html += `
                    <tr class="hover:bg-slate-50 transition-colors" data-index="${index}">
                        <td class="dosen-cb-cell py-4 px-4 text-center">${checkboxHtml}</td>
                        <td class="mhs-info-cell py-4 px-4">
                            <div class="relative inline-block group">
                                <span class="font-bold text-slate-900 cursor-pointer hover:text-orange-600 transition-colors underline decoration-dotted decoration-orange-300 underline-offset-2" onmouseenter="showHoverPanel(event, ${index})" onmouseleave="scheduleHidePanel()">${mhs.nama_mahasiswa}</span>
                            </div>
                            <div class="text-xs text-slate-500 font-mono mt-0.5">${mhs.nim}</div>
                            ${mhs.judul ? `<div class="text-[10px] text-slate-500 font-medium italic mt-1 line-clamp-2 max-w-[250px]" title="${mhs.judul}">"${mhs.judul}"</div>` : ''}
                        </td>
                        <td data-label="Berkas" class="py-4 px-4 text-center">${previewHtml}</td>
                        <td data-label="Waktu Upload" class="py-4 px-4 text-center">${timeHtml}</td>
                        <td data-label="Status" class="py-4 px-4 text-center">${statusBadge}${subStatusHtml}</td>
                        ${ isP1 ? `<td data-label="Rekomendasi" class="py-4 px-4 text-center">${rekomenBadge}</td>` : '' }
                        <td data-label="Komentar" class="py-4 px-4 text-center">${commentBtnHtml}</td>
                        <td class="aksi-cell py-4 px-4 pr-6 text-right">${btnHtml}</td>
                    </tr>
                `;
            });

            const emptyColspan = isP1 ? 8 : 7;
            if (pageData.length === 0) html = `<tr><td colspan="${emptyColspan}" class="text-center py-10 text-slate-500 font-medium">Tidak ada data mahasiswa ditemukan.</td></tr>`;

            tbody.innerHTML = html;
            renderPagination(totalItems, startIdx, endIdx);
            rebindDosenCheckboxes();
            updateTableButtonHighlights();
        }

        // ============================================================
        // REVIEW MODAL
        // ============================================================
        function openReviewModal(index) {
            const mhs = bimbinganData[index];
            const latest = mhs.latest_preview;
            if (!latest) return;
            document.getElementById('modalTahapTitle').textContent = `Review Berkas ${currentTahap}`;
            document.getElementById('modalStudentName').textContent = `${mhs.nama_mahasiswa} (${mhs.nim})`;
            document.getElementById('modalJudul').textContent = mhs.judul || '-';
            document.getElementById('modalStudentNotes').textContent = latest.catatan_mahasiswa || '-';
            document.getElementById('modalFileLink').href = `<?= base_url('uploads/preview_ta/') ?>${latest.file_draft}`;
            document.getElementById('modalIdPreview').value = latest.id;
            if (document.getElementById('modalStatus')) document.getElementById('modalStatus').value = latest.status_pembimbing || 'Pending';
            let catatanDosen = <?= $posisi ?> === 1 ? (latest.catatan_pembimbing || '') : (latest.catatan_pembimbing_2 || '');
            if (tinymce.get('modalCatatan')) tinymce.get('modalCatatan').setContent(catatanDosen);
            else { document.getElementById('modalCatatan').value = catatanDosen; initModalTinyMCE(); }
            if (<?= $posisi ?> === 1 && document.getElementById('modalRiwayatContainer')) {
                if (latest.catatan_pembimbing) {
                    document.getElementById('modalRiwayatContainer').classList.remove('hidden');
                    document.getElementById('modalCatatanSebelumnya').textContent = latest.catatan_pembimbing;
                } else document.getElementById('modalRiwayatContainer').classList.add('hidden');
            }
            document.getElementById('reviewModal').classList.remove('hidden');
        }
        function closeReviewModal() { document.getElementById('reviewModal').classList.add('hidden'); }

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-5 right-5 z-[9999] p-4 rounded-xl text-white font-bold shadow-lg transition-opacity ${type === 'success' ? 'bg-emerald-500' : 'bg-rose-500'}`;
            toast.innerHTML = `<i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'} mr-2"></i> ${message}`;
            document.body.appendChild(toast);
            setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
        }

        const formReview = document.getElementById('formReview');
        if (formReview) {
            formReview.addEventListener('submit', function(e) {
                e.preventDefault(); tinymce.triggerSave();
                const btn = this.querySelector('button[type="submit"]');
                const originalBtnText = btn.innerHTML;
                btn.disabled = true; btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> Menyimpan...';
                const formData = new FormData(this);
                fetch(this.action + '_ajax', { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.json())
                    .then(data => { if (data.status) { showToast(data.message, 'success'); closeReviewModal(); } else showToast(data.message || 'Gagal menyimpan', 'error'); })
                    .catch(err => { console.error(err); showToast('Kesalahan koneksi', 'error'); })
                    .finally(() => { btn.disabled = false; btn.innerHTML = originalBtnText; });
            });
        }

        // ============================================================
        // SSE
        // ============================================================
        let dosenEventSource = null;
        function startDosenSSE() {
            if (dosenEventSource) dosenEventSource.close();
            const posisi = <?= $posisi ?>;
            dosenEventSource = new EventSource(`<?= site_url('mahasiswa/sse_dosen_bimbingan') ?>?posisi=${posisi}`);
            dosenEventSource.onmessage = function(event) {
                try { const data = JSON.parse(event.data); if (data && data.length !== undefined) fetchBimbinganData(true); }
                catch (e) { console.error('SSE Error:', e); }
            };
        }

        // ============================================================
        // BATCH CHECKBOXES
        // ============================================================
        function rebindDosenCheckboxes() {
            const checkAll = document.getElementById('checkAllDosenStudents');
            const studentCbs = document.querySelectorAll('.dosen-student-cb');
            if (checkAll) {
                checkAll.checked = false;
                checkAll.onchange = function() { studentCbs.forEach(cb => { if (!cb.disabled) cb.checked = this.checked; }); updateDosenBatchBar(); };
            }
            studentCbs.forEach(cb => {
                cb.onchange = () => {
                    updateDosenBatchBar();
                    if (checkAll) {
                        const enabledCbs = Array.from(studentCbs).filter(c => !c.disabled);
                        const checkedCount = enabledCbs.filter(c => c.checked).length;
                        checkAll.checked = (enabledCbs.length > 0 && checkedCount === enabledCbs.length);
                    }
                };
            });
            updateDosenBatchBar();
        }
        function updateDosenBatchBar() {
            const checkedCbs = document.querySelectorAll('.dosen-student-cb:checked');
            const batchBar = document.getElementById('dosenBatchActionBar');
            const countBadge = document.getElementById('dosenSelectedCountBadge');
            if (checkedCbs.length > 0) { countBadge.textContent = checkedCbs.length; batchBar.classList.remove('hidden'); batchBar.classList.add('flex'); }
            else { batchBar.classList.add('hidden'); batchBar.classList.remove('flex'); }
        }
        function unselectAllDosenStudents() {
            document.querySelectorAll('.dosen-student-cb').forEach(cb => cb.checked = false);
            const checkAll = document.getElementById('checkAllDosenStudents');
            if (checkAll) checkAll.checked = false;
            updateDosenBatchBar();
        }

        function openSingleBatchModal(index) {
            const mhs = bimbinganData[index];
            if (!mhs || !mhs.latest_preview) return;
            const cb = {
                getAttribute: function(attr) {
                    if (attr === 'data-name') return mhs.nama_mahasiswa;
                    if (attr === 'data-file') return currentTahap === 'Preview 4' ? (mhs.latest_preview.file_sidang || '') : mhs.latest_preview.file_draft;
                    if (attr === 'data-id') return mhs.latest_preview.id;
                    if (attr === 'data-status') return mhs.latest_preview.status_pembimbing || 'Pending';
                    if (attr === 'data-catatan') return encodeURIComponent(mhs.latest_preview.catatan_pembimbing || '');
                    if (attr === 'data-catatan2') return encodeURIComponent(mhs.latest_preview.catatan_pembimbing_2 || '');
                    return null;
                }
            };
            renderBatchModal([cb]);
        }

        function openDosenBatchModal() {
            const checkedCbs = document.querySelectorAll('.dosen-student-cb:checked');
            if (checkedCbs.length === 0) return;
            renderBatchModal(checkedCbs);
        }

        function renderBatchModal(checkedCbs) {
            const posisi = <?= $posisi ?>;
            const isPreview4 = currentTahap === 'Preview 4';
            const uploadBase = isPreview4 ? '<?= base_url('uploads/sidang/') ?>' : '<?= base_url('uploads/preview_ta/') ?>';
            let html = '';

            checkedCbs.forEach((cb, i) => {
                const name = cb.getAttribute('data-name');
                const file = cb.getAttribute('data-file');
                const idPreview = cb.getAttribute('data-id');
                const statusCurrent = cb.getAttribute('data-status') || 'Pending';
                const catatanCurrent = decodeURIComponent(cb.getAttribute('data-catatan') || '');
                const catatan2Current = decodeURIComponent(cb.getAttribute('data-catatan2') || '');
                const fileUrl = uploadBase + file;

                const statusOptions = posisi === 1 ? `
                    <div class="mb-2">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status Penilaian (P1)</label>
                        <select id="batchStatus_${i}" class="w-full p-2.5 rounded-xl border border-slate-200 focus:ring-orange-500 focus:border-orange-500 text-xs font-semibold bg-white">
                            <option value="Approved" ${statusCurrent === 'Approved' ? 'selected' : ''}>Disetujui (ACC)</option>
                            <option value="Revision" ${statusCurrent === 'Revision' ? 'selected' : ''}>Perlu Revisi</option>
                        </select>
                    </div>` : '';

                const catatanLabel = posisi === 1 ? 'Komentar / Feedback' : 'Komentar untuk P1';
                const catatanVal = posisi === 1 ? catatanCurrent : catatan2Current;
                const btnColor = posisi === 1 ? 'bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600' : 'bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700';
                const btnIcon = posisi === 1 ? 'bi-send-fill' : 'bi-chat-dots-fill';
                const btnLabel = posisi === 1 ? 'Simpan Review' : 'Simpan Komentar';

                html += `
                    <div class="mb-6 rounded-2xl border border-slate-200 overflow-hidden shadow-sm bg-white">
                        <div class="flex items-center gap-3 px-4 py-3 ${posisi === 1 ? 'bg-slate-900' : 'bg-indigo-900'} text-white">
                            <span class="w-7 h-7 rounded-lg ${posisi === 1 ? 'bg-orange-500' : 'bg-indigo-500'} text-white font-black text-xs flex items-center justify-center shrink-0">${i + 1}</span>
                            <div class="min-w-0">
                                <div class="font-bold text-sm truncate">${name}</div>
                                <div class="text-[10px] text-slate-400 font-mono truncate"><i class="bi bi-file-earmark-pdf-fill ${posisi === 1 ? 'text-orange-400' : 'text-indigo-400'} mr-1"></i>${file}</div>
                            </div>
                        </div>
                        <div class="relative bg-slate-100" style="height:220px;">
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 text-xs z-0" id="batchPdfLoader_${i}"><i class="bi bi-arrow-repeat animate-spin text-2xl mb-1"></i> Memuat dokumen...</div>
                            <iframe src="${fileUrl}" class="w-full h-full border-0 relative z-10" onload="document.getElementById('batchPdfLoader_${i}').style.display='none'"></iframe>
                        </div>
                        <div class="p-4 space-y-2 border-t border-slate-100">
                            ${statusOptions}
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">${catatanLabel}</label>
                                <textarea id="batchCatatan_${i}" rows="2" class="batch-textarea w-full p-2.5 rounded-xl border border-slate-200 text-xs font-medium resize-none focus:outline-none focus:ring-2 ${posisi === 1 ? 'focus:ring-orange-400/25 focus:border-orange-400' : 'focus:ring-indigo-400/25 focus:border-indigo-400'}" placeholder="Tuliskan komentar...">${catatanVal}</textarea>
                            </div>
                            <button onclick="submitBatchItemReview('${idPreview}', ${posisi}, ${i})" class="w-full py-2.5 px-4 rounded-xl ${btnColor} text-white font-bold text-xs shadow transition cursor-pointer flex items-center justify-center gap-1.5 mt-1">
                                <i class="bi ${btnIcon}"></i> ${btnLabel}
                            </button>
                        </div>
                    </div>
                `;
            });

            document.getElementById('dosenBatchModalBody').innerHTML = html;
            document.getElementById('dosenBatchReviewModal').classList.remove('hidden');
            tinymce.remove('.batch-textarea');
            tinymce.init({
                selector: '.batch-textarea',
                menubar: false, statusbar: false,
                plugins: 'lists link',
                toolbar: 'bold italic underline | bullist numlist | link',
                height: 200, skin: 'oxide',
                setup: function (editor) { editor.on('change', function () { tinymce.triggerSave(); }); }
            });
        }

        function submitBatchItemReview(idPreview, posisi, idx) {
            tinymce.triggerSave();
            const catatan = document.getElementById('batchCatatan_' + idx)?.value || '';
            const statusEl = document.getElementById('batchStatus_' + idx);
            const status = statusEl ? statusEl.value : null;
            const fd = new FormData();
            fd.append('id_preview', idPreview);
            fd.append('posisi', posisi);
            fd.append('catatan_pembimbing', catatan);
            if (status) fd.append('status_pembimbing', status);
            if (currentTahap === 'Preview 4') fd.append('tahap_preview', 'Preview 4');

            const btn = document.querySelector(`button[onclick="submitBatchItemReview('${idPreview}', ${posisi}, ${idx})"]`);
            if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> Menyimpan...'; }

            fetch('<?= site_url('mahasiswa/review_preview_ajax') ?>', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(data => {
                    if (data.status) {
                        showToast(data.message, 'success');
                        if (btn) { btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Tersimpan'; btn.classList.remove('from-orange-500','to-amber-500','from-indigo-500','to-purple-600','hover:from-orange-600','hover:to-amber-600','hover:from-indigo-600','hover:to-purple-700'); btn.classList.add('bg-emerald-500'); }
                        fetchBimbinganData(true);
                    } else {
                        showToast(data.message || 'Gagal menyimpan', 'error');
                        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-send-fill"></i> Simpan Review'; }
                    }
                })
                .catch(() => { showToast('Kesalahan koneksi', 'error'); if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-send-fill"></i> Simpan Review'; } });
        }

        function closeDosenBatchModal() { document.getElementById('dosenBatchReviewModal').classList.add('hidden'); }

        function submitDosenBatchApprove() {
            const checkedCbs = document.querySelectorAll('.dosen-student-cb:checked');
            if (checkedCbs.length === 0) return;
            if (currentTahap === 'Preview 3') { showToast('Preview 3 harus di-ACC per file. Buka panel Lihat Berkas.', 'error'); return; }

            Swal.fire({
                title: 'Konfirmasi Approve Massal',
                text: `Apakah anda yakin ingin approve masal ${checkedCbs.length} mahasiswa terpilih?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Approve!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const ids = Array.from(checkedCbs).map(cb => cb.value);
                    const formData = new FormData();
                    ids.forEach(id => formData.append('ids[]', id));
                    formData.append('posisi', <?= $posisi ?>);
                    if (currentTahap === 'Preview 4') formData.append('tahap_preview', 'Preview 4');
                    fetch('<?= site_url("mahasiswa/review_preview_batch_ajax") ?>', { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status) { Swal.fire('Berhasil!', data.message, 'success'); unselectAllDosenStudents(); fetchBimbinganData(true); }
                            else Swal.fire('Gagal!', data.message || 'Gagal approve massal', 'error');
                        })
                        .catch(err => { console.error(err); Swal.fire('Error!', 'Kesalahan koneksi', 'error'); });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => { startDosenSSE(); });

        // ============================================================
        // HOVER PREVIEW PANEL
        // ============================================================
        let hoverHideTimer = null;
        let hoverShowTimer = null;
        const panel = document.getElementById('hoverPreviewPanel');

        function showHoverPanel(event, index) {
            clearTimeout(hoverHideTimer);
            clearTimeout(hoverShowTimer);
            hoverShowTimer = setTimeout(() => {
                const mhs = bimbinganData[index];
                if (!mhs) return;
                const posisi = <?= $posisi ?>;
                const latest = mhs.latest_preview;
                document.getElementById('hoverPanelName').textContent = mhs.nama_mahasiswa + ' (' + mhs.nim + ')';

                const pdfWrap = document.getElementById('hoverPdfWrap');
                let previewFile = null;
                let previewBase = '<?= base_url('uploads/preview_ta/') ?>';
                if (latest) {
                    if (currentTahap === 'Preview 3') previewFile = latest.file_bimbingan || latest.file_sitasi || latest.file_persyaratan;
                    else if (currentTahap === 'Preview 4') { previewFile = latest.file_sidang; previewBase = '<?= base_url('uploads/sidang/') ?>'; }
                    else previewFile = latest.file_draft;
                }
                if (previewFile) pdfWrap.innerHTML = `<iframe src="${previewBase}${previewFile}" class="w-full h-full" frameborder="0"></iframe>`;
                else pdfWrap.innerHTML = `<div class="pdf-no-file"><i class="bi bi-file-earmark-x text-3xl"></i><span>Belum ada berkas diunggah</span></div>`;

                const formWrap = document.getElementById('hoverFormWrap');
                if (!latest || currentTahap === 'Preview 3') {
                    formWrap.innerHTML = currentTahap === 'Preview 3'
                        ? `<p class="text-xs text-slate-500 italic">Preview 3 di-review per file. Buka panel "Lihat Berkas" untuk ACC setiap file.</p>`
                        : `<p class="text-xs text-slate-500 italic">Tidak ada berkas untuk dikomentari.</p>`;
                } else if (posisi === 1) {
                    formWrap.innerHTML = `
                        <div class="panel-label">Status Penilaian (P1)</div>
                        <select id="hoverStatusSelect" class="w-full mb-3 p-2.5 rounded-xl border border-slate-300 focus:ring-orange-500 focus:border-orange-500 text-sm font-semibold">
                            <option value="Approved" ${latest.status_pembimbing === 'Approved' ? 'selected' : ''}>Disetujui (ACC)</option>
                            <option value="Revision" ${latest.status_pembimbing === 'Revision' ? 'selected' : ''}>Perlu Revisi</option>
                        </select>
                        <div class="panel-label">Komentar / Feedback</div>
                        <textarea id="hoverCatatanTA" rows="3" class="w-full p-2.5 rounded-xl border border-slate-300 text-sm font-medium resize-none focus:outline-none focus:ring-2 focus:ring-orange-400/30 focus:border-orange-500" placeholder="Tuliskan feedback...">${latest.catatan_pembimbing || ''}</textarea>
                        <button onclick="submitHoverReview(${latest.id}, 1)" class="mt-2 w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold text-xs shadow-md transition cursor-pointer"><i class="bi bi-send-fill mr-1"></i> Simpan Review</button>
                    `;
                } else {
                    formWrap.innerHTML = `
                        <div class="panel-label">Komentar untuk P1</div>
                        <textarea id="hoverCatatanTA" rows="3" class="w-full p-2.5 rounded-xl border border-slate-300 text-sm font-medium resize-none focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-500" placeholder="Tuliskan komentar...">${latest.catatan_pembimbing_2 || ''}</textarea>
                        <button onclick="submitHoverReview(${latest.id}, 2)" class="mt-2 w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-bold text-xs shadow-md transition cursor-pointer"><i class="bi bi-chat-dots-fill mr-1"></i> Simpan Komentar</button>
                    `;
                }

                const rect = event.target.getBoundingClientRect();
                let left = rect.left + window.scrollX;
                let top = rect.bottom + window.scrollY + 8;
                const panelW = 520;
                if (left + panelW > window.innerWidth - 16) left = window.innerWidth - panelW - 16;
                if (left < 8) left = 8;
                const panelEstH = 480;
                if (top + panelEstH > window.innerHeight + window.scrollY - 16) top = rect.top + window.scrollY - panelEstH - 8;
                panel.style.left = left + 'px';
                panel.style.top = top + 'px';
                panel.classList.add('visible');
                tinymce.remove('#hoverCatatanTA');
                tinymce.init({
                    selector: '#hoverCatatanTA',
                    menubar: false, statusbar: false,
                    plugins: 'lists link',
                    toolbar: 'bold italic underline | bullist numlist | link',
                    height: 150, skin: 'oxide',
                    setup: function (editor) { editor.on('change', function () { tinymce.triggerSave(); }); }
                });
            }, 350);
        }

        function scheduleHidePanel() { clearTimeout(hoverShowTimer); hoverHideTimer = setTimeout(() => { panel.classList.remove('visible'); }, 300); }

        let _commentData = { p1: '', p2: '', u1: '', u2: '' };
        let _activeCommentTab = 'p1';

        function showUnifiedCommentModal(name, encodedP1, encodedP2, encodedU1, encodedU2) {
            _commentData.p1 = decodeURIComponent(encodedP1 || '');
            _commentData.p2 = decodeURIComponent(encodedP2 || '');
            _commentData.u1 = decodeURIComponent(encodedU1 || '');
            _commentData.u2 = decodeURIComponent(encodedU2 || '');
            document.getElementById('commentModalName').textContent = name;
            document.getElementById('commentModalTitle').innerHTML = '<i class="bi bi-chat-quote-fill text-violet-500"></i> Komentar Dosen';
            if (_commentData.p1.trim()) _activeCommentTab = 'p1';
            else if (_commentData.p2.trim()) _activeCommentTab = 'p2';
            else if (_commentData.u1.trim()) _activeCommentTab = 'u1';
            else if (_commentData.u2.trim()) _activeCommentTab = 'u2';
            else _activeCommentTab = 'p1';
            switchCommentTab(_activeCommentTab);
            const modal = document.getElementById('commentModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function switchCommentTab(tab) {
            _activeCommentTab = tab;
            const content = document.getElementById('commentModalContent');
            const tabs = { p1: 'commentTabP1', p2: 'commentTabP2', u1: 'commentTabU1', u2: 'commentTabU2' };
            const activeClasses = 'px-3 py-1.5 rounded-lg text-xs font-bold border transition';
            const colors = {
                p1: { active: 'bg-orange-100 text-orange-700 border-orange-300', inactive: 'bg-slate-100 text-slate-600 hover:bg-slate-200 border-slate-200' },
                p2: { active: 'bg-indigo-100 text-indigo-700 border-indigo-300', inactive: 'bg-slate-100 text-slate-600 hover:bg-slate-200 border-slate-200' },
                u1: { active: 'bg-emerald-100 text-emerald-700 border-emerald-300', inactive: 'bg-slate-100 text-slate-600 hover:bg-slate-200 border-slate-200' },
                u2: { active: 'bg-purple-100 text-purple-700 border-purple-300', inactive: 'bg-slate-100 text-slate-600 hover:bg-slate-200 border-slate-200' }
            };
            for (const [key, elId] of Object.entries(tabs)) {
                const el = document.getElementById(elId);
                if (el) el.className = activeClasses + ' ' + (key === tab ? colors[key].active : colors[key].inactive);
            }
            const labels = { p1: 'Pembimbing 1', p2: 'Pembimbing 2', u1: 'Penguji 1', u2: 'Penguji 2' };
            const comment = _commentData[tab] || '';
            if (comment.trim()) content.innerHTML = comment;
            else content.innerHTML = `<em class="text-slate-400">Belum ada komentar dari ${labels[tab]}.</em>`;
        }

        function closeCommentModal() {
            const modal = document.getElementById('commentModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        panel.addEventListener('mouseenter', () => clearTimeout(hoverHideTimer));
        panel.addEventListener('mouseleave', scheduleHidePanel);

        function submitHoverReview(idPreview, posisi) {
            tinymce.triggerSave();
            const catatan = document.getElementById('hoverCatatanTA')?.value || '';
            const status = posisi === 1 ? (document.getElementById('hoverStatusSelect')?.value || 'Pending') : null;
            const fd = new FormData();
            fd.append('id_preview', idPreview);
            fd.append('posisi', posisi);
            fd.append('catatan_pembimbing', catatan);
            if (status) fd.append('status_pembimbing', status);
            if (currentTahap === 'Preview 4') fd.append('tahap_preview', 'Preview 4');
            fetch('<?= site_url('mahasiswa/review_preview_ajax') ?>', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(data => { if (data.status) { showToast(data.message, 'success'); panel.classList.remove('visible'); fetchBimbinganData(true); } else showToast(data.message || 'Gagal menyimpan', 'error'); })
                .catch(() => showToast('Kesalahan koneksi', 'error'));
        }

        function showCommentModal(comment, name, pos) {
            const p1 = pos === 1 ? comment : '';
            const p2 = pos === 2 ? comment : '';
            showUnifiedCommentModal(name, encodeURIComponent(p1), encodeURIComponent(p2), '', '');
        }

        // ============================================================
        // COMMENT ACTION MODAL
        // ============================================================
        function handleFileAction(nim, fileIndex, action, fileStage) {
            pendingAction = { nim, fileIndex, action, fileStage: fileStage || null };
            const mhs = bimbinganData.find(m => m.nim === nim);
            if (!mhs) return;
            const preview = mhs.riwayat_previews[fileIndex];
            if (!preview) return;

            const header = document.getElementById('commentActionHeader');
            const iconEl = document.getElementById('commentActionIcon');
            const titleEl = document.getElementById('commentActionTitle');
            const subtitleEl = document.getElementById('commentActionSubtitle');
            const submitBtn = document.getElementById('commentActionSubmit');

            if (submitBtn) { submitBtn.disabled = false; submitBtn.classList.remove('bg-emerald-500'); }
            header.classList.remove('p2-theme', 'p1-approve-theme', 'p1-revision-theme', 'u1-theme', 'u2-theme');

            let currentComment = '';
            const stageLabel = fileStage ? ` [${fileStage.charAt(0).toUpperCase() + fileStage.slice(1)}]` : '';

            if (action === 'Comment') {
                header.classList.add('p2-theme');
                iconEl.className = 'bi bi-chat-dots-fill text-indigo-200';
                titleEl.textContent = 'Komentar untuk Pembimbing 1';
                subtitleEl.textContent = 'Catatan ini akan dibaca oleh Pembimbing 1 sebagai pertimbangan bimbingan (opsional).';
                currentComment = preview.catatan_pembimbing_2 || '';
                submitBtn.className = 'px-6 py-2.5 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-bold shadow-md transition flex items-center gap-2 cursor-pointer';
                submitBtn.innerHTML = '<i class="bi bi-chat-dots-fill"></i> Simpan Komentar';
            } else if (action === 'Approved') {
                header.classList.add('p1-approve-theme');
                iconEl.className = 'bi bi-check-circle-fill text-emerald-200';
                titleEl.textContent = 'Setujui Berkas' + stageLabel;
                subtitleEl.textContent = fileStage
                    ? `Menyetujui file ${fileStage}. Berikan komentar persetujuan (opsional).`
                    : 'Berikan komentar persetujuan (opsional).';
                currentComment = fileStage ? getP3SubCatatan(preview, fileStage) : (preview.catatan_pembimbing || '');
                submitBtn.className = 'px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold shadow-md transition flex items-center gap-2 cursor-pointer';
                submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Simpan & Setujui';
            } else {
                header.classList.add('p1-revision-theme');
                iconEl.className = 'bi bi-exclamation-triangle-fill text-rose-200';
                titleEl.textContent = 'Revisi Berkas' + stageLabel;
                subtitleEl.textContent = fileStage
                    ? `Minta revisi untuk file ${fileStage}. Berikan saran revisi (opsional).`
                    : 'Berikan saran revisi (opsional).';
                currentComment = fileStage ? getP3SubCatatan(preview, fileStage) : (preview.catatan_pembimbing || '');
                submitBtn.className = 'px-6 py-2.5 rounded-xl bg-gradient-to-r from-rose-500 to-pink-500 hover:from-rose-600 hover:to-pink-600 text-white font-bold shadow-md transition flex items-center gap-2 cursor-pointer';
                submitBtn.innerHTML = '<i class="bi bi-send-fill"></i> Simpan & Minta Revisi';
            }

            if (commentEditor) commentEditor.setContent(currentComment);
            else document.getElementById('commentActionTextarea').value = currentComment;

            const modal = document.getElementById('commentActionModal');
            modal.classList.remove('hidden');
            modal.classList.add('active');
            modal.style.visibility = '';
            modal.style.display = 'flex';
            submitBtn.onclick = function() { submitFileAction(); };
            if (!commentEditor) initCommentTinyMCE();
        }

        function closeCommentActionModal() {
            const modal = document.getElementById('commentActionModal');
            if (modal) {
                modal.classList.remove('active');
                modal.classList.add('hidden');
                modal.style.display = 'none';
                modal.style.visibility = 'hidden';
                setTimeout(() => { if (modal) modal.style.visibility = ''; }, 60);
            }
            pendingAction = null;
            try {
                if (typeof commentEditor !== 'undefined' && commentEditor) {
                    try { commentEditor.destroy(); } catch (e) {}
                    commentEditor = null;
                }
            } catch (e) {}
            try {
                if (typeof tinymce !== 'undefined' && tinymce.get('commentActionTextarea')) {
                    try { tinymce.get('commentActionTextarea').remove(); } catch (e) {}
                }
            } catch (e) {}
        }

        function closePreviewOnly(nim) {
            window.activePreviews = (window.activePreviews || []).filter(p => p.nim !== nim);
            refreshLihatBerkasView();
            updateTableButtonHighlights();
        }

        function closeStudentAndPreview(nim) {
            let foundIndex = -1;
            window.activeLihatBerkasIndices.forEach((dataIdx, idx) => {
                const mhs = bimbinganData[dataIdx];
                if (mhs && mhs.nim === nim) foundIndex = idx;
            });
            if (foundIndex !== -1) {
                window.activeLihatBerkasIndices.splice(foundIndex, 1);
            }
            window.activePreviews = (window.activePreviews || []).filter(p => p.nim !== nim);

            if (window.activeLihatBerkasIndices.length === 0) {
                closeLihatBerkasPanel();
            } else {
                refreshLihatBerkasView();
            }
            updateTableButtonHighlights();
        }

        function submitFileAction() {
            if (!pendingAction) return;
            const { nim, fileIndex, action, fileStage } = pendingAction;
            let comment = commentEditor ? commentEditor.getContent() : document.getElementById('commentActionTextarea').value;
            const mhs = bimbinganData.find(m => m.nim === nim);
            if (!mhs) { closeCommentActionModal(); return; }
            const preview = mhs.riwayat_previews[fileIndex];
            if (!preview) { closeCommentActionModal(); return; }
            const idPreview = preview.id;

            const fd = new FormData();
            fd.append('id_preview', idPreview);
            fd.append('posisi', <?= $posisi ?>);
            fd.append('catatan_pembimbing', comment);
            if (action !== 'Comment') fd.append('status_pembimbing', action);
            if (fileStage) fd.append('file_stage', fileStage);

            const btn = document.getElementById('commentActionSubmit');
            const originalBtnHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> Menyimpan...';

            fetch('<?= site_url('mahasiswa/review_preview_ajax') ?>', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(data => {
                    if (data.status) {
                        showToast(data.message, 'success');

                        if (fileStage) {
                            preview['status_' + fileStage + '_p1'] = action;
                            preview['catatan_' + fileStage + '_p1'] = comment;
                        } else if (action === 'Comment') {
                            preview.catatan_pembimbing_2 = comment;
                        } else {
                            preview.status_pembimbing = action;
                            preview.catatan_pembimbing = comment;
                        }

                        closePreviewOnly(nim);
                        fetchBimbinganData(true);
                    } else {
                        showToast(data.message || 'Gagal menyimpan', 'error');
                        btn.disabled = false;
                        btn.innerHTML = originalBtnHtml;
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Kesalahan koneksi', 'error');
                    btn.disabled = false;
                    btn.innerHTML = originalBtnHtml;
                })
                .finally(() => {
                    closeCommentActionModal();
                });
        }

        // ============================================================
        // HELPERS: Files per Preview
        // ============================================================
        function getPreviewFiles(preview) {
            if (!preview) return [];
            if (currentTahap === 'Preview 3') {
                const files = [];
                if (preview.file_bimbingan) files.push({ type: 'bimbingan', label: 'File Bimbingan', file: preview.file_bimbingan, icon: 'bi-file-earmark-check-fill' });
                if (preview.file_sitasi) files.push({ type: 'sitasi', label: 'File Sitasi', file: preview.file_sitasi, icon: 'bi-quote' });
                if (preview.file_persyaratan) files.push({ type: 'persyaratan', label: 'File Persyaratan Jalur TA', file: preview.file_persyaratan, icon: 'bi-signpost-split-fill' });
                return files;
            }
            if (currentTahap === 'Preview 4') {
                if (preview.file_sidang) return [{ type: 'sidang', label: 'File Sidang', file: preview.file_sidang, icon: 'bi-file-earmark-text-fill', baseUrl: '<?= base_url('uploads/sidang/') ?>' }];
                return [];
            }
            if (preview.file_draft) return [{ type: 'draft', label: 'File Draft', file: preview.file_draft, icon: 'bi-file-earmark-pdf' }];
            return [];
        }

        function isPreviewItemActive(nim, fileIndex, fileType) {
            return window.activePreviews.some(p => p.nim === nim && p.fileIndex === fileIndex && (p.fileType || 'draft') === fileType);
        }
        function previewItemKey(nim, fileIndex, fileType) { return `${nim}_${fileIndex}_${fileType}`; }

        function refreshLihatBerkasView() {
            updateLihatBerkasLayout();
            renderAllLihatBerkasCards();
            renderAllPreviewCards();
            updateTableButtonHighlights();
        }

        function updateLihatBerkasLayout() {
            const container = document.getElementById('lihatBerkasContainer');
            const wrapperDaftar = document.getElementById('wrapperDaftarMhs');
            const wrapperPreview = document.getElementById('wrapperPreviewBerkas');
            if (!container || !wrapperDaftar || !wrapperPreview) return;
            const isMobile = window.innerWidth <= 768;
            const isPreviewActive = window.activePreviews && window.activePreviews.length > 0;
            container.style.display = 'flex';
            if (isMobile) { container.style.flexDirection = 'column'; container.style.alignItems = 'stretch'; container.style.justifyContent = 'flex-start'; container.style.overflowY = 'auto'; container.style.overflowX = 'hidden'; }
            else { container.style.flexDirection = 'row'; container.style.alignItems = 'stretch'; container.style.justifyContent = 'center'; container.style.overflowY = 'hidden'; container.style.overflowX = 'hidden'; }
            if (isMobile) wrapperDaftar.className = 'flex flex-col gap-3 w-full overflow-y-auto flex-shrink-0';
            else wrapperDaftar.className = 'flex flex-col gap-3 w-[30%] min-w-[320px] max-w-[420px] overflow-y-auto flex-shrink-0';
            if (isPreviewActive) {
                wrapperPreview.classList.add('active');
                if (isMobile) wrapperPreview.className = 'flex w-full overflow-x-auto overflow-y-visible gap-3 flex-row items-stretch flex-shrink-0';
                else wrapperPreview.className = 'flex-1 overflow-x-auto overflow-y-hidden gap-4 flex items-stretch';
            } else { wrapperPreview.classList.remove('active'); wrapperPreview.className = 'hidden'; }
        }

        function renderAllLihatBerkasCards() {
            const wrapper = document.getElementById('wrapperDaftarMhs');
            if (!wrapper) return;
            if (!window.activeLihatBerkasIndices || window.activeLihatBerkasIndices.length === 0) { wrapper.innerHTML = ''; return; }

            const totalActive = window.activeLihatBerkasIndices.length;
            let headerSummaryBar = '';
            if (totalActive > 1) {
                headerSummaryBar = `
                    <div class="bg-slate-900 text-white px-3.5 py-2 rounded-2xl flex items-center justify-between shadow-lg border border-slate-800 shrink-0 pointer-events-auto w-full animate-bar-in">
                        <div class="flex items-center gap-2"><i class="bi bi-people-fill text-orange-400 text-xs"></i><span class="text-xs font-bold">${totalActive} Mahasiswa <span class="text-slate-400 font-normal text-[10px]">(Maks. 4)</span></span></div>
                        <button type="button" onclick="closeLihatBerkasPanel()" class="text-[11px] text-slate-300 hover:text-rose-400 font-bold transition cursor-pointer">Tutup Semua</button>
                    </div>`;
            }

            let cardsHtml = '';
            window.activeLihatBerkasIndices.forEach((index, cardIdx) => {
                const mhs = bimbinganData[index];
                if (!mhs) return;
                const fileList = (mhs.riwayat_previews || []).slice(0, 2);
                const cardNum = cardIdx + 1;
                const isPreview3 = currentTahap === 'Preview 3';

                let itemsHtml = '';
                if (fileList.length === 0) {
                    itemsHtml = `<div class="p-3 text-center text-slate-400 text-xs">Tidak ada riwayat berkas.</div>`;
                } else {
                    fileList.forEach((preview, idx) => {
                        const files = getPreviewFiles(preview);
                        const posisi = <?= isset($posisi) ? $posisi : 1 ?>;

                        let headerBadge = '';
                        if (isPreview3) {
                            const appCount = [
                                getP3SubStatus(preview, 'bimbingan'),
                                getP3SubStatus(preview, 'sitasi'),
                                getP3SubStatus(preview, 'persyaratan')
                            ].filter(s => s === 'Approved').length;
                            headerBadge = `<span class="px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">${appCount}/3 Disetujui</span>`;
                        } else {
                            const status = preview.status_pembimbing || 'Pending';
                            if (status === 'Approved') headerBadge = `<span class="px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">✓ Disetujui</span>`;
                            else if (status === 'Revision') headerBadge = `<span class="px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Revisi</span>`;
                            else headerBadge = `<span class="px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Pending</span>`;
                        }

                        itemsHtml += `
                            <div class="mb-2 pb-1 border-b border-slate-100 flex items-center justify-between gap-2 px-1">
                                <div class="flex items-center gap-1.5">
                                    <i class="bi bi-folder2-open text-orange-500 text-xs"></i>
                                    <span class="text-[11px] font-bold text-slate-700">Pengajuan #${idx + 1}</span>
                                    ${headerBadge}
                                </div>
                            </div>
                        `;

                        if (files.length === 0) {
                            itemsHtml += `<div class="p-2 text-center text-slate-400 text-[11px] italic">Tidak ada file pada pengajuan ini.</div>`;
                        } else {
                            files.forEach((f) => {
                                const baseUrl = f.baseUrl || '<?= base_url('uploads/preview_ta/') ?>';
                                const fileUrl = baseUrl + f.file;
                                const fileName = f.file;
                                const isActive = isPreviewItemActive(mhs.nim, idx, f.type);
                                const activeCardBorder = isActive ? 'ring-2 ring-orange-500 border-orange-300 bg-orange-50/45' : 'border-slate-200 bg-white hover:border-slate-300';
                                const previewBtnStyle = isActive ? 'bg-orange-600 text-white border-orange-600 shadow-xs ring-2 ring-orange-400' : 'bg-orange-50 hover:bg-orange-100 text-orange-700 border-orange-200 shadow-2xs';
                                const previewBtnLabel = isActive ? 'Tutup' : 'Lihat';
                                const previewBtnIcon = isActive ? 'bi-x-circle-fill' : 'bi-eye-fill';

                                let actionButtons = '';
                                if (isPreview3) {
                                    const subSt = getP3SubStatus(preview, f.type);
                                    if (posisi === 1) {
                                        if (subSt === 'Approved') {
                                            actionButtons = `<span class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-700 border border-emerald-300 text-[10px] font-bold flex items-center gap-1"><i class="bi bi-check-circle-fill"></i> Disetujui</span>`;
                                        } else {
                                            actionButtons = `
                                                <button onclick="event.stopPropagation(); handleFileAction('${mhs.nim}', ${idx}, 'Approved', '${f.type}')" class="px-2.5 py-1 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-300 text-[10px] font-bold transition cursor-pointer flex items-center gap-1"><i class="bi bi-check-lg"></i> ACC</button>
                                                <button onclick="event.stopPropagation(); handleFileAction('${mhs.nim}', ${idx}, 'Revision', '${f.type}')" class="px-2.5 py-1 rounded-lg bg-rose-100 hover:bg-rose-200 text-rose-700 border border-rose-300 text-[10px] font-bold transition cursor-pointer flex items-center gap-1"><i class="bi bi-x-lg"></i> Revisi</button>
                                            `;
                                        }
                                    } else {
                                        actionButtons = `<span class="text-slate-400 text-[10px] italic">P1 only</span>`;
                                    }
                                } else {
                                    const status = preview.status_pembimbing || 'Pending';
                                    if (posisi === 1) {
                                        if (status === 'Approved') {
                                            actionButtons = `<span class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-700 border border-emerald-300 text-[10px] font-bold">✓ Disetujui</span>`;
                                        } else {
                                            actionButtons = `
                                                <button onclick="handleFileAction('${mhs.nim}', ${idx}, 'Approved')" class="px-2.5 py-1 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-300 text-[10px] font-bold transition cursor-pointer">ACC</button>
                                                <button onclick="handleFileAction('${mhs.nim}', ${idx}, 'Revision')" class="px-2.5 py-1 rounded-lg bg-rose-100 hover:bg-rose-200 text-rose-700 border border-rose-300 text-[10px] font-bold transition cursor-pointer">Revisi</button>
                                            `;
                                        }
                                    } else {
                                        const hasP2Comment = (preview.catatan_pembimbing_2 || '').trim().length > 0;
                                        const label = hasP2Comment ? 'Edit Komentar' : 'Komentari';
                                        const icon = hasP2Comment ? 'bi-chat-dots-fill' : 'bi-chat-dots';
                                        actionButtons = `<button onclick="handleFileAction('${mhs.nim}', ${idx}, 'Comment')" class="px-2.5 py-1 rounded-lg bg-indigo-100 hover:bg-indigo-200 text-indigo-700 border border-indigo-300 text-[10px] font-bold transition cursor-pointer flex items-center gap-1"><i class="bi ${icon} text-[10px]"></i> <span>${label}</span></button>`;
                                    }
                                }

                                let subStatusMini = '';
                                if (isPreview3) {
                                    const subSt = getP3SubStatus(preview, f.type);
                                    subStatusMini = p3SubBadgeHtml(subSt, f.label.split(' ')[1] || f.type);
                                }

                                itemsHtml += `
                                    <div class="p-2 px-2.5 rounded-xl border shadow-2xs hover:shadow-xs transition-all flex flex-col gap-2 ${activeCardBorder}">
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <i class="bi ${f.icon} text-orange-500 text-xs shrink-0"></i>
                                                    <span class="font-bold text-slate-800 text-[11px] truncate">${f.label}</span>
                                                    ${subStatusMini}
                                                </div>
                                                <p class="text-[10px] font-mono text-slate-400 truncate mt-0.5" title="${fileName}"><i class="bi bi-file-pdf text-rose-500 mr-1 text-[9px]"></i>${fileName}</p>
                                            </div>
                                            <div class="flex items-center gap-1.5 shrink-0">
                                                <button type="button" onclick="event.stopPropagation(); previewBerkasItem('${mhs.nim}', ${idx}, '${f.type}')" class="px-2.5 h-7 rounded-lg border text-[10px] font-bold transition flex items-center justify-center gap-1 cursor-pointer active:scale-95 whitespace-nowrap ${previewBtnStyle}"><i class="bi ${previewBtnIcon} text-[11px]"></i> <span>${previewBtnLabel}</span></button>
                                                <a href="${fileUrl}" download="${fileName}" target="_blank" class="w-7 h-7 rounded-lg bg-white hover:bg-slate-100 text-slate-600 border border-slate-200 text-xs font-bold transition flex items-center justify-center cursor-pointer shadow-2xs active:scale-95" title="Unduh Berkas"><i class="bi bi-download text-xs"></i></a>
                                            </div>
                                        </div>
                                        ${actionButtons ? `<div class="flex items-center gap-1.5 justify-end pt-1 border-t border-slate-100">${actionButtons}</div>` : ''}
                                    </div>
                                `;
                            });
                        }
                    });
                }

                cardsHtml += `
                    <div class="student-card-item pointer-events-auto bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-slate-200/90 overflow-hidden flex flex-col shrink-0 w-full animate-pop-in" id="cardMhs_${mhs.nim}">
                        <div class="p-2.5 px-3.5 bg-slate-900 text-white flex items-center justify-between gap-2 shrink-0 border-b border-slate-800">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-6 h-6 rounded-lg bg-orange-600/30 border border-orange-500/50 text-orange-400 flex items-center justify-center font-bold text-[11px] shrink-0 shadow-2xs">${cardNum}</div>
                                <div class="min-w-0 flex items-center gap-2">
                                    <h4 class="text-xs font-bold text-white truncate max-w-[150px] sm:max-w-[180px]">${mhs.nama_mahasiswa}</h4>
                                    <span class="px-1.5 py-0.2 rounded bg-white/10 text-orange-300 font-mono text-[10px] font-bold">${mhs.nim}</span>
                                </div>
                            </div>
                            <button type="button" onclick="removeStudentFromLihatBerkas(${index})" class="w-6 h-6 rounded-md bg-white/10 hover:bg-rose-600/80 text-slate-300 hover:text-white flex items-center justify-center text-[11px] font-bold transition-colors cursor-pointer" title="Tutup Mahasiswa Ini"><i class="bi bi-x-lg"></i></button>
                        </div>
                        <div class="p-2.5 space-y-1.5 bg-slate-50/50 overflow-y-auto">${itemsHtml}</div>
                    </div>
                `;
            });

            wrapper.innerHTML = headerSummaryBar + cardsHtml;
        }

        function renderAllPreviewCards() {
            const wrapper = document.getElementById('wrapperPreviewBerkas');
            if (!wrapper) return;
            if (!window.activePreviews || window.activePreviews.length === 0) { wrapper.innerHTML = ''; wrapper.classList.remove('active'); return; }
            wrapper.classList.add('active');

            const isPreview3 = currentTahap === 'Preview 3';
            const posisi = <?= isset($posisi) ? $posisi : 1 ?>;

            let html = '';
            window.activePreviews.forEach((p, idx) => {
                const mhs = bimbinganData.find(m => m.nim === p.nim);
                if (!mhs) return;
                const preview = mhs.riwayat_previews[p.fileIndex];
                if (!preview) return;

                const fileType = p.fileType || 'draft';
                const files = getPreviewFiles(preview);
                const fileObj = files.find(f => f.type === fileType) || files[0];
                if (!fileObj) return;

                const baseUrl = fileObj.baseUrl || '<?= base_url('uploads/preview_ta/') ?>';
                const fileUrl = baseUrl + fileObj.file;
                const fileName = fileObj.file;
                const fileLabel = fileObj.label;
                const fullName = mhs.nama_mahasiswa || 'Mahasiswa';
                const slotNum = idx + 1;
                const uniqueKey = previewItemKey(p.nim, p.fileIndex, fileType);

                let actionButtons = '';
                if (isPreview3) {
                    const subSt = getP3SubStatus(preview, fileType);
                    if (posisi === 1) {
                        if (subSt === 'Approved') {
                            actionButtons = `<span class="px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-700 border border-emerald-300 text-xs font-bold flex items-center gap-1.5"><i class="bi bi-check-circle-fill"></i> Disetujui</span>`;
                        } else {
                            actionButtons = `
                                <button onclick="handleFileAction('${p.nim}', ${p.fileIndex}, 'Approved', '${fileType}')" class="px-3 py-1.5 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-300 text-xs font-bold transition cursor-pointer flex items-center gap-1.5"><i class="bi bi-check-lg"></i> ACC</button>
                                <button onclick="handleFileAction('${p.nim}', ${p.fileIndex}, 'Revision', '${fileType}')" class="px-3 py-1.5 rounded-lg bg-rose-100 hover:bg-rose-200 text-rose-700 border border-rose-300 text-xs font-bold transition cursor-pointer flex items-center gap-1.5"><i class="bi bi-x-lg"></i> Revisi</button>
                            `;
                        }
                    } else {
                        actionButtons = `<span class="text-slate-400 text-[10px] italic">Hanya P1 yang bisa ACC file ini</span>`;
                    }
                } else {
                    const status = preview.status_pembimbing || 'Pending';
                    if (posisi === 1) {
                        if (status === 'Approved') actionButtons = `<span class="px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-700 border border-emerald-300 text-xs font-bold">✓ Disetujui</span>`;
                        else actionButtons = `
                            <button onclick="handleFileAction('${p.nim}', ${p.fileIndex}, 'Approved')" class="px-3 py-1.5 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-300 text-xs font-bold transition cursor-pointer">ACC</button>
                            <button onclick="handleFileAction('${p.nim}', ${p.fileIndex}, 'Revision')" class="px-3 py-1.5 rounded-lg bg-rose-100 hover:bg-rose-200 text-rose-700 border border-rose-300 text-xs font-bold transition cursor-pointer">Revisi</button>
                        `;
                    } else {
                        const hasP2 = (preview.catatan_pembimbing_2 || '').trim().length > 0;
                        const label = hasP2 ? 'Edit Komentar' : 'Komentari';
                        const icon = hasP2 ? 'bi-chat-dots-fill' : 'bi-chat-dots';
                        actionButtons = `<button onclick="handleFileAction('${p.nim}', ${p.fileIndex}, 'Comment')" class="px-3 py-1.5 rounded-lg bg-indigo-100 hover:bg-indigo-200 text-indigo-700 border border-indigo-300 text-xs font-bold transition cursor-pointer flex items-center gap-1.5"><i class="bi ${icon}"></i> <span>${label}</span></button>`;
                    }
                }

                html += `
                    <div class="preview-card-item pointer-events-auto bg-white rounded-3xl shadow-2xl border border-slate-200/90 overflow-hidden flex flex-col shrink-0 animate-preview-in" id="previewCard_${uniqueKey}">
                        <div class="preview-header p-2.5 px-3.5 bg-slate-900 text-white flex items-center justify-between gap-2.5 shrink-0 border-b border-slate-800">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="preview-slot-badge w-7 h-7 rounded-lg bg-rose-600/30 border border-rose-500/50 text-rose-400 flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">${window.activePreviews.length > 1 ? slotNum : '<i class="bi bi-file-pdf"></i>'}</div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-white truncate max-w-[190px] sm:max-w-[240px]">${fileLabel} · Pengajuan #${p.fileIndex + 1}</h4>
                                    <p class="text-[10px] text-slate-300 font-medium truncate">${fullName} · <span class="font-mono text-slate-400">${fileName}</span></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                <button type="button" onclick="togglePreviewIframeInteraction('${p.nim}', ${p.fileIndex}, '${fileType}')" id="btnPreviewInteract_${uniqueKey}" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer" title="Kursor Terkunci">
                                    <i class="bi bi-arrow-pointer" id="iconPreviewInteract_${uniqueKey}"></i>
                                </button>
                                <a href="${fileUrl}" target="_blank" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer" title="Buka di tab baru"><i class="bi bi-box-arrow-up-right"></i></a>
                                <button type="button" onclick="closeSinglePreview(${idx})" class="preview-close-btn w-7 h-7 rounded-lg bg-white/10 hover:bg-rose-600/80 text-slate-300 hover:text-white flex items-center justify-center text-xs font-bold transition-colors cursor-pointer ml-0.5" title="Tutup"><i class="bi bi-x-lg"></i></button>
                            </div>
                        </div>
                        <div class="preview-body" style="height: 85vh; min-height: 300px; max-height: 90vh;">
                            <div class="loader" id="previewLoader_${uniqueKey}"><i class="bi bi-arrow-repeat"></i> Memuat dokumen...</div>
                            <iframe id="iframePreviewBerkas_${uniqueKey}" src="${fileUrl}#toolbar=0&navpanes=0" class="w-full h-full border-0 relative z-10 pointer-events-none" onload="document.getElementById('previewLoader_${uniqueKey}').style.display='none'" title="Pratinjau Berkas"></iframe>
                        </div>
                        <div class="preview-footer p-2 px-3 bg-white flex items-center justify-between text-xs shrink-0 gap-2 border-t border-slate-200">
                            <div class="flex items-center gap-1.5">${actionButtons}</div>
                            <div class="flex items-center gap-1.5">
                                <a href="${fileUrl}" download="${fileName}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 font-bold transition flex items-center gap-1.5 cursor-pointer shadow-2xs text-[11px]"><i class="bi bi-download"></i><span>Unduh</span></a>
                                <button type="button" onclick="closeSinglePreview(${idx})" class="px-2.5 py-1 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold transition cursor-pointer text-[11px] shadow-2xs">Tutup</button>
                            </div>
                        </div>
                    </div>
                `;
            });
            wrapper.innerHTML = html;
        }

        function previewBerkasItem(nim, fileIndex, fileType = 'draft') {
            const existingIdx = window.activePreviews.findIndex(p => p.nim === nim && p.fileIndex === fileIndex && (p.fileType || 'draft') === fileType);
            if (existingIdx > -1) { window.activePreviews.splice(existingIdx, 1); refreshLihatBerkasView(); return; }
            if (window.activePreviews.length >= 5) window.activePreviews.shift();
            window.activePreviews.push({ nim, fileIndex, fileType });
            refreshLihatBerkasView();
            if (window.innerWidth <= 768) setTimeout(() => { const previewWrap = document.getElementById('wrapperPreviewBerkas'); if (previewWrap) previewWrap.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 150);
        }

        function closeSinglePreview(index) {
            if (index < 0 || index >= window.activePreviews.length) return;
            window.activePreviews.splice(index, 1);
            refreshLihatBerkasView();
        }

        function togglePreviewIframeInteraction(nim, fileIndex, fileType = 'draft') {
            const uniqueKey = previewItemKey(nim, fileIndex, fileType);
            const iframe = document.getElementById(`iframePreviewBerkas_${uniqueKey}`);
            const btn = document.getElementById(`btnPreviewInteract_${uniqueKey}`);
            const icon = document.getElementById(`iconPreviewInteract_${uniqueKey}`);
            if (!iframe) return;
            const isLocked = iframe.classList.contains('pointer-events-none');
            if (isLocked) {
                iframe.classList.remove('pointer-events-none');
                if (btn) { btn.className = 'w-7 h-7 rounded-lg bg-orange-600 text-white flex items-center justify-center text-xs transition cursor-pointer shadow-2xs'; btn.title = 'Mode Scroll Aktif.'; }
                if (icon) icon.className = 'bi bi-hand';
            } else {
                iframe.classList.add('pointer-events-none');
                if (btn) { btn.className = 'w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer'; btn.title = 'Kursor Normal (Terkunci).'; }
                if (icon) icon.className = 'bi bi-arrow-pointer';
            }
        }

        function removeStudentFromLihatBerkas(index) {
            const idx = window.activeLihatBerkasIndices.indexOf(index);
            if (idx === -1) return;
            window.activeLihatBerkasIndices.splice(idx, 1);
            const mhs = bimbinganData[index];
            if (mhs) window.activePreviews = window.activePreviews.filter(p => p.nim !== mhs.nim);
            if (window.activeLihatBerkasIndices.length === 0) closeLihatBerkasPanel();
            else refreshLihatBerkasView();
            updateTableButtonHighlights();
        }

        function toggleLihatBerkasPanel(index) {
            const mhs = bimbinganData[index];
            if (!mhs || !mhs.riwayat_previews || mhs.riwayat_previews.length === 0) { showToast('Mahasiswa ini belum memiliki berkas yang diunggah.', 'error'); return; }
            const idx = window.activeLihatBerkasIndices.indexOf(index);
            if (idx > -1) { removeStudentFromLihatBerkas(index); return; }
            if (window.activeLihatBerkasIndices.length >= 4) {
                const removed = window.activeLihatBerkasIndices.shift();
                const removedMhs = bimbinganData[removed];
                if (removedMhs) window.activePreviews = window.activePreviews.filter(p => p.nim !== removedMhs.nim);
            }
            window.activeLihatBerkasIndices.push(index);
            showLihatBerkasContainer();
            refreshLihatBerkasView();
        }

        function showLihatBerkasContainer() {
            const container = document.getElementById('lihatBerkasContainer');
            if (container) { container.style.display = 'flex'; container.classList.add('active'); }
            const wrapperPreview = document.getElementById('wrapperPreviewBerkas');
            if (wrapperPreview) { wrapperPreview.classList.remove('active'); wrapperPreview.innerHTML = ''; }
            updateLihatBerkasLayout();
        }

        function closeLihatBerkasPanel() {
            const container = document.getElementById('lihatBerkasContainer');
            if (container) { container.style.display = 'none'; container.classList.remove('active'); }
            const wrapperDaftar = document.getElementById('wrapperDaftarMhs');
            if (wrapperDaftar) wrapperDaftar.innerHTML = '';
            const wrapperPreview = document.getElementById('wrapperPreviewBerkas');
            if (wrapperPreview) { wrapperPreview.innerHTML = ''; wrapperPreview.classList.remove('active'); }
            window.activeLihatBerkasIndices = [];
            window.activePreviews = [];
            updateTableButtonHighlights();
        }

        function updateTableButtonHighlights() {
            const activeIndices = window.activeLihatBerkasIndices || [];
            document.querySelectorAll('#bimbinganTableBody tr').forEach((tr) => {
                const dataIdx = tr.getAttribute('data-index');
                if (dataIdx === null) return;
                const idx = parseInt(dataIdx);
                const btn = tr.querySelector('button[onclick^="toggleLihatBerkasPanel"]');
                if (btn) {
                    const isActive = activeIndices.includes(idx);
                    if (isActive) {
                        btn.className = 'inline-flex items-center gap-1.5 text-white font-bold px-3 py-1.5 rounded-xl text-xs cursor-pointer shadow-md transition-transform bg-orange-600 ring-2 ring-orange-400 scale-105';
                        btn.innerHTML = '<i class="bi bi-eye-fill text-xs"></i> Melihat';
                    } else {
                        btn.className = 'inline-flex items-center gap-1.5 text-white font-bold px-3 py-1.5 rounded-xl text-xs cursor-pointer shadow-md transition-transform btn-3d-orange';
                        btn.innerHTML = '<i class="bi bi-folder-open text-xs"></i> Lihat Berkas';
                    }
                }
            });
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                if (document.getElementById('commentActionModal').classList.contains('active')) { closeCommentActionModal(); return; }
                if (window.activeLihatBerkasIndices && window.activeLihatBerkasIndices.length > 0) closeLihatBerkasPanel();
            }
        });
        window.addEventListener('resize', () => { if (window.activeLihatBerkasIndices && window.activeLihatBerkasIndices.length > 0) updateLihatBerkasLayout(); });

        window.toggleLihatBerkasPanel = toggleLihatBerkasPanel;
        window.closeLihatBerkasPanel = closeLihatBerkasPanel;
        window.previewBerkasItem = previewBerkasItem;
        window.closeSinglePreview = closeSinglePreview;
        window.togglePreviewIframeInteraction = togglePreviewIframeInteraction;
        window.updateTableButtonHighlights = updateTableButtonHighlights;
        window.removeStudentFromLihatBerkas = removeStudentFromLihatBerkas;
        window.openReviewModal = openReviewModal;
        window.closeReviewModal = closeReviewModal;
        window.showCommentModal = showCommentModal;
        window.closeCommentModal = closeCommentModal;
        window.showHoverPanel = showHoverPanel;
        window.scheduleHidePanel = scheduleHidePanel;
        window.submitHoverReview = submitHoverReview;
        window.handleFileAction = handleFileAction;
        window.closeCommentActionModal = closeCommentActionModal;
        window.switchDosenTab = switchDosenTab;
        window.setDosenFilter = setDosenFilter;
        window.toggleSearchCatMenu = toggleSearchCatMenu;
        window.setSearchCat = setSearchCat;
        window.doSearch = doSearch;
        window.changePerPage = changePerPage;
        window.goToPage = goToPage;
        window.openSingleBatchModal = openSingleBatchModal;
        window.openDosenBatchModal = openDosenBatchModal;
        window.closeDosenBatchModal = closeDosenBatchModal;
        window.submitDosenBatchApprove = submitDosenBatchApprove;
        window.unselectAllDosenStudents = unselectAllDosenStudents;
        window.submitBatchItemReview = submitBatchItemReview;
        window.showUnifiedCommentModal = showUnifiedCommentModal;
        window.switchCommentTab = switchCommentTab;
    </script>

    <!-- ============================================================
         SCRIPT PENILAIAN SIDANG (Preview 4)
         ============================================================ -->
    <script>
        // ============================================================
        // RUBRIK PENILAIAN PRODI
        // ============================================================
        const RUBRIK_PENILAIAN_PRODI = {
            'DKV': {
                peminatan: {
                    'Desain Grafis': [
                        { id: 'k1', title: 'Kreativitas & Orisinalitas', desc: 'Kemampuan menghasilkan ide desain yang baru dan unik.', bobot: 20 },
                        { id: 'k2', title: 'Eksekusi Visual', desc: 'Penguasaan elemen dan prinsip desain (tipografi, warna, komposisi).', bobot: 25 },
                        { id: 'k3', title: 'Keselarasan Konsep & Solusi', desc: 'Seberapa baik karya menjawab permasalahan/brief yang ada.', bobot: 25 },
                        { id: 'k4', title: 'Keterampilan Teknis & Media', desc: 'Penguasaan perangkat lunak atau teknik manual yang digunakan.', bobot: 15 },
                        { id: 'k5', title: 'Presentasi & Komunikasi', desc: 'Kemampuan mempresentasikan karya dan mempertahankan argumen.', bobot: 15 }
                    ],
                    'Multimedia': [
                        { id: 'k1', title: 'Kreativitas Ide & Narasi', desc: 'Kekuatan cerita (storytelling) dan orisinalitas ide.', bobot: 20 },
                        { id: 'k2', title: 'Eksekusi Audio-Visual', desc: 'Kualitas sinematografi, animasi, suara, dan penyuntingan.', bobot: 25 },
                        { id: 'k3', title: 'Fungsionalitas & Interaktivitas', desc: 'Tingkat kebergunaan dan interaksi pada media interaktif.', bobot: 25 },
                        { id: 'k4', title: 'Keterampilan Teknis Media', desc: 'Penguasaan teknologi, software, atau peralatan yang digunakan.', bobot: 15 },
                        { id: 'k5', title: 'Presentasi Karya', desc: 'Sistematika penyajian, artikulasi, dan kemampuan menjawab.', bobot: 15 }
                    ]
                }
            }
        };

        function _penilaianEscapeHtml(unsafe) {
            if (unsafe === null || unsafe === undefined) return '';
            return String(unsafe)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function detectProdiKey(prodiName) {
            if (!prodiName) return 'DKV';
            const p = String(prodiName).toLowerCase();
            if (p.includes('sistem informasi')) return 'SI';
            if (p.includes('informatika') || p.includes('ilmu komputer')) return 'IF';
            if (p.includes('bisnis digital')) return 'BD';
            if (p.includes('kewirausahaan')) return 'KWU';
            if (p.includes('dkv') || p.includes('desain komunikasi visual')) return 'DKV';
            return 'DKV';
        }

        // ============================================================
        // OPEN MODAL PENILAIAN
        // ============================================================
        window.openModalPenilaianSidang = function (nim) {
            // FIX: struktur bimbinganData dosen = { nim, nama_mahasiswa, judul, latest_preview, ... }
            const student = bimbinganData.find(s => String(s.nim) === String(nim));

            if (!student) {
                console.warn('[Penilaian] Mahasiswa tidak ditemukan untuk NIM:', nim);
                if (typeof showToast === 'function') showToast('Data mahasiswa tidak ditemukan.', 'error');
                return;
            }

            const modal = document.getElementById('modalPenilaianSidang');
            if (!modal) {
                console.error('[Penilaian] #modalPenilaianSidang tidak ada di DOM.');
                return;
            }

            const inputNim     = document.getElementById('penilaianNim');
            const namaMhsEl    = document.getElementById('penilaianNamaMhs');
            const nimMhsEl     = document.getElementById('penilaianNimMhs');
            const judulEl      = document.getElementById('penilaianJudulTa');
            const tglTextEl    = document.getElementById('penilaianTglText');
            const ruanganTextEl= document.getElementById('penilaianRuanganText');
            const pb1El        = document.getElementById('penilaianPembimbing1');
            const pb2El        = document.getElementById('penilaianPembimbing2');
            const pg1El        = document.getElementById('penilaianPenguji1');
            const pg2El        = document.getElementById('penilaianPenguji2');
            const prodiSelect  = document.getElementById('penilaianProdiSelect');
            const prodiBadge   = document.getElementById('penilaianProdiBadge');
            const catatanEl    = document.getElementById('penilaianCatatan');
            const statusKelEl  = document.getElementById('penilaianStatusKelulusan');
            const versiBadgeEl = document.getElementById('penilaianVersiBadge');

            const latest = student.latest_preview || {};

            if (inputNim)      inputNim.value = student.nim;
            if (namaMhsEl)     namaMhsEl.textContent = student.nama_mahasiswa || 'Mahasiswa';
            if (nimMhsEl)      nimMhsEl.textContent = 'NIM: ' + (student.nim || '-');
            if (judulEl)       judulEl.textContent = student.judul || '-';
            if (versiBadgeEl)  versiBadgeEl.textContent = 'v1';
            if (tglTextEl)     tglTextEl.textContent = latest.tgl_sidang || 'Belum Ada Jadwal';
            if (ruanganTextEl) ruanganTextEl.textContent = latest.ruangan_sidang || 'Belum Ditentukan';

            if (pb1El) pb1El.textContent = 'Pembimbing 1: ' + (latest.pembimbing_1 || '-');
            if (pb2El) pb2El.textContent = 'Pembimbing 2: ' + (latest.pembimbing_2 || '-');
            if (pg1El) pg1El.textContent = 'Penguji 1: ' + (latest.penguji_1 || '-');
            if (pg2El) pg2El.textContent = 'Penguji 2: ' + (latest.penguji_2 || '-');

            const prodiKey = detectProdiKey(student.prodi || student.konsentrasi_dkv);
            if (prodiSelect) prodiSelect.value = prodiKey;
            if (prodiBadge)  prodiBadge.textContent = prodiKey;

            // Reset publikasi
            const currentPub = latest.status_publish_sidang || 'Draft';
            if (currentPub === 'Published') {
                const rad = document.getElementById('publishRadioInstant');
                if (rad) rad.checked = true;
                onPublishModeChange('Published');
            } else if (currentPub === 'Scheduled') {
                const rad = document.getElementById('publishRadioScheduled');
                if (rad) rad.checked = true;
                onPublishModeChange('Scheduled');
                const dateInp = document.getElementById('penilaianTglPublish');
                if (dateInp && latest.tgl_publish_sidang) {
                    dateInp.value = String(latest.tgl_publish_sidang).replace(' ', 'T').substring(0, 16);
                }
            } else {
                const rad = document.getElementById('publishRadioDraft');
                if (rad) rad.checked = true;
                onPublishModeChange('Draft');
            }

            setupTahunAkademikOptions(latest.tahun_akademik);

            // ✅ BUKA MODAL DULU — baru fetch detail.
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.style.display = 'flex';
            document.body.classList.add('overflow-hidden');

            // Fetch detail opsional dari koordinatorta (kalau ada)
            const getDetailUrl = '<?= site_url('koordinatorta/ajax_get_detail_penilaian_sidang') ?>';
            fetch(getDetailUrl + '?nim=' + encodeURIComponent(nim))
                .then(r => r.json())
                .then(res => {
                    if (res && res.status && res.data) {
                        const d = res.data;
                        if (d.catatan_koor && catatanEl) catatanEl.value = d.catatan_koor;
                        if (d.status_kelulusan_sidang && statusKelEl) statusKelEl.value = d.status_kelulusan_sidang;
                        if (d.versi_penilaian && versiBadgeEl) versiBadgeEl.textContent = 'v' + d.versi_penilaian;
                        if (d.tahun_akademik) setupTahunAkademikOptions(d.tahun_akademik);

                        if (d.status_publish_sidang) {
                            if (d.status_publish_sidang === 'Published') {
                                const rad = document.getElementById('publishRadioInstant');
                                if (rad) rad.checked = true;
                                onPublishModeChange('Published');
                            } else if (d.status_publish_sidang === 'Scheduled') {
                                const rad = document.getElementById('publishRadioScheduled');
                                if (rad) rad.checked = true;
                                onPublishModeChange('Scheduled');
                                const dateInp = document.getElementById('penilaianTglPublish');
                                if (dateInp && d.tgl_publish_sidang) {
                                    dateInp.value = String(d.tgl_publish_sidang).replace(' ', 'T').substring(0, 16);
                                }
                            } else {
                                const rad = document.getElementById('publishRadioDraft');
                                if (rad) rad.checked = true;
                                onPublishModeChange('Draft');
                            }
                        }

                        setupPenilaianPeminatanOptions(prodiKey, d.peminatan || student.peminatan);
                        renderPenilaianRubrik(d.detail_penilaian_parsed);
                        calculatePenilaianScore();
                    } else {
                        setupPenilaianPeminatanOptions(prodiKey, student.peminatan);
                        renderPenilaianRubrik(null);
                        calculatePenilaianScore();
                    }
                })
                .catch(err => {
                    console.warn('[Penilaian] Gagal fetch detail (endpoint mungkin belum tersedia untuk dosen):', err);
                    setupPenilaianPeminatanOptions(prodiKey, student.peminatan);
                    renderPenilaianRubrik(null);
                    calculatePenilaianScore();
                });
        };

        window.closeModalPenilaianSidang = function () {
            const modal = document.getElementById('modalPenilaianSidang');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                modal.style.display = 'none';
                document.body.classList.remove('overflow-hidden');
            }
        };

        // ============================================================
        // HELPER: Tahun Akademik
        // ============================================================
        function setupTahunAkademikOptions(selectedYear) {
            const input = document.getElementById('penilaianTahunAkademik');
            const datalist = document.getElementById('listTahunAkademik');
            if (!input) return;

            const now = new Date();
            const curYear = now.getFullYear();
            const curMonth = now.getMonth() + 1;
            const defaultActive = (curMonth >= 8)
                ? (curYear + '/' + (curYear + 1))
                : ((curYear - 1) + '/' + curYear);
            const activeTarget = selectedYear || defaultActive;

            input.value = activeTarget;

            if (datalist) {
                const yearList = [];
                for (let y = curYear + 3; y >= curYear - 5; y--) {
                    yearList.push(y + '/' + (y + 1));
                }
                if (activeTarget && !yearList.includes(activeTarget)) yearList.unshift(activeTarget);

                let html = '';
                yearList.forEach(ta => {
                    const isCur = (ta === defaultActive) ? ' (Aktif)' : '';
                    html += '<option value="' + ta + '">' + ta + isCur + '</option>';
                });
                datalist.innerHTML = html;
            }
        }

        function setupPenilaianPeminatanOptions(prodiKey, selectedPeminatan) {
            const peminatanSelect = document.getElementById('penilaianPeminatanSelect');
            if (!peminatanSelect) return;

            const prodiData = RUBRIK_PENILAIAN_PRODI[prodiKey] || RUBRIK_PENILAIAN_PRODI['DKV'];
            const peminatanList = Object.keys(prodiData.peminatan || {});

            let html = '';
            peminatanList.forEach(p => {
                const isSel = (p === selectedPeminatan) || (!selectedPeminatan && p === peminatanList[0]);
                html += '<option value="' + p + '"' + (isSel ? ' selected' : '') + '>' + p + '</option>';
            });
            peminatanSelect.innerHTML = html;
        }

        window.onPenilaianProdiChange = function (prodiVal) {
            const prodiKey = detectProdiKey(prodiVal);
            const prodiBadge = document.getElementById('penilaianProdiBadge');
            if (prodiBadge) prodiBadge.textContent = prodiKey;

            setupPenilaianPeminatanOptions(prodiKey, null);
            renderPenilaianRubrik(null);
            calculatePenilaianScore();
        };

        window.onPenilaianPeminatanChange = function () {
            renderPenilaianRubrik(null);
            calculatePenilaianScore();
        };

        // ============================================================
        // PUBLISH MODE
        // ============================================================
        window.onPublishModeChange = function (mode) {
            const dateInput = document.getElementById('penilaianTglPublish');
            const pillEl    = document.getElementById('currentPublishStatusPill');
            const dateHint  = document.getElementById('publishDateHint');

            if (mode === 'Draft') {
                if (dateInput) { dateInput.disabled = true; dateInput.value = ''; }
                if (pillEl) {
                    pillEl.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700 border border-slate-200';
                    pillEl.innerHTML = '<i class="fa-solid fa-eye-slash mr-1"></i> Draft (Privat)';
                }
                if (dateHint) dateHint.textContent = 'Nilai hanya disimpan dan belum dirilis.';
            } else if (mode === 'Published') {
                if (dateInput) {
                    dateInput.disabled = true;
                    const n = new Date();
                    const pad = x => String(x).padStart(2, '0');
                    dateInput.value = n.getFullYear() + '-' + pad(n.getMonth() + 1) + '-' + pad(n.getDate()) + 'T' + pad(n.getHours()) + ':' + pad(n.getMinutes());
                }
                if (pillEl) {
                    pillEl.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-2xs';
                    pillEl.innerHTML = '<i class="fa-solid fa-globe mr-1"></i> Publikasikan Sekarang (Live)';
                }
                if (dateHint) dateHint.textContent = 'Nilai langsung terbit dan dapat dilihat oleh mahasiswa.';
            } else if (mode === 'Scheduled') {
                if (dateInput) {
                    dateInput.disabled = false;
                    if (!dateInput.value) {
                        const t = new Date();
                        t.setDate(t.getDate() + 1);
                        t.setHours(9, 0, 0, 0);
                        const pad = x => String(x).padStart(2, '0');
                        dateInput.value = t.getFullYear() + '-' + pad(t.getMonth() + 1) + '-' + pad(t.getDate()) + 'T' + pad(t.getHours()) + ':' + pad(t.getMinutes());
                    }
                }
                if (pillEl) {
                    pillEl.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-sky-100 text-sky-800 border border-sky-300 shadow-2xs';
                    pillEl.innerHTML = '<i class="fa-solid fa-clock mr-1"></i> Terjadwal Otomatis';
                }
                if (dateHint) dateHint.textContent = 'Nilai otomatis terbuka untuk mahasiswa setelah waktu di atas tercapai.';
            }
        };

        // ============================================================
        // RENDER RUBRIK
        // ============================================================
        window.renderPenilaianRubrik = function (existingParsed) {
            const container = document.getElementById('penilaianRubrikContainer');
            const prodiSelect = document.getElementById('penilaianProdiSelect');
            const peminatanSelect = document.getElementById('penilaianPeminatanSelect');
            const totalBobotLabel = document.getElementById('penilaianTotalBobotLabel');
            if (!container) return;

            const prodiKey = detectProdiKey(prodiSelect ? prodiSelect.value : 'DKV');
            const peminatanKey = peminatanSelect ? peminatanSelect.value : '';

            let criteriaList = [];
            if (existingParsed && existingParsed.criteria && Array.isArray(existingParsed.criteria) && existingParsed.criteria.length > 0) {
                criteriaList = existingParsed.criteria;
            } else if (Array.isArray(existingParsed) && existingParsed.length > 0 && (existingParsed[0].kriteria || existingParsed[0].title)) {
                criteriaList = existingParsed;
            } else {
                const prodiData = RUBRIK_PENILAIAN_PRODI[prodiKey] || RUBRIK_PENILAIAN_PRODI['DKV'];
                criteriaList = prodiData.peminatan[peminatanKey] || prodiData.peminatan[Object.keys(prodiData.peminatan)[0]] || [];
            }

            let totalCalculatedBobot = 0;
            let html = '';

            criteriaList.forEach((crit, idx) => {
                let existingScore = '';
                const critId    = crit.id || ('k' + idx);
                const critTitle = crit.title || crit.kriteria || ('Penilaian ' + (idx + 1));
                const critDesc  = crit.desc || crit.deskripsi || 'Indikator penilaian kompetensi karya sidang tugas akhir.';
                const critBobot = parseFloat(crit.bobot) || 25;
                totalCalculatedBobot += critBobot;

                if (typeof crit.nilai !== 'undefined' && crit.nilai !== null && crit.nilai !== '') {
                    existingScore = crit.nilai;
                } else if (existingParsed && existingParsed.scores && typeof existingParsed.scores[critId] !== 'undefined') {
                    existingScore = existingParsed.scores[critId];
                } else if (Array.isArray(existingParsed)) {
                    const found = existingParsed.find(ep => ep.id === critId || (ep.kriteria && ep.kriteria === critTitle));
                    if (found && typeof found.nilai !== 'undefined') existingScore = found.nilai;
                    else if (existingParsed[idx] && typeof existingParsed[idx].nilai !== 'undefined') existingScore = existingParsed[idx].nilai;
                }

                html += `
                    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-2xs hover:border-amber-400 hover:shadow-md transition-all space-y-2.5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-600 text-white flex items-center justify-center font-black text-xs shrink-0 shadow-md shadow-amber-500/20 mt-0.5">
                                    ${idx + 1}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-800 border border-amber-200">
                                            Kriteria #${idx + 1}
                                        </span>
                                        <h5 class="text-xs sm:text-sm font-extrabold text-slate-900">${_penilaianEscapeHtml(critTitle)}</h5>
                                    </div>
                                    <p class="text-[11px] text-slate-500 leading-relaxed mt-1">${_penilaianEscapeHtml(critDesc)}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 shrink-0 self-end sm:self-center">
                                <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200">
                                    Bobot: <strong class="text-amber-700">${critBobot}%</strong>
                                </span>
                                <div class="w-28">
                                    <input type="number"
                                           min="0" max="100" step="0.5"
                                           data-bobot="${critBobot}"
                                           data-crit-id="${_penilaianEscapeHtml(critId)}"
                                           data-crit-title="${_penilaianEscapeHtml(critTitle)}"
                                           data-crit-desc="${_penilaianEscapeHtml(critDesc)}"
                                           value="${existingScore}"
                                           placeholder="Nilai (0-100)"
                                           oninput="calculatePenilaianScore()"
                                           class="rubrik-score-input w-full px-3 py-2 bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 rounded-xl text-center text-sm font-black text-slate-900 outline-none transition"
                                           required>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            if (totalBobotLabel) {
                totalBobotLabel.textContent = totalCalculatedBobot + '%';
                totalBobotLabel.className = (totalCalculatedBobot === 100)
                    ? 'text-slate-700 font-extrabold'
                    : 'text-rose-600 font-extrabold';
            }
            container.innerHTML = html;
        };

        // ============================================================
        // HITUNG SKOR
        // ============================================================
        window.calculatePenilaianScore = function () {
            const inputs = document.querySelectorAll('.rubrik-score-input');
            const totalScoreEl = document.getElementById('penilaianTotalScore');
            const gradeBadgeEl = document.getElementById('penilaianGradeBadge');
            const statusKelEl  = document.getElementById('penilaianStatusKelulusan');

            let totalWeighted = 0;
            let hasAnyInput = false;

            inputs.forEach(inp => {
                const val = parseFloat(inp.value);
                const bobot = parseFloat(inp.getAttribute('data-bobot')) || 0;
                if (!isNaN(val)) {
                    totalWeighted += (val * bobot) / 100;
                    hasAnyInput = true;
                }
            });

            const finalScore = hasAnyInput ? totalWeighted : 0;
            if (totalScoreEl) totalScoreEl.textContent = finalScore.toFixed(2);

            let grade = '-';
            let gradeClass = 'bg-slate-100 text-slate-600 border-slate-200';

            if (hasAnyInput) {
                if (finalScore >= 85)         { grade = 'A';  gradeClass = 'bg-emerald-100 text-emerald-800 border-emerald-300'; }
                else if (finalScore >= 77.5)  { grade = 'AB'; gradeClass = 'bg-teal-100 text-teal-800 border-teal-300'; }
                else if (finalScore >= 70)    { grade = 'B';  gradeClass = 'bg-cyan-100 text-cyan-800 border-cyan-300'; }
                else if (finalScore >= 62.5)  { grade = 'BC'; gradeClass = 'bg-amber-100 text-amber-800 border-amber-300'; }
                else if (finalScore >= 55)    { grade = 'C';  gradeClass = 'bg-yellow-100 text-yellow-800 border-yellow-300'; }
                else if (finalScore >= 45)    { grade = 'D';  gradeClass = 'bg-rose-100 text-rose-800 border-rose-300'; }
                else                          { grade = 'E';  gradeClass = 'bg-rose-200 text-rose-900 border-rose-400'; }
            }

            if (gradeBadgeEl) {
                gradeBadgeEl.textContent = grade;
                gradeBadgeEl.className = 'px-4 py-1 rounded-xl text-lg font-black border shadow-2xs ' + gradeClass;
            }

            if (statusKelEl && hasAnyInput) {
                if (finalScore >= 70) statusKelEl.value = 'Lulus';
                else if (finalScore >= 55) statusKelEl.value = 'Lulus dengan Revisi';
                else statusKelEl.value = 'Tidak Lulus';
            }

            return { score: finalScore, grade: grade };
        };

        // ============================================================
        // SUBMIT
        // ============================================================
        window.submitPenilaianSidang = function (e) {
            e.preventDefault();
            const nim = document.getElementById('penilaianNim')?.value;
            const prodi = document.getElementById('penilaianProdiSelect')?.value;
            const peminatan = document.getElementById('penilaianPeminatanSelect')?.value;
            const tahunAkademik = document.getElementById('penilaianTahunAkademik')?.value || '';
            const statusKelulusan = document.getElementById('penilaianStatusKelulusan')?.value;
            const catatan = document.getElementById('penilaianCatatan')?.value;
            const calc = calculatePenilaianScore();

            const statusPublish = document.querySelector('input[name="status_publish"]:checked')?.value || 'Draft';
            const tglPublishInp = document.getElementById('penilaianTglPublish')?.value || '';

            if (!nim) { Swal.fire({ icon: 'error', title: 'Error', text: 'NIM mahasiswa tidak valid.' }); return; }

            const scoreInputs = document.querySelectorAll('.rubrik-score-input');
            const details = [];
            let allValid = true;
            scoreInputs.forEach(inp => {
                const val = parseFloat(inp.value);
                if (isNaN(val) || val < 0 || val > 100) allValid = false;
                details.push({
                    id: inp.getAttribute('data-crit-id'),
                    title: inp.getAttribute('data-crit-title'),
                    desc: inp.getAttribute('data-crit-desc') || '',
                    bobot: parseFloat(inp.getAttribute('data-bobot')),
                    nilai: isNaN(val) ? 0 : val
                });
            });

            if (!allValid) {
                Swal.fire({ icon: 'warning', title: 'Nilai Belum Lengkap', text: 'Pastikan seluruh kolom nilai kriteria terisi (0-100).' });
                return;
            }

            const btn = document.getElementById('btnSubmitPenilaianSidang');
            if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i> Menyimpan...'; }

            const formData = new FormData();
            formData.append('nim', nim);
            formData.append('prodi', prodi);
            formData.append('peminatan', peminatan);
            formData.append('tahun_akademik', tahunAkademik);
            formData.append('nilai_akhir', calc.score.toFixed(2));
            formData.append('grade', calc.grade);
            formData.append('status_kelulusan', statusKelulusan);
            formData.append('detail_penilaian', JSON.stringify(details));
            formData.append('status_publish', statusPublish);
            formData.append('tgl_publish', String(tglPublishInp).replace('T', ' '));
            formData.append('catatan', catatan || '');

            const targetUrl = '<?= site_url('koordinatorta/ajax_simpan_penilaian_sidang') ?>';
            fetch(targetUrl, { method: 'POST', body: formData })
                .then(r => r.json())
                .then(res => {
                    if (res && res.status) {
                        closeModalPenilaianSidang();
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Penilaian sidang berhasil disimpan.' })
                            .then(() => { window.location.reload(); });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Gagal menyimpan penilaian.' });
                    }
                })
                .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan sistem.' }))
                .finally(() => {
                    if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-check-double text-xs"></i> <span>Simpan Penilaian Sidang TA</span>'; }
                });
        };
    </script>

</body>
</html>