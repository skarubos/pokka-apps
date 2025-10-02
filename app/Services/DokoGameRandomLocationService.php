<?php

namespace App\Services;

use App\Models\User;
use App\Models\Location;
use App\Models\Game;
use App\Models\GameLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DokoGameRandomLocationService
{
    /**
     * 指定した緯度経度がGeoJSON内のどの行政区に含まれるか判定する
     *
     * @param int $count 取得する地点の数(ステージ数)
     * @return array|null 取得した地点の配列
     */
    public function getLocationsInJapan(int $count): ?array
    {
        // GeoJSONファイルを読み込み
        $path = storage_path('app\geo\gadm41_JPN_2.geojson');
        $geojson = json_decode(file_get_contents($path), true);

        $locations = [];

        while (count($locations) < $count) {
            [$lng, $lat] = $this->generateRandomPointByRegion();

            foreach ($geojson['features'] as $feature) {
                $geometry = $feature['geometry'];
                $type = $geometry['type'];
                $coordinates = $geometry['coordinates'];

                if ($type === 'MultiPolygon') {
                    foreach ($coordinates as $polygon) {
                        if ($this->pointInPolygon([$lng, $lat], $polygon[0])) {
                            $locations[] = [
                                'lat'        => $lat,
                                'lng'        => $lng,
                                'region'     => $feature['properties']['NL_NAME_1'] ?? $feature['properties']['NAME_1'],
                                'sub_region' => $feature['properties']['NL_NAME_2'] ?? $feature['properties']['NAME_2'],
                            ];
                            break 2; // ポリゴン探索を抜けて次の地点生成へ
                        }
                    }
                }
            }
        }

        return $locations;
    }

    /**
     * 射影法による点がポリゴン内にあるかどうかの判定
     *
     * @param array $point [lng, lat]
     * @param array $polygon [[lng, lat], ...]
     * @return bool
     */
    private function pointInPolygon(array $point, array $polygon): bool
    {
        [$x, $y] = $point;
        $inside = false;
        $n = count($polygon);

        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            [$xi, $yi] = $polygon[$i];
            [$xj, $yj] = $polygon[$j];

            $intersect = (($yi > $y) !== ($yj > $y))
                && ($x < ($xj - $xi) * ($y - $yi) / (($yj - $yi) ?: 1e-12) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }

    /**
     * 日本の主要な地域ごとに矩形を定義し、その中でランダムな緯度経度を生成
     * 各地域の面積に応じて重み付けを行い、より広い地域が選ばれやすくする
     */
    public function generateRandomPointByRegion(): array
    {
        $regions = $this->regions;

        // 各矩形の面積を計算（単純に緯度差×経度差）
        $areas = [];
        $totalArea = 0;
        foreach ($regions as $region) {
            $area = ($region['lat_max'] - $region['lat_min']) * ($region['lng_max'] - $region['lng_min']);
            $areas[] = $area;
            $totalArea += $area;
        }

        // 面積に応じた重み付き乱数で地域を選択
        $rand = mt_rand() / mt_getrandmax() * $totalArea;
        $cumulative = 0;
        $selectedRegion = null;
        foreach ($regions as $i => $region) {
            $cumulative += $areas[$i];
            if ($rand <= $cumulative) {
                $selectedRegion = $region;
                break;
            }
        }

        // 選ばれた矩形内でランダムな緯度経度を生成
        $lat = mt_rand($selectedRegion['lat_min'] * 100000, $selectedRegion['lat_max'] * 100000) / 100000.0;
        $lng = mt_rand($selectedRegion['lng_min'] * 100000, $selectedRegion['lng_max'] * 100000) / 100000.0;

        // dd($lat, $lng, $selectedRegion);
        // return [$lat, $lng];
        return [$lng, $lat];
    }
    // 日本の主要な地域ごとの矩形定義
    private array $regions = [
        // 北海道
        ['lat_min' => 41.5, 'lat_max' => 45.5, 'lng_min' => 139.5, 'lng_max' => 145.5],
        // 東北
        ['lat_min' => 38.0, 'lat_max' => 41.5, 'lng_min' => 139.0, 'lng_max' => 142.0],
        // 関東・中部
        ['lat_min' => 35.0, 'lat_max' => 38.0, 'lng_min' => 137.0, 'lng_max' => 141.5],
        // 近畿・中国・四国
        ['lat_min' => 33.0, 'lat_max' => 35.0, 'lng_min' => 132.0, 'lng_max' => 135.5],
        // 九州
        ['lat_min' => 31.0, 'lat_max' => 33.0, 'lng_min' => 129.5, 'lng_max' => 132.0],
    ];


    function generateRandomPointInJapan(): array {
        $lat = mt_rand(2400000, 4600000) / 100000.0;
        $lng = mt_rand(12300000, 14600000) / 100000.0;
        return [$lat, $lng];
    }

    /**
     * 指定された緯度経度が日本の陸地内にあるかどうかを判定
     */
    function isPointOnLand(float $lat, float $lng): bool {
        // 日本のポリゴンを読み込み
        $geojson = file_get_contents(storage_path('app/geo/japan.geojson'));
        $japanPolygon = geoPHP::load($geojson, 'geojson');

        // 点を作成
        $point = geoPHP::load("POINT($lng $lat)", 'wkt');

        // ポリゴン内かどうか判定
        return $japanPolygon->contains($point);
    }



    /**
     * 指定されたゲーム・ステージのロケーションを返す
     */
    public function getLocation(int $gameId, int $stage)
    {
        // 対応する game_logs を取得
        $gameLog = GameLog::where('game_id', $gameId)
            ->where('stage', $stage)
            ->first();

        return Location::select('id','name','country','city','lat','lng')
            ->find($gameLog->location_id);
    }

}
