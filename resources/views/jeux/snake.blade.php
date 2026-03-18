@extends('layouts.app')

@section('title', 'PIXELZONE — Snake')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/jeux/snake.css') }}">
@endpush

@section('content')
<a href="javascript:history.back()" class="back-link">‹ RETOUR</a>

<div class="game-page">
  <div class="game-header">
    <h1 class="game-title">SNAKE</h1>
    <div class="game-score">SCORE <span id="score">0</span></div>
    <div class="game-score">MEILLEUR <span id="highscore">0</span></div>
  </div>

  <div style="position:relative;">
    <canvas id="gameCanvas" width="480" height="480"></canvas>
    <div class="game-overlay" id="overlay">
      <div class="overlay-title">SNAKE</div>
      <div class="overlay-score" id="overlay-msg">Prêt à jouer ?</div>
      <button class="btn-play" id="startBtn">▶ JOUER</button>
    </div>
  </div>

  <div class="game-controls">
    FLÈCHES ou ZQSD pour diriger &nbsp;|&nbsp; P pour pause
  </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/jeux/snake.js') }}"></script>
@endpush