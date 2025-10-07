<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // game_modes テーブル
        Schema::create('game_modes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 32);
            $table->unsignedInteger('stage')->default(5);
            $table->unsignedInteger('limit')->default(null)->nullable();
            $table->decimal('offset', 4, 3)->default(0.02);
            $table->integer('score_max')->default(1000);
            $table->boolean('score_demerit')->default(true);
            $table->integer('score_reference')->default(5000);
            $table->string('map')->nullable();
        });

        // games テーブル
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')   // users.id を参照
                ->restrictOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('game_mode_id')
                ->constrained('game_modes')  // game_modes.id を参照
                ->restrictOnUpdate()
                ->restrictOnDelete();
            $table->integer('progress')->default(1);
            $table->integer('result')->nullable();
            $table->integer('ranking')->nullable();
            $table->timestamps();
        });

        // game_logs テーブル
        Schema::create('game_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')
                ->constrained('games')   // games.id を参照
                ->restrictOnUpdate()     // onUpdate('no action')
                ->cascadeOnDelete();     // onDelete('cascade')
            $table->unsignedInteger('stage');
            $table->unsignedInteger('location_id')->nullable();
            $table->text('name')->nullable();
            $table->text('country')->nullable();
            $table->text('region')->nullable();
            $table->text('sub_region')->nullable();
            $table->decimal('q_lat', 9, 7)->nullable();
            $table->decimal('q_lng', 10, 7)->nullable();
            $table->decimal('a_lat', 9, 7)->nullable();
            $table->decimal('a_lng', 10, 7)->nullable();
            $table->decimal('distance', 8, 3)->nullable();
            $table->integer('score')->nullable();
            $table->timestamps();

            // 1ゲーム内でステージはユニーク
            $table->unique(['game_id', 'stage']);
        });

        // users テーブルにカラム追加
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('mybest_a')
                ->nullable()
                ->after('password')
                ->constrained('games')   // games.id を参照
                ->unique()               // 1:1 関係を保証
                ->nullOnDelete();        // Non-Identifying: 親削除時は NULL にする
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['try_count', 'mybest_a']);
        });
        Schema::dropIfExists('game_logs');
        Schema::dropIfExists('games');
    }
};
