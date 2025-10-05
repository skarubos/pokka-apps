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
        $path = database_path('seeders\data\prefectures.csv');

        if (!file_exists($path)) {
            $this->command->error("CSV file not found: {$path}");
            return;
        }

        $file = fopen($path, 'r');

        while (($row = fgetcsv($file)) !== false) {
            // CSVの列順: id, name, size
            DB::table('prefectures')->insert([
                'id'   => (int) $row[0],
                'name' => $row[1],
                'size' => (int) $row[2],
            ]);
        }

        fclose($file);
    }
}
