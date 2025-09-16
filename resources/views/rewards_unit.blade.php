<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache">
    <meta name="referrer" content="no-referrer">
    <title>UNIT</title>
    @vite('resources/css/app.css') {{-- TailwindやCSSビルド用 --}}
</head>
<body class="bg-gray-900 text-gray-300">
<main class="container mx-auto p-6">
    <p class="text-5xl font-bold mb-10" id="param-now"></p>
    <p class="text-2xl font-semibold">{{ $nextLink }}</p>
</main>
<script>
    // 現在のURLの"now"パラメータを取得（文字列で返る）
    const params = new URLSearchParams(window.location.search);
    const nowParam = params.get('now');

    // p要素に現在のループ数として表示
    const target = document.getElementById('param-now');
    if (target) {
    target.textContent = nowParam !== null ? nowParam : 'パラメータがありません';
    }

    // PHP配列 → JavaScript配列に変換
    let urls = @json($urls);
    let nextLink = "{!! $nextLink !!}";
    let wt = {{ $waiting }};
    let random = {{ $random }};

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
            let tab = window.open(href, i + 1);
            tabs.push(tab);

            // ランダム秒数の挿入
            if (random == 1) {
                // 刻み幅ごとの候補数を計算
                const stepsCount = range / step + 1;

                // ランダムなインデックスを選び、ミリ秒に変換
                wt = min + Math.floor(Math.random() * stepsCount) * step;
            };
            console.log(wt, href);
            Sleep(wt);
        }
        // 全てのタブを閉じる
        tabs.forEach(function(tab) {
            setTimeout(() => tab.close(), 1000);
        });
    }

    // ページが読み込まれたら実行
    window.onload = function() {
        // URLを新規タブで順次開く
        OpenLinks(urls);

        // 次のUNITへジャンプ（random=1の場合、3ループ目に待機時間挿入）
        let interval = 1000;
        if (random == 1) {
            interval = nowParam === '3' ? 7000 : 1000;
        }
        // console.log(interval, random);
        // debugger;
        setTimeout(() => {
            window.location.href = nextLink;
        }, interval);
    }
    
</script>
</body>
</html>