@extends('layouts.app')

@section('title', 'PIXELZONE — Tetris')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/jeux/tetris.css') }}">
@endpush

@section('content')
<a href="javascript:history.back()" class="back-link">‹ RETOUR</a>

<div class="game-page">

  <div style="position:relative;">
    <canvas id="gameCanvas" width="300" height="600"></canvas>
    <div class="game-overlay" id="overlay">
      <div class="overlay-title">TETRIS</div>
      <div class="overlay-score" id="overlay-msg">Prêt à jouer ?</div>
      <button class="btn-play" id="startBtn">▶ JOUER</button>
    </div>
  </div>

  <div class="side-panel">
    <h1 class="game-title">TETRIS</h1>

    <div class="panel-box">
      <div class="panel-label">SUIVANT</div>
      <canvas id="nextCanvas" width="96" height="96"></canvas>
    </div>

    <div class="panel-box">
      <div class="panel-label">SCORE</div>
      <div class="panel-value" id="score">0</div>
    </div>

    <div class="panel-box">
      <div class="panel-label">NIVEAU</div>
      <div class="panel-value" id="level">1</div>
    </div>

    <div class="panel-box">
      <div class="panel-label">LIGNES</div>
      <div class="panel-value" id="lines">0</div>
    </div>

    <div class="game-controls">
      ← → &nbsp; DÉPLACER<br>
      ↑ &nbsp;&nbsp;&nbsp;&nbsp; ROTATION<br>
      ↓ &nbsp;&nbsp;&nbsp;&nbsp; DESCENDRE<br>
      ESPACE &nbsp; CHUTE<br>
      P &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; PAUSE
    </div>
  </div>

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/jeux/save-score.js') }}"></script>
    <script src="{{ asset('js/jeux/tetris.js') }}"></script>
@endpush