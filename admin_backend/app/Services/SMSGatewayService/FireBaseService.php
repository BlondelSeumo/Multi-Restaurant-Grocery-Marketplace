<?php

namespace App\Services\SMSGatewayService;

use App\Helpers\ResponseError;
use App\Models\SmsPayload;
use App\Services\CoreService;
use Exception;
use Twilio\Rest\Client;

class FireBaseService extends CoreService
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

            if (in_array($phone, [112, 999, 911, 933])) {
                throw new Exception(__('errors.' . ResponseError::ERROR_115, locale: $this->language), 400);
            }

            $client = new Client($accountId, $authToken);
            $client->messages->create($phone, [
                'from' => $twilioNumber,
                'body' => __('errors.' . ResponseError::CONFIRMATION_CODE, ['code' => $otpCode], $this->language)
            ]);

            return [
                'status'  => true,
                'message' => __('errors.' . ResponseError::SUCCESS, locale: $this->language)
            ];

        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

}
