<?php

namespace App\Http\Controllers;

use App\Events\NotificationEvent;
use Illuminate\Http\Request;
use App\Models\HazardReport;
use App\Models\ActivityUserLog;
use Illuminate\Support\Facades\Validator;
use App\Events\HazardReport as HazardReportEvent;

class HazardReportController extends Controller
{
    private $hazardReport, $logActivity;

    function __construct()
    {
        $this->hazardReport = new HazardReport;
        $this->logActivity  = new ActivityUserLog;
    }

    public function getHazardReport()
    {
        $hazardReport = request()->route()->getPrefix() == "resident" ?
            $this->hazardReport->where('status', 'Verified')->get() :
            $this->hazardReport->all();

        return response()->json($hazardReport);
    }

    public function createHazardReport(Request $request)
    {
        $hazardReportValidation = Validator::make($request->all(), [
            'latitude'  => 'required',
            'longitude' => 'required',
            'type'      => 'required'
        ]);

        if ($hazardReportValidation->fails())
            return response(['status' => 'warning', 'message' =>  implode('<br>', $hazardReportValidation->errors()->all())]);

        $this->hazardReport->create([
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
            'type'      => $request->type
        ]);
        // event(new HazardReportEvent());
        // event(new NotificationEvent());

        return response()->json();
    }

    public function verifyHazardReport($reportId)
    {
        $report = $this->hazardReport->find($reportId);
        $report->update([
            'status' => 'Verified'
        ]);
        $this->logActivity->generateLog($reportId, $report->type, 'verified hazard report');
        // event(new HazardReportEvent());

        return response()->json();
    }

    public function updateHazardReport(Request $request, $reportId)
    {
        $hazardReportValidation = Validator::make($request->all(), [
            'update' => 'required'
        ]);

        if ($hazardReportValidation->fails())
            return response(['status' => 'warning', 'message' =>  $hazardReportValidation->errors()->first()]);

        $report = $this->hazardReport->find($reportId);
        $report->update([
            'update' => trim($request->update)
        ]);
        $this->logActivity->generateLog($reportId, $report->type, 'updated hazard report');
        // event(new HazardReportEvent());

        return response()->json();
    }

    public function removeHazardReport($reportId)
    {
        $report = $this->hazardReport->find($reportId);
        $report->delete();
        $this->logActivity->generateLog($reportId, $report->type, 'removed hazard report');
        // event(new HazardReportEvent());

        return response()->json();
    }
}
