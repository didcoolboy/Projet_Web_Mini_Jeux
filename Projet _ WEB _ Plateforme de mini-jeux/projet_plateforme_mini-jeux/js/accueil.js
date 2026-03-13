/* ═══════════════════════════════════════════════
   PIXELZONE — accueil.js
═══════════════════════════════════════════════ */

/* ── Compteurs animés au scroll ── */
function animateCount(el, target) {
  const duration = 1800;
  let start = null;
  const step = ts => {
    if (!start) start = ts;
    const progress = Math.min((ts - start) / duration, 1);
    const ease = 1 - Math.pow(1 - progress, 3);
    const val = Math.floor(ease * target);
    el.textContent = val >= 1000
      ? val.toLocaleString('fr')
      : val;
    if (progress < 1) requestAnimationFrame(step);
  };
  requestAnimationFrame(step);
}

const statsObserver = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    if (!entry.isIntersecting) return;
    entry.target.querySelectorAll('[data-target]').forEach(el => {
      animateCount(el, parseInt(el.dataset.target));
    });
    statsObserver.unobserve(entry.target);
  });
}, { threshold: 0.5 });

const heroStats = document.querySelector('.hero-stats');
if (heroStats) statsObserver.observe(heroStats);

/* ── Apparition des cartes au scroll ── */
const cardObserver = new IntersectionObserver(entries => {
  entries.forEach((entry, i) => {
    if (!entry.isIntersecting) return;
    entry.target.style.animation = `fadeUp .5s ${i * 0.07}s ease both`;
    cardObserver.unobserve(entry.target);
  });
}, { threshold: 0.1 });

document.querySelectorAll('.game-card, .how-card, .podium-card, .lb-row').forEach(el => {
  el.style.opacity = '0';
  cardObserver.observe(el);
});

/* ── Lien actif dans la navbar au scroll ── */
const sections  = document.querySelectorAll('section[id]');
const navLinks  = document.querySelectorAll('.nav-link');

const navObserver = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    if (!entry.isIntersecting) return;
    navLinks.forEach(l => l.classList.remove('active'));
    const active = document.querySelector(`.nav-link[href="#${entry.target.id}"]`);
    if (active) active.classList.add('active');
  });
}, { threshold: 0.4 });

sections.forEach(s => navObserver.observe(s));

/* ── Burger mobile ── */
const burger   = document.getElementById('burger');
const navLinks2 = document.querySelector('.nav-links');
const navActions= document.querySelector('.nav-actions');

if (burger) {
  burger.addEventListener('click', () => {
    const open = navLinks2.style.display === 'flex';
    navLinks2.style.cssText  = open ? '' : 'display:flex;flex-direction:column;position:fixed;top:64px;left:0;right:0;background:rgba(5,5,15,0.97);padding:24px 32px;gap:20px;border-bottom:1px solid var(--border);z-index:499';
    navActions.style.cssText = open ? '' : 'display:flex;flex-direction:column;position:fixed;top:calc(64px + 180px);left:0;right:0;padding:0 32px 24px;background:rgba(5,5,15,0.97);gap:12px;z-index:499';
  });
}