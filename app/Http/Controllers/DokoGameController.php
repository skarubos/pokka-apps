<?php

namespace App\Http\Controllers;

use App\Services\DokoGameService;
use App\Models\Location;
use App\Models\Game;
use App\Models\GameLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DokoGameController extends Controller
{
    protected $dokoGameService;

    public function __construct(DokoGameService $dokoGameService)
    {
        $this->dokoGameService = $dokoGameService;
    }

    public function index()
    {
        return view('doko_home');
    }
    
    public function test()
    {
        // ステージ数
        $totalStages = 5;

        // ゲームを準備
        $myGame = $this->dokoGameService->setGame($totalStages);

        // progress が -1 なら結果ページへ
        if ($myGame->progress == -1) {
            // ゲームに属するログを取得
            $logs = GameLog::where('game_id', $myGame->id)->get();

            return view('doko_result', [
                'myGame' => $myGame,
                'logs'   => $logs,
            ]);
        }

        // ロケーションを取得
        $location = $this->dokoGameService->getLocation($myGame->id, $myGame->progress);

        // dd($location);

        return view('doko_answer', [
            'myGame' => $myGame,
            'location' => $location,
            'delta' => 0.02,
        ]);
    }

    public function check(Request $request)
    {
        $gameId = $request->input('game_id');
        $stage = $request->input('stage');
        $latA = $request->input('lat');
        $lngA = $request->input('lng');

        $result = $this->dokoGameService->setResult($gameId, $stage, $latA, $lngA);

        $locationQ = $this->dokoGameService->getLocation($gameId, $stage);

        // ゲームの進行度を更新
        $game = Game::find($gameId);
        if ($game->progress < 5) {
            $game->progress += 1;
        } else {
            $game->progress = -1; // ゲーム終了
        }
        $game->save();

        return view('doko_check', [
            'gameId'   => $gameId,
            'stage'    => $stage,
            'latQ'     => $locationQ->lat,
            'lngQ'     => $locationQ->lng,
            'distance' => $result['distance'],
            'score'    => $result['score'],
            'totalScore'    => $result['total'],
            'latA'     => $latA,
            'lngA'     => $lngA,
        ]);
    }
}
