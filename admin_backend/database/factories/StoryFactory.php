<?php

namespace Database\Factories;

use App\Models\Shop;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class StoryFactory extends Factory
{
	public function definition(): array
	{
		return [
			'file_urls' => $this->faker->words(),
			'active' => $this->faker->boolean(),
			'created_at' => Carbon::now(),
			'updated_at' => Carbon::now(),

			'stock_id' => Stock::factory(),
			'shop_id' => Shop::factory(),
		];
	}
}
