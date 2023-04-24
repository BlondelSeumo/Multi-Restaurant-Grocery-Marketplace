<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PointFactory extends Factory
{
    public function definition(): array
    {
        return [
            'shop_id' => $this->faker->randomNumber(),
            'type' => $this->faker->word(),
            'price' => $this->faker->word(),
            'value' => $this->faker->word(),
            'active' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
