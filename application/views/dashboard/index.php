<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IFIK - Dashboard Premium</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- NProgress (Web Loading Bar) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>
    
    <!-- Model Viewer for 3D -->
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.3.0/model-viewer.min.js"></script>

    <!-- jQuery & Flatpickr & SweetAlert2 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom CSS untuk tiap Section -->
    <link rel="stylesheet" href="<?= base_url('assets/css/timepicker.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/info_ruangan.css?v=' . time()) ?>">

    <!-- Preload 3D Models agar transisi instan tanpa delay/hilang -->
    <link rel="preload" href="<?= base_url('assets/3D/ifikouter.glb') ?>" as="fetch" crossorigin>
    <link rel="preload" href="<?= base_url('assets/3D/ifik.glb') ?>" as="fetch" crossorigin>

    <!-- Lenis Smooth Scroll -->
    <script src="https://cdn.jsdelivr.net/npm/lenis@1.1.18/dist/lenis.min.js"></script>

    <script>
        // Mulai loading web saat halaman pertama kali diproses
        NProgress.start(); 
        window.addEventListener('load', () => {
            // Selesai saat seluruh aset (termasuk 3D model besar) sudah termuat sepenuhnya
            NProgress.done(); 
        });
    </script>
    
    <style>
        :root {
            --bg-color: #fbf7f1; /* Off-white theme dari login */
            --text-color: #1e293b;
            --glass-bg: rgba(255, 255, 255, 0.4);
            --glass-border: rgba(234, 88, 12, 0.2);
        }

        /* NProgress Customization (Orange Premium) */
        #nprogress .bar {
            background: #ea580c !important;
            height: 4px !important;
            z-index: 10001 !important;
        }
        #nprogress .peg {
            box-shadow: 0 0 10px #ea580c, 0 0 5px #ea580c !important;
        }
        #nprogress .spinner-icon {
            border-top-color: #ea580c !important;
            border-left-color: #ea580c !important;
        }
        
        /* Permanent Hide for Google Model-Viewer Built-in Blue Arrow Interaction Prompt Graphic */
        model-viewer::part(user-prompt),
        model-viewer::part(prompt),
        model-viewer::part(interaction-prompt),
        model-viewer::part(ar-button),
        model-viewer .slot.user-prompt,
        model-viewer #prompt,
        model-viewer [slot="user-prompt"],
        model-viewer img,
        .user-prompt,
        #user-prompt {
            display: none !important;
            opacity: 0 !important;
            visibility: hidden !important;
            pointer-events: none !important;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            overflow: hidden; /* Prevent default window scrolling */
        }

        /* Smooth Container for Vertical Sections with Lenis */
        .dashboard-container {
            height: 100vh;
            width: 100vw;
            overflow-y: scroll;
            overscroll-behavior: none;
            scrollbar-width: none; /* Firefox */
            position: relative;
        }
        
        .dashboard-container::-webkit-scrollbar {
            display: none; /* Chrome/Safari/Edge */
        }

        /* Individual Vertical Sections */
        .section-wrapper {
            height: 100vh;
            width: 100vw;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* 3D Model Center Piece - Global Fixed Position (Scroll-Driven Continuous Morph) */
        #global-model-container {
            position: fixed;
            width: 600px;
            height: 600px;
            pointer-events: none;
            z-index: 8;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(1) rotateY(0deg);
            will-change: transform, left, top, opacity;
            transition: opacity 0.4s ease;
        }

        .model-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            display: block !important;
            will-change: opacity;
            transition: opacity 0.15s linear;
        }
        
        #global-model-container model-viewer {
            width: 100%;
            height: 100%;
            display: block;
        }

        /* Ambient Glow di belakang Model 3D */
        .glow-effect {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(0,0,0,0) 70%);
            z-index: 1;
            filter: blur(50px);
            animation: pulse 4s infinite alternate;
            transition: opacity 0.8s ease;
        }

        /* Hilangkan efek cahaya (glow) saat logo mengecil di pojok kiri atas */
        #global-model-container.pos-top-left .glow-effect {
            opacity: 0;
        }

        /* Scroll Progress Bar di Atas */
        #scroll-progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            height: 4px;
            background: linear-gradient(90deg, #f86b1d, #ea580c);
            width: 0%;
            z-index: 10000;
            transition: width 0.1s ease-out;
            box-shadow: 0 0 10px rgba(234, 88, 12, 0.6);
        }

        @keyframes pulse {
            0% { transform: translate(-50%, -50%) scale(0.9); opacity: 0.8; }
            100% { transform: translate(-50%, -50%) scale(1.1); opacity: 1; }
        }

        /* --- RESPONSIVE DESIGN FOR 3D LOGO --- */
        /* Deteksi jika layar terlalu sempit (Lebar) */
        @media (max-width: 1200px) {
            #global-model-container.pos-left {
                left: 15%; 
            }
        }
        
        @media (max-width: 900px) {
            #global-model-container.pos-left {
                left: 10%;
                top: 40%; 
            }
            #global-model-container.pos-top-left {
                left: 40px;
                top: 35px;
                transform: translate(-50%, -50%) scale(0.1) rotateY(720deg);
            }
        }

        /* Deteksi jika layar "Terasa Pendek" karena Scaling 125%-150% atau Laptop Kecil */
        @media (max-height: 950px) {
            #global-model-container {
                /* Menggunakan Viewport Height (vh) agar logo mutlak mengikuti tinggi layar, bukan pixel kaku */
                width: 50vh; 
                height: 50vh;
            }
            .glow-effect {
                width: 40vh;
                height: 40vh;
            }
            #global-model-container.pos-center {
                /* Menggeser titik tengah logo sedikit lebih ke atas menjauhi kartu di bawah */
                top: 42%; 
                transform: translate(-50%, -50%) scale(1) rotateY(0deg);
            }
        }
        
        @media (max-height: 750px) {
            #global-model-container {
                width: 40vh;
                height: 40vh;
            }
            #global-model-container.pos-center {
                top: 38%; 
            }
        }

        /* --- SPLASH SCREEN --- */
        #splash-screen {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            background: #ea580c; /* Warna Solid Oranye Premium */
            z-index: 999999;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.8s cubic-bezier(0.77, 0, 0.175, 1);
        }
        #splash-screen.hide-splash {
            transform: translateY(-100%);
        }
        .splash-content {
            display: flex; flex-direction: column; align-items: center; gap: 30px;
        }
        .splash-logo {
            height: 65px;
            filter: drop-shadow(0 0 20px rgba(255,255,255,0.4)) brightness(0) invert(1);
        }
        .splash-counter-wrapper {
            display: flex; flex-direction: column; align-items: center; gap: 10px;
        }
        #splash-counter {
            color: #ffffff;
            font-size: 2.8rem;
            font-weight: 900;
            letter-spacing: 2px;
            font-variant-numeric: tabular-nums;
            text-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .splash-progress-track {
            width: 220px; height: 4px;
            background: rgba(255,255,255,0.2);
            border-radius: 4px; overflow: hidden;
        }
        #splash-progress-bar {
            width: 0%; height: 100%;
            background: #ffffff;
            box-shadow: 0 0 10px #ffffff;
        }

        /* Global Sticky Scroll Button */
        .global-scroll-btn {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999; /* Frontmost */
            width: 65px;
            height: 65px;
            border-radius: 50%;
            background: transparent; /* Transparent background */
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 2px solid #ea580c; /* Orange outer border */
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            padding: 0;
            backdrop-filter: blur(4px);
            pointer-events: auto;
        }
        .global-scroll-btn:hover {
            background: #ea580c;
            transform: translateX(-50%) translateY(-5px);
            box-shadow: 0 10px 25px rgba(234, 88, 12, 0.7);
        }
        .global-scroll-btn svg {
            width: 32px;
            height: 32px;
            animation: arrowBounce 2s infinite;
            transition: transform 0.5s ease;
        }
        @keyframes arrowBounce {
            0%, 100% { transform: translateY(-3px); }
            50% { transform: translateY(3px); }
        }
        /* Rotasi panah ke atas jika di sesi terakhir */
        .global-scroll-btn.pointing-up svg {
            transform: rotate(180deg);
        }

        /* Elevate specific content cards so they glide over the global scroll button (z-index: 1) */
        .about-container, 
        .lab-container, 
        .news-stage,
        .vt-grid {
            position: relative;
            z-index: 10 !important;
        }
    </style>
</head>
<body>

    <!-- SPLASH SCREEN ENTRANCE -->
    <div id="splash-screen">
        <div class="splash-content">
            <img src="<?= base_url('assets/images/logo-dummy.webp') ?>" alt="IFIK Logo" class="splash-logo">
            <div class="splash-counter-wrapper">
                <div id="splash-counter">0%</div>
                <div class="splash-progress-track">
                    <div id="splash-progress-bar"></div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.splashScreenDone = false;
            let count = 0;
            const counterEl = document.getElementById('splash-counter');
            const barEl = document.getElementById('splash-progress-bar');
            const splash = document.getElementById('splash-screen');
            
            // Simulasi loading super cepat 0 - 100
            const interval = setInterval(() => {
                count += Math.floor(Math.random() * 18) + 4; // naik random 4-22%
                if (count >= 100) {
                    count = 100;
                    clearInterval(interval);
                    counterEl.innerText = count + '%';
                    barEl.style.width = count + '%';
                    
                    // Jeda sebentar sebelum layar menyusut ke atas
                    setTimeout(() => {
                        splash.classList.add('hide-splash');
                        // Beri tanda ke body bahwa entrance mulai setelah splash bergerak
                        setTimeout(() => {
                            window.splashScreenDone = true;
                            document.body.classList.add('play-animations');
                        }, 200);
                    }, 400);
                } else {
                    counterEl.innerText = count + '%';
                    barEl.style.width = count + '%';
                }
            }, 60);
        });
    </script>
    <!-- Progress Bar (Pengganti Scrollbar) -->
    <div id="scroll-progress-bar"></div>

    <!-- Navigation Bar Menu -->
    <?php $this->load->view('partials/navbar'); ?>

    <!-- 3D Model diletakkan di luar container scroll agar statis (fixed) di layar -->
    <div id="global-model-container">
        <div class="glow-effect" id="modelGlow"></div>
        <!-- Model 1: ifikouter.glb (Logo Router 3D) untuk Header / Sesi 1 -->
        <model-viewer 
            id="modelOuter"
            class="model-layer"
            src="<?= base_url('assets/3D/ifikouter.glb') ?>" 
            alt="3D Logo IFIK Outer" 
            disable-zoom 
            shadow-intensity="1.5" 
            shadow-softness="0.8"
            exposure="1.15"
            camera-orbit="90deg 85deg 100%"
            field-of-view="24deg"
            interaction-prompt="none"
            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: transparent; opacity: 1;">
        </model-viewer>

        <!-- Model 2: ifik.glb (Logo IFIK 3D) untuk Informasi Ruangan / Sesi 2 dst -->
        <model-viewer 
            id="modelInner"
            class="model-layer"
            src="<?= base_url('assets/3D/ifik.glb') ?>" 
            alt="3D Logo IFIK" 
            disable-zoom 
            shadow-intensity="1.5" 
            shadow-softness="0.8"
            exposure="1.15"
            camera-orbit="90deg 85deg 100%"
            field-of-view="24deg"
            interaction-prompt="none"
            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: transparent; opacity: 0;">
        </model-viewer>
    </div>

    <!-- Sticky Center Scroll Button (Ditaruh sebelum konten dashboard agar tertutup jika ada elemen dgn z-index >= 1) -->
    <button class="global-scroll-btn" id="globalScrollBtn" onclick="scrollToNextSection()" title="Scroll ke Sesi Selanjutnya">
        <svg fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
    </button>

    <!-- Main Container that handles vertical Smooth Scrolling -->
    <div class="dashboard-container">
        
        <!-- Sesi 1: Header -->
        <?php $this->load->view('dashboard/sections/header'); ?>

        <!-- Sesi 2: Info Ruangan -->
        <?php $this->load->view('dashboard/sections/info_ruangan'); ?>

        <!-- Sesi 3: Berita & Informasi Terkini -->
        <?php $this->load->view('dashboard/sections/berita'); ?>

        <!-- Sesi 5: Virtual Tour 3D -->
        <?php $this->load->view('dashboard/sections/virtual_tour'); ?>

        <!-- Sesi 6: Footer -->
        <div class="section-wrapper" id="section-footer" style="height: auto; min-height: 100vh; scroll-snap-align: start; display: flex; align-items: flex-end;">
            <?php $this->load->view('partials/footer'); ?>
        </div>

    </div>

    <!-- JS untuk Lenis Smooth Scroll, Parallax dan Continuous 3D Morphing -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modelOuter = document.getElementById('modelOuter');
            const modelInner = document.getElementById('modelInner');
            const modelContainer = document.getElementById('global-model-container');
            const dashboardContainer = document.querySelector('.dashboard-container');
            const progressBar = document.getElementById('scroll-progress-bar');

            // Fungsi Kontinu Animasi Scroll-Driven 3D Model (Lusion Style Smooth Morph)
            function updateScrollDrivenModel(scrollTop) {
                const vh = window.innerHeight || 800;
                const vw = window.innerWidth || 1200;

                // Responsive target positions
                let leftPos2 = 22; // persen
                let topPos2 = 50;  // persen
                if (vw <= 900) {
                    leftPos2 = 10;
                    topPos2 = 40;
                } else if (vw <= 1200) {
                    leftPos2 = 15;
                }

                let topPos1 = 50;
                if (vh <= 750) topPos1 = 38;
                else if (vh <= 950) topPos1 = 42;

                // 1. Transisi Mulus Sesi 1 (Header) -> Sesi 2 (Info Ruangan: 3D Logo Fade Out)
                if (scrollTop <= vh) {
                    const p = Math.max(0, Math.min(1, scrollTop / vh)); // 0.0 -> 1.0
                    const ease = p * p * (3 - 2 * p);

                    const currentLeft = 50;
                    const currentTop = topPos1;
                    const currentScale = 1.0 - (0.35 * ease);
                    const currentRotY = 180 * ease;

                    // Fade out container saat scroll masuk ke Sesi 2 (Informasi Ruangan)
                    const containerOp = Math.max(0, 1 - (p * 1.8));
                    modelContainer.style.opacity = containerOp;
                    modelContainer.style.pointerEvents = containerOp < 0.1 ? 'none' : 'auto';
                    modelContainer.style.left = `${currentLeft}%`;
                    modelContainer.style.top = `${currentTop}%`;
                    modelContainer.style.transform = `translate(-50%, -50%) scale(${currentScale}) rotateY(${currentRotY}deg)`;
                    modelContainer.style.zIndex = '8';

                    if (modelOuter) modelOuter.style.opacity = Math.max(0, 1 - (p * 2));
                    if (modelInner) modelInner.style.opacity = '0';

                    if (p < 0.1 && window.splashScreenDone) {
                        document.body.classList.add('play-animations');
                    } else {
                        document.body.classList.remove('play-animations');
                    }
                } 
                // Sesi 2 (Info Ruangan) -> Sesi 3 (Lab: 3D Logo Fade In menuju Navbar)
                else if (scrollTop <= 2 * vh) {
                    const p = Math.max(0, Math.min(1, (scrollTop - vh) / vh));
                    const ease = p * p * (3 - 2 * p);

                    const targetLeftPx = vw <= 900 ? 40 : 50;
                    const targetTopPx = 35;
                    const targetScale = vw <= 900 ? 0.1 : 0.11;
                    const targetRotY = 720;

                    modelContainer.style.left = `${targetLeftPx}px`;
                    modelContainer.style.top = `${targetTopPx}px`;
                    modelContainer.style.transform = `translate(-50%, -50%) scale(${targetScale}) rotateY(${targetRotY}deg)`;
                    modelContainer.style.zIndex = '110';
                    modelContainer.style.opacity = ease; // Fade in saat mendekati Sesi 3
                    modelContainer.style.pointerEvents = 'none';
                    if (modelOuter) modelOuter.style.opacity = '0';
                    if (modelInner) modelInner.style.opacity = '1';
                    document.body.classList.remove('play-animations');
                } 
                // Sesi 3 (Lab) dan seterusnya -> Tetap di Navbar
                else {
                    const targetLeftPx = vw <= 900 ? 40 : 50;
                    const targetTopPx = 35;
                    const targetScale = vw <= 900 ? 0.1 : 0.11;
                    const targetRotY = 720;

                    modelContainer.style.left = `${targetLeftPx}px`;
                    modelContainer.style.top = `${targetTopPx}px`;
                    modelContainer.style.transform = `translate(-50%, -50%) scale(${targetScale}) rotateY(${targetRotY}deg)`;
                    modelContainer.style.zIndex = '110';
                    modelContainer.style.opacity = '1';
                    modelContainer.style.pointerEvents = 'none';
                    if (modelOuter) modelOuter.style.opacity = '0';
                    if (modelInner) modelInner.style.opacity = '1';
                    document.body.classList.remove('play-animations');
                }
 

                // Update Progress Bar
                if (dashboardContainer && progressBar) {
                    const totalHeight = dashboardContainer.scrollHeight - dashboardContainer.clientHeight;
                    if (totalHeight > 0) {
                        const progressPercentage = (scrollTop / totalHeight) * 100;
                        progressBar.style.width = progressPercentage + '%';
                    }
                }
            }

            // Inisialisasi Lenis Smooth Scroll (Lusion Style Momentum)
            if (typeof Lenis !== 'undefined' && dashboardContainer) {
                window.lenis = new Lenis({
                    wrapper: dashboardContainer,
                    content: dashboardContainer,
                    duration: 1.2,
                    easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
                    orientation: 'vertical',
                    gestureOrientation: 'vertical',
                    smoothWheel: true,
                    wheelMultiplier: 1.05,
                    touchMultiplier: 1.5,
                    lerp: 0.09
                });

                function raf(time) {
                    if (window.lenis) {
                        window.lenis.raf(time);
                    }
                    requestAnimationFrame(raf);
                }
                requestAnimationFrame(raf);

                // Sinkronisasi realtime saat user scroll via Lenis
                window.lenis.on('scroll', (e) => {
                    const scrollTop = e.scroll !== undefined ? e.scroll : dashboardContainer.scrollTop;
                    
                    if (scrollTop > lastScrollTop + 2) {
                        scrollDirection = 1; // Arah Scroll ke Bawah
                    } else if (scrollTop < lastScrollTop - 2) {
                        scrollDirection = -1; // Arah Scroll ke Atas
                    }
                    lastScrollTop = scrollTop;
                    
                    updateScrollDrivenModel(scrollTop);
                });
            }

            // Fallback native scroll listener
            if (dashboardContainer) {
                dashboardContainer.addEventListener('scroll', () => {
                    const scrollTop = dashboardContainer.scrollTop;
                    if (scrollTop > lastScrollTop + 2) {
                        scrollDirection = 1;
                    } else if (scrollTop < lastScrollTop - 2) {
                        scrollDirection = -1;
                    }
                    lastScrollTop = scrollTop;
                    updateScrollDrivenModel(scrollTop);
                });
            }

            // Inisialisasi posisi awal saat halaman dibuka
            updateScrollDrivenModel(dashboardContainer ? dashboardContainer.scrollTop : 0);

            // --- GENTLE BIDIRECTIONAL SECTION COASTING (SCROLL DOWN & SCROLL UP) ---
            let snapTimeout = null;
            let isUserInteracting = false;
            let lastScrollTop = 0;
            let scrollDirection = 1; // 1 = down, -1 = up

            const triggerSectionSnap = (scrollTop) => {
                if (!window.lenis) return;
                const vh = window.innerHeight || 800;
                const ratio = scrollTop / vh;
                
                let targetIndex = Math.round(ratio);

                // Perhitungan cerdas berdasarkan arah scroll pengguna:
                if (scrollDirection === 1) {
                    // Scroll ke Bawah: Jika sudah lewati 30% perjalanan, lanjutkan meluncur perlahan ke sesi bawahnya
                    if (ratio < 0.30) {
                        targetIndex = 0; // Balik ke Header
                    } else if (ratio < 1.30) {
                        targetIndex = 1; // Informasi Ruangan
                    } else if (ratio < 2.30) {
                        targetIndex = 2; // Lab
                    } else if (ratio < 3.30) {
                        targetIndex = 3; // Berita
                    } else if (ratio < 4.30) {
                        targetIndex = 4; // Virtual Tour
                    } else {
                        targetIndex = 5; // Footer
                    }
                } else {
                    // Scroll ke Atas: Jika sudah lewati 30% perjalanan ke atas, lanjutkan meluncur ke sesi atasnya
                    if (ratio > 4.70) {
                        targetIndex = 5; // Footer
                    } else if (ratio > 3.70) {
                        targetIndex = 4; // Virtual Tour
                    } else if (ratio > 2.70) {
                        targetIndex = 3; // Berita
                    } else if (ratio > 1.70) {
                        targetIndex = 2; // Lab
                    } else if (ratio > 0.70) {
                        targetIndex = 1; // Informasi Ruangan
                    } else {
                        targetIndex = 0; // Header
                    }
                }

                const targetScrollTop = targetIndex * vh;
                
                // Hanya lakukan glide halus jika posisi masih nanggung di tengah (selisih > 25px)
                if (Math.abs(scrollTop - targetScrollTop) > 25) {
                    window.lenis.scrollTo(targetScrollTop, {
                        duration: 1.5, // Durasi diperpanjang agar terasa sangat lembut dan sinematik
                        easing: (t) => 1 - Math.pow(1 - t, 4) // Quartic ease-out yang super smooth tanpa hentakan
                    });
                }
            };

            const scheduleSnap = () => {
                clearTimeout(snapTimeout);
                // Jeda 320ms setelah putaran wheel berhenti agar inersia alami user selesai dulu baru coasting aktif
                snapTimeout = setTimeout(() => {
                    if (!isUserInteracting && dashboardContainer) {
                        triggerSectionSnap(dashboardContainer.scrollTop);
                    }
                }, 320);
            };

            if (dashboardContainer) {
                dashboardContainer.addEventListener('wheel', () => {
                    isUserInteracting = true;
                    clearTimeout(snapTimeout);
                    setTimeout(() => { 
                        isUserInteracting = false; 
                        scheduleSnap(); 
                    }, 150);
                }, { passive: true });

                dashboardContainer.addEventListener('touchstart', () => {
                    isUserInteracting = true;
                    clearTimeout(snapTimeout);
                }, { passive: true });

                dashboardContainer.addEventListener('touchend', () => {
                    isUserInteracting = false;
                    scheduleSnap();
                }, { passive: true });
            }

            // Parallax Camera Tracking (Mouse Movement) untuk Kedua Model 3D
            if (dashboardContainer) {
                dashboardContainer.addEventListener('mousemove', (e) => {
                    const centerX = window.innerWidth / 2;
                    const centerY = window.innerHeight / 2;
                    
                    const rotateY = ((e.clientX - centerX) / (window.innerWidth / 2)) * 18; 
                    const rotateX = -((e.clientY - centerY) / (window.innerHeight / 2)) * 18;

                    const orbitAzimuth = 90 + (rotateY * 0.6); 
                    const orbitElevation = 85 - (rotateX * 0.5); 
                    const orbitVal = `${orbitAzimuth}deg ${orbitElevation}deg 100%`;

                    if (modelOuter) modelOuter.cameraOrbit = orbitVal;
                    if (modelInner) modelInner.cameraOrbit = orbitVal;
                });
            }
        });
    </script>

    <script src="<?= base_url('assets/js/timepicker.js?v=' . filemtime(FCPATH . 'assets/js/timepicker.js')) ?>"></script>
    
    <!-- External Custom Script (info_ruangan.js harus paling atas agar toggleFullscreen() tersedia) -->
    <script src="<?= base_url('assets/js/info_ruangan.js?v=' . filemtime(FCPATH . 'assets/js/info_ruangan.js')) ?>"></script>

    <script>
        // Fungsi Scroll Dinamis ke Sesi Berikutnya
        function scrollToNextSection() {
            const vh = window.innerHeight;
            const container = document.querySelector('.dashboard-container');
            const currentScroll = window.lenis ? window.lenis.scroll : container.scrollTop;
            
            // Hitung index section saat ini (pembulatan ke bawah dengan toleransi 10px)
            const currentIndex = Math.floor((currentScroll + 10) / vh);
            const totalSections = 5; // 0=Header, 1=Info, 2=Lab, 3=Berita, 4=Virtual, 5=Footer
            
            let nextIndex = currentIndex + 1;
            
            if (nextIndex > totalSections) {
                // Balik ke paling atas jika sudah di footer
                nextIndex = 0;
            }
            
            const targetScroll = nextIndex * vh;
            
            if (window.lenis) {
                window.lenis.scrollTo(targetScroll, { 
                    duration: 1.5, 
                    easing: (t) => 1 - Math.pow(1 - t, 4) 
                });
            } else {
                container.scrollTo({ top: targetScroll, behavior: 'smooth' });
            }
        }

        // Update arah panah (ke atas/bawah) secara realtime saat di-scroll
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('globalScrollBtn');
            const container = document.querySelector('.dashboard-container');
            
            function checkScrollPos() {
                const vh = window.innerHeight;
                const currentScroll = window.lenis ? window.lenis.scroll : container.scrollTop;
                const ratio = currentScroll / vh;
                
                // Jika sudah masuk area footer (di atas 4.5 dari total tinggi layar), ubah panah ke atas
                if (ratio > 4.5) {
                    btn.classList.add('pointing-up');
                    btn.title = "Kembali ke Atas";
                } else {
                    btn.classList.remove('pointing-up');
                    btn.title = "Scroll ke Sesi Selanjutnya";
                }
            }
            
            if (window.lenis) {
                window.lenis.on('scroll', checkScrollPos);
            } else {
                container.addEventListener('scroll', checkScrollPos);
            }
        });
    </script>

</body>
</html>
