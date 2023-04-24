<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $data =  [
            [
                'id' => 97,
                'title' => 'title1',
                'type' => 'orders',
                'price' => 100.00,
                'product_limit' => 1000,
                'order_limit'   => 1000,
                'with_report'   => 0,
                'month' => 1,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 98,
                'title' => 'title3',
                'type' => 'orders',
                'price' => 250.00,
                'product_limit' => 3000,
                'order_limit'   => 3000,
                'with_report'   => 1,
                'month' => 3,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 99,
                'title' => 'title6',
                'type' => 'orders',
                'product_limit' => 6000,
                'order_limit'   => 6000,
                'with_report'   => 1,
                'price' => 450.00,
                'month' => 6,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 100,
                'title' => 'title12',
                'product_limit' => 12000,
                'order_limit'   => 12000,
                'with_report'   => 1,
                'type' => 'orders',
                'price' => 800.00,
                'month' => 12,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($data as $item){
            Subscription::updateOrInsert(['id' => $item['id']], $item);
        }
    }
}
