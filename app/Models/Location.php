<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'name',
        'country',
        'city',
        'lat',
        'lng',
    ];

    // ロケーションは複数のゲームログを持つ
    public function gameLogs()
    {
        return $this->hasMany(GameLog::class);
    }
}
