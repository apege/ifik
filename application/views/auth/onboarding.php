<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($title) ? $title : 'Radial Orbital Onboarding — IK Labs Portal'; ?></title>

  <!-- Tailwind CSS v3 -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Google Font & JetBrains Mono -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

  <!-- Leaflet Map for Interactive Address Pinning -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

  <!-- Orbital Custom Stylesheet -->
  <link rel="stylesheet" href="<?= base_url('assets/css/onboarding.css'); ?>?v=<?= time(); ?>">
</head>
<body class="bg-slate-50 text-slate-900 antialiased overflow-hidden">

  <!-- Full Viewport Cosmic Orbital Container -->
  <div class="orbital-viewport">
    
    <!-- 3D Tubes Cursor Canvas (Behind all UI elements) -->
    <canvas id="tubesCanvas" class="fixed inset-0 pointer-events-none z-0"></canvas>

    <div class="cosmic-grid"></div>

    <!-- Top Floating Navbar -->
    <header class="orbital-topbar">
      <div class="brand-badge">
        <img src="<?= base_url('assets/images/logo-dummy.webp'); ?>" alt="Logo IFIK" class="brand-logo">
        <span class="brand-text">IK Labs Portal</span>
        <span class="brand-tag">v2.4 Core</span>
      </div>

      <!-- Linear Step Progress Indicator -->
      <nav class="orbital-stepper-bar" id="stepperNav">
        <button type="button" class="step-nav-item active" data-step="1">
          <span class="step-num">1</span>
          <span class="step-text">Ganti Password</span>
        </button>
        <div class="step-nav-line"></div>
        <button type="button" class="step-nav-item" data-step="2">
          <span class="step-num">2</span>
          <span class="step-text"><?= !empty($is_dosen) ? 'Identitas Dosen' : 'Identitas Mahasiswa'; ?></span>
        </button>
        <div class="step-nav-line"></div>
        <button type="button" class="step-nav-item" data-step="3">
          <span class="step-num">3</span>
          <span class="step-text">Kelahiran & Domisili</span>
        </button>
        <div class="step-nav-line"></div>
        <button type="button" class="step-nav-item" data-step="4">
          <span class="step-num">4</span>
          <span class="step-text"><?= !empty($is_dosen) ? 'Program Studi' : 'Akademik & Dosen Wali'; ?></span>
        </button>
      </nav>
    </header>

    <!-- Main Radial Orbital Stage Wrapper -->
    <main class="orbital-stage-wrapper">
      <div class="orbital-stage" id="orbitalStage">
        
        <!-- Central Glowing Cosmic Singularity Core -->
        <div class="core-singularity">
          <div class="core-ping-1"></div>
          <div class="core-ping-2"></div>
          <div class="core-inner-dot"></div>
        </div>

        <!-- Concentric Orbital Path Rings -->
        <div class="orbit-ring"></div>

        <!-- Orbital nodes are rendered dynamically via onboarding.js -->

      </div>
    </main>

    <!-- Footer System Status -->
    <footer class="absolute bottom-6 left-0 right-0 flex items-center justify-between px-8 text-xs text-slate-500 pointer-events-none">
      <div class="flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
        <span>Aktivasi Akun Mahasiswa Terproteksi</span>
      </div>
      <div class="font-mono">Fakultas Industri Kreatif &bull; Telkom University</div>
    </footer>

  </div>

  <!-- Dynamic Database Data Injection for JS -->
  <script>
    window.ONBOARDING_INITIAL_DATA = {
      base_url: <?= json_encode(base_url()); ?>,
      save_url: <?= json_encode(base_url('onboarding/process_biodata')); ?>,
      dashboard_url: <?= json_encode(base_url('dashboard')); ?>,
      is_dosen: <?= json_encode(!empty($is_dosen)); ?>,
      role_name: <?= json_encode(!empty($role_name) ? $role_name : 'mahasiswa'); ?>,
      nim: <?= json_encode(!empty($nim) ? $nim : '130210091'); ?>,
      nama_depan: <?= json_encode(!empty($nama_depan) ? $nama_depan : 'Indah'); ?>,
      nama_belakang: <?= json_encode(!empty($nama_belakang) ? $nama_belakang : 'Permatasari'); ?>,
      dosen_list: <?= json_encode(!empty($dosen_wali_list) ? $dosen_wali_list : []); ?>,
      konsentrasi_list: <?= json_encode(!empty($konsentrasi_list) ? $konsentrasi_list : []); ?>
    };
  </script>

  <!-- JavaScript Kinematics & Interaction -->
  <script src="<?= base_url('assets/js/onboarding.js'); ?>?v=<?= time(); ?>"></script>

  <!-- 3D Tubes Cursor Initialization (Exact Commit 9447ba9) -->
  <script type="module">
    document.addEventListener('DOMContentLoaded', () => {
      setTimeout(() => {
        import('https://cdn.jsdelivr.net/npm/threejs-components@0.0.19/build/cursors/tubes1.min.js')
          .then(module => {
            const TubesCursor = module.default;
            const canvas = document.getElementById('tubesCanvas');
            if (canvas) {
              const app = TubesCursor(canvas, {
                bloom: false,
                tubes: {
                  colors: ["#ff5500", "#ff7700", "#ffa000", "#ff3300"],
                  lights: {
                    intensity: 150,
                    colors: ["#ffffff", "#ffaa00", "#ffffff", "#ff6600"]
                  }
                }
              });
              window.tubesApp = app;
            }
          })
          .catch(err => console.error("Failed to load TubesCursor module:", err));
      }, 100);
    });
  </script>
</body>
</html>
