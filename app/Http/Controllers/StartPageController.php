<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use Illuminate\Http\Request;

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
            ->route('startpage')
            ->with('success', "並び替えを保存しました。");
    }
}
