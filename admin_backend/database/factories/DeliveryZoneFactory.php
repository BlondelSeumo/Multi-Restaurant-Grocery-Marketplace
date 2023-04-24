<?php

namespace Database\Factories;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DeliveryZoneFactory extends Factory
{
    public function definition(): array
    {
        return [
            'address'       => [],
            'created_at'    => Carbon::now(),
            'updated_at'    => Carbon::now(),
            'shop_id'       => Shop::factory(),
        ];
    }
}
