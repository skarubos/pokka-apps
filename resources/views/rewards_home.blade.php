@extends('layouts.app')

@section('content')

    @php
        // .envファイルからURLを取得（開発環境と本番環境で切り替えるため）
        $domain = config('app.my_domain');
        $url = $domain . "/rewards?random=0";
    @endphp
    
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex pb-10">
    <div class="w-1/2">
        <p><b>れんぞく検索リンク</b></p>
        <div class="pl-5 pb-3">
            <x-button class="wt-target px-25" href="{{ $url }}&site=bing&type=news&unit=5&max=6&now=1">Daily検索</x-button>
        <br>
            <x-button class="wt-target" href="{{ $url }}&site=bing&type=search&unit=2&max=1&now=1">Bing 2検索</x-button>
            <x-button class="wt-target" href="{{ $url }}&site=bing&type=search&unit=5&max=6&now=1">Bing 5x6検索</x-button>
            <br>
            <x-button class="wt-target" href="{{ $url }}&site=bing&type=news&unit=2&max=1&now=1">NEWS 2検索</x-button>
            <x-button class="wt-target" href="{{ $url }}&site=bing&type=news&unit=5&max=1&now=1">NEWS 5検索</x-button>
<!--         
            <x-button class="wt-target" href="{{ $url }}&site=rakuten&unit=2&max=1&now=1">楽天 2検索</x-button>
            <x-button class="wt-target" href="{{ $url }}&site=rakuten&unit=2&max=2&now=1">楽天 3x10検索</x-button>
            <x-button class="wt-target" href="https://websearch.rakuten.co.jp/Web?qt=明日の天気&ref=top_r&col=OW" target="_blank">楽天 「明日の天気」</x-button>
         -->
        </div>

        <p><b>公式リンク</b></p>
        <div class="pl-5 pb-3">
            <x-button href="https://rewards.bing.com/" target="_blank">Microsoft Rewards</x-button>
            <x-button href="https://rewards.bing.com/pointsbreakdown" target="_blank">MR ポイント確認</x-button>
            <br>
            <x-button ref="hthtps://websearch.rakuten.co.jp/" target="_blank">楽天検索 Home</x-button>
            <x-button href="https://get.point.rakuten.co.jp/" target="_blank">楽天ポイントコード入力</x-button>
        </div>
    </div>
    <div class="w-1/2">
        <div class="flex items-center pt-5 gap-4">
            <!-- トグル -->
            <button 
                id="wt-toggle"
                type="button"
                class="bg-gray-400 relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                role="switch"
                aria-checked="false"
            >
                <span 
                    id="wt-toggle-knob"
                    class="translate-x-0 pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                ></span>
            </button>
            <span class="text-m">ランダム待機時間</span>
        </div>
    </div>
    </div>
    <p>※注意：複数のタブが順々に開きます！</p>
    <p>※ブラウザとその設定によっては開かない場合があります（ポップアップを許可=ブロック解除すれば動作する場合があります）。</p>
@endsection

@push('scripts')
<script>
    const toggle = document.getElementById('wt-toggle');
    const knob = document.getElementById('wt-toggle-knob');
    const buttons = document.querySelectorAll('.wt-target');

    // 初期状態（OFF）
    toggle.setAttribute('aria-checked', 'false');

    toggle.addEventListener('click', () => {
        const isOn = toggle.getAttribute('aria-checked') === 'true';
        const newState = !isOn;

        // トグル見た目切り替え
        toggle.setAttribute('aria-checked', String(newState));
        toggle.classList.toggle('bg-indigo-600', newState);
        toggle.classList.toggle('bg-gray-200', !newState);
        knob.classList.toggle('translate-x-5', newState);
        knob.classList.toggle('translate-x-0', !newState);

        // 全ボタンのhrefを一括更新
        buttons.forEach(btn => {
            const currentHref = btn.getAttribute('href');
            // random=0 または random=1 を置換
            const updatedHref = currentHref.replace(/random=\d/, `random=${newState ? 1 : 0}`);
            btn.setAttribute('href', updatedHref);
        });
    });
</script>
@endpush