<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ami extends Model
{
    protected $table    = 'amis';
    protected $fillable = ['id_demandeur', 'id_receveur', 'statut'];

    // Relation vers l'utilisateur qui a envoyé la demande
    public function demandeur()
    {
        return $this->belongsTo(User::class, 'id_demandeur');
    }

    // Relation vers l'utilisateur qui a reçu la demande
    public function receveur()
    {
        return $this->belongsTo(User::class, 'id_receveur');
    }
}
