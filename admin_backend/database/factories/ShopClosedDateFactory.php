<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ShopClosedDateFactory extends Factory
{
    public function definition(): array
    {
        return [
                'shop_id'    => $this->faker->randomNumber(),
                'date'       => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
        ];
    }
}
