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
use App\Events\AreaReport;
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
        $areaReport = $this->areaReport->where('is_archive', $operation == "archived" ? 1 : 0);

        if ($operation != "archived") {
            $prefix = basename(trim(request()->route()->getPrefix(), '/'));
            $areaReport = $areaReport->whereIn('type', ['Flooded', 'Roadblocked'])->when(
                $prefix == "resident" || ($prefix == 'cdrrmo' && $operation == 'locator'),
                fn ($query) => $query->where('status', 'Approved')
            )->get();
            foreach ($areaReport as $report) {
                $report->update = $this->reportUpdate->where('report_id', $report->id)
                    ->when(
                        $prefix != "cdrrmo" || ($prefix == 'cdrrmo' && $operation == 'locator'),
                        fn ($query) => $query->where('update_time', '>=', Date::now()->subHours(24))
                    )
                    ->OrderBy('update_time', 'desc')->get();
            }

            return response($areaReport);
        } else
            return DataTables::of($areaReport->where('type', $type)->whereYear('report_time', $year)->get())
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
                })->rawColumns(['location', 'photo'])->make(true);
    }

    public function createAreaReport(Request $request)
    {
        $areaReportValidation = Validator::make($request->all(), [
            'type'      => 'required',
            'image'     => 'required|image|mimes:jpeg,png,jpg',
            'details'   => 'required',
            'latitude'  => 'required',
            'longitude' => 'required'
        ]);

        if ($areaReportValidation->fails()) return response(['status' => 'warning', 'message' =>  implode('<br>', $areaReportValidation->errors()->all())]);

        $userIp          = $request->ip();
        $resident        = $this->reportLog->where('user_ip', $userIp)->where('report_type', 'Area')->first();
        $reportPhotoPath = $request->file('image')->store();
        $request->image->move(public_path('reports_image'), $reportPhotoPath);

        if ($resident) {
            $reportTime      = $resident->report_time;
            $residentAttempt = $resident->attempt;

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
                'attempt'     => 1,
                'user_ip'     => $userIp,
                'report_type' => 'Area'
            ]);
        }

        $this->areaReport->create([
            'type'        => $request->type,
            'photo'       => $reportPhotoPath,
            'user_ip'     => $userIp,
            'details'     => trim($request->details),
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'report_time' => Date::now()
        ]);
        event(new AreaReport());
        event(new Notification());

        return response([]);
    }

    public function approveAreaReport($reportId)
    {
        $this->areaReport->find($reportId)->update(['status' => 'Approved']);
        $this->logActivity->generateLog('Approved area report(ID - ' . $reportId . ')');
        event(new AreaReport());
        event(new Notification());

        return response([]);
    }

    public function updateAreaReport(Request $request, $reportId)
    {
        $areaReportValidation = Validator::make($request->all(), ['update' => 'required']);

        if ($areaReportValidation->fails()) return response(['status' => 'warning', 'message' =>  $areaReportValidation->errors()->first()]);

        $this->reportUpdate->addUpdate($reportId, $request->update);
        $this->logActivity->generateLog('Added update to area report(ID - ' . $reportId . ')');
        event(new AreaReport());
        event(new Notification());

        return response([]);
    }

    public function removeAreaReport($reportId)
    {
        $report = $this->areaReport->find($reportId);
        $report->delete();
        unlink(public_path('reports_image/' . $report->photo));
        $this->logActivity->generateLog('Removed area report(ID - ' . $reportId . ')');
        event(new AreaReport());
        event(new Notification());

        return response([]);
    }

    public function archiveAreaReport($reportId)
    {
        $this->areaReport->find($reportId)->update(['is_archive' => 1]);
        $this->logActivity->generateLog('Archived area report(ID - ' . $reportId . ')');
        event(new AreaReport());

        return response([]);
    }
}
