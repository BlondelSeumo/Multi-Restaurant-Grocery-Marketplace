<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = [
            [
                'id' => 1,
                'locale' => 'en',
                'title' => 'English',
                'default' => 1,
            ]
        ];

        foreach ($languages as $language){
            Language::updateOrInsert(['id' => $language['id']], $language);
        }
    }
}
