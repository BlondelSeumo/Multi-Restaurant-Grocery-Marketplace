<?php

namespace App\Services\SMSGatewayService;

use App\Models\SmsPayload;
use App\Services\CoreService;
use Exception;
use Twilio\Rest\Client;

class TwilioService extends CoreService
{
    protected function getModelClass(): string
    {
        return SmsPayload::class;
    }

    /**
     * @param $phone
     * @param $otp
     * @param SmsPayload $smsPayload
     * @return array|bool[]
     */
    public function sendSms($phone, $otp, SmsPayload $smsPayload): array
    {
        try {
            $accountId      = data_get($smsPayload->payload, 'twilio_account_id');
            $authToken      = data_get($smsPayload->payload, 'twilio_auth_token');
            $otpCode        = data_get($otp, 'otpCode');
            $twilioNumber   = data_get($smsPayload->payload, 'twilio_number');

            if (strlen($phone) < 7) {
                throw new Exception('Invalid phone number', 400);
            }

            $client = new Client($accountId, $authToken);
            $client->messages->create($phone, [
                'from' => $twilioNumber,
                'body' => "Confirmation code $otpCode"
            ]);

            return ['status' => true, 'message' => 'success'];

        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

}
