<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Score extends Model
{
    protected $fillable = ['user_id', 'game_id', 'score'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function showInvite()
    {
        $topScores = Score::with('user')
            ->orderByDesc('score')
            ->take(10)
            ->get();
            return view('auth.invite', compact('topScores'));
    }

}

