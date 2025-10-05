<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prefecture extends Model
{
    protected $table = 'prefectures';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'size',
    ];
}
