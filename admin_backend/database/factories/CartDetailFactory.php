<?php

namespace Database\Factories;

use App\Models\Stock;
use App\Models\UserCart;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CartDetailFactory extends Factory
{
	public function definition(): array
	{
		return [
			'quantity' => $this->faker->randomNumber(),
			'price' => $this->faker->randomFloat(),
			'discount' => $this->faker->randomFloat(),
			'bonus' => $this->faker->randomNumber(),
			'created_at' => Carbon::now(),
			'updated_at' => Carbon::now(),
			'rate_price' => $this->faker->randomFloat(),

			'stock_id' => Stock::factory(),
			'user_cart_id' => UserCart::factory(),
		];
	}
}
