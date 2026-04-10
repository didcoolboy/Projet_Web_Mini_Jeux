<?php

namespace App\Observers;

use App\Models\Score;

class ScoreObserver
{
    /**
     * Handle the Score "created" event.
     *
     * @param  \App\Models\Score  $score
     * @return void
     */
    public function created(Score $score)
    {
        // Mettre à jour la dernière partie jouée pour cet utilisateur et ce jeu
        \App\Models\LastPlayed::updateOrCreate(
            [
                'user_id' => $score->user_id,
                'game_id' => $score->game_id,
            ],
            [
                'last_played_at' => $score->created_at,
            ]
        );
    }

    /**
     * Handle the Score "updated" event.
     *
     * @param  \App\Models\Score  $score
     * @return void
     */
    public function updated(Score $score)
    {
        //
    }

    /**
     * Handle the Score "deleted" event.
     *
     * @param  \App\Models\Score  $score
     * @return void
     */
    public function deleted(Score $score)
    {
        //
    }

    /**
     * Handle the Score "restored" event.
     *
     * @param  \App\Models\Score  $score
     * @return void
     */
    public function restored(Score $score)
    {
        //
    }

    /**
     * Handle the Score "force deleted" event.
     *
     * @param  \App\Models\Score  $score
     * @return void
     */
    public function forceDeleted(Score $score)
    {
        //
    }
}
