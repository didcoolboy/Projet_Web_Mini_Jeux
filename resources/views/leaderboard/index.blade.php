<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classement — PixelZone</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <style>
        /* ── Reset & Base ─────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --green:   #39ff14;
            --cyan:    #00f5ff;
            --magenta: #ff00ff;
            --yellow:  #ffff00;
            --red:     #ff4444;
            --bg:      #060d06;
            --pixel:   'Press Start 2P', monospace;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: var(--pixel);
            background-color: var(--bg);
            background-image:
                linear-gradient(rgba(57,255,20,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(57,255,20,.03) 1px, transparent 1px),
                radial-gradient(ellipse 60% 40% at 50% 50%, rgba(57,255,20,.06) 0%, transparent 70%);
            background-size: 40px 40px, 40px 40px, 100% 100%;
            min-height: 100vh;
            color: #fff;
            overflow-x: hidden;
        }

        /* Scanlines */
        body::after {
            content: '';
            position: fixed; inset: 0;
            background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(0,0,0,.08) 2px, rgba(0,0,0,.08) 4px);
            pointer-events: none;
            z-index: 9999;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: var(--green); }

        /* ── Animations ───────────────────────────────────── */
        @keyframes glow {
            0%,100% { text-shadow: 0 0 10px var(--green), 0 0 20px var(--green); }
            50%      { text-shadow: 0 0 20px var(--green), 0 0 40px var(--green), 0 0 60px var(--green); }
        }
        @keyframes pixelFloat {
            0%,100% { transform: translateY(0); }
            50%      { transform: translateY(-8px); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes barGrow {
            from { width: 0 !important; }
        }

        /* ── Floating pixels ──────────────────────────────── */
        .pixel-bg { position: fixed; inset: 0; pointer-events: none; z-index: 0; overflow: hidden; }
        .pixel-dot {
            position: absolute;
            image-rendering: pixelated;
            animation: pixelFloat 4s ease-in-out infinite;
        }

        /* ── Layout ───────────────────────────────────────── */
        .wrapper {
            position: relative; z-index: 1;
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px 60px;
        }

        /* ── Header ───────────────────────────────────────── */
        .header { text-align: center; margin-bottom: 40px; animation: fadeIn .6s ease both; }

        .header__badge {
            display: inline-block;
            border: 1px solid rgba(57,255,20,.4);
            padding: 4px 16px;
            margin-bottom: 16px;
            font-size: 8px;
            color: rgba(57,255,20,.7);
            letter-spacing: 4px;
        }

        .header__title {
            font-size: clamp(20px, 4vw, 32px);
            color: var(--green);
            letter-spacing: 6px;
            animation: glow 3s ease-in-out infinite;
        }

        .header__sub {
            font-size: 8px;
            color: rgba(255,255,255,.35);
            letter-spacing: 2px;
            margin-top: 10px;
        }

        /* ── Stats perso ──────────────────────────────────── */
        .my-stats {
            background: rgba(0,245,255,.05);
            border: 2px solid rgba(0,245,255,.25);
            box-shadow: 0 0 30px rgba(0,245,255,.08);
            padding: 20px;
            margin-bottom: 32px;
            animation: fadeIn .6s .1s ease both;
        }

        .my-stats__label {
            font-size: 8px;
            color: rgba(0,245,255,.6);
            letter-spacing: 3px;
            margin-bottom: 16px;
        }

        .stats-grid {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .stat-card {
            flex: 1; min-width: 110px;
            background: rgba(0,0,0,.5);
            border: 1px solid rgba(0,245,255,.15);
            padding: 14px 16px;
        }

        .stat-card__icon  { font-size: 18px; margin-bottom: 8px; }
        .stat-card__value { font-size: 14px; color: var(--cyan); text-shadow: 0 0 10px var(--cyan); margin-bottom: 4px; }
        .stat-card__key   { font-size: 7px; color: rgba(255,255,255,.35); }

        /* ── Tabs jeux ────────────────────────────────────── */
        .game-tabs {
            display: flex; gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 20px;
            animation: fadeIn .6s .2s ease both;
        }

        .game-tab {
            font-family: var(--pixel);
            font-size: 8px;
            padding: 8px 12px;
            border: 2px solid rgba(57,255,20,.3);
            background: transparent;
            color: var(--green);
            cursor: pointer;
            transition: all .2s;
            text-decoration: none;
            display: flex; align-items: center; gap: 6px;
        }

        .game-tab:hover,
        .game-tab--active {
            background: var(--green);
            color: #000;
            border-color: var(--green);
            box-shadow: 0 0 12px var(--green);
        }

        /* ── Barre filtres ────────────────────────────────── */
        .toolbar {
            display: flex; gap: 12px; align-items: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
            animation: fadeIn .6s .25s ease both;
        }

        .filter-toggle {
            display: flex;
            border: 2px solid rgba(57,255,20,.3);
            overflow: hidden;
        }

        .filter-btn {
            font-family: var(--pixel);
            font-size: 7px;
            padding: 8px 14px;
            background: transparent;
            color: rgba(57,255,20,.5);
            border: none;
            cursor: pointer;
            transition: all .2s;
            text-decoration: none;
            white-space: nowrap;
        }

        .filter-btn:hover,
        .filter-btn--active {
            background: rgba(57,255,20,.15);
            color: var(--green);
        }

        .search-wrap {
            flex: 1; min-width: 200px; position: relative;
        }

        .search-input {
            width: 100%;
            font-family: var(--pixel);
            font-size: 8px;
            padding: 8px 14px;
            background: rgba(0,0,0,.6);
            border: 2px solid rgba(57,255,20,.3);
            color: var(--green);
            outline: none;
            letter-spacing: 1px;
            transition: border-color .2s;
        }

        .search-input:focus { border-color: var(--green); box-shadow: 0 0 8px rgba(57,255,20,.2); }
        .search-input::placeholder { color: rgba(57,255,20,.3); }

        .count-label {
            font-size: 8px;
            color: rgba(57,255,20,.4);
            white-space: nowrap;
        }

        /* ── Légende ──────────────────────────────────────── */
        .legend {
            display: flex; gap: 20px; flex-wrap: wrap;
            font-size: 7px; color: rgba(255,255,255,.3);
            margin-bottom: 12px;
        }
        .legend span { color: rgba(57,255,20,.5); }

        /* ── Leaderboard ──────────────────────────────────── */
        .board {
            background: rgba(0,0,0,.6);
            border: 2px solid rgba(57,255,20,.2);
            overflow: hidden;
            animation: fadeIn .6s .3s ease both;
        }

        .board__head {
            display: flex; align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-bottom: 2px solid rgba(57,255,20,.15);
            background: rgba(57,255,20,.04);
        }

        .board__head-cell {
            font-size: 7px;
            color: rgba(57,255,20,.45);
        }

        .board__empty {
            text-align: center;
            padding: 48px;
            font-size: 8px;
            color: rgba(57,255,20,.3);
        }

        /* ── Ligne joueur ─────────────────────────────────── */
        .row {
            display: flex; align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-bottom: 1px solid rgba(255,255,255,.04);
            position: relative;
            transition: background .2s;
            cursor: pointer;
        }

        .row:last-child { border-bottom: none; }

        .row:hover { background: rgba(57,255,20,.04); }

        .row--you {
            background: linear-gradient(90deg, rgba(0,245,255,.08), rgba(0,245,255,.02));
            border: 1px solid rgba(0,245,255,.3) !important;
        }
        .row--you:hover { background: linear-gradient(90deg, rgba(0,245,255,.13), rgba(0,245,255,.04)); }

        .row--friend {
            background: linear-gradient(90deg, rgba(255,0,255,.06), transparent);
            border-bottom: 1px solid rgba(255,0,255,.1) !important;
        }
        .row--friend:hover { background: linear-gradient(90deg, rgba(255,0,255,.1), transparent); }

        /* Barre colorée gauche top 3 */
        .row__side {
            position: absolute; left: 0; top: 0; bottom: 0; width: 3px;
        }

        /* Rank badge */
        .rank-badge {
            font-size: 10px;
            padding: 4px 8px;
            min-width: 40px;
            text-align: center;
            border: 2px solid;
            flex-shrink: 0;
        }

        .rank-badge--1 { background: #ffd700; border-color: #ffd700; color: #000; box-shadow: 0 0 10px #ffd700; }
        .rank-badge--2 { background: #c0c0c0; border-color: #c0c0c0; color: #000; box-shadow: 0 0 8px #c0c0c0; }
        .rank-badge--3 { background: #cd7f32; border-color: #cd7f32; color: #000; box-shadow: 0 0 8px #cd7f32; }
        .rank-badge--default { background: #0a0f0a; border-color: rgba(57,255,20,.3); color: var(--green); }

        /* Infos joueur */
        .player-info { flex: 1; min-width: 0; }

        .player-info__top {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 6px;
        }

        .player-name {
            font-size: 9px;
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }

        .player-name--you    { color: var(--cyan);    text-shadow: 0 0 8px var(--cyan); }
        .player-name--friend { color: var(--magenta); text-shadow: 0 0 8px var(--magenta); }
        .player-name--other  { color: var(--green);   text-shadow: 0 0 6px var(--green); }

        .tag {
            font-size: 7px;
            padding: 2px 5px;
            flex-shrink: 0;
        }

        .tag--you    { color: var(--cyan);    background: rgba(0,245,255,.15);  border: 1px solid rgba(0,245,255,.4); }
        .tag--friend { color: var(--magenta); background: rgba(255,0,255,.12);  border: 1px solid rgba(255,0,255,.4); }

        /* Score bar */
        .score-bar-wrap {
            background: #0a0f0a;
            border: 1px solid #1a2a1a;
            height: 6px;
            overflow: hidden;
        }

        .score-bar {
            height: 100%;
            transition: width 1s cubic-bezier(.4,0,.2,1);
            animation: barGrow .8s ease both;
        }
        .score-bar--you    { background: var(--cyan);    box-shadow: 0 0 6px var(--cyan); }
        .score-bar--friend { background: var(--magenta); box-shadow: 0 0 6px var(--magenta); }
        .score-bar--other  { background: var(--green);   box-shadow: 0 0 6px var(--green); }

        /* Score value */
        .score-value {
            text-align: right;
            min-width: 90px;
            flex-shrink: 0;
        }

        .score-value__pts {
            font-size: 11px;
        }
        .score-value__pts--you    { color: var(--cyan);    text-shadow: 0 0 10px var(--cyan); }
        .score-value__pts--friend { color: var(--magenta); text-shadow: 0 0 10px var(--magenta); }
        .score-value__pts--other  { color: var(--green);   text-shadow: 0 0 8px var(--green); }
        .score-value__label { font-size: 7px; color: rgba(255,255,255,.3); margin-top: 3px; }

        /* ── Ma position (sticky bas) ─────────────────────── */
        .my-position {
            margin-top: 16px;
            background: rgba(0,245,255,.06);
            border: 1px dashed rgba(0,245,255,.3);
            padding: 12px 16px;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 8px;
        }

        .my-position__label { font-size: 7px; color: rgba(0,245,255,.6); letter-spacing: 2px; }
        .my-position__value { font-size: 10px; color: var(--cyan); text-shadow: 0 0 10px var(--cyan); }

        /* ── Footer ───────────────────────────────────────── */
        .footer {
            text-align: center;
            margin-top: 32px;
            font-size: 7px;
            color: rgba(57,255,20,.2);
            letter-spacing: 2px;
        }
    </style>
</head>
<body>

{{-- ── Pixels décoratifs ─────────────────────────────────── --}}
<div class="pixel-bg" id="pixelBg"></div>

<div class="wrapper">

    {{-- ── En-tête ────────────────────────────────────────── --}}
    <div class="header">
        <div class="header__badge">★ PIXELZONE ★</div>
        <div class="header__title">CLASSEMENT</div>
        <div class="header__sub">SAISON 01 — {{ strtoupper(now()->translatedFormat('F Y')) }}</div>
    </div>

    {{-- ── Stats perso ────────────────────────────────────── --}}
    <div class="my-stats">
        <div class="my-stats__label">▶ VOS STATS</div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card__icon">🏆</div>
                <div class="stat-card__value">#{{ $myStats['global_rank'] }}</div>
                <div class="stat-card__key">RANG GLOBAL</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__icon">⭐</div>
                <div class="stat-card__value">{{ number_format($myStats['total_score']) }}</div>
                <div class="stat-card__key">SCORE TOTAL</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__icon">👥</div>
                <div class="stat-card__value">{{ $myStats['friends_count'] }}</div>
                <div class="stat-card__key">AMIS</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__icon">🎮</div>
                <div class="stat-card__value">{{ $myStats['games_count'] }}</div>
                <div class="stat-card__key">MINI-JEUX</div>
            </div>
        </div>
    </div>

    {{-- ── Tabs jeux ───────────────────────────────────────── --}}
    <div class="game-tabs">
        <a href="{{ request()->fullUrlWithQuery(['game' => 'all', 'filter' => $filter]) }}"
           class="game-tab {{ $activeGame === 'all' ? 'game-tab--active' : '' }}">
            🏆 GLOBAL
        </a>
        @foreach($games as $game)
            <a href="{{ request()->fullUrlWithQuery(['game' => $game->slug, 'filter' => $filter]) }}"
               class="game-tab {{ $activeGame === $game->slug ? 'game-tab--active' : '' }}">
                {{ $game->icon }} {{ strtoupper($game->name) }}
            </a>
        @endforeach
    </div>

    {{-- ── Toolbar ─────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('leaderboard.index') }}" class="toolbar">
        <input type="hidden" name="game" value="{{ $activeGame }}">

        {{-- Filtre all / amis --}}
        <div class="filter-toggle">
            <a href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}"
               class="filter-btn {{ $filter === 'all' ? 'filter-btn--active' : '' }}">
                🌍 TOUS
            </a>
            <a href="{{ request()->fullUrlWithQuery(['filter' => 'friends']) }}"
               class="filter-btn {{ $filter === 'friends' ? 'filter-btn--active' : '' }}">
                👥 AMIS
            </a>
        </div>

        {{-- Recherche --}}
        <div class="search-wrap">
            <input
                type="text"
                name="search"
                class="search-input"
                placeholder="RECHERCHER UN JOUEUR..."
                value="{{ $search }}"
                autocomplete="off"
            >
        </div>

        <span class="count-label">
            {{ count($leaderboard) }} JOUEUR{{ count($leaderboard) > 1 ? 'S' : '' }}
        </span>
    </form>

    {{-- ── Légende ─────────────────────────────────────────── --}}
    <div class="legend">
        <span>🟩 <span>JOUEUR</span></span>
        <span>🟦 <span style="color:rgba(0,245,255,.5)">VOUS</span></span>
        <span>🟪 <span style="color:rgba(255,0,255,.5)">AMI</span></span>
    </div>

    {{-- ── Tableau classement ──────────────────────────────── --}}
    <div class="board">

        {{-- En-tête colonnes --}}
        <div class="board__head">
            <div class="board__head-cell" style="width:50px">RANG</div>
            <div class="board__head-cell" style="flex:1">JOUEUR</div>
            <div class="board__head-cell" style="min-width:90px;text-align:right">SCORE</div>
        </div>

        @forelse($leaderboard as $index => $player)
            @php
                $type      = $player['is_you'] ? 'you' : ($player['is_friend'] ? 'friend' : 'other');
                $rankClass = $player['rank'] <= 3 ? "rank-badge--{$player['rank']}" : 'rank-badge--default';
                $sideColor = match($player['rank']) { 1 => '#ffd700', 2 => '#c0c0c0', 3 => '#cd7f32', default => null };
                $maxScore  = $leaderboard[0]['total_score'] ?? 1;
                $barPct    = $maxScore > 0 ? round(($player['total_score'] / $maxScore) * 100, 1) : 0;
                $displayScore = $activeGame === 'all'
                    ? $player['total_score']
                    : ($player['per_game'][$activeGame] ?? 0);
                $barPctGame = $maxScore > 0 ? round(($displayScore / $maxScore) * 100, 1) : 0;
            @endphp

            <div class="row row--{{ $type }}"
                 style="animation: slideIn .4s ease {{ $index * 0.05 }}s both;">

                {{-- Barre colorée gauche top 3 --}}
                @if($sideColor)
                    <div class="row__side" style="background:{{ $sideColor }};box-shadow:0 0 8px {{ $sideColor }};"></div>
                @endif

                {{-- Rang --}}
                <div class="rank-badge {{ $rankClass }}">
                    #{{ str_pad($player['rank'], 2, '0', STR_PAD_LEFT) }}
                </div>

                {{-- Infos --}}
                <div class="player-info">
                    <div class="player-info__top">
                        <span class="player-name player-name--{{ $type }}">
                            {{ $player['name'] }}
                        </span>
                        @if($player['is_you'])
                            <span class="tag tag--you">YOU</span>
                        @elseif($player['is_friend'])
                            <span class="tag tag--friend">AMI</span>
                        @endif
                    </div>
                    <div class="score-bar-wrap">
                        <div class="score-bar score-bar--{{ $type }}"
                             style="width:{{ $barPctGame }}%"></div>
                    </div>
                </div>

                {{-- Score --}}
                <div class="score-value">
                    <div class="score-value__pts score-value__pts--{{ $type }}">
                        {{ number_format($displayScore) }}
                    </div>
                    <div class="score-value__label">PTS</div>
                </div>
            </div>

        @empty
            <div class="board__empty">AUCUN JOUEUR TROUVÉ</div>
        @endforelse

    </div>

    {{-- ── Ma position --}}
    @php
        $myEntry = collect($leaderboard)->firstWhere('is_you', true);
    @endphp
    @if(!$myEntry && !$search)
        <div class="my-position">
            <span class="my-position__label">▶ VOTRE POSITION GLOBALE</span>
            <span class="my-position__value">
                #{{ $myStats['global_rank'] }} — {{ number_format($myStats['total_score']) }} PTS
            </span>
        </div>
    @endif

    {{-- ── Footer ──────────────────────────────────────────── --}}
    <div class="footer">
        PIXELZONE © {{ date('Y') }} — LA PLATEFORME DE MINI-JEUX DES SIO 2
    </div>

</div>

<script>
    // ── Pixels flottants générés en JS ─────────────────────
    (function () {
        const colors = ['#39ff14','#00f5ff','#ff00ff','#ffff00','#ff4444'];
        const bg = document.getElementById('pixelBg');
        for (let i = 0; i < 35; i++) {
            const el = document.createElement('div');
            el.className = 'pixel-dot';
            const size = Math.random() > .7 ? 8 : 5;
            Object.assign(el.style, {
                left:             (Math.random() * 100) + '%',
                top:              (Math.random() * 100) + '%',
                width:            size + 'px',
                height:           size + 'px',
                backgroundColor:  colors[Math.floor(Math.random() * colors.length)],
                opacity:          .5,
                animationDuration:(3 + Math.random() * 4) + 's',
                animationDelay:   (Math.random() * 4) + 's',
            });
            bg.appendChild(el);
        }
    })();

    // ── Soumission auto du formulaire de recherche ──────────
    (function () {
        const input = document.querySelector('.search-input');
        if (!input) return;
        let timer;
        input.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => input.closest('form').submit(), 400);
        });
    })();
</script>

</body>
</html>
