<?php

namespace App\Http\Controllers;

use App\Models\ReportLog;
use Illuminate\Http\Request;
use App\Models\ResidentReport;
use App\Models\ActivityUserLog;
use Yajra\DataTables\DataTables;
use App\Events\Notification;
use App\Events\EmergencyReport;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\ResidentReportController;

class EmergencyReportController extends Controller
{
    private $reportLog, $logActivity, $emergencyReport, $residentReport;

    function __construct()
    {
        $this->reportLog       = new ReportLog;
        $this->emergencyReport = new ResidentReport;
        $this->logActivity     = new ActivityUserLog;
        $this->residentReport  = new ResidentReportController;
    }

    public function getEmergencyReport($operation, $year, $type)
    {
        $emergencyReport = $this->emergencyReport->where('is_archive', $operation == "manage" ? 0 : 1);

        if ($operation == "manage")
            return response($emergencyReport->where('type', 'Emergency')->get());
        else
            return DataTables::of(
                $emergencyReport->where('type', $type)->whereYear('report_time', $year)->get()
            )
                ->addColumn('location', '<button class="btn-table-primary viewLocationBtn"><i class="bi bi-pin-map"></i>View</button>')
                ->addColumn('photo', function ($report) {
                    return '<div class="photo-container">
                                <div class="image-wrapper">
                                    <img class="report-img" src="' . asset('reports_image/' . $report->photo) . '">
                                    <div class="image-overlay">
                                        <div class="overlay-text">View Photo</div>
                                    </div>
                                </div>
                            </div>';
                })
                ->rawColumns(['location', 'photo'])
                ->make(true);
    }

    public function createEmergencyReport(Request $request)
    {
        $emergencyReportValidation = Validator::make($request->all(), [
            'latitude'  => 'required',
            'longitude' => 'required',
            'details'   => 'required',
            'image'     => 'required|image|mimes:jpeg,png,jpg'
        ]);

        if ($emergencyReportValidation->fails())
            return response(['status' => 'warning', 'message' => implode('<br>', $emergencyReportValidation->errors()->all())]);

        $userIp          = $request->ip();
        $resident        = $this->reportLog->where('user_ip', $userIp)->where('report_type', 'Emergency')->first();
        $reportPhotoPath = $request->file('image');
        $reportPhotoPath = $reportPhotoPath->store();
        $request->image->move(public_path('reports_image'), $reportPhotoPath);
        $emergencyReport = [
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'type'        => 'Emergency',
            'photo'       => $reportPhotoPath,
            'details'     => trim($request->details),
            'status'      => 'Pending',
            'user_ip'     => $userIp,
            'report_time' => Date::now()
        ];

        if ($resident) {
            $residentAttempt = $resident->attempt;
            $reportTime      = $resident->report_time;

            if ($residentAttempt == 3) {
                $isBlock = $this->residentReport->isBlocked($reportTime);

                if (!$isBlock) {
                    $resident->update(['attempt' => 0, 'report_time' => null]);
                    $residentAttempt = 0;
                } else
                    return response(['status' => 'blocked', 'message' => "You can report again after " . $isBlock . "."]);
            }

            $resident->update(['attempt' => $residentAttempt + 1]);
            if ($resident->attempt == 3) $resident->update(['report_time' => Date::now()->addHour()]);
        } else {
            $this->reportLog->create([
                'user_ip'     => $userIp,
                'report_type' => 'Emergency',
                'attempt'     => 1,
            ]);
        }

        $this->emergencyReport->create($emergencyReport);
        // event(new IncidentReport());
        // event(new Notification());

        return response()->json();
    }

    public function changeEmergencyReportStatus($reportId)
    {
        $report = $this->emergencyReport->find($reportId);
        $status = $report->status == "Pending" ? "Resolving" : "Resolved";
        $report->update(['status' => $status]);
        $this->logActivity->generateLog($reportId, 'Emergency', 'set the emergency report status to resolving');
        // event(new IncidentReport());

        return response()->json();
    }

    public function removeEmergencyReport($reportId)
    {
        $report = $this->emergencyReport->find($reportId);
        $report->delete();

        if ($report->photo) {
            $image_path = public_path('reports_image/' . $report->photo);

            if (file_exists($image_path)) unlink($image_path);
        }
        $this->logActivity->generateLog($reportId, ' Emergency', 'removed emergency report');
        // event(new IncidentReport());

        return response()->json();
    }

    public function archiveEmergencyReport($reportId)
    {
        $report = $this->emergencyReport->find($reportId);
        $report->update(['is_archive' => 1]);
        $this->logActivity->generateLog($reportId, 'Emergency', "archived emergency report");
        //event(new IncidentReport());

        return response()->json();
    }
}
