<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id',
        'word',
        'history',
    ];

    public $incrementing = true;
}
