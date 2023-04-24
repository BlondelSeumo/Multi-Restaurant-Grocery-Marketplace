<?php

namespace App\Events\Order;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttachDeliveryMan
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public ?Order $order;
    public ?string $language;

    /**
     * Create a new event instance.
     *
     * @param Order|null $order
     * @param string|null $language
     */
    public function __construct(?Order $order, ?string $language)
    {
        $this->order    = $order;
        $this->language = $language;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('channel-name');
    }
}
