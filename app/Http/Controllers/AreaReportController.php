<?php

namespace App\Http\Controllers;

use App\Models\ReportLog;
use App\Models\ReportUpdate;
use App\Models\ResidentReport;
use App\Models\ActivityUserLog;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Validator;
use App\Events\Notification;
use App\Events\AreaReport as AreaReportEvent;
use App\Http\Controllers\ResidentReportController;

class AreaReportController extends Controller
{
    private $reportLog, $reportUpdate, $areaReport, $logActivity, $residentReport;

    function __construct()
    {
        $this->reportLog      = new ReportLog;
        $this->reportUpdate   = new ReportUpdate;
        $this->areaReport     = new ResidentReport;
        $this->logActivity    = new ActivityUserLog;
        $this->residentReport = new ResidentReportController;
    }

    public function getAreaReport($operation, $year, $type)
    {
        $areaReport = $this->areaReport->where('is_archive', $operation == "manage" ? 0 : 1);

        if ($operation != "archived") {
            $prefix     = request()->route()->getPrefix();
            $areaReport = $areaReport
                ->whereIn('type', ['Flooded', 'Roadblocked'])
                ->when($prefix == "resident", fn ($query) => $query->where('status', 'Approved'))
                ->get();

            foreach ($areaReport as $report) {
                $report->update = $this->reportUpdate
                    ->where('report_id', $report->id)
                    ->when($prefix != "cdrrmo", fn ($query) => $query->where('update_time', '>=', Date::now()->subHours(24)))
                    ->sortByDesc('update_time')
                    ->get();
            }

            return response($areaReport);
        } else
            return DataTables::of(
                $areaReport->where('type', $type)
                    ->whereYear('report_time', $year)
                    ->get()
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

    public function createAreaReport(Request $request)
    {
        $areaReportValidation = Validator::make($request->all(), [
            'latitude'  => 'required',
            'longitude' => 'required',
            'type'      => 'required',
            'details'   => 'required',
            'image'     => 'required|image|mimes:jpeg,png,jpg'
        ]);

        if ($areaReportValidation->fails())
            return response(['status' => 'warning', 'message' => implode('<br>', $areaReportValidation->errors()->all())]);

        $userIp          = $request->ip();
        $resident        = $this->reportLog->where('user_ip', $userIp)->where('report_type', 'Area')->first();
        $reportPhotoPath = $request->file('image');
        $reportPhotoPath = $request->file('image')->store();
        $request->image->move(public_path('reports_image'), $reportPhotoPath);
        $areaReport      = [
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'type'        => $request->type,
            'details'     => trim($request->details),
            'photo'       => $reportPhotoPath,
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
                'report_type' => 'Area',
                'attempt'     => 1
            ]);
        }

        $this->areaReport->create($areaReport);
        // event(new AreaReport());
        // event(new Notification());

        return response()->json();
    }

    public function approveAreaReport($reportId)
    {
        $report = $this->areaReport->find($reportId);
        $report->update(['status' => 'Approved']);
        $this->logActivity->generateLog($reportId, $report->type, 'approved area report');
        // event(new AreaReport());

        return response()->json();
    }

    public function updateAreaReport(Request $request, $reportId)
    {
        $areaReportValidation = Validator::make($request->all(), [
            'update' => 'required'
        ]);

        if ($areaReportValidation->fails())
            return response(['status' => 'warning', 'message' =>  $areaReportValidation->errors()->first()]);

        $this->reportUpdate->addUpdate($reportId, $request->update);
        $report = $this->areaReport->find($reportId);
        $this->logActivity->generateLog($reportId, $report->type, 'add update to area report');
        // event(new AreaReport());

        return response()->json();
    }

    public function removeAreaReport($reportId)
    {
        $report = $this->areaReport->find($reportId);
        $report->delete();

        if ($report->photo) {
            $image_path = public_path('reports_image/' . $report->photo);

            if (file_exists($image_path)) unlink($image_path);
        }

        $this->logActivity->generateLog($reportId, $report->type, 'removed area report');
        // event(new AreaReport());

        return response()->json();
    }

    public function archiveAreaReport($reportId)
    {
        $report = $this->areaReport->find($reportId);
        $report->update(['is_archive' => 1]);
        $this->logActivity->generateLog($reportId, $report->type, 'archived area report');
        // event(new AreaReport());

        return response()->json();
    }
}
