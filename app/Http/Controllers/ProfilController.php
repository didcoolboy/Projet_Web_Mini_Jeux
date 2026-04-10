<?php

namespace App\Http\Controllers;

use App\Models\Score;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    public function show(User $user)
    {
        // Score total réel
        $scoreTotal = $user->totalScore();

        // Rang global
        $rang = User::all()->filter(function ($u) use ($scoreTotal) {
            return $u->totalScore() > $scoreTotal;
        })->count() + 1;

        if ($rang === 1) {
            $rangLabel = 'gold';
        } elseif ($rang === 2) {
            $rangLabel = 'silver';
        } elseif ($rang === 3) {
            $rangLabel = 'bronze';
        } else {
            $rangLabel = null;
        }

        // Parties (= entrées dans scores)
        $nbParties = Score::where('user_id', $user->id)->count();

        // Parties cette semaine
        $partiesSemaine = Score::where('user_id', $user->id)
            ->where('created_at', '>=', now()->startOfWeek())
            ->count();

        // Nombre de jeux joués (jeux distincts)
        $nbJeux = $user->gamesPlayedCount();

        // Dernière partie jouée
        $dernierePartie = Score::where('user_id', $user->id)
            ->with('game')
            ->latest()
            ->first();

        // Dernières parties (avec jeu associé)
        $dernieresParties = Score::where('user_id', $user->id)
            ->with('game')
            ->latest()
            ->take(5)
            ->get();

        // Nombre d'amis
        $nbAmis = $user->friends()->count();

        return view('profil.index', compact(
            'user',
            'scoreTotal',
            'rangLabel',
            'nbParties',
            'partiesSemaine',
            'nbJeux',
            'dernierePartie',
            'dernieresParties',
            'nbAmis',
        ));
    }

    public function edit()
    {
        return view('profil.edit', ['user' => Auth::user()]);
    }

    public function settings()
    {
        return view('profil.settings', ['user' => Auth::user()]);
    }

    public function historique(User $user)
    {
        $parties = Score::where('user_id', $user->id)
            ->with('game')
            ->latest()
            ->paginate(20);

        return view('profil.historique', compact('user', 'parties'));
    }

    public function destroy()
    {
        $user = Auth::user();
        Auth::logout();
        $user->delete();
        return redirect('/')->with('notif', 'Compte supprimé.');
    }
}
