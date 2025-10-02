@extends('layouts.doko')

@section('content')
    <div class="items-end text-center text-2xl py-6">
        <div class="inline-flex py-1 px-6 mr-10 bg-gray-700 rounded-full">
            <p class="">Stage:<span class="text-3xl font-bold"> {{ $gameLog->stage }} </span>/ 5</p>
        </div>
        <p class="inline-flex text-5xl text-white font-bold">{{ $gameLog->score }}</p>
        <p class="inline-flex px-3"> / 1000 点</p>
        <p class="inline-flex px-3">（誤差：{{ $gameLog->distance }} km）</p>
    </div>
    <div class="flex-1">
        <div id="map" class="w-full h-full"></div>
        <!-- 「次へ」ボタン -->
        <form method="POST" action="{{ route('doko.next') }}" class="">
            @csrf
            <input type="hidden" name="game_id" value="{{ $myGame->id }}">
            <input type="hidden" name="game_mode_id" value="{{ $myGame->game_mode_id }}">
            <button type="submit"
                class="flex fixed right-5 top-1/2 -translate-y-1/2
                    items-center justify-center
                    w-25 h-40 rounded-xl cursor-pointer
                    text-white text-xl font-bold
                    hover:outline-3 outline-offset-3 outline-gray-800
                    bg-gray-800/90 hover:bg-gray-800 transition">
                次へ
            </button>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    window.initMap = function() {
        // Bladeから渡された座標をJS変数に変換
        const latQ = Number(@json($gameLog->q_lat));
        const lngQ = Number(@json($gameLog->q_lng));
        const latA = {{ $latA }};
        const lngA = {{ $lngA }};
        const distance = "{{ $gameLog->distance }} km";
        console.log(latA, lngA, distance);

        const pointQ = { lat: latQ, lng: lngQ }; // 問題地点
        const pointA = { lat: latA, lng: lngA }; // 回答地点

        // 中心は2点の中間地点に設定
        const center = {
            lat: (latQ + latA) / 2,
            lng: (lngQ + lngA) / 2
        };

        // 地図を初期化
        const map = new google.maps.Map(document.getElementById("map"), {
            center: center,
            zoom: 3,
            mapTypeId: "roadmap"
        });

        // 問題地点マーカー
        new google.maps.Marker({
            position: pointQ,
            map: map,
            title: "問題地点",
            icon: {
                url: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
            }
        });

        // 回答地点マーカー
        new google.maps.Marker({
            position: pointA,
            map: map,
            label: "A",
            title: "回答地点"
        });

        // 2点を結ぶ線を描画
        const line = new google.maps.Polyline({
            path: [pointQ, pointA],
            geodesic: true,
            strokeColor: "#FF0000",
            strokeOpacity: 1.0,
            strokeWeight: 2
        });
        line.setMap(map);

        // 両地点が収まるようにズーム調整
        const bounds = new google.maps.LatLngBounds();
        bounds.extend(pointQ);
        bounds.extend(pointA);
        map.fitBounds(bounds);

        // 吹き出し（距離表示）
        const infoWindow = new google.maps.InfoWindow({
            content: `<div class="text-gray-900 text-2xl font-bold px-5">${distance}</div>`,
            position: center,
            disableAutoPan: true
        });
        google.maps.event.addListenerOnce(infoWindow, 'domready', () => {
            const closeBtn = document.querySelector('.gm-ui-hover-effect');
            if (closeBtn) closeBtn.style.display = 'none';
        });
        infoWindow.open(map);
    }
</script>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&callback=initMap">
</script>
@endpush