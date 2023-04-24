<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductPropertiesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'product_id' => Product::inRandomOrder()->first(),
            'locale' => 'en',
            'key' => $this->faker->firstName,
            'value' => $this->faker->lastName,
        ];
    }
}
