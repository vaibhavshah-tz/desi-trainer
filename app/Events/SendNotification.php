<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendNotification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $model;
    public $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($model, $data)
    {
        $this->model = $model;
        $this->data = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [];
    }
}
