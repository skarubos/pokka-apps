<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyApp extends Model
{
    /** @use HasFactory<\Database\Factories\MyAppsFactory> */
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'url',
        'explanation',
        'type',
    ];
}
