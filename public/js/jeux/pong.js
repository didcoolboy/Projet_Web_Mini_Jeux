const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');
const overlay = document.getElementById('overlay');
const startBtn = document.getElementById('startBtn');
const overlayMsg = document.getElementById('overlay-msg');
const scoreLeftEl = document.getElementById('scoreLeft');
const scoreRightEl = document.getElementById('scoreRight');

const W = canvas.width;
const H = canvas.height;
const PADDLE_W = 12;
const PADDLE_H = 80;
const BALL_SIZE = 10;
const WIN_SCORE = 7;
const PADDLE_SPEED = 6;

let ball, paddleL, paddleR, scoreL, scoreR, running, animFrame;
const keys = {};

function init() {
  paddleL = { x: 20, y: H / 2 - PADDLE_H / 2 };
  paddleR = { x: W - 20 - PADDLE_W, y: H / 2 - PADDLE_H / 2 };
  resetBall();
  scoreL = 0;
  scoreR = 0;
  scoreLeftEl.textContent = 0;
  scoreRightEl.textContent = 0;
}

function resetBall() {
  const angle = (Math.random() * 60 - 30) * Math.PI / 180;
  const dir = Math.random() > 0.5 ? 1 : -1;
  ball = {
    x: W / 2,
    y: H / 2,
    vx: Math.cos(angle) * 5 * dir,
    vy: Math.sin(angle) * 5,
    speed: 5
  };
}

function update() {
  if (!running) return;

  // Paddles joueur (W/S) et IA droite
  if (keys['w'] || keys['W'] || keys['ArrowUp']) {
    paddleL.y = Math.max(0, paddleL.y - PADDLE_SPEED);
  }
  if (keys['s'] || keys['S'] || keys['ArrowDown']) {
    paddleL.y = Math.min(H - PADDLE_H, paddleL.y + PADDLE_SPEED);
  }

  // IA paddle droit
  const centerR = paddleR.y + PADDLE_H / 2;
  if (centerR < ball.y - 5) paddleR.y = Math.min(H - PADDLE_H, paddleR.y + 4);
  if (centerR > ball.y + 5) paddleR.y = Math.max(0, paddleR.y - 4);

  // Balle
  ball.x += ball.vx;
  ball.y += ball.vy;

  // Rebond haut/bas
  if (ball.y <= 0) { ball.y = 0; ball.vy *= -1; }
  if (ball.y >= H - BALL_SIZE) { ball.y = H - BALL_SIZE; ball.vy *= -1; }

  // Rebond paddle gauche
  if (
    ball.x <= paddleL.x + PADDLE_W &&
    ball.x >= paddleL.x &&
    ball.y + BALL_SIZE >= paddleL.y &&
    ball.y <= paddleL.y + PADDLE_H
  ) {
    ball.x = paddleL.x + PADDLE_W;
    const hit = (ball.y + BALL_SIZE / 2 - paddleL.y) / PADDLE_H - 0.5;
    ball.speed = Math.min(ball.speed + 0.3, 12);
    ball.vy = hit * ball.speed * 2.5;
    ball.vx = Math.abs(ball.vx) * (ball.speed / Math.abs(ball.vx));
  }

  // Rebond paddle droit
  if (
    ball.x + BALL_SIZE >= paddleR.x &&
    ball.x + BALL_SIZE <= paddleR.x + PADDLE_W &&
    ball.y + BALL_SIZE >= paddleR.y &&
    ball.y <= paddleR.y + PADDLE_H
  ) {
    ball.x = paddleR.x - BALL_SIZE;
    const hit = (ball.y + BALL_SIZE / 2 - paddleR.y) / PADDLE_H - 0.5;
    ball.speed = Math.min(ball.speed + 0.3, 12);
    ball.vy = hit * ball.speed * 2.5;
    ball.vx = -Math.abs(ball.vx) * (ball.speed / Math.abs(ball.vx));
  }

  // Point
  if (ball.x < 0) {
    scoreR++;
    scoreRightEl.textContent = scoreR;
    if (scoreR >= WIN_SCORE) return endGame('IA');
    resetBall();
  }
  if (ball.x > W) {
    scoreL++;
    scoreLeftEl.textContent = scoreL;
    if (scoreL >= WIN_SCORE) return endGame('JOUEUR');
    resetBall();
  }
}

function draw() {
  ctx.clearRect(0, 0, W, H);

  // Ligne centrale
  ctx.setLineDash([10, 10]);
  ctx.strokeStyle = 'rgba(0,255,136,0.15)';
  ctx.lineWidth = 2;
  ctx.beginPath();
  ctx.moveTo(W / 2, 0);
  ctx.lineTo(W / 2, H);
  ctx.stroke();
  ctx.setLineDash([]);

  // Paddles
  ctx.fillStyle = '#00ff88';
  ctx.shadowColor = '#00ff88';
  ctx.shadowBlur = 15;
  ctx.fillRect(paddleL.x, paddleL.y, PADDLE_W, PADDLE_H);
  ctx.fillStyle = '#bf00ff';
  ctx.shadowColor = '#bf00ff';
  ctx.fillRect(paddleR.x, paddleR.y, PADDLE_W, PADDLE_H);

  // Balle
  ctx.fillStyle = '#ffdd00';
  ctx.shadowColor = '#ffdd00';
  ctx.shadowBlur = 20;
  ctx.fillRect(ball.x, ball.y, BALL_SIZE, BALL_SIZE);
  ctx.shadowBlur = 0;
}

function endGame(winner) {
  running = false;
  cancelAnimationFrame(animFrame);
  overlayMsg.textContent = winner + ' GAGNE !';
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
  init();
  running = true;
  animFrame = requestAnimationFrame(loop);
}

startBtn.addEventListener('click', startGame);

document.addEventListener('keydown', e => {
  keys[e.key] = true;
  if (['ArrowUp','ArrowDown',' '].includes(e.key)) e.preventDefault();
});
document.addEventListener('keyup', e => { keys[e.key] = false; });