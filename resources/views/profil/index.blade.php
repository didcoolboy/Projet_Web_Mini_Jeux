@extends('layouts.pixel')

@section('title', 'Profil — ' . $user->pseudo)

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/profil.css') }}">
  <style>
    .pixels {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: 1;
    }
    .pixels::before {
      content: '';
      position: absolute;
      width: 100%;
      height: 100%;
      background-image: 
        radial-gradient(circle, #00ff88 1px, transparent 1px),
        radial-gradient(circle, #00d4ff 1px, transparent 1px),
        radial-gradient(circle, #bf00ff 1px, transparent 1px),
        radial-gradient(circle, #ffdd00 1px, transparent 1px),
        radial-gradient(circle, #ff3366 1px, transparent 1px);
      background-size: 
        150px 150px,
        200px 200px,
        180px 180px,
        220px 220px,
        190px 190px;
      background-position: 
        0 0,
        30px 30px,
        60px 60px,
        90px 90px,
        120px 120px;
      opacity: 0.15;
      animation: floatPixels 20s linear infinite;
    }
    @keyframes floatPixels {
      0% { background-position: 0 0, 30px 30px, 60px 60px, 90px 90px, 120px 120px; }
      100% { background-position: -150px -150px, -170px -170px, -180px -180px, -220px -220px, -190px -190px; }
    }
  </style>
@endpush

@section('content')

  {{-- Pixels flottants --}}
  <div class="pixels" id="pixels"></div>

  {{-- ═══ HERO ═══ --}}
  <div class="profile-hero fade-up">
    <div class="hero-glow"></div>

    <div class="avatar-wrap">
      <div class="avatar-sq">{{ strtoupper(substr($user->pseudo, 0, 2)) }}</div>
    </div>

    <div class="player-info">
      <div class="player-tag">// PROFIL JOUEUR</div>
      <div class="player-name">{{ strtoupper($user->pseudo) }}<span>_</span></div>
      <div class="player-meta">
        <div class="meta-chip">MEMBRE DEPUIS {{ strtoupper($user->created_at->translatedFormat('M. Y')) }}</div>
        <div class="meta-chip">ID — #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</div>
      </div>
    </div>

    <div class="score-panel">
      <div class="score-label">Score total</div>
      <div class="score-value" id="scoreVal" data-score="{{ $scoreTotal }}">0</div>
      <div class="score-rank">
        @if($rangLabel === 'gold')
          🥇 RANG OR
        @elseif($rangLabel === 'silver')
          🥈 RANG ARGENT
        @elseif($rangLabel === 'bronze')
          🥉 RANG BRONZE
        @else
          — NON CLASSÉ
        @endif
      </div>
      @if(auth()->id() === $user->id)
      @endif
    </div>
  </div>

  {{-- ═══ STATS ═══ --}}
  <div class="stats-grid">
    <div class="stat-card fade-up" style="animation-delay:.05s">
      <div class="stat-icon">🎮</div>
      <div class="stat-label">Parties jouées</div>
      <div class="stat-val accent">{{ $nbParties }}</div>
      <div class="stat-sub">+{{ $partiesSemaine }} cette semaine</div>
      <div class="stat-progress">
        <div class="stat-progress-fill" style="background:var(--accent)" data-w="{{ min(100, round($nbParties / 3)) }}"></div>
      </div>
    </div>
    <div class="stat-card fade-up" style="animation-delay:.10s">
      <div class="stat-icon">🎯</div>
      <div class="stat-label">Jeux joués</div>
      <div class="stat-val" style="color:var(--neon-b)">{{ $nbJeux }}</div>
      <div class="stat-sub">jeux différents</div>
      <div class="stat-progress">
        <div class="stat-progress-fill" style="background:var(--neon-b)" data-w="{{ min(100, $nbJeux * 20) }}"></div>
      </div>
    </div>
    <div class="stat-card fade-up" style="animation-delay:.15s">
      <div class="stat-icon">👥</div>
      <div class="stat-label">Amis</div>
      <div class="stat-val" style="color:var(--neon-p)">{{ $nbAmis }}</div>
      <div class="stat-sub">sur PixelZone</div>
      <div class="stat-progress">
        <div class="stat-progress-fill" style="background:var(--neon-p)" data-w="{{ min(100, $nbAmis * 10) }}"></div>
      </div>
    </div>
  </div>

  {{-- ═══ DERNIÈRE PARTIE ═══ --}}
  @if($dernierePartie)
  <div class="section-head">
    <div class="section-title">Dernière partie</div>
  </div>

  <div class="match-row last-game">
    <div class="game-pill">{{ strtoupper($dernierePartie->game->name) }}</div>
    <div class="match-game">
      <div class="match-date">{{ $dernierePartie->created_at->diffForHumans() }}</div>
    </div>
    <div>
      <div class="match-score">{{ number_format($dernierePartie->score, 0, ',', ' ') }}</div>
    </div>
  </div>
  @endif

  {{-- ═══ HISTORIQUE ═══ --}}
  <div class="section-head">
    <div class="section-title">Dernières parties</div>
    <a href="{{ route('profil.historique', $user->id) }}" class="section-link">VOIR TOUT →</a>
  </div>

  <div class="match-list">
    @forelse($dernieresParties as $partie)
      <div class="match-row">
        <div class="game-pill">{{ strtoupper($partie->game->name) }}</div>
        <div class="match-game">
          <div class="match-date">{{ $partie->created_at->diffForHumans() }}</div>
        </div>
        <div>
          <div class="match-score">{{ number_format($partie->score, 0, ',', ' ') }}</div>
        </div>
      </div>
    @empty
      <div style="color:var(--muted);font-size:.72rem;letter-spacing:2px;padding:20px 0;">
        AUCUNE PARTIE JOUÉE
      </div>
    @endforelse
  </div>


  <div class="section-head" style="margin-top:22px;">
    <div class="section-title">Rechercher un joueur</div>
    <div class="section-link" style="font-size:.85rem;color:var(--muted)">Tapez un pseudo puis appuyez sur Entrée</div>
  </div>

  <div class="search-player">
    <form id="playerSearchForm" onsubmit="return false;">
      <input id="playerSearchInput" type="search" name="q" placeholder="Rechercher un pseudo..." autocomplete="off" style="width:100%;padding:10px;border-radius:6px;border:1px solid rgba(255,255,255,0.06);background:transparent;color:inherit;">
    </form>

    <div id="searchResults" style="margin-top:12px;"></div>
  </div>

@endsection

@push('scripts')
  <script src="{{ asset('js/profil.js') }}"></script>
@endpush