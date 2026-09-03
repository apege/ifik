<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lupa Password — IK Labs Portal</title>
  <meta name="description" content="Reset password IK Labs Portal — Fakultas Industri Kreatif, Telkom University.">

  <!-- Tailwind CSS v3 -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] }
        }
      }
    }
  </script>

  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/css/login.css'); ?>?v=<?= time(); ?>">
</head>

<body>

  <!-- ========== BACKGROUND ========== -->
  <div class="bg-layer" id="bgLayer">
    <img src="<?= base_url('assets/images/background.png'); ?>" alt="" class="bg-img" id="bgImg">
    <div class="bg-overlay"></div>
  </div>

  <!-- ========== FLOATING CSS SPHERES ========== -->
  <div class="sphere-layer" id="sphereLayer">
    <div class="sph sph-xl" style="left:3%;  top:55%; --dur:8s; --delay:-2s; background:radial-gradient(circle at 35% 30%,#fff9e6,#f59e0b);"></div>
    <div class="sph sph-lg" style="right:6%; top:8%;  --dur:7s; --delay:-1s; background:radial-gradient(circle at 35% 30%,#fff,#fbbf24);"></div>
    <div class="sph sph-md" style="left:28%;bottom:6%;--dur:5s; --delay:-3s; background:radial-gradient(circle at 35% 30%,#fef3c7,#d97706);"></div>
    <div class="sph sph-sm" style="left:12%;top:18%; --dur:6s; --delay:-0.5s;background:radial-gradient(circle at 35% 30%,#fffbeb,#f59e0b);"></div>
    <div class="sph sph-xs" style="right:20%;bottom:28%;--dur:4s;--delay:-1.5s;background:radial-gradient(circle at 35% 30%,#fff,#fde68a);"></div>
    <div class="sph sph-xs" style="left:52%;top:12%; --dur:5s; --delay:-2.5s;background:radial-gradient(circle at 35% 30%,#fff,#b45309);"></div>
    <div class="sph sph-sm" style="right:33%;top:68%;--dur:7s; --delay:-0.8s;background:radial-gradient(circle at 35% 30%,#fef3c7,#92400e);"></div>
  </div>

  <!-- ========== MAIN LAYOUT ========== -->
  <div class="page-wrap">

    <!-- LEFT HERO TEXT -->
    <div class="hero-text" id="heroText">
      <img src="<?= base_url('assets/images/logo-dummy.webp'); ?>" alt="Logo IFIK" class="hero-logo-img">
      <h1 class="hero-title">Reset your<br>password<br>here.</h1>
      <p class="hero-sub">Masukkan email kamu untuk menerima tautan pemulihan kata sandi.</p>
    </div>

    <!-- RIGHT — HANGING CARD WITH ROPE -->
    <div class="hang-root" id="hangRoot">

      <!-- Rope extension going up off-screen -->
      <div class="rope-extension"></div>

      <!-- Rope Knot & Metallic Silver Ring SVG -->
      <div class="rope-ring-wrapper">
        <svg class="rope-ring-svg" viewBox="0 0 80 160" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <!-- Braided Rope Pattern -->
            <pattern id="ropePattern" width="8" height="8" patternUnits="userSpaceOnUse" patternTransform="rotate(45)">
              <rect width="4" height="8" fill="#8a5a36"/>
              <rect x="4" width="4" height="8" fill="#b88358"/>
            </pattern>

            <!-- Chrome Silver Metallic Gradient -->
            <linearGradient id="chromeMetal" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" stop-color="#444444"/>
              <stop offset="20%" stop-color="#c5c5c5"/>
              <stop offset="45%" stop-color="#ffffff"/>
              <stop offset="70%" stop-color="#a0a0a0"/>
              <stop offset="100%" stop-color="#333333"/>
            </linearGradient>

            <filter id="shadow" x="-30%" y="-30%" width="160%" height="160%">
              <feDropShadow dx="2" dy="4" stdDeviation="3" flood-color="rgba(0,0,0,0.4)"/>
            </filter>
          </defs>

          <!-- Vertical Rope Strand -->
          <rect x="36" y="0" width="8" height="95" fill="url(#ropePattern)" filter="url(#shadow)"/>
          <line x1="38" y1="0" x2="38" y2="95" stroke="rgba(255,255,255,0.3)" stroke-width="1.2"/>

          <!-- Rope Knot around top of the Ring -->
          <g filter="url(#shadow)">
            <ellipse cx="40" cy="93" rx="9" ry="5.5" fill="#8a5a36"/>
            <ellipse cx="40" cy="98" rx="11" ry="6" fill="#6d4228"/>
            <ellipse cx="40" cy="102" rx="9" ry="4.5" fill="#52301a"/>
          </g>

          <!-- Teardrop Silver Ring / Loop (connected to card cap) -->
          <g filter="url(#shadow)">
            <!-- Outer Loop Path -->
            <path d="M 40 98 
                     C 25 98, 23 118, 33 133 
                     C 37 140, 37 148, 37 158 
                     L 43 158 
                     C 43 148, 43 140, 47 133 
                     C 57 118, 55 98, 40 98 Z" 
                  fill="url(#chromeMetal)" />
            
            <!-- Inner Cutout / Hole -->
            <path d="M 40 104 
                     C 30 104, 29 116, 37 127 
                     C 39 130, 39 144, 39 158 
                     L 41 158 
                     C 41 144, 41 130, 43 127 
                     C 51 116, 50 104, 40 104 Z" 
                  fill="#78350f" />

            <!-- Metallic Highlight overlay -->
            <path d="M 40 99 C 27 99, 25 117, 34 132" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="1.5" stroke-linecap="round"/>
          </g>
        </svg>
      </div>

      <!-- THE HANGING CARD -->
      <div class="login-card-container" id="loginCard">
        
        <!-- Glowing Scanner Beam (Active during loading) -->
        <div class="scanner-beam" id="scannerBeam"></div>

        <!-- Premium Metallic Top Cap (Horizontal Bar with Hole) -->
        <div class="card-metallic-cap">
          <div class="cap-progress-bar" id="capProgressBar"></div>
          <div class="cap-reflection"></div>
          <div class="cap-hole-wrapper">
            <div class="cap-hole"></div>
          </div>
        </div>

        <!-- Card Body Content -->
        <div class="card-body">
          <!-- Brand Logo -->
          <div class="card-brand">
            <img src="<?= base_url('assets/images/logo-dummy.webp'); ?>" alt="Logo IFIK" class="brand-logo-img">
          </div>

          <h2 class="card-title">Lupa password?</h2>
          <p class="card-sub">Masukkan email Telkom University kamu</p>

          <!-- Flash Error -->
          <?php if (isset($error) || $this->session->flashdata('error')): ?>
          <div class="flash-error">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span><?= isset($error) ? $error : $this->session->flashdata('error'); ?></span>
          </div>
          <?php endif; ?>

          <!-- Flash Success -->
          <?php if (isset($success) || $this->session->flashdata('success')): ?>
          <div class="flash-success">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            <span><?= isset($success) ? $success : $this->session->flashdata('success'); ?></span>
          </div>
          <?php endif; ?>

          <!-- Forgot Password Form -->
          <form action="<?= base_url('login/send_reset_link'); ?>" method="POST" id="forgotForm">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

            <!-- Email Input -->
            <div class="field-group">
              <label class="field-label">Email</label>
              <div class="field-wrap" id="emailWrapper">
                <svg class="field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                <input type="text" id="identityInput" name="email" required
                       placeholder="contoh: nama@telkomuniversity.ac.id atau email Anda"
                       class="field-input">
              </div>
              <p id="emailHint" class="field-hint"></p>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="submitBtn" class="btn-submit">Kirim Link Reset &rarr;</button>

            <p class="forgot-link"><a href="<?= base_url('login'); ?>">&larr; Kembali ke Login</a></p>
          </form>
        </div>

      </div><!-- /login-card-container -->
    </div><!-- /hang-root -->

  </div><!-- /page-wrap -->

  <p class="footer-copy">&copy; 2025 Fakultas Industri Kreatif &mdash; Telkom University</p>

  <script src="<?= base_url('assets/js/forgot_password.js'); ?>?v=<?= time(); ?>"></script>
</body>
</html>
