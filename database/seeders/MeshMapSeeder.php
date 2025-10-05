<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MeshMapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($prefId = 1; $prefId <= 47; $prefId++) {
            $fileName = sprintf('1km_mesh_slim_2024_%02d.geojson', $prefId);
            $path = storage_path("app/geo/{$fileName}");

            if (!file_exists($path)) {
                continue; // ファイルが存在しない場合はスキップ
            }

            $geojson = json_decode(file_get_contents($path), true);

            foreach ($geojson['features'] as $feature) {
                $props = $feature['properties'];
                $coords = $feature['geometry']['coordinates'][0]; // Polygonの外周リング

                $lngs = array_column($coords, 0);
                $lats = array_column($coords, 1);

                $min_lng = round(min($lngs), 3);
                $min_lat = round(min($lats), 3);
                $max_lng = round(max($lngs), 3);
                $max_lat = round(max($lats), 3);

                DB::table('mesh_1km_2020_japan_maps')->insert([
                    'pref_id'   => $prefId,
                    'mesh_id'   => (int) $props['MESH_ID'],
                    'shicode'   => (int) $props['SHICODE'],
                    'ptn_2020'  => (float) $props['PTN_2020'],
                    'min_lng'   => $min_lng,
                    'min_lat'   => $min_lat,
                    'max_lng'   => $max_lng,
                    'max_lat'   => $max_lat,
                ]);
            }
        }
    }
    
    // public function run(): void
    // {
    //    $path = storage_path('app/geo/1km_mesh_slim_2024_47.geojson');
    //     $geojson = json_decode(file_get_contents($path), true);

    //     foreach ($geojson['features'] as $feature) {
    //         $props = $feature['properties'];
    //         $coords = $feature['geometry']['coordinates'][0]; 
    //         // Polygonの最初のリングを取得

    //         // 左下(最小経度・緯度)と右上(最大経度・緯度)を算出
    //         $lngs = array_column($coords, 0);
    //         $lats = array_column($coords, 1);

    //         $min_lng = min($lngs);
    //         $min_lat = min($lats);
    //         $max_lng = max($lngs);
    //         $max_lat = max($lats);

    //         DB::table('mesh_1km_2020_japan_maps')->insert([
    //             'mesh_id' => $props['MESH_ID'],
    //             'shicode' => $props['SHICODE'],
    //             'ptn_2020' => $props['PTN_2020'],
    //             'min_lng' => $min_lng,
    //             'min_lat' => $min_lat,
    //             'max_lng' => $max_lng,
    //             'max_lat' => $max_lat,
    //         ]);
    //     }
    // }
}
