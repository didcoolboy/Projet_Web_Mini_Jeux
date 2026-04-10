const difficultySettings = {
  easy: {
    targetSize: [90, 120],
    aliveTime: [1400, 1900],
    moveInterval: [0, 0],
    scoreHit: 10,
    scoreMiss: 2,
  },
  medium: {
    targetSize: [64, 88],
    aliveTime: [1000, 1450],
    moveInterval: [350, 700],
    scoreHit: 15,
    scoreMiss: 4,
  },
  hard: {
    targetSize: [44, 64],
    aliveTime: [650, 1000],
    moveInterval: [180, 430],
    scoreHit: 22,
    scoreMiss: 7,
  },
};

const startScreen = document.getElementById("start-screen");
const gameScreen = document.getElementById("game-screen");
const endScreen = document.getElementById("end-screen");
const startButton = document.getElementById("start-button");
const restartButton = document.getElementById("restart-button");
const menuButton = document.getElementById("menu-button");
const difficultySelect = document.getElementById("difficulty-select");
const skinSelect = document.getElementById("skin-select");
const durationSelect = document.getElementById("duration-select");
const stageBanner = document.getElementById("stage-banner");
const gameStage = document.getElementById("game-stage");
const target = document.getElementById("target");

const scoreValue = document.getElementById("score-value");
const timeValue = document.getElementById("time-value");
const accuracyValue = document.getElementById("accuracy-value");
const hitsValue = document.getElementById("hits-value");

const finalScore = document.getElementById("final-score");
const finalAccuracy = document.getElementById("final-accuracy");
const finalReaction = document.getElementById("final-reaction");
const finalHits = document.getElementById("final-hits");
const finalMisses = document.getElementById("final-misses");
const finalMessage = document.getElementById("final-message");

let gameActive = false;
let score = 0;
let hits = 0;
let misses = 0;
let shots = 0;
let totalReactionTime = 0;
let timeLeft = 30;
let roundDuration = 30;
let currentDifficulty = difficultySettings.medium;
let targetSpawnedAt = 0;
let targetTimeoutId = null;
let targetMoveTimeoutId = null;
let timerIntervalId = null;
let targetBounds = { width: 0, height: 0 };

function randomBetween(min, max) {
  return Math.random() * (max - min) + min;
}

function pickRandomInt(min, max) {
  return Math.floor(randomBetween(min, max + 1));
}

function clearTargetTimers() {
  if (targetTimeoutId !== null) {
    window.clearTimeout(targetTimeoutId);
    targetTimeoutId = null;
  }

  if (targetMoveTimeoutId !== null) {
    window.clearTimeout(targetMoveTimeoutId);
    targetMoveTimeoutId = null;
  }
}

function updateHud() {
  const accuracy = shots === 0 ? 100 : Math.max(0, Math.round((hits / shots) * 100));

  scoreValue.textContent = String(score);
  timeValue.textContent = timeLeft.toFixed(1);
  accuracyValue.textContent = `${accuracy}%`;
  hitsValue.textContent = String(hits);
}

function getStageBounds() {
  const rect = gameStage.getBoundingClientRect();
  targetBounds = {
    width: rect.width,
    height: rect.height,
  };
  return rect;
}

function hideTarget() {
  target.hidden = true;
  target.removeAttribute("data-moving");
  clearTargetTimers();
}

function spawnTarget() {
  if (!gameActive) {
    return;
  }

  clearTargetTimers();

  const rect = getStageBounds();
  const size = pickRandomInt(currentDifficulty.targetSize[0], currentDifficulty.targetSize[1]);
  const radius = size / 2;
  const x = randomBetween(radius + 12, Math.max(radius + 12, rect.width - radius - 12));
  const y = randomBetween(radius + 12, Math.max(radius + 12, rect.height - radius - 12));

  target.dataset.skin = skinSelect.value;
  target.style.setProperty("--size", `${size}px`);
  target.style.left = `${x}px`;
  target.style.top = `${y}px`;
  target.hidden = false;
  targetSpawnedAt = performance.now();

  const aliveTime = pickRandomInt(currentDifficulty.aliveTime[0], currentDifficulty.aliveTime[1]);

  targetTimeoutId = window.setTimeout(() => {
    if (!gameActive || target.hidden) {
      return;
    }

    shots += 1;
    misses += 1;
    score = Math.max(0, score - currentDifficulty.scoreMiss);
    updateHud();
    spawnTarget();
  }, aliveTime);

  const [moveMin, moveMax] = currentDifficulty.moveInterval;
  if (moveMax > 0) {
    const moveDelay = pickRandomInt(moveMin, moveMax);
    targetMoveTimeoutId = window.setTimeout(() => {
      if (!gameActive || target.hidden) {
        return;
      }

      target.setAttribute("data-moving", "true");
      spawnTarget();
    }, moveDelay);
  }
}

function endGame() {
  if (!gameActive) {
    return;
  }

  gameActive = false;
  clearInterval(timerIntervalId);
  clearTargetTimers();
  hideTarget();

  const accuracy = shots === 0 ? 100 : Math.round((hits / shots) * 100);
  const averageReaction = hits === 0 ? 0 : Math.round(totalReactionTime / hits);

  finalScore.textContent = String(score);
  finalAccuracy.textContent = `${accuracy}%`;
  finalReaction.textContent = `${averageReaction} ms`;
  finalHits.textContent = String(hits);
  finalMisses.textContent = String(misses);

  if (score >= 180) {
    finalMessage.textContent = "Très bon contrôle. Tu peux passer sur un rythme plus agressif.";
  } else if (score >= 100) {
    finalMessage.textContent = "Solide. La précision est là, il reste à accélérer.";
  } else {
    finalMessage.textContent = "Bonne base. Rejoue pour stabiliser ton timing.";
  }

  gameScreen.hidden = true;
  endScreen.hidden = false;
  startScreen.hidden = true;
  stageBanner.hidden = false;
}

function startTimer() {
  timerIntervalId = window.setInterval(() => {
    if (!gameActive) {
      return;
    }

    timeLeft = Math.max(0, timeLeft - 0.1);
    updateHud();

    if (timeLeft <= 0) {
      endGame();
    }
  }, 100);
}

function startGame() {
  currentDifficulty = difficultySettings[difficultySelect.value] ?? difficultySettings.medium;
  roundDuration = Number(durationSelect.value) || 30;

  score = 0;
  hits = 0;
  misses = 0;
  shots = 0;
  totalReactionTime = 0;
  timeLeft = roundDuration;
  gameActive = true;

  startScreen.hidden = true;
  endScreen.hidden = true;
  gameScreen.hidden = false;
  stageBanner.hidden = true;

  updateHud();
  getStageBounds();
  startTimer();
  spawnTarget();
}

function restartGame() {
  clearTargetTimers();
  clearInterval(timerIntervalId);
  gameActive = false;
  hideTarget();
  startGame();
}

function returnToMenu() {
  clearTargetTimers();
  clearInterval(timerIntervalId);
  gameActive = false;
  hideTarget();

  stageBanner.hidden = false;
  gameScreen.hidden = true;
  endScreen.hidden = true;
  startScreen.hidden = false;
}

function handleTargetClick(event) {
  event.stopPropagation();

  if (!gameActive || target.hidden) {
    return;
  }

  const isInnerHit = Boolean(event.target.closest(".target-inner"));
  const reactionTime = performance.now() - targetSpawnedAt;
  totalReactionTime += reactionTime;
  hits += 1;
  shots += 1;
  score += isInnerHit ? currentDifficulty.scoreHit + 8 : currentDifficulty.scoreHit;
  updateHud();
  spawnTarget();
}

function handleStageClick(event) {
  if (!gameActive) {
    return;
  }

  if (event.target === target) {
    return;
  }

  shots += 1;
  misses += 1;
  score = Math.max(0, score - currentDifficulty.scoreMiss);
  updateHud();
}

function handleResize() {
  if (!gameActive || target.hidden) {
    return;
  }

  const rect = gameStage.getBoundingClientRect();
  const targetSize = Number.parseFloat(getComputedStyle(target).getPropertyValue("--size")) || 0;
  const centerX = Math.min(rect.width - targetSize / 2 - 12, Math.max(targetSize / 2 + 12, parseFloat(target.style.left)));
  const centerY = Math.min(rect.height - targetSize / 2 - 12, Math.max(targetSize / 2 + 12, parseFloat(target.style.top)));

  target.style.left = `${centerX}px`;
  target.style.top = `${centerY}px`;
}

startButton.addEventListener("click", startGame);
restartButton.addEventListener("click", restartGame);
menuButton.addEventListener("click", returnToMenu);
target.addEventListener("click", handleTargetClick);
gameStage.addEventListener("pointerdown", handleStageClick);
window.addEventListener("resize", handleResize);
window.addEventListener("keydown", (event) => {
  if (event.key === "Escape" && gameActive) {
    endGame();
  }
});

updateHud();
