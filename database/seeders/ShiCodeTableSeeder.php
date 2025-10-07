<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiCodeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/shicode.csv');

        if (!file_exists($path)) {
            $this->command->error("CSV file not found: {$path}");
            return;
        }

        // インデックスを一時的に無効化
        DB::statement('ALTER TABLE shi_codes DISABLE KEYS');

        DB::transaction(function () use ($path) {
            $file = fopen($path, 'r');
            $batch = [];
            $batchSize = 1000; // まとめて挿入する件数

            while (($row = fgetcsv($file)) !== false) {
                $originalCode = $row[0];
                $trimmedCode = substr($originalCode, 0, -1);

                $batch[] = [
                    'code'      => (int) $trimmedCode,
                    'pref'      => $row[1],
                    'city'      => $row[2],
                    'pref_kana' => $row[3],
                    'city_kana' => $row[4],
                ];

                if (count($batch) >= $batchSize) {
                    DB::table('shi_codes')->insert($batch);
                    $batch = [];
                }
            }

            if (!empty($batch)) {
                DB::table('shi_codes')->insert($batch);
            }

            fclose($file);
        });

        // インデックスを再有効化
        DB::statement('ALTER TABLE shi_codes ENABLE KEYS');
    }
}
