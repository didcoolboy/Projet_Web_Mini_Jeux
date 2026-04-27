@extends('layouts.app')

@section('title', 'PIXELZONE — Mot de passe oublié')

@section('content')
    <a href="javascript:history.back()" class="back-link">‹ RETOUR</a>

    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-card">
                <h1 class="auth-title">MOT DE PASSE OUBLIÉ</h1>
                <p class="auth-desc">Rentre ton adresse email et on te renvoie un lien pour réinitialiser ton mot de passe.</p>

                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" class="form">
                    @csrf

                    <div class="form-group">
                        <label class="field-label" for="email">EMAIL</label>
                        <input type="email" id="email" name="email" class="field-input @error('email') is-error @enderror"
                            placeholder="ton@email.com" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn-primary btn-full">
                        ENVOYER LE LIEN DE RESET
                    </button>
                </form>

                <div class="auth-footer">
                    <a href="{{ route('connexion') }}" class="link-inline">Retour à la connexion</a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .auth-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            background: linear-gradient(135deg, #0a0e16 0%, #10141e 50%, #0a0c14 100%);
        }

        .auth-container {
            width: 100%;
            max-width: 420px;
        }

        .auth-card {
            background: linear-gradient(135deg, #141a28 0%, #0f1420 100%);
            border: 1px solid rgba(57, 255, 20, 0.1);
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        }

        .auth-title {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 0.08em;
            color: #39ff14;
            margin-bottom: 8px;
            text-align: center;
        }

        .auth-desc {
            font-size: 13px;
            color: #888;
            text-align: center;
            margin-bottom: 24px;
            line-height: 1.4;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .field-label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.08em;
            color: #aaa;
            margin-bottom: 6px;
        }

        .field-input {
            width: 100%;
            padding: 10px 12px;
            background: #0a0c14;
            border: 1px solid #1a2840;
            border-radius: 6px;
            color: #ddd;
            font-family: inherit;
            font-size: 13px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .field-input:focus {
            border-color: #39ff14;
            box-shadow: 0 0 12px rgba(57, 255, 20, 0.2);
        }

        .field-input.is-error {
            border-color: #ff4444;
        }

        .error-msg {
            display: block;
            font-size: 12px;
            color: #ff6b6b;
            margin-top: 4px;
        }

        .btn-primary {
            background: #39ff14;
            color: #000;
            border: none;
            padding: 10px 16px;
            font-weight: 600;
            font-size: 12px;
            letter-spacing: 0.06em;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background: #4bff28;
            box-shadow: 0 0 20px rgba(57, 255, 20, 0.4);
        }

        .btn-full {
            width: 100%;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
            line-height: 1.4;
        }

        .alert-success {
            background: rgba(57, 255, 20, 0.1);
            border: 1px solid rgba(57, 255, 20, 0.3);
            color: #39ff14;
        }

        .alert-error {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #ff6b6b;
        }

        .auth-footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
        }

        .link-inline {
            color: #39ff14;
            text-decoration: none;
            transition: color 0.2s;
        }

        .link-inline:hover {
            color: #4bff28;
            text-decoration: underline;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #888;
            text-decoration: none;
            font-size: 13px;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: #39ff14;
        }
    </style>
@endsection
