<?php

namespace App\Services\SMSGatewayService;

use App\Models\SmsGateway;
use Illuminate\Support\Str;

class NexmoService extends \App\Services\CoreService
{

    protected function getModelClass()
    {
        return SmsGateway::class;
    }

    public function sendSms($gateway, $phone, $otp)
    {
        if (!isset($gateway->api_key) || !isset($gateway->secret_key)) {
            return ['status' => false, 'message' => 'Bad credentials. Contact with Support Team'];
        }

        $basic  = new \Vonage\Client\Credentials\Basic($gateway->api_key, $gateway->secret_key);
        $client = new \Vonage\Client($basic);
        $text = Str::replace('#OTP#', $otp['otpCode'], $gateway->text);

        try {
            $response = $client->sms()->send(
                new \Vonage\SMS\Message\SMS($phone, $gateway->from, $text)
            );
            $message = $response->current();
            if ($message->getStatus() == 0) {
                return ['status' => true];
            } else {
                return ['status' => false, 'message' => $message->getStatus()];
            }
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
}
