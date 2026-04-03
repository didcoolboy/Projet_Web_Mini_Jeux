<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class Game extends Model
{
    protected $fillable = ['slug', 'name', 'icon', 'description', 'code'];

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getCodeAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }
}
