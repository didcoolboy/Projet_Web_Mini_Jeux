<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>PIXELZONE — @yield('title', 'Accueil')</title>
  <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <style>
    body, a, button, nav, header {
      cursor: none !important;
    }
  </style>
  @stack('styles')
</head>
<body>

  {{-- Curseur pixel --}}
  <div id="cur-main"></div>

  {{-- Orbes de fond --}}
  <div class="orb orb1"></div>
  <div class="orb orb2"></div>

  <div class="container">

    <header>
      <a class="logo" href="{{ route('accueil') }}">PIXELZONE</a>
      <nav>
        <a href="{{ route('accueil') }}"
           class="{{ request()->routeIs('accueil') ? 'active' : '' }}">Accueil</a>
        <a href="{{ route('amis.index') }}"
           class="{{ request()->routeIs('amis.*') ? 'active' : '' }}">Amis</a>
        <a href="{{ route('profil', auth()->user()->id) }}"
           class="{{ request()->routeIs('profil*') ? 'active' : '' }}">Profil</a>
        <form method="POST" action="{{ route('deconnexion') }}" style="display:inline;">
          @csrf
          <button type="submit" class="nav-logout">Déconnexion</button>
        </form>
      </nav>
    </header>

    @if(session('notif'))
      <div class="flash-notif">{{ session('notif') }}</div>
    @endif

    @yield('content')

  </div>

  {{-- Toast notification --}}
  <div class="notif" id="notif"></div>

  <script>
    /* Curseur avec traînée */
    const TRAIL_LEN = 18;
    const trail = [];
    let mouseX = -200;
    let mouseY = -200;

    // Créer les carrés fantômes
    const trailEls = [];
    for (let i = 0; i < TRAIL_LEN; i++) {
      const el = document.createElement('div');
      el.className = 'cursor-trail';
      el.style.position = 'fixed';
      el.style.pointerEvents = 'none';
      el.style.zIndex = '99999';
      const ratio = 1 - i / TRAIL_LEN;
      const size = Math.max(1, Math.round(12 * ratio));
      el.style.width = size + 'px';
      el.style.height = size + 'px';
      el.style.backgroundColor = '#00ff88';
      el.style.border = '1px solid #00ff88';
      el.style.opacity = (ratio * 0.5).toFixed(2);
      el.style.filter = 'blur(' + (i * 1.2).toFixed(1) + 'px)';
      document.body.appendChild(el);
      trailEls.push(el);
    }

    // Suivi souris
    document.addEventListener('mousemove', function(e) {
      mouseX = e.clientX;
      mouseY = e.clientY;
    });

    // Boucle RAF
    function animateCursor() {
      trail.unshift({ x: mouseX, y: mouseY });
      if (trail.length > TRAIL_LEN) trail.pop();

      for (var i = 0; i < trailEls.length; i++) {
        var pos = trail[i] || { x: mouseX, y: mouseY };
        trailEls[i].style.left = (pos.x - 6) + 'px';
        trailEls[i].style.top = (pos.y - 6) + 'px';
      }

      requestAnimationFrame(animateCursor);
    }
    requestAnimationFrame(animateCursor);

    // Changement de couleur au survol des boutons
    document.querySelectorAll('a, button').forEach(function(el) {
      el.addEventListener('mouseenter', function() {
        trailEls.forEach(function(trail) {
          trail.style.backgroundColor = '#bf00ff';
          trail.style.borderColor = '#bf00ff';
        });
      });
      el.addEventListener('mouseleave', function() {
        trailEls.forEach(function(trail) {
          trail.style.backgroundColor = '#00ff88';
          trail.style.borderColor = '#00ff88';
        });
      });
    });
  </script>

  <script src="{{ asset('js/app.js') }}"></script>
  @stack('scripts')

</body>
</html>