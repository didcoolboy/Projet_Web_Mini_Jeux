<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>PIXELZONE — @yield('title', 'Accueil')</title>
  <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  @stack('styles')
</head>
<body>

  {{-- Curseur pixel --}}
  <div id="cur-main"></div>

  {{-- Fond animé --}}
  <div class="pixels" id="pixels"></div>
  <div class="orb orb1"></div>
  <div class="orb orb2"></div>

  <div class="container">

    <header>
      <a class="logo" href="{{ route('home') }}">PIXELZONE <span>v2.0</span></a>
      <nav>
        <a href="{{ route('home') }}"                         class="{{ request()->routeIs('home')        ? 'active' : '' }}">Accueil</a>
        <a href="{{ route('jeux') }}"                         class="{{ request()->routeIs('jeux')        ? 'active' : '' }}">Jeux</a>
        <a href="{{ route('amis.index') }}"                   class="{{ request()->routeIs('amis.*')      ? 'active' : '' }}">Amis</a>
        <a href="{{ route('profil', auth()->user()->id) }}"   class="{{ request()->routeIs('profil')      ? 'active' : '' }}">Profil</a>
      </nav>
    </header>

    @yield('content')

  </div>

  {{-- Toast notification --}}
  <div class="notif" id="notif"></div>

  <script src="{{ asset('js/app.js') }}"></script>
  @stack('scripts')

</body>
</html>
