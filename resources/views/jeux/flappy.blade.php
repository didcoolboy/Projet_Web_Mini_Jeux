@extends('layouts.app')

@section('title', 'PIXELZONE — Flappy Bird')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/jeux/flappy.css') }}">
@endpush

@section('content')
<a href="javascript:history.back()" class="back-link">‹ RETOUR</a>

<div class="game-page">

  <div class="game-header">
    <h1 class="game-title">FLAPPY</h1>
    <div class="game-score">SCORE <span id="score">0</span></div>
    <div class="game-score">MEILLEUR <span id="best">0</span></div>
  </div>

  <div style="position:relative;">
    <canvas id="gameCanvas" width="480" height="560"></canvas>
    <div class="game-overlay" id="overlay">
      <div class="overlay-title">FLAPPY</div>
      <div class="overlay-score" id="overlay-msg">Évite les tuyaux !</div>
      <button class="btn-play" id="startBtn">▶ JOUER</button>
    </div>
  </div>

  <div class="game-controls">
    ESPACE / ↑ / CLIC &nbsp; pour sauter
  </div>

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/jeux/flappy.js') }}"></script>
@endpush