<?php

namespace Database\Factories;

use App\Models\OrderRefund;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class OrderRefundFactory extends Factory
{
    public function definition(): array
    {
        return [
            'status'        => $this->faker->randomElement(OrderRefund::STATUSES),
            'cause'         => $this->faker->word(),
            'answer'        => $this->faker->word(),
            'created_at'    => Carbon::now(),
            'updated_at'    => Carbon::now(),
        ];
    }
}
