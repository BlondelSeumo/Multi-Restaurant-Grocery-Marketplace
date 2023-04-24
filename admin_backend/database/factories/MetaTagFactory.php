<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class MetaTagFactory extends Factory
{
    public function definition(): array
    {
        return [
            'path' => $this->faker->word(),
            'model_id' => $this->faker->randomNumber(),
            'model_type' => $this->faker->word(),
            'title' => $this->faker->word(),
            'keywords' => $this->faker->word(),
            'description' => $this->faker->text(),
            'h1' => $this->faker->word(),
            'seo_text' => $this->faker->text(),
            'canonical' => $this->faker->word(),
            'robots' => $this->faker->word(),
            'change_freq' => $this->faker->word(),
            'priority' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
