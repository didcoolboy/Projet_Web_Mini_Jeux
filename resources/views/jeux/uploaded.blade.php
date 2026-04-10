<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $game->name }} - PixelZone</title>
    <style>
        :root {
            color-scheme: dark;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: radial-gradient(circle at top left, #1b2f47 0%, #0d1625 60%, #070b12 100%);
            color: #f5f8ff;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(8px);
        }

        .title {
            margin: 0;
            font-size: 1.1rem;
            letter-spacing: 0.02em;
        }

        .btn-back {
            color: #d6e2ff;
            text-decoration: none;
            font-weight: 600;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 8px;
            padding: 8px 12px;
        }

        .frame-wrap {
            flex: 1;
            padding: 12px;
        }

        iframe {
            width: 100%;
            height: calc(100vh - 90px);
            border: 0;
            border-radius: 12px;
            background: #000;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.35);
        }
    </style>
</head>
<body>
    <header class="topbar">
        <h1 class="title">{{ $game->name }}</h1>
        <a class="btn-back" href="{{ route('accueil.invite') }}">Retour</a>
    </header>

    <main class="frame-wrap">
        <iframe src="{{ $gameUrl }}" title="{{ $game->name }}"></iframe>
    </main>
</body>
</html>
