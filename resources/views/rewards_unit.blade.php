@extends('layouts.app')

@section('content')
    <p class="text-5xl font-bold mb-10">{{ substr($nextLink, -5) }}</p>
    <p class="text-2xl font-semibold">{{ $nextLink }}</p>
@endsection

@push('scripts')
<script>
    // PHP配列 → JavaScript配列に変換
    let urls = @json($urls);
    let nextLink = "{!! $nextLink !!}";
    let wt = {{ $waiting }};
    let random = {{ $random }};

    console.log(urls);
    console.log(nextLink);

    // 指定ミリ秒待機
    function Sleep(wait) {
        var start = new Date();
        while (new Date() - start < wait);
    }

    function OpenLinks(urls){
        let tabs = [];
        let href;

        // ランダム待機時間のパラメータ設定
        const min = wt; // 最小値（ミリ秒）
        const range = 1000;   // ランダム幅（ミリ秒）
        const step = 100; // 刻み幅（ミリ秒）

        // 順番に開く
        for (let i = 0; i < urls.length; i++) {
            href = urls[i];
            console.log(href);
            let tab = window.open(href, i + 1);
            tabs.push(tab);
            // ランダム秒数の挿入
            if (random == 1) {
                // 刻み幅ごとの候補数を計算
                const stepsCount = range / step + 1;

                // ランダムなインデックスを選び、ミリ秒に変換
                const randomMs = min + Math.floor(Math.random() * stepsCount) * step;
                Sleep(randomMs);
            } else {
                Sleep(wt);
            }
        }
        // 全てのタブを閉じる（random=1の場合、3ループ目に待機時間挿入）
        tabs.forEach(function(tab) {
            if (random == 1) {
                interval = nextLink.slice(-1) === '4' ? 7000 : 1000;
            } else {
                interval = 1000;
            }
            setTimeout(() => tab.close(), interval);
        });
    }

    // ページが読み込まれたら実行
    window.onload = function() {
        OpenLinks(urls);
        setTimeout(() => {
            console.log(nextLink);
            debugger;
            window.location.href = nextLink;
        }, 1500);
    }
    
</script>
@endpush