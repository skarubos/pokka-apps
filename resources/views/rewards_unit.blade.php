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
        // 順番に開く
        for (let step = 0; step < urls.length; step++) {
            href = urls[step];
            console.log(href);
            let tab = window.open(href, step + 1);
            tabs.push(tab);
            Sleep(wt);
        }
        // 2秒後に閉じる
        tabs.forEach(function(tab) {
            setTimeout(() => tab.close(), 1000);
        });
    }

    window.onload = function() {
        OpenLinks(urls);
        setTimeout(() => {
            // console.log(nextLink);
            window.location.href = nextLink;
        }, 1500);
    }
    
</script>
@endpush