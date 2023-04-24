<?php

namespace Database\Factories;

use App\Models\Currency;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'delivery_type'         => $this->faker->word(),
            'total_price'           => $this->faker->randomFloat(),
            'rate'                  => $this->faker->randomNumber(),
            'note'                  => $this->faker->word(),
            'tax'                   => $this->faker->randomFloat(),
            'commission_fee'        => $this->faker->randomFloat(),
            'rate_commission_fee'   => $this->faker->randomFloat(),
            'status'                => $this->faker->word(),
            'location'              => $this->faker->words(),
            'address'               => $this->faker->address(),
            'delivery_fee'          => $this->faker->randomFloat(),
            'delivery_date'         => $this->faker->word(),
            'delivery_time'         => $this->faker->word(),
            'total_discount'        => $this->faker->randomNumber(),
            'created_at'            => Carbon::now(),
            'updated_at'            => Carbon::now(),
            'user_id'               => User::factory(),
            'currency_id'           => Currency::factory(),
            'shop_id'               => Shop::factory(),
        ];
    }
}
