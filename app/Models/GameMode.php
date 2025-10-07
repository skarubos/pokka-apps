<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameMode extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'stage',
        'limit',
        'offset',
        'score_max',
        'score_demerit',
        'score_reference',
        'map',
    ];

    // ゲームモードは複数のゲームを持つ
    public function games()
    {
        return $this->hasMany(Game::class);
    }
}
