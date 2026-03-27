<?php

namespace App\Policies;

use App\Models\Ami;
use App\Models\User;

class AmiPolicy
{
    /**
     * Seul le receveur peut accepter ou refuser une demande.
     */
    public function repondre(User $user, Ami $ami): bool
    {
        return $user->id === $ami->id_receveur;
    }
}
