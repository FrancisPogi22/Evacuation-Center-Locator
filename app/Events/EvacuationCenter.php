<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\Channel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\EvacuationCenter as ModelsEvacuationCenter;

class EvacuationCenter implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $activeEvacuation, $inactiveEvacuation, $fullEvacuation;

    public function __construct()
    {
        $evacuation = new ModelsEvacuationCenter;
        $evacuationCenter = $evacuation->getEvacuationCount();
        $this->activeEvacuation = $evacuationCenter->activeEvacuation;
        $this->inactiveEvacuation = $evacuationCenter->inactiveEvacuation;
        $this->fullEvacuation = $evacuationCenter->fullEvacuation;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('evacuation-center'),
        ];
    }
}
