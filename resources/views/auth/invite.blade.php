@extends('layouts.app')

@section('title', 'PIXELZONE — Mode Invité')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/connexion.css') }}">
    <link rel="stylesheet" href="{{ asset('css/invite.css') }}">
@endpush

@section('content')

    <a href="{{ route('welcome') }}" class="back-link">‹ RETOUR</a>

    <main class="invite-page">

        {{-- Bannière invité --}}
        <div class="guest-banner">
            <span>⚠️ Mode invité — Tes scores ne seront <strong>pas sauvegardés</strong>. Crée un compte pour apparaître au classement !</span>
            <div class="banner-actions">
                <a href="{{ route('connexion') }}" class="btn-banner btn-banner--outline">Connexion</a>
                <a href="{{ route('inscription') }}" class="btn-banner btn-banner--fill">S'inscrire</a>
            </div>
        </div>

        {{-- Titre --}}
        <div class="guest-header">
            <h1>▶ CHOISIR UN JEU</h1>
            <p>Joue librement — tes résultats s'affichent après chaque partie.</p>
        </div>

        {{-- Grille des jeux --}}
        <p class="section-title">Jeux disponibles</p>
        <div class="games-grid">

            <a href="#" class="game-card">
                <div class="game-card__thumb">🐍</div>
                <div class="game-card__body">
                    <p class="game-card__title">SNAKE</p>
                    <p class="game-card__desc">Mange, grandis, survive. Le classique indémodable.</p>
                    <span class="game-card__badge">INVITÉ OK</span>
                </div>
            </a>

            <a href="#" class="game-card">
                <div class="game-card__thumb">🧠</div>
                <div class="game-card__body">
                    <p class="game-card__title">MEMORY</p>
                    <p class="game-card__desc">Retrouve toutes les paires le plus vite possible.</p>
                    <span class="game-card__badge">INVITÉ OK</span>
                </div>
            </a>

            <a href="#" class="game-card">
                <div class="game-card__thumb">🔴</div>
                <div class="game-card__body">
                    <p class="game-card__title">PUISSANCE 4</p>
                    <p class="game-card__desc">Aligne 4 jetons avant ton adversaire.</p>
                    <span class="game-card__badge">INVITÉ OK</span>
                </div>
            </a>

            <a href="#" class="game-card">
                <div class="game-card__thumb">❌</div>
                <div class="game-card__body">
                    <p class="game-card__title">MORPION</p>
                    <p class="game-card__desc">X ou O — qui alignera trois en premier ?</p>
                    <span class="game-card__badge">INVITÉ OK</span>
                </div>
            </a>

        </div>

        {{-- Classement global --}}
        <p class="section-title">Classement global</p>
        <div class="leaderboard">
            @forelse($topScores as $index => $score)
                <div class="leaderboard__row">
                    <span class="leaderboard__rank {{ $index === 0 ? 'gold' : ($index === 1 ? 'silver' : ($index === 2 ? 'bronze' : '')) }}">
                        {{ $index === 0 ? '🥇' : ($index === 1 ? '🥈' : ($index === 2 ? '🥉' : '#'.($index+1))) }}
                    </span>
                    <div class="leaderboard__avatar">{{ mb_strtoupper(mb_substr($score->user->pseudo, 0, 1)) }}</div>
                    <span class="leaderboard__name">{{ $score->user->pseudo }}</span>
                    <span class="leaderboard__game">{{ $score->jeu }}</span>
                    <span class="leaderboard__score">{{ number_format($score->score) }} pts</span>
                </div>
            @empty
                <div class="leaderboard__row leaderboard__empty">
                    Aucun score enregistré pour l'instant.
                </div>
            @endforelse
        </div>

        {{-- CTA inscription --}}
        <div class="guest-cta">
            <h2>🏆 ENVIE D'APPARAÎTRE ICI ?</h2>
            <p>Crée un compte gratuit pour sauvegarder tes scores et défier les autres joueurs.</p>
            <div class="guest-cta__btns">
                <a href="{{ route('inscription') }}" class="btn-auth" style="text-decoration:none; display:inline-block; padding: 16px 32px;">
                    ▶ CRÉER MON COMPTE
                </a>
                <a href="{{ route('connexion') }}" class="btn-guest" style="display:inline-block; padding: 13px 24px; margin-top: 0;">
                    Se connecter
                </a>
            </div>
        </div>

    </main>

    {{-- Score flash après une partie --}}
    @if(session('score_flash'))
    <div class="score-flash" id="scoreFlash">
        <button class="score-flash__close" id="closeFlash">✕</button>
        <div class="score-flash__label">⚡ TON SCORE</div>
        <div class="score-flash__value">{{ session('score_flash') }} pts</div>
        <div class="score-flash__note">
            Non sauvegardé —
            <a href="{{ route('inscription') }}">crée un compte</a> pour le garder !
        </div>
    </div>
    @endif

@endsection

@push('scripts')
    <script src="{{ asset('js/connexion.js') }}"></script>
    <script src="{{ asset('js/invite.js') }}"></script>
@endpush