<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class OrderStatusFactory extends Factory
{
	public function definition(): array
	{
		return [
			'name'          => $this->faker->name(),
			'active'        => $this->faker->boolean(),
			'sort'          => $this->faker->randomNumber(),
			'created_at'    => Carbon::now(),
			'updated_at'    => Carbon::now(),
		];
	}
}
