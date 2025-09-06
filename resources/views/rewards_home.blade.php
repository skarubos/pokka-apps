@extends('layouts.app')

@section('content')

    @php
        // .envファイルからURLを取得（開発環境と本番環境で切り替えるため）
        $domain = config('app.rewards_url');
    @endphp

    <div>
        <p><b>れんぞく検索リンク</b></p>
        <div class="pl-5 pt-2 pb-3">
            <x-button class="px-25" href="{{ $domain }}?site=bing&type=news&unit=5&max=6&now=1">Daily検索</x-button>
        </div >
        <div class="pl-5 pt-2 pb-3">
            <x-button href="{{ $domain }}?site=bing&type=search&unit=2&max=1&now=1">Bing 2検索</x-button>
            <x-button href="{{ $domain }}?site=bing&type=search&unit=5&max=6&now=1">Bing 5x6検索</x-button>
            <x-button href="{{ $domain }}?site=bing&type=news&unit=2&max=1&now=1">NEWS 2検索</x-button>
            <x-button href="{{ $domain }}?site=bing&type=news&unit=5&max=1&now=1">NEWS 5検索</x-button>
        </div >
        <div class="pl-5 pb-5">
            <x-button href="{{ $domain }}?site=rakuten&unit=2&max=1&now=1">楽天 2検索</x-button>
            <x-button href="{{ $domain }}?site=rakuten&unit=2&max=2&now=1">楽天 3x10検索</x-button>
            <x-button href="https://websearch.rakuten.co.jp/Web?qt=明日の天気&ref=top_r&col=OW" target="_blank">楽天 「明日の天気」</x-button>
        </div >
    </div>
    <div class="pb-10">
        <p><b>公式リンク</b></p>
        <div class="pl-5 pt-2 pb-3">
            <x-button href="https://rewards.bing.com/" target="_blank">Microsoft Rewards</x-button>
            <x-button href="https://rewards.bing.com/pointsbreakdown" target="_blank">MR ポイント確認</x-button>
        </div >
        <div class="pl-5 pt-2 pb-3">
            <x-button href="https://websearch.rakuten.co.jp/" target="_blank">楽天検索 Home</x-button>
            <x-button href="https://get.point.rakuten.co.jp/" target="_blank">楽天ポイントコード入力</x-button>
        </div >
    </div>
    <p>※注意：複数のタブが順々に開きます！</p>
    <p>※ブラウザとその設定によっては開かない場合があります（ポップアップを許可=ブロック解除すれば動作する場合があります）。</p>
@endsection