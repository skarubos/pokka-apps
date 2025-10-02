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
        $gameModes = GameMode::all();

        return view('doko_home', [
            'title' => 'Doko Game',
            'gameModes' => $gameModes
        ]);
    }
    
    public function show_mypage()
    {
        $user = Auth::user();
        $bestGame = Game::where('user_id', $user->id)
            ->where('progress', -1)
            ->where('game_mode_id', 1)
            ->orderByDesc('result')
            ->first();

        $gameModes = GameMode::all();

        return view('doko_mypage', [
            'title' => 'Doko Game',
            'user' => $user,
            'bestGame' => $bestGame,
            'gameModes' => $gameModes,
        ]);

    }

    public function start(int $mode)
    {
        // geme_mode: 1=デフォルト 2=日本
        $gameMode = GameMode::where('id', $mode)->first();

        // 既存の進行中ゲームがあれば削除
        Game::where('user_id', Auth::id())
            ->where('progress', '!=', -1)
            ->delete();

        // 新しいゲームを準備
        $myGame = $this->dokoGameService->setGame($gameMode);

        // 出題地点を取得
        $location = $this->dokoGameService->getLocation($myGame);

        return view('doko_answer', [
            'myGame' => $myGame,
            'location' => $location,
            'delta' => $gameMode->offset,
        ]);
    }

    public function next(Request $request)
    {
        $gameId = $request->input('game_id');
        $gameModeId = $request->input('game_mode_id');
        $gameMode = GameMode::where('id', $gameModeId)->first();

        $myGame = Game::where('id', $gameId)->first();
        // 途中から再開の時などのゲーム取得処理
        if (!$myGame) {
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
            $myBestGame = $this->dokoGameService->setMyBest($myGame->id);
            $newRecord = ($myBestGame->id == $myGame->id);

            $maxScore = $gameMode->score_max * $gameMode->stage;

            return view('doko_result', [
                'user'   => Auth::user(),
                'myGame' => $myGame,
                'logs'   => $logs,
                'newRecord' => $newRecord,
                'myBestScore' => $myBestGame->result,
                'maxScore'  => $maxScore,
            ]);
        } elseif ($myGame->progress >= 1 && $myGame->progress <= $gameMode->stage) {
            // 次の問題へ進行
            // ロケーションを取得
            $location = $this->dokoGameService->getLocation($myGame);

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

        // 対象のゲームを取得
        $myGame = Game::where('id', $gameId)->where('user_id', Auth::id())->first();

        if ($stage == $myGame->progress) {
            // 結果を記録
            $this->dokoGameService->setResult($gameId, $stage, $latA, $lngA);

            // ゲームの進行度を更新
            $gameMode = GameMode::where('id', $myGame->game_mode_id)->first();
            if ($myGame->progress == $gameMode->stage) {
                $myGame->progress = -1; // ゲーム終了
            } elseif ($myGame->progress >= 1 && $myGame->progress < $gameMode->stage) {
                $myGame->progress += 1;
            }
            $myGame->save();
        }

        $gameLog = GameLog::where('game_id', $gameId)->where('stage', $stage)->first();

        return view('doko_check', [
            'myGame'     => $myGame,
            'gameLog'   => $gameLog,
            'latA'     => $latA,
            'lngA'     => $lngA,
        ]);
    }
}
