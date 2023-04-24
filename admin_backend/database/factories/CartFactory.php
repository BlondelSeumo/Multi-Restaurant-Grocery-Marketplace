<?php

namespace Database\Factories;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CartFactory extends Factory
{
	public function definition(): array
	{
		return [
			'total_price' => $this->faker->randomFloat(),
			'rate_total_price' => $this->faker->randomFloat(),
			'status' => $this->faker->randomNumber(),
			'rate' => $this->faker->randomNumber(),
			'created_at' => Carbon::now(),
			'updated_at' => Carbon::now(),

			'shop_id' => Shop::factory(),
			'owner_id' => User::factory(),
			'currency_id' => Shop::factory(),
		];
	}
}
