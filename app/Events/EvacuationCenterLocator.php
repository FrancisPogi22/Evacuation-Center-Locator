<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EvacuationCenterLocator implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct()
    {
    }

    public function broadcastOn()
    {
        return new Channel('evacuation-center-locator');
    }
}
