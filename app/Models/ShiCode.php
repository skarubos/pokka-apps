<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiCode extends Model
{
    protected $table = 'shi_codes';

    public $timestamps = false;

    protected $fillable = [
        'code',
        'pref',
        'city',
        'pref_kana',
        'city_kana',
    ];
}
