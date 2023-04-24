<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GalleryFactory extends Factory
{
	public function definition(): array
	{
		return [
			'title' => $this->faker->word(),
			'loadable_type' => $this->faker->word(),
			'loadable_id' => $this->faker->randomNumber(),
			'type' => $this->faker->word(),
			'path' => $this->faker->word(),
			'mime' => $this->faker->word(),
			'size' => $this->faker->word(),
		];
	}
}
