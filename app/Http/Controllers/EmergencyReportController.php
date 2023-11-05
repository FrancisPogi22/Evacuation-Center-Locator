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
            return DataTables::of($emergencyReport->where('type', $type)->whereYear('report_time', $year)->get())
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
                })->rawColumns(['location', 'photo'])->make(true);
    }

    public function createEmergencyReport(Request $request)
    {
        $userIp   = $request->ip();
        $resident = $this->reportLog->where('user_ip', $userIp)->where('report_type', 'Emergency')->first();

        if ($resident) {
            $reportTime      = $resident->report_time;
            $residentAttempt = $resident->attempt;

            if ($residentAttempt == 3) {
                $isBlock = $this->residentReport->isBlocked($reportTime);

                if (!$isBlock) {
                    $resident->update(['attempt' => 0, 'report_time' => null]);
                    $residentAttempt = 0;
                } else
                    return response(['status' => 'blocked', 'message' => "You can click the SOS button again after " . $isBlock . "."]);
            }

            $resident->update(['attempt' => $residentAttempt + 1]);
            if ($resident->attempt == 3) $resident->update(['report_time' => Date::now()->addHour(1)]);
        } else {
            $this->reportLog->create([
                'attempt'     => 1,
                'user_ip'     => $userIp,
                'report_type' => 'Emergency'
            ]);
        }

        if ($this->emergencyReport
            ->where([
                'status'    => 'Pending',
                'user_ip'   => $userIp,
                'latitude'  => $request->latitude,
                'longitude' => $request->longitude
            ])
            ->exists()
        ) return response(['status' => 'duplicate', 'message' => 'You\'ve already requested help.']);

        $this->emergencyReport->create([
            'type'        => 'Emergency',
            'user_ip'     => $userIp,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'report_time' => Date::now(),
        ]);
        event(new EmergencyReport());
        event(new Notification());

        return response([]);
    }

    public function changeEmergencyReportStatus($reportId)
    {
        $report = $this->emergencyReport->find($reportId);
        $report->update(['status' => $report->status == "Pending" ? "Rescuing" : "Rescued"]);
        $this->logActivity->generateLog($reportId, 'Emergency', 'set the emergency report status to resolving');
        event(new EmergencyReport());
        event(new Notification());

        return response([]);
    }

    public function removeEmergencyReport($reportId)
    {
        $this->emergencyReport->find($reportId)->delete();
        $this->logActivity->generateLog($reportId, ' Emergency', 'removed emergency report');
        event(new EmergencyReport());
        event(new Notification());

        return response([]);
    }

    public function archiveEmergencyReport(Request $request, $reportId)
    {
        $emergencyReportValidation = Validator::make($request->all(), [
            'image'   => 'required|image|mimes:jpeg,png,jpg',
            'details' => 'required'
        ]);

        if ($emergencyReportValidation->fails())
            return response(['status' => 'warning', 'message' =>  implode('<br>', $emergencyReportValidation->errors()->all())]);

        $reportPhotoPath = $request->file('image')->store();
        $request->image->move(public_path('reports_image'), $reportPhotoPath);
        $this->emergencyReport->find($reportId)->update([
            'photo'      => $reportPhotoPath,
            'details'    => trim($request->details),
            'is_archive' => 1
        ]);
        $this->logActivity->generateLog($reportId, 'Emergency', "archived emergency report");
        event(new EmergencyReport());

        return response([]);
    }
}
