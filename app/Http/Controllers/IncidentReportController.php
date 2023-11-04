<?php

namespace App\Http\Controllers;

use App\Models\ReportLog;
use Illuminate\Http\Request;
use App\Models\ResidentReport;
use App\Models\ActivityUserLog;
use Yajra\DataTables\DataTables;
use App\Events\Notification;
use App\Events\IncidentReport;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\ResidentReportController;

class IncidentReportController extends Controller
{
    private $reportLog, $logActivity, $incidentReport, $residentReport;

    function __construct()
    {
        $this->reportLog      = new ReportLog;
        $this->incidentReport = new ResidentReport;
        $this->logActivity    = new ActivityUserLog;
        $this->residentReport = new ResidentReportController;
    }

    public function getIncidentReport($operation, $year, $type)
    {
        $incidentReports = $this->incidentReport->where('is_archive', $operation == "manage" ? 0 : 1);

        if ($operation == "manage")
            return response($incidentReports->where('type', 'Incident')->get());
        else
            return DataTables::of(
                $incidentReports->where('type', $type)
                    ->whereYear('report_time', $year)
                    ->get()
            )
                ->addColumn('location', '<button class="btn-table-primary viewLocationBtn"><i class="bi bi-pin-map"></i> View</button>')
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

    public function createIncidentReport(Request $request)
    {
        $incidentReportValidation = Validator::make($request->all(), [
            'latitude'  => 'required',
            'longitude' => 'required',
            'details'   => 'required',
            'image'     => 'required|image|mimes:jpeg,png,jpg'
        ]);

        if ($incidentReportValidation->fails())
            return response(['status' => 'warning', 'message' => implode('<br>', $incidentReportValidation->errors()->all())]);

        $resident = $this->reportLog
            ->where('user_ip', $request->ip())
            ->where('report_type', 'Incident')
            ->first();

        $reportPhotoPath = $request->file('image');
        $reportPhotoPath = $request->file('image')->store();
        $request->image->move(public_path('reports_image'), $reportPhotoPath);

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
            if ($resident->attempt == 3) $resident->update(['report_time' => Date::now()->addHour(1)]);
        } else {
            $this->reportLog->create([
                'user_ip'     => $request->ip(),
                'report_type' => 'Incident',
                'attempt'     => 1,
            ]);
        }

        $this->incidentReport->create([
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'type'        => 'Incident',
            'photo'       => $reportPhotoPath,
            'details'     => trim($request->details),
            'user_ip'     => $request->ip(),
            'report_time' => Date::now()
        ]);
        event(new IncidentReport());
        event(new Notification());

        return response()->json();
    }

    public function changeIncidentReportStatus($reportId)
    {
        $report = $this->incidentReport->find($reportId);
        $status = $report->status == "Pending" ? "Resolving" : "Resolved";
        $report->update([
            'status' => $status
        ]);
        $this->logActivity->generateLog($reportId, 'Incident', 'set the incident report status to resolving');
        event(new IncidentReport());
        event(new Notification());

        return response()->json();
    }

    public function removeIncidentReport($reportId)
    {
        $report = $this->incidentReport->find($reportId);
        $report->delete();

        if ($report->photo) {
            $image_path = public_path('reports_image/' . $report->photo);
            File::delete($image_path);
        }
        $this->logActivity->generateLog($reportId, ' Incident', 'removed incident report');
        event(new IncidentReport());
        event(new Notification());

        return response()->json();
    }

    public function archiveIncidentReport($reportId)
    {
        $report = $this->incidentReport->find($reportId);
        $report->update([
            'is_archive' => 1
        ]);
        $this->logActivity->generateLog($reportId, 'Incident', "archived incident report");
        event(new IncidentReport());

        return response()->json();
    }
}
