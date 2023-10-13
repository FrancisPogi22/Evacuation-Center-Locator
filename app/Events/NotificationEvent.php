<?php

namespace App\Events;

use App\Models\HazardReport;
use App\Models\IncidentReport;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notifications = [];

    public function __construct()
    {
        $this->notifications = $this->notifications();
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('notification'),
        ];
    }

    public function notifications()
    {
        $notifications             = [];
        $notifications['incident'] = IncidentReport::where('status', 'On Process')->get();
        $notifications['hazard']   = HazardReport::where('status', 'Pending')->get();

        return $notifications;
    }
}
