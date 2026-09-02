<!-- Global Custom Cursor Partial: Glowing Orange Solid Circle (Hover Expand Support) -->
<style>
    /* Ensure SweetAlert2 popups always render in front of all modal overlays */
    .swal2-container {
        z-index: 2147483600 !important;
    }

    /* Hide default laptop/OS cursor on ALL elements globally ONLY on Desktop */
    @media (min-width: 901px) and (pointer: fine) {
        *, *::before, *::after, html, body, a, button, input, select, textarea, label, [role="button"], tr, td, th {
            cursor: none !important;
        }
    }

    /* Di Responsif Layar HP: Kembalikan kursor standar & sembunyikan bulatan custom kursor */
    @media (max-width: 900px), (pointer: coarse) {
        *, *::before, *::after, html, body, a, button, input, select, textarea, label, [role="button"], tr, td, th {
            cursor: auto !important;
        }
        a, button, [role="button"], [onclick], select, summary {
            cursor: pointer !important;
        }
        input[type="text"], input[type="password"], input[type="email"], textarea {
            cursor: text !important;
        }
        #customCursorCircle {
            display: none !important;
            opacity: 0 !important;
            visibility: hidden !important;
            pointer-events: none !important;
        }
    }

    #customCursorCircle {
        position: fixed !important;
        top: -100px;
        left: -100px;
        width: 24px !important;
        height: 24px !important;
        border: 2.5px solid #ea580c !important;
        background: rgba(234, 88, 12, 0.25) !important;
        backdrop-filter: blur(1px) !important;
        -webkit-backdrop-filter: blur(1px) !important;
        border-radius: 50% !important;
        pointer-events: none !important;
        z-index: 2147483647 !important;
        transform: translate(-50%, -50%) !important;
        box-shadow: 0 0 16px rgba(234, 88, 12, 0.8), inset 0 0 6px rgba(234, 88, 12, 0.2) !important;
        transition: width 0.22s cubic-bezier(0.25, 1, 0.5, 1),
                    height 0.22s cubic-bezier(0.25, 1, 0.5, 1),
                    background-color 0.22s ease,
                    border-color 0.22s ease,
                    border-width 0.22s ease,
                    box-shadow 0.22s ease !important;
        will-change: left, top, width, height;
    }

    /* Membesar saat mendekat / hover ke button, link, atau elemen interaktif */
    html.cursor-hover #customCursorCircle,
    body.cursor-hover #customCursorCircle,
    #customCursorCircle.hovered {
        width: 48px !important;
        height: 48px !important;
        background: rgba(234, 88, 12, 0.35) !important;
        border-color: #ea580c !important;
        border-width: 3px !important;
        box-shadow: 0 0 26px rgba(234, 88, 12, 0.95), inset 0 0 10px rgba(234, 88, 12, 0.35) !important;
    }

    /* Efek klik ditekan */
    html.cursor-active #customCursorCircle,
    body.cursor-active #customCursorCircle,
    #customCursorCircle.active {
        width: 14px !important;
        height: 14px !important;
        background: #ea580c !important;
        border-color: #ea580c !important;
        border-width: 3px !important;
    }
</style>

<div id="customCursorCircle"></div>

<script>
(function() {
    function setupCursor() {
        if (window.innerWidth <= 900 || window.matchMedia('(pointer: coarse)').matches) {
            const el = document.getElementById('customCursorCircle');
            if (el) el.style.display = 'none';
            return;
        }

        let circle = document.getElementById('customCursorCircle');
        const rootContainer = document.documentElement || document.body;

        if (!circle) {
            circle = document.createElement('div');
            circle.id = 'customCursorCircle';
            rootContainer.appendChild(circle);
        } else if (circle.parentElement !== rootContainer) {
            rootContainer.appendChild(circle);
        }

        let mouseX = -100, mouseY = -100;
        let circleX = -100, circleY = -100;

        function updateMouse(e) {
            mouseX = e.clientX;
            mouseY = e.clientY;
        }

        function updateTouch(e) {
            if (e.touches && e.touches[0]) {
                mouseX = e.touches[0].clientX;
                mouseY = e.touches[0].clientY;
            }
        }

        window.addEventListener('mousemove', updateMouse, { passive: true });
        document.addEventListener('mousemove', updateMouse, { passive: true });
        window.addEventListener('pointermove', updateMouse, { passive: true });
        document.addEventListener('pointermove', updateMouse, { passive: true });
        window.addEventListener('touchmove', updateTouch, { passive: true });
        window.addEventListener('touchstart', updateTouch, { passive: true });

        function render() {
            circleX += (mouseX - circleX) * 0.28;
            circleY += (mouseY - circleY) * 0.28;
            circle.style.left = circleX + 'px';
            circle.style.top = circleY + 'px';
            requestAnimationFrame(render);
        }
        requestAnimationFrame(render);

        // Selector seluruh elemen yang bisa diklik / berinteraksi
        const hoverSelector = 'a, button, input, select, textarea, label, [role="button"], tr, [onclick], .cursor-pointer, .box-3d, .btn-action, .nav-link, .card-3d-orange, img, svg, i.bi, i.fa-solid';
        
        document.addEventListener('mouseover', function(e) {
            if (e.target && e.target.closest && (e.target.closest(hoverSelector) || window.getComputedStyle(e.target).cursor === 'pointer')) {
                document.documentElement.classList.add('cursor-hover');
                document.body.classList.add('cursor-hover');
                circle.classList.add('hovered');
            }
        }, { passive: true });

        document.addEventListener('mouseout', function(e) {
            if (e.target && e.target.closest && (e.target.closest(hoverSelector) || window.getComputedStyle(e.target).cursor === 'pointer')) {
                document.documentElement.classList.remove('cursor-hover');
                document.body.classList.remove('cursor-hover');
                circle.classList.remove('hovered');
            }
        }, { passive: true });

        document.addEventListener('mousedown', function() {
            document.documentElement.classList.add('cursor-active');
            document.body.classList.add('cursor-active');
            circle.classList.add('active');
        }, { passive: true });

        document.addEventListener('mouseup', function() {
            document.documentElement.classList.remove('cursor-active');
            document.body.classList.remove('cursor-active');
            circle.classList.remove('active');
        }, { passive: true });

        document.addEventListener('pointerdown', function() {
            document.documentElement.classList.add('cursor-active');
            document.body.classList.add('cursor-active');
            circle.classList.add('active');
        }, { passive: true });

        document.addEventListener('pointerup', function() {
            document.documentElement.classList.remove('cursor-active');
            document.body.classList.remove('cursor-active');
            circle.classList.remove('active');
        }, { passive: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupCursor);
    } else {
        setupCursor();
    }
})();
</script>
