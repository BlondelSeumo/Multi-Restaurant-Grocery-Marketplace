<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class NotificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => $this->faker->word(),
            'payload' => $this->faker->words(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
