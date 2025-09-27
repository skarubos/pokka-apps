<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookmarkRequest;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StartPageController extends Controller
{
    public function index()
    {
        $bookmarks = Bookmark::orderBy('priority', 'asc')
            ->get();

        return view('start_page', [
            'bookmarks' => $bookmarks,
        ]);
    }

    public function sort(Request $request)
    {
        $order = json_decode($request->order, true);

        foreach ($order as $item) {
            Bookmark::where('id', $item['id'])
                ->update(['priority' => $item['priority']]);
        }

        return redirect()
            ->route('bookmark')
            ->with('success', "並び替えを保存しました。");
    }

    public function form(Request $request)
    {
        $mode = $request->query('mode');
        $id = $request->query('id');

        if (!in_array($mode, ['create', 'edit'], true)) {
            abort(404);
        }

        $bookmarksCount = Bookmark::count();

        $bookmark = null;
        if ($mode === 'edit') {
            if (!$id) abort(404);
            $bookmark = Bookmark::find($id);
            if (!$bookmark) abort(404);
        }

        return view('start_page_form', [
            'mode' => $mode,
            'bookmarksCount' => $bookmarksCount,
            'bookmark' => $bookmark,
        ]);
    }

    // 共通の画像保存処理
    private function saveImageFile(Request $request, string $fieldName = 'img_name'): ?string
    {
        if ($request->hasFile($fieldName)) {
            $file = $request->file($fieldName);

            // 重複しないファイル名を生成
            $uniqueFileName = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();

            // storage/app/public/images に保存
            $file->storeAs('images', $uniqueFileName, 'public');

            return $uniqueFileName;
        }

        return null;
    }

    public function create(BookmarkRequest $request)
    {
        $validatedData = $request->validated();

        DB::transaction(function () use ($request, $validatedData) {
            $priority = $validatedData['priority'];

            // 現在の最大 priority を取得
            $maxPriority = Bookmark::max('priority') ?? 0;

            if ($priority <= $maxPriority) {
                // 指定位置に割り込み → その priority 以上を +1 して後ろへずらす
                Bookmark::where('priority', '>=', $priority)->increment('priority');
            }
            // もし priority が max+1 ならシフト不要、そのまま末尾に追加

            // 画像ファイルの保存処理
            $fileName = $this->saveImageFile($request);

            Bookmark::create([
                'name'     => $validatedData['name'],
                'link_url' => $validatedData['url'],
                'priority' => $priority,
                'img_name' => $fileName,
            ]);
        });

        return redirect()
            ->route('bookmark')
            ->with('success', '新しいブックマークを登録しました。');
    }


    public function update(BookmarkRequest $request)
    {
        $validatedData = $request->validated();

        DB::transaction(function () use ($request, $validatedData) {
            $bookmark = Bookmark::findOrFail($validatedData['id']);
            $oldPriority = $bookmark->priority;
            $newPriority = $validatedData['priority'];

            // priority の調整
            if ($oldPriority !== $newPriority) {
                if ($oldPriority < $newPriority) {
                    Bookmark::whereBetween('priority', [$oldPriority + 1, $newPriority])
                        ->decrement('priority');
                } else {
                    Bookmark::whereBetween('priority', [$newPriority, $oldPriority - 1])
                        ->increment('priority');
                }
            }

            // 画像ファイルの保存処理
            $fileName = $this->saveImageFile($request);
            // 古い画像を削除
            if ($fileName && $bookmark->img_name) {
                Storage::delete('public/images/' . $bookmark->img_name);
            }

            $bookmark->update([
                'name'     => $validatedData['name'],
                'link_url' => $validatedData['url'],
                'priority' => $newPriority,
                // 新しい画像があれば更新、なければ既存を維持
                'img_name' => $fileName ?? $bookmark->img_name,
            ]);
        });

        return redirect()->route('bookmark')->with('success', '更新しました');
    }


    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $bookmark = Bookmark::findOrFail($id);
            $priority = $bookmark->priority;

            // 画像ファイルを削除
            if ($bookmark->img_name) {
                Storage::delete('public/images/' . $bookmark->img_name);
            }

            $bookmark->delete();

            // 後ろを詰める
            Bookmark::where('priority', '>', $priority)->decrement('priority');
        });

        return redirect()->route('bookmark')->with('success', '削除しました');
    }

}
