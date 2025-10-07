<?php

namespace Database\Seeders;

use App\Models\Keyword;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
        $rows = [];

        while (($data = fgetcsv($file)) !== false) {
            if (count($data) < 2) {
                continue;
            }

            $id   = (int) $data[0];
            $word = $data[1];

            $rows[$id] = [
                'id'      => $id,
                'word'    => $word,
                'history' => $id === 1 ? 1 : null,
            ];
        }
        fclose($file);

        DB::transaction(function () use ($rows) {
            // 既存IDを一括取得
            $existingIds = Keyword::whereIn('id', array_keys($rows))
                ->pluck('id')
                ->all();

            $existingIds = array_flip($existingIds);

            $toInsert = [];
            $toUpdate = [];

            foreach ($rows as $id => $row) {
                if (isset($existingIds[$id])) {
                    $toUpdate[] = $row;
                } else {
                    $toInsert[] = $row;
                }
            }

            // 新規レコードをバルクインサート
            if (!empty($toInsert)) {
                Keyword::insert($toInsert);
            }

            // 既存レコードをバルクアップデート
            if (!empty($toUpdate)) {
                foreach (array_chunk($toUpdate, 1000) as $chunk) {
                    foreach ($chunk as $row) {
                        Keyword::where('id', $row['id'])->update([
                            'word'    => $row['word'],
                            'history' => $row['history'],
                        ]);
                    }
                }
            }
        });
    }
    // public function run(): void
    // {
    //     $path = database_path('seeders/data/keywords.csv');

    //     if (!file_exists($path)) {
    //         throw new \Exception("CSV file not found: {$path}");
    //     }

    //     $file = fopen($path, 'r');

    //     // 1行ずつ読み込み
    //     while (($data = fgetcsv($file)) !== false) {
    //         if (count($data) < 2) {
    //             continue;
    //         }

    //         $id   = (int) $data[0];
    //         $word = $data[1];

    //         Keyword::updateOrCreate(
    //             ['id' => $id],
    //             [
    //                 'word'    => $word,
    //                 'history' => $id === 1 ? 1 : null, // ← id=1だけ1、それ以外はnull
    //             ]
    //         );
    //     }

    //     fclose($file);
    // }
}
