@extends('layouts.app')

@section('title', 'PIXELZONE — Morpion')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/jeux/morpion.css') }}">
@endpush

@section('content')
<a href="javascript:history.back()" class="back-link">‹ RETOUR</a>

<div class="game-page">

  <h1 class="game-title">MORPION</h1>

  <div class="scores">
    <div class="score-box active" id="boxX">
      JOUEUR X
      <span id="scoreX">0</span>
    </div>
    <div class="score-box">
      NULS
      <span id="scoreD">0</span>
    </div>
    <div class="score-box" id="boxO">
      JOUEUR O
      <span id="scoreO">0</span>
    </div>
  </div>

  <p class="game-status" id="status"></p>

  <div class="board">
    <div class="cell" data-i="0"></div>
    <div class="cell" data-i="1"></div>
    <div class="cell" data-i="2"></div>
    <div class="cell" data-i="3"></div>
    <div class="cell" data-i="4"></div>
    <div class="cell" data-i="5"></div>
    <div class="cell" data-i="6"></div>
    <div class="cell" data-i="7"></div>
    <div class="cell" data-i="8"></div>
  </div>

  <button class="btn-play" id="resetBtn">↺ REJOUER</button>

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/jeux/morpion.js') }}"></script>
@endpush