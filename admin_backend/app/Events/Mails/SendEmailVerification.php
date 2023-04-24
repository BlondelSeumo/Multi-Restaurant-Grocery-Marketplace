<?php

namespace App\Events\Mails;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendEmailVerification
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public ?User $user;

    /**
     * Create a new event instance.
     *
     * @param User|null $user
     */
    public function __construct(?User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('channel-name');
    }
}
