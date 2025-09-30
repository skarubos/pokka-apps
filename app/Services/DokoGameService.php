<?php

namespace App\Services;

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
    public function setGame(int $stages)
    {
        // 既存のゲームを確認（progress != -1）
        $existingGame = Game::where('user_id', Auth::id())
            ->where('progress', '!=', -1)
            ->first();

        if ($existingGame) {
            // 既存ゲームがある場合 → progress に対応する game_logs を返却
            return $existingGame;
        } else {
            // 既存ゲームがない場合 → 新規作成
            $locationId = Location::pluck('id')->random($stages)->toArray();

            $game = Game::create([
                'user_id'  => Auth::id(),
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
     * 2地点の距離とスコアを計算して記録し、その結果を返す
     */
    public function setResult(int $gameId, int $stage, float $latA, float $lngA)
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

        // スコア計算【得点 = (5000-D)/10）】
        $score = max(0, (5000 - $distance)) / 10;

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

        return [
            'distance' => $distance,
            'score'    => $score,
            'total'   => $totalScore,
        ];
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
