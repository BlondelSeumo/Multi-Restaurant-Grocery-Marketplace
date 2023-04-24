<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\Order;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $notifications = [
            [
                'type'          => Notification::PUSH,
                'payload'       => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'type'          => Notification::DISCOUNT,
                'payload'       => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'type'          => Notification::ORDER_VERIFY,
                'payload'       => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'type'          => Notification::ORDER_STATUSES,
                'payload'       => [Order::STATUS_ACCEPTED, Order::STATUS_ON_A_WAY, Order::STATUS_CANCELED],
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];

        foreach ($notifications as $notification) {
            Notification::create($notification);
        }

    }
}
