<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameLog extends Model
{
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    protected $fillable = [
        'game_id',
        'stage',
        'location_id',
        'q_lat',
        'q_lng',
        'a_lat',
        'a_lmg',
        'distance',
        'score',
    ];
}
