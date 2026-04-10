<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    /**
     * Enregistrer un score après une partie.
     * POST /api/games/{game}/save-score
     */
    public function saveScore(Request $request, Game $game)
    {
        // Vérifier que l'utilisateur est connecté
        if (!Auth::check()) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        // Valider les données
        $validated = $request->validate([
            'score' => 'required|integer|min:0',
        ]);

        try {
            // Créer le score
            $score = Score::create([
                'user_id' => Auth::id(),
                'game_id' => $game->id,
                'score' => $validated['score'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Score enregistré avec succès',
                'score' => $score,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de l\'enregistrement',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
