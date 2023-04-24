<?php

namespace Database\Factories;

use App\Models\ExtraGroup;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductExtraFactory extends Factory
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
            'extra_group_id' => ExtraGroup::inRandomOrder()->first(),
        ];
    }
}
