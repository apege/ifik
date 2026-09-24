<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Reset File TA'; ?> - IFIK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{brand:{50:'#fff7ed',100:'#ffedd5',500:'#f97316',600:'#ea580c',700:'#c2410c',900:'#7c2d12'}}}}}</script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body,button,input,textarea,select{font-family:'Plus Jakarta Sans',-apple-system,sans-serif!important;}
        .unified-search-pill{display:flex;align-items:center;background:rgba(255,255,255,.95);backdrop-filter:blur(12px);border:1.5px solid #e2e8f0;border-radius:16px;padding:3px 14px;height:46px;transition:border-color .25s,box-shadow .25s;position:relative;}
        .unified-search-pill:focus-within,.unified-search-pill.active{border-color:#ea580c!important;background:#fff!important;box-shadow:0 0 0 4px rgba(234,88,12,.12),0 10px 25px -5px rgba(234,88,12,.1)!important;}
        .unified-divider{width:1px;height:22px;background:#e2e8f0;margin:0 8px;}
        .autocomplete-box{max-height:320px;overflow-y:auto;border-radius:18px;box-shadow:0 20px 45px -10px rgba(15,23,42,.2),0 8px 24px -4px rgba(234,88,12,.12);}
        .autocomplete-item-row{padding:10px 16px;transition:all .18s;cursor:pointer;}
        .autocomplete-item-row:hover,.autocomplete-item-row.active-nav{background-color:#fff7ed;color:#ea580c;}
        .autocomplete-item-row mark{background:#fed7aa;color:#c2410c;font-weight:800;border-radius:4px;padding:0 3px;}
        .btn-standalone-add{display:inline-flex;align-items:center;gap:6px;background:#fff7ed;border:1.5px solid #fed7aa;border-radius:16px;padding:6px 14px;height:46px;font-size:.8rem;font-weight:700;color:#ea580c;cursor:pointer;transition:all .2s;white-space:nowrap;}
        .btn-standalone-add:hover{background:#ffedd5;border-color:#fdba74;transform:scale(1.02);}
        .badge-standalone-count{background:#ea580c;color:#fff;font-size:.72rem;font-weight:800;padding:1.5px 8px;border-radius:99px;}
        .btn-remove-row{display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;background:#fff1f2;border:1.5px solid #fecdd3;border-radius:14px;color:#e11d48;cursor:pointer;transition:all .2s;flex-shrink:0;}
        .btn-remove-row:hover{background:#ffe4e6;border-color:#fda4af;color:#be123c;transform:scale(1.05);}
        .extra-rows-card{display:none;position:relative;margin-top:12px;background:#fafafa;border:1.5px solid #e2e8f0;border-radius:16px;padding:14px;box-shadow:inset 0 2px 4px rgba(0,0,0,.02);}
        .extra-rows-card.open{display:block!important;}
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-orange-50/20 to-slate-100 min-h-screen text-slate-800 antialiased">
<?php $this->load->view('admin_layanan/sidebar'); ?>
<?php $this->load->view('partials/app_navbar',['user_role_id'=>$this->session->userdata('role_id')??5,'user_role_label'=>'Admin Layanan (LAA)','user_display_name'=>'Unit Layanan FIK','user_display_sub'=>'Reset File TA']); ?>
<main class="min-h-screen p-4 sm:p-6 lg:p-10 max-w-7xl mx-auto">
<div class="mb-6 sm:mb-8">
    <div class="flex flex-wrap items-center gap-1.5 text-[11px] font-semibold text-slate-400 mb-2 pl-11 sm:pl-0 pt-0.5">
        <a href="<?= site_url('adminlayanan') ?>" class="hover:text-orange-600 transition-colors">Portal LAA</a>
        <i class="bi bi-chevron-right text-[10px]"></i><span class="text-slate-600">Pendaftaran TA</span>
        <i class="bi bi-chevron-right text-[10px]"></i><span class="text-red-600 font-bold">Reset File TA</span>
    </div>
    <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2.5">
        <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-gradient-to-tr from-red-600 to-rose-500 text-white flex items-center justify-center shadow-lg shadow-red-500/25 shrink-0">
            <i class="bi bi-arrow-counterclockwise text-lg sm:text-xl"></i>
        </span>
        <span>Reset File TA Mahasiswa</span>
    </h1>
    <p class="text-xs sm:text-sm text-slate-500 mt-1">Hapus file yang sudah diupload mahasiswa agar mereka dapat mengupload ulang berkas yang perlu diperbaiki.</p>
</div>
<?php if($this->session->flashdata('success')): ?><div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-semibold flex items-center gap-2"><i class="bi bi-check-circle-fill text-green-500"></i><?=$this->session->flashdata('success')?></div><?php endif; ?>
<?php if($this->session->flashdata('error')): ?><div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm font-semibold flex items-center gap-2"><i class="bi bi-x-circle-fill text-red-500"></i><?=$this->session->flashdata('error')?></div><?php endif; ?><!-- Unified Search Bar -->
<div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-sm p-3.5 sm:p-4 mb-6 relative">
<form action="<?= site_url('adminlayanan/reset_file_ta') ?>" method="GET" id="formSearchReset" onsubmit="executeResetSearch(event)" class="relative">
<input type="hidden" name="cat" id="mainCategorySelectReset" value="<?= htmlspecialchars($cat ?? 'query') ?>">
<div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
    <div class="unified-search-pill flex-1 flex items-center justify-between gap-1 min-w-0">
        <div class="relative custom-dropdown-container shrink-0" id="dropdownCatWrapperReset">
            <?php
            $catLabels=['query'=>'🔍 Kata Kunci (Semua)','nama'=>'🏷️ Nama Mahasiswa','nim'=>'🆔 NIM Mahasiswa','judul'=>'📖 Judul Tugas Akhir','prodi'=>'🎯 Program Studi & KK'];
            $curLabel=$catLabels[$cat??'query']??'🔍 Kata Kunci (Semua)';
            ?>
            <button type="button" onclick="toggleResetCatDropdown(event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-1 hover:text-orange-600 focus:outline-none">
                <span id="labelCatReset" class="truncate max-w-[120px] sm:max-w-[170px]"><?= $curLabel ?></span>
                <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 transition-transform duration-200 dropdown-arrow" id="arrowCatReset"></i>
            </button>
            <div id="menuCatReset" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-56 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 p-1.5 space-y-0.5 text-xs">
                <div onclick="selectResetMainCategory('query','🔍 Kata Kunci (Semua)')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer font-semibold <?= ($cat??'query')==='query'?'bg-orange-50 text-orange-700 font-bold':'text-slate-700 hover:bg-slate-50' ?>"><span>🔍 Kata Kunci (Semua)</span></div>
                <div onclick="selectResetMainCategory('nama','🏷️ Nama Mahasiswa')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer font-semibold <?= ($cat??'')==='nama'?'bg-orange-50 text-orange-700 font-bold':'text-slate-700 hover:bg-slate-50' ?>"><span>🏷️ Nama Mahasiswa</span></div>
                <div onclick="selectResetMainCategory('nim','🆔 NIM Mahasiswa')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer font-semibold <?= ($cat??'')==='nim'?'bg-orange-50 text-orange-700 font-bold':'text-slate-700 hover:bg-slate-50' ?>"><span>🆔 NIM Mahasiswa</span></div>
                <div onclick="selectResetMainCategory('judul','📖 Judul Tugas Akhir')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer font-semibold <?= ($cat??'')==='judul'?'bg-orange-50 text-orange-700 font-bold':'text-slate-700 hover:bg-slate-50' ?>"><span>📖 Judul Tugas Akhir</span></div>
                <div onclick="selectResetMainCategory('prodi','🎯 Program Studi & KK')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer font-semibold <?= ($cat??'')==='prodi'?'bg-orange-50 text-orange-700 font-bold':'text-slate-700 hover:bg-slate-50' ?>"><span>🎯 Program Studi & KK</span></div>
            </div>
        </div>
        <div class="unified-divider shrink-0"></div>
        <div class="flex-1 flex items-center min-w-0 px-1 relative">
            <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
            <input type="text" name="q" id="inputSearchReset" autocomplete="off" value="<?= htmlspecialchars($search ?? '') ?>"
                   placeholder="Ketik NIM / nama mahasiswa lalu tekan Enter..."
                   oninput="handleResetAutocomplete(this.value)" onkeydown="handleResetInputKey(event)"
                   class="w-full text-xs font-semibold bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400 min-w-0">
            <button type="button" id="btnClearSearchReset" onclick="clearResetSearch()" class="<?= empty($search)?'opacity-0 scale-75 pointer-events-none':'opacity-100 scale-100' ?> text-slate-400 hover:text-rose-600 text-xs px-1.5 py-1 cursor-pointer shrink-0 transition-all" title="Hapus"><i class="fa-solid fa-circle-xmark text-sm"></i></button>
        </div>
        <button type="button" onclick="executeResetSearch(event)" class="px-3 sm:px-4 py-2 bg-gradient-to-r from-red-500 to-rose-500 hover:from-red-400 hover:to-rose-400 text-white text-xs font-bold rounded-xl flex items-center gap-1.5 transition-all cursor-pointer active:scale-95 shrink-0 ml-1">
            <i class="fa-solid fa-magnifying-glass text-[11px]"></i><span class="hidden sm:inline">Cari</span>
        </button>
    </div>
    <button type="button" onclick="toggleResetMultiFilter(event)" class="btn-standalone-add shrink-0 justify-center w-full sm:w-auto" title="Tambah Kriteria Filter (Maks 4)">
        <i class="fa-solid fa-plus text-xs"></i><span id="filterCountBadgeReset" class="badge-standalone-count">1/4</span>
    </button>
</div>
<div id="autocompleteReset" class="hidden absolute left-0 right-0 top-full mt-2 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 autocomplete-box overflow-hidden">
    <div id="autocompleteResultsReset" class="divide-y divide-slate-100 text-xs"></div>
</div>
<div id="extraRowsCardReset" class="extra-rows-card space-y-2.5">
    <div id="additionalFilterRowsContainerReset" class="space-y-2.5"></div>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-t border-slate-100 pt-2.5 mt-2 text-xs">
        <span class="text-slate-400 text-[11px]">Kombinasi kriteria untuk mempersempit pencarian. Tekan Enter / Cari.</span>
        <button type="button" onclick="resetResetMultiSearch()" class="text-rose-600 hover:text-rose-700 font-bold cursor-pointer self-start sm:self-auto">Reset All Filters</button>
    </div>
</div>
</form>
</div><?php if(!empty($search) && !$detail): ?>
<div class="bg-red-50 border border-red-200 rounded-2xl p-8 text-center mb-6">
    <i class="bi bi-person-x text-red-300 text-4xl mb-2"></i>
    <p class="text-red-700 font-semibold">Mahasiswa dengan kata kunci <strong>"<?= htmlspecialchars($search) ?>"</strong> tidak ditemukan dalam data pendaftaran TA.</p>
</div>
<?php endif; ?>
<?php if($detail): ?>
<div class="bg-white/80 backdrop-blur-sm border border-slate-200/80 rounded-2xl shadow-sm p-5 sm:p-6 mb-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white flex items-center justify-center text-xl font-extrabold shadow-md shrink-0">
            <?= strtoupper(substr($detail['nama_depan']??'M',0,1)) ?>
        </div>
        <div class="flex-1 min-w-0">
            <h3 class="text-base font-bold text-slate-900"><?= htmlspecialchars(($detail['nama_depan']??'').' '.($detail['nama_belakang']??'')) ?></h3>
            <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1 text-xs text-slate-500 font-medium">
                <span><i class="bi bi-person-badge mr-1"></i><?= htmlspecialchars($detail['nim']??'') ?></span>
                <span><i class="bi bi-mortarboard mr-1"></i><?= htmlspecialchars($detail['prodi']??'-') ?></span>
                <span><i class="bi bi-diagram-3 mr-1"></i><?= htmlspecialchars($detail['kode_kk']??'-') ?></span>
            </div>
            <div class="mt-2 flex flex-wrap gap-2">
                <?php 
                $sa = $detail['status_approval_admin'] ?? 'Pending'; 
                $bc = ($sa === 'Approved') ? 'bg-green-100 text-green-700 border-green-200' : (($sa === 'Rejected') ? 'bg-red-100 text-red-700 border-red-200' : 'bg-amber-100 text-amber-700 border-amber-200'); 
                ?>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold border <?= $bc ?>">Status LAA: <?= $sa ?></span>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold border bg-slate-100 text-slate-600 border-slate-200">Tahap: <?= htmlspecialchars($detail['current_stage']??'-') ?></span>
            </div>
        </div>
        <button onclick="confirmResetAll('<?= htmlspecialchars($detail['nim']) ?>','<?= htmlspecialchars(($detail['nama_depan']??'').' '.($detail['nama_belakang']??'')) ?>')" class="shrink-0 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-bold transition flex items-center gap-2 shadow-sm">
            <i class="bi bi-arrow-counterclockwise"></i> Reset Semua File
        </button>
    </div>
</div>
<?php if(!empty($berkas)): ?>
<div class="bg-white/80 backdrop-blur-sm border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-slate-100">
        <h2 class="text-sm font-bold text-slate-700 flex items-center gap-2"><i class="bi bi-files text-orange-500"></i>File Terupload &mdash; <?= count($berkas) ?> file</h2>
    </div>
    <div class="divide-y divide-slate-100">
    <?php foreach($berkas as $kode=>$bk): ?>
    <div class="flex items-center justify-between px-5 py-3.5 hover:bg-slate-50/60 transition">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-9 h-9 rounded-xl bg-red-50 text-red-500 flex items-center justify-center shrink-0"><i class="bi bi-file-earmark-pdf-fill"></i></div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-800 truncate"><?= htmlspecialchars($bk['nama_berkas']??strtoupper($kode)) ?></p>
                <?php 
                $st = $bk['status'] ?? 'Pending'; 
                $sc = ($st === 'Valid') ? 'text-green-600' : (($st === 'Invalid') ? 'text-red-600' : 'text-amber-600'); 
                ?>
                <p class="text-[11px] text-slate-400 font-medium truncate"><?= htmlspecialchars($bk['file_name']??'-') ?> &middot; <span class="font-bold <?= $sc ?>"><?= $st ?></span></p>
            </div>
        </div>
        <button onclick="confirmResetOne('<?= htmlspecialchars($detail['nim']) ?>','<?= $kode ?>','<?= htmlspecialchars($bk['nama_berkas']??strtoupper($kode)) ?>')" class="shrink-0 ml-3 px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
            <i class="bi bi-trash3"></i> Reset
        </button>
    </div>
    <?php endforeach; ?>
    </div>
</div>
<?php else: ?>
<div class="bg-slate-50 border border-slate-200 rounded-2xl p-8 text-center"><i class="bi bi-folder-x text-slate-300 text-4xl mb-2"></i><p class="text-slate-500 font-semibold text-sm">Mahasiswa ini belum mengupload file apapun.</p></div>
<?php endif; ?>
<?php endif; ?>
</main>
<!-- Confirm Modal -->
<div id="confirmModal" class="fixed inset-0 z-[99999] flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm mx-4 z-10">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0"><i class="bi bi-exclamation-triangle-fill text-lg"></i></div>
            <div><h3 class="font-extrabold text-slate-900 text-base" id="modalTitle">Konfirmasi Reset</h3><p class="text-xs text-slate-500">Tindakan ini tidak dapat dibatalkan</p></div>
        </div>
        <p class="text-sm text-slate-600 mb-5" id="modalBody">Apakah kamu yakin?</p>
        <div class="flex gap-2">
            <button onclick="closeModal()" class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold transition">Batal</button>
            <button id="modalConfirmBtn" class="flex-1 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-bold transition flex items-center justify-center gap-2"><i class="bi bi-arrow-counterclockwise"></i> Ya, Reset</button>
        </div>
    </div>
</div><script>
window.resetMhsData=<?= json_encode(array_map(function($r){return['nim'=>$r['nim']??'','nama'=>trim(($r['nama_depan']??'').' '.($r['nama_belakang']??'')),'judul'=>$r['judul_1']??'','prodi'=>($r['prodi']??'').' '.($r['konsentrasi_dkv']??'')];}, $allPengajuan??[])); ?>;
const AJAX_RESET_URL='<?= site_url('adminlayanan/ajax_reset_file_ta') ?>';
const BASE_RESET_URL='<?= site_url('adminlayanan/reset_file_ta') ?>';
let extraRowCounterReset=0,_pendingNim='',_pendingKode='';

function toggleResetCatDropdown(e){e.stopPropagation();const m=document.getElementById('menuCatReset'),a=document.getElementById('arrowCatReset');m.classList.toggle('hidden');a.style.transform=m.classList.contains('hidden')?'rotate(0deg)':'rotate(180deg)';}
function selectResetMainCategory(val,label){document.getElementById('mainCategorySelectReset').value=val;document.getElementById('labelCatReset').textContent=label;document.getElementById('menuCatReset').classList.add('hidden');document.getElementById('arrowCatReset').style.transform='rotate(0deg)';document.getElementById('inputSearchReset').focus();}

function toggleResetMultiFilter(e){if(e){e.stopPropagation();e.preventDefault();}const ec=document.getElementById('extraRowsCardReset'),cr=document.querySelectorAll('.extra-filter-row-reset').length;if(cr>=3){if(ec)ec.classList.toggle('open');return;}addResetFilterRow(e);}

function addResetFilterRow(e){if(e)e.stopPropagation();const con=document.getElementById('additionalFilterRowsContainerReset'),ec=document.getElementById('extraRowsCardReset'),cr=document.querySelectorAll('.extra-filter-row-reset').length;if(cr>=3)return;extraRowCounterReset++;const rid='extra-reset-'+extraRowCounterReset;const cats=[{key:'nama',label:'🏷️ Nama Mahasiswa',ph:'Ketik nama mahasiswa...'},{key:'nim',label:'🆔 NIM Mahasiswa',ph:'Ketik NIM...'},{key:'judul',label:'📖 Judul Tugas Akhir',ph:'Ketik judul TA...'},{key:'prodi',label:'🎯 Program Studi & KK',ph:'Ketik prodi...'}];const sc=cats[cr%cats.length];const rd=document.createElement('div');rd.className='extra-filter-row-reset flex items-center gap-2 relative';rd.id=rid;rd.innerHTML=`<div class="unified-search-pill flex-1 flex items-center justify-between gap-1"><div class="relative custom-dropdown-container shrink-0"><button type="button" onclick="toggleExtraDropdownReset('${rid}',event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-1 hover:text-orange-600 focus:outline-none"><span id="label-${rid}" class="truncate max-w-[120px]">${sc.label}</span><i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow" id="arrow-${rid}"></i></button><div id="menu-${rid}" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-52 bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 text-xs"><div onclick="selExCatR('${rid}','nama','🏷️ Nama Mahasiswa','Ketik nama...')" class="px-3 py-2 rounded-lg cursor-pointer font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-700"><span>🏷️ Nama Mahasiswa</span></div><div onclick="selExCatR('${rid}','nim','🆔 NIM Mahasiswa','Ketik NIM...')" class="px-3 py-2 rounded-lg cursor-pointer font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-700"><span>🆔 NIM Mahasiswa</span></div><div onclick="selExCatR('${rid}','judul','📖 Judul TA','Ketik judul...')" class="px-3 py-2 rounded-lg cursor-pointer font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-700"><span>📖 Judul Tugas Akhir</span></div><div onclick="selExCatR('${rid}','prodi','🎯 Prodi & KK','Ketik prodi...')" class="px-3 py-2 rounded-lg cursor-pointer font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-700"><span>🎯 Program Studi & KK</span></div></div></div><div class="unified-divider"></div><div class="flex-1 flex items-center min-w-0 px-1"><i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i><input type="text" data-cat="${sc.key}" placeholder="${sc.ph}" onkeydown="handleResetInputKey(event)" class="extra-row-input-reset w-full text-xs font-semibold bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400"></div></div><button type="button" onclick="removeResetFilterRow(this)" class="btn-remove-row shrink-0" title="Hapus"><i class="fa-solid fa-trash-can text-sm"></i></button>`;con.appendChild(rd);if(ec)ec.classList.add('open');updateResetFilterBadge();const ni=rd.querySelector('input');if(ni)ni.focus();}

function removeResetFilterRow(btn){const r=btn.closest('.extra-filter-row-reset');if(r)r.remove();const ec=document.getElementById('extraRowsCardReset');if(document.querySelectorAll('.extra-filter-row-reset').length===0&&ec)ec.classList.remove('open');updateResetFilterBadge();}
function toggleExtraDropdownReset(rid,e){if(e)e.stopPropagation();const m=document.getElementById('menu-'+rid),a=document.getElementById('arrow-'+rid);document.querySelectorAll('.custom-dropdown-menu').forEach(x=>{if(x!==m)x.classList.add('hidden');});document.querySelectorAll('.dropdown-arrow').forEach(x=>{if(x!==a)x.style.transform='rotate(0deg)';});if(m){m.classList.toggle('hidden');if(a)a.style.transform=m.classList.contains('hidden')?'rotate(0deg)':'rotate(180deg)';}}
function selExCatR(rid,key,label,ph){const l=document.getElementById('label-'+rid),i=document.querySelector('#'+rid+' input.extra-row-input-reset');if(l)l.textContent=label;if(i){i.setAttribute('data-cat',key);i.placeholder=ph;i.focus();}document.querySelectorAll('.custom-dropdown-menu').forEach(m=>m.classList.add('hidden'));document.querySelectorAll('.dropdown-arrow').forEach(a=>a.style.transform='rotate(0deg)');}
function updateResetFilterBadge(){const t=document.querySelectorAll('.extra-filter-row-reset').length+1;const b=document.getElementById('filterCountBadgeReset');if(b)b.innerText=`${t}/4`;}
function resetResetMultiSearch(){document.getElementById('additionalFilterRowsContainerReset').innerHTML='';document.getElementById('extraRowsCardReset').classList.remove('open');document.getElementById('inputSearchReset').value='';const b=document.getElementById('btnClearSearchReset');if(b){b.classList.remove('opacity-100','scale-100');b.classList.add('opacity-0','scale-75','pointer-events-none');}updateResetFilterBadge();}
function clearResetSearch(){document.getElementById('inputSearchReset').value='';document.getElementById('autocompleteReset').classList.add('hidden');const b=document.getElementById('btnClearSearchReset');if(b){b.classList.remove('opacity-100','scale-100');b.classList.add('opacity-0','scale-75','pointer-events-none');}}

function executeResetSearch(e){if(e&&e.preventDefault)e.preventDefault();let q=document.getElementById('inputSearchReset').value.trim();let cat=document.getElementById('mainCategorySelectReset').value;if(!q){document.querySelectorAll('.extra-row-input-reset').forEach(i=>{if(!q&&i.value.trim()){q=i.value.trim();cat=i.getAttribute('data-cat')||cat;}});}if(!q)return;window.location.href=BASE_RESET_URL+'?q='+encodeURIComponent(q)+'&cat='+encodeURIComponent(cat);}
function handleResetInputKey(e){if(e.key==='Enter'){e.preventDefault();executeResetSearch(e);}if(e.key==='Escape'){document.getElementById('autocompleteReset').classList.add('hidden');document.querySelectorAll('.custom-dropdown-menu').forEach(m=>m.classList.add('hidden'));}}

let acDebounceR=null;
function handleResetAutocomplete(val){const q=val.trim().toLowerCase();const bc=document.getElementById('btnClearSearchReset');if(bc){if(q.length>0){bc.classList.remove('opacity-0','scale-75','pointer-events-none');bc.classList.add('opacity-100','scale-100');}else{bc.classList.remove('opacity-100','scale-100');bc.classList.add('opacity-0','scale-75','pointer-events-none');}}clearTimeout(acDebounceR);if(q.length<1||!window.resetMhsData||!window.resetMhsData.length){document.getElementById('autocompleteReset').classList.add('hidden');return;}acDebounceR=setTimeout(()=>{const cat=document.getElementById('mainCategorySelectReset').value;const matches=window.resetMhsData.filter(i=>{if(cat==='nim')return i.nim.toLowerCase().includes(q);if(cat==='nama')return i.nama.toLowerCase().includes(q);if(cat==='judul')return(i.judul||'').toLowerCase().includes(q);if(cat==='prodi')return(i.prodi||'').toLowerCase().includes(q);return i.nim.toLowerCase().includes(q)||i.nama.toLowerCase().includes(q)||(i.judul||'').toLowerCase().includes(q);}).slice(0,8);const con=document.getElementById('autocompleteResultsReset'),ab=document.getElementById('autocompleteReset');if(!matches.length){ab.classList.add('hidden');return;}const h=s=>s?(s+'').replace(new RegExp('('+q.replace(/[.*+?^${}()|[\]\\]/g,'\\$&')+')','gi'),'<mark>$1</mark>'):'';con.innerHTML=matches.map(i=>`<div class="autocomplete-item-row" onclick="selectResetAutocomplete('${i.nim}')"><div class="font-bold text-slate-800">${h(i.nama)} <span class="font-mono text-orange-600 text-[11px] ml-1">${h(i.nim)}</span></div><div class="text-slate-400 truncate mt-0.5">${h(i.prodi)}${i.judul?' · '+h(i.judul.substring(0,60))+'...':''}</div></div>`).join('');ab.classList.remove('hidden');},180);}

function selectResetAutocomplete(nim){document.getElementById('autocompleteReset').classList.add('hidden');window.location.href=BASE_RESET_URL+'?q='+encodeURIComponent(nim)+'&cat=nim';}

document.addEventListener('click',function(e){if(!e.target.closest('.custom-dropdown-container')&&!e.target.closest('#dropdownCatWrapperReset')){document.querySelectorAll('.custom-dropdown-menu').forEach(m=>m.classList.add('hidden'));document.querySelectorAll('.dropdown-arrow').forEach(a=>a.style.transform='rotate(0deg)');}const ab=document.getElementById('autocompleteReset');if(ab&&!e.target.closest('#inputSearchReset')&&!e.target.closest('#autocompleteReset'))ab.classList.add('hidden');});

function confirmResetAll(nim,nama){_pendingNim=nim;_pendingKode='';document.getElementById('modalTitle').textContent='Reset Semua File TA';document.getElementById('modalBody').innerHTML='Semua file TA milik <strong>'+nama+'</strong> (NIM: '+nim+') akan dihapus. Mahasiswa harus upload ulang dari awal.';document.getElementById('confirmModal').classList.remove('hidden');}
function confirmResetOne(nim,kode,nb){_pendingNim=nim;_pendingKode=kode;document.getElementById('modalTitle').textContent='Reset File: '+nb;document.getElementById('modalBody').innerHTML='File <strong>'+nb+'</strong> milik NIM <strong>'+nim+'</strong> akan dihapus. Mahasiswa bisa upload ulang file ini saja.';document.getElementById('confirmModal').classList.remove('hidden');}
function closeModal(){document.getElementById('confirmModal').classList.add('hidden');_pendingNim='';_pendingKode='';}

document.getElementById('modalConfirmBtn').addEventListener('click',function(){if(!_pendingNim)return;const btn=this;btn.disabled=true;btn.innerHTML='<i class="bi bi-hourglass-split"></i> Memproses...';fetch(AJAX_RESET_URL,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'nim='+encodeURIComponent(_pendingNim)+'&kode_berkas='+encodeURIComponent(_pendingKode)}).then(r=>r.json()).then(data=>{closeModal();if(data.success){window.location.href=BASE_RESET_URL+'?q='+encodeURIComponent(_pendingNim)+'&cat=nim&msg=success';}else{alert('Gagal: '+(data.message||'Terjadi kesalahan.'));}}).catch(()=>{closeModal();alert('Terjadi kesalahan jaringan.');}).finally(()=>{btn.disabled=false;btn.innerHTML='<i class="bi bi-arrow-counterclockwise"></i> Ya, Reset';});});

const up=new URLSearchParams(window.location.search);if(up.get('msg')==='success'){const b=document.createElement('div');b.className='fixed top-4 right-4 z-[999999] bg-green-500 text-white px-5 py-3 rounded-xl shadow-lg text-sm font-bold flex items-center gap-2';b.innerHTML='<i class="bi bi-check-circle-fill"></i> File berhasil direset!';document.body.appendChild(b);setTimeout(()=>b.remove(),3500);}
</script>
</body>
</html>