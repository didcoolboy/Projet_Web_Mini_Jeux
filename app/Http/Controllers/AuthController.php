<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Score;
use App\Models\Game;

class AuthController extends Controller
{
    public function showConnexion()
    {
        return view('auth.connexion');
    }

    public function connexion(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();
        
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
        
            return redirect()->route('accueil');
        }

        return back()->withErrors([
            'email' => 'Email ou mot de passe incorrect.',
        ])->withInput();
    }

    public function showInscription()
    {
        return view('auth.inscription');
    }

    public function inscription(Request $request)
    {
        $request->validate([
            'nom'      => 'required|string|max:50',
            'prenom'   => 'required|string|max:50',
            'pseudo'   => 'required|string|max:50|unique:users',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'nom'      => $request->nom,
            'prenom'   => $request->prenom,
            'pseudo'   => $request->pseudo,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'joueur',
        ]);

        Auth::login($user);

        // Rediriger vers la page invité si venu de là
        if ($request->query('from') === 'invite') {
            return redirect()->route('accueil.invite');
        }

        return redirect()->route('accueil');
    }

    public function deconnexion(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('welcome');
    }


    public function showInvite()
    {
        $topScores = \App\Models\Score::with('user')
            ->orderByDesc('score')
            ->take(10)
            ->get();

        $uploadedGames = Game::query()
            ->whereNotIn('slug', ['snake', 'morpion', 'tetris', 'pong', 'memory', 'flappy'])
            ->latest()
            ->get();

        return view('auth.invite', compact('topScores', 'uploadedGames'));
    }

    public function showForgotPassword()
    {
        return view('auth.mot_de_passe_oublie');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $status = \Illuminate\Support\Facades\Password::sendResetLink(
            $request->only('email')
        );

        return $status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT
            ? back()->with('status', 'Un lien de réinitialisation a été envoyé à ton email.')
            : back()->withErrors(['email' => 'Impossible d\'envoyer le lien.']);
    }

    public function showResetForm(string $token, ?string $email = null)
    {
        return view('auth.reset_mot_de_passe', ['token' => $token, 'email' => $email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'                 => 'required',
            'email'                 => 'required|email|exists:users,email',
            'password'              => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        $status = \Illuminate\Support\Facades\Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        return $status === \Illuminate\Support\Facades\Password::PASSWORD_RESET
            ? redirect()->route('connexion')->with('status', 'Mot de passe réinitialisé ! Connecte-toi avec ton nouveau mot de passe.')
            : back()->withErrors(['email' => 'Erreur lors de la réinitialisation.']);
    }

}