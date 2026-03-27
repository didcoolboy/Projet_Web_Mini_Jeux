<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
    'nom', 'prenom', 'pseudo', 'email', 'password', 'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }

    /** Score total (somme de tous les jeux) */
    public function totalScore(): int
    {
        return (int) $this->scores()->sum('score');
    }

    /** Score pour un jeu précis (par game_id) */
    public function scoreForGame(int $gameId): int
    {
        return (int) $this->scores()->where('game_id', $gameId)->sum('score');
    }

    // ─── Amis ─────────────────────────────────────────────────

    /** Amitiés où l'utilisateur est l'expéditeur */
    public function sentFriendships(): HasMany
    {
        return $this->hasMany(Friendship::class, 'sender_id');
    }

    /** Amitiés où l'utilisateur est le destinataire */
    public function receivedFriendships(): HasMany
    {
        return $this->hasMany(Friendship::class, 'receiver_id');
    }

    /**
     * Retourne la collection des amis acceptés (les deux sens).
     */
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

    /** Vérifie si un user est ami avec l'utilisateur courant */
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
}

// Dans $fillable, ajouter 'role'
protected $fillable = ['name', 'email', 'password', 'role'];

// Helper pratique
public function isAdmin(): bool
{
    return $this->role === 'admin';
}

// Relation
public function gameRequests()
{
    return $this->hasMany(GameRequest::class);
}
