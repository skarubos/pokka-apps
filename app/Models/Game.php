<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    public function logs()
    {
        return $this->hasMany(GameLog::class);
    }

    protected $fillable = [
        'user_id',
        'progress',
        'result',
        'ranking',
    ];
}
