<?php

namespace App\Http\Controllers;

use App\Models\MyApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MyAppsController extends Controller
{
    public function test()
    {
        return view('test', [
            'title' => 'TEST',
        ]);
    }

    public function index()
    {
        $data = MyApp::orderBy('sort_order', 'asc')
            ->get();

        return view('my_apps', [
            'title' => 'APP LIST',
            'data' => $data,
        ]);
    }

    public function edit()
    {
        $data = MyApp::orderBy('sort_order', 'asc')->get();

        // 空のモデルを作成し、全カラムを自動で取得、idだけ上書き
        $dummy = new MyApp();
        $attributes = array_fill_keys(array_keys($dummy->getAttributes()), '');
        $attributes['id'] = 100;

        // モデルインスタンス化
        $data->push(new MyApp($attributes));

        return view('my_apps_edit', [
            'title' => 'APP LIST - Edit',
            'data' => $data,
        ]);
    }

    public function sort_show()
    {
        $data = MyApp::orderBy('sort_order', 'asc')
            ->select('id', 'sort_order', 'name', 'url')
            ->get();

        return view('my_apps_sort', [
            'title' => 'APP LIST - Sort',
            'data' => $data,
        ]);
    }

    public function sort(Request $request)
    {
        $order = json_decode($request->order, true);

        foreach ($order as $item) {
            MyApp::where('id', $item['id'])
                ->update(['sort_order' => $item['sort_order']]);
        }

        return redirect()
            ->route('myapps')
            ->with('success', "並び替えを保存しました。");
    }

    public function update(Request $request, $id)
    {
        try {
            // バリデーション
            $validated = $request->validate([
                'name'        => 'required|string|max:255',
                'url'         => 'required|url|max:255',
                'explanation' => 'nullable|string',
            ]);

            if ((int)$id === 100) {
                // ===== 新規登録処理 =====

                // 現在のID一覧を昇順で取得
                $existingIds = MyApp::orderBy('id', 'asc')->pluck('id')->toArray();

                // 最小の空きIDを探す（1から順にチェック）
                $newId = 1;
                while (in_array($newId, $existingIds, true)) {
                    $newId++;
                }

                // 現在の sort_order 一覧を昇順で取得
                $existingSortOrders = MyApp::orderBy('sort_order', 'asc')->pluck('sort_order')->toArray();

                // 最小の空き sort_order を探す（1から順にチェック）
                $newSortOrder = 1;
                while (in_array($newSortOrder, $existingSortOrders, true)) {
                    $newSortOrder++;
                }

                // 新規作成
                $app = new MyApp();
                $app->id          = $newId;
                $app->name        = $validated['name'];
                $app->url         = $validated['url'];
                $app->explanation = $validated['explanation'] ?? '';
                $app->sort_order  = $newSortOrder; // 未使用の最小整数値
                $app->type        = 1;             // デフォルト値
                $app->save();

                return redirect()
                    ->route('myapps')
                    ->with('success', "（{$app->id}：{$app->name}）を新規登録しました。");

            } else {
                // ===== 既存レコード更新処理 =====
                $app = MyApp::findOrFail($id);
                $app->name        = $validated['name'];
                $app->url         = $validated['url'];
                $app->explanation = $validated['explanation'] ?? '';
                $app->save();

                return redirect()
                    ->route('myapps')
                    ->with('success', "（{$app->id}：{$app->name}）の変更に成功しました。");
            }

        } catch (\Throwable $e) {
            // ログ出力（開発・調査用）
            Log::error('MyApp更新/登録エラー', [
                'id'    => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('myapps')
                ->with('error', '処理中にエラーが発生しました。');
        }
    }


    public function destroy($id)
    {
        try {
            // 該当レコードを取得して削除
            $app = MyApp::findOrFail($id);
            $appName = $app->name;
            $appId   = $app->id;
            $deletedSortOrder = $app->sort_order;
            $app->delete();

            // sort_order を繰り上げ（欠番を詰める）
            MyApp::where('sort_order', '>', $deletedSortOrder)
                ->orderBy('sort_order', 'asc')
                ->get()
                ->each(function ($item) {
                    $item->sort_order = $item->sort_order - 1;
                    $item->save();
                });

            return redirect()
                ->route('myapps')
                ->with('success', "（{$appId}：{$appName}）を削除しました。");

        } catch (\Throwable $e) {
            // ログ出力（調査用）
            Log::error('MyApp削除エラー', [
                'id'    => $id,
                'error' => $e->getMessage(),
            ]);

            // エラーメッセージをフラッシュ
            return redirect()
                ->route('myapps')
                ->with('error', '削除処理中にエラーが発生しました。');
        }
    }
}
