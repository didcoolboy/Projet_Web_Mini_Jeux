/* ═══════════════════════════════════════════════
   PIXELZONE — connexion.js
═══════════════════════════════════════════════ */

/* ── Afficher / masquer le mot de passe ── */
const togglePwd = document.getElementById('togglePwd');
const pwdInput  = document.getElementById('password');

if (togglePwd && pwdInput) {
  togglePwd.addEventListener('click', function() {
    const visible = pwdInput.type === 'text';
    pwdInput.type        = visible ? 'password' : 'text';
    togglePwd.textContent = visible ? '👁' : '🙈';
  });
}

/* ── Validation + feedback bouton ── */
const form      = document.getElementById('loginForm');
const submitBtn = document.getElementById('submitBtn');
const btnText   = submitBtn ? submitBtn.querySelector('.btn-auth__text')    : null;
const btnLoad   = submitBtn ? submitBtn.querySelector('.btn-auth__loading') : null;
const emailInput = document.getElementById('email');

// Petit message d'erreur inline
function showError(input, msg) {
  input.classList.add('error');
  let err = input.parentElement.parentElement.querySelector('.field-error');
  if (!err) {
    err = document.createElement('span');
    err.className = 'field-error';
    err.style.cssText = 'font-size:11px;color:var(--neon-r);margin-top:4px;display:block;';
    input.parentElement.parentElement.appendChild(err);
  }
  err.textContent = msg;
}

function clearError(input) {
  input.classList.remove('error');
  const err = input.parentElement.parentElement.querySelector('.field-error');
  if (err) err.remove();
}

emailInput  && emailInput.addEventListener('input',  () => clearError(emailInput));
pwdInput    && pwdInput.addEventListener('input',    () => clearError(pwdInput));

if (form) {
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    let valid = true;

    // Validation email
    if (!emailInput.value.trim()) {
      showError(emailInput, 'Champ obligatoire.');
      valid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value)) {
      showError(emailInput, 'Email invalide.');
      valid = false;
    }

    // Validation mot de passe
    if (!pwdInput.value) {
      showError(pwdInput, 'Champ obligatoire.');
      valid = false;
    }

    if (!valid) return;

    // Feedback visuel de chargement
    submitBtn.disabled   = true;
    btnText.hidden       = true;
    btnLoad.hidden       = false;

    // Soumettre le formulaire (PHP prendra la main)
    // Pour l'instant on simule un court délai
    setTimeout(() => form.submit(), 400);
  });
}