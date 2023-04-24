<?php

namespace Database\Factories;

use App\Models\ExtraGroup;
use App\Models\ExtraValue;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;

class StockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $countable = $this->faker->randomElement([
            Product::class,
        ]);

        return [
            'countable_id' => Product::factory(),
            'countable_type' => array_search($countable, Relation::$morphMap),
            'price' => rand(10, 1200),
            'quantity' => rand(10, 150),
        ];
    }
}
