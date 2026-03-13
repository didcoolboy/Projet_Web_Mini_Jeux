/* ═══════════════════════════════════════════════
   PIXELZONE — script.js
═══════════════════════════════════════════════ */

/* ── Curseur personnalisé + traînée ── */
const cursor    = document.getElementById('cursor');

const TRAIL_LEN = 18;
const trail     = [];
let   mouseX    = -200;
let   mouseY    = -200;

// Créer les carrés fantômes
const trailEls  = [];
for (let i = 0; i < TRAIL_LEN; i++) {
  const el     = document.createElement('div');
  el.className = 'cursor-trail';
  const ratio  = 1 - i / TRAIL_LEN;
  const size   = Math.max(1, Math.round(14 * ratio));
  el.style.width   = size + 'px';
  el.style.height  = size + 'px';
  el.style.opacity = (ratio * 0.6).toFixed(2);
  el.style.filter  = 'blur(' + (i * 1.5).toFixed(1) + 'px)';
  document.body.appendChild(el);
  trailEls.push(el);
}

// Suivi souris
document.addEventListener('mousemove', function(e) {
  mouseX = e.clientX;
  mouseY = e.clientY;
});

// Boucle RAF
function animateCursor() {
  cursor.style.left = mouseX + 'px';
  cursor.style.top  = mouseY + 'px';

  trail.unshift({ x: mouseX, y: mouseY });
  if (trail.length > TRAIL_LEN) trail.pop();

  for (var i = 0; i < trailEls.length; i++) {
    var pos = trail[i] || { x: mouseX, y: mouseY };
    trailEls[i].style.left = pos.x + 'px';
    trailEls[i].style.top  = pos.y + 'px';
  }

  requestAnimationFrame(animateCursor);
}
requestAnimationFrame(animateCursor);

// Hover liens/boutons
document.querySelectorAll('a, button').forEach(function(el) {
  el.addEventListener('mouseenter', function() {
    cursor.classList.add('hover');
  });
  el.addEventListener('mouseleave', function() {
    cursor.classList.remove('hover');
  });
});

/* ── Pixels flottants ── */
const field  = document.getElementById('pixelField');
const colors = ['#00ff88', '#00d4ff', '#bf00ff', '#ffdd00', '#ff3366'];
const sizes  = [2, 4, 6, 8];

for (let i = 0; i < 38; i++) {
  const px     = document.createElement('div');
  px.className = 'px';
  const size   = sizes[Math.floor(Math.random() * sizes.length)];
  px.style.left              = (Math.random() * 100) + '%';
  px.style.width             = size + 'px';
  px.style.height            = size + 'px';
  px.style.background        = colors[Math.floor(Math.random() * colors.length)];
  px.style.animationDuration = (6 + Math.random() * 12) + 's';
  px.style.animationDelay    = (Math.random() * 10) + 's';
  field.appendChild(px);
}
