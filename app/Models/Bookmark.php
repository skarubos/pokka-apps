<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'link_url',
        'priority',
        'img_name',
    ];
}
