<?php

namespace Database\Seeders;

use App\Models\MyApp;
use App\Models\User;
use App\Models\Game;
use App\Models\GameLog;
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
        $this->call(LocationsTableSeeder::class);


        // ----- MyApp関連 -----
        // MuAppsに10件作成（sort_orderは仮値0）
        MyApp::factory()->count(10)->create();

        // 作成後に id と sort_order を一致させる
        MyApp::all()->each(function ($app) {
            $app->update(['sort_order' => $app->id]);
        });


        // ----- DokoGame関連 -----
        // ユーザーを5件作成
        $users = User::factory(5)->create();

        // 1人目を開発用に更新
        $me = $users->first();
        $me->update([
            'name' => 'test',
            'email' => 'test@gmail.com',
            'password' => bcrypt('11111111'),
        ]);

        // 各ユーザーに1〜3件のゲームを作成
        $users->each(function ($user) {
            $games = Game::factory(rand(1, 3))
                ->for($user) // user_id を紐付け
                ->create();

            // 各ゲームに stage=1〜5 のログを作成
            $games->each(function ($game) {
                for ($stage = 1; $stage <= 5; $stage++) {
                    GameLog::factory()->create([
                        'game_id' => $game->id,
                        'stage' => $stage,
                    ]);
                }
            });
        });
    }
}
