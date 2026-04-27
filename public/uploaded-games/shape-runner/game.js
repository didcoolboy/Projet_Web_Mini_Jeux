const cv = document.getElementById('c'), ctx = cv.getContext('2d');
const W = 680, H = 360;
cv.width = W; cv.height = H;

// ── PROJECTION ─────────────────────────────────────────
const VPX = W / 2, VPY = H * 0.33, FOV = 360, CAMY = 2.6;
let CAM_X = 0;
function pr(wx, wy, wz) {
  const z = Math.max(wz, 0.05);
  return { x: VPX + (wx - CAM_X) / z * FOV, y: VPY + (CAMY - wy) / z * FOV, s: FOV / z, ok: wz > 0.08 };
}
function qd(p, f, e) {
  if (p.some(v => !v.ok)) return;
  ctx.beginPath(); ctx.moveTo(p[0].x, p[0].y);
  for (let i = 1; i < p.length; i++) ctx.lineTo(p[i].x, p[i].y);
  ctx.closePath(); ctx.fillStyle = f; ctx.fill();
  if (e) { ctx.strokeStyle = e; ctx.lineWidth = 0.7; ctx.stroke(); }
}

// ── CONSTANTS ──────────────────────────────────────────
const LW = 2.0, LANES = [-LW, 0, LW], THW = LW * 1.6;
const FAR = 46, TILE = 2.2, PZ = 4.2, SPAWN = 40;
const HIT_N = PZ + 1.0, HIT_F = PZ - 0.85;

// ── LEVELS ─────────────────────────────────────────────
const LVS = [
  { min: 0,    spd: 1.0,  label: 'Niveau 1' },
  { min: 100,  spd: 1.6,  label: 'Niveau 2' },
  { min: 280,  spd: 2.3,  label: 'Niveau 3' },
  { min: 520,  spd: 3.1,  label: 'Niveau 4' },
  { min: 850,  spd: 4.0,  label: 'Niveau 5' },
  { min: 1300, spd: 5.2,  label: 'Niveau 6' },
  { min: 1900, spd: 6.6,  label: 'Niveau 7' },
  { min: 2700, spd: 8.2,  label: 'Niveau 8' },
  { min: 3700, spd: 10.0, label: 'Niveau 9' },
  { min: 5000, spd: 12.0, label: 'Niveau 10' },
];
function getLv(s) {
  let l = LVS[0];
  for (const v of LVS) if (s >= v.min) l = v;
  if (s >= LVS[LVS.length - 1].min) {
    const e = Math.floor((s - LVS[LVS.length - 1].min) / 1500);
    return { ...l, spd: l.spd + e * 2.5, label: `Niveau ${10 + e}` };
  }
  return l;
}

function mkPat() {
  const free = Math.floor(Math.random() * 3);
  const r = Math.random();
  return { free, obs: r < 0.45 ? 'jump' : r < 0.82 ? 'tunnel' : 'clear' };
}

// ── STATE ──────────────────────────────────────────────
let G = null;
function fresh() {
  return {
    running: false, dead: false,
    score: 0, frame: 0, speed: 1, scroll: 0,
    lane: 1, laneX: LANES[1], laneVX: 0,
    posY: 0, velY: 0,
    state: 'run',
    slideT: 0, SLIDE_DUR: 16,
    queue: [],
    obs: [], nextZ: SPAWN * 0.45,
    parts: [], stars: null,
    combo: 0, okFlash: 0,
    lvFlash: 0, lvLabel: '', prevLv: '',
    deadY: 0, deadVY: 0,
  };
}

// ── INPUT QUEUE ────────────────────────────────────────
function applyAction(g, act) {
  if (act === 'left')  { if (g.lane > 0) g.lane--; return; }
  if (act === 'right') { if (g.lane < 2) g.lane++; return; }
  if (act === 'jump') {
    if (g.state === 'run' || g.state === 'slide') {
      g.state = 'jump'; g.posY = 0; g.velY = 4.8;
    } else if (g.state === 'jump' && g.posY < 0.3) {
      g.velY = 4.8;
    }
    return;
  }
  if (act === 'slide') {
    if (g.state === 'run')        { g.state = 'slide'; g.slideT = 0; }
    else if (g.state === 'jump')  { g.velY = Math.min(g.velY, -3); g.slideAfterLand = true; }
    else if (g.state === 'slide') { g.slideT = 0; }
  }
}

function spawnAll(g) {
  const h = g.scroll + SPAWN;
  while (g.nextZ < h) {
    g.obs.push({ z: g.nextZ, pat: mkPat(), done: false, hit: false });
    g.nextZ += Math.max(8, 17 - g.speed * 0.7) + Math.random() * 4;
  }
  g.obs = g.obs.filter(o => o.z > g.scroll - 3);
}

// ── TRACK ──────────────────────────────────────────────
function drawTrack(g) {
  const sc = g.scroll;
  qd([pr(-THW, 0, .3), pr(THW, 0, .3), pr(THW * .04, 0, FAR), pr(-THW * .04, 0, FAR)], '#10141e', null);

  const off = sc % TILE;
  for (let i = 0; i < 24; i++) {
    const z0 = i * TILE - off + .2, z1 = z0 + TILE;
    if (z0 < .1 || z0 > FAR) continue;
    const row = Math.floor((i + Math.floor(sc / TILE)));
    const ev = row % 2 === 0;
    qd([pr(-THW, 0, z0), pr(THW, 0, z0), pr(THW, 0, z1), pr(-THW, 0, z1)], ev ? '#141a28' : '#101420', null);
    for (const lx of LANES) {
      const L = pr(lx - .06, .005, z0), R = pr(lx + .06, .005, z0), L2 = pr(lx - .06, .005, z1), R2 = pr(lx + .06, .005, z1);
      if (L.ok && L2.ok) qd([L, R, R2, L2], '#1c2a44', null);
    }
    if (row % 4 === 0) {
      for (let m = 0; m < 3; m++) {
        const mx = (m - 1) * LW;
        const mL = pr(mx - .35, .005, z0 + TILE * .3), mR = pr(mx + .35, .005, z0 + TILE * .3);
        const mL2 = pr(mx - .35, .005, z0 + TILE * .55), mR2 = pr(mx + .35, .005, z0 + TILE * .55);
        if (mL.ok) qd([mL, mR, mR2, mL2], '#1e2c40', null);
      }
    }
  }

  for (const side of [-1, 1]) {
    const ex = side * THW, iw = side * .40, off2 = sc % (TILE * 1.6);
    for (let i = 0; i < 16; i++) {
      const z0 = i * TILE * 1.6 - off2 + .2, z1 = z0 + TILE * 1.6;
      if (z0 < .1 || z0 > FAR) continue;
      const ev = i % 2 === 0;
      qd([pr(ex, .24, z0), pr(ex + iw, .24, z0), pr(ex + iw, .24, z1), pr(ex, .24, z1)], ev ? '#dd3311' : '#eecc00', null);
      qd([pr(ex, .24, z0), pr(ex + iw, .24, z0), pr(ex + iw, 0, z0), pr(ex, 0, z0)], ev ? '#991100' : '#aa8800', null);
    }
    for (let i = 0; i < 24; i++) {
      const wz = i * TILE * 0.9 - (sc % (TILE * .9)) + .3;
      if (wz < .3 || wz > FAR) continue;
      const rp = pr(ex + iw * .5, .5, wz);
      if (rp.ok && rp.s > 4) {
        ctx.beginPath(); ctx.arc(rp.x, rp.y, Math.max(1.5, rp.s * .06), 0, Math.PI * 2);
        ctx.fillStyle = '#445566'; ctx.fill();
      }
    }
  }

  const bldgs = [
    { x: -17, z: 26, w: 5, h: 10, c: '#0c1118' }, { x: -12, z: 24, w: 3, h: 7, c: '#0e1420' },
    { x: -9,  z: 29, w: 4, h: 5,  c: '#0a0e16' }, { x: 7,   z: 25, w: 5, h: 9, c: '#0c1118' },
    { x: 13,  z: 28, w: 4, h: 12, c: '#0a0e16' }, { x: -20, z: 32, w: 7, h: 6, c: '#0e1218' },
    { x: 17,  z: 31, w: 6, h: 8,  c: '#0c1118' }, { x: -7,  z: 35, w: 3, h: 14, c: '#0a0c14' },
  ];
  for (const b of bldgs) {
    const tL = pr(b.x, b.h, b.z), tR = pr(b.x + b.w, b.h, b.z);
    const bL = pr(b.x, 0, b.z), bR = pr(b.x + b.w, 0, b.z);
    if (!tL.ok) continue;
    qd([tL, tR, bR, bL], b.c, '#06080e');
    const sR = pr(b.x + b.w, b.h, b.z + 1.5), sbR = pr(b.x + b.w, 0, b.z + 1.5);
    if (sR.ok) qd([tR, sR, sbR, bR], '#080c12', null);
    if (tL.s > 2) {
      const wh = tL.s * .12;
      for (let wy = .7; wy < b.h - .5; wy += 1.3) {
        for (let wx = b.x + .3; wx < b.x + b.w - .2; wx += .85) {
          const wp = pr(wx, wy, b.z);
          if (!wp.ok || wp.s < 2) continue;
          const lit = Math.random() < .45;
          ctx.fillStyle = lit ? 'rgba(255,220,100,.4)' : 'rgba(255,220,100,.06)';
          ctx.fillRect(wp.x - wh * .6, wp.y - wh * .6, wh * 1.2, wh * 1.2);
        }
      }
    }
  }

  const ls = 5.0, lo = sc % ls;
  for (let i = 0; i < 10; i++) {
    const wz = i * ls - lo + .6; if (wz < .4 || wz > FAR) continue;
    for (const side of [-1, 1]) {
      const px = side * (THW + 1.0);
      const pbot = pr(px, 0, wz), ptop = pr(px, 3.0, wz), parm = pr(px - side * .7, 2.9, wz);
      if (!ptop.ok) continue;
      const lw2 = Math.max(1.5, ptop.s * .09);
      ctx.beginPath(); ctx.moveTo(pbot.x, pbot.y); ctx.lineTo(ptop.x, ptop.y); ctx.strokeStyle = '#1c2e48'; ctx.lineWidth = lw2; ctx.stroke();
      ctx.beginPath(); ctx.moveTo(ptop.x, ptop.y); ctx.lineTo(parm.x, parm.y); ctx.strokeStyle = '#1c2e48'; ctx.lineWidth = lw2 * .7; ctx.stroke();
      const bulb = pr(px - side * .7, 2.8, wz);
      if (bulb.ok) {
        const br = Math.max(2.5, bulb.s * .16);
        ctx.beginPath(); ctx.arc(bulb.x, bulb.y, br, 0, Math.PI * 2); ctx.fillStyle = '#fff8cc'; ctx.fill();
        const lgt = pr(px, 0, wz);
        if (br > 5 && lgt.ok) {
          ctx.beginPath(); ctx.moveTo(bulb.x, bulb.y);
          ctx.lineTo(lgt.x - br * 4, lgt.y); ctx.lineTo(lgt.x + br * 4, lgt.y);
          ctx.closePath(); ctx.fillStyle = 'rgba(255,248,180,.04)'; ctx.fill();
        }
      }
    }
  }

  ctx.fillStyle = 'rgba(15,20,40,.5)';
  ctx.fillRect(0, VPY - 8, W, 16);
}

// ── OBSTACLES ──────────────────────────────────────────
function drawWall(cx, rz) {
  const hw = LW * .46, h = 1.5, d = .36, x0 = cx - hw, x1 = cx + hw;
  qd([pr(x0, h, rz - d), pr(x1, h, rz - d), pr(x1, h, rz), pr(x0, h, rz)], '#7a0c0c', null);
  for (let s = 0; s < 5; s++) {
    const y0 = h * s / 5, y1 = h * (s + .52) / 5;
    qd([pr(x0, y1, rz), pr(x1, y1, rz), pr(x1, y0, rz), pr(x0, y0, rz)], s % 2 === 0 ? '#c01414' : '#a01010', '#550000');
  }
  qd([pr(x1, h, rz - d), pr(x1, h, rz), pr(x1, 0, rz), pr(x1, 0, rz - d)], '#6a0a0a', null);
  const tL = pr(x0, h + .04, rz), tR = pr(x1, h + .04, rz);
  if (tL.ok) { ctx.beginPath(); ctx.moveTo(tL.x, tL.y); ctx.lineTo(tR.x, tR.y); ctx.strokeStyle = 'rgba(255,80,80,.7)'; ctx.lineWidth = Math.max(1.5, tL.s * .05); ctx.stroke(); }
}

function drawBarrier(cx, rz) {
  const hw = LW * .41, pH = .68, bY = .48, bH = .19, d = .20;
  for (const side of [-1, 1]) {
    const px = cx + side * hw * .86, pw = .11;
    qd([pr(px - pw, pH, rz - d), pr(px + pw, pH, rz - d), pr(px + pw, pH, rz), pr(px - pw, pH, rz)], '#c08800', null);
    qd([pr(px - pw, pH, rz), pr(px + pw, pH, rz), pr(px + pw, 0, rz), pr(px - pw, 0, rz)], '#f0aa00', '#886600');
    qd([pr(px + pw, pH, rz - d), pr(px + pw, pH, rz), pr(px + pw, 0, rz), pr(px + pw, 0, rz - d)], '#aa7700', null);
  }
  const x0 = cx - hw + .11, x1 = cx + hw - .11;
  qd([pr(x0, bY + bH, rz - d), pr(x1, bY + bH, rz - d), pr(x1, bY + bH, rz), pr(x0, bY + bH, rz)], '#ffe040', null);
  for (let s = 0; s < 6; s++) {
    const xa = x0 + (x1 - x0) * s / 6, xb = xa + (x1 - x0) * .44 / 6;
    qd([pr(xa, bY + bH, rz), pr(xb, bY + bH, rz), pr(xb, bY, rz), pr(xa, bY, rz)], s % 2 === 0 ? '#ffcc22' : '#1a1a1a', '#886600');
  }
  qd([pr(x1, bY + bH, rz - d), pr(x1, bY + bH, rz), pr(x1, bY, rz), pr(x1, bY, rz - d)], '#cc9900', null);
  const gL = pr(x0, bY + bH + .03, rz), gR = pr(x1, bY + bH + .03, rz);
  if (gL.ok) { ctx.beginPath(); ctx.moveTo(gL.x, gL.y); ctx.lineTo(gR.x, gR.y); ctx.strokeStyle = 'rgba(255,220,50,.6)'; ctx.lineWidth = Math.max(1.5, gL.s * .05); ctx.stroke(); }
}

function drawTunnel(cx, rz) {
  const hw = LW * .46, ah = .56, lw = .14, bH = .18, d = .48;
  const x0 = cx - hw, x1 = cx + hw, xi0 = x0 + lw, xi1 = x1 - lw;
  for (const [lx, rx] of [[x0, x0 + lw], [x1 - lw, x1]]) {
    qd([pr(lx, ah, rz - d), pr(rx, ah, rz - d), pr(rx, ah, rz), pr(lx, ah, rz)], '#1a3a8a', null);
    qd([pr(lx, ah, rz), pr(rx, ah, rz), pr(rx, 0, rz), pr(lx, 0, rz)], '#2255bb', '#0a1844');
    qd([pr(rx, ah, rz - d), pr(rx, ah, rz), pr(rx, 0, rz), pr(rx, 0, rz - d)], '#1a3a8a', null);
  }
  qd([pr(xi0, ah + bH, rz - d), pr(xi1, ah + bH, rz - d), pr(xi1, ah + bH, rz), pr(xi0, ah + bH, rz)], '#2a55cc', null);
  qd([pr(xi0, ah + bH, rz), pr(xi1, ah + bH, rz), pr(xi1, ah, rz), pr(xi0, ah, rz)], '#3a6aee', '#0a1844');
  qd([pr(xi1, ah + bH, rz - d), pr(xi1, ah + bH, rz), pr(xi1, ah, rz), pr(xi1, ah, rz - d)], '#1e44aa', null);
  qd([pr(xi0, ah, rz - d), pr(xi1, ah, rz - d), pr(xi1, ah, rz), pr(xi0, ah, rz)], '#020408', null);
  const eL = pr(xi0, ah + bH, rz), eR = pr(xi1, ah + bH, rz);
  if (eL.ok) { ctx.beginPath(); ctx.moveTo(eL.x, eL.y); ctx.lineTo(eR.x, eR.y); ctx.strokeStyle = 'rgba(60,140,255,.9)'; ctx.lineWidth = Math.max(2, eL.s * .06); ctx.stroke(); }
}

function drawObs(g) {
  [...g.obs].filter(o => !o.done).sort((a, b) => b.z - a.z).forEach(o => {
    const rz = o.z - g.scroll;
    if (rz < .1 || rz > FAR) return;
    const { free, obs } = o.pat;
    for (let li = 0; li < 3; li++) if (li !== free) drawWall(LANES[li], rz);
    if (obs === 'jump')   drawBarrier(LANES[free], rz);
    else if (obs === 'tunnel') drawTunnel(LANES[free], rz);
  });
}

// ── PLAYER ─────────────────────────────────────────────
function drawPlayer(g) {
  const st = g.state;
  const jh = g.posY;
  const ok = g.okFlash > 0;

  const SX = VPX;
  const GY = H * .80;
  const jPx = Math.min(90, jh * 42);
  const baseY = GY - jPx;

  const skinC  = ok ? '#55ffbb' : '#c8844a';
  const kitTop = ok ? '#22dd77' : '#cc0020';
  const kitBot = ok ? '#11aa44' : '#0a0a1a';
  const shoeA  = ok ? '#33ffaa' : '#ffdd00';
  const shoeB  = ok ? '#22ee88' : '#ff8800';
  const hairC  = '#1a0800';
  const sockC  = '#eeeeee';

  const spd = g.speed || 1;
  const t = g.frame * (0.20 + spd * 0.016);

  // ombre
  const shW = st === 'slide' ? 44 : 22;
  const shSc = Math.max(.3, 1 - jh * .3);
  ctx.save(); ctx.globalAlpha = .4 * shSc;
  ctx.fillStyle = '#000';
  ctx.beginPath(); ctx.ellipse(SX, GY + 2, shW * shSc, 5 * shSc, 0, 0, Math.PI * 2); ctx.fill();
  ctx.restore();

  ctx.save();
  ctx.translate(SX, baseY);

  if (st === 'slide') {
    ctx.save();
    ctx.translate(0, -14);
    ctx.fillStyle = kitBot; ctx.beginPath(); ctx.roundRect(-10, 0, 50, 12, 5); ctx.fill();
    ctx.fillStyle = sockC;  ctx.beginPath(); ctx.roundRect(32, 2, 10, 8, 2); ctx.fill();
    ctx.fillStyle = shoeA;  ctx.beginPath(); ctx.roundRect(38, 0, 14, 10, 3); ctx.fill();
    ctx.fillStyle = kitTop; ctx.beginPath(); ctx.roundRect(-28, -12, 42, 14, 6); ctx.fill();
    ctx.fillStyle = 'rgba(255,255,255,.75)'; ctx.beginPath(); ctx.roundRect(-20, -10, 16, 10, 3); ctx.fill();
    ctx.fillStyle = kitTop; ctx.font = 'bold 9px monospace'; ctx.textAlign = 'center'; ctx.fillText('17', -12, -4);
    ctx.fillStyle = skinC; ctx.beginPath(); ctx.ellipse(-32, -6, 10, 8, 0, 0, Math.PI * 2); ctx.fill();
    ctx.fillStyle = hairC; ctx.beginPath(); ctx.ellipse(-32, -6, 10, 8, 0, Math.PI, 0); ctx.fill();
    ctx.fillStyle = kitTop; ctx.beginPath(); ctx.roundRect(10, -18, 28, 8, 4); ctx.fill();
    ctx.restore();

  } else {
    const inAir = jPx > 4;
    const liftL  = inAir ? 0.5 : Math.max(0, Math.sin(t));
    const liftR  = inAir ? 0.5 : Math.max(0, Math.sin(t + Math.PI));
    const armSwL = inAir ? 0 : Math.sin(t + Math.PI) * 0.4;
    const armSwR = inAir ? 0 : Math.sin(t) * 0.4;
    const bounce = inAir ? 0 : -Math.abs(Math.sin(t)) * 3;

    function drawLeg(ox, lift, shoe) {
      const kneeY  = 14 - lift * 18;
      const footY2 = kneeY + 20 - lift * 6;
      ctx.save();
      ctx.translate(ox, -26 + bounce);
      ctx.lineCap = 'round';
      ctx.strokeStyle = kitBot; ctx.lineWidth = 10;
      ctx.beginPath(); ctx.moveTo(0, 0); ctx.lineTo(0, kneeY); ctx.stroke();
      ctx.lineWidth = 8;
      ctx.beginPath(); ctx.moveTo(0, kneeY); ctx.lineTo(0, footY2); ctx.stroke();
      ctx.fillStyle = sockC;
      ctx.beginPath(); ctx.roundRect(-5, footY2 - 3, 10, 6, 2); ctx.fill();
      ctx.fillStyle = shoe;
      ctx.beginPath(); ctx.roundRect(-8, footY2 + 2, 16, 8, 3); ctx.fill();
      ctx.restore();
    }

    function drawArm(ox, swing) {
      const thickness = 8 - Math.abs(swing) * 3;
      ctx.save();
      ctx.translate(ox, -62 + bounce);
      ctx.strokeStyle = kitTop; ctx.lineWidth = Math.max(4, thickness); ctx.lineCap = 'round';
      const elbowX = ox * 0.3 * swing;
      ctx.beginPath(); ctx.moveTo(0, 0); ctx.lineTo(elbowX, 16); ctx.stroke();
      const handX = elbowX - ox * 0.2;
      ctx.lineWidth = Math.max(4, thickness - 1);
      ctx.beginPath(); ctx.moveTo(elbowX, 16); ctx.lineTo(handX, 30); ctx.stroke();
      ctx.fillStyle = skinC; ctx.beginPath(); ctx.arc(handX, 30, 4, 0, Math.PI * 2); ctx.fill();
      ctx.restore();
    }

    // jambe arrière
    if (liftL <= liftR) drawLeg(-9, liftL, shoeA);
    else                 drawLeg(9,  liftR, shoeB);

    // torse
    ctx.save();
    ctx.translate(0, bounce);
    ctx.fillStyle = kitBot; ctx.beginPath(); ctx.roundRect(-13, -28, 26, 10, 4); ctx.fill();
    ctx.fillStyle = kitTop;
    ctx.beginPath();
    ctx.moveTo(-15, -62); ctx.lineTo(15, -62);
    ctx.lineTo(13, -28);  ctx.lineTo(-13, -28);
    ctx.closePath(); ctx.fill();
    ctx.save(); ctx.globalAlpha = .18; ctx.strokeStyle = '#000'; ctx.lineWidth = 1.5;
    ctx.beginPath(); ctx.moveTo(0, -58); ctx.lineTo(0, -32); ctx.stroke();
    ctx.restore();
    ctx.fillStyle = 'rgba(255,255,255,.80)'; ctx.beginPath(); ctx.roundRect(-9, -54, 18, 14, 3); ctx.fill();
    ctx.fillStyle = kitTop; ctx.font = 'bold 10px monospace'; ctx.textAlign = 'center';
    ctx.fillText('17', 0, -45);
    ctx.fillStyle = skinC; ctx.beginPath(); ctx.roundRect(-4, -67, 8, 8, 3); ctx.fill();
    ctx.fillStyle = skinC; ctx.beginPath(); ctx.arc(0, -76, 11, 0, Math.PI * 2); ctx.fill();
    ctx.fillStyle = hairC; ctx.beginPath(); ctx.arc(0, -76, 11, Math.PI, 0); ctx.fill();
    ctx.fillStyle = skinC;
    ctx.beginPath(); ctx.ellipse(-11, -76, 3, 4, 0, 0, Math.PI * 2); ctx.fill();
    ctx.beginPath(); ctx.ellipse(11,  -76, 3, 4, 0, 0, Math.PI * 2); ctx.fill();
    ctx.fillStyle = '#fff'; ctx.beginPath(); ctx.roundRect(-11, -83, 22, 4, 2); ctx.fill();
    ctx.restore();

    // jambe avant
    if (liftL <= liftR) drawLeg(9,  liftR, shoeB);
    else                 drawLeg(-9, liftL, shoeA);

    drawArm(-16, armSwL);
    drawArm(16,  armSwR);
  }

  ctx.restore();
}

// ── HINT ───────────────────────────────────────────────
function drawHint(g) {
  const next = g.obs.filter(o => !o.done && o.z > g.scroll + 1).sort((a, b) => a.z - b.z)[0];
  if (!next) return;
  const dist = next.z - g.scroll - 2;
  if (dist > 22 || dist < 0) return;
  const urg = Math.max(0, Math.min(1, 1 - dist / 18));
  const { free, obs } = next.pat, pl = g.lane;
  let txt = '', col = '#44aaff', tc = '#001840';
  if (pl !== free)          { txt = free < pl ? '← GAUCHE' : '→ DROITE'; col = '#ff8833'; tc = '#3a1800'; }
  else if (obs === 'jump')  { txt = '↑ SAUTER'; col = '#ffcc22'; tc = '#4a2c00'; }
  else if (obs === 'tunnel'){ txt = '↓ SLIDER'; col = '#44aaff'; tc = '#001840'; }
  else                      { txt = 'COURIR';   col = '#44ee88'; tc = '#003820'; }
  const pw = 200, ph = 34, px = W / 2 - pw / 2, py = 8;
  ctx.globalAlpha = .12 + urg * .82; ctx.fillStyle = col; ctx.beginPath(); ctx.roundRect(px, py, pw, ph, 8); ctx.fill(); ctx.globalAlpha = 1;
  ctx.fillStyle = tc; ctx.font = `bold ${13 + Math.round(urg * 3)}px monospace`; ctx.textAlign = 'center'; ctx.fillText(txt, W / 2, py + ph * .68);
  const bw = pw * Math.max(0, 1 - dist / 20);
  ctx.fillStyle = 'rgba(255,255,255,.05)'; ctx.beginPath(); ctx.roundRect(px, py + ph + 2, pw, 4, 2); ctx.fill();
  ctx.fillStyle = col; ctx.globalAlpha = .4 + urg * .6; ctx.beginPath(); ctx.roundRect(px, py + ph + 2, bw, 4, 2); ctx.fill(); ctx.globalAlpha = 1;
}

function drawLvFlash(g) {
  if (g.lvFlash <= 0) return;
  const a = Math.min(1, g.lvFlash / 20) * .9;
  ctx.globalAlpha = a * .12; ctx.fillStyle = '#ffdd44'; ctx.fillRect(0, 0, W, H); ctx.globalAlpha = a;
  ctx.fillStyle = '#ffee66'; ctx.font = 'bold 26px monospace'; ctx.textAlign = 'center'; ctx.fillText(g.lvLabel, W / 2, H / 2);
  ctx.globalAlpha = 1; g.lvFlash--;
}

function burst(g, col, n) {
  const cx = VPX, cy = H * .75;
  for (let i = 0; i < n; i++) g.parts.push({ x: cx, y: cy, vx: (Math.random() - .5) * 11, vy: (Math.random() - .5) * 11 - 7, life: 1, color: col, r: 2 + Math.random() * 5 });
}
function win(g, col) { g.combo++; g.score += 12 + Math.floor(g.speed * 7); g.okFlash = 12; burst(g, col, 14); }

// ── COLLISION ──────────────────────────────────────────
function checkColl(g) {
  for (const o of g.obs) {
    if (o.done || o.hit) continue;
    const rz = o.z - g.scroll;
    if (rz > HIT_N || rz < HIT_F) continue;
    o.hit = true;
    const pl = g.lane, { free, obs } = o.pat;
    if (pl !== free) return true;
    if (obs === 'jump')   { if (g.posY > .20) { o.done = true; win(g, '#ffcc22'); return false; } return true; }
    if (obs === 'tunnel') { if (g.state === 'slide') { o.done = true; win(g, '#44aaff'); return false; } return true; }
    o.done = true; win(g, '#44ee88'); return false;
  }
  return false;
}

// ── MAIN LOOP ──────────────────────────────────────────
let last = 0;
function loop(ts) {
  const dt = Math.min((ts - last) / 16.67, 2.0); last = ts;
  const g = G, lv = getLv(g.score);

  ctx.fillStyle = '#04060f'; ctx.fillRect(0, 0, W, H);
  for (let i = 0; i < 14; i++) {
    const t = i / 14;
    ctx.fillStyle = `rgb(${Math.round(4 + t * 18)},${Math.round(6 + t * 14)},${Math.round(15 + t * 25)})`;
    ctx.fillRect(0, i * (VPY / 14), W, VPY / 14 + 2);
  }
  if (!g.stars) { g.stars = []; for (let i = 0; i < 90; i++) g.stars.push({ x: Math.random() * W, y: Math.random() * VPY * .9, r: Math.random() * 1.5 + .2, b: Math.random() }); }
  g.stars.forEach(s => {
    ctx.globalAlpha = .3 + s.b * .6 + Math.sin(g.frame * .012 + s.b * 7) * .14;
    ctx.fillStyle = '#fff'; ctx.beginPath(); ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2); ctx.fill();
  });
  ctx.globalAlpha = 1;
  ctx.fillStyle = 'rgba(20,40,100,.18)'; ctx.beginPath(); ctx.ellipse(W / 2, VPY, W * .42, 20, 0, 0, Math.PI * 2); ctx.fill();
  ctx.fillStyle = 'rgba(20,40,100,.08)'; ctx.beginPath(); ctx.ellipse(W / 2, VPY, W * .6, 40, 0, 0, Math.PI * 2); ctx.fill();

  if (!g.running && !g.dead) {
    g.scroll += .016; spawnAll(g);
    CAM_X = g.laneX;
    drawTrack(g); drawObs(g); drawPlayer(g);
    ctx.fillStyle = 'rgba(4,6,15,.7)'; ctx.fillRect(0, 0, W, H);
    ctx.fillStyle = '#eedd55'; ctx.font = 'bold 30px monospace'; ctx.textAlign = 'center'; ctx.fillText('SUBWAY RUNNER', W / 2, H / 2 - 58);
    ctx.fillStyle = '#5588aa'; ctx.font = '13px monospace';
    ctx.fillText('ESPACE ou clic pour démarrer', W / 2, H / 2 - 20);
    ctx.fillText('← → Changer de voie', W / 2, H / 2 + 6);
    ctx.fillText('↑ Sauter la barre  |  ↓ Slider sous l\'arche', W / 2, H / 2 + 28);
    requestAnimationFrame(loop); return;
  }

  if (g.dead) {
    drawTrack(g); drawObs(g);
    g.parts.forEach(p => { p.x += p.vx * dt; p.y += p.vy * dt; p.vy += .28 * dt; p.life -= .024 * dt; });
    g.parts = g.parts.filter(p => p.life > 0);
    g.parts.forEach(p => { ctx.globalAlpha = Math.max(0, p.life); ctx.fillStyle = p.color; ctx.beginPath(); ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2); ctx.fill(); });
    ctx.globalAlpha = 1;
    ctx.fillStyle = 'rgba(4,6,15,.84)'; ctx.fillRect(0, 0, W, H);
    ctx.fillStyle = '#ff4444'; ctx.font = 'bold 28px monospace'; ctx.textAlign = 'center'; ctx.fillText('GAME OVER', W / 2, H / 2 - 32);
    ctx.fillStyle = '#f0c040'; ctx.font = 'bold 15px monospace'; ctx.fillText(lv.label, W / 2, H / 2 - 6);
    ctx.fillStyle = '#6688aa'; ctx.font = '13px monospace';
    ctx.fillText(`Score : ${Math.floor(g.score)}`, W / 2, H / 2 + 18);
    ctx.fillText('ESPACE ou clic pour rejouer', W / 2, H / 2 + 42);
    requestAnimationFrame(loop); return;
  }

  g.frame += dt;
  g.score += lv.spd * .045 * dt;
  g.scroll += .053 * lv.spd * dt;
  g.speed = lv.spd;
  if (g.prevLv && g.prevLv !== lv.label) { g.lvFlash = 55; g.lvLabel = lv.label + ' !'; }
  g.prevLv = lv.label;

  while (g.queue.length > 0) applyAction(g, g.queue.shift());

  if (g.state === 'jump') {
    g.velY -= .32 * dt;
    g.posY += g.velY * dt;
    if (g.posY <= 0) {
      g.posY = 0; g.velY = 0;
      if (g.slideAfterLand) { g.state = 'slide'; g.slideT = 0; g.slideAfterLand = false; }
      else g.state = 'run';
    }
  }
  if (g.state === 'slide') {
    g.slideT += dt;
    if (g.slideT > g.SLIDE_DUR) { g.state = 'run'; g.slideT = 0; }
  }

  g.laneX += (LANES[g.lane] - g.laneX) * .35 * dt * 3;
  CAM_X = g.laneX;

  if (g.okFlash > 0) g.okFlash -= dt;
  g.parts.forEach(p => { p.x += p.vx * dt; p.y += p.vy * dt; p.vy += .15 * dt; p.life -= .025 * dt; });
  g.parts = g.parts.filter(p => p.life > 0);

  spawnAll(g);
  if (checkColl(g)) { burst(g, '#ff4433', 24); g.dead = true; requestAnimationFrame(loop); return; }

  drawTrack(g); drawObs(g); drawHint(g); drawPlayer(g);
  g.parts.forEach(p => { ctx.globalAlpha = Math.max(0, p.life); ctx.fillStyle = p.color; ctx.beginPath(); ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2); ctx.fill(); });
  ctx.globalAlpha = 1;
  if (g.okFlash > 0) { ctx.fillStyle = `rgba(68,255,120,${(g.okFlash / 12) * .12})`; ctx.fillRect(0, 0, W, H); }
  drawLvFlash(g);

  const pct = Math.min(1, (lv.spd - 1) / 11);
  ctx.fillStyle = 'rgba(255,255,255,.04)'; ctx.beginPath(); ctx.roundRect(W - 98, 10, 84, 5, 2); ctx.fill();
  ctx.fillStyle = pct < .3 ? '#44cc66' : pct < .6 ? '#f0c040' : pct < .82 ? '#ff8833' : '#ff4433';
  ctx.beginPath(); ctx.roundRect(W - 98, 10, 84 * pct, 5, 2); ctx.fill();
  ctx.fillStyle = 'rgba(255,255,255,.18)'; ctx.font = '9px monospace'; ctx.textAlign = 'right'; ctx.fillText(lv.label, W - 10, 23);

  document.getElementById('sc').textContent = Math.floor(g.score);
  document.getElementById('lv').textContent = lv.label;
  requestAnimationFrame(loop);
}

// ── INPUT ──────────────────────────────────────────────
const KM = { ArrowUp: 'up', ArrowDown: 'down', ArrowLeft: 'left', ArrowRight: 'right', ' ': 'sp' };
document.addEventListener('keydown', e => {
  const k = KM[e.key]; if (!k) return; e.preventDefault();
  if (k === 'sp') { if (!G || G.dead || !G.running) startGame(); return; }
  if (!G || G.dead || !G.running) return;
  if (k === 'left')  G.queue.push('left');
  if (k === 'right') G.queue.push('right');
  if (k === 'up')    G.queue.push('jump');
  if (k === 'down')  G.queue.push('slide');
  const m = { up: 'ku', down: 'kd', left: 'kl', right: 'kr' };
  const el = document.getElementById(m[k]); if (el) el.classList.add('on');
});
document.addEventListener('keyup', e => {
  const k = KM[e.key]; if (!k) return;
  if (k === 'down' && G && G.state === 'slide') G.state = 'run';
  const m = { up: 'ku', down: 'kd', left: 'kl', right: 'kr' };
  const el = document.getElementById(m[k]); if (el) el.classList.remove('on');
});
cv.addEventListener('click', () => { if (!G || G.dead || !G.running) startGame(); });
function startGame() { G = fresh(); G.running = true; G.prevLv = getLv(0).label; }
G = fresh(); requestAnimationFrame(loop);
