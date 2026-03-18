const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');
const nextCanvas = document.getElementById('nextCanvas');
const nCtx = nextCanvas.getContext('2d');
const overlay = document.getElementById('overlay');
const startBtn = document.getElementById('startBtn');
const scoreEl = document.getElementById('score');
const levelEl = document.getElementById('level');
const linesEl = document.getElementById('lines');
const overlayMsg = document.getElementById('overlay-msg');

const COLS = 10;
const ROWS = 20;
const BLOCK = 30;

const COLORS = {
  I: '#00d4ff',
  O: '#ffdd00',
  T: '#bf00ff',
  S: '#00ff88',
  Z: '#ff3366',
  J: '#0044ff',
  L: '#ff8800'
};

const PIECES = {
  I: [[1,1,1,1]],
  O: [[1,1],[1,1]],
  T: [[0,1,0],[1,1,1]],
  S: [[0,1,1],[1,1,0]],
  Z: [[1,1,0],[0,1,1]],
  J: [[1,0,0],[1,1,1]],
  L: [[0,0,1],[1,1,1]]
};

let board, score, level, lines, current, next, gameLoop, running, paused;

function emptyBoard() {
  return Array.from({ length: ROWS }, () => Array(COLS).fill(0));
}

function randomPiece() {
  const keys = Object.keys(PIECES);
  const key = keys[Math.floor(Math.random() * keys.length)];
  return {
    type: key,
    shape: PIECES[key].map(r => [...r]),
    color: COLORS[key],
    x: Math.floor(COLS / 2) - Math.floor(PIECES[key][0].length / 2),
    y: 0
  };
}

function rotate(shape) {
  return shape[0].map((_, i) => shape.map(r => r[i]).reverse());
}

function valid(shape, ox, oy) {
  for (let r = 0; r < shape.length; r++) {
    for (let c = 0; c < shape[r].length; c++) {
      if (!shape[r][c]) continue;
      const nx = ox + c, ny = oy + r;
      if (nx < 0 || nx >= COLS || ny >= ROWS) return false;
      if (ny >= 0 && board[ny][nx]) return false;
    }
  }
  return true;
}

function place() {
  for (let r = 0; r < current.shape.length; r++) {
    for (let c = 0; c < current.shape[r].length; c++) {
      if (!current.shape[r][c]) continue;
      const ny = current.y + r;
      if (ny < 0) { gameOver(); return; }
      board[ny][current.x + c] = current.color;
    }
  }
  clearLines();
  current = next;
  next = randomPiece();
  drawNext();
  if (!valid(current.shape, current.x, current.y)) {
    gameOver();
  }
}

function clearLines() {
  let cleared = 0;
  for (let r = ROWS - 1; r >= 0; r--) {
    if (board[r].every(c => c)) {
      board.splice(r, 1);
      board.unshift(Array(COLS).fill(0));
      cleared++;
      r++;
    }
  }
  if (cleared) {
    const pts = [0, 100, 300, 500, 800];
    score += (pts[cleared] || 800) * level;
    lines += cleared;
    level = Math.floor(lines / 10) + 1;
    scoreEl.textContent = score;
    levelEl.textContent = level;
    linesEl.textContent = lines;
    clearInterval(gameLoop);
    gameLoop = setInterval(tick, Math.max(100, 500 - (level - 1) * 40));
  }
}

function drawBlock(context, x, y, color, size = BLOCK) {
  context.fillStyle = color;
  context.shadowColor = color;
  context.shadowBlur = 8;
  context.fillRect(x * size + 1, y * size + 1, size - 2, size - 2);
  context.shadowBlur = 0;
}

function draw() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);

  // Grille
  ctx.strokeStyle = 'rgba(0,255,136,0.05)';
  ctx.lineWidth = 0.5;
  for (let r = 0; r < ROWS; r++)
    for (let c = 0; c < COLS; c++)
      ctx.strokeRect(c * BLOCK, r * BLOCK, BLOCK, BLOCK);

  // Board
  for (let r = 0; r < ROWS; r++)
    for (let c = 0; c < COLS; c++)
      if (board[r][c]) drawBlock(ctx, c, r, board[r][c]);

  // Ghost
  let ghostY = current.y;
  while (valid(current.shape, current.x, ghostY + 1)) ghostY++;
  for (let r = 0; r < current.shape.length; r++)
    for (let c = 0; c < current.shape[r].length; c++)
      if (current.shape[r][c]) {
        ctx.fillStyle = 'rgba(255,255,255,0.08)';
        ctx.fillRect((current.x + c) * BLOCK + 1, (ghostY + r) * BLOCK + 1, BLOCK - 2, BLOCK - 2);
      }

  // Pièce courante
  for (let r = 0; r < current.shape.length; r++)
    for (let c = 0; c < current.shape[r].length; c++)
      if (current.shape[r][c]) drawBlock(ctx, current.x + c, current.y + r, current.color);
}

function drawNext() {
  nCtx.clearRect(0, 0, nextCanvas.width, nextCanvas.height);
  const size = 24;
  const ox = Math.floor((4 - next.shape[0].length) / 2);
  const oy = Math.floor((4 - next.shape.length) / 2);
  for (let r = 0; r < next.shape.length; r++)
    for (let c = 0; c < next.shape[r].length; c++)
      if (next.shape[r][c]) {
        nCtx.fillStyle = next.color;
        nCtx.shadowColor = next.color;
        nCtx.shadowBlur = 6;
        nCtx.fillRect((ox + c) * size + 1, (oy + r) * size + 1, size - 2, size - 2);
        nCtx.shadowBlur = 0;
      }
}

function tick() {
  if (!running || paused) return;
  if (valid(current.shape, current.x, current.y + 1)) {
    current.y++;
  } else {
    place();
  }
  draw();
}

function gameOver() {
  running = false;
  clearInterval(gameLoop);
  overlayMsg.textContent = 'SCORE : ' + score;
  startBtn.textContent = '▶ REJOUER';
  overlay.style.display = 'flex';
}

function startGame() {
  overlay.style.display = 'none';
  board = emptyBoard();
  score = 0; level = 1; lines = 0;
  scoreEl.textContent = 0;
  levelEl.textContent = 1;
  linesEl.textContent = 0;
  current = randomPiece();
  next = randomPiece();
  running = true;
  paused = false;
  drawNext();
  clearInterval(gameLoop);
  gameLoop = setInterval(tick, 500);
}

startBtn.addEventListener('click', startGame);

document.addEventListener('keydown', e => {
  if (!running || paused) {
    if (e.key === 'p' || e.key === 'P') paused = !paused;
    return;
  }
  switch(e.key) {
    case 'ArrowLeft':  case 'q': case 'Q':
      if (valid(current.shape, current.x - 1, current.y)) current.x--; break;
    case 'ArrowRight': case 'd': case 'D':
      if (valid(current.shape, current.x + 1, current.y)) current.x++; break;
    case 'ArrowDown':  case 's': case 'S':
      if (valid(current.shape, current.x, current.y + 1)) current.y++;
      else place(); break;
    case 'ArrowUp': case 'z': case 'Z': {
      const rot = rotate(current.shape);
      if (valid(rot, current.x, current.y)) current.shape = rot; break;
    }
    case ' ':
      while (valid(current.shape, current.x, current.y + 1)) current.y++;
      place(); break;
    case 'p': case 'P':
      paused = !paused; break;
  }
  if (['ArrowUp','ArrowDown','ArrowLeft','ArrowRight',' '].includes(e.key)) {
    e.preventDefault();
  }
  draw();
});