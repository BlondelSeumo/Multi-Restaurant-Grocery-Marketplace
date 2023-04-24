<?php

namespace App\Listeners\Mails;

use App\Events\Mails\SendEmailVerification;
use App\Models\User;
use App\Services\EmailSettingService\EmailSendService;
use App\Traits\Loggable;
use Exception;

class SendEmailVerificationListener
{
    use Loggable;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event
     * @param SendEmailVerification $event
     * @return void
     */
    public function handle(SendEmailVerification $event): void
    {
        try {

            if (!empty($event->user)) {

                $token = mb_substr(time(), -6, 6);
               
                $event->user->update(['verify_token' => $token]);

                (new EmailSendService)->sendVerify(User::find($event->user->id));

            }

        } catch (Exception $e) {
            $this->error($e);
        }
    }
}
