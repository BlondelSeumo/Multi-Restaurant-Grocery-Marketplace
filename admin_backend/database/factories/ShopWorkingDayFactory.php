<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ShopWorkingDayFactory extends Factory
{
    public function definition(): array
    {
        return [
            'shop_id'       => $this->faker->word(),
            'day'           => $this->faker->word(),
            'from'          => '9-00',
            'to'            => '21-00',
            'disabled'      => rand(0,1),
            'created_at'    => Carbon::now(),
            'updated_at'    => Carbon::now(),
        ];
    }
}
