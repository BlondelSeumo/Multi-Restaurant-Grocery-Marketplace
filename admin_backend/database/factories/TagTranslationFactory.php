<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TagTranslationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tag_id'        => $this->faker->randomNumber(),
            'locale'        => $this->faker->word(),
            'title'         => $this->faker->word(),
            'description'   => $this->faker->text(),
        ];
    }
}
