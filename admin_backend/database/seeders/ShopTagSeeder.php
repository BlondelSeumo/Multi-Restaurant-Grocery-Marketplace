<?php

namespace Database\Seeders;

use App\Models\ShopTagTranslation;
use App\Models\Language;
use App\Models\ShopTag;
use Illuminate\Database\Seeder;

class ShopTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $locale = Language::languagesList()->first();

        $shopTag = ShopTag::create();

        ShopTagTranslation::create([
            'shop_tag_id' => $shopTag->id,
            'locale'      => data_get($locale, 'locale', 'en'),
            'title'       => 'Halal'
        ]);
    }
}
