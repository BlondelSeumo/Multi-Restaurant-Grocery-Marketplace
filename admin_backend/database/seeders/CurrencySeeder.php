<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = [
            [
                'id' => 2,
                'symbol' => '$',
                'title' => 'Dollar',
                'rate' => 1.0,
                'default' => 1,
                'active' => 1,
            ]
        ];

        foreach ($currencies as $currency){
            Currency::updateOrInsert(['id' => $currency['id']], $currency);
        }

    }
}
