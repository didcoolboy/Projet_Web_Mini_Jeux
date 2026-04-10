const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');
const overlay = document.getElementById('overlay');
const startBtn = document.getElementById('startBtn');
const scoreEl = document.getElementById('score');
const highscoreEl = document.getElementById('highscore');
const overlayMsg = document.getElementById('overlay-msg');

const GRID = 20;
const COLS = canvas.width / GRID;
const ROWS = canvas.height / GRID;
const COLORS = ['#00ff88','#00d4ff','#bf00ff','#ffdd00'];

let snake, dir, nextDir, food, score, highscore, gameLoop, paused, running;
highscore = 0;

function init() {
  snake = [
    { x: 12, y: 12 },
    { x: 11, y: 12 },
    { x: 10, y: 12 },
  ];
  dir = { x: 1, y: 0 };
  nextDir = { x: 1, y: 0 };
  score = 0;
  paused = false;
  running = true;
  scoreEl.textContent = 0;
  spawnFood();
}

function spawnFood() {
  let pos;
  do {
    pos = {
      x: Math.floor(Math.random() * COLS),
      y: Math.floor(Math.random() * ROWS),
      color: COLORS[Math.floor(Math.random() * COLORS.length)]
    };
  } while (snake.some(s => s.x === pos.x && s.y === pos.y));
  food = pos;
}

function update() {
  if (!running || paused) return;

  dir = { ...nextDir };
  const head = { x: snake[0].x + dir.x, y: snake[0].y + dir.y };

  if (head.x < 0 || head.x >= COLS || head.y < 0 || head.y >= ROWS) {
    return gameOver();
  }
  if (snake.some(s => s.x === head.x && s.y === head.y)) {
    return gameOver();
  }

  snake.unshift(head);

  if (head.x === food.x && head.y === food.y) {
    score++;
    scoreEl.textContent = score;
    if (score > highscore) {
      highscore = score;
      highscoreEl.textContent = highscore;
    }
    spawnFood();
  } else {
    snake.pop();
  }
}

function draw() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);

  ctx.strokeStyle = 'rgba(0,255,136,0.05)';
  ctx.lineWidth = 0.5;
  for (let x = 0; x < COLS; x++) {
    for (let y = 0; y < ROWS; y++) {
      ctx.strokeRect(x * GRID, y * GRID, GRID, GRID);
    }
  }

  ctx.fillStyle = food.color;
  ctx.shadowColor = food.color;
  ctx.shadowBlur = 15;
  ctx.fillRect(food.x * GRID + 2, food.y * GRID + 2, GRID - 4, GRID - 4);
  ctx.shadowBlur = 0;

  snake.forEach((seg, i) => {
    const ratio = i / snake.length;
    ctx.fillStyle = i === 0 ? '#00ff88' : `rgba(0,255,136,${1 - ratio * 0.7})`;
    ctx.shadowColor = '#00ff88';
    ctx.shadowBlur = i === 0 ? 15 : 5;
    ctx.fillRect(seg.x * GRID + 1, seg.y * GRID + 1, GRID - 2, GRID - 2);
  });
  ctx.shadowBlur = 0;
}

function gameOver() {
  running = false;
  clearInterval(gameLoop);
  overlayMsg.textContent = 'SCORE : ' + score;
  startBtn.textContent = '▶ REJOUER';
  overlay.style.display = 'flex';

  // Enregistrer le score si l'utilisateur est connecté
  saveScore(score, 'snake');
}

function startGame() {
  overlay.style.display = 'none';
  init();
  clearInterval(gameLoop);
  gameLoop = setInterval(() => {
    update();
    draw();
  }, 120);
}

startBtn.addEventListener('click', startGame);

document.addEventListener('keydown', e => {
  switch(e.key) {
    case 'ArrowUp':    case 'z': case 'Z':
      if (dir.y !== 1)  nextDir = { x: 0, y: -1 }; break;
    case 'ArrowDown':  case 's': case 'S':
      if (dir.y !== -1) nextDir = { x: 0, y: 1 };  break;
    case 'ArrowLeft':  case 'q': case 'Q':
      if (dir.x !== 1)  nextDir = { x: -1, y: 0 }; break;
    case 'ArrowRight': case 'd': case 'D':
      if (dir.x !== -1) nextDir = { x: 1, y: 0 };  break;
    case 'p': case 'P':
      paused = !paused; break;
  }
  if (['ArrowUp','ArrowDown','ArrowLeft','ArrowRight'].includes(e.key)) {
    e.preventDefault();
  }
});

draw();