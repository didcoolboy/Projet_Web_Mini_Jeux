/* ═══════════════════════════════════════════════
   PIXELZONE — invite.js
═══════════════════════════════════════════════ */
 
document.addEventListener('DOMContentLoaded', function () {
 
    /* ── Fermer le score flash ── */
    const closeBtn   = document.getElementById('closeFlash');
    const scoreFlash = document.getElementById('scoreFlash');
 
    if (closeBtn && scoreFlash) {
        closeBtn.addEventListener('click', function () {
            scoreFlash.style.opacity = '0';
            scoreFlash.style.transform = 'translateY(20px)';
            scoreFlash.style.transition = 'all .25s ease';
            setTimeout(function () { scoreFlash.remove(); }, 250);
        });
 
        /* Auto-fermeture après 8 secondes */
        setTimeout(function () {
            if (document.getElementById('scoreFlash')) {
                closeBtn.click();
            }
        }, 8000);
    }
 
    /* ── Burger menu mobile ── */
    const burger   = document.getElementById('burger');
    const navLinks = document.querySelector('.nav-links');
 
    if (burger && navLinks) {
        burger.addEventListener('click', function () {
            navLinks.classList.toggle('open');
            burger.classList.toggle('active');
        });
    }
 
});
 