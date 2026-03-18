@extends('layouts.app')

@section('title', 'PIXELZONE — Pong')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/jeux/pong.css') }}">
@endpush

@section('content')
<a href="javascript:history.back()" class="back-link">‹ RETOUR</a>

<div class="game-page">

  <div class="game-header">
    <div class="game-score" id="scoreLeft">0</div>
    <h1 class="game-title">PONG</h1>
    <div class="game-score" id="scoreRight">0</div>
  </div>

  <div style="position:relative;">
    <canvas id="gameCanvas" width="700" height="480"></canvas>
    <div class="game-overlay" id="overlay">
      <div class="overlay-title">PONG</div>
      <div class="overlay-score" id="overlay-msg">JOUEUR vs IA — Premier à 7 !</div>
      <button class="btn-play" id="startBtn">▶ JOUER</button>
    </div>
  </div>

  <div class="game-controls">
    W / S ou ↑ ↓ &nbsp; DÉPLACER &nbsp;|&nbsp; PREMIER À 7 GAGNE
  </div>

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/jeux/pong.js') }}"></script>
@endpush