<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Shop;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ProductAddonFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid(),
            'keywords' => $this->faker->word(),
            'img' => $this->faker->word(),
            'min_qty' => $this->faker->randomNumber(),
            'max_qty' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'qr_code' => $this->faker->word(),
            'bar_code' => $this->faker->word(),
            'tax' => $this->faker->randomFloat(),
            'active' => $this->faker->boolean(),
            'addon' => $this->faker->boolean(),
            'status' => $this->faker->word(),

            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'unit_id' => Unit::factory(),
            'shop_id' => Shop::factory(),
        ];
    }
}
