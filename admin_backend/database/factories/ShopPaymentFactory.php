<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ShopPaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'shop_id'           => $this->faker->randomNumber(),
            'status'            => $this->faker->randomNumber(),
            'client_id'         => $this->faker->word(),
            'secret_id'         => $this->faker->word(),
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
            'merchant_email'    => $this->faker->unique()->safeEmail(),
            'payment_key'       => $this->faker->word(),
            'payment_id'        => Payment::factory(),
        ];
    }
}
