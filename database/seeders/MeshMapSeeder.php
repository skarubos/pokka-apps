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
        // インデックス無効化
        DB::statement('ALTER TABLE mesh_1km_2020_japan_maps DISABLE KEYS');

        DB::transaction(function () {
            for ($prefId = 1; $prefId <= 47; $prefId++) {
                $fileName = sprintf('1km_mesh_slim_2024_%02d.geojson', $prefId);
                $path = storage_path("app/geo/{$fileName}");

                if (!file_exists($path)) {
                    continue;
                }

                $geojson = json_decode(file_get_contents($path), true);

                $batch = [];
                foreach ($geojson['features'] as $feature) {
                    $props = $feature['properties'];
                    $coords = $feature['geometry']['coordinates'][0];

                    $lngs = array_column($coords, 0);
                    $lats = array_column($coords, 1);

                    $batch[] = [
                        'pref_id'   => $prefId,
                        'mesh_id'   => (int) $props['MESH_ID'],
                        'shicode'   => (int) $props['SHICODE'],
                        'ptn_2020'  => (float) $props['PTN_2020'],
                        'min_lng'   => round(min($lngs), 3),
                        'min_lat'   => round(min($lats), 3),
                        'max_lng'   => round(max($lngs), 3),
                        'max_lat'   => round(max($lats), 3),
                    ];

                    // 1000件ごとにまとめて insert
                    if (count($batch) >= 1000) {
                        DB::table('mesh_1km_2020_japan_maps')->insert($batch);
                        $batch = [];
                    }
                }

                if (!empty($batch)) {
                    DB::table('mesh_1km_2020_japan_maps')->insert($batch);
                }
            }
        });

        // トランザクションの外でインデックス再有効化
        DB::statement('ALTER TABLE mesh_1km_2020_japan_maps ENABLE KEYS');
    }

}
