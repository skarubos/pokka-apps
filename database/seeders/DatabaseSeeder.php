<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\GameLog;
use App\Models\GameMode;
use App\Models\Keyword;
use App\Models\MyApp;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (!Keyword::exists()) {
            $this->call(KeywordsTableSeeder::class);
        }


        // ----- MyApp関連 -----
        if (!MyApp::exists()) {
            // MuAppsに10件作成（sort_orderは仮値0）
            MyApp::factory()->count(10)->create();

            // 作成後に id と sort_order を一致させる
            MyApp::all()->each(function ($app) {
                $app->update(['sort_order' => $app->id]);
            });
        }


        // ----- DokoGame関連 -----
        // $this->call(DummySeeder::class);
        if (!DB::table('locations')->exists()) {
            $this->call(LocationsTableSeeder::class);
        }
        if (!DB::table('mesh_1km_2020_japan_maps')->exists()) {
            $this->call(MeshMapJapanSeeder::class);
        }
        if (!DB::table('mesh_1km_2020_world_above10000_maps')->exists()) {
            $this->call(MeshMapWorld10000Seeder::class);
        }
        if (!DB::table('mesh_1km_2020_world_5000to10000_maps')->exists()) {
            $this->call(MeshMapWorld5000Seeder::class);
        }
        if (!DB::table('countries')->exists()) {
            $this->call(CountryTableSeeder::class);
        }
        if (!DB::table('prefectures')->exists()) {
            $this->call(PrefectureTableSeeder::class);
        }
        if (!DB::table('shi_codes')->exists()) {
            $this->call(ShiCodeTableSeeder::class);
        }

        // game_modes テーブルに追加
        if (!GameMode::exists()) {
            GameMode::insert([
                [
                    'name' => '世界（選定場所）',
                    'stage' => 3,
                    'limit' => null,
                    'offset' => 0.02,
                    'score_max' => 1000,
                    'score_demerit' => false,
                    'score_reference' => 5000,
                    'map' => 'world_selected',
                ],
                [
                    'name' => '日本（完全ランダム）',
                    'stage' => 3,
                    'limit' => null,
                    'offset' => 0.02,
                    'score_max' => 1000,
                    'score_demerit' => true,
                    'score_reference' => 1000,
                    'map' => 'japan_only',
                ],
                [
                    'name' => '日本（人口ありの場所：ランダム）',
                    'stage' => 3,
                    'limit' => null,
                    'offset' => 0.02,
                    'score_max' => 1000,
                    'score_demerit' => true,
                    'score_reference' => 1000,
                    'map' => 'japan_weighted',
                ],
                [
                    'name' => '世界（人口密集地：ランダム）',
                    'stage' => 3,
                    'limit' => null,
                    'offset' => 0.02,
                    'score_max' => 1000,
                    'score_demerit' => true,
                    'score_reference' => 5000,
                    'map' => 'world_above10000',
                ],
            ]);
        }

        if (!User::exists()) {
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
}
