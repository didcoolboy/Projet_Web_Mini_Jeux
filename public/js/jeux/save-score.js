/**
 * Enregistrer un score
 * @param {number} scoreValue - La valeur du score
 * @param {string} gameSlug - Le slug du jeu (snake, tetris, flappy, etc.)
 */
function saveScore(scoreValue, gameSlug) {
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

  if (!csrfToken) {
    console.log('Non authentifié, score non enregistré');
    return;
  }

  fetch(`/save-score/${gameSlug}`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken,
      'Accept': 'application/json',
    },
    body: JSON.stringify({ score: scoreValue }),
  })
    .then(response => {
      if (response.ok) {
        console.log(`Score ${scoreValue} enregistré pour ${gameSlug}`);
      } else if (response.status === 401) {
        console.log('Non authentifié');
      } else {
        console.error('Erreur lors de l\'enregistrement du score');
      }
    })
    .catch(error => console.error('Erreur réseau:', error));
}
