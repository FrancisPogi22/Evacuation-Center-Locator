<?php

namespace App\Events;

use App\Models\ResidentReport;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Notification implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $notifications = [];

    public function __construct()
    {
        $this->notifications = $this->notifications();
    }

    public function broadcastOn(): Channel
    {
        return new Channel('notification');
    }

    public function notifications()
    {
        return [
            'emergency' => ResidentReport::where('type', 'Emergency')->where('status', 'Pending')->get(),
            'incident' => ResidentReport::where('type', 'Incident')->where('status', 'Pending')->get(),
            'area' => ResidentReport::where('status', 'Pending')->whereIn('type', ['Flooded', 'Roadblocked'])
                ->get(),
        ];
    }
}
