document.addEventListener('DOMContentLoaded', () => {

  const hangRoot = document.getElementById('hangRoot');

  // ─── ERROR SHAKE ON PAGE LOAD (if backend flash error exists) ───
  const flashError = document.querySelector('.flash-error');
  if (flashError && hangRoot) {
    triggerErrorShake();
  }

  function triggerErrorShake() {
    if (!hangRoot) return;
    hangRoot.classList.remove('is-loading', 'is-error');
    // Force reflow for animation restart
    void hangRoot.offsetWidth;
    hangRoot.classList.add('is-error');

    hangRoot.addEventListener('animationend', function handler(e) {
      if (e.animationName === 'errorShake') {
        hangRoot.classList.remove('is-error');
        hangRoot.removeEventListener('animationend', handler);
      }
    });
  }

  // ─── PASSWORD TOGGLE ──────────────────────────
  const togglePasswordBtn = document.getElementById('togglePassword');
  const passwordInput     = document.getElementById('passwordInput');
  const eyeIcon           = document.getElementById('eyeIcon');
  const eyeOffIcon        = document.getElementById('eyeOffIcon');

  if (togglePasswordBtn && passwordInput) {
    togglePasswordBtn.addEventListener('click', () => {
      const isPw = passwordInput.type === 'password';
      passwordInput.type = isPw ? 'text' : 'password';
      eyeIcon?.classList.toggle('hidden', isPw);
      eyeOffIcon?.classList.toggle('hidden', !isPw);
    });
  }

  // ─── EMAIL VALIDATION ─────────────────────────
  const identityInput = document.getElementById('identityInput');
  const emailWrapper  = document.getElementById('emailWrapper');
  const emailHint     = document.getElementById('emailHint');

  function setEmailState(state, message) {
    if (!emailWrapper || !emailHint) return;
    emailWrapper.style.borderColor = '';
    emailWrapper.style.boxShadow   = '';
    emailHint.style.display        = 'none';
    emailHint.innerHTML            = '';

    if (state === 'error') {
      emailWrapper.style.borderColor = '#ef4444';
      emailWrapper.style.boxShadow   = '0 0 0 3px rgba(239,68,68,0.2)';
      emailHint.style.display        = 'flex';
      emailHint.style.color          = '#dc2626';
      emailHint.innerHTML            = '&#10007; ' + message;
    } else if (state === 'valid') {
      emailWrapper.style.borderColor = '#22c55e';
      emailWrapper.style.boxShadow   = '0 0 0 3px rgba(34,197,94,0.2)';
      emailHint.style.display        = 'flex';
      emailHint.style.color          = '#16a34a';
      emailHint.innerHTML            = '&#10003; ' + message;
    }
  }

  function validateEmail(val, strict) {
    if (!val) { setEmailState('none'); return false; }
    const hasAt = val.includes('@');
    const isTelU = /@(student\.)?telkomuniversity\.ac\.id$/i.test(val);
    const isGeneralEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);

    if (!hasAt && !strict) { setEmailState('none'); return false; }

    if (isTelU) {
      setEmailState('valid', 'Email Telkom University valid');
      return true;
    } else if (isGeneralEmail) {
      setEmailState('valid', 'Email valid');
      return true;
    } else {
      setEmailState('error', 'Masukkan format email yang valid');
      return false;
    }
  }

  if (identityInput) {
    identityInput.addEventListener('input', () => validateEmail(identityInput.value.trim(), false));
    identityInput.addEventListener('blur',  () => validateEmail(identityInput.value.trim(), true));
  }

  // ─── PARALLAX (Mouse moves background only) ───
  const bgImg = document.getElementById('bgImg');

  window.addEventListener('mousemove', (e) => {
    const cx = window.innerWidth  / 2;
    const cy = window.innerHeight / 2;
    const dx = (e.clientX - cx) / cx;
    const dy = (e.clientY - cy) / cy;

    if (bgImg) {
      bgImg.style.transform = `translate(${dx * -10}px, ${dy * -7}px) scale(1.05)`;
    }
  });

  document.addEventListener('mouseleave', () => {
    if (bgImg) bgImg.style.transform = 'scale(1.05)';
  });

  // ─── FORM SUBMIT & 3D SCANNER LOADING (MINIMAL 3.2 DETIK) ─────────────
  const loginForm      = document.getElementById('loginForm');
  const submitBtn      = document.getElementById('submitBtn');
  const capProgressBar = document.getElementById('capProgressBar');
  let isSubmitting     = false;

  if (loginForm && submitBtn) {
    loginForm.addEventListener('submit', (e) => {
      if (isSubmitting) return; // Prevent double submit
      e.preventDefault();

      const emailVal = identityInput ? identityInput.value.trim() : '';
      const isEmailValid = /^[^\s@]+@telkomuniversity\.ac\.id$/i.test(emailVal);

      if (!isEmailValid) {
        setEmailState('error', 'Harus email @telkomuniversity.ac.id');
        triggerErrorShake();
        return;
      }

      isSubmitting = true;

      // Add loading state (Card lifts, scanner beam activates, glowing aura pulses)
      if (hangRoot) {
        hangRoot.classList.remove('is-error');
        hangRoot.classList.add('is-loading');
      }

      submitBtn.disabled = true;
      let progress = 0;
      
      const updateButton = (pct) => {
        submitBtn.innerHTML = `
          <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="margin-top:-2px;">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>Memverifikasi Autentikasi... ${pct}%</span>
        `;
        if (capProgressBar) capProgressBar.style.width = pct + '%';
      };

      updateButton(0);

      // Smooth progress bar update over 3.2 seconds (3200ms)
      const duration = 3200;
      const intervalTime = 40;
      const step = 100 / (duration / intervalTime);

      const timer = setInterval(() => {
        progress += step;
        if (progress >= 100) {
          progress = 100;
          updateButton(100);
          clearInterval(timer);
          
          setTimeout(() => {
            loginForm.submit();
          }, 200);
        } else {
          updateButton(Math.floor(progress));
        }
      }, intervalTime);
    });
  }

});
