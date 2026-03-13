/* ═══════════════════════════════════════════════
   PIXELZONE — invite.js
═══════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', function () {

    /* ── Fermer le score flash ── */
    const closeBtn   = document.getElementById('closeFlash');
    const scoreFlash = document.getElementById('scoreFlash');

    if (closeBtn && scoreFlash) {
        closeBtn.addEventListener('click', function () {
            scoreFlash.style.animation = 'fadeDown .25s ease forwards';
            setTimeout(function () {
                scoreFlash.remove();
            }, 250);
        });

        /* Auto-fermeture après 8 secondes */
        setTimeout(function () {
            if (scoreFlash) {
                scoreFlash.style.animation = 'fadeDown .25s ease forwards';
                setTimeout(function () {
                    scoreFlash.remove();
                }, 250);
            }
        }, 8000);
    }

});