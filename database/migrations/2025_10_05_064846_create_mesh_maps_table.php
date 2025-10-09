<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mesh_1km_2020_japan_maps', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('pref_id')->nullable();
            $table->string('shicode');
            $table->unsignedInteger('mesh_id');
            $table->float('ptn_2020');
            $table->decimal('min_lat', 6, 4);
            $table->decimal('min_lng', 7, 4);
            $table->decimal('max_lat', 6, 4);
            $table->decimal('max_lng', 7, 4);
        });

        Schema::create('mesh_1km_2020_world_above10000_maps', function (Blueprint $table) {
            $table->id();
            $table->unsignedsmallInteger('country_id');
            $table->decimal('lat', 6, 4);
            $table->decimal('lng', 7, 4);
            $table->unsignedMediumInteger('population');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mesh_1km_2020_japan_maps');
        Schema::dropIfExists('mesh_1km_2020_world_above10000_maps');
    }
};
