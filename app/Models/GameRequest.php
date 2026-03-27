<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameRequest extends Model
{
    protected $fillable = [
        'user_id', 'game_name', 'category', 'description', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}