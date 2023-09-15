<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HazardReport;
use App\Models\ActivityUserLog;
use App\Events\HazardReport as HazardReportEvent;
use Illuminate\Support\Facades\Validator;

class HazardReportController extends Controller
{
    private $hazardReport, $logActivity;

    function __construct()
    {
        $this->hazardReport = new HazardReport;
        $this->logActivity = new ActivityUserLog;
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
            'latitude' => 'required',
            'longitude' => 'required',
            'type' => 'required'
        ]);

        if ($hazardReportValidation->fails())
            return response(['status' => 'warning', 'message' =>  implode('<br>', $hazardReportValidation->errors()->all())]);

        $this->hazardReport->create([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'type' => $request->type
        ]);

        // event(new HazardReportEvent());
        return response()->json();
    }

    public function verifyHazardReport($reportId)
    {
        $this->hazardReport->find($reportId)->update([
            'status' => 'Verified'
        ]);

        $this->logActivity->generateLog($reportId, 'Verified Hazard Report');

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

        $this->hazardReport->find($reportId)->update([
            'update' => trim($request->update)
        ]);

        $this->logActivity->generateLog($reportId, 'Updated Hazard Report');

        // event(new HazardReportEvent());
        return response()->json();
    }

    public function removeHazardReport($reportId)
    {
        $this->hazardReport->find($reportId)->delete();

        $this->logActivity->generateLog($reportId, 'Removed Hazard Report');

        // event(new HazardReportEvent());
        return response()->json();
    }
}
