<?php

use Illuminate\Support\Facades\App;
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
        if (App::environment('production')) {
            // 本番では何もしない
            return;
        }
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->text('name')->nullable();
            $table->text('link_url')->nullable();
            $table->text('img_name')->nullable();
            $table->integer('priority')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookmarks');
    }
};
