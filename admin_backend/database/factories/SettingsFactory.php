<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SettingsFactory extends Factory
{
	public function definition(): array
	{
		return [
			'key'           => $this->faker->word(),
			'value'         => $this->faker->word(),
			'created_at'    => Carbon::now(),
			'updated_at'    => Carbon::now(),
		];
	}
}
