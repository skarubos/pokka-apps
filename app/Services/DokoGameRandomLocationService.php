<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Location;
use App\Models\GameLog;
use App\Models\Mesh1km2020JapanMap;
use App\Models\Prefecture;
use App\Models\ShiCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DokoGameRandomLocationService
{
    /** === Mode: 4 === */

    /**
     * 世界の地点（人口密集地）を国ごとの重みづけありでランダムに地点を取得
     *
     * @param int $count 取得する地点の数(ステージ数)
     * @return array 取得した地点の配列
     */
    public function getRandomWeightedPopulatedLocations(int $count): array
    {
        // 国を重みづけありで取得
        // 人口密度「10000人以上」と「5000〜10000人」を少なくとも1つずつ含めるようにランダム個数取得
        $randomNum = random_int(1, $count - 1);
        $table_name = 'countries';
        $column_get = 'country_id';

        $column_weight = 'above10000';
        $arr_above10000 = $this->getWeightedRegions($randomNum, 0.2, $table_name, $column_get, $column_weight);
        $column_weight = '5000to10000';
        $arr_5000to10000 = $this->getWeightedRegions($count - $randomNum, 0.3, $table_name, $column_get, $column_weight);

        $countries = array_merge($arr_above10000, $arr_5000to10000);
        shuffle($countries);

        // Regionを指定してランダムに地点を取得
        $points = $this->getRandomLocationsInRegion($countries);

        // 座標に国名情報を付加
        $result = $this->addCountryName($points);
// dd($countries, $points, $result);
        return $result;
    }

    /**
     * 任意の数値カラムに基づき重みづけありでRegionをランダム抽出
     *
     * @param int $count 抽出数
     * @param float $exponent 重みづけパラメータ
     * @param string $table_name テーブル名
     * @param string $column_get 取得するカラム名
     * @param string $column_weight 重みを計算するカラム名
     * @return array 選ばれたRegionのID,カラム名を持つ連想配列の配列
     */
    public function getWeightedRegions(int $count = 5, float $exponent = 1.0, string $table_name, string $column_get, string $column_weight): array
    {
        $data = DB::table($table_name)->select($column_get, $column_weight)->get();

        // $column_weight カラムの値を exponent 乗して重みを計算
        // exponent=1 → 値に比例
        // exponent=0.5 → sqrt($column_weight) に比例（差を弱める）
        // exponent=2 → $column_weight^2 に比例（差を強める）
        $weights = [];
        foreach ($data as $rec) {
            $weights[$rec->$column_get] = pow($rec->$column_weight, $exponent);
        }

        // 重みの合計を算出（ルーレット選択の基準となる）
        $totalWeight = array_sum($weights);

        // 選ばれたIDを格納する連想配列
        $selected = [];

        // 指定件数に達するまで繰り返す
        while (count($selected) < $count) {
            // 0〜totalWeight の範囲で乱数を生成
            $rand = mt_rand() / mt_getrandmax() * $totalWeight;

            // 累積和を使って「ルーレット選択」を実施
            $cumulative = 0;
            foreach ($weights as $id => $weight) {
                $cumulative += $weight;

                // 乱数が累積値を超えた時点でそのIDを選択
                if ($rand <= $cumulative) {
                    // すでに選ばれていなければ追加
                    if (!in_array($id, $selected)) {
                        $selected[] = [
                            'id' => $id,
                            'column' => $column_weight,
                        ];
                    }
                    break; // 1件選んだらループを抜ける
                }
            }
        }

        // 選ばれたIDの配列を返す
        return $selected;
    }

    /**
     * Regionの配列からランダムに地点をコレクション型で取得
     *
     * @param array $regions RegionのIDと指定カラム名の連想配列の配列
     * @return array ランダムに取得された地点のコレクション
     */
    public function getRandomLocationsInRegion(array $regions) {
        $points = [];
        foreach ($regions as $region) {
            $tabel_name = 'mesh_1km_2020_world_' . $region['column'] . '_maps';
            $count = DB::table($tabel_name)
                ->where('country_id', $region['id'])
                ->count();
            $randomOffset = rand(0, $count - 1);

            $points[] = DB::table($tabel_name)
                ->where('country_id', $region['id'])
                ->skip($randomOffset)
                ->first();
        }
        return $points;
    }

    /**
     * 国名をロケーション情報に付与
     *
     * @param array $points 地点情報の配列
     * @return array 国名付きの地点情報の配列
     */
    public function addCountryName(array $points): array
    {
        $countries = Country::where(function ($query) {
                $query->where('above10000', '>', 0)
                    ->orWhere('5000to10000', '>', 0);
            })
            ->get()
            ->keyBy('country_id');

        $locations = [];
        foreach ($points as $point) {
            $locations[] = [
                'country_id'    => $point->country_id,
                'country_name'  => $countries[$point->country_id]->name ?? null,
                'lat'           => $point->lat,
                'lng'           => $point->lng,
                'population'    => $point->population,
            ];
        }
        return $locations;
    }



    /** === Mode: 3 === */

    /**
     * 日本全国（人口あり）の地点を重みづけありでランダムに取得
     *
     * @param int $count 取得する地点の数(ステージ数)
     * @return array 取得した地点の配列
     */
    public function getRandomWeightedLocationsInJapan(int $count): array
    {
        // 都道府県を重み付きで取得
        $prefectures = $this->getPrefectures($count, 1.0);

        // メッシュ単位でランダムに選定
        $meshes = $this->getWeightedRandomMeshes($prefectures);

        // メッシュから座標を抽出
        $points = $this->getRandomLocationsFromMeshes($meshes);

        // 座標に地名情報を付加
        return $this->addLocationInfo($points);
    }

    /**
     * 面積（または任意の数値カラム）に基づき重みづけされたランダム抽出
     *
     * @param int $count 抽出数（例: 5件）
     * @param float $exponent 重みづけパラメータ
     * @return array 選ばれた都道府県IDの配列
     */
    public function getPrefectures(int $count = 5, float $exponent = 1.0): array
    {
        $prefectures = Prefecture::all(['id', 'size']);

        // size の値を exponent 乗して重みを計算
        // exponent=1 → 値に比例
        // exponent=0.5 → sqrt(size) に比例（差を弱める）
        // exponent=2 → size^2 に比例（差を強める）
        $weights = [];
        foreach ($prefectures as $pref) {
            $weights[$pref->id] = pow($pref->size, $exponent);
        }

        // 重みの合計を算出（ルーレット選択の基準となる）
        $totalWeight = array_sum($weights);

        // 選ばれたIDを格納する配列
        $selected = [];

        // 指定件数に達するまで繰り返す
        while (count($selected) < $count) {
            // 0〜totalWeight の範囲で乱数を生成
            $rand = mt_rand() / mt_getrandmax() * $totalWeight;

            // 累積和を使って「ルーレット選択」を実施
            $cumulative = 0;
            foreach ($weights as $id => $weight) {
                $cumulative += $weight;

                // 乱数が累積値を超えた時点でそのIDを選択
                if ($rand <= $cumulative) {
                    // すでに選ばれていなければ追加
                    if (!in_array($id, $selected)) {
                        $selected[] = $id;
                    }
                    break; // 1件選んだらループを抜ける
                }
            }
        }

        // 選ばれた都道府県IDの配列を返す
        return $selected;
    }

    /**
     * 指定された都道府県IDごとに、重み付きランダムで1件メッシュを取得する
     *
     * @param array $prefectureIds 都道府県IDの配列
     * @return array 選ばれたレコードの配列
     */
    public function getWeightedRandomMeshes(array $prefectureIds): array
    {
        $results = [];

        foreach ($prefectureIds as $prefId) {
            // まず対象pref_idの最小値・最大値を取得
            $stats = Mesh1km2020JapanMap::where('pref_id', $prefId)
                ->selectRaw('MIN(ptn_2020) as min_val, MAX(ptn_2020) as max_val')
                ->first();

            if (!$stats || $stats->min_val === null || $stats->max_val === null) {
                continue;
            }

            $min = (float) $stats->min_val;
            $max = (float) $stats->max_val;
            $range = max($max - $min, 1e-9); // 0除算回避

            // 全件を取得すると重いので、idとptn_2020だけを取得
            $rows = Mesh1km2020JapanMap::where('pref_id', $prefId)
                ->select('id', 'pref_id', 'ptn_2020')
                ->get();

            if ($rows->isEmpty()) {
                continue;
            }

            // 重みを計算（min→1, max→2）
            $weights = [];
            foreach ($rows as $row) {
                $weights[$row->id] = 1.0 + (($row->ptn_2020 - $min) / $range);
            }

            $totalWeight = array_sum($weights);
            $rand = mt_rand() / mt_getrandmax() * $totalWeight;

            // ルーレット選択
            $cumulative = 0;
            $selectedId = null;
            foreach ($weights as $id => $weight) {
                $cumulative += $weight;
                if ($rand <= $cumulative) {
                    $selectedId = $id;
                    break;
                }
            }

            if ($selectedId) {
                $record = Mesh1km2020JapanMap::where('id', $selectedId)
                    ->first();
                if ($record) {
                    $results[] = $record;
                }
            }
        }

        return $results;
    }

    /**
     * getWeightedRandomMeshes() の結果を受け取り、
     * 各メッシュの緯度経度範囲からランダムに1点を選ぶ
     *
     * @param array $meshes getWeightedRandomMeshes() の返却配列
     * @return array 各メッシュごとに選ばれた緯度経度の配列
     */
    public function getRandomLocationsFromMeshes(array $meshes): array
    {
        $points = [];

        foreach ($meshes as $mesh) {
            // 緯度を min_lat〜max_lat の範囲でランダムに選ぶ
            $lat = $this->randomFloatInRange((float)$mesh->min_lat, (float)$mesh->max_lat, 4);

            // 経度を min_lng〜max_lng の範囲でランダムに選ぶ
            $lng = $this->randomFloatInRange((float)$mesh->min_lng, (float)$mesh->max_lng, 4);

            $points[] = [
                'mesh_id' => $mesh->mesh_id,
                'pref_id' => $mesh->pref_id,
                'shicode' => $mesh->shicode,
                'population' => $mesh->ptn_2020,
                'lat'     => $lat,
                'lng'     => $lng,
            ];
        }

        return $points;
    }

    /**
     * 指定範囲内でランダムな浮動小数点数を生成（小数点以下 $precision 桁）
     */
    private function randomFloatInRange(float $min, float $max, int $precision = 4): float
    {
        $scale = pow(10, $precision);
        return mt_rand((int)($min * $scale), (int)($max * $scale)) / $scale;
    }

    /**
     * 都道府県名・市区町村名をロケーション情報に付与
     *
     * @param array $points メッシュの情報の配列
     * @return array ロケーション情報の配列
     */
    public function addLocationInfo(array $points): array
    {
        $prefectures = Prefecture::all()->keyBy('id');
        $shicodes = ShiCode::all()->keyBy('code');

        $locations = [];
        foreach ($points as $point) {
            $shicode_head = explode('_', $point['shicode'])[0]; // 先頭の市コードを抽出

            $locations[] = [
                'mesh_id' => $point['mesh_id'],
                'pref_id' => $point['pref_id'],
                'pref_name' => $prefectures[$point['pref_id']]->name ?? null,
                'shicode' => $point['shicode'],
                'shi_name' => $shicodes[(int)$shicode_head]->city ?? null,
                'lat'     => $point['lat'],
                'lng'     => $point['lng'],
                'population' => $point['population'],
            ];
        }
        return $locations;
    }



    /** === Mode: 2 === */

    /**
     * 指定した緯度経度がGeoJSON内のどの行政区に含まれるか判定する
     *
     * @param int $count 取得する地点の数(ステージ数)
     * @return array 取得した地点の配列
     */
    public function getRandomLocationsInJapan(int $count): array
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



    /** === Mode: 1 === */

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
