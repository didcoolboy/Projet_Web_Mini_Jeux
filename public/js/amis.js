/* ═══════════════════════════════════════════════
   PIXELZONE — amis.js  (frontend uniquement)
   Les données viennent du serveur via Blade.
═══════════════════════════════════════════════ */

// ═══════════════════════════════════════════════
// TABS
// ═══════════════════════════════════════════════

function switchTab(id, el) {
  document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
  document.getElementById(id).classList.add('active');
  el.classList.add('active');
}

// ═══════════════════════════════════════════════
// NOTIFICATION TOAST (sessions Laravel → JS)
// ═══════════════════════════════════════════════

function showNotif(msg) {
  const n = document.getElementById('notif');
  if (!n || !msg) return;
  n.textContent = msg;
  n.classList.add('show');
  setTimeout(() => n.classList.remove('show'), 3000);
}

// Affiche la notif si Laravel a passé un message en session
document.addEventListener('DOMContentLoaded', () => {
  const notifMsg = document.body.dataset.notif;
  if (notifMsg) showNotif(notifMsg);
});

// ═══════════════════════════════════════════════
// RECHERCHE GLOBALE (appel AJAX)
// ═══════════════════════════════════════════════

async function searchPlayer() {
  const val    = document.getElementById('searchInput').value.trim();
  const result = document.getElementById('searchResult');
  if (!val) { result.classList.add('search-result-hidden'); return; }

  try {
    const res  = await fetch(`/amis/search?pseudo=${encodeURIComponent(val)}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
    });
    const data = await res.json();

    if (data.found) {
      document.getElementById('srAvatar').textContent = data.pseudo.slice(0, 2).toUpperCase();
      document.getElementById('srPseudo').textContent = data.pseudo;
      document.getElementById('srInfo').textContent   = `score total : ${data.score} · ${data.parties} parties`;
      document.getElementById('searchResult').dataset.userId = data.id;
    } else {
      document.getElementById('srAvatar').textContent = '?';
      document.getElementById('srPseudo').textContent = 'AUCUN RÉSULTAT';
      document.getElementById('srInfo').textContent   = `pseudo "${val}" introuvable`;
    }
    result.classList.remove('search-result-hidden');
  } catch (e) {
    console.error('Erreur recherche :', e);
  }
}

function sendRequest() {
  const pseudo = document.getElementById('srPseudo').textContent;
  if (pseudo === 'AUCUN RÉSULTAT') return;

  // Soumet un formulaire POST dynamique
  const form   = document.createElement('form');
  form.method  = 'POST';
  form.action  = '/amis/envoyer';
  form.innerHTML = `
    <input type="hidden" name="_token"  value="${document.querySelector('meta[name=csrf-token]').content}">
    <input type="hidden" name="pseudo"  value="${pseudo}">
  `;
  document.body.appendChild(form);
  form.submit();
}

document.addEventListener('DOMContentLoaded', () => {
  const si = document.getElementById('searchInput');
  if (si) si.addEventListener('keydown', e => { if (e.key === 'Enter') searchPlayer(); });
});

// ═══════════════════════════════════════════════
// FLAMMES PIXEL (cartes or)
// ═══════════════════════════════════════════════

function initPixelFlames(card) {
  const canvas = document.createElement('canvas');
  canvas.style.cssText = 'position:absolute;inset:-6px;width:calc(100%+12px);height:calc(100%+12px);pointer-events:none;z-index:5;image-rendering:pixelated;';
  card.style.overflow  = 'visible';
  card.appendChild(canvas);

  const PX = 3;
  function resize() {
    canvas.width        = Math.ceil((card.offsetWidth  + 12) / PX);
    canvas.height       = Math.ceil((card.offsetHeight + 12) / PX);
    canvas.style.width  = (canvas.width  * PX) + 'px';
    canvas.style.height = (canvas.height * PX) + 'px';
  }
  resize();

  const particles = [];
  const W = () => canvas.width;
  const H = () => canvas.height;

  function spawnParticle() {
    const edge = Math.floor(Math.random() * 4);
    let x, y, vx, vy;
    const w = W(), h = H();
    if      (edge === 0) { x = Math.random()*w; y = h-1;  vx = (Math.random()-.5)*.3;      vy = -(0.15+Math.random()*.25); }
    else if (edge === 1) { x = 0;               y = Math.random()*h; vx = 0.1+Math.random()*.2;  vy = (Math.random()-.5)*.3; }
    else if (edge === 2) { x = w-1;             y = Math.random()*h; vx = -(0.1+Math.random()*.2); vy = (Math.random()-.5)*.3; }
    else                 { x = Math.random()*w; y = 0;    vx = (Math.random()-.5)*.3;      vy = 0.1+Math.random()*.2; }
    particles.push({ x, y, vx, vy, life: 1, maxLife: 30+Math.random()*50,
      color: Math.random() < .5 ? [255,220,0] : Math.random() < .5 ? [255,140,0] : [255,80,0] });
  }

  for (let i = 0; i < 18; i++) spawnParticle();

  function step() {
    if (Math.random() < 0.35) spawnParticle();
    if (particles.length > 40) particles.splice(0, particles.length - 40);
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    for (let i = particles.length-1; i >= 0; i--) {
      const p = particles[i];
      p.x += p.vx; p.y += p.vy; p.life -= 1/p.maxLife;
      if (p.life <= 0) { particles.splice(i,1); continue; }
      const alpha = p.life < .3 ? p.life/.3 : p.life > .8 ? (1-p.life)/.2 : 1;
      const [r,g,b] = p.color;
      ctx.fillStyle = `rgba(${r},${g},${b},${(alpha*.85).toFixed(2)})`;
      ctx.fillRect(Math.round(p.x), Math.round(p.y), 1, 1);
    }
    requestAnimationFrame(step);
  }
  step();
}

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.rank-gold').forEach(c => initPixelFlames(c));
});

// ═══════════════════════════════════════════════
// CURSEUR CARRÉ + TRAÎNÉE
// ═══════════════════════════════════════════════

const TRAIL_LEN = 4;
const trailPos  = Array.from({ length: TRAIL_LEN }, () => ({ x: -99, y: -99 }));
let mouseX = -99, mouseY = -99;

const mainCur = document.getElementById('cur-main');

const trailEls = [];
for (let i = 0; i < TRAIL_LEN; i++) {
  const el    = document.createElement('div');
  const size  = Math.max(3, 8 - i * 2);
  const alpha = ((TRAIL_LEN - i + 1) / (TRAIL_LEN + 1)).toFixed(2);
  el.style.cssText = `position:fixed;width:${size}px;height:${size}px;pointer-events:none;z-index:${9998-i};background:rgba(0,255,136,${alpha});image-rendering:pixelated;transform:translate(-50%,-50%);`;
  document.body.appendChild(el);
  trailEls.push({ el });
}

document.addEventListener('mousemove', e => {
  mouseX = e.clientX; mouseY = e.clientY;
  mainCur.style.left = mouseX + 'px';
  mainCur.style.top  = mouseY + 'px';
});

const LERP = [0.28, 0.22, 0.17, 0.13];
(function animTrail() {
  trailPos[0].x += (mouseX - trailPos[0].x) * LERP[0];
  trailPos[0].y += (mouseY - trailPos[0].y) * LERP[0];
  for (let i = 1; i < TRAIL_LEN; i++) {
    trailPos[i].x += (trailPos[i-1].x - trailPos[i].x) * LERP[i];
    trailPos[i].y += (trailPos[i-1].y - trailPos[i].y) * LERP[i];
  }
  trailEls.forEach((t, i) => {
    t.el.style.left = trailPos[i].x + 'px';
    t.el.style.top  = trailPos[i].y + 'px';
  });
  requestAnimationFrame(animTrail);
})();

// ═══════════════════════════════════════════════
// PIXELS FLOTTANTS EN FOND
// ═══════════════════════════════════════════════

const bgCols   = ['#00ff88', '#ff3366', '#ffdd00', '#bf00ff', '#00d4ff'];
const pixelsEl = document.getElementById('pixels');
if (pixelsEl) {
  for (let i = 0; i < 35; i++) {
    const p     = document.createElement('div');
    p.className = 'pixel';
    const size  = 2 + Math.floor(Math.random() * 4);
    p.style.cssText = `left:${Math.random()*100}%;background:${bgCols[Math.floor(Math.random()*bgCols.length)]};animation-duration:${6+Math.random()*14}s;animation-delay:${Math.random()*12}s;width:${size}px;height:${size}px;`;
    pixelsEl.appendChild(p);
  }
}
