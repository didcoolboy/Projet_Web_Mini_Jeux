<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
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
        try {
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
        } catch (QueryException $e) {
            report($e);

            return back()
                ->withInput($request->except('password'))
                ->withErrors(['email' => 'Service temporairement indisponible. Verifie que MySQL est bien lance, puis reessaie.']);
        }

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
        $coreGameSlugs = ['snake', 'morpion', 'tetris', 'pong', 'memory', 'flappy'];

        $topScores = \App\Models\Score::with(['user', 'game'])
            ->whereRaw('scores.id = (SELECT s2.id FROM scores s2 WHERE s2.game_id = scores.game_id ORDER BY s2.score DESC, s2.id DESC LIMIT 1)')
            ->orderByDesc('scores.score')
            ->get();

        $totalScores = DB::table('users')
            ->leftJoin('scores', 'users.id', '=', 'scores.user_id')
            ->select('users.id', 'users.pseudo', DB::raw('COALESCE(SUM(scores.score), 0) as total_score'))
            ->groupBy('users.id', 'users.pseudo')
            ->orderByDesc('total_score')
            ->limit(5)
            ->get();

        $uploadedGames = Game::query()
            ->whereNotIn('slug', $coreGameSlugs)
            ->latest()
            ->get();

        $availableGameSlugs = Game::query()
            ->whereIn('slug', $coreGameSlugs)
            ->pluck('slug')
            ->all();

        return view('auth.invite', compact('topScores', 'totalScores', 'uploadedGames', 'availableGameSlugs'));
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

    public function showResetForm(Request $request, string $token)
    {
        $email = $request->query('email');
        return view('auth.reset_mot_de_passe', ['token' => $token, 'email' => $email ?? '']);
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