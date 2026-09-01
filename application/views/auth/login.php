<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — IK Labs Portal</title>
  <meta name="description" content="Masuk ke IK Labs Portal — Fakultas Industri Kreatif, Telkom University.">

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

  <!-- ========== MAIN LAYOUT ========== -->
  <div class="page-wrap">

    <!-- LEFT HERO TEXT -->
    <div class="hero-text" id="heroText">
      <img src="<?= base_url('assets/images/logo-dummy.webp'); ?>" alt="Logo IFIK" class="hero-logo-img">
      <h1 class="hero-title">Masuk dengan<br>Email Tel-U.</h1>
      <p class="hero-sub">Gunakan akun email resmi <span class="hero-highlight">@telkomuniversity.ac.id</span> atau <span class="hero-highlight">@student.telkomuniversity.ac.id</span> untuk mengakses layanan portal IFIK.</p>
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

          <p class="card-sub">Gunakan email Telkom University kamu</p>

          <!-- Flash Error -->
          <?php if ($this->session->flashdata('error')): ?>
          <div class="flash-error">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span><?= $this->session->flashdata('error'); ?></span>
          </div>
          <?php endif; ?>

          <!-- Login Form -->
          <form action="<?= base_url('login/authenticate'); ?>" method="POST" id="loginForm">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

            <!-- Email Input -->
            <div class="field-group">
              <label class="field-label">Email</label>
              <div class="field-wrap" id="emailWrapper">
                <svg class="field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <input type="text" id="identityInput" name="identity" required
                       placeholder="nama@telkomuniversity.ac.id"
                       class="field-input">
              </div>
              <p id="emailHint" class="field-hint"></p>
            </div>

            <!-- Password Input -->
            <div class="field-group">
              <label class="field-label">Password</label>
              <div class="field-wrap" id="passWrapper">
                <svg class="field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <input type="password" id="passwordInput" name="password" required
                       placeholder="Masukkan password"
                       class="field-input">
                <button type="button" id="togglePassword" class="eye-btn">
                  <svg id="eyeIcon" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                  <svg id="eyeOffIcon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                </button>
              </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="submitBtn" class="btn-submit">Masuk &rarr;</button>

            <p class="forgot-link"><a href="<?= base_url('forgot_password'); ?>">Lupa password?</a></p>
          </form>
        </div>

      </div><!-- /login-card-container -->
    </div><!-- /hang-root -->

  </div><!-- /page-wrap -->

  <p class="footer-copy">&copy; 2025 Fakultas Industri Kreatif &mdash; Telkom University</p>

  <script src="<?= base_url('assets/js/login.js'); ?>?v=<?= time(); ?>"></script>
</body>
</html>
