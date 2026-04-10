@extends('layouts.pixel')

@section('title', 'Historique — ' . $user->pseudo)

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/profil.css') }}">
  <style>
    .pixels {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      overflow: hidden;
      pointer-events: none;
      z-index: 1;
    }

    .pixel {
      position: absolute;
      left: var(--x);
      bottom: -24px;
      width: var(--size);
      height: var(--size);
      background: var(--color);
      border-radius: 2px;
      opacity: 0;
      box-shadow: 0 0 8px var(--color);
      animation: pixelRise var(--dur) linear infinite;
      animation-delay: var(--delay);
      animation-fill-mode: both;
    }

    @keyframes pixelRise {
      0% {
        transform: translate3d(0, 0, 0) scale(1);
        opacity: 0;
      }
      12% {
        opacity: .9;
      }
      88% {
        opacity: .7;
      }
      100% {
        transform: translate3d(var(--drift), -115vh, 0) scale(1.08);
        opacity: 0;
      }
    }

    .history-wrap {
      position: relative;
      z-index: 2;
      max-width: 980px;
      margin: 24px auto 40px;
      padding: 0 16px;
    }

    .history-head {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      margin-bottom: 16px;
    }

    .history-title {
      margin: 0;
      font-size: 1.2rem;
      letter-spacing: 1px;
      text-transform: uppercase;
    }

    .history-back {
      display: inline-block;
      color: var(--neon-g);
      text-decoration: none;
      letter-spacing: 1px;
      font-size: .75rem;
      border: 1px solid rgba(0, 255, 136, .35);
      border-radius: 10px;
      padding: 8px 12px;
      transition: transform .18s ease, box-shadow .18s ease, background-color .18s ease;
    }

    .history-back:hover {
      transform: translateY(-2px);
      background-color: rgba(0, 255, 136, .1);
      box-shadow: 0 6px 16px rgba(0, 255, 136, .18);
    }

    .history-meta {
      color: var(--muted);
      font-size: .72rem;
      letter-spacing: 1px;
      margin-bottom: 18px;
      text-transform: uppercase;
    }

    .history-empty {
      color: var(--muted);
      font-size: .78rem;
      letter-spacing: 2px;
      text-transform: uppercase;
      padding: 22px 0;
    }

    .history-pagination {
      margin-top: 18px;
    }

    .history-pagination nav > div:first-child {
      display: none;
    }

    .history-pagination svg {
      width: 16px;
      height: 16px;
    }
  </style>
@endpush

@section('content')
<div class="pixels" id="pixels">
  @php($pixelColors = ['#00ff88', '#00d4ff', '#bf00ff', '#ffdd00', '#ff3366'])
  @for($i = 0; $i < 48; $i++)
    <span
      class="pixel"
      style="
        --x: {{ rand(0, 100) }}%;
        --size: {{ rand(4, 7) }}px;
        --dur: {{ rand(65, 165) / 10 }}s;
        --delay: {{ rand(0, 180) / 10 }}s;
        --drift: {{ rand(-24, 24) }}px;
        --color: {{ $pixelColors[$i % count($pixelColors)] }};
      "
    ></span>
  @endfor
</div>
<div class="history-wrap">
  <div class="history-head">
    <h1 class="history-title">Historique complet des parties</h1>
    <a href="{{ route('profil', $user->id) }}" class="history-back">← RETOUR AU PROFIL</a>
  </div>

  <div class="history-meta">
    {{ $parties->total() }} partie(s) enregistrée(s)
  </div>

  <div class="match-list">
    @forelse($parties as $partie)
      <div class="match-row">
        <div class="game-pill">{{ strtoupper(optional($partie->game)->name ?? 'JEU SUPPRIMÉ') }}</div>
        <div class="match-game">
          <div class="match-date">{{ $partie->created_at->translatedFormat('d M Y à H:i') }}</div>
        </div>
        <div>
          <div class="match-score">{{ number_format($partie->score, 0, ',', ' ') }}</div>
        </div>
      </div>
    @empty
      <div class="history-empty">AUCUNE PARTIE JOUÉE</div>
    @endforelse
  </div>

  @if($parties->hasPages())
    <div class="history-pagination">
      {{ $parties->links() }}
    </div>
  @endif
</div>
@endsection
