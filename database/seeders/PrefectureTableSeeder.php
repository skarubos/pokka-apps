<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrefectureTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/prefectures.csv');

        if (!file_exists($path)) {
            $this->command->error("CSV file not found: {$path}");
            return;
        }

        $file = fopen($path, 'r');

        $batch = [];
        $batchSize = 1000; // まとめて挿入する件数（少数ならそのまま全部でもOK）

        DB::transaction(function () use ($file, $batchSize, &$batch) {
            while (($row = fgetcsv($file)) !== false) {
                // CSVの列順: id, name, size
                $batch[] = [
                    'id'   => (int) $row[0],
                    'name' => $row[1],
                    'size' => (int) $row[2],
                ];

                if (count($batch) >= $batchSize) {
                    DB::table('prefectures')->insert($batch);
                    $batch = [];
                }
            }

            if (!empty($batch)) {
                DB::table('prefectures')->insert($batch);
            }

            fclose($file);
        });
    }

}
