<?php

namespace App\Events;

use App\Models\ResidentReport;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IncidentReport implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $todayReport, $resolvingReport, $resolvedReport;

    public function __construct()
    {
        $residentReport = new ResidentReport;
        $reports = $residentReport->getReportCount();
        $this->todayReport = $reports['todayReport'];
        $this->resolvingReport = $reports['resolvingReport'];
        $this->resolvedReport = $reports['resolvedReport'];
    }


    public function broadcastOn()
    {
        return new Channel('incident-report');
    }
}
