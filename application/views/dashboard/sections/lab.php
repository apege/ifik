<style>
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

    /* Styling Sesi Laboratorium Fakultas (Pergeseran Super Lambat 1.6s Ultra Silk-Smooth) */
    /* Styling Sesi Laboratorium Fakultas (Full-Screen Room Photo Mode) */
    #section-lab {
        width: 100vw;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
        z-index: 2;
        background: #000000;
    }

    .lab-container {
        margin: 0;
        width: 100vw;
        height: 100vh;
        max-width: 100vw;
        max-height: 100vh;
        padding: 0;
        z-index: 2;
        background: transparent;
        border: none;
        border-radius: 0;
        backdrop-filter: none;
        -webkit-backdrop-filter: none;
        box-shadow: none;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .lab-header {
        position: absolute;
        top: 85px;
        left: 70px;
        z-index: 25;
        pointer-events: none;
    }

    .lab-header h1 {
        font-size: 2.3rem;
        font-weight: 800;
        color: #ffffff;
        letter-spacing: -0.5px;
        text-transform: uppercase;
        margin-bottom: 4px;
        text-shadow: 0 4px 18px rgba(0, 0, 0, 0.85);
    }

    .lab-header p {
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.98rem;
        font-weight: 600;
        text-shadow: 0 2px 12px rgba(0, 0, 0, 0.85);
    }

    /* Viewport Container (Full 100vw x 100vh) */
    .lab-viewport {
        width: 100vw;
        height: 100vh;
        overflow: hidden;
        position: absolute;
        top: 0;
        left: 0;
        padding: 0;
        cursor: grab;
        user-select: none;
        touch-action: pan-y;
    }

    .lab-viewport.active-drag {
        cursor: grabbing;
    }

    /* Track Pergeseran Full Photo */
    .lab-track {
        display: flex;
        gap: 0px;
        align-items: center;
        height: 100vh;
        transition: transform 1.0s cubic-bezier(0.25, 1, 0.35, 1);
        will-change: transform;
    }

    /* Card Fullscreen 100vw x 100vh */
    .lab-card {
        flex: 0 0 100vw;
        width: 100vw;
        height: 100vh;
        max-width: 100vw;
        border-radius: 0;
        border: none;
        box-shadow: none;
        position: relative;
        overflow: hidden;
        background: #0f172a;
        opacity: 1 !important;
        transform: translateZ(0);
        will-change: transform;
        cursor: pointer;
    }

    .lab-card.active-card {
        opacity: 1 !important;
    }

    /* Foto Full Screen */
    .lab-card-bg-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        object-fit: cover;
        z-index: 1;
        transform: none !important;
        filter: brightness(0.8) !important;
        pointer-events: none;
    }

    /* Gradient Overlay pada Foto Fullscreen */
    .lab-card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 2;
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.65) 0%, rgba(15, 23, 42, 0.1) 45%, rgba(15, 23, 42, 0.88) 100%);
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 0 70px 140px 70px;
    }

    /* Content Flat Clean */
    .lab-card-top-content {
        max-width: 600px;
        text-align: left;
        transform: none !important;
        opacity: 1 !important;
    }

    .lab-card-title-text {
        font-size: 2.2rem;
        font-weight: 800;
        color: #ffffff;
        margin: 10px 0 10px 0;
        letter-spacing: -0.5px;
        line-height: 1.2;
        text-shadow: 0 4px 15px rgba(0, 0, 0, 0.8);
    }

    .lab-card-desc-text {
        color: rgba(255, 255, 255, 0.95);
        font-size: 1.05rem;
        line-height: 1.5;
        font-weight: 500;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.8);
    }

    /* 3D Model Tag */
    .badge-3d-tag-overlay {
        display: inline-flex;
        width: fit-content;
        align-items: center;
        background: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: #ffffff;
        font-size: 0.82rem;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 20px;
        gap: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    }

    .badge-3d-tag-overlay svg {
        width: 14px;
        height: 14px;
        fill: #f97316;
    }

    /* Bottom Action Row & Button */
    .lab-card-bottom-row {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        margin-top: 20px;
        width: 100%;
    }

    .btn-apple-action {
        background: #ffffff;
        color: #0f172a;
        font-weight: 800;
        font-size: 0.95rem;
        padding: 12px 28px;
        border-radius: 30px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        transition: all 0.3s ease;
    }

    .btn-apple-action:hover {
        background: #ea580c;
        color: #ffffff;
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(234, 88, 12, 0.5);
    }

    .btn-apple-secondary {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.35);
        color: #ffffff;
        font-weight: 700;
        font-size: 0.92rem;
        padding: 12px 22px;
        border-radius: 30px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-apple-secondary:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: #ffffff;
        color: #ffffff;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(255, 255, 255, 0.25);
    }

    /* Carousel Indicators Desain Fasilitas untuk Laboratorium Seekbar */
    .lab-indicators-container {
        position: absolute;
        bottom: 45px;
        left: 50%;
        transform: translateX(-50%);
        width: 65vw;
        max-width: 900px;
        display: flex;
        align-items: center;
        gap: 20px;
        z-index: 30;
    }

    .lab-indicators-list {
        flex: 1;
        display: flex;
        justify-content: space-between;
        gap: 15px;
    }

    .lab-indicators-list .dot {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 6px;
        cursor: pointer;
        opacity: 0.5;
        transition: all 0.3s ease;
    }

    .lab-indicators-list .dot.active, 
    .lab-indicators-list .dot:hover {
        opacity: 1;
    }

    .lab-indicators-list .dot .dot-label {
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #ffffff;
        letter-spacing: 1px;
        white-space: nowrap;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.8);
    }

    .lab-indicators-list .dot .dot-track {
        height: 4px;
        width: 100%;
        border-radius: 4px;
        background: rgba(255, 255, 255, 0.3);
        position: relative;
        overflow: hidden;
    }

    .lab-indicators-list .dot .progress {
        position: absolute;
        top: 0; left: 0; height: 100%;
        background: #ffffff;
        width: 0%;
        border-radius: 4px;
        box-shadow: 0 0 8px #ffffff, 0 0 15px rgba(255,255,255,0.8);
    }

    .lab-play-pause-btn {
        width: 42px;
        height: 42px;
        min-width: 42px;
        border-radius: 50%;
        background: #ea580c;
        border: none;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(234, 88, 12, 0.5);
    }

    .lab-play-pause-btn:hover {
        background: #ffffff;
        color: #ea580c;
        transform: scale(1.12);
        box-shadow: 0 6px 20px rgba(255, 255, 255, 0.6);
    }

    .lab-play-pause-btn svg {
        width: 18px;
        height: 18px;
        fill: currentColor;
    }

    .apple-indicator-dots {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .apple-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.35);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition: width 2.5s cubic-bezier(0.25, 1, 0.35, 1), background-color 0.6s ease, border-color 0.6s ease;
    }

    .apple-dot:hover {
        background: rgba(255, 255, 255, 0.6);
    }

    /* Active Pill Bar Expands to 38px Seek Progress Track */
    .apple-dot.active {
        width: 38px;
        height: 8px;
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(249, 115, 22, 0.4);
    }

    /* Inside Filler Animation (Progres Oranye Tepat 6 Detik) */
    .apple-dot-fill {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 0%;
        background: #f97316;
        background: linear-gradient(135deg, #ff8c00 0%, #ea580c 100%);
        border-radius: 6px;
        box-shadow: 0 0 10px rgba(249, 115, 22, 0.8);
        pointer-events: none;
    }

    .apple-dot.active .apple-dot-fill.animating {
        animation: appleSeekProgress 6s linear forwards;
    }

    @keyframes appleSeekProgress {
        from { width: 0%; }
        to { width: 100%; }
    }

    /* Play / Pause Toggle Button */
    .apple-play-pause-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(30, 41, 59, 0.45);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        transition: transform 0.25s ease, background-color 0.25s ease, border-color 0.25s ease;
    }

    .apple-play-pause-btn svg {
        width: 15px;
        height: 15px;
        fill: #ffffff;
        transition: fill 0.25s ease;
    }

    .apple-play-pause-btn:hover {
        background: #ea580c;
        border-color: rgba(234, 88, 12, 0.6);
        transform: scale(1.08);
    }

    /* Premium Glassmorphic Left/Right Navigation Arrow Buttons */
    .lab-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
        transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1), background-color 0.3s ease, box-shadow 0.3s ease;
    }

    .lab-nav-btn svg {
        width: 24px;
        height: 24px;
        fill: #ffffff;
        transition: transform 0.25s ease, fill 0.25s ease;
    }

    .lab-nav-btn.prev-btn {
        left: 20px;
    }

    .lab-nav-btn.next-btn {
        right: 20px;
    }

    .lab-nav-btn:hover {
        background: #ea580c;
        border-color: rgba(234, 88, 12, 0.6);
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 10px 30px rgba(234, 88, 12, 0.5);
    }

    .lab-nav-btn:active {
        transform: translateY(-50%) scale(0.95);
    }

    @media (max-width: 768px) {
        .lab-card {
            flex: 0 0 88vw;
            height: 360px;
        }
        .lab-card-title-text {
            font-size: 1.25rem;
        }
        .lab-nav-btn {
            width: 40px;
            height: 40px;
        }
        .lab-nav-btn.prev-btn {
            left: 8px;
        }
        .lab-nav-btn.next-btn {
            right: 8px;
        }
    }
</style>

<!-- Sesi: Laboratorium Fakultas (Pergeseran Super Lambat 2.5s & Ultra Silk-Smooth) -->
<div class="section-wrapper" id="section-lab">
    <div class="lab-container">
        
        <div class="lab-header">
            <div>
                <h1>LABORATORIUM FAKULTAS</h1>
                <p>Fasilitas laboratorium di fakultas industri kreatif</p>
            </div>
        </div>

        <div class="lab-viewport" id="labViewport">
            <!-- Tombol Navigasi Kiri & Kanan -->
            <button class="lab-nav-btn prev-btn" id="labPrevBtn" aria-label="Slide Kiri" title="Foto Sebelumnya">
                <svg viewBox="0 0 24 24"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
            </button>
            <button class="lab-nav-btn next-btn" id="labNextBtn" aria-label="Slide Kanan" title="Foto Selanjutnya">
                <svg viewBox="0 0 24 24"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
            </button>

            <!-- 7 Slot Kartu Flat Presisi Tengah -->
            <div class="lab-track" id="labTrack">
                <div class="lab-card" id="cardSlot0"></div>
                <div class="lab-card" id="cardSlot1"></div>
                <div class="lab-card" id="cardSlot2"></div>
                <div class="lab-card active-card" id="cardSlot3"></div>
                <div class="lab-card" id="cardSlot4"></div>
                <div class="lab-card" id="cardSlot5"></div>
                <div class="lab-card" id="cardSlot6"></div>
            </div>
        </div>



    </div>
</div>

<script>
    <?php
        $featured_keys = ['multimedia', 'aula', 'cintiq', 'greenscreen', 'incubator', 'mac'];
        $dyn_lab_data = [];
        $seen_keys = [];

        if (!empty($ruangan)) {
            foreach ($ruangan as $r) {
                $name = strtolower(trim(isset($r->nama_ruangan) ? $r->nama_ruangan : ''));
                $code = strtolower(trim(isset($r->kode_ruangan) ? $r->kode_ruangan : ''));

                if ($name === 'ss' || $code === 'ss' || strpos($name, 'test') !== false || strpos($name, 'qqq') !== false) continue;

                $key = '';
                if (strpos($name, 'multimedia') !== false && !in_array('multimedia', $seen_keys)) $key = 'multimedia';
                elseif (strpos($name, 'aula') !== false && !in_array('aula', $seen_keys)) $key = 'aula';
                elseif ((strpos($name, 'cintiq') !== false || strpos($name, 'tablet') !== false || strpos($name, 'sablon') !== false) && !in_array('cintiq', $seen_keys)) $key = 'cintiq';
                elseif (strpos($name, 'green') !== false && !in_array('greenscreen', $seen_keys)) $key = 'greenscreen';
                elseif ((strpos($name, 'inkubator') !== false || strpos($name, 'incubator') !== false) && !in_array('incubator', $seen_keys)) $key = 'incubator';
                elseif (strpos($name, 'mac') !== false && !in_array('mac', $seen_keys)) $key = 'mac';
                else {
                    $key = preg_replace('/[^a-z0-9]/', '', $code);
                    if (empty($key)) $key = 'room_' . $r->id;
                }

                if (!empty($key) && !isset($dyn_lab_data[$key])) {
                    $seen_keys[] = $key;
                    $default_img = base_url('assets/images/multimedia.jpg');
                    if ($key === 'aula') $default_img = file_exists(FCPATH . 'assets/images/Aula1.jpg') ? base_url('assets/images/Aula1.jpg') : base_url('assets/images/aula.jpg');
                    elseif ($key === 'cintiq') $default_img = file_exists(FCPATH . 'assets/images/sintiq.jpg') ? base_url('assets/images/sintiq.jpg') : base_url('assets/images/cintiq.jpg');
                    elseif ($key === 'greenscreen') $default_img = base_url('assets/images/greenscreen.jpg');
                    elseif ($key === 'incubator') $default_img = base_url('assets/images/incubator.jpg');
                    elseif ($key === 'mac') $default_img = base_url('assets/images/mac.jpg');

                    $detail_url = site_url('dashboard/lab_detail/' . $key);

                    $dyn_lab_data[$key] = [
                        'key'     => $key,
                        'id'      => $r->id,
                        'title'   => $r->nama_ruangan,
                        'desc'    => !empty($r->tagline) ? $r->tagline : (!empty($r->deskripsi) ? substr($r->deskripsi, 0, 95) . '...' : 'Fasilitas Laboratorium Fakultas Industri Kreatif'),
                        'btnText' => 'Lihat Detail &rarr;',
                        'url'     => $detail_url,
                        'img'     => !empty($r->foto) ? (strpos($r->foto, 'http') === 0 ? $r->foto : base_url($r->foto)) : $default_img
                    ];
                }
            }
        }

        // Sort: 6 Lab Utama di depan, lalu diikuti seluruh ruangan lainnya
        uksort($dyn_lab_data, function($k1, $k2) use ($dyn_lab_data, $featured_keys) {
            $posA = array_search($k1, $featured_keys);
            $posB = array_search($k2, $featured_keys);
            if ($posA !== false && $posB !== false) return $posA - $posB;
            if ($posA !== false) return -1;
            if ($posB !== false) return 1;
            $idA = $dyn_lab_data[$k1]['id'] ?? 0;
            $idB = $dyn_lab_data[$k2]['id'] ?? 0;
            return $idA - $idB;
        });

        $dyn_lab_keys = array_keys($dyn_lab_data);
        $total_labs_count = count($dyn_lab_keys);
        $split_point_lab = !empty($dyn_lab_keys) ? (int)ceil($total_labs_count / 2) : 3;
    ?>

    <?php if (!empty($dyn_lab_data)): ?>
        const LAB_DATA = <?= json_encode($dyn_lab_data) ?>;
        const LAB_KEYS = <?= json_encode(array_values($dyn_lab_keys)) ?>;
    <?php else: ?>
        const LAB_DATA = {
            multimedia: { key: 'multimedia', title: 'Lab Multimedia & Game', desc: '36 Workstation PC RTX GPU untuk animasi digital &amp; 3D modelling.', btnText: 'Lihat Lab &rarr;', url: '<?= site_url('dashboard/lab_detail/multimedia') ?>', img: '<?= base_url('assets/images/multimedia.jpg') ?>' },
            aula: { key: 'aula', title: 'Aula Utama Fakultas', desc: 'Kapasitas 300+ orang dengan Sound System pro &amp; Stage LED.', btnText: 'Lihat Aula &rarr;', url: '<?= site_url('dashboard/lab_detail/aula') ?>', img: '<?= file_exists(FCPATH . 'assets/images/Aula1.jpg') ? base_url('assets/images/Aula1.jpg') : base_url('assets/images/aula.jpg') ?>' },
            cintiq: { key: 'cintiq', title: 'Lab Tablet Cintiq', desc: 'Studio Wacom Cintiq Pro 8K Pen Display untuk komik &amp; 2D art.', btnText: 'Lihat Lab &rarr;', url: '<?= site_url('dashboard/lab_detail/cintiq') ?>', img: '<?= file_exists(FCPATH . 'assets/images/sintiq.jpg') ? base_url('assets/images/sintiq.jpg') : base_url('assets/images/cintiq.jpg') ?>' },
            greenscreen: { key: 'greenscreen', title: 'Lab Green Screen Studio', desc: 'Dinding Cyclorama Chroma Key &amp; Lighting Rig DMX.', btnText: 'Lihat Lab &rarr;', url: '<?= site_url('dashboard/lab_detail/greenscreen') ?>', img: '<?= base_url('assets/images/greenscreen.jpg') ?>' },
            incubator: { key: 'incubator', title: 'Lab Inkubator Bisnis & Tech', desc: 'Ruang Pitching Investor, Co-Working Space, &amp; Wifi 6E.', btnText: 'Lihat Lab &rarr;', url: '<?= site_url('dashboard/lab_detail/incubator') ?>', img: '<?= base_url('assets/images/incubator.jpg') ?>' },
            mac: { key: 'mac', title: 'Lab Workstation Apple Mac', desc: 'Apple Mac Studio M2 Max &amp; Studio Display Retina 5K.', btnText: 'Lihat Lab &rarr;', url: '<?= site_url('dashboard/lab_detail/mac') ?>', img: '<?= base_url('assets/images/mac.jpg') ?>' }
        };
        const LAB_KEYS = ['multimedia', 'aula', 'cintiq', 'greenscreen', 'incubator', 'mac'];
    <?php endif; ?>
    const TOTAL_LABS = LAB_KEYS.length;
    const SPLIT_POINT_LAB = <?= $split_point_lab ?>;
    const DEKANAT_IMG_URL = "<?= base_url('assets/images/' . (!empty($header_settings->dekanat_image) ? $header_settings->dekanat_image : 'dekanat2.png')) ?>";
    let activeLabIndex = 0; // Mulai dari Multimedia (Indeks 0)

    let isMoving = false;
    let isDragging = false;
    let isPlaying = false;
    let startX = 0;
    let dragOffset = 0;
    let baseTargetX = 0;
    const CENTER_SLOT_INDEX = 3; // Slot DOM Index 3 SELALU kartu aktif pas di tengah

    function buildCardHTML(labKey) {
        const data = LAB_DATA[labKey];
        if (!data) return '';
        return `
            <img src="${data.img}" alt="${data.title}" class="lab-card-bg-img">
            <div class="lab-card-overlay">
                <div class="lab-card-top-content">
                    <span class="badge-3d-tag-overlay">
                        <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                        3D Model
                    </span>
                    <h3 class="lab-card-title-text" style="margin-top: 14px;">${data.title}</h3>
                    <div class="lab-card-desc-text">${data.desc}</div>
                </div>

                <div class="lab-card-bottom-row">
                    <div></div>
                    <a href="${data.url}" class="btn-apple-action">${data.btnText}</a>
                </div>
            </div>
            <img src="${DEKANAT_IMG_URL}" alt="Dekanat" class="dekanat-img-right">
        `;
    }

    let labProgEndListener = null;

    // UPDATE INDIKATOR CONTINUOUS TRACK & THUMB PROPORSIONAL
    function syncIndicators(targetIdx) {
        const totalCount = TOTAL_LABS;
        const splitPoint = SPLIT_POINT_LAB;

        const dotLeft = document.getElementById('dotFasilitasLeft');
        const dotRight = document.getElementById('dotFasilitasRight');
        const dotSingle = document.getElementById('dotFasilitas');

        const thumbLeft = document.getElementById('thumbFasilitasLeft');
        const thumbRight = document.getElementById('thumbFasilitasRight');
        const thumbFull = document.getElementById('thumbFasilitasFull');

        // Cleanup any active animation listener
        if (labProgEndListener) {
            const allProgs = document.querySelectorAll('.dot-track-continuous .thumb .progress');
            allProgs.forEach(p => p.removeEventListener('animationend', labProgEndListener));
            labProgEndListener = null;
        }

        let activeProgToAnimate = null;

        const counterLeft = document.getElementById('fasilitasCounterLeft');
        const counterRight = document.getElementById('fasilitasCounterRight');
        const counterFull = document.getElementById('fasilitasCounterFull');

        if (dotLeft && dotRight && thumbLeft && thumbRight) {
            const countLeft = splitPoint;
            const countRight = totalCount - splitPoint;

            // Ukuran thumb proporsional terhadap total data dalam part masing-masing
            const thumbWidthLeftPct = Math.max(8, (1 / countLeft) * 100);
            const thumbWidthRightPct = Math.max(8, (1 / countRight) * 100);

            thumbLeft.style.width = `${thumbWidthLeftPct}%`;
            thumbRight.style.width = `${thumbWidthRightPct}%`;

            const progLeft = thumbLeft.querySelector('.progress');
            const progRight = thumbRight.querySelector('.progress');

            if (targetIdx < splitPoint) {
                // Sayap Kiri Aktif
                dotLeft.classList.add('active');
                dotRight.classList.remove('active');

                if (counterLeft) {
                    counterLeft.textContent = `${String(targetIdx + 1).padStart(2, '0')}/${String(countLeft).padStart(2, '0')}`;
                }
                if (counterRight) {
                    counterRight.textContent = `01/${String(countRight).padStart(2, '0')}`;
                }

                const leftPos = (countLeft > 1) 
                    ? (targetIdx / (countLeft - 1)) * (100 - thumbWidthLeftPct) 
                    : 0;
                thumbLeft.style.left = `${leftPos}%`;
                thumbLeft.style.opacity = '1';

                // Sayap Kanan Belum Dilewati (0%)
                thumbRight.style.left = '0%';
                thumbRight.style.opacity = '0.35';
                if (progRight) {
                    progRight.style.animation = 'none';
                    progRight.style.width = '0%';
                }

                activeProgToAnimate = progLeft;
            } else {
                // Sayap Kanan Aktif
                dotLeft.classList.remove('active');
                dotRight.classList.add('active');

                if (counterLeft) {
                    counterLeft.textContent = `${String(countLeft).padStart(2, '0')}/${String(countLeft).padStart(2, '0')}`;
                }
                if (counterRight) {
                    counterRight.textContent = `${String(targetIdx - splitPoint + 1).padStart(2, '0')}/${String(countRight).padStart(2, '0')}`;
                }

                // Sayap Kiri Sudah Selesai (100%)
                thumbLeft.style.left = `${100 - thumbWidthLeftPct}%`;
                thumbLeft.style.opacity = '0.35';
                if (progLeft) {
                    progLeft.style.animation = 'none';
                    progLeft.style.width = '100%';
                }

                const rightIdx = targetIdx - splitPoint;
                const rightPos = (countRight > 1) 
                    ? (rightIdx / (countRight - 1)) * (100 - thumbWidthRightPct) 
                    : 0;
                thumbRight.style.left = `${rightPos}%`;
                thumbRight.style.opacity = '1';

                activeProgToAnimate = progRight;
            }
        } else if (thumbFull) {
            if (dotSingle) dotSingle.classList.add('active');

            if (counterFull) {
                counterFull.textContent = `${String(targetIdx + 1).padStart(2, '0')}/${String(totalCount).padStart(2, '0')}`;
            }

            // Ukuran thumb proporsional terhadap total seluruh data
            const thumbWidthPct = Math.max(6, (1 / totalCount) * 100);
            thumbFull.style.width = `${thumbWidthPct}%`;

            const pos = (totalCount > 1) 
                ? (targetIdx / (totalCount - 1)) * (100 - thumbWidthPct) 
                : 0;
            thumbFull.style.left = `${pos}%`;
            thumbFull.style.opacity = '1';

            activeProgToAnimate = thumbFull.querySelector('.progress');
        }

        if (activeProgToAnimate) {
            activeProgToAnimate.style.animation = 'none';
            void activeProgToAnimate.offsetWidth; // Force reflow

            if (isPlaying) {
                labProgEndListener = () => {
                    activeProgToAnimate.removeEventListener('animationend', labProgEndListener);
                    labProgEndListener = null;
                    if (isPlaying && !isMoving && !isDragging) {
                        if (targetIdx === TOTAL_LABS - 1) {
                            if (typeof window.goToSlide === 'function') {
                                window.goToSlide(2);
                            }
                        } else {
                            shiftNext();
                        }
                    }
                };
                activeProgToAnimate.addEventListener('animationend', labProgEndListener);
                activeProgToAnimate.style.animation = 'slideProgress 6.5s linear forwards';
                activeProgToAnimate.style.animationPlayState = 'running';
            } else {
                activeProgToAnimate.style.width = '100%';
            }
        }
    }

    function updateAllSlots(activeIdx) {
        const slots = [
            document.getElementById('cardSlot0'),
            document.getElementById('cardSlot1'),
            document.getElementById('cardSlot2'),
            document.getElementById('cardSlot3'),
            document.getElementById('cardSlot4'),
            document.getElementById('cardSlot5'),
            document.getElementById('cardSlot6')
        ];

        const relativeIndices = [-3, -2, -1, 0, 1, 2, 3];

        relativeIndices.forEach((rel, slotIdx) => {
            const computedLabIdx = (activeIdx + rel + TOTAL_LABS * 100) % TOTAL_LABS;
            const labKey = LAB_KEYS[computedLabIdx];
            const slotEl = slots[slotIdx];

            if (slotEl) {
                if (slotEl.getAttribute('data-lab') !== labKey) {
                    slotEl.setAttribute('data-lab', labKey);
                    slotEl.innerHTML = buildCardHTML(labKey);
                }
                slotEl.classList.toggle('active-card', slotIdx === CENTER_SLOT_INDEX);
            }
        });
    }

    function getCardGeometry() {
        const viewport = document.getElementById('labViewport');
        const cardWidth = viewport ? viewport.clientWidth : window.innerWidth;
        return { cardWidth: cardWidth, stepOffset: cardWidth };
    }

    function getTargetXForIndex(slotIndex = CENTER_SLOT_INDEX) {
        const viewport = document.getElementById('labViewport');
        const track = document.getElementById('labTrack');
        if (!viewport || !track) return 0;
        const cards = track.querySelectorAll('.lab-card');
        if (!cards.length) return 0;

        const { cardWidth, stepOffset } = getCardGeometry();
        const viewportWidth = viewport.clientWidth;
        return (viewportWidth / 2) - (slotIndex * stepOffset + cardWidth / 2);
    }

    function setTrackTransform(x, animate = true) {
        const track = document.getElementById('labTrack');
        const viewport = document.getElementById('labViewport');
        if (!track || !viewport) return;

        if (animate) {
            // PERGESERAN SMOOTH 1.0s ULTRA SILK-SMOOTH
            track.style.transition = 'transform 1.0s cubic-bezier(0.25, 1, 0.35, 1)';
        } else {
            track.style.transition = 'none';
        }

        track.style.transform = `translate3d(${x}px, 0, 0)`;

        const cards = track.querySelectorAll('.lab-card');
        const { cardWidth, stepOffset } = getCardGeometry();
        const viewportCenter = viewport.clientWidth / 2;

        let closestIndex = CENTER_SLOT_INDEX;
        let minDistance = Infinity;

        cards.forEach((card, idx) => {
            const cardCenterX = x + (idx * stepOffset + cardWidth / 2);
            const dist = Math.abs(viewportCenter - cardCenterX);
            if (dist < minDistance) {
                minDistance = dist;
                closestIndex = idx;
            }
        });

        cards.forEach((card, idx) => {
            card.classList.toggle('active-card', idx === closestIndex);
        });
    }

    function renderTrackPosition(slotIndex = CENTER_SLOT_INDEX, animate = true) {
        const targetX = getTargetXForIndex(slotIndex);
        setTrackTransform(targetX, animate);
    }

    function startLabSequence() {
        activeLabIndex = 0;
        isPlaying = true;
        isMoving = false;
        renderTrackPosition(CENTER_SLOT_INDEX, false);
        updateAllSlots(0);
        const playPauseIcon = document.getElementById('playPauseIcon');
        if (playPauseIcon) {
            playPauseIcon.innerHTML = `<path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>`; // Icon Pause
        }
        syncIndicators(0);
    }
    window.startLabSequence = startLabSequence;

    function startAutoPlay() {
        isPlaying = true;
        const playPauseIcon = document.getElementById('playPauseIcon');
        if (playPauseIcon) {
            playPauseIcon.innerHTML = `<path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>`; // Icon Pause
        }

        const activeThumbProg = document.querySelector('.dot.dot-fasilitas.active .dot-track-continuous .thumb .progress');
        if (activeThumbProg && activeThumbProg.style.animationPlayState === 'paused') {
            activeThumbProg.style.animationPlayState = 'running';
        } else {
            syncIndicators(activeLabIndex);
        }
    }
    window.startAutoPlay = startAutoPlay;

    function pauseAutoPlay() {
        isPlaying = false;
        const playPauseIcon = document.getElementById('playPauseIcon');
        if (playPauseIcon) {
            playPauseIcon.innerHTML = `<path d="M8 5v14l11-7z"/>`; // Icon Play
        }

        const activeThumbProg = document.querySelector('.dot.dot-fasilitas.active .dot-track-continuous .thumb .progress');
        if (activeThumbProg && activeThumbProg.style.animation && activeThumbProg.style.animation !== 'none') {
            activeThumbProg.style.animationPlayState = 'paused';
        }
    }
    window.pauseAutoPlay = pauseAutoPlay;

    function toggleAutoPlay() {
        if (typeof window.goToSlide === 'function') {
            const currentActiveDot = document.querySelector('#carouselDots .dot.active');
            const currentIdx = currentActiveDot ? parseInt(currentActiveDot.getAttribute('data-index') || '0') : 0;
            if (currentIdx !== 1) {
                window.goToSlide(1);
                startAutoPlay();
                return;
            }
        }

        if (isPlaying) {
            pauseAutoPlay();
        } else {
            startAutoPlay();
        }
    }

    // NAVIGASI SMOOTH UNTUK SETIAP PERPINDAHAN MANUAL ATAU OTOMATIS
    function navigateToIndex(targetIdx) {
        if (isMoving || targetIdx === activeLabIndex || targetIdx < 0 || targetIdx >= TOTAL_LABS) return;
        isMoving = true;
        const track = document.getElementById('labTrack');
        if (!track) {
            isMoving = false;
            return;
        }

        // Hitung jarak terdekat (forward/backward)
        let diffForward = (targetIdx - activeLabIndex + TOTAL_LABS) % TOTAL_LABS;
        let diffBackward = (activeLabIndex - targetIdx + TOTAL_LABS) % TOTAL_LABS;

        let targetSlotIndex;
        if (diffForward <= diffBackward) {
            targetSlotIndex = CENTER_SLOT_INDEX + diffForward;
        } else {
            targetSlotIndex = CENTER_SLOT_INDEX - diffBackward;
        }

        // Langsung update titik indikator & seek bar secara bersamaan
        syncIndicators(targetIdx);

        // Mulai meluncurkan track dengan animasi smooth
        renderTrackPosition(targetSlotIndex, true);

        const onEnd = (e) => {
            if (e.target !== track) return;
            track.removeEventListener('transitionend', onEnd);

            const cards = track.querySelectorAll('.lab-card');
            cards.forEach(c => c.style.transition = 'none');

            activeLabIndex = targetIdx;
            updateAllSlots(activeLabIndex);

            renderTrackPosition(CENTER_SLOT_INDEX, false);

            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    cards.forEach(c => c.style.transition = '');
                    isMoving = false;
                });
            });
        };

        track.addEventListener('transitionend', onEnd);
    }

    function shiftNext() {
        const nextLabIndex = (activeLabIndex + 1) % TOTAL_LABS;
        navigateToIndex(nextLabIndex);
    }

    function shiftPrev() {
        const prevLabIndex = (activeLabIndex - 1 + TOTAL_LABS) % TOTAL_LABS;
        navigateToIndex(prevLabIndex);
    }

    // 1:1 REAL-TIME PHYSICAL DRAG ENGINE
    function onDragStart(clientX) {
        if (isMoving) return;
        isDragging = true;
        startX = clientX;
        dragOffset = 0;
        baseTargetX = getTargetXForIndex(CENTER_SLOT_INDEX);
        const viewport = document.getElementById('labViewport');
        if (viewport) viewport.classList.add('active-drag');
    }

    function onDragMove(clientX) {
        if (!isDragging) return;
        dragOffset = clientX - startX;
        const currentX = baseTargetX + dragOffset;
        setTrackTransform(currentX, false);
    }

    function onDragEnd() {
        if (!isDragging) return;
        isDragging = false;
        const viewport = document.getElementById('labViewport');
        if (viewport) viewport.classList.remove('active-drag');

        if (dragOffset < -50) {
            shiftNext();
        } else if (dragOffset > 50) {
            shiftPrev();
        } else {
            renderTrackPosition(CENTER_SLOT_INDEX, true);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const track = document.getElementById('labTrack');
        const viewport = document.getElementById('labViewport');

        if (track && viewport) {
            updateAllSlots(activeLabIndex);
            renderTrackPosition(CENTER_SLOT_INDEX, false);

            window.addEventListener('resize', () => renderTrackPosition(CENTER_SLOT_INDEX, false));
            window.addEventListener('load', () => renderTrackPosition(CENTER_SLOT_INDEX, false));

            // POINTER EVENTS (1:1 Real-Time Mouse & Touchpad Dragging)
            viewport.addEventListener('pointerdown', (e) => {
                onDragStart(e.clientX);
            });

            window.addEventListener('pointermove', (e) => {
                onDragMove(e.clientX);
            });

            window.addEventListener('pointerup', () => {
                onDragEnd();
            });

            window.addEventListener('pointercancel', () => {
                onDragEnd();
            });

            // Trackpad horizontal wheel swipe
            let wheelThrottle = false;
            viewport.addEventListener('wheel', (e) => {
                if (Math.abs(e.deltaX) > Math.abs(e.deltaY) && Math.abs(e.deltaX) > 15) {
                    e.preventDefault();
                    if (!wheelThrottle && !isMoving) {
                        wheelThrottle = true;
                        if (e.deltaX > 0) {
                            shiftNext();
                        } else {
                            shiftPrev();
                        }
                        setTimeout(() => { wheelThrottle = false; }, 500);
                    }
                }
            }, { passive: false });

            // DRAG-TO-SCRUB & LIVE FLOATING TOOLTIP PADA CONTINUOUS PROGRESS TRACK
            document.querySelectorAll('.dot-track-continuous').forEach((track) => {
                const tooltip = track.querySelector('.fasilitas-scrub-tooltip');
                let isScrubbing = false;

                function updateScrub(clientX, commit = false) {
                    const rect = track.getBoundingClientRect();
                    const ratio = Math.max(0, Math.min(0.999, (clientX - rect.left) / rect.width));
                    const part = track.getAttribute('data-part');

                    let targetIdx = 0;
                    if (part === '1') {
                        const countLeft = SPLIT_POINT_LAB;
                        targetIdx = Math.min(countLeft - 1, Math.floor(ratio * countLeft));
                    } else if (part === '2') {
                        const countRight = TOTAL_LABS - SPLIT_POINT_LAB;
                        targetIdx = SPLIT_POINT_LAB + Math.min(countRight - 1, Math.floor(ratio * countRight));
                    } else {
                        targetIdx = Math.min(TOTAL_LABS - 1, Math.floor(ratio * TOTAL_LABS));
                    }

                    if (tooltip) {
                        tooltip.style.left = `${ratio * 100}%`;
                        const labKey = LAB_KEYS[targetIdx];
                        if (labKey && LAB_DATA[labKey]) {
                            tooltip.innerHTML = `<span>🏛️ ${LAB_DATA[labKey].title}</span>`;
                        }
                        tooltip.classList.add('visible');
                    }

                    if (commit) {
                        if (typeof window.goToSlide === 'function') {
                            window.goToSlide(1);
                        }
                        navigateToIndex(targetIdx);
                    }
                }

                track.addEventListener('pointerdown', (e) => {
                    e.stopPropagation();
                    e.preventDefault();
                    isScrubbing = true;
                    track.classList.add('is-dragging');
                    track.setPointerCapture(e.pointerId);
                    updateScrub(e.clientX, true);
                });

                track.addEventListener('pointermove', (e) => {
                    if (isScrubbing) {
                        updateScrub(e.clientX, true);
                    } else {
                        updateScrub(e.clientX, false);
                    }
                });

                track.addEventListener('pointerup', (e) => {
                    if (isScrubbing) {
                        isScrubbing = false;
                        track.classList.remove('is-dragging');
                        try { track.releasePointerCapture(e.pointerId); } catch(err) {}
                        if (tooltip) tooltip.classList.remove('visible');
                    }
                });

                track.addEventListener('pointercancel', (e) => {
                    if (isScrubbing) {
                        isScrubbing = false;
                        track.classList.remove('is-dragging');
                        if (tooltip) tooltip.classList.remove('visible');
                    }
                });

                track.addEventListener('pointerleave', () => {
                    if (!isScrubbing && tooltip) {
                        tooltip.classList.remove('visible');
                    }
                });
            });

            // Keyboard Arrow Navigation (Khusus saat di Slide Fasilitas)
            window.addEventListener('keydown', (e) => {
                if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName)) return;

                const activeDot = document.querySelector('#carouselDots .dot.active');
                const activeSlideIdx = activeDot ? parseInt(activeDot.getAttribute('data-index') || '0') : 0;

                if (activeSlideIdx === 1) { // Fasilitas slide
                    if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                        e.preventDefault();
                        shiftNext();
                    } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                        e.preventDefault();
                        shiftPrev();
                    } else if (e.key === ' ' || e.code === 'Space') {
                        e.preventDefault();
                        toggleAutoPlay();
                    }
                }
            });

            // Klik langsung pada kartu di samping untuk berpindah dengan animasi smooth
            document.querySelectorAll('.lab-card').forEach((card) => {
                card.addEventListener('click', (e) => {
                    if (e.target.closest('.btn-apple-action') || e.target.closest('.lab-nav-btn')) return;

                    const targetLab = card.getAttribute('data-lab');
                    const targetIdx = LAB_KEYS.indexOf(targetLab);
                    if (targetIdx !== -1 && targetIdx !== activeLabIndex) {
                        navigateToIndex(targetIdx);
                    }
                });
            });

            // Tombol Navigasi Kanan & Kiri
            const prevBtn = document.getElementById('labPrevBtn');
            const nextBtn = document.getElementById('labNextBtn');

            if (prevBtn) {
                prevBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    shiftPrev();
                });
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    shiftNext();
                });
            }

            // Tombol Auto Play / Pause
            const playPauseBtn = document.getElementById('labAutoPlayBtn');
            if (playPauseBtn) {
                playPauseBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    toggleAutoPlay();
                });
            }
        }
    });
</script>
