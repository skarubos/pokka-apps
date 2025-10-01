<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameLog extends Model
{
    use HasFactory;

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
    
    // ログは1つのゲームに属する
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    // ログは1つのロケーションに属する
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
