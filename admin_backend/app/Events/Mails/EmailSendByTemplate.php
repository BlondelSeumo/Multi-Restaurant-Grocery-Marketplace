<?php

namespace App\Events\Mails;

use App\Models\EmailTemplate;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmailSendByTemplate
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public ?EmailTemplate $emailTemplate;

    /**
     * Create a new event instance.
     *
     * @param EmailTemplate|null $emailTemplate
     */
    public function __construct(?EmailTemplate $emailTemplate)
    {
        $this->emailTemplate = $emailTemplate;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('channel-name');
    }
}
