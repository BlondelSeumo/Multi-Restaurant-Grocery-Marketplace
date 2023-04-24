<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shop>
 */
class ShopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid(),
            'user_id' => User::inRandomOrder()->first(),
            'tax' => rand(1, 15),
            'percentage' => rand(1, 10),
            'location' => ["latitude" => "-69.12345","longitude" => "21.10121"],
            'phone' => 998331901212,
            'show_type' => null,
            'open' => true,
            'visibility' => true,
            'min_amount' => rand(1,1000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
