const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');
const overlay = document.getElementById('overlay');
const startBtn = document.getElementById('startBtn');
const scoreEl = document.getElementById('score');
const bestEl = document.getElementById('best');
const overlayMsg = document.getElementById('overlay-msg');

const W = canvas.width;
const H = canvas.height;

const BIRD_X = 80;
const BIRD_SIZE = 20;
const GRAVITY = 0.45;
const JUMP = -8;
const PIPE_W = 50;
const PIPE_GAP = 160;
const PIPE_SPEED = 3;
const PIPE_INTERVAL = 1500;

let bird, pipes, score, best, running, animFrame, lastPipe;
best = 0;

function init() {
  bird = { y: H / 2, vy: 0, angle: 0 };
  pipes = [];
  score = 0;
  scoreEl.textContent = 0;
  lastPipe = Date.now();
  running = true;
}

function jump() {
  if (!running) return;
  bird.vy = JUMP;
}

function spawnPipe() {
  const top = Math.random() * (H - PIPE_GAP - 100) + 50;
  pipes.push({ x: W, top, scored: false });
}

function update() {
  if (!running) return;

  // Bird
  bird.vy += GRAVITY;
  bird.y += bird.vy;
  bird.angle = Math.min(Math.max(bird.vy * 3, -30), 90);

  // Sol / plafond
  if (bird.y <= 0 || bird.y + BIRD_SIZE >= H) return gameOver();

  // Spawn pipes
  if (Date.now() - lastPipe > PIPE_INTERVAL) {
    spawnPipe();
    lastPipe = Date.now();
  }

  // Pipes
  for (let i = pipes.length - 1; i >= 0; i--) {
    const p = pipes[i];
    p.x -= PIPE_SPEED;

    // Score
    if (!p.scored && p.x + PIPE_W < BIRD_X) {
      p.scored = true;
      score++;
      scoreEl.textContent = score;
      if (score > best) {
        best = score;
        bestEl.textContent = best;
      }
    }

    // Collision
    const bx = BIRD_X, by = bird.y;
    if (
      bx + BIRD_SIZE > p.x && bx < p.x + PIPE_W &&
      (by < p.top || by + BIRD_SIZE > p.top + PIPE_GAP)
    ) return gameOver();

    if (p.x + PIPE_W < 0) pipes.splice(i, 1);
  }
}

function drawBird() {
  ctx.save();
  ctx.translate(BIRD_X + BIRD_SIZE / 2, bird.y + BIRD_SIZE / 2);
  ctx.rotate(bird.angle * Math.PI / 180);

  // Corps
  ctx.fillStyle = '#ffdd00';
  ctx.shadowColor = '#ffdd00';
  ctx.shadowBlur = 15;
  ctx.fillRect(-BIRD_SIZE / 2, -BIRD_SIZE / 2, BIRD_SIZE, BIRD_SIZE);

  // Oeil
  ctx.fillStyle = '#000';
  ctx.shadowBlur = 0;
  ctx.fillRect(4, -6, 5, 5);

  // Bec
  ctx.fillStyle = '#ff8800';
  ctx.fillRect(8, -2, 8, 4);

  ctx.restore();
}

function drawPipes(p) {
  // Tuyau haut
  ctx.fillStyle = '#00ff88';
  ctx.shadowColor = '#00ff88';
  ctx.shadowBlur = 10;
  ctx.fillRect(p.x, 0, PIPE_W, p.top);

  // Chapeau haut
  ctx.fillRect(p.x - 5, p.top - 20, PIPE_W + 10, 20);

  // Tuyau bas
  ctx.fillRect(p.x, p.top + PIPE_GAP, PIPE_W, H - p.top - PIPE_GAP);

  // Chapeau bas
  ctx.fillRect(p.x - 5, p.top + PIPE_GAP, PIPE_W + 10, 20);

  ctx.shadowBlur = 0;
}

function draw() {
  ctx.clearRect(0, 0, W, H);

  // Grille
  ctx.strokeStyle = 'rgba(0,255,136,0.04)';
  ctx.lineWidth = 0.5;
  for (let x = 0; x < W; x += 32) {
    ctx.beginPath(); ctx.moveTo(x, 0); ctx.lineTo(x, H); ctx.stroke();
  }
  for (let y = 0; y < H; y += 32) {
    ctx.beginPath(); ctx.moveTo(0, y); ctx.lineTo(W, y); ctx.stroke();
  }

  pipes.forEach(drawPipes);
  drawBird();
}

function gameOver() {
  running = false;
  cancelAnimationFrame(animFrame);
  overlayMsg.textContent = 'SCORE : ' + score;
  startBtn.textContent = '▶ REJOUER';
  overlay.style.display = 'flex';
}

function loop() {
  update();
  draw();
  if (running) animFrame = requestAnimationFrame(loop);
}

function startGame() {
  overlay.style.display = 'none';
  cancelAnimationFrame(animFrame);
  init();
  animFrame = requestAnimationFrame(loop);
}

startBtn.addEventListener('click', startGame);

document.addEventListener('keydown', e => {
  if (e.code === 'Space' || e.key === 'ArrowUp' || e.key === 'z' || e.key === 'Z') {
    e.preventDefault();
    jump();
  }
});

canvas.addEventListener('click', jump);