<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TagFactory extends Factory
{
    public function definition(): array
    {
        return [
            'active'        => $this->faker->boolean(),
            'created_at'    => Carbon::now(),
            'updated_at'    => Carbon::now(),
            'product_id'    => Product::factory(),
        ];
    }
}
