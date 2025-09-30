@extends('layouts.app')

@section('content')
    <div class="items-center text-center text-2xl">
        <h2 class="text-2xl font-bold mb-4">ゲーム結果</h2>

        {{-- ゲーム概要 --}}
        <div class="mb-6">
            <p><strong>ゲームID:</strong> {{ $myGame->id }}</p>
            <p><strong>ユーザーID:</strong> {{ $myGame->user_id }}</p>
            <p><strong>合計スコア:</strong> {{ $myGame->result }}</p>
        </div>

        {{-- 各ステージの結果一覧 --}}
        <table class="table-auto border-collapse border border-gray-400 w-full mb-6">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-400 px-4 py-2">ステージ</th>
                    <th class="border border-gray-400 px-4 py-2">ロケーションID</th>
                    <th class="border border-gray-400 px-4 py-2">距離 (km)</th>
                    <th class="border border-gray-400 px-4 py-2">スコア</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    <tr>
                        <td class="border border-gray-400 px-4 py-2 text-center">{{ $log->stage }}</td>
                        <td class="border border-gray-400 px-4 py-2 text-center">{{ $log->location_id }}</td>
                        <td class="border border-gray-400 px-4 py-2 text-center">
                            {{ $log->distance !== null ? $log->distance : '-' }}
                        </td>
                        <td class="border border-gray-400 px-4 py-2 text-center">
                            {{ $log->score !== null ? $log->score : '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- 戻るボタンや再挑戦リンク --}}
        <div class="flex space-x-4">
            <a href="{{ route('game.start') }}" 
            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                新しいゲームを始める
            </a>
            <a href="{{ route('home') }}" 
            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                ホームに戻る
            </a>
        </div>
    </div>
@endsection

@push('scripts')
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&callback=initMap">
</script>
<script>
    function initMap() {
        // Bladeから渡された座標をJS変数に変換
        const latQ = {{ $latQ }};
        const lngQ = {{ $lngQ }};
        const latA = {{ $latA }};
        const lngA = {{ $lngA }};
        const distance = "{{ $distance }} km";

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
@endpush