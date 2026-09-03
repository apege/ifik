<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password — IK Labs Portal</title>
  <meta name="description" content="Reset password akun IK Labs Portal — Fakultas Industri Kreatif, Telkom University.">

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
      <h1 class="hero-title">Reset<br>Password<br>Akun.</h1>
      <p class="hero-sub">Buat kata sandi baru yang aman dan mudah diingat untuk mengakses kembali seluruh layanan IFIK Labs Portal.</p>
    </div>

    <!-- RIGHT — HANGING CARD WITH ROPE -->
    <div class="hang-root" id="hangRoot">

      <!-- Rope extension going up off-screen -->
      <div class="rope-extension"></div>

      <!-- Rope Knot & Metallic Silver Ring SVG -->
      <div class="rope-ring-wrapper">
        <svg class="rope-ring-svg" viewBox="0 0 80 160" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <pattern id="ropePattern" width="8" height="8" patternUnits="userSpaceOnUse" patternTransform="rotate(45)">
              <rect width="4" height="8" fill="#8a5a36"/>
              <rect x="4" width="4" height="8" fill="#b88358"/>
            </pattern>

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

          <rect x="36" y="0" width="8" height="95" fill="url(#ropePattern)" filter="url(#shadow)"/>
          <line x1="38" y1="0" x2="38" y2="95" stroke="rgba(255,255,255,0.3)" stroke-width="1.2"/>

          <g filter="url(#shadow)">
            <ellipse cx="40" cy="93" rx="9" ry="5.5" fill="#8a5a36"/>
            <ellipse cx="40" cy="98" rx="11" ry="6" fill="#6d4228"/>
            <ellipse cx="40" cy="102" rx="9" ry="4.5" fill="#52301a"/>
          </g>

          <g filter="url(#shadow)">
            <path d="M 40 98 C 25 98, 23 118, 33 133 C 37 140, 37 148, 37 158 L 43 158 C 43 148, 43 140, 47 133 C 57 118, 55 98, 40 98 Z" fill="url(#chromeMetal)" />
            <path d="M 40 104 C 30 104, 29 116, 37 127 C 39 130, 39 144, 39 158 L 41 158 C 41 144, 41 130, 43 127 C 51 116, 50 104, 40 104 Z" fill="#78350f" />
            <path d="M 40 99 C 27 99, 25 117, 34 132" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="1.5" stroke-linecap="round"/>
          </g>
        </svg>
      </div>

      <!-- THE HANGING CARD -->
      <div class="login-card-container" id="loginCard">
        
        <div class="scanner-beam" id="scannerBeam"></div>

        <div class="card-metallic-cap">
          <div class="cap-progress-bar" id="capProgressBar"></div>
          <div class="cap-reflection"></div>
          <div class="cap-hole-wrapper">
            <div class="cap-hole"></div>
          </div>
        </div>

        <!-- Card Body Content -->
        <div class="card-body">
          <div class="card-brand">
            <img src="<?= base_url('assets/images/logo-dummy.webp'); ?>" alt="Logo IFIK" class="brand-logo-img">
          </div>

          <h2 class="card-title">Buat password baru</h2>
          <p class="card-sub">Masukkan password baru untuk akun <strong><?= htmlspecialchars(isset($email) ? $email : ''); ?></strong></p>

          <!-- Flash Error -->
          <?php if (isset($error) || $this->session->flashdata('error')): ?>
          <div class="flash-error">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span><?= isset($error) ? $error : $this->session->flashdata('error'); ?></span>
          </div>
          <?php endif; ?>

          <!-- Reset Password Form -->
          <form action="<?= base_url('login/process_reset_password'); ?>" method="POST" id="resetForm">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars(isset($token) ? $token : ''); ?>">
            <input type="hidden" name="email" value="<?= htmlspecialchars(isset($email) ? $email : ''); ?>">

            <!-- Password Baru -->
            <div class="field-group">
              <label class="field-label">Password Baru</label>
              <div class="field-wrap">
                <svg class="field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                <input type="password" id="passwordBaru" name="password_baru" required
                       placeholder="Minimal 6 karakter"
                       class="field-input">
                <button type="button" id="togglePass" class="field-toggle-btn" title="Lihat password">
                  <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </button>
              </div>
            </div>

            <!-- Konfirmasi Password -->
            <div class="field-group">
              <label class="field-label">Konfirmasi Password Baru</label>
              <div class="field-wrap">
                <svg class="field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <input type="password" id="konfirmasiPassword" name="konfirmasi_password" required
                       placeholder="Ulangi password baru"
                       class="field-input">
              </div>
              <p id="passHint" class="field-hint"></p>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="submitBtn" class="btn-submit">Simpan Password Baru &rarr;</button>

            <p class="forgot-link"><a href="<?= base_url('login'); ?>">&larr; Batal & Kembali ke Login</a></p>
          </form>
        </div>

      </div><!-- /login-card-container -->
    </div><!-- /hang-root -->

  </div><!-- /page-wrap -->

  <p class="footer-copy">&copy; 2025 Fakultas Industri Kreatif &mdash; Telkom University</p>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const form = document.getElementById('resetForm');
      const passBaru = document.getElementById('passwordBaru');
      const passConfirm = document.getElementById('konfirmasiPassword');
      const passHint = document.getElementById('passHint');
      const toggleBtn = document.getElementById('togglePass');
      const submitBtn = document.getElementById('submitBtn');
      const hangRoot = document.getElementById('hangRoot');
      const progressBar = document.getElementById('capProgressBar');

      toggleBtn?.addEventListener('click', () => {
        const isPass = passBaru.type === 'password';
        passBaru.type = isPass ? 'text' : 'password';
        passConfirm.type = isPass ? 'text' : 'password';
      });

      form?.addEventListener('submit', (e) => {
        passHint.textContent = '';
        passHint.className = 'field-hint';

        if (passBaru.value.length < 6) {
          e.preventDefault();
          passHint.textContent = 'Password baru minimal 6 karakter!';
          passHint.className = 'field-hint error';
          passBaru.focus();
          return;
        }

        if (passBaru.value !== passConfirm.value) {
          e.preventDefault();
          passHint.textContent = 'Konfirmasi password tidak cocok!';
          passHint.className = 'field-hint error';
          passConfirm.focus();
          return;
        }

        // Animate Scanner on Submit
        hangRoot.classList.add('is-loading');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Menyimpan Password...';
        if (progressBar) progressBar.style.width = '100%';
      });
    });
  </script>
</body>
</html>
