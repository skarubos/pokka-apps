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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');       // 地点名（ランドマークや市街地名）
            $table->string('country');    // 国名
            $table->string('city');       // 都市名
            $table->decimal('lat', 10, 6); // 緯度（±90度まで、小数点以下6桁）
            $table->decimal('lng', 10, 6); // 経度（±180度まで、小数点以下6桁）
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
