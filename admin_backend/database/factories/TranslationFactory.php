<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TranslationFactory extends Factory
{
	public function definition(): array
	{
		return [
			'status'        => $this->faker->randomNumber(),
			'locale'        => $this->faker->word(),
			'group'         => $this->faker->word(),
			'key'           => $this->faker->word(),
			'value'         => $this->faker->word(),
			'created_at'    => Carbon::now(),
			'updated_at'    => Carbon::now(),
		];
	}
}
