@extends('layouts.app')

@section('title', 'PIXELZONE — Inscription')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/connexion.css') }}">
@endpush

@section('content')

    <a href="{{ route('connexion') }}" class="back-link">‹ RETOUR</a>

    <main class="auth-page">
        <div class="auth-card">

            <a href="{{ route('welcome') }}" class="auth-logo">PIXELZONE</a>
            <p class="auth-subtitle">Crée ton compte pour jouer et sauvegarder tes scores.</p>

            @if(session('error'))
                <div class="auth-error-msg">{{ session('error') }}</div>
            @endif

            <form class="auth-form" action="{{ route('inscription.post') }}" method="POST">
                @csrf

                <div class="field-group">
                    <label class="field-label" for="nom">NOM</label>
                    <div class="field-wrap">
                        <span class="field-icon">👤</span>
                        <input type="text" id="nom" name="nom" class="field-input @error('nom') is-error @enderror"
                            placeholder="Ton nom" value="{{ old('nom') }}" required>
                    </div>
                    @error('nom')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field-group">
                    <label class="field-label" for="prenom">PRENOM</label>
                    <div class="field-wrap">
                        <span class="field-icon">👤</span>
                        <input type="text" id="prenom" name="prenom" class="field-input @error('prenom') is-error @enderror"
                            placeholder="Ton prénom" value="{{ old('prenom') }}" required>
                    </div>
                    @error('prenom')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field-group">
                    <label class="field-label" for="pseudo">PSEUDO</label>
                    <div class="field-wrap">
                        <span class="field-icon">👤</span>
                        <input type="text" id="pseudo" name="pseudo" class="field-input @error('pseudo') is-error @enderror"
                            placeholder="Ton pseudo" value="{{ old('pseudo') }}" required>
                    </div>
                    @error('pseudo')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field-group">
                    <label class="field-label" for="email">EMAIL</label>
                    <div class="field-wrap">
                        <span class="field-icon">@</span>
                        <input type="email" id="email" name="email" class="field-input @error('email') is-error @enderror"
                            placeholder="ton@email.com" value="{{ old('email') }}" autocomplete="email" required>
                    </div>
                    @error('email')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field-group">
                    <label class="field-label" for="password">MOT DE PASSE</label>
                    <div class="field-wrap">
                        <span class="field-icon">🔒</span>
                        <input type="password" id="password" name="password" class="field-input @error('password') is-error @enderror"
                            placeholder="••••••••" autocomplete="new-password" required>
                        <button type="button" class="field-toggle" id="togglePwd" aria-label="Voir">👁</button>
                    </div>
                    @error('password')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-auth">
                    <span class="btn-auth__text">▶ CRÉER MON COMPTE</span>
                </button>

            </form>

            <div class="auth-sep"><span>ou</span></div>

            <a href="{{ route('accueil') }}" class="btn-guest">→ Continuer en tant qu'invité</a>

            <p class="auth-switch">
                Déjà un compte ?
                <a href="{{ route('connexion') }}" class="auth-switch__link">Se connecter</a>
            </p>

        </div>
    </main>

@endsection

@push('scripts')
    <script src="{{ asset('js/connexion.js') }}"></script>
@endpush