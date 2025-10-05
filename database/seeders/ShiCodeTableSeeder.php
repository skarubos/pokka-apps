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
        $path = database_path('seeders\data\shicode.csv');

        if (!file_exists($path)) {
            $this->command->error("CSV file not found: {$path}");
            return;
        }

        $file = fopen($path, 'r');

        while (($row = fgetcsv($file)) !== false) {
            // CSVの列順: id, name, size
            DB::table('shi_codes')->insert([
                'code'      => (int) $row[0],
                'pref'      => $row[1],
                'city'      => $row[2],
                'pref_kana' => $row[3],
                'city_kana' => $row[4],
            ]);
        }

        fclose($file);
    }
}
