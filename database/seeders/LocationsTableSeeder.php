<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/locations.csv');

        if (!file_exists($path)) {
            throw new \Exception("CSV file not found: {$path}");
        }

        $file = fopen($path, 'r');

        // ヘッダー行あり
        $isHeader = true;

        // 1行ずつ読み込み
        while (($data = fgetcsv($file)) !== false) {
            // 空行や不完全な行をスキップ
            if (count($data) < 5) {
                continue;
            }

            // 1行目（ヘッダー）はスキップ
            if ($isHeader) {
                $isHeader = false;
                continue;
            }

            // CSV: name, country, city, lat, lng
            Location::create([
                'name'    => $data[0],
                'country' => $data[1],
                'city'    => $data[2],
                'lat'     => $data[3],
                'lng'     => $data[4],
            ]);

        }

        fclose($file);
    }
}
