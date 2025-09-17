<?php

namespace Database\Seeders;

use App\Models\MyApp;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call(KeywordsTableSeeder::class);

        // MuAppsに10件作成（sort_orderは仮値0）
        MyApp::factory()->count(10)->create();

        // 作成後に id と sort_order を一致させる
        MyApp::all()->each(function ($app) {
            $app->update(['sort_order' => $app->id]);
        });
    }
}
