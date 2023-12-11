<?php

namespace App\Events;

use App\Models\Evacuee;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Evacuees implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $evacuated, $returnedHome;

    public function __construct()
    {
        $evacuee = new Evacuee;
        $evacueeCount = $evacuee->countEvacueesByStatus();
        $this->evacuated = $evacueeCount['evacuated'];
        $this->returnedHome = $evacueeCount['returnedHome'];
    }

    public function broadcastOn()
    {
        return new Channel('evacuees');
    }
}
