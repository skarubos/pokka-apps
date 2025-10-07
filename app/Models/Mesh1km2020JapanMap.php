<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mesh1km2020JapanMap extends Model
{
    protected $table = 'mesh_1km_2020_japan_maps';

    public $timestamps = false;

    protected $fillable = [
        'pref_id',
        'shicode',
        'mesh_id',
        'ptn_2020',
        'a_lat',
        'a_lng',
        'b_lat',
        'b_lng',
    ];
}
