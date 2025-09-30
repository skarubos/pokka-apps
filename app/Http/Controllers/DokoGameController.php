<?php

namespace App\Http\Controllers;

use App\Services\DokoGameService;
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
    
    public function start()
    {
        // ステージ数
        $totalStages = 5;

        // 既存の進行中ゲームがあれば削除
        Game::where('user_id', Auth::id())
            ->where('progress', '!=', -1)
            ->delete();

        // 新しいゲームを準備
        $myGame = $this->dokoGameService->setGame($totalStages);

        // ロケーションを取得
        $location = $this->dokoGameService->getLocation($myGame->id, $myGame->progress);

        return view('doko_answer', [
            'myGame' => $myGame,
            'location' => $location,
            'delta' => 0.02,
        ]);

    }

    public function next()
    {
        // ステージ数
        $totalStages = 5;

        // 現在進行中のゲームを取得
        $myGame = Game::where('user_id', Auth::id())
            ->where('progress', '!=', -1)
            ->first();
        if (!$myGame) {
            // 進行中のゲームがない場合、直近の終了ゲームを取得
            $myGame = Game::where('user_id', Auth::id())
                ->where('progress', -1)
                ->orderByDesc('updated_at')
                ->first();
        }
        if (!$myGame) {
            // ゲームが存在しない場合
            return redirect()->route('doko.home');
        }

        if ($myGame->progress == -1) {
            // progress が -1 なら結果ページへ
            // ゲームに属するログを取得
            $logs = GameLog::where('game_id', $myGame->id)->get();

            return view('doko_result', [
                'myGame' => $myGame,
                'logs'   => $logs,
            ]);
        } elseif ($myGame->progress >= 1 && $myGame->progress <= $totalStages) {
            // 次の問題へ進行
            // ロケーションを取得
            $location = $this->dokoGameService->getLocation($myGame->id, $myGame->progress);

            return view('doko_answer', [
                'myGame' => $myGame,
                'location' => $location,
                'delta' => 0.02,
            ]);
        }
    }

    public function check(Request $request)
    {
        $gameId = $request->input('game_id');
        $stage = $request->input('stage');
        $latA = $request->input('lat');
        $lngA = $request->input('lng');

        // 緯度・経度が未指定の場合、再度回答ページへ
        if (!$latA || !$lngA) {
            return redirect()->route('doko.next');
        }

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
