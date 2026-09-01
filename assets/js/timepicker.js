let activeTarget = 'mulai'; // 'mulai' or 'selesai'
let selectedHour = 14;
let selectedMinute = 30;
let isSelectingHour = true; // true = hour mode, false = minute mode

function initTimePicker() {
    renderClock();
    updateDisplay();
}

function openTimePicker(target) {
    activeTarget = target;
    document.getElementById('timePickerModal').classList.add('active');
    
    // Set date label
    let d = document.querySelector('input[name="tanggal_peminjaman"]').value;
    if(d) {
        document.getElementById('tpDateDisplay').innerText = d; // Simple format
    }
}

function closeTimePicker() {
    document.getElementById('timePickerModal').classList.remove('active');
}

function applyTimePicker() {
    let hh = selectedHour.toString().padStart(2, '0');
    let mm = selectedMinute.toString().padStart(2, '0');
    
    if(activeTarget === 'mulai') {
        document.getElementById('inputJamMulai').value = hh + ':' + mm;
    } else {
        document.getElementById('inputJamSelesai').value = hh + ':' + mm;
    }
    
    closeTimePicker();
}

function setMode(mode) {
    isSelectingHour = (mode === 'hour');
    document.getElementById('tpTabHour').classList.toggle('active', isSelectingHour);
    document.getElementById('tpTabMinute').classList.toggle('active', !isSelectingHour);
    renderClock();
}

function setQuickTime(hh, mm) {
    selectedHour = hh;
    selectedMinute = mm;
    updateDisplay();
}

function updateDisplay() {
    let hh = selectedHour.toString().padStart(2, '0');
    let mm = selectedMinute.toString().padStart(2, '0');
    document.getElementById('tpDisplayHour').innerText = hh;
    document.getElementById('tpDisplayMinute').innerText = mm;
    renderClock();
}

function drawNumber(container, val, isInner) {
    let el = document.createElement('div');
    el.className = 'tp-clock-number ' + (isInner ? 'inner' : '');
    el.innerText = val.toString().padStart(isInner ? 2 : 1, '0');
    
    let radius = isInner ? 60 : 95;
    
    let angleBase = isSelectingHour ? (val % 12) * 30 : val * 6;
    let rad = (angleBase - 90) * (Math.PI / 180);
    
    let x = 120 + radius * Math.cos(rad);
    let y = 120 + radius * Math.sin(rad);
    
    el.style.left = x + 'px';
    el.style.top = y + 'px';
    
    let isActive = false;
    if(isSelectingHour && selectedHour === val) isActive = true;
    if(!isSelectingHour && selectedMinute === val) isActive = true;
    
    if(isActive) {
        el.classList.add('active');
        el.style.background = '#7c3aed';
        el.style.color = '#ffffff';
        el.style.borderRadius = '50%';
        el.style.fontWeight = '700';
        el.style.boxShadow = '0 2px 8px rgba(124, 58, 237, 0.4)';
        el.style.zIndex = 20;
    }
    
    container.appendChild(el);
}

function renderClock() {
    const container = document.getElementById('tpClockNumbers');
    const hand = document.getElementById('tpClockHand');
    container.innerHTML = '';
    
    let items = isSelectingHour ? 12 : 60;
    let step = isSelectingHour ? 1 : 5;
    
    // Draw numbers
    for(let i = step; i <= items; i += step) {
        let val = (i === 60) ? 0 : i;
        if(isSelectingHour) {
            drawNumber(container, val, false);
            drawNumber(container, val + 12 === 24 ? 0 : val + 12, true);
        } else {
            drawNumber(container, val, false);
        }
    }
    
    // If minute is not multiple of 5, draw it dynamically so it shows in the purple dot
    if (!isSelectingHour && selectedMinute % 5 !== 0) {
        drawNumber(container, selectedMinute, false);
    }
    
    // Position hand
    let val = isSelectingHour ? selectedHour : selectedMinute;
    let targetAngle = isSelectingHour ? (val % 12) * 30 : val * 6;
    
    // Continuous shortest rotational path (eliminates 360-degree reverse spin on 11 <-> 12)
    if (typeof hand.currentAngle === 'undefined') {
        hand.currentAngle = targetAngle;
    } else {
        let diff = (targetAngle - (hand.currentAngle % 360) + 540) % 360 - 180;
        hand.currentAngle += diff;
    }
    
    // Determine if we point to inner circle (hours 13-00)
    let isInner = isSelectingHour && (val === 0 || val > 12);
    let handHeight = isInner ? '60px' : '95px';
    
    hand.style.height = handHeight;
    hand.style.transition = isDragging ? 'none' : 'transform 0.15s cubic-bezier(0.4, 0, 0.2, 1)';
    hand.style.transform = `translate(-50%, 0) rotate(${hand.currentAngle}deg)`;
}

let isDragging = false;

function handleClockEvent(e) {
    if(e.type === 'mousemove' && !isDragging) return;
    
    // Prevent default to avoid text selection while dragging
    if (e.cancelable) e.preventDefault();
    
    // Support touch and mouse
    let clientX = e.clientX;
    let clientY = e.clientY;
    
    if(e.touches && e.touches.length > 0) {
        clientX = e.touches[0].clientX;
        clientY = e.touches[0].clientY;
    }
    
    const rect = document.getElementById('tpClockContainer').getBoundingClientRect();
    const x = clientX - rect.left - 120; // 120 is center
    const y = clientY - rect.top - 120;
    
    // Calculate angle in degrees (0 is top, clockwise)
    let angle = Math.atan2(y, x) * (180 / Math.PI) + 90;
    if (angle < 0) angle += 360;
    
    const distance = Math.sqrt(x*x + y*y);
    
    if (isSelectingHour) {
        let hour = Math.round(angle / 30);
        if (hour === 0) hour = 12;
        if (hour === 12 && angle > 345) hour = 12;
        
        // Inner circle for 24h mode (13-00)
        if (distance < 75) {
            hour += 12;
            if (hour === 24) hour = 0;
        }
        
        if (selectedHour !== hour) {
            selectedHour = hour;
            updateDisplay();
        }
    } else {
        let minute = Math.round(angle / 6);
        if (minute === 60) minute = 0;
        
        if (selectedMinute !== minute) {
            selectedMinute = minute;
            updateDisplay();
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    initTimePicker();
    
    const clockContainer = document.getElementById('tpClockContainer');
    
    // Mouse Events
    clockContainer.addEventListener('mousedown', function(e) {
        isDragging = true;
        handleClockEvent(e);
    });
    document.addEventListener('mousemove', handleClockEvent);
    document.addEventListener('mouseup', function(e) {
        if(isDragging && isSelectingHour) {
            setMode('minute'); // Auto switch after hour drop
        }
        isDragging = false;
    });
    
    // Touch Events
    clockContainer.addEventListener('touchstart', function(e) {
        isDragging = true;
        handleClockEvent(e);
    }, {passive: false});
    document.addEventListener('touchmove', handleClockEvent, {passive: false});
    document.addEventListener('touchend', function(e) {
        if(isDragging && isSelectingHour) {
            setMode('minute');
        }
        isDragging = false;
    });
    
    let dateInput = document.querySelector('input[name="tanggal_peminjaman"]');
    if(dateInput) {
        dateInput.addEventListener('change', function() {
            if(this.value) {
                document.getElementById('timeSelectionGroup').style.display = 'block';
            } else {
                document.getElementById('timeSelectionGroup').style.display = 'none';
            }
        });
    }
});

// ===== DRAG SELECT UNTUK TIME RANGE SLOTS =====
(function() {
    var isDragging = false;
    var dragStartIdx = -1;
    var currentEndIdx = -1;

    function getSlots() {
        return Array.from(document.querySelectorAll('#tpTimeSlots .tp-slot'));
    }

    function resetSlotStyle(slot) {
        slot.style.background    = '#fff';
        slot.style.borderColor   = '#e2e8f0';
        slot.style.color         = '#475569';
        slot.style.boxShadow     = '0 1px 3px rgba(0,0,0,0.06)';
    }

    function previewSlotStyle(slot) {
        slot.style.background    = '#ede9fe';
        slot.style.borderColor   = '#7c3aed';
        slot.style.color         = '#6d28d9';
        slot.style.boxShadow     = '0 2px 6px rgba(124,58,237,0.2)';
    }

    function selectedSlotStyle(slot) {
        slot.style.background    = '#7c3aed';
        slot.style.borderColor   = '#7c3aed';
        slot.style.color         = '#fff';
        slot.style.boxShadow     = '0 3px 10px rgba(124,58,237,0.35)';
    }

    function updateHighlight(minIdx, maxIdx) {
        getSlots().forEach(function(slot, i) {
            if (i >= minIdx && i <= maxIdx) {
                previewSlotStyle(slot);
            } else {
                resetSlotStyle(slot);
            }
        });
    }

    function finalizeSelection(minIdx, maxIdx) {
        var slots = getSlots();

        slots.forEach(function(slot, i) {
            if (i >= minIdx && i <= maxIdx) {
                selectedSlotStyle(slot);
            } else {
                resetSlotStyle(slot);
            }
        });

        if (slots[minIdx] && slots[maxIdx]) {
            var jamMulai  = slots[minIdx].getAttribute('data-start');
            var jamSelesai = slots[maxIdx].getAttribute('data-end');

            var elMulai   = document.getElementById('inputJamMulai');
            var elSelesai = document.getElementById('inputJamSelesai');
            if (elMulai)   elMulai.value   = jamMulai;
            if (elSelesai) elSelesai.value  = jamSelesai;

            // Tutup picker otomatis setelah sedikit delay supaya user lihat seleksinya
            setTimeout(function() {
                var panel = document.getElementById('inlineClockPanel');
                if (panel) panel.style.display = 'none';
                // Reset semua slot ke default
                slots.forEach(resetSlotStyle);
            }, 400);
        }
    }

    document.addEventListener('mousedown', function(e) {
        var slot = e.target.closest('.tp-slot');
        if (!slot) return;
        e.preventDefault();
        isDragging = true;
        var slots = getSlots();
        dragStartIdx = slots.indexOf(slot);
        currentEndIdx = dragStartIdx;
        updateHighlight(dragStartIdx, dragStartIdx);
    });

    document.addEventListener('mouseover', function(e) {
        if (!isDragging) return;
        var slot = e.target.closest('.tp-slot');
        if (!slot) return;
        var slots = getSlots();
        var idx = slots.indexOf(slot);
        if (idx === -1) return;
        currentEndIdx = idx;
        var minIdx = Math.min(dragStartIdx, currentEndIdx);
        var maxIdx = Math.max(dragStartIdx, currentEndIdx);
        updateHighlight(minIdx, maxIdx);
    });

    document.addEventListener('mouseup', function(e) {
        if (!isDragging) return;
        isDragging = false;
        var minIdx = Math.min(dragStartIdx, currentEndIdx);
        var maxIdx = Math.max(dragStartIdx, currentEndIdx);
        finalizeSelection(minIdx, maxIdx);
    });

    // Touch support
    document.addEventListener('touchstart', function(e) {
        var slot = e.target.closest('.tp-slot');
        if (!slot) return;
        isDragging = true;
        var slots = getSlots();
        dragStartIdx = slots.indexOf(slot);
        currentEndIdx = dragStartIdx;
        updateHighlight(dragStartIdx, dragStartIdx);
    }, {passive: true});

    document.addEventListener('touchmove', function(e) {
        if (!isDragging) return;
        var touch = e.touches[0];
        var el = document.elementFromPoint(touch.clientX, touch.clientY);
        if (!el) return;
        var slot = el.closest('.tp-slot');
        if (!slot) return;
        var slots = getSlots();
        var idx = slots.indexOf(slot);
        if (idx === -1) return;
        currentEndIdx = idx;
        var minIdx = Math.min(dragStartIdx, currentEndIdx);
        var maxIdx = Math.max(dragStartIdx, currentEndIdx);
        updateHighlight(minIdx, maxIdx);
    }, {passive: true});

    document.addEventListener('touchend', function() {
        if (!isDragging) return;
        isDragging = false;
        var minIdx = Math.min(dragStartIdx, currentEndIdx);
        var maxIdx = Math.max(dragStartIdx, currentEndIdx);
        finalizeSelection(minIdx, maxIdx);
    });
})();
