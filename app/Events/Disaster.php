<?php

namespace App\Events;

use App\Models\Disaster as ModelDisaster;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Disaster implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $onGoingDisaster;

    public function __construct()
    {
        $this->onGoingDisaster = ModelDisaster::where('status', 'On Going')->get();
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('disaster'),
        ];
    }
}
