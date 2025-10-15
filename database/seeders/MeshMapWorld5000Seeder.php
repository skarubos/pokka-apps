<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MeshMapWorld5000Seeder extends Seeder
{
    public function run(): void
    {
        // インデックス無効化
        DB::statement('ALTER TABLE mesh_1km_2020_world_5000to10000_maps DISABLE KEYS');

        DB::transaction(function () {
            $path = database_path('seeders/data/world_5000to10000.csv');

            if (!file_exists($path)) {
                throw new \RuntimeException("CSV file not found: {$path}");
            }

            $handle = fopen($path, 'r');
            if ($handle === false) {
                throw new \RuntimeException("Failed to open CSV file: {$path}");
            }

            // ヘッダー行を読み飛ばす
            fgetcsv($handle);

            $batchSize = 5000; // バルクインサート件数
            $buffer = [];

            while (($row = fgetcsv($handle)) !== false) {
                // CSV列: X,Y,Z,code
                [$x, $y, $z, $code] = $row;

                $buffer[] = [
                    'country_id' => (int)$code,
                    'lat'        => (float)$y,
                    'lng'        => (float)$x,
                    'population' => (int)$z,
                ];

                if (count($buffer) >= $batchSize) {
                    DB::table('mesh_1km_2020_world_5000to10000_maps')->insert($buffer);
                    $buffer = [];
                }
            }

            // 残りを挿入
            if (!empty($buffer)) {
                DB::table('mesh_1km_2020_world_5000to10000_maps')->insert($buffer);
            }

            fclose($handle);
        });

        // インデックス再有効化
        DB::statement('ALTER TABLE mesh_1km_2020_world_5000to10000_maps ENABLE KEYS');
    }
}
