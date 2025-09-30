<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // users テーブルにカラム追加
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('try_count')->default(0)->after('password');
            $table->unsignedInteger('mybest')->nullable()->after('try_count');
        });

        // games テーブル
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('progress')->default(1);
            $table->integer('result')->nullable();
            $table->integer('ranking')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onUpdate('no action')
                ->onDelete('no action');
        });

        // game_logs テーブル
        Schema::create('game_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_id');
            $table->unsignedInteger('stage');
            $table->unsignedInteger('location_id')->nullable();
            $table->decimal('q_lat', 10, 8)->nullable();
            $table->decimal('q_lng', 10, 8)->nullable();
            $table->decimal('a_lat', 10, 8)->nullable();
            $table->decimal('a_lng', 10, 8)->nullable();
            $table->decimal('distance', 8, 3)->nullable();
            $table->integer('score')->nullable();
            $table->timestamps();

            // 1ゲーム内でステージはユニーク
            $table->unique(['game_id', 'stage']);

            // 外部キー：ゲーム削除時にログも削除
            $table->foreign('game_id')
                ->references('id')->on('games')
                ->onUpdate('no action')
                ->onDelete('cascade');
        });

    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['try_count', 'mybest']);
        });
        Schema::dropIfExists('game_logs');
        Schema::dropIfExists('games');
    }
};
