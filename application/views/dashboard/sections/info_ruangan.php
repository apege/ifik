<!-- Sesi 2: Informasi Ruangan -->
<div class="section-wrapper" id="section-about">
    <div class="about-container" id="ruanganCard">
        
        <!-- Tombol Buka Kalender Penuh (Ke Page Terpisah) -->
        <a class="btn-fullscreen" href="<?= base_url('kalender') ?>" title="Buka Kalender Penuh" style="display: flex; align-items: center; justify-content: center; text-decoration: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path>
            </svg>
        </a>

        <h1>INFORMASI RUANGAN</h1>
        
        <!-- Table Column Header -->
        <div class="room-column-header">
            <div class="rth-col rth-room">Ruangan</div>
            <div class="rth-col rth-user-time">Peminjam & Waktu</div>
            <div class="rth-col rth-date">Tanggal</div>
            <div class="rth-col rth-desc">Keterangan / Keperluan</div>
            <div class="rth-col rth-status">Status</div>
        </div>

        <div class="room-list-wrapper" id="roomListWrapper">
            
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
                $today_date = date('Y-m-d');
                $active_jadwal = array_filter($jadwal_peminjaman ?: [], function($j) use ($today_date) {
                    return ($j->tanggal_selesai >= $today_date);
                });
            ?>
            <?php if(empty($active_jadwal)): ?>
                <!-- Empty State -->
                <div class="empty-state" style="text-align: center; padding: 40px 20px; color: rgba(30, 41, 59, 0.5);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 15px; opacity: 0.5;">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="9" y1="3" x2="9" y2="21"></line>
                    </svg>
                    <h3 style="font-size: 1.1rem; margin: 0 0 5px 0; color: rgba(30, 41, 59, 0.7);">Belum ada jadwal hari ini & mendatang</h3>
                    <p style="font-size: 0.9rem; margin: 0;">Jadwal peminjaman ruangan mendatang akan tampil di sini.</p>
                </div>
            <?php else: ?>
                <?php 
                    $limited_jadwal = array_slice(array_values($active_jadwal), 0, 4); 
                ?>
                <?php foreach($limited_jadwal as $j): ?>
                <?php 
                    $dateStr = format_indo_date_php($j->tanggal_mulai, $j->tanggal_selesai);
                    $timeStr = substr($j->jam_mulai, 0, 5) . ' - ' . substr($j->jam_selesai, 0, 5);
                    $ketStr  = $j->keterangan ?: '-';
                ?>
                <div class="room-item" onclick="openDetailBookingModal(<?= $j->id ?>)" style="cursor: pointer;">
                    <div class="room-item-left">
                        <div class="room-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        </div>
                        <div class="room-info" title="<?= htmlspecialchars($j->nama_ruangan . ' (' . $j->kode_ruangan . ')') ?>">
                            <h3 data-text="<?= htmlspecialchars($j->kode_ruangan) ?>"><?= $j->kode_ruangan ?></h3>
                            <p data-text="<?= htmlspecialchars($j->nama_ruangan) ?>"><?= $j->nama_ruangan ?></p>
                        </div>

                        <!-- Floating Room Detail Tooltip on Hover (Direct child of room-item-left) -->
                        <div class="room-hover-tooltip">
                            <div class="rht-header">
                                <span class="rht-code"><?= $j->kode_ruangan ?></span>
                                <span class="rht-cat"><?= $j->nama_kategori ?: 'Ruangan' ?></span>
                            </div>
                            <div class="rht-title"><?= $j->nama_ruangan ?></div>
                            <?php if (!empty($j->lokasi) || !empty($j->kapasitas)): ?>
                            <div class="rht-meta">
                                <?php if (!empty($j->lokasi)): ?>
                                    <span>📍 <?= $j->lokasi ?></span>
                                <?php endif; ?>
                                <?php if (!empty($j->kapasitas)): ?>
                                    <span>👥 <?= $j->kapasitas ?> Orang</span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="room-item-tags">
                        <span class="tag" title="<?= htmlspecialchars($j->nama_lengkap) ?>">
                            <svg style="margin-right:4px; vertical-align:text-bottom; flex-shrink: 0;" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            <span class="tag-user-name" data-text="<?= htmlspecialchars($j->nama_lengkap) ?>"><?= htmlspecialchars($j->nama_lengkap) ?></span>
                        </span>
                        <span class="tag" style="border-color: #fb923c; color: #ea580c; background: #ffffff;">
                            <span class="tag-time-text" data-text="<?= $timeStr ?>"><?= $timeStr ?></span>
                        </span>
                    </div>

                    <div class="room-item-date">
                        <span class="room-date-text" data-text="<?= $dateStr ?>"><?= $dateStr ?></span>
                    </div>

                    <div class="room-item-desc" title="<?= htmlspecialchars($ketStr) ?>">
                        <span class="room-desc-text" data-text="<?= htmlspecialchars($ketStr) ?>"><?= htmlspecialchars($ketStr) ?></span>
                        
                        <!-- Floating Keterangan Detail Tooltip on Hover -->
                        <div class="desc-hover-tooltip">
                            <span class="dht-badge">📝 Keterangan / Keperluan</span>
                            <div class="dht-content"><?= htmlspecialchars($ketStr) ?></div>
                        </div>
                    </div>

                    <div class="room-item-action">
                        <?php
                            $s = $j->status;
                            $dot   = '';
                            $bg    = '';
                            $color = '';
                            $label = '';

                            if ($s === 'Pending') {
                                $dot = '#f59e0b'; $bg = '#fffbeb'; $color = '#b45309'; $label = 'Menunggu';
                            } elseif (strpos($s, 'Ka. Ur') !== false) {
                                $dot = '#22c55e'; $bg = '#f0fdf4'; $color = '#166534'; $label = 'Disetujui Ka. Ur';
                            } elseif (strpos($s, 'Laboran') !== false) {
                                $dot = '#3b82f6'; $bg = '#eff6ff'; $color = '#1d4ed8'; $label = 'Disetujui Laboran';
                            } elseif (strpos($s, 'Admin') !== false) {
                                $dot = '#8b5cf6'; $bg = '#f5f3ff'; $color = '#6d28d9'; $label = 'Disetujui Admin';
                            } elseif (strpos($s, 'Disetujui') !== false) {
                                $dot = '#22c55e'; $bg = '#f0fdf4'; $color = '#166534'; $label = 'Disetujui';
                            } elseif ($s === 'Selesai') {
                                $dot = '#94a3b8'; $bg = '#f8fafc'; $color = '#475569'; $label = 'Selesai';
                            } else {
                                $dot = '#94a3b8'; $bg = '#f8fafc'; $color = '#475569'; $label = htmlspecialchars($s);
                            }
                        ?>
                        <span class="landing-status-badge" style="background:<?= $bg ?>; color:<?= $color ?>;">
                            <span style="width:7px;height:7px;border-radius:50%;background:<?= $dot ?>;flex-shrink:0;"></span>
                            <span class="landing-status-label" data-text="<?= $label ?>"><?= $label ?></span>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>



        </div>

        <!-- Kalender Fullscreen (Hidden by default) -->
        <div class="gcal-wrapper" id="gcalWrapper" style="display: none;">
            <div class="gcal-header">
                <div class="gcal-header-left">
                    <button class="gcal-btn-today" onclick="goToToday()">Today</button>
                    <div class="gcal-nav-arrows">
                        <button onclick="prevWeek()"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg></button>
                        <button onclick="nextWeek()"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg></button>
                    </div>
                    <h2 class="gcal-month-title" id="gcalMonthTitle">August 2026</h2>
                </div>
                <div class="gcal-header-right">
                    <?php if ($this->session->userdata('logged_in')): ?>
                        <a href="<?= base_url('ajukan-booking') ?>" class="gcal-btn-booking" style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">+ Ajukan Booking</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="gcal-body">
                <div class="gcal-days-header" id="gcalDaysHeader">
                    <!-- Digenerate via JS -->
                </div>
                <div class="gcal-grid-scroll">
                    <div class="gcal-grid" id="gcalGrid">
                        <!-- Digenerate via JS -->
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


<!-- Modal Detail & Approval Peminjaman -->
<div class="modal-overlay" id="detailBookingModal">
    <div class="modal-content" style="max-width: 520px; border-radius: 16px;">
        <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; padding: 18px 24px;">
            <h2 style="font-size: 1.15rem; font-weight: 700; color: #1e293b; margin: 0;">Detail Peminjaman Ruangan</h2>
            <button class="modal-close" type="button" onclick="closeDetailBookingModal()">&times;</button>
        </div>
        <div class="modal-body" style="padding: 20px 24px; overflow-y: auto;">
            <input type="hidden" id="detailBookingId">
            
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; gap: 10px;">
                    <div>
                        <span id="detailKodeRuangan" style="display: inline-block; background: #ede9fe; color: #7c3aed; font-size: 0.75rem; font-weight: 700; padding: 3px 10px; border-radius: 20px; margin-bottom: 4px;"></span>
                        <h3 id="detailNamaRuangan" style="margin: 0; font-size: 1.1rem; font-weight: 700; color: #0f172a;"></h3>
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
                    <div id="detailAlasanContainer" style="display: none; background: #fef2f2; border-left: 3px solid #ef4444; padding: 8px 12px; border-radius: 4px; margin-top: 4px;">
                        <strong style="color: #991b1b;">Alasan Penolakan:</strong> <span id="detailAlasanPenolakan" style="color: #7f1d1d;"></span>
                    </div>
                </div>
            </div>

            <!-- Panel Aksi Approval (Hanya jika status Pending & untuk Admin / Laboran / Ka. Ur) -->
            <div id="approvalActionPanel" style="display: none; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 16px; margin-bottom: 12px;">
                <h4 style="margin: 0 0 10px 0; font-size: 0.88rem; font-weight: 700; color: #166534; display: flex; align-items: center; gap: 6px;">
                    ⚡ Persetujuan Peminjaman (<span id="approvalRoleLabel"></span>)
                </h4>
                <div style="display: flex; gap: 10px;">
                    <button type="button" onclick="approveBookingAction()" style="flex: 1; background: #16a34a; color: #fff; border: none; padding: 10px 14px; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: background 0.2s;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        Setujui
                    </button>
                    <button type="button" onclick="toggleRejectInput()" style="flex: 1; background: #dc2626; color: #fff; border: none; padding: 10px 14px; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: background 0.2s;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        Tolak
                    </button>
                </div>

                <!-- Input Alasan Penolakan -->
                <div id="rejectReasonBox" style="display: none; margin-top: 12px; border-top: 1px dashed #cbd5e1; padding-top: 12px;">
                    <label style="font-size: 0.8rem; font-weight: 600; color: #991b1b; display: block; margin-bottom: 6px;">Alasan Penolakan (Opsional):</label>
                    <textarea id="rejectReasonInput" rows="2" class="form-control" placeholder="Tuliskan alasan penolakan..." style="font-size: 0.85rem; margin-bottom: 8px; border-color: #fca5a5; width: 100%; box-sizing: border-box;"></textarea>
                    <button type="button" onclick="rejectBookingAction()" style="width: 100%; background: #991b1b; color: #fff; border: none; padding: 8px; border-radius: 6px; font-weight: 700; font-size: 0.8rem; cursor: pointer;">Konfirmasi Penolakan</button>
                </div>
            </div>

            <!-- Panel Aksi Hapus Jadwal (Khusus Role 1: Admin, 2: Laboran, 3: Ka. Ur) -->
            <div id="deleteActionPanel" style="display: none; margin-top: 8px;">
                <button type="button" onclick="deleteBookingAction()" style="width: 100%; background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5; padding: 9px 14px; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    Hapus Jadwal Peminjaman
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Passing data dari PHP ke JS (prevent redeclaration error)
    window.bookingData = <?= json_encode($jadwal_peminjaman ? $jadwal_peminjaman : []) ?: '[]' ?>;
    window.isLoggedIn = <?= $this->session->userdata('logged_in') ? 'true' : 'false' ?>;
    window.ajukanBookingUrl = '<?= base_url('ajukan-booking') ?>';
    window.approveBookingUrl = '<?= base_url('dashboard/approve_booking') ?>';
    window.rejectBookingUrl = '<?= base_url('dashboard/reject_booking') ?>';
    window.deleteBookingUrl = '<?= base_url('dashboard/delete_booking') ?>';
    window.getUpdatedBookingsUrl = '<?= base_url('dashboard/get_updated_bookings') ?>';
    window.userRoleId = <?= json_encode($this->session->userdata('role_id')) ?>;
</script>

