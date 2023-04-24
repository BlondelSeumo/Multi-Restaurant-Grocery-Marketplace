<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SubscriptionFactory extends Factory
{
	public function definition(): array
	{
		return [
			'type' => $this->faker->word(),
			'price' => $this->faker->randomFloat(),
			'month' => $this->faker->randomNumber(),
			'active' => $this->faker->randomNumber(),
			'created_at' => Carbon::now(),
			'updated_at' => Carbon::now(),
		];
	}
}
