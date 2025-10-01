@extends('layouts.doko')

@section('content')
    <div id="question-container" class="">
        <div id="map" class="w-full h-screen border border-gray-300 rounded-lg shadow-md"></div>
    </div>
    <div id="answer-container" class="hidden">
        <!-- マップコンテナ -->
        <div id="answer-map" class="relative w-full h-screen border border-gray-300 rounded-lg shadow-md"></div>

        <!-- 中心の十字マーク -->
        <div id="crosshair"
            class="absolute top-1/2 left-1/2 w-6 h-6 -ml-3 -mt-3 pointer-events-none z-10">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <line x1="12" y1="0" x2="12" y2="24" stroke="red" stroke-width="2"/>
                <line x1="0" y1="12" x2="24" y2="12" stroke="red" stroke-width="2"/>
            </svg>
        </div>

        <!-- 回答フォーム -->
        <form id="answer-form" method="POST" action="{{ route('doko.answer') }}" class="flex flex-col items-center space-y-2">
            @csrf
            <input type="hidden" name="game_id" value="{{ $myGame->id }}">
            <input type="hidden" name="stage" value="{{ $myGame->progress }}">
            <input type="hidden" name="lat" id="answer-lat">
            <input type="hidden" name="lng" id="answer-lng">
            <button type="submit"
                class="flex fixed right-5 top-1/2 -translate-y-1/2
                    items-center justify-center
                    w-25 h-40 rounded-xl cursor-pointer
                    text-white text-xl font-bold
                    hover:outline-3 outline-offset-3 outline-gray-800
                    bg-gray-800/90 hover:bg-gray-800 transition">
                解答
            </button>
        </form>
    </div>
    <div class="text-xl font-bold">
        <button id="show-q-map"
            class="flex group fixed left-10 bottom-[calc(50%+01rem)]
                items-center justify-center
                w-25 h-25 rounded-xl cursor-pointer
                text-white text-xl font-bold
                hover:outline-3 outline-offset-3 outline-gray-800
                bg-gray-800/90 hover:bg-gray-800">
            Q.
        </button>
        <button id="show-a-map"
            class="flex group fixed left-10 top-[calc(50%+1rem)]
                items-center justify-center
                w-25 h-25 rounded-xl cursor-pointer
                text-white text-xl font-bold
                hover:outline-3 outline-offset-3 outline-gray-800
                bg-gray-800/90 hover:bg-gray-800">
            A.
        </button>
    </div>
@endsection

@push('scripts')
<script>
    // 問題地点の座標と制限範囲を取得
    const question = @json($location);
    const latQ = parseFloat(question.lat);
    const lngQ = parseFloat(question.lng);
    const delta = {{ $delta }};
    console.log(question);
    console.log("Question Location:", latQ, lngQ, delta);

    window.initMaps = function() {
        initMap();
        initAnswerMap();
    };

    // 問題用マップ
    function initMap() {
        const center = { lat: latQ, lng: lngQ };

        // 地図を初期化
        const map = new google.maps.Map(document.getElementById("map"), {
            center: center,
            zoom: 12,
            mapTypeId: "satellite",
            disableDefaultUI: true,
            restriction: {
                latLngBounds: {
                    north: latQ + delta,
                    south: latQ - delta,
                    east: lngQ + delta,
                    west: lngQ - delta,
                },
                strictBounds: true,
            },
            styles: [{
                featureType: "all",
                elementType: "labels",
                stylers: [{ visibility: "off" }],
            },],
        });

        // 中心にマーカーを設置
        new google.maps.Marker({
            position: center,
            map: map,
            title: "中心座標"
        });
    }


    // 回答用マップ
    let answerMap;
    function initAnswerMap() {
        answerMap = new google.maps.Map(document.getElementById("answer-map"), {
            center: { lat: 20, lng: 0 },
            zoom: 2,
            mapTypeId: "roadmap",
            disableDefaultUI: true,
        });

        // フォーム送信時に中心座標を取得
        document.getElementById("answer-form").addEventListener("submit", function () {
            const center = answerMap.getCenter();
            document.getElementById("answer-lat").value = center.lat();
            document.getElementById("answer-lng").value = center.lng();
        });
    }


    // Q/Aマップ切り替えボタンの動作
    const qBtn = document.getElementById('show-q-map');
    const aBtn = document.getElementById('show-a-map');
    const qContainer = document.getElementById('question-container');
    const aContainer = document.getElementById('answer-container');

    // 初期状態: Q表示、Qボタン無効化
    qContainer.classList.remove('hidden');
    aContainer.classList.add('hidden');
    qBtn.disabled = true;
    qBtn.classList.add('outline-3', 'opacity-80', 'cursor-not-allowed');

    // Qボタンクリック時
    qBtn.addEventListener('click', () => {
        qContainer.classList.remove('hidden');
        aContainer.classList.add('hidden');

        qBtn.disabled = true;
        qBtn.classList.add('outline-3', 'opacity-80', 'cursor-not-allowed');

        aBtn.disabled = false;
        aBtn.classList.remove('outline-3', 'opacity-80', 'cursor-not-allowed');
    });

    // Aボタンクリック時
    aBtn.addEventListener('click', () => {
        aContainer.classList.remove('hidden');
        qContainer.classList.add('hidden');

        aBtn.disabled = true;
        aBtn.classList.add('outline-3', 'opacity-80', 'cursor-not-allowed');

        qBtn.disabled = false;
        qBtn.classList.remove('outline-3', 'opacity-80', 'cursor-not-allowed');
    });
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&callback=initMaps&v=weekly"></script>
@endpush