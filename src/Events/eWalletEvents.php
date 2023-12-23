<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class eWalletEvents
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $webhook_data;

    /**
     * Create a new event instance.
     */
    public function __construct($webhook_data)
    {
        $this->webhook_data = $webhook_data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
