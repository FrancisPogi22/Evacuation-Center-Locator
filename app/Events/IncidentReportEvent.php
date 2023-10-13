<?php

namespace App\Events;

use App\Models\IncidentReport;
use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IncidentReportEvent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $reportLog, $incidentReport, $totalReport;

    public function __construct()
    {
        $this->totalReport = IncidentReport::where('report_time', '>=', now()->format('Y-m-d H:i:s'))->count();
    }

    function approveStatus($incidentReportId)
    {
        $this->incidentReport = new IncidentReport;
        $this->incidentReport->find($incidentReportId)->update([
            'status' => 'Approved'
        ]);
    }

    function declineStatus($incidentReportId)
    {
        $this->incidentReport = new IncidentReport;
        $this->incidentReport->find($incidentReportId)->update([
            'status' => 'Declined'
        ]);
    }

    function revertIncidentReport($incidentReportId, $reportPhotoPath)
    {
        $this->incidentReport = new IncidentReport;

        if ($reportPhotoPath) {
            $image_path = public_path('reports_image/' . $reportPhotoPath);

            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        $this->incidentReport->find($incidentReportId)->delete();
    }

    function confirmDangerAreaReport($dangerAreaId)
    {
        $this->incidentReport = new IncidentReport;
        $dangerAreaReport     = $this->incidentReport->find($dangerAreaId);
        $this->incidentReport->find($dangerAreaId)->update([
            'status' => 'Confirmed'
        ]);
        $reportDescription = $dangerAreaReport->description;

        return $reportDescription;
    }

    function archiveDangerAreaReport($dangerAreaId, $operation)
    {
        $this->incidentReport = new IncidentReport;
        $dangerAreaReport     = $this->incidentReport->find($dangerAreaId);
        $dangerAreaReport->find($dangerAreaId)->update([
            'is_archive' => $operation == 'archive' ? 1 : 0
        ]);
        
        return $dangerAreaReport->description;
    }

    function revertDangerAreaReport($dangerAreaId)
    {
        $this->incidentReport = new IncidentReport;
        $this->incidentReport->find($dangerAreaId)->delete();
    }

    public function broadcastOn()
    {
        return new Channel('incident-report-event');
    }
}
