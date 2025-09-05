<?php

namespace Database\Seeders;

use App\Models\Keyword;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KeywordsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/keywords.csv');

        if (!file_exists($path)) {
            throw new \Exception("CSV file not found: {$path}");
        }

        $file = fopen($path, 'r');

        // 1行ずつ読み込み
        while (($data = fgetcsv($file)) !== false) {
            if (count($data) < 2) {
                continue;
            }

            $id   = (int) $data[0];
            $word = $data[1];

            Keyword::updateOrCreate(
                ['id' => $id],
                [
                    'word'    => $word,
                    'history' => $id === 1 ? 1 : null, // ← id=1だけ1、それ以外はnull
                ]
            );
        }

        fclose($file);
    }
}
