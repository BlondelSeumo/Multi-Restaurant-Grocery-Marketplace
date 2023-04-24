<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class NotificationUserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'active' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'notification_id' => Notification::factory(),
            'user_id' => User::factory(),
        ];
    }
}
