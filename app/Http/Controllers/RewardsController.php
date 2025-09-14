<?php

namespace App\Http\Controllers;

use App\Models\Keyword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RewardsController extends Controller
{
    public function test()
    {
        $data = Keyword::where('id', 1)->first();

        return view('test', [
            'title' => 'TEST',
            'data' => $data,
        ]);
    }

    public function index(Request $request)
    {
        // 検索実行後の待ち時間をここで変更できます（ミリ秒で指定）
        $waitTime_bing = 7000;
        $waitTime_rakuten = 3000;

        // バリデーション
        $validated = $request->validate([
            'random' => ['required', 'integer', 'between:0,1'],
            'unit' => ['required', 'integer', 'between:1,5'],
            'max'  => ['required', 'integer', 'between:1,10'],
            'now'  => ['required', 'integer', 'min:1', 'lte:max'], // max以下
            'site' => ['required', 'in:bing,rakuten'],
            // type: siteがbingのとき必須 & search または news のみ
            'type' => ['required_if:site,bing', 'in:search,news', 'nullable'],
        ]);
        $random = $validated['random'];
        $unit = $validated['unit'];
        $max  = $validated['max'];
        $now  = $validated['now'];
        $site = $validated['site'];
        $type = $validated['type'] ?? null;

        // siteに応じて$waitingを設定
        if ($site === 'bing') {
            $waiting = $waitTime_bing ;
        } elseif ($site === 'rakuten') {
            $waiting = $waitTime_rakuten;
;
        }

        // 検索用URLを作成
        $keywords = $this->getKeywords($unit);
        $urls = $this->genarate_urls($site, $type, $keywords);

        // 検索終了後の遷移先URLを作成
        // .envファイルからURL（共通部分）を取得（開発環境と本番環境で切り替えるため.envを利用）
        $domain = config('app.my_domain');
        $rewards_url = $domain . "/rewards";
        if ($now < $max) {
            $next = $now + 1;
            $nextLink = $rewards_url . "?random=".$random."&site=".$site."&type=".$type."&unit=".$unit."&max=".$max."&now=".$next;
        } else {
            $nextLink = $rewards_url . "/home";
        }

        // 楽天検索を連続で行う分岐ありの場合
        // if ($now < $max) {
        //     $next = $now + 1;
        //     $nextLink = $domain . "?random=".$random."&site=".$site."&type=".$type."&unit=".$unit."&max=".$max."&now=".$next;
        // } elseif ($site == "bing" && $max == 6) {
        //     $nextLink = $domain . "?random=".$random."site=rakuten&unit=3&max=10&now=1";
        // } else {
        //     $nextLink = $domain . "/rewards/home";
        // }
        
        // dd($keywords, $nextLink);

        return view('rewards_unit', [
            'title' => 'UNIT',
            'random' => $random,
            'waiting' => $waiting,
            'urls' => $urls,
            'nextLink' => $nextLink
        ]);

    }

    // $keywords配列を元に、検索サイトとタイプを指定してURLを生成
    public function genarate_urls($site, $type, $keywords)
    {
        $urls = [];
        foreach($keywords as $word) {
            if ($site == "bing") {
                $key = "?q=" . $word;
                $href = "https://www.bing.com/" . $type . $key;
                $href .= "&qs=n&form=QBRE&sp=-1&ghc=1&lq=0" . "&p" . $key;
            } else if ($site == "rakuten") {
                $href = "https://websearch.rakuten.co.jp/Web?qt=" . $word . "&ref=top_r&col=OW";
            } else {
                
            }
            $urls[] = $href;
        }
        return $urls;
    }

    // DBから $unit 件だけ単語を取得（直前に取得した単語の次の単語から取得を開始）
    public function getKeywords($unit)
    {
        return DB::transaction(function () use ($unit) {

            // 現在の history=1（取得開始位置の目印） のレコードを取得（なければ最小IDを基準に設定）
            $current = Keyword::where('history', 1)
                ->orderBy('id', 'asc')
                ->first();

            if (!$current) {
                $current = Keyword::orderBy('id', 'asc')->first();
                $current->update(['history' => 1]);
            }

            // 基準位置から $unit 件取得
            $keywords = Keyword::where('id', '>=', $current->id)
                ->orderBy('id', 'asc')
                ->limit($unit)
                ->pluck('word', 'id')
                ->toArray();

            // 件数不足なら先頭から追加取得
            if (count($keywords) < $unit) {
                $remaining = $unit - count($keywords);
                $extra = Keyword::orderBy('id', 'asc')
                    ->limit($remaining)
                    ->pluck('word', 'id')
                    ->toArray();
                $keywords += $extra; // IDキーを維持して結合
            }

            // 次の history=1 の位置を決定
            $lastId = array_key_last($keywords);
            $next = Keyword::where('id', '>', $lastId)
                ->orderBy('id', 'asc')
                ->first() ?? Keyword::orderBy('id', 'asc')->first();

            // history を更新（全て null にしてから次の位置を 1 に）
            Keyword::whereNotNull('history')->update(['history' => null]);
            $next->update(['history' => 1]);

            // 配列をシャッフルして返す
            $shuffled = array_values($keywords);
            shuffle($shuffled);

            return $shuffled;
        });
    }
}
