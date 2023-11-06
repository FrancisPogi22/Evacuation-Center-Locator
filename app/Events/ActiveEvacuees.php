<?php

namespace App\Events;

use App\Models\Disaster;
use App\Models\Evacuee;
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
        $onGoingDisaster = Disaster::where('status', "On Going")->get();
        $totalEvacuee    = 0;

        foreach ($onGoingDisaster as $disaster) {
            $totalEvacueeCount = Evacuee::where('disaster_id', $disaster->id)->sum('individuals');
            $totalEvacuee      += $totalEvacueeCount;
        }

        $this->activeEvacuees = $totalEvacuee;
    }

    public function broadcastOn()
    {
        return new Channel('active-evacuees');
    }
}
