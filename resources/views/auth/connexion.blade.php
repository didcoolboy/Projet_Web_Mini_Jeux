@extends('layouts.app')

@section('title', 'PIXELZONE — Connexion')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/connexion.css') }}">
@endpush

@section('content')

    <a href="{{ route('welcome') }}" class="back-link">‹ RETOUR</a>

    <main class="auth-page">
        <div class="auth-card">

            <span class="auth-logo">PIXELZONE</span>
            <p class="auth-subtitle">Connecte-toi pour jouer et sauvegarder tes scores.</p>

            @if(session('error'))
                <div class="auth-error-msg">{{ session('error') }}</div>
            @endif

            <form class="auth-form" id="loginForm" action="{{ route('connexion.post') }}" method="POST">
                @csrf

                <div class="field-group">
                    <label class="field-label" for="email">EMAIL</label>
                    <div class="field-wrap">
                        <span class="field-icon">@</span>
                        <input type="email" id="email" name="email" class="field-input @error('email') is-error @enderror"
                            placeholder="ton@email.com" autocomplete="email"
                            value="{{ old('email') }}" required>
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
                            placeholder="••••••••" autocomplete="current-password" required>
                        <button type="button" class="field-toggle" id="togglePwd" aria-label="Voir">👁</button>
                    </div>
                    @error('password')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field-options">
                    <label class="checkbox-wrap">
                        <input type="checkbox" name="remember" id="remember">
                        <span class="checkbox-custom"></span>
                        <span class="checkbox-label">Se souvenir de moi</span>
                    </label>
                    <a href="{{ route('mot_de_passe_oublie') }}" class="link-forgot">Mot de passe oublié ?</a>
                </div>

                <button type="submit" class="btn-auth" id="submitBtn">
                    <span class="btn-auth__text">▶ SE CONNECTER</span>
                    <span class="btn-auth__loading" hidden>⏳ CONNEXION...</span>
                </button>

            </form>

            <div class="auth-sep"><span>ou</span></div>

            <a href="{{ route('accueil.invite') }}" class="btn-guest">→ Continuer en tant qu'invité</a>

            <p class="auth-switch">
                Pas encore de compte ?
                <a href="{{ route('inscription') }}" class="auth-switch__link">Créer un compte</a>
            </p>

        </div>
    </main>

@endsection

@push('scripts')
    <script src="{{ asset('js/connexion.js') }}"></script>
@endpush