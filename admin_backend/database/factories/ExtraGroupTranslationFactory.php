<?php

namespace Database\Factories;

use App\Models\ExtraGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExtraGroupTranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'extra_group_id' => ExtraGroup::factory(),
            'locale' => 'en',
            'title' => $this->faker->firstName,
        ];
    }
}
