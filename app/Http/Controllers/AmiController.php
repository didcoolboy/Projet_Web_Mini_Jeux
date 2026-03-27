<?php

namespace App\Http\Controllers;

use App\Models\Ami;
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
        $userId = Auth::id();

        // Amis acceptés (relation bidirectionnelle), triés par score décroissant
        $amis = User::whereHas('amisAcceptes', function ($q) use ($userId) {
                $q->where('id_receveur', $userId)->orWhere('id_demandeur', $userId);
            })
            ->where('id', '!=', $userId)
            ->orderByDesc('score_total')
            ->get();

        // Demandes reçues en attente
        $demandes = Ami::where('id_receveur', $userId)
            ->where('statut', 'en_attente')
            ->with('demandeur')
            ->orderByDesc('created_at')
            ->get();

        // Classement : amis + moi, triés par score
        $classement = $amis->push(Auth::user())->sortByDesc('score_total')->values();

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

        $userId   = Auth::id();
        $cible    = User::where('pseudo', $request->pseudo)->first();

        if (!$cible) {
            return back()->with('req_error', "Pseudo \"{$request->pseudo}\" introuvable.");
        }

        if ($cible->id === $userId) {
            return back()->with('req_error', "Tu ne peux pas t'ajouter toi-même.");
        }

        // Déjà amis ou demande existante ?
        $existe = Ami::where(function ($q) use ($userId, $cible) {
            $q->where('id_demandeur', $userId)->where('id_receveur', $cible->id);
        })->orWhere(function ($q) use ($userId, $cible) {
            $q->where('id_demandeur', $cible->id)->where('id_receveur', $userId);
        })->first();

        if ($existe) {
            $msg = match ($existe->statut) {
                'accepte'    => "{$cible->pseudo} est déjà ton ami.",
                'en_attente' => "Une demande est déjà en cours avec {$cible->pseudo}.",
                default      => "Relation déjà existante avec {$cible->pseudo}.",
            };
            return back()->with('req_error', $msg);
        }

        Ami::create([
            'id_demandeur' => $userId,
            'id_receveur'  => $cible->id,
            'statut'       => 'en_attente',
        ]);

        return back()->with('req_success', "Demande envoyée à {$cible->pseudo} !");
    }

    /**
     * Accepte une demande d'ami.
     */
    public function accepter(Ami $ami)
    {
        $this->authorize('repondre', $ami);

        $ami->update(['statut' => 'accepte']);

        return back()->with('notif', "Ami ajouté : {$ami->demandeur->pseudo} !");
    }

    /**
     * Refuse (et supprime) une demande d'ami.
     */
    public function refuser(Ami $ami)
    {
        $this->authorize('repondre', $ami);

        $pseudo = $ami->demandeur->pseudo;
        $ami->delete();

        return back()->with('notif', "Demande de {$pseudo} refusée.");
    }

    /**
     * Retire un ami.
     */
    public function retirer(User $user)
    {
        $userId = Auth::id();

        Ami::where(function ($q) use ($userId, $user) {
            $q->where('id_demandeur', $userId)->where('id_receveur', $user->id);
        })->orWhere(function ($q) use ($userId, $user) {
            $q->where('id_demandeur', $user->id)->where('id_receveur', $userId);
        })->delete();

        return back()->with('notif', "{$user->pseudo} retiré de tes amis.");
    }
}
