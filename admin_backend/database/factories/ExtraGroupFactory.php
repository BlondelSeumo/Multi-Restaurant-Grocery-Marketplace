<?php

namespace Database\Factories;

use App\Models\ExtraGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExtraGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => ExtraGroup::TYPES[rand(0,2)],
            'active' => 1,
        ];
    }
}
