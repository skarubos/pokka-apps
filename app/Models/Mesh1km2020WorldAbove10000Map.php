<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mesh1km2020WorldAbove10000Map extends Model
{
    protected $table = 'mesh_1km_2020_world_above10000_maps';

    public $timestamps = false;

    protected $fillable = [
        'country_id',
        'lat',
        'lng',
    ];
}
