@extends('layouts.app')

@section('title', 'PIXELZONE — Mode Invité')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/accueil.css') }}">
    <link rel="stylesheet" href="{{ asset('css/invite.css') }}">
@endpush

@section('content')

    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="{{ route('welcome') }}" class="nav-logo">PIXELZONE</a>

        <ul class="nav-links">
            <li><a href="#comment" class="nav-link">Comment ça marche</a></li>
            <li><a href="#jeux" class="nav-link">Jeux</a></li>
            <li><a href="#classement" class="nav-link">Classement</a></li>
        </ul>

        <div class="nav-actions">
            <a href="{{ route('connexion') }}" class="btn-nav-outline">Connexion</a>
            <a href="{{ route('inscription') }}" class="btn-nav-solid">S'inscrire</a>
        </div>

        <button class="burger" id="burger" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </nav>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-orb hero-orb--green"></div>
        <div class="hero-orb hero-orb--purple"></div>

        <div class="hero-inner">
            <span class="hero-tag">👤 MODE INVITE ACTIF</span>
            <h1 class="hero-title">
                <span class="hero-title__top">JOUE.</span>
                <span class="hero-title__bot">SANS LIMITE.</span>
            </h1>
            <p class="hero-sub">Accède à tous les mini-jeux gratuitement. Crée un compte pour sauvegarder tes scores et grimper dans le classement.</p>
            <div class="hero-cta">
                <a href="#jeux" class="btn-primary">▶ VOIR LES JEUX</a>
                <a href="{{ route('inscription') }}" class="btn-ghost">📝 CRÉER UN COMPTE</a>
            </div>
        </div>
    </section>

    <!-- COMMENT ÇA MARCHE -->
    <section class="section" id="comment">
        <div class="section-head">
            <div>
                <p class="section-tag">// TUTORIEL</p>
                <h2 class="section-title">Comment ça marche ?</h2>
            </div>
        </div>

        <div class="how-grid">
            <div class="how-card" style="--hc: var(--neon-b)">
                <div class="how-card__num">01</div>
                <div class="how-card__icon">📝</div>
                <h3 class="how-card__title">Crée ton compte</h3>
                <p class="how-card__desc">Inscris-toi gratuitement en quelques secondes. Aucune carte de crédit requise.</p>
                <div class="how-card__arrow">›</div>
            </div>
            <div class="how-card" style="--hc: var(--neon-g)">
                <div class="how-card__num">02</div>
                <div class="how-card__icon">🎮</div>
                <h3 class="how-card__title">Choisis un jeu</h3>
                <p class="how-card__desc">Parcours le catalogue et lance-toi directement dans le navigateur, sans installation.</p>
                <div class="how-card__arrow">›</div>
            </div>
            <div class="how-card" style="--hc: var(--neon-y)">
                <div class="how-card__num">03</div>
                <div class="how-card__icon">⚡</div>
                <h3 class="how-card__title">Joue & performe</h3>
                <p class="how-card__desc">Ton score s'affiche à la fin de la partie. Crée un compte pour le sauvegarder.</p>
                <div class="how-card__arrow">›</div>
            </div>
            <div class="how-card" style="--hc: var(--neon-p)">
                <div class="how-card__num">04</div>
                <div class="how-card__icon">👑</div>
                <h3 class="how-card__title">Domine le classement</h3>
                <p class="how-card__desc">Compare tes scores avec la communauté et affiche ton rang dans le top mondial.</p>
            </div>
        </div>
    </section>

    <!-- JEUX -->
    <section class="section" id="jeux">
        <div class="section-head">
            <div>
                <p class="section-tag">// CATALOGUE</p>
                <h2 class="section-title">Jeux disponibles</h2>
            </div>
        </div>

        <div class="games-grid games-grid--4col">

            <div class="game-card" data-color="#00ff88">
                <div class="game-card__screen" style="background:linear-gradient(135deg,#001a0a,#002a10)">
                    <span class="game-card__emoji">🐍</span>
                    <div class="game-card__overlay">
                        <a href="{{ route('jeux.snake') }}" class="game-card__play">▶ JOUER</a>
                    </div>
                </div>
                <div class="game-card__body">
                    <div class="game-card__meta">
                        <span class="game-card__tag" style="color:var(--neon-g);border-color:rgba(0,255,136,0.3)">ARCADE</span>
                        <span class="game-card__players">👤 INVITÉ</span>
                    </div>
                    <h3 class="game-card__name">Snake</h3>
                    <p class="game-card__desc">Mange, grandis, survive. Le classique indémodable.</p>
                </div>
                <div class="game-card__glow" style="--gc: rgba(0,255,136,0.15)"></div>
            </div>

            <div class="game-card" data-color="#00d4ff">
                <div class="game-card__screen" style="background:linear-gradient(135deg,#00101a,#001825)">
                    <span class="game-card__emoji">🧠</span>
                    <div class="game-card__overlay">
                        <a href="{{ route('jeux.memory') }}" class="game-card__play">▶ JOUER</a>
                    </div>
                </div>
                <div class="game-card__body">
                    <div class="game-card__meta">
                        <span class="game-card__tag" style="color:var(--neon-b);border-color:rgba(0,212,255,0.3)">MÉMOIRE</span>
                        <span class="game-card__players">👤 INVITÉ</span>
                    </div>
                    <h3 class="game-card__name">Memory</h3>
                    <p class="game-card__desc">Retrouve toutes les paires le plus vite possible.</p>
                </div>
                <div class="game-card__glow" style="--gc: rgba(0,212,255,0.15)"></div>
            </div>

            <div class="game-card" data-color="#ffdd00">
                <div class="game-card__screen" style="background:linear-gradient(135deg,#1a1400,#0d0b00)">
                    <span class="game-card__emoji">❌</span>
                    <div class="game-card__overlay">
                        <a href="{{ route('jeux.morpion') }}" class="game-card__play">▶ JOUER</a>
                    </div>
                </div>
                <div class="game-card__body">
                    <div class="game-card__meta">
                        <span class="game-card__tag" style="color:var(--neon-y);border-color:rgba(255,221,0,0.3)">RÉFLEXE</span>
                        <span class="game-card__players">👤 INVITÉ</span>
                    </div>
                    <h3 class="game-card__name">Morpion</h3>
                    <p class="game-card__desc">X ou O — qui alignera trois en premier ?</p>
                </div>
                <div class="game-card__glow" style="--gc: rgba(255,221,0,0.15)"></div>
            </div>

            <div class="game-card" data-color="#bf00ff">
                <div class="game-card__screen" style="background:linear-gradient(135deg,#0d0015,#1a002a)">
                    <span class="game-card__emoji">🟦</span>
                    <div class="game-card__overlay">
                        <a href="{{ route('jeux.tetris') }}" class="game-card__play">▶ JOUER</a>
                    </div>
                </div>
                <div class="game-card__body">
                    <div class="game-card__meta">
                        <span class="game-card__tag" style="color:var(--neon-p);border-color:rgba(191,0,255,0.3)">PUZZLE</span>
                        <span class="game-card__players">👤 INVITÉ</span>
                    </div>
                    <h3 class="game-card__name">Tetris</h3>
                    <p class="game-card__desc">Empile les blocs et efface les lignes.</p>
                </div>
                <div class="game-card__glow" style="--gc: rgba(191,0,255,0.15)"></div>
            </div>

            <div class="game-card" data-color="#00ff88">
                <div class="game-card__screen" style="background:linear-gradient(135deg,#001a0a,#002a10)">
                    <span class="game-card__emoji">🏓</span>
                    <div class="game-card__overlay">
                        <a href="{{ route('jeux.pong') }}" class="game-card__play">▶ JOUER</a>
                    </div>
                </div>
                <div class="game-card__body">
                    <div class="game-card__meta">
                        <span class="game-card__tag" style="color:var(--neon-g);border-color:rgba(0,255,136,0.3)">ARCADE</span>
                        <span class="game-card__players">👤 INVITÉ</span>
                    </div>
                    <h3 class="game-card__name">Pong</h3>
                    <p class="game-card__desc">Affronte l'IA dans le jeu vidéo originel.</p>
                </div>
                <div class="game-card__glow" style="--gc: rgba(0,255,136,0.15)"></div>
            </div>

            <div class="game-card" data-color="#ffdd00">
                <div class="game-card__screen" style="background:linear-gradient(135deg,#1a1400,#0d0b00)">
                    <span class="game-card__emoji">🐦</span>
                    <div class="game-card__overlay">
                        <a href="{{ route('jeux.flappy') }}" class="game-card__play">▶ JOUER</a>
                    </div>
                </div>
                <div class="game-card__body">
                    <div class="game-card__meta">
                        <span class="game-card__tag" style="color:var(--neon-y);border-color:rgba(255,221,0,0.3)">RÉFLEXE</span>
                        <span class="game-card__players">👤 INVITÉ</span>
                    </div>
                    <h3 class="game-card__name">Flappy Bird</h3>
                    <p class="game-card__desc">Évite les tuyaux le plus longtemps possible.</p>
                </div>
                <div class="game-card__glow" style="--gc: rgba(255,221,0,0.15)"></div>
            </div>

            @foreach(($uploadedGames ?? []) as $uploadedGame)
            <div class="game-card" data-color="#00d4ff">
                <div class="game-card__screen" style="background:linear-gradient(135deg,#00101a,#001825)">
                    <span class="game-card__emoji">🎮</span>
                    <div class="game-card__overlay">
                        <a href="{{ route('jeux.dynamic', $uploadedGame->slug) }}" class="game-card__play">▶ JOUER</a>
                    </div>
                </div>
                <div class="game-card__body">
                    <div class="game-card__meta">
                        <span class="game-card__tag" style="color:var(--neon-b);border-color:rgba(0,212,255,0.3)">NOUVEAU</span>
                        <span class="game-card__players">👤 INVITÉ</span>
                    </div>
                    <h3 class="game-card__name">{{ $uploadedGame->name }}</h3>
                    <p class="game-card__desc">{{ $uploadedGame->description ?: 'Nouveau jeu ajouté par l\'administration.' }}</p>
                </div>
                <div class="game-card__glow" style="--gc: rgba(0,212,255,0.15)"></div>
            </div>
            @endforeach

        </div>
    </section>

    <!-- CLASSEMENT -->
    <section class="section section--dark" id="classement">
        <div class="section-head">
            <div>
                <p class="section-tag">// COMPETITION</p>
                <h2 class="section-title">Classement global</h2>
            </div>
        </div>

        <div class="lb-wrap">
            @if(isset($topScores) && count($topScores) >= 3)
            <div class="podium">
                <div class="podium-card podium-card--2">
                    <div class="podium-avatar">🥈</div>
                    <div class="podium-name">{{ $topScores[1]->user->pseudo }}</div>
                    <div class="podium-game">{{ $topScores[1]->jeu }}</div>
                    <div class="podium-score" style="color:#c0c0c0">{{ number_format($topScores[1]->score) }} pts</div>
                    <div class="podium-rank" style="background:#c0c0c0;color:#000">2</div>
                </div>
                <div class="podium-card podium-card--1">
                    <div class="podium-crown">👑</div>
                    <div class="podium-avatar">🥇</div>
                    <div class="podium-name">{{ $topScores[0]->user->pseudo }}</div>
                    <div class="podium-game">{{ $topScores[0]->jeu }}</div>
                    <div class="podium-score" style="color:var(--neon-y)">{{ number_format($topScores[0]->score) }} pts</div>
                    <div class="podium-rank" style="background:var(--neon-y);color:#000">1</div>
                </div>
                <div class="podium-card podium-card--3">
                    <div class="podium-avatar">🥉</div>
                    <div class="podium-name">{{ $topScores[2]->user->pseudo }}</div>
                    <div class="podium-game">{{ $topScores[2]->jeu }}</div>
                    <div class="podium-score" style="color:#cd7f32">{{ number_format($topScores[2]->score) }} pts</div>
                    <div class="podium-rank" style="background:#cd7f32;color:#000">3</div>
                </div>
            </div>
            @endif

            <div class="lb-table">
                <div class="lb-table__head">
                    <span>RANG</span>
                    <span>JOUEUR</span>
                    <span>JEU</span>
                    <span>SCORE</span>
                </div>
                @if(isset($topScores) && count($topScores) > 3)
                    @foreach($topScores->skip(3) as $index => $s)
                    <div class="lb-row">
                        <span class="lb-row__rank">{{ str_pad($index + 4, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="lb-row__player"><span class="lb-row__avatar">🎮</span> {{ $s->user->pseudo }}</span>
                        <span class="lb-row__game">{{ $s->jeu }}</span>
                        <span class="lb-row__score" style="color:var(--neon-g)">{{ number_format($s->score) }}</span>
                    </div>
                    @endforeach
                @else
                    <div class="lb-row" style="justify-content:center;color:var(--muted);">
                        Aucun score enregistré pour l'instant.
                    </div>
                @endif

                <div class="lb-row lb-row--you">
                    <span class="lb-row__rank">??</span>
                    <span class="lb-row__player"><span class="lb-row__avatar">😎</span> Toi ?</span>
                    <span class="lb-row__game">—</span>
                    <span class="lb-row__score" style="color:var(--muted)">
                        <a href="{{ route('inscription') }}" style="color:var(--neon-g);text-decoration:none;">Inscris-toi !</a>
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-grid">
            <div class="footer-brand">
                <span class="footer-logo">PIXELZONE</span>
                <p class="footer-brand__desc">La plateforme de mini-jeux gratuite pour joueurs passionnés. Joue, crée, partage.</p>
            </div>
            <div class="footer-col">
                <h4 class="footer-col__title">JEUX</h4>
                <ul class="footer-col__list">
                    <li><a href="#jeux">Tous les jeux</a></li>
                    <li><a href="{{ route('jeux.snake') }}">Snake</a></li>
                    <li><a href="{{ route('jeux.tetris') }}">Tetris</a></li>
                    <li><a href="{{ route('jeux.morpion') }}">Morpion</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4 class="footer-col__title">COMPTE</h4>
                <ul class="footer-col__list">
                    <li><a href="{{ route('connexion') }}">Connexion</a></li>
                    <li><a href="{{ route('inscription') }}">Inscription</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4 class="footer-col__title">INFO</h4>
                <ul class="footer-col__list">
                    <li><a href="#">À propos</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© 2026 PIXELZONE — TOUS DROITS RESERVES</span>
        </div>
    </footer>

    @if(session('score_flash'))
    <div class="score-flash" id="scoreFlash">
        <button class="score-flash__close" id="closeFlash">✕</button>
        <div class="score-flash__label">⚡ TON SCORE</div>
        <div class="score-flash__value">{{ session('score_flash') }} pts</div>
        <div class="score-flash__note">
            Non sauvegardé — <a href="{{ route('inscription') }}">crée un compte</a> pour le garder !
        </div>
    </div>
    @endif

@endsection

@push('scripts')
    <script src="{{ asset('js/invite.js') }}"></script>
@endpush