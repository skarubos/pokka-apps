<?php

namespace App\Services;

use geoPHP;
use App\Models\User;
use App\Models\Location;
use App\Models\Game;
use App\Models\GameLog;
use App\Models\GameMode;
use Illuminate\Support\Facades\Auth;

class DokoGameService
{
    protected DokoGameRandomLocationService $randomLocationService;

    // コンストラクタで依存を注入
    public function __construct(DokoGameRandomLocationService $randomLocationService)
    {
        $this->randomLocationService = $randomLocationService;
    }

    /**
     * 指定されたステージ数でゲームを新規作成して返す
     * 既存のゲームがあればそれを返却する
     */
    public function setGame($gameMode)
    {
        // 既存のゲームを確認（progress != -1）
        $existingGame = Game::where('user_id', Auth::id())
            ->where('game_mode_id', $gameMode->id)
            ->where('progress', '!=', -1)
            ->first();

        if ($existingGame) {
            // 既存ゲームがある場合 → progress に対応する game_logs を返却
            return $existingGame;
        } else {
            // 既存ゲームがない場合 → 新規作成
            $game = Game::create([
                'user_id'  => Auth::id(),
                'game_mode_id' => $gameMode->id,
                'progress' => 1,
            ]);

            if ($gameMode->id == 1) {
                // game_mode: 1=デフォルト
                $locationId = Location::pluck('id')->random($gameMode->stage)->toArray();
                foreach ($locationId as $index => $locId) {
                    $loc = Location::find($locId);
                    GameLog::create([
                        'game_id'     => $game->id,
                        'stage'       => $index + 1,
                        'location_id' => $locId,
                        'name'        => $loc->name,
                        'country'     => $loc->country,
                        'region'      => $loc->city,
                        'q_lat'       => $loc->lat,
                        'q_lng'       => $loc->lng,
                    ]);
                }
            } elseif ($gameMode->id == 2) {
                // game_mode: 2=日本
                $locations = $this->randomLocationService->getLocationsInJapan($gameMode->stage);
                foreach ($locations as $index => $loc) {
                    GameLog::create([
                        'game_id'     => $game->id,
                        'stage'       => $index + 1,
                        'country'     => "日本",
                        'region'      => $loc['region'],
                        'sub_region'  => $loc['sub_region'],
                        'q_lat'         => $loc['lat'],
                        'q_lng'         => $loc['lng'],
                    ]);
                }
            }

            return $game;
        }
    }

    /**
     * 指定されたゲームの進行中のロケーションを返す
     */
    public function getLocation($myGame)
    {
        if ($myGame->progress == -1) {
            return null;
        }

        // 対応する game_logs からロケーションを取得
        $location = GameLog::where('game_id', $myGame->id)
            ->where('stage', $myGame->progress)
            ->select('q_lat', 'q_lng')
            ->first();

        return $location;
    }

    /**
     * 2地点の距離とスコアを計算して記録し、合計スコアを返す
     *
     * @param int $gameId ゲームID
     * @param int $stage ステージ番号
     * @param float $latA 回答地点の緯度
     * @param float $lngA 回答地点の経度
     * @return int 合計スコア
     */
    public function setResult(int $gameId, int $stage, float $latA, float $lngA)
    {
        // 設問の地点を取得
        $myGame = Game::where('id', $gameId)->first();
        $locQ = $this->getLocation($myGame );

        // Haversine式による距離計算
        $earthRadius = 6371; // 地球の半径 (km)
        $dLat = deg2rad($locQ->q_lat - $latA);
        $dLng = deg2rad($locQ->q_lng - $lngA);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($latA)) * cos(deg2rad($locQ->q_lat)) *
            sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c; // 距離 (km)

        // スコア計算【2: (5000-D)/5）】【2: (1000-D)/1）】
        $gameMode = GameMode::where('id', $myGame->game_mode_id)->first();
        $ref = $gameMode->score_reference; // 参照基準距離 (km)
        $max = $gameMode->score_max; // 最大スコア
        $div = $ref / $max; // 1点あたりの距離 (km)
        if ($gameMode->score_demerit) {
            // 負の値も許容
            $score = ($ref - $distance) / $div;
        } else {
            // 0以下は全て0点
            $score = max(0, ($ref - $distance)) / $div;
        }

        // 小数点以下を丸める
        $distance = round($distance, 3);
        $score    = round($score);

        // 距離とスコアを game_logs テーブルに保存
        GameLog::where('game_id', $gameId)
            ->where('stage', $stage)
            ->update([
                'a_lat'    => $latA,
                'a_lng'    => $lngA,
                'distance' => $distance,
                'score'    => $score,
            ]);

        // 合計スコアを games テーブルに保存
        $totalScore = GameLog::where('game_id', $gameId)->sum('score');
        Game::where('id', $gameId)->update([
            'result' => $totalScore,
        ]);

        return $totalScore;
    }

    /**
     * ユーザーの自己ベストを更新し、そのゲームIDを返す
     *
     * @param  int  $gameId
     */
    public function setMyBest(int $gameId)
    {
        // 基準となるゲームを取得
        $game = Game::find($gameId);
        if (!$game) {
            return null;
        }

        $userId = $game->user_id;
        $gameModeId = $game->game_mode_id;

        // progress = -1 かつ game_mode_id が一致する中で result が最大のレコードを取得
        $bestGame = Game::where('user_id', $userId)
            ->where('progress', -1)
            ->where('game_mode_id', $gameModeId)
            ->orderByDesc('result')
            ->first();

        if (!$bestGame) {
            return null; // 対象レコードがない場合
        }

        // users.mybest_a を更新
        $user = User::find($userId);
        $user->mybest_a = $bestGame->id;
        $user->save();

        // 自己ベストだったゲームを返す
        return $bestGame;
    }


    /**
     * ゲームを削除する
     */
    public function deleteGame(int $gameId)
    {
        $game = Game::findOrFail($gameId);

        // ゲームを削除すると関連する game_logs も cascade で削除される
        $game->delete();

        return response()->json(['message' => 'Game aborted and records deleted.']);
    }
}
