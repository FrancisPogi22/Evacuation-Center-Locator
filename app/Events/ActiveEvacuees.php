<?php

namespace App\Events;

use App\Models\Disaster;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActiveEvacuees implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $activeEvacuees;

    public function __construct()
    {
        $this->activeEvacuees = Disaster::join('evacuee', 'evacuee.disaster_id', '=', 'disaster.id')->where('evacuee.status', 'Evacuated')->sum('evacuee.individuals');
    }

    public function broadcastOn()
    {
        return new Channel('active-evacuees');
    }
}
