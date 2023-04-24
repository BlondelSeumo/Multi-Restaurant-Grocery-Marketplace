<?php

namespace Database\Factories;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShopTranslation>
 */
class ShopTranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'shop_id' => Shop::inRandomOrder()->first(),
            'locale' => 'en',
            'title' => $this->faker->firstName,
            'description' => $this->faker->text(255),
            'address' => $this->faker->address(),
        ];
    }
}
