<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use App\Models\Score;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AmiController extends Controller
{
    /**
     * Affiche la page amis : liste, demandes reçues, classement.
     */
    public function index()
    {
        $user = Auth::user();

        // Amis acceptés (relation bidirectionnelle via User::friends())
        $amis = $user->friends()->sortByDesc(function ($ami) {
            return $ami->totalScore();
        })->values();

        // Demandes reçues en attente (accepted_at IS NULL)
        $demandes = Friendship::where('receiver_id', $user->id)
            ->whereNull('accepted_at')
            ->with('sender')
            ->latest()
            ->get();

        // Classement : amis triés par score total (sans l'utilisateur connecté)
        $classement = $amis->sortByDesc(function ($joueur) {
            return $joueur->totalScore();
        })->values();

        return view('amis.index', compact('amis', 'demandes', 'classement'));
    }

    /**
     * Envoie une demande d'ami.
     */
    public function envoyer(Request $request)
    {
        $request->validate([
            'pseudo' => 'required|string|max:50',
        ]);

        $userId = Auth::id();
        $cible  = User::where('pseudo', $request->pseudo)->first();

        if (!$cible) {
            return back()->with('req_error', "Pseudo \"{$request->pseudo}\" introuvable.");
        }

        if ($cible->id === $userId) {
            return back()->with('req_error', "Tu ne peux pas t'ajouter toi-même.");
        }

        // Relation existante ?
        $existe = Friendship::where(function ($q) use ($userId, $cible) {
            $q->where('sender_id', $userId)->where('receiver_id', $cible->id);
        })->orWhere(function ($q) use ($userId, $cible) {
            $q->where('sender_id', $cible->id)->where('receiver_id', $userId);
        })->first();

        if ($existe) {
            if ($existe->accepted_at !== null) {
                $msg = "{$cible->pseudo} est déjà ton ami.";
            } elseif ($existe->sender_id === $userId) {
                $msg = "Tu as déjà envoyé une demande à {$cible->pseudo}.";
            } else {
                $msg = "{$cible->pseudo} t'a déjà envoyé une demande.";
            }
            return back()->with('req_error', $msg);
        }

        Friendship::create([
            'sender_id'   => $userId,
            'receiver_id' => $cible->id,
            'accepted_at' => null,
        ]);

        return back()->with('req_success', "Demande envoyée à {$cible->pseudo} !");
    }

    /**
     * Accepte une demande d'ami.
     */
    public function accepter(Friendship $friendship)
    {
        $this->authorize('repondre', $friendship);

        $friendship->update(['accepted_at' => now()]);

        return back()->with('notif', "Ami ajouté : {$friendship->sender->pseudo} !");
    }

    /**
     * Refuse (et supprime) une demande d'ami.
     */
    public function refuser(Friendship $friendship)
    {
        $this->authorize('repondre', $friendship);

        $pseudo = $friendship->sender->pseudo;
        $friendship->delete();

        return back()->with('notif', "Demande de {$pseudo} refusée.");
    }

    /**
     * Retire un ami.
     */
    public function retirer(User $user)
    {
        $userId = Auth::id();

        Friendship::where(function ($q) use ($userId, $user) {
            $q->where('sender_id', $userId)->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($userId, $user) {
            $q->where('sender_id', $user->id)->where('receiver_id', $userId);
        })->delete();

        return back()->with('notif', "{$user->pseudo} retiré de tes amis.");
    }
}
