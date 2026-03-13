<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Friendship extends Model
{
    protected $fillable = ['sender_id', 'receiver_id', 'accepted_at'];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    // ─── Relations ────────────────────────────────────────────

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // ─── Scopes ───────────────────────────────────────────────

    /** Uniquement les amitiés acceptées */
    public function scopeAccepted(Builder $query): Builder
    {
        return $query->whereNotNull('accepted_at');
    }

    /** Amitiés impliquant un utilisateur donné */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('sender_id', $userId)
                     ->orWhere('receiver_id', $userId);
    }
}
