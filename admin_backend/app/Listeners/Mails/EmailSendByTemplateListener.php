<?php

namespace App\Listeners\Mails;

use App\Events\Mails\EmailSendByTemplate as Event;
use App\Services\EmailSettingService\EmailSendService;
use App\Traits\Loggable;
use Exception;

class EmailSendByTemplateListener
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
     * @param Event $event
     * @return void
     */
    public function handle(Event $event): void
    {
        try {
            if (!empty($event->emailTemplate)) {

                $event->emailTemplate->update(['status' => 1]);

                (new EmailSendService)->sendSubscriptions($event->emailTemplate);
            }

        } catch (Exception $e) {
            $this->error($e);
        }
    }
}
