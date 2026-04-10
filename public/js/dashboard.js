/* ═══════════════════════════════════════════════
   PIXELZONE — dashboard.js
═══════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {

  // ── Pixels flottants ──
  const cols = ['#00ff88','#ff3366','#ffdd00','#bf00ff','#00d4ff'];
  const container = document.getElementById('pixels');
  if (container) {
    for (let i = 0; i < 35; i++) {
      const p = document.createElement('div');
      p.className = 'pixel';
      const s = 2 + Math.floor(Math.random() * 4);
      p.style.cssText = `
        left: ${Math.random() * 100}%;
        background: ${cols[Math.floor(Math.random() * cols.length)]};
        animation-duration: ${6 + Math.random() * 14}s;
        animation-delay: ${Math.random() * 12}s;
        width: ${s}px; height: ${s}px;
      `;
      container.appendChild(p);
    }
  }

  // ── Compteur score animé ──
  const scoreEl = document.getElementById('scoreVal');
  if (scoreEl) {
    const target = parseInt(scoreEl.dataset.score, 10);
    let startTime = null;
    function countUp(ts) {
      if (!startTime) startTime = ts;
      const p    = Math.min((ts - startTime) / 1600, 1);
      const ease = 1 - Math.pow(1 - p, 3);
      scoreEl.textContent = Math.floor(ease * target).toLocaleString('fr-FR');
      if (p < 1) requestAnimationFrame(countUp);
    }
    requestAnimationFrame(countUp);
  }

});
