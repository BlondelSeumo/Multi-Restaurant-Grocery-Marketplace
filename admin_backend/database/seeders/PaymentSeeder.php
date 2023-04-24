<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $payments = [
            ['tag' => 'stripe', 'input' => 1],
            ['tag' => 'razorpay'],
            ['tag' => 'mercado-pago'],
            ['tag' => 'paystack'],
            ['tag' => 'flutterWave'],
            ['tag' => 'paytabs'],
        ];

        foreach ($payments as $payment) {
            Payment::updateOrCreate([
                'tag' => data_get($payment, 'tag')
            ], $payment);
        }

    }

}
