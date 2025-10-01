<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'game_mode_id',
        'progress',
        'result',
        'ranking',
    ];
    
    // ゲームは1人のユーザーに属する
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ゲームは1つのゲームモードに属する
    public function gameMode()
    {
        return $this->belongsTo(GameMode::class);
    }

    // ゲームは複数のログを持つ
    public function logs()
    {
        return $this->hasMany(GameLog::class);
    }

    // 逆に「このゲームを mybest にしているユーザー」を取得（1:1）
    public function bestOfUser()
    {
        return $this->hasOne(User::class, 'mybest_a');
    }
}
