<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Language;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $locale = Language::languagesList()->first();

        foreach (Category::TYPES as $key => $value) {
            $category = Category::create([
                'keywords' => $key,
                'type' => $value,
            ]);

            CategoryTranslation::create([
                'category_id' => $category->id,
                'locale'      => data_get($locale, 'locale', 'en'),
                'title'       => $key
            ]);
        }
    }
}
