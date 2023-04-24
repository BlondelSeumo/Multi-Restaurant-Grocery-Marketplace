<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryManSettingFactory extends Factory
{
	public function definition(): array
	{
		return [
			'user_id' => $this->faker->randomNumber(),
			'type_of_technique' => $this->faker->word(),
			'brand' => $this->faker->word(),
			'model' => $this->faker->word(),
			'number' => $this->faker->word(),
			'color' => $this->faker->word(),
			'online' => $this->faker->boolean(),
			'location' => $this->faker->words(),
		];
	}
}
