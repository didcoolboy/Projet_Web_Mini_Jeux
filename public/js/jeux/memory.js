const board = document.getElementById('board');
const movesEl = document.getElementById('moves');
const pairsEl = document.getElementById('pairs');
const overlay = document.getElementById('overlay');
const startBtn = document.getElementById('startBtn');
const overlayMsg = document.getElementById('overlay-msg');

const EMOJIS = ['🎮','👾','🕹️','⭐','💎','🔥','⚡','🎯'];

let flipped, matched, moves, lock;

function shuffle(arr) {
  return [...arr].sort(() => Math.random() - 0.5);
}

function init() {
  board.innerHTML = '';
  flipped = [];
  matched = 0;
  moves = 0;
  lock = false;
  movesEl.textContent = 0;
  pairsEl.textContent = 0;

  const cards = shuffle([...EMOJIS, ...EMOJIS]);

  cards.forEach(emoji => {
    const card = document.createElement('div');
    card.className = 'card';
    card.dataset.emoji = emoji;
    card.innerHTML = `
      <div class="card-inner">
        <div class="card-front">?</div>
        <div class="card-back">${emoji}</div>
      </div>
    `;
    card.addEventListener('click', () => flipCard(card));
    board.appendChild(card);
  });
}

function flipCard(card) {
  if (lock || card.classList.contains('flipped') || card.classList.contains('matched')) return;

  card.classList.add('flipped');
  flipped.push(card);

  if (flipped.length === 2) {
    lock = true;
    moves++;
    movesEl.textContent = moves;
    checkMatch();
  }
}

function checkMatch() {
  const [a, b] = flipped;
  if (a.dataset.emoji === b.dataset.emoji) {
    a.classList.add('matched');
    b.classList.add('matched');
    matched++;
    pairsEl.textContent = matched;
    flipped = [];
    lock = false;
    if (matched === EMOJIS.length) {
      setTimeout(() => {
        overlayMsg.textContent = moves + ' COUPS !';
        startBtn.textContent = '▶ REJOUER';
        overlay.style.display = 'flex';
        
        // Enregistrer le score (nombre de coups)
        saveScore(moves, 'memory');
      }, 600);
    }
  } else {
    setTimeout(() => {
      a.classList.remove('flipped');
      b.classList.remove('flipped');
      flipped = [];
      lock = false;
    }, 900);
  }
}

function startGame() {
  overlay.style.display = 'none';
  init();
}

startBtn.addEventListener('click', startGame);