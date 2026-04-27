/**
 * Enregistrer un score
 * @param {number} scoreValue - La valeur du score
 * @param {string} gameSlug - Le slug du jeu (snake, tetris, flappy, etc.)
 */
function saveScore(scoreValue, gameSlug) {
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
  const isAuthenticated = document.querySelector('meta[name="auth-status"]')?.content === '1';

  if (!isAuthenticated) {
    console.log('Mode invite: score non enregistre');
    return;
  }

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
    .then(async response => {
      const contentType = response.headers.get('content-type') || '';
      const payload = contentType.includes('application/json')
        ? await response.json()
        : null;

      if (response.ok && payload?.success) {
        console.log(`Score ${scoreValue} enregistre pour ${gameSlug} (best: ${payload.best_score})`);
      } else if (response.status === 401) {
        console.log('Mode invite: score non enregistre');
      } else {
        console.error('Erreur lors de l\'enregistrement du score');
      }
    })
    .catch(error => console.error('Erreur réseau:', error));
}
