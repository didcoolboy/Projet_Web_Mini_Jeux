<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin — PIXELZONE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <nav>
        <a href="/">PIXELZONE</a>
        <a href="{{ route('admin.users.index') }}">Comptes</a>
        <a href="{{ route('admin.games.index') }}">Jeux</a>
        <a href="{{ route('admin.requests.index') }}">
            Demandes
            @php $pending = \App\Models\GameRequest::where('status','pending')->count() @endphp
            @if($pending > 0)
                <span class="badge">{{ $pending }}</span>
            @endif
        </a>
        <span class="badge-admin">ADMIN</span>
        <span>{{ auth()->user()->name }}</span>
    </nav>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    @yield('content')
</body>
</html>