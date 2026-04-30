/* ═══════════════════════════════════════════════
   PIXELZONE — profil.js
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
    const duration = 1600;
    function countUp(ts) {
      if (!startTime) startTime = ts;
      const p    = Math.min((ts - startTime) / duration, 1);
      const ease = 1 - Math.pow(1 - p, 3);
      scoreEl.textContent = Math.floor(ease * target).toLocaleString('fr-FR');
      if (p < 1) requestAnimationFrame(countUp);
    }
    requestAnimationFrame(countUp);
  }

  // ── Barres animées ──
  setTimeout(() => {
    const xpBar = document.getElementById('xpBar');
    if (xpBar) xpBar.style.width = xpBar.dataset.xp + '%';

    document.querySelectorAll('.stat-progress-fill').forEach(el => {
      el.style.width = el.dataset.w + '%';
    });
  }, 300);

  const searchInput = document.getElementById('playerSearchInput');
  const searchResults = document.getElementById('searchResults');
  if (searchInput && searchResults) {
    async function doSearch(q) {
      if (!q || q.trim() === '') {
        searchResults.innerHTML = '';
        return;
      }
      try {
        const res = await fetch('/profil/search?q=' + encodeURIComponent(q));
        if (!res.ok) throw new Error('Network error');
        const users = await res.json();
        if (!users || users.length === 0) {
          searchResults.innerHTML = '<div style="color:var(--muted);padding:8px 0;">Aucun joueur trouvé.</div>';
          return;
        }
        const html = users.map(u => {
          const scoresHtml = (u.scores && u.scores.length > 0)
            ? `<div style="margin-top:8px;display:flex;gap:8px;flex-wrap:wrap">` + u.scores.map(s =>
                `<div style="background:rgba(255,255,255,0.03);padding:6px 8px;border-radius:6px;font-size:.82rem">` +
                `${s.game ? s.game : '—'}: <strong style="color:var(--neon-g);">${Number(s.top_score).toLocaleString('fr-FR')}</strong>` +
                `</div>`
              ).join('') + `</div>`
            : `<div style="margin-top:8px;color:var(--muted);font-size:.9rem">Aucun score</div>`;

          return `<a href="/profil/${u.id}" class="match-row" style="display:block;padding:8px 10px;border-radius:6px;text-decoration:none;color:inherit;margin-bottom:12px;background:rgba(255,255,255,0.02)">` +
            `<div style="display:flex;align-items:flex-start;gap:12px">` +
              `<div style="width:36px;height:36px;border-radius:6px;background:rgba(255,255,255,0.03);display:flex;align-items:center;justify-content:center;font-weight:700">${u.pseudo.slice(0,2).toUpperCase()}</div>` +
              `<div style="flex:1">` +
                `<div style="display:flex;align-items:center;justify-content:space-between">` +
                  `<div style="font-weight:700">${u.pseudo}</div>` +
                  `<div style="font-size:.78rem;color:var(--muted)">Voir le profil →</div>` +
                `</div>` +
                `${scoresHtml}` +
              `</div>` +
            `</div>` +
          `</a>`;
        }).join('');
        searchResults.innerHTML = html;
      } catch (err) {
        searchResults.innerHTML = '<div style="color:var(--danger);">Erreur lors de la recherche.</div>';
      }
    }

    // déclencher sur Entrée
    searchInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        doSearch(searchInput.value);
      }
    });
  }

});
