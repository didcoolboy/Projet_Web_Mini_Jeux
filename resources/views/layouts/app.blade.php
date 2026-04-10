<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PIXELZONE')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
</head>
<body>

    <div class="pixel-grid"></div>
    <div class="pixel-field" id="pixelField"></div>
    <div class="scanlines"></div>
    <div class="cursor" id="cursor"></div>

    @yield('content')

    <script src="{{ asset('js/script.js') }}"></script>
    @stack('scripts')

</body>
</html>