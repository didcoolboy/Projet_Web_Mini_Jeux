@extends('layouts.app')

@section('title', 'PIXELZONE — Memory')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/jeux/memory.css') }}">
@endpush

@section('content')
<a href="javascript:history.back()" class="back-link">‹ RETOUR</a>

<div class="game-page">

  <div class="game-header">
    <h1 class="game-title">MEMORY</h1>
    <div class="game-stat">
      COUPS
      <span id="moves">0</span>
    </div>
    <div class="game-stat">
      PAIRES
      <span id="pairs">0</span>
    </div>
  </div>

  <div class="board" id="board"></div>

  <div class="game-overlay" id="overlay">
    <div class="overlay-title">MEMORY</div>
    <div class="overlay-score" id="overlay-msg">Retrouve toutes les paires !</div>
    <button class="btn-play" id="startBtn">▶ JOUER</button>
  </div>

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/jeux/memory.js') }}"></script>
@endpush