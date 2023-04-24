<?php

namespace Database\Factories;

use App\Models\ExtraValue;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockExtraFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'stock_id' => Stock::factory(),
            'extra_value_id' => ExtraValue::factory(),
        ];
    }
}
