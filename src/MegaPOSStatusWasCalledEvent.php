<?php

namespace Datastat\MegaPOS;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MegaPOSStatusWasCalledEvent extends Event
{
    use SerializesModels;
    private $txId;

    /**
     * Create a new event instance.
     *
     */

    public function __construct($txId)
    {
        $this->txId = $txId;
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
