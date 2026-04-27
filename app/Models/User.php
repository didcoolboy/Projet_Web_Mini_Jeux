<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nom', 'prenom', 'pseudo', 'email', 'password', 'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // ─── Admin ────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function gameRequests(): HasMany
    {
        return $this->hasMany(GameRequest::class);
    }

    // ─── Scores ───────────────────────────────────────────────

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }

    public function lastPlayed(): HasMany
    {
        return $this->hasMany(LastPlayed::class);
    }

    public function totalScore(): int
    {
        return (int) $this->scores()->sum('score');
    }

    public function gamesPlayedCount(): int
    {
        return $this->scores()->distinct('game_id')->count('game_id');
    }

    public function scoreForGame(int $gameId): int
    {
        return (int) $this->scores()->where('game_id', $gameId)->sum('score');
    }

    // ─── Amis ─────────────────────────────────────────────────

    public function sentFriendships(): HasMany
    {
        return $this->hasMany(Friendship::class, 'sender_id');
    }

    public function receivedFriendships(): HasMany
    {
        return $this->hasMany(Friendship::class, 'receiver_id');
    }

    public function friends(): Collection
    {
        $sent = $this->sentFriendships()
            ->whereNotNull('accepted_at')
            ->with('receiver')
            ->get()
            ->pluck('receiver');

        $received = $this->receivedFriendships()
            ->whereNotNull('accepted_at')
            ->with('sender')
            ->get()
            ->pluck('sender');

        return $sent->merge($received)->unique('id');
    }

    public function isFriendWith(int $userId): bool
    {
        return Friendship::accepted()
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $this->id)->where('receiver_id', $userId);
            })
            ->orWhere(function ($q) use ($userId) {
                $q->where('sender_id', $userId)->where('receiver_id', $this->id);
            })
            ->exists();
    }

    // ─── Password Reset ────────────────────────────────────────

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token, $this->email));
    }
}