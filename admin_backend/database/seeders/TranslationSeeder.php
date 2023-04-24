<?php

namespace Database\Seeders;

use App\Models\Translation;
use DB;
use Illuminate\Database\Seeder;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
//        $data = Lang::get('errors');
//
//        foreach ($data as $index => $item){
//            Translation::updateOrInsert(['key' => $index], [
//                'status' => true,
//                'locale' => 'en',
//                'group' => 'errors',
//                'value' => $item,
//                'created_at' => now(),
//                'updated_at' => now(),
//            ]);
//        }

        $filePath = resource_path('lang/translations_en.sql');

        if (file_exists($filePath)) {
            Translation::truncate();
            DB::unprepared(file_get_contents($filePath));
        }
    }
}
