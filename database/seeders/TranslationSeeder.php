<?php

namespace Database\Seeders;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use DB;
use Illuminate\Database\Seeder;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        try {
            // Ensure some locales and tags exist
            if (Locale::count() === 0) {
                $this->call(LocaleSeeder::class);
            }
            if (Tag::count() === 0) {
                $this->call(TagSeeder::class);
            }

            // Create 100 tags
            $tags = Tag::pluck('id');

            $batchSize = 1000;
            $totalRecords = 100000;
            foreach ($this->generateData($batchSize, $totalRecords) as $key => $batch) {
                $start = microtime(true);
                Translation::insert($batch);

                // Attach tags to translations via pivot table
                $pivotData = [];
                $startTranslationId = count($batch) * $key + 1;
                $endTranslationId = count($batch) * ($key + 1);
                for ($i = $startTranslationId; $i <= $endTranslationId; $i++) {
                    foreach ($tags as $tagId) {
                        $pivotData[] = [
                            'tag_id' => $tagId,
                            'translation_id' => $i,
                        ];
                    }
                }
                // Bulk insert into the pivot table
                if (! empty($pivotData)) {
                    DB::table('tag_translation')->insert($pivotData);
                }

                $end = microtime(true);
                $executionTime = round($end - $start, 4);
                echo (count($batch) * ($key + 1))." records inserted successfully in {$executionTime} seconds\n";
            }
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }

    public function generateData(int $batchSize, int $totalRecords)
    {
        $data = [];

        for ($i = 0; $i < $totalRecords; $i++) {
            $data[] = Translation::factory()->make()->toArray();

            if (count($data) >= $batchSize) {
                yield $data;
                $data = [];
            }
        }

        if (! empty($data)) {
            yield $data;
        }
    }
}
