<?php

namespace Database\Seeders;

use App\Models\SmsGateway;
use Illuminate\Database\Seeder;

class SmsGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $smsGetaways = [
            [
                'id' => 1,
                'title' => 'Nexmo',
                'type' => 'nexmo',
                'from' => 'go-shop',
                'api_key' => null,
                'secret_key' => null,
                'text' => 'Your verify code: #OTP#. Valid for 5 minutes',
                'active' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'title' => 'Twilio',
                'type' => 'twilio',
                'from' => '',
                'api_key' => null,
                'secret_key' => null,
                'text' => 'Your verify code: #OTP#. Valid for 5 minutes',
                'active' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($smsGetaways as $sms){
            SmsGateway::updateOrInsert(['id' => $sms['id']], $sms);
        }
    }
}
