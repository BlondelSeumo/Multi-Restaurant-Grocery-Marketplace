<?php

namespace App\Services\SMSGatewayService;

use App\Models\Settings;
use App\Models\SmsGateway;
use App\Models\SmsPayload;
use App\Services\CoreService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SMSBaseService extends CoreService
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return SmsGateway::class;
    }

    /**
     * @param $phone
     * @return array
     */
    public function smsGateway($phone): array
    {
        $otp = $this->setOTP();

        $smsPayload = SmsPayload::where('default', 1)->first();

        $result = ['status' => false];

        if ($smsPayload?->type === SmsPayload::FIREBASE) {

            $result = (new TwilioService)->sendSms($phone, $otp, $smsPayload);

        } else if ($smsPayload?->type === SmsPayload::TWILIO) {

            $result = (new TwilioService)->sendSms($phone, $otp, $smsPayload);

        }

        if (data_get($result, 'status')) {

            $this->setOTPToCache($phone, $otp);

            return [
                'status' => true,
                'verifyId' => data_get($otp, 'verifyId'),
                'phone' => Str::mask($phone, '*', -12, 8),
                'message' => data_get($result, 'message', ''),
            ];
        }

        return ['status' => false, 'message' => data_get($result, 'message')];
    }


    public function setOTP(): array
    {
        return ['verifyId' => Str::uuid(), 'otpCode' => rand(100000, 999999)];
    }

    public function setOTPToCache($phone, $otp)
    {
        $verifyId  = data_get($otp, 'verifyId');
        $expiredAt = Settings::adminSettings()->where('key', 'otp_expire_time')->first()?->value;

        Cache::put('sms-' . $verifyId, [
            'phone'     => $phone,
            'verifyId'  => $verifyId,
            'OTPCode'   => data_get($otp, 'otpCode'),
            'expiredAt' => now()->addMinutes($expiredAt >= 1 ? $expiredAt : 10),
        ], 1800);

        Cache::put($verifyId, 3, 300);
    }
}
