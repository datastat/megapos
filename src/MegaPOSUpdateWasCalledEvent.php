<?php

namespace Datastat\MegaPOS;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MegaPOSUpdateWasCalledEvent extends Event
{
    use SerializesModels;
    public $data;

    /**
     * Create a new event instance.
     *
     */

    public function __construct($result)
    {
        $this->data = $result;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
