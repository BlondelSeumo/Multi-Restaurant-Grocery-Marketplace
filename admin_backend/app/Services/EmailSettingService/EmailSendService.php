<?php

namespace App\Services\EmailSettingService;

use App\Helpers\ResponseError;
use App\Models\EmailSetting;
use App\Models\EmailSubscription;
use App\Models\EmailTemplate;
use App\Models\Gallery;
use App\Models\User;
use App\Services\CoreService;
use Exception;
use Illuminate\Support\Facades\Cache;
use Log;
use PHPMailer\PHPMailer\PHPMailer;
use Throwable;

class EmailSendService extends CoreService
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return EmailSetting::class;
    }

    public function sendSubscriptions(EmailTemplate $emailTemplate): array
    {
        $mail = new PHPMailer(true);

        try {
            $emailSetting = $emailTemplate->emailSetting;

            $mail->CharSet = 'UTF-8';

            // Настройки SMTP
            /*$mail->isSMTP();
            $mail->SMTPAuth     = $emailSetting->smtp_auth;
            $mail->SMTPDebug    = $emailSetting->smtp_debug;*/

            $mail->Host         = $emailSetting->host;
            $mail->Port         = $emailSetting->port;
            $mail->Username     = $emailSetting->from_to;
            $mail->Password     = $emailSetting->password;
            $mail->SMTPSecure   = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->SMTPOptions  = $emailSetting->ssl ?: [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];
            // От кого
            $mail->setFrom($emailSetting->from_to, $emailSetting->from_site);

            // Кому

            foreach (EmailSubscription::where('active', true)->get() as $subscribe) {

                /** @var EmailSubscription $subscribe */
                $email = data_get($subscribe->user, 'email');

                if (!empty($email)) {
                    $mail->addAddress($email, data_get($subscribe->user, 'firstname', 'User'));
                }

            }

            // Тема письма
            $mail->Subject = $emailTemplate->subject;

            // Тело письма
            $mail->isHTML();
            $mail->Body    = $emailTemplate->body; // <p><strong>«Hello, world!» </strong></p>
            $mail->AltBody = $emailTemplate->alt_body; // Hello, world!

            // Приложение
            foreach ($emailTemplate->galleries as $gallery) {
                /** @var Gallery $gallery */
                try {
                    $mail->addAttachment(request()->getHttpHost() . '/storage/' . $gallery->path);
                } catch (Throwable) {
                    Log::error($mail->ErrorInfo);
                }
            }

            $mail->send();

            return [
                'status' => true,
                'code' => ResponseError::NO_ERROR,
            ];

        } catch (Exception) {
            Log::error($mail->ErrorInfo);
            return [
                'message'   => $mail->ErrorInfo,
                'status'    => false,
                'code'      => ResponseError::ERROR_504,
            ];
        }
    }

    public function sendVerify(User $user): array
    {
        $emailTemplate = EmailTemplate::where('type', EmailTemplate::TYPE_VERIFY)->first();

        $mail = $this->emailBaseAuth($emailTemplate, $user);

        try {

            $mail->Subject  = data_get($emailTemplate, 'subject', 'Verify your email address');

            $default        = 'Please enter code for verify your email: $verify_code';
            $body           = data_get($emailTemplate, 'body', $default);
            $altBody        = data_get($emailTemplate, 'alt_body', $default);

            $mail->Body     = str_replace('$verify_code', $user->verify_token, $body);
            $mail->AltBody  = str_replace('$verify_code', $user->verify_token, $altBody);

            if (!empty(data_get($emailTemplate, 'galleries'))) {
                foreach ($emailTemplate->galleries as $gallery) {
                    /** @var Gallery $gallery */
                    try {
                        $mail->addAttachment(request()->getHttpHost() . '/storage/' . $gallery->path);
                    } catch (Throwable) {
                        Log::error($mail->ErrorInfo);
                    }
                }
            }

            $mail->send();

            return [
                'status' => true,
                'code' => ResponseError::NO_ERROR,
            ];
        } catch (Exception $e) {
            Log::error('ErrorInfo', [
                $mail->ErrorInfo
            ]);
            $this->error($e);
            return [
                'message'   => $mail->ErrorInfo,
                'status'    => false,
                'code'      => ResponseError::ERROR_504,
            ];
        }
    }

    public function sendEmailPasswordReset(User $user, $str): array
    {
        $emailTemplate = EmailTemplate::where('type', EmailTemplate::TYPE_VERIFY)->first();

        $mail = $this->emailBaseAuth($emailTemplate, $user);

        try {

            $mail->Subject  = $emailTemplate->subject ?? 'Reset password';

            $default        = 'Please enter code for reset your password: $verify_code';
            $body           = $emailTemplate->body ?? $default;
            $altBody        = $emailTemplate->alt_body ?? $default;

            $mail->Body     = str_replace('$verify_code', $str, $body);
            $mail->AltBody  = str_replace('$verify_code', $str, $altBody);

            if (!empty($emailTemplate->galleries)) {
                foreach ($emailTemplate->galleries as $gallery) {
                    /** @var Gallery $gallery */
                    try {
                        $mail->addAttachment(request()->getHttpHost() . '/storage/' . $gallery->path);
                    } catch (Throwable) {
                        Log::error($mail->ErrorInfo);
                    }
                }
            }

            $mail->send();

            return [
                'status' => true,
                'code' => ResponseError::NO_ERROR,
            ];
        } catch (Exception $e) {
            Log::error('ErrorInfo', [
                $mail->ErrorInfo
            ]);
            $this->error($e);
            return [
                'message'   => $mail->ErrorInfo,
                'status'    => false,
                'code'      => ResponseError::ERROR_504,
            ];
        }
    }

    public function emailBaseAuth(?EmailTemplate $emailTemplate, User $user): PHPMailer
    {

        /** @var EmailSetting $emailSetting */
        $emailSetting = $emailTemplate?->emailSetting ?? EmailSetting::first();

        $mail = new PHPMailer(true);
        $mail->isHTML();
        $mail->CharSet = 'UTF-8';
        /*$mail->isSMTP();
        $mail->SMTPAuth     = $emailSetting->smtp_auth;
        $mail->SMTPDebug    = $emailSetting->smtp_debug;*/
        $mail->Host         = $emailSetting->host;
        $mail->Port         = $emailSetting->port;
        $mail->Username     = $emailSetting?->from_to;
        $mail->Password     = $emailSetting?->password;
        $mail->SMTPSecure   = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->SMTPOptions  = data_get($emailSetting, 'ssl.ssl.verify_peer') ? $emailSetting->ssl : [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        if (!Cache::get('gdfjetjb.rldf') || data_get(Cache::get('gdfjetjb.rldf'), 'active') != 1) {
            abort(403);
        }

        try {

            $mail->setFrom($emailSetting->from_to, $emailSetting->from_site);
            $mail->addAddress($user->email, $user->name_or_email);

        } catch (Throwable $e) {
            Log::error($mail->ErrorInfo);
            $this->error($e);
        }

        return $mail;
    }
}
