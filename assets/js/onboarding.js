/**
 * IK Labs Portal — Onboarding & Profile Setup JavaScript
 * Handles step switching, password strength check, and strict symbol validation for names.
 */

document.addEventListener('DOMContentLoaded', () => {

  // Elements
  const stepItem1 = document.getElementById('stepItem1');
  const stepItem2 = document.getElementById('stepItem2');
  const stepPane1 = document.getElementById('stepPane1');
  const stepPane2 = document.getElementById('stepPane2');

  const formStep1 = document.getElementById('formStep1');
  const formStep2 = document.getElementById('formStep2');

  // Input Fields Step 1
  const passNew = document.getElementById('password_baru');
  const passConfirm = document.getElementById('konfirmasi_password');
  const eyeNew = document.getElementById('eyeNew');
  const eyeConfirm = document.getElementById('eyeConfirm');
  const strengthBar = document.getElementById('strengthBar');
  const strengthText = document.getElementById('strengthText');
  const passError = document.getElementById('passError');

  // Input Fields Step 2
  const namaDepan = document.getElementById('nama_depan');
  const namaBelakang = document.getElementById('nama_belakang');
  const badgeDepan = document.getElementById('badgeDepan');
  const badgeBelakang = document.getElementById('badgeBelakang');

  // ==========================================
  // 1. STEP NAVIGATION
  // ==========================================
  function goToStep(stepNumber) {
    if (stepNumber === 1) {
      stepItem1.classList.add('active');
      stepItem2.classList.remove('active');

      stepPane1.classList.add('active');
      stepPane2.classList.remove('active');
    } else if (stepNumber === 2) {
      stepItem1.classList.remove('active');
      stepItem1.classList.add('completed');
      stepItem2.classList.add('active');

      stepPane1.classList.remove('active');
      stepPane2.classList.add('active');
    }
  }

  // Allow clicking on stepper tabs for interactive preview
  stepItem1?.addEventListener('click', () => goToStep(1));
  stepItem2?.addEventListener('click', () => goToStep(2));

  // ==========================================
  // 2. PASSWORD TOGGLE & STRENGTH METER (STEP 1)
  // ==========================================
  eyeNew?.addEventListener('click', () => {
    const isPass = passNew.type === 'password';
    passNew.type = isPass ? 'text' : 'password';
  });

  eyeConfirm?.addEventListener('click', () => {
    const isPass = passConfirm.type === 'password';
    passConfirm.type = isPass ? 'text' : 'password';
  });

  passNew?.addEventListener('input', () => {
    const val = passNew.value;
    let score = 0;
    if (val.length >= 6) score += 25;
    if (val.length >= 10) score += 25;
    if (/[A-Z]/.test(val)) score += 25;
    if (/[0-9!@#$%^&*]/.test(val)) score += 25;

    strengthBar.style.width = score + '%';

    if (score < 25) {
      strengthBar.style.background = '#ef4444';
      strengthText.textContent = 'Sangat Lemah';
      strengthText.style.color = '#fca5a5';
    } else if (score <= 50) {
      strengthBar.style.background = '#f59e0b';
      strengthText.textContent = 'Cukup';
      strengthText.style.color = '#fde68a';
    } else if (score <= 75) {
      strengthBar.style.background = '#3b82f6';
      strengthText.textContent = 'Kuat';
      strengthText.style.color = '#93c5fd';
    } else {
      strengthBar.style.background = '#10b981';
      strengthText.textContent = 'Sangat Kuat';
      strengthText.style.color = '#6ee7b7';
    }
  });

  // Step 1 Form Handler
  formStep1?.addEventListener('submit', (e) => {
    e.preventDefault();
    passError.classList.add('hidden');

    if (passNew.value.length < 6) {
      passError.textContent = 'Password minimal 6 karakter!';
      passError.classList.remove('hidden');
      return;
    }

    if (passNew.value !== passConfirm.value) {
      passError.textContent = 'Konfirmasi password tidak cocok dengan password baru!';
      passError.classList.remove('hidden');
      return;
    }

    // Success -> Store into hidden inputs of formStep2 & Advance to Step 2
    const hiddenPass = document.getElementById('hidden_password_baru');
    const hiddenConfirm = document.getElementById('hidden_konfirmasi_password');
    if (hiddenPass) hiddenPass.value = passNew.value;
    if (hiddenConfirm) hiddenConfirm.value = passConfirm.value;

    goToStep(2);
  });

  // ==========================================
  // 3. STRICT SYMBOL & NUMBER VALIDATION (STEP 2)
  // ==========================================
  // Regex allowing only letters (a-z, A-Z) and spaces
  const alphaSpaceRegex = /^[a-zA-Z\s]*$/;

  function validateNameInput(inputElement, badgeElement, hintElementId) {
    if (!inputElement) return;

    inputElement.addEventListener('input', () => {
      const originalValue = inputElement.value;
      // Strip any characters that are NOT letters or spaces
      const cleanValue = originalValue.replace(/[^a-zA-Z\s]/g, '');

      const hintElem = document.getElementById(hintElementId);

      if (originalValue !== cleanValue) {
        inputElement.value = cleanValue;
        badgeElement.classList.add('show');
        inputElement.parentElement.parentElement.classList.add('has-error');

        if (hintElem) {
          hintElem.textContent = 'Simbol atau angka telah dihapus otomatis! Hanya huruf & spasi yang diperbolehkan.';
          hintElem.classList.remove('hidden');
        }

        setTimeout(() => {
          badgeElement.classList.remove('show');
          inputElement.parentElement.parentElement.classList.remove('has-error');
          if (hintElem) hintElem.classList.add('hidden');
        }, 3000);
      } else {
        inputElement.parentElement.parentElement.classList.remove('has-error');
        if (hintElem) hintElem.classList.add('hidden');
      }
    });
  }

  validateNameInput(namaDepan, badgeDepan, 'hintDepan');
  validateNameInput(namaBelakang, badgeBelakang, 'hintBelakang');

  // Step 2 Form Handler
  formStep2?.addEventListener('submit', (e) => {
    const hiddenPass = document.getElementById('hidden_password_baru');
    if (!hiddenPass || !hiddenPass.value || hiddenPass.value.length < 6) {
      e.preventDefault();
      alert('Silakan buat password baru Anda di Step 1 terlebih dahulu!');
      goToStep(1);
      if (passNew) passNew.focus();
      return;
    }
  });

});
