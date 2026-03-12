@extends('layouts.app')

@section('title', 'PIXELZONE')

@section('content')
<main class="welcome">
    <div class="welcome-inner">

        <p class="welcome-label">★ BIENVENUE SUR ★</p>

        <h1 class="welcome-logo">PIXELZONE</h1>

        <p class="welcome-sub">La plateforme de mini-jeux des SIO 2.</p>

        <a href="{{ route('connexion') }}" class="btn-enter">
            ▶ ACCEDER AU SITE
        </a>
    </div>
</main>
@endsection