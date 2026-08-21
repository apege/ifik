<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($title) ? $title : 'Onboarding Mahasiswa — IK Labs Portal'; ?></title>

  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Tailwind CSS v3 -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Onboarding Custom CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/css/onboarding.css'); ?>?v=<?= time(); ?>">
</head>
<body>

  <!-- Background Layer -->
  <div class="bg-layer">
    <img src="<?= base_url('assets/images/background.png'); ?>" alt="Background" class="bg-img">
    <div class="bg-overlay"></div>
  </div>

  <div class="page-container">

    <!-- Header Brand -->
    <div class="brand-header">
      <img src="<?= base_url('assets/images/logo-dummy.webp'); ?>" alt="Logo IFIK" class="brand-logo mx-auto">
      <h1 class="brand-title">Aktivasi & Lengkapi Profil Akun</h1>
      <p class="brand-subtitle">Fakultas Industri Kreatif — Telkom University</p>
    </div>

    <!-- Main Onboarding Card -->
    <div class="onboarding-card">

      <?php if ($this->session->flashdata('warning')): ?>
        <div class="p-4 mx-6 mt-6 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-200 text-sm flex items-center gap-3">
          <svg class="w-5 h-5 flex-shrink-0 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
          <div><?= $this->session->flashdata('warning'); ?></div>
        </div>
      <?php endif; ?>

      <?php if ($this->session->flashdata('error')): ?>
        <div class="p-4 mx-6 mt-6 rounded-xl bg-red-500/20 border border-red-500/40 text-red-200 text-sm flex items-center gap-3">
          <svg class="w-5 h-5 flex-shrink-0 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6m0-6l6 6"/></svg>
          <div><?= $this->session->flashdata('error'); ?></div>
        </div>
      <?php endif; ?>

      <!-- Stepper Progress Header -->
      <div class="stepper-bar">
        <div class="step-item active" id="stepItem1">
          <div class="step-num">1</div>
          <div class="step-text">
            <span class="step-title">Step 1: Ubah Password</span>
            <span class="step-sub">Wajib Ganti Password Token</span>
          </div>
        </div>

        <div class="step-item" id="stepItem2">
          <div class="step-num">2</div>
          <div class="step-text">
            <span class="step-title">Step 2: Isi Biodata</span>
            <span class="step-sub">NIM & Data Mahasiswa</span>
          </div>
        </div>
      </div>

      <div class="card-body">

        <!-- ========================================== -->
        <!-- STEP 1: FORCE CHANGE PASSWORD              -->
        <!-- ========================================== -->
        <div class="step-pane active" id="stepPane1">

          <div class="alert-banner alert-warning">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01"/></svg>
            <div>
              <strong>Perhatian: Password Anda Saat Ini Adalah Token Sementara!</strong>
              <p class="mt-0.5 opacity-90">Demi keamanan akun Anda, sistem mewajibkan pembaruan password baru sebelum dapat mengakses Layanan Dashboard IFIK.</p>
            </div>
          </div>

          <div class="section-head">
            <h2 class="section-title">
              <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              Buat Password Baru Anda
            </h2>
            <p class="section-desc">Gunakan minimal 6 karakter dengan kombinasi huruf dan angka.</p>
          </div>

          <form id="formStep1" action="<?= base_url('onboarding/process_password'); ?>" method="POST">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

            <div class="form-grid">
              <!-- Password Baru -->
              <div class="form-group form-col-full">
                <label for="password_baru" class="form-label">
                  <span>Password Baru <span class="req">*</span></span>
                </label>
                <div class="input-wrap">
                  <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                  <input type="password" id="password_baru" name="password_baru" class="form-input" placeholder="Masukkan password baru" required>
                  <button type="button" id="eyeNew" class="eye-toggle" title="Lihat password">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                  </button>
                </div>
                <div class="strength-wrap">
                  <div class="strength-bar-bg"><div class="strength-bar-fill" id="strengthBar"></div></div>
                  <span class="strength-text" id="strengthText">Kekuatan password</span>
                </div>
              </div>

              <!-- Konfirmasi Password -->
              <div class="form-group form-col-full">
                <label for="konfirmasi_password" class="form-label">
                  <span>Konfirmasi Password Baru <span class="req">*</span></span>
                </label>
                <div class="input-wrap">
                  <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  <input type="password" id="konfirmasi_password" name="konfirmasi_password" class="form-input" placeholder="Ulangi password baru" required>
                  <button type="button" id="eyeConfirm" class="eye-toggle" title="Lihat password">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                  </button>
                </div>
              </div>
            </div>

            <p id="passError" class="error-hint hidden mt-2"></p>

            <div class="form-actions">
              <button type="submit" class="btn-primary">
                Simpan & Lanjut ke Step 2
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
              </button>
            </div>
          </form>
        </div>


        <!-- ========================================== -->
        <!-- STEP 2: ISI BIODATA MAHASISWA              -->
        <!-- ========================================== -->
        <div class="step-pane" id="stepPane2">

          <div class="alert-banner alert-info">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <div>
              <strong>Lengkapi Informasi Biodata Mahasiswa</strong>
              <p class="mt-0.5 opacity-90">Mohon periksa dan lengkapi data profil Anda. Nama depan dan belakang tidak boleh mengandung angka atau simbol apapun.</p>
            </div>
          </div>

          <div class="section-head">
            <h2 class="section-title">
              <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              Data Diri & Akademik
            </h2>
            <p class="section-desc">Pastikan data yang diisi sesuai dengan dokumen resmi Telkom University.</p>
          </div>

          <form id="formStep2" action="<?= base_url('onboarding/process_biodata'); ?>" method="POST">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <!-- Hidden inputs to transfer password from Step 1 -->
            <input type="hidden" name="password_baru" id="hidden_password_baru">
            <input type="hidden" name="konfirmasi_password" id="hidden_konfirmasi_password">

            <div class="form-grid">

              <!-- NIM -->
              <div class="form-group form-col-full">
                <label for="nim" class="form-label">
                  <span>NIM (Nomor Induk Mahasiswa) <span class="req">*</span></span>
                </label>
                <div class="input-wrap">
                  <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 012-2h2a2 2 0 012 2v1m-6 0h6"/></svg>
                  <input type="text" id="nim" name="nim" class="form-input" placeholder="Contoh: 1301210001" value="<?= isset($nim) ? htmlspecialchars($nim) : ''; ?>" required>
                </div>
              </div>

              <!-- Nama Depan (STRICT NO SYMBOLS) -->
              <div class="form-group">
                <label for="nama_depan" class="form-label">
                  <span>Nama Depan <span class="req">*</span></span>
                  <span class="badge-rule" id="badgeDepan">⚠️ Tanpa Simbol / Angka</span>
                </label>
                <div class="input-wrap">
                  <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                  <input type="text" id="nama_depan" name="nama_depan" class="form-input" placeholder="Nama depan tanpa simbol" value="<?= isset($nama_depan) ? htmlspecialchars($nama_depan) : ''; ?>" required>
                </div>
                <p id="hintDepan" class="error-hint hidden"></p>
              </div>

              <!-- Nama Belakang (STRICT NO SYMBOLS) -->
              <div class="form-group">
                <label for="nama_belakang" class="form-label">
                  <span>Nama Belakang <span class="req">*</span></span>
                  <span class="badge-rule" id="badgeBelakang">⚠️ Tanpa Simbol / Angka</span>
                </label>
                <div class="input-wrap">
                  <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                  <input type="text" id="nama_belakang" name="nama_belakang" class="form-input" placeholder="Nama belakang tanpa simbol" value="<?= isset($nama_belakang) ? htmlspecialchars($nama_belakang) : ''; ?>" required>
                </div>
                <p id="hintBelakang" class="error-hint hidden"></p>
              </div>

              <!-- Tempat Lahir -->
              <div class="form-group">
                <label for="tempat_lahir" class="form-label">
                  <span>Tempat Lahir <span class="req">*</span></span>
                </label>
                <div class="input-wrap">
                  <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                  <input type="text" id="tempat_lahir" name="tempat_lahir" class="form-input" placeholder="Contoh: Bandung" required>
                </div>
              </div>

              <!-- Tanggal Lahir -->
              <div class="form-group">
                <label for="tanggal_lahir" class="form-label">
                  <span>Tanggal Lahir <span class="req">*</span></span>
                </label>
                <div class="input-wrap">
                  <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                  <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="form-input" required>
                </div>
              </div>

              <!-- Alamat -->
              <div class="form-group form-col-full">
                <label for="alamat" class="form-label">
                  <span>Alamat Lengkap <span class="req">*</span></span>
                </label>
                <div class="input-wrap">
                  <svg class="input-icon" style="top: 14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                  <textarea id="alamat" name="alamat" class="form-textarea" placeholder="Jl. Telekomunikasi No. 1, Terusan Buah Batu, Bandung" required></textarea>
                </div>
              </div>

              <!-- Konsentrasi (Jurusan) -->
              <div class="form-group">
                <label for="konsentrasi" class="form-label">
                  <span>Konsentrasi / Jurusan <span class="req">*</span></span>
                </label>
                <div class="input-wrap">
                  <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h6m-6 0V11m0 0h6m-6 0V5"/></svg>
                  <select id="konsentrasi" name="konsentrasi" class="form-select" required>
                    <option value="" disabled selected>Pilih Konsentrasi / Jurusan</option>
                    <?php if (isset($konsentrasi_list)): ?>
                      <?php foreach ($konsentrasi_list as $kons): ?>
                        <option value="<?= htmlspecialchars($kons); ?>"><?= htmlspecialchars($kons); ?></option>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <option value="Desain Komunikasi Visual">Desain Komunikasi Visual</option>
                      <option value="Informatika">Informatika</option>
                      <option value="Rekayasa Perangkat Lunak">Rekayasa Perangkat Lunak</option>
                    <?php endif; ?>
                  </select>
                </div>
              </div>

              <!-- Dosen Wali -->
              <div class="form-group">
                <label for="dosen_wali" class="form-label">
                  <span>Dosen Wali <span class="req">*</span></span>
                </label>
                <div class="input-wrap">
                  <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                  <select id="dosen_wali" name="dosen_wali" class="form-select" required>
                    <option value="" disabled selected>Pilih Dosen Wali</option>
                    <?php if (isset($dosen_wali_list)): ?>
                      <?php foreach ($dosen_wali_list as $nip => $nama_dosen): ?>
                        <option value="<?= htmlspecialchars($nip); ?>"><?= htmlspecialchars($nama_dosen); ?></option>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <option value="19850101">Dr. Ir. Ahmad Yani, M.T.</option>
                      <option value="19880205">Prof. Siti Aminah, Ph.D.</option>
                    <?php endif; ?>
                  </select>
                </div>
              </div>

            </div><!-- /form-grid -->

            <div class="form-actions">
              <button type="button" class="btn-secondary" onclick="document.getElementById('stepItem1').click();">
                &larr; Kembali ke Step 1
              </button>
              <button type="submit" class="btn-primary">
                Simpan & Masuk ke Dashboard
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
              </button>
            </div>
          </form>

        </div><!-- /stepPane2 -->

      </div><!-- /card-body -->
    </div><!-- /onboarding-card -->

    <p class="footer-copy">&copy; 2025 Fakultas Industri Kreatif &mdash; Telkom University</p>

  </div><!-- /page-container -->

  <script src="<?= base_url('assets/js/onboarding.js'); ?>?v=<?= time(); ?>"></script>
</body>
</html>
