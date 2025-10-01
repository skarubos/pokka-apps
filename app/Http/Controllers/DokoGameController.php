<?php

namespace App\Http\Controllers;

use App\Services\DokoGameService;
use App\Models\Game;
use App\Models\GameLog;
use App\Models\GameMode;
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
    
    public function show_mypage()
    {
        $user = Auth::user();
        $bestGame = Game::where('user_id', $user->id)
            ->where('progress', -1)
            ->where('game_mode_id', 1)
            ->orderByDesc('result')
            ->first();

        return view('doko_mypage', [
            'title' => 'Doko Game',
            'user' => $user,
            'bestGame' => $bestGame,
        ]);

    }

    public function start()
    {
        // geme_mode: 1=デフォルト
        $gameMode = GameMode::where('id', 1)->first();

        // 既存の進行中ゲームがあれば削除
        Game::where('user_id', Auth::id())
            ->where('progress', '!=', -1)
            ->delete();

        // 新しいゲームを準備
        $myGame = $this->dokoGameService->setGame($gameMode);

        // ロケーションを取得
        $location = $this->dokoGameService->getLocation($myGame->id, $myGame->progress);

        return view('doko_answer', [
            'myGame' => $myGame,
            'location' => $location,
            'delta' => $gameMode->offset,
        ]);

    }

    public function next()
    {
        // geme_mode: 1=デフォルト
        $gameMode = GameMode::where('id', 1)->first();

        // 現在進行中のゲームを取得
        $myGame = Game::where('user_id', Auth::id())
            ->where('game_mode_id', $gameMode->id)
            ->where('progress', '!=', -1)
            ->first();
        if (!$myGame) {
            // 進行中のゲームがない場合、直近の終了ゲームを取得
            $myGame = Game::where('user_id', Auth::id())
                ->where('game_mode_id', $gameMode->id)
                ->where('progress', -1)
                ->orderByDesc('updated_at')
                ->first();
        }
        if (!$myGame) {
            // ゲームが存在しない場合
            return redirect()->route('doko.mypage');
        }

        if ($myGame->progress == -1) {
            // progress が -1 なら結果ページへ
            // ゲームに属するログを取得
            $logs = GameLog::with('location')
                ->where('game_id', $myGame->id)
                ->get();


            // 自己ベストかどうかをチェックして更新
            $myBestGameId = $this->dokoGameService->setMyBest($myGame->id);
            $newRecord = ($myBestGameId == $myGame->id);

            return view('doko_result', [
                'user'   => Auth::user(),
                'myGame' => $myGame,
                'logs'   => $logs,
                'newRecord' => $newRecord,
            ]);
        } elseif ($myGame->progress >= 1 && $myGame->progress <= $gameMode->stage) {
            // 次の問題へ進行
            // ロケーションを取得
            $location = $this->dokoGameService->getLocation($myGame->id, $myGame->progress);

            return view('doko_answer', [
                'myGame' => $myGame,
                'location' => $location,
                'delta' => $gameMode->offset,
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

        // geme_mode: 1=デフォルト
        $gameMode = GameMode::where('id', 1)->first();
        // 結果を記録
        $result = $this->dokoGameService->setResult($gameMode, $gameId, $stage, $latA, $lngA);

        $game_log = GameLog::with('location')
            ->where('game_id', $gameId)
            ->where('stage', $stage)
            ->first();

        // ゲームの進行度を更新
        $game = Game::find($gameId);
        if ($game_log->score !== null) {
            if ($game->progress == $gameMode->stage) {
                $game->progress = -1; // ゲーム終了
            } elseif ($game->progress >= 1 && $game->progress < $gameMode->stage) {
                $game->progress += 1;
            }
            $game->save();
        }

        return view('doko_check', [
            'gameLog'   => $game_log,
            'totalScore'    => $result,
            'latA'     => $latA,
            'lngA'     => $lngA,
        ]);
    }
}
