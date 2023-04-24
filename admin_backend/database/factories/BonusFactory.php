<?php

namespace Database\Factories;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class BonusFactory extends Factory
{
	public function definition(): array
	{
		return [
			'bonusable_type' => $this->faker->word(),
			'bonusable_id' => $this->faker->randomNumber(),
			'bonus_quantity' => $this->faker->randomNumber(),
			'bonus_stock_id' => $this->faker->randomNumber(),
			'value' => $this->faker->randomNumber(),
			'type' => $this->faker->word(),
			'expired_at' => $this->faker->word(),
			'status' => $this->faker->randomNumber(),
			'created_at' => Carbon::now(),
			'updated_at' => Carbon::now(),

			'shop_id' => Shop::factory(),
		];
	}
}
