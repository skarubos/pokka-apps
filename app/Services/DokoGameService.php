<?php

namespace App\Services;

use App\Models\User;
use App\Models\Location;
use App\Models\Game;
use App\Models\GameLog;
use Illuminate\Support\Facades\Auth;

class DokoGameService
{
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
            $locationId = Location::pluck('id')->random($gameMode->stage)->toArray();

            $game = Game::create([
                'user_id'  => Auth::id(),
                'game_mode_id' => $gameMode->id,
                'progress' => 1,
            ]);

            foreach ($locationId as $index => $locId) {
                GameLog::create([
                    'game_id'     => $game->id,
                    'stage'       => $index + 1,
                    'location_id' => $locId,
                ]);
            }
            
            return $game;
        }
    }

    /**
     * 指定されたゲーム・ステージのロケーションを返す
     */
    public function getLocation(int $gameId, int $stage)
    {
        // 対応する game_logs を取得
        $gameLog = GameLog::where('game_id', $gameId)
            ->where('stage', $stage)
            ->first();

        return Location::select('id','name','country','city','lat','lng')
            ->find($gameLog->location_id);
    }

    /**
     * 2地点の距離とスコアを計算して記録し、合計スコアを返す
     */
    public function setResult($gameMode, int $gameId, int $stage, float $latA, float $lngA)
    {
        // 設問の地点を取得
        $locationQ = $this->getLocation($gameId, $stage);

        // Haversine式による距離計算
        $earthRadius = 6371; // 地球の半径 (km)
        $dLat = deg2rad($locationQ->lat - $latA);
        $dLng = deg2rad($locationQ->lng - $lngA);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($latA)) * cos(deg2rad($locationQ->lat)) *
            sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c; // 距離 (km)

        // スコア計算【得点 = (5000-D)/5）】
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
     * @return int|null  自己ベストのゲームID、存在しなければ null
     */
    public function setMyBest(int $gameId): ?int
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

        // 自己ベストだったゲームのIDを返す
        return $bestGame->id;
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
