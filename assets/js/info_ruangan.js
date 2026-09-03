    let currentWeekStart = new Date(); // Hari ini sebagai acuan minggu
    currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay()); // Set ke hari Minggu

    // ==========================================
    // FUNGSI TOGGLE FULLSCREEN
    // ==========================================
    function toggleFullscreen() {
        const card = document.getElementById('ruanganCard');
        const icon = document.getElementById('fsIcon');
        const listWrapper = document.querySelector('.room-list-wrapper');
        const gcalWrapper = document.getElementById('gcalWrapper');
        
        const isCurrentlyFullscreen = card.classList.contains('is-fullscreen');
        
        if (!isCurrentlyFullscreen) {
            // Masuk Mode Fullscreen (Pindahkan langsung ke document.body agar tidak terjebak transform Lenis/celah)
            let placeholder = document.getElementById('ruanganCardPlaceholder');
            if (!placeholder) {
                placeholder = document.createElement('div');
                placeholder.id = 'ruanganCardPlaceholder';
                placeholder.style.display = 'none';
                card.parentNode.insertBefore(placeholder, card);
            }
            document.body.appendChild(card);
            card.classList.add('is-fullscreen');

            icon.innerHTML = '<path d="M8 3v3a2 2 0 0 1-2 2H3m18 0h-3a2 2 0 0 1-2-2V3m0 18v-3a2 2 0 0 1 2-2h3M3 16h3a2 2 0 0 1 2 2v3"></path>';
            listWrapper.style.display = 'none';
            gcalWrapper.style.display = 'flex';
            
            // Hentikan Lenis scroll agar layar di belakang tidak bergerak
            if (window.lenis) window.lenis.stop();
            document.body.style.overflow = 'hidden';
            
            // Inisialisasi Kalender saat dibuka
            renderCalendar();
        } else {
            // Keluar Mode Fullscreen (Kembalikan ke posisi semula di Section 2)
            const placeholder = document.getElementById('ruanganCardPlaceholder');
            if (placeholder && placeholder.parentNode) {
                placeholder.parentNode.insertBefore(card, placeholder);
            } else {
                const sec = document.getElementById('section-about');
                if (sec) sec.appendChild(card);
            }
            card.classList.remove('is-fullscreen');

            icon.innerHTML = '<path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path>';
            listWrapper.style.display = 'flex';
            gcalWrapper.style.display = 'none';

            // Nyalakan kembali Lenis scroll
            if (window.lenis) window.lenis.start();
            document.body.style.overflow = '';
        }
    }

    // Isolasi event wheel di dalam grid kalender agar scroll lancar tanpa dicegat Lenis
    document.addEventListener('wheel', function(e) {
        if (e.target && e.target.closest && e.target.closest('.gcal-grid-scroll')) {
            e.stopPropagation();
        }
    }, { passive: true });



    // ==========================================
    // LOGIKA RENDER GOOGLE CALENDAR GRID
    // ==========================================

    function renderCalendar() {
        renderHeaderAndDays();
        renderTimeGridAndEvents();
    }

    function renderHeaderAndDays() {
        const daysHeaderContainer = document.getElementById('gcalDaysHeader');
        const monthTitle = document.getElementById('gcalMonthTitle');
        const days = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
        
        let headerHTML = '';
        let monthName = currentWeekStart.toLocaleString('en-US', { month: 'long' });
        let year = currentWeekStart.getFullYear();
        monthTitle.innerText = `${monthName} ${year}`;

        const today = new Date();

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
        
        // Render Time Column (Y-Axis) — jam 07:00 s/d 22:00
        let timeColHTML = `<div class="gcal-time-col">`;
        for (let i = 7; i <= 22; i++) {
            const hourStr = i.toString().padStart(2, '0') + ':00';
            timeColHTML += `<div class="gcal-time-label"><span>${hourStr}</span></div>`;
        }
        timeColHTML += `</div>`;

        // Render Day Columns
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

    // Mapping status ke warna dan label singkat
    function getStatusStyle(status) {
        const s = (status || '').toLowerCase();
        if (s === 'pending') {
            return {
                bg: '#f59e0b', border: '#d97706',
                badgeBg: '#fffbeb', badgeColor: '#b45309', dot: '#f59e0b',
                label: 'Menunggu Persetujuan'
            };
        } else if (s.includes('ka. ur')) {
            return {
                bg: '#10b981', border: '#059669',
                badgeBg: '#f0fdf4', badgeColor: '#166534', dot: '#22c55e',
                label: 'Disetujui Ka. Ur'
            };
        } else if (s.includes('laboran')) {
            return {
                bg: '#3b82f6', border: '#2563eb',
                badgeBg: '#eff6ff', badgeColor: '#1d4ed8', dot: '#3b82f6',
                label: 'Disetujui Laboran'
            };
        } else if (s.includes('admin')) {
            return {
                bg: '#8b5cf6', border: '#7c3aed',
                badgeBg: '#f5f3ff', badgeColor: '#6d28d9', dot: '#8b5cf6',
                label: 'Disetujui Admin'
            };
        } else if (s.includes('disetujui')) {
            return {
                bg: '#10b981', border: '#059669',
                badgeBg: '#f0fdf4', badgeColor: '#166534', dot: '#22c55e',
                label: 'Disetujui'
            };
        } else if (s === 'ditolak') {
            return {
                bg: '#ef4444', border: '#dc2626',
                badgeBg: '#fef2f2', badgeColor: '#991b1b', dot: '#ef4444',
                label: 'Ditolak'
            };
        } else if (s === 'selesai') {
            return {
                bg: '#64748b', border: '#475569',
                badgeBg: '#f8fafc', badgeColor: '#475569', dot: '#94a3b8',
                label: 'Selesai'
            };
        }
        return {
            bg: '#7c3aed', border: '#6d28d9',
            badgeBg: '#f5f3ff', badgeColor: '#6d28d9', dot: '#7c3aed',
            label: status
        };
    }


    function generateEventsForDay(targetDateStr) {
        if (typeof bookingData === 'undefined') return '';

        let eventsHTML = '';
        const pxPerHour = 48;

        bookingData.forEach(booking => {
            if (booking.tanggal_mulai <= targetDateStr && booking.tanggal_selesai >= targetDateStr) {
                
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

                const gridStartHour = 7; // Grid dimulai dari jam 07:00
                // Label CSS pakai bottom:-6px (Google Calendar style), sehingga label "07:00"
                // muncul di batas BAWAH row 07:00 = batas ATAS row 08:00.
                // Perlu tambah 1 row (pxPerHour) agar event 08:00 align dengan label 08:00.
                const topPx    = ((startHour - gridStartHour + 1) + startMin / 60) * pxPerHour;
                const endPx    = ((endHour   - gridStartHour + 1) + endMin   / 60) * pxPerHour;
                const heightPx = Math.max(endPx - topPx, 24);

                const st = getStatusStyle(booking.status);
                const timeLabel = `${startHour}:${startMin.toString().padStart(2,'0')} - ${endHour}:${endMin.toString().padStart(2,'0')}`;

                eventsHTML += `
                    <div class="gcal-event" onclick="openDetailBookingModal(${booking.id})" style="top:${topPx}px; height:${heightPx}px; background:${st.bg}; border-left:3px solid ${st.border}; cursor:pointer;"
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

    // ==========================================
    // NAVIGASI MINGGU
    // ==========================================
    function nextWeek() {
        currentWeekStart.setDate(currentWeekStart.getDate() + 7);
        renderCalendar();
    }

    function prevWeek() {
        currentWeekStart.setDate(currentWeekStart.getDate() - 7);
        renderCalendar();
    }

    function goToToday() {
        currentWeekStart = new Date();
        currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay());
        renderCalendar();
    }

    // ==========================================
    // LOGIKA MODAL DETAIL & APPROVAL PEMINJAMAN
    // ==========================================
    function openDetailBookingModal(id) {

        if (typeof bookingData === 'undefined') return;
        const booking = bookingData.find(b => parseInt(b.id) === parseInt(id));
        if (!booking) return;

        document.getElementById('detailBookingId').value = booking.id;
        document.getElementById('detailKodeRuangan').innerText = booking.kode_ruangan || '';
        document.getElementById('detailNamaRuangan').innerText = booking.nama_ruangan || '';
        document.getElementById('detailNamaLengkap').innerText = booking.nama_lengkap || '-';

        // Tanggal
        let tglStr = booking.tanggal_mulai;
        if (booking.tanggal_selesai && booking.tanggal_selesai !== booking.tanggal_mulai) {
            tglStr += ' s/d ' + booking.tanggal_selesai;
        }
        document.getElementById('detailTanggal').innerText = tglStr;

        // Waktu
        const jMulai = booking.jam_mulai ? booking.jam_mulai.substring(0, 5) : '00:00';
        const jSelesai = booking.jam_selesai ? booking.jam_selesai.substring(0, 5) : '00:00';
        document.getElementById('detailWaktu').innerText = jMulai + ' - ' + jSelesai;

        // Keterangan
        document.getElementById('detailKeterangan').innerText = booking.keterangan || '-';

        // Status Badge
        const st = getStatusStyle(booking.status);
        document.getElementById('detailStatusBadge').innerHTML = `
            <span style="display:inline-flex; align-items:center; gap:6px; background:${st.badgeBg}; color:${st.badgeColor}; border-radius:999px; padding:5px 13px; font-size:0.76rem; font-weight:700; white-space:nowrap;">
                <span style="width:7px;height:7px;border-radius:50%;background:${st.dot};flex-shrink:0;"></span>
                ${st.label}
            </span>
        `;


        // Alasan penolakan jika ada
        const alasBox = document.getElementById('detailAlasanContainer');
        if (booking.status === 'Ditolak' && booking.alasan_penolakan) {
            document.getElementById('detailAlasanPenolakan').innerText = booking.alasan_penolakan;
            alasBox.style.display = 'block';
        } else {
            alasBox.style.display = 'none';
        }

        // Role-based Approval & Delete Action Panels
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

        // Tampilkan tombol Hapus Jadwal untuk pengguna berwenang (Role 1, 2, 3)
        if (isAuthorized && deletePanel) {
            deletePanel.style.display = 'block';
        } else if (deletePanel) {
            deletePanel.style.display = 'none';
        }

        const modal = document.getElementById('detailBookingModal');
        if (modal && modal.parentNode !== document.body) {
            document.body.appendChild(modal);
        }
        if (modal) modal.classList.add('show');
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
                const url = (window.approveBookingUrl || window.location.origin + '/dashboard/approve_booking') + '/' + id;

                fetch(url, { method: 'POST' })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            title: 'Disetujui!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#16a34a'
                        });
                        closeDetailBookingModal();
                        reloadBookingData();
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.message,
                            icon: 'error',
                            confirmButtonColor: '#dc2626'
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error', 'Terjadi kesalahan pada server', 'error');
                });
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

        const url = (window.rejectBookingUrl || window.location.origin + '/dashboard/reject_booking') + '/' + id;

        fetch(url, { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    title: 'Ditolak',
                    text: data.message,
                    icon: 'success',
                    confirmButtonColor: '#dc2626'
                });
                closeDetailBookingModal();
                reloadBookingData();
            } else {
                Swal.fire({
                    title: 'Gagal',
                    text: data.message,
                    icon: 'error',
                    confirmButtonColor: '#dc2626'
                });
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'Terjadi kesalahan pada server', 'error');
        });
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
                const url = (window.deleteBookingUrl || window.location.origin + '/dashboard/delete_booking') + '/' + id;

                fetch(url, { method: 'POST' })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            title: 'Terhapus!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#7c3aed'
                        });
                        closeDetailBookingModal();
                        reloadBookingData();
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.message,
                            icon: 'error',
                            confirmButtonColor: '#dc2626'
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error', 'Terjadi kesalahan pada server', 'error');
                });
            }
        });
    }


    // ==========================================
    // RE-FETCH DATA TANPA RELOAD HALAMAN
    // ==========================================
    function reloadBookingData(callback) {
        const url = (window.getUpdatedBookingsUrl || window.location.origin + '/dashboard/get_updated_bookings');
        fetch(url)
        .then(r => r.json())
        .then(data => {
            window.bookingData = data;
            
            // Re-render kalender jika dibuka
            renderCalendar();

            // Re-render daftar ruangan
            renderRoomList(data);

            if (typeof callback === 'function') callback();
        })
        .catch(err => console.error('Error fetching updated bookings:', err));
    }

    // ===== CLEAN 4-ROW AUTO-ROTATION ANIMATION =====
    let rowRotateInterval = null;
    let currentRotateIndex = 0;

    function renderRoomList(data) {
        startRowRotation(data);
    }

    function startRowRotation(data) {
        if (rowRotateInterval) clearInterval(rowRotateInterval);

        const wrapper = document.getElementById('roomListWrapper');
        if (!wrapper) return;

        if (!data || data.length === 0) {
            wrapper.innerHTML = `
                <div class="empty-state" style="text-align: center; padding: 40px 20px; color: rgba(30, 41, 59, 0.5);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 15px; opacity: 0.5;">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="9" y1="3" x2="9" y2="21"></line>
                    </svg>
                    <h3 style="font-size: 1.1rem; margin: 0 0 5px 0; color: rgba(30, 41, 59, 0.7);">Belum ada jadwal peminjaman</h3>
                    <p style="font-size: 0.9rem; margin: 0;">Jadwal peminjaman ruangan akan tampil di sini.</p>
                </div>
            `;
            return;
        }

        let today = new Date();
        let y = today.getFullYear();
        let m = String(today.getMonth() + 1).padStart(2, '0');
        let d = String(today.getDate()).padStart(2, '0');
        let todayStr = `${y}-${m}-${d}`;

        // Filter jadwal aktif (hari ini & mendatang)
        let activeList = data.filter(b => b.tanggal_selesai >= todayStr);

        activeList.sort((a, b) => {
            if (a.tanggal_mulai !== b.tanggal_mulai) {
                return a.tanggal_mulai.localeCompare(b.tanggal_mulai);
            }
            return (a.jam_mulai || '').localeCompare(b.jam_mulai || '');
        });

        const pageSize = 4;
        const totalPages = Math.ceil(activeList.length / pageSize);
        currentRotateIndex = 0;

        // Render Pertama
        renderRowPage(activeList, currentRotateIndex, pageSize, false);

        // Putar Otomatis 4 Baris Setiap 9 Detik secara konsisten
        if (rowRotateInterval) clearInterval(rowRotateInterval);
        if (totalPages > 1) {
            rowRotateInterval = setInterval(() => {
                currentRotateIndex = (currentRotateIndex + 1) % totalPages;
                renderRowPage(activeList, currentRotateIndex, pageSize, true);
            }, 9000);
        }
    }

    function renderRowPage(list, pageIndex, pageSize, isTransitioning) {
        const wrapper = document.getElementById('roomListWrapper');
        if (!wrapper) return;

        const pageData = list.slice(pageIndex * pageSize, (pageIndex + 1) * pageSize);

        if (!isTransitioning) {
            let html = '';
            pageData.forEach(j => { html += createRowHTML(j); });
            wrapper.innerHTML = html;
            return;
        }

        // ANIMASI TRANSISI ROTASI BARIS (SLIDE & FADE SMOOTH DAN TENANG)
        const existingRows = wrapper.querySelectorAll('.room-item');
        existingRows.forEach(row => {
            row.style.transition = 'opacity 0.35s cubic-bezier(0.4, 0, 0.2, 1), transform 0.35s cubic-bezier(0.4, 0, 0.2, 1)';
            row.style.opacity = '0';
            row.style.transform = 'translateY(-6px)';
        });

        setTimeout(() => {
            let html = '';
            pageData.forEach(j => { html += createRowHTML(j); });
            wrapper.innerHTML = html;

            const newRows = wrapper.querySelectorAll('.room-item');
            newRows.forEach((row, idx) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(6px)';

                setTimeout(() => {
                    row.style.transition = 'opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1), transform 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';

                    // ANIMASI KETIK HALUS PADA TEKS DINAMIS TANPA MENGGESER LAYOUT
                    animateAllRowElements(row);
                }, idx * 70);
            });
        }, 360);
    }

    function animateAllRowElements(row) {
        if (!row) return;
        const titleEl = row.querySelector('.room-info h3');
        const subEl   = row.querySelector('.room-info p');
        const userEl  = row.querySelector('.tag-user-name');
        const descEl  = row.querySelector('.room-desc-text');

        if (titleEl) typeTextEffect(titleEl, 28);
        if (subEl)   typeTextEffect(subEl, 18);
        if (userEl)  typeTextEffect(userEl, 20);
        if (descEl)  typeTextEffect(descEl, 12);
    }

    function typeTextEffect(element, speed) {
        if (!element) return;
        if (element._typeTimer) {
            clearInterval(element._typeTimer);
            element._typeTimer = null;
        }
        const fullText = element.getAttribute('data-text') || element.innerText || '';
        if (!fullText) return;

        element.innerText = '';
        let i = 0;
        element._typeTimer = setInterval(() => {
            if (i < fullText.length) {
                element.innerText = fullText.slice(0, i + 1);
                i++;
            } else {
                clearInterval(element._typeTimer);
                element._typeTimer = null;
            }
        }, speed || 18);
    }

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

    function createRowHTML(j) {
        const st = getStatusStyle(j.status);
        const dateStr = formatIndoDateRange(j.tanggal_mulai, j.tanggal_selesai);

        const jMulai = j.jam_mulai ? j.jam_mulai.substring(0, 5) : '00:00';
        const jSelesai = j.jam_selesai ? j.jam_selesai.substring(0, 5) : '00:00';
        const lokasi = (j.lokasi || '').replace(/"/g, '&quot;');
        const kapasitas = j.kapasitas || '';
        const namaKategori = (j.nama_kategori || 'Ruangan').replace(/"/g, '&quot;');

        let metaHtml = '';
        if (lokasi || kapasitas) {
            metaHtml = `
                <div class="rht-meta">
                    ${lokasi ? `<span>📍 ${lokasi}</span>` : ''}
                    ${kapasitas ? `<span>👥 ${kapasitas} Orang</span>` : ''}
                </div>
            `;
        }

        return `
            <div class="room-item" onclick="openDetailBookingModal(${j.id})" style="cursor: pointer;">
                <div class="room-item-left">
                    <div class="room-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    </div>
                    <div class="room-info" title="${namaRuangan} (${kode})">
                        <h3 data-text="${kode}">${kode}</h3>
                        <p data-text="${namaRuangan}">${namaRuangan}</p>
                    </div>

                    <!-- Floating Room Detail Tooltip on Hover (Direct child of room-item-left) -->
                    <div class="room-hover-tooltip">
                        <div class="rht-header">
                            <span class="rht-code">${kode}</span>
                            <span class="rht-cat">${namaKategori}</span>
                        </div>
                        <div class="rht-title">${namaRuangan}</div>
                        ${metaHtml}
                    </div>
                </div>
                
                <div class="room-item-tags">
                    <span class="tag" title="${namaLengkap}">
                        <svg style="margin-right:4px; vertical-align:text-bottom; flex-shrink: 0;" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        <span class="tag-user-name" data-text="${namaLengkap}">${namaLengkap}</span>
                    </span>
                    <span class="tag" style="border-color: #fb923c; color: #ea580c; background: #ffffff;">
                        <span class="tag-time-text" data-text="${timeStr}">${timeStr}</span>
                    </span>
                </div>

                <div class="room-item-date">
                    <span class="room-date-text" data-text="${dateStr}">${dateStr}</span>
                </div>

                <div class="room-item-desc" title="${keterangan}">
                    <span class="room-desc-text" data-text="${keterangan}">${keterangan}</span>
                    
                    <!-- Floating Keterangan Detail Tooltip on Hover -->
                    <div class="desc-hover-tooltip">
                        <span class="dht-badge">📝 Keterangan / Keperluan</span>
                        <div class="dht-content">${keterangan}</div>
                    </div>
                </div>

                <div class="room-item-action">
                    <span class="landing-status-badge" style="background:${st.badgeBg}; color:${st.badgeColor};">
                        <span style="width:7px;height:7px;border-radius:50%;background:${st.dot};flex-shrink:0;"></span>
                        <span class="landing-status-label" data-text="${st.label}">${st.label}</span>
                    </span>
                </div>
            </div>
        `;
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.bookingData !== 'undefined' && window.bookingData.length > 0) {
            renderRoomList(window.bookingData);
        }
    });







