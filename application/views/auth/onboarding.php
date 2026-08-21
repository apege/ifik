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

  <!-- Orbital Custom Stylesheet -->
  <link rel="stylesheet" href="<?= base_url('assets/css/onboarding.css'); ?>?v=<?= time(); ?>">
</head>
<body class="bg-black text-white antialiased overflow-hidden select-none">

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

      <div class="orbit-controls">
        <button type="button" id="toggleAutoRotate" class="control-pill-btn">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          Auto Rotate
        </button>
      </div>
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
        <div class="orbit-ring-outer"></div>

        <!-- Orbital nodes are rendered dynamically via onboarding.js -->

      </div>
    </main>

    <!-- Footer System Status -->
    <footer class="absolute bottom-6 left-0 right-0 flex items-center justify-between px-8 text-xs text-white/40 pointer-events-none">
      <div class="flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
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

  <!-- 3D Tubes Cursor Initialization -->
  <script type="module">
    document.addEventListener('DOMContentLoaded', () => {
      setTimeout(() => {
        import('https://cdn.jsdelivr.net/npm/threejs-components@0.0.19/build/cursors/tubes1.min.js')
          .then(module => {
            const TubesCursor = module.default;
            const canvas = document.getElementById('tubesCanvas');
            if (canvas) {
              const app = TubesCursor(canvas, {
                tubes: {
                  colors: ["#5e72e4", "#8965e0", "#f5365c"],
                  lights: {
                    intensity: 200,
                    colors: ["#21d4fd", "#b721ff", "#f4d03f", "#11cdef"]
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
