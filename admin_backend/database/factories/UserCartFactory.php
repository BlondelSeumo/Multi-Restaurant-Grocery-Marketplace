<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class UserCartFactory extends Factory
{
    public function definition(): array
    {
        return [
                'status'     => $this->faker->randomNumber(),
                'name'       => $this->faker->name(),
                'uuid'       => $this->faker->uuid(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),

                'cart_id' => Cart::factory(),
                'user_id' => User::factory(),
        ];
    }
}
