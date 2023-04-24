<?php

namespace Database\Factories;

use App\Models\ExtraGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExtraValueFactory extends Factory
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
            'value' => $this->faker->colorName,
            'active' => 1,
        ];
    }
}
