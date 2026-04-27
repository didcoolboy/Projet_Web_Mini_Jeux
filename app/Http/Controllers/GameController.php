<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\LastPlayed;
use App\Models\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    /**
     * Enregistrer un score après une partie.
     * POST /save-score/{gameSlug}
     */
    public function saveScore(Request $request, string $gameSlug)
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
            $game = Game::firstOrCreate(
                ['slug' => $gameSlug],
                ['name' => ucfirst($gameSlug)]
            );

            $incomingScore = (int) $validated['score'];

            // On conserve uniquement le meilleur score utilisateur par jeu.
            $score = Score::query()
                ->where('user_id', Auth::id())
                ->where('game_id', $game->id)
                ->orderByDesc('score')
                ->orderByDesc('id')
                ->first();

            $scoreUpdated = false;

            if (!$score) {
                $score = Score::create([
                    'user_id' => Auth::id(),
                    'game_id' => $game->id,
                    'score' => $incomingScore,
                ]);
                $scoreUpdated = true;
            } elseif ($incomingScore > (int) $score->score) {
                $score->score = $incomingScore;
                $score->save();
                $scoreUpdated = true;
            }

            // Garder une trace du dernier jeu joué par utilisateur
            LastPlayed::updateOrCreate(
                ['user_id' => Auth::id(), 'game_id' => $game->id],
                ['last_played_at' => now()]
            );

            return response()->json([
                'success' => true,
                'message' => $scoreUpdated
                    ? 'Nouveau meilleur score enregistre'
                    : 'Score non enregistre car inferieur au meilleur score actuel',
                'score' => $score,
                'best_score' => (int) $score->score,
                'updated' => $scoreUpdated,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de l\'enregistrement',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
