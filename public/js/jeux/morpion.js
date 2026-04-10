const cells = document.querySelectorAll('.cell');
const status = document.getElementById('status');
const resetBtn = document.getElementById('resetBtn');
const scoreX = document.getElementById('scoreX');
const scoreO = document.getElementById('scoreO');
const scoreD = document.getElementById('scoreD');
const boxX = document.getElementById('boxX');
const boxO = document.getElementById('boxO');

const WINS = [
  [0,1,2],[3,4,5],[6,7,8],
  [0,3,6],[1,4,7],[2,5,8],
  [0,4,8],[2,4,6]
];

let board, current, gameOver, scores;
scores = { X: 0, O: 0, D: 0 };

function init() {
  board = Array(9).fill('');
  current = 'X';
  gameOver = false;
  cells.forEach(c => {
    c.textContent = '';
    c.className = 'cell';
  });
  updateStatus();
  updateActive();
}

function updateStatus() {
  status.textContent = gameOver ? '' : `TOUR DU JOUEUR ${current}`;
}

function updateActive() {
  boxX.classList.toggle('active', current === 'X');
  boxO.classList.toggle('active', current === 'O');
}

function checkWin() {
  for (const [a, b, c] of WINS) {
    if (board[a] && board[a] === board[b] && board[b] === board[c]) {
      cells[a].classList.add('win');
      cells[b].classList.add('win');
      cells[c].classList.add('win');
      return board[a];
    }
  }
  if (board.every(c => c !== '')) return 'D';
  return null;
}

cells.forEach((cell, i) => {
  cell.addEventListener('click', () => {
    if (gameOver || board[i]) return;

    board[i] = current;
    cell.textContent = current === 'X' ? '✕' : '○';
    cell.classList.add(current.toLowerCase(), 'taken');

    const result = checkWin();
    if (result) {
      gameOver = true;
      if (result === 'D') {
        status.textContent = 'MATCH NUL !';
        scores.D++;
        scoreD.textContent = scores.D;
      } else {
        status.textContent = `JOUEUR ${result} GAGNE !`;
        scores[result]++;
        result === 'X' ? scoreX.textContent = scores.X : scoreO.textContent = scores.O;
        
        // Enregistrer le score si X gagne
        if (result === 'X') {
          saveScore(scores.X, 'morpion');
        }
      }
      return;
    }

    current = current === 'X' ? 'O' : 'X';
    updateStatus();
    updateActive();
  });
});

resetBtn.addEventListener('click', init);

init();