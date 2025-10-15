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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->unsignedsmallInteger('country_id');
            // $table->string('country_code', 3); // 国名コード(JPNなど)
            $table->string('name', 30);
            $table->unsignedInteger('above10000');
            $table->unsignedInteger('5000to10000');
        });

        Schema::create('prefectures', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary(); // 都道府県ID（1〜47）
            $table->string('name', 50); // 都道府県名
            $table->integer('size');    // 要素数
        });

        Schema::create('shi_codes', function (Blueprint $table) {
            $table->id();
            $table->integer('code');
            $table->string('pref', 10); // 都道府県名
            $table->string('city', 20); // 市区町村名
            $table->string('pref_kana', 10); // 都道府県名カナ
            $table->string('city_kana', 30); // 市区町村名カナ
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
        Schema::dropIfExists('prefectures');
        Schema::dropIfExists('shi_codes');
    }
};
