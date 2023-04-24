<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class OrderDetailFactory extends Factory
{
    public function definition(): array
    {
        return [
            'origin_price'  => $this->faker->randomFloat(),
            'total_price'   => $this->faker->randomFloat(),
            'tax'           => $this->faker->randomFloat(),
            'discount'      => $this->faker->randomFloat(),
            'rate_discount' => $this->faker->randomFloat(),
            'quantity'      => $this->faker->randomNumber(),
            'created_at'    => Carbon::now(),
            'updated_at'    => Carbon::now(),
            'order_id'      => Order::factory(),
            'stock_id'      => Stock::factory(),
        ];
    }
}
