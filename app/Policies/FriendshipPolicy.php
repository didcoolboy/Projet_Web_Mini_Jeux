<?php

namespace App\Policies;

use App\Models\Friendship;
use App\Models\User;

class FriendshipPolicy
{
    /**
     * Seul le receveur peut accepter ou refuser une demande.
     */
    public function repondre(User $user, Friendship $friendship): bool
    {
        return $user->id === $friendship->receiver_id;
    }
}
