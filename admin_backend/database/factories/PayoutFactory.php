<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PayoutFactory extends Factory
{
    public function definition(): array
    {
        return [
            'status'        => $this->faker->word(),
            'created_by'    => $this->faker->randomNumber(),
            'approved_by'   => $this->faker->randomNumber(),
            'currency_id'   => $this->faker->randomNumber(),
            'cause'         => $this->faker->word(),
            'answer'        => $this->faker->word(),
            'price'         => $this->faker->randomFloat(),
            'created_at'    => Carbon::now(),
            'updated_at'    => Carbon::now(),
        ];
    }
}
