<?php

namespace App\Http\Controllers;

use App\Events\Notification;
use App\Models\ResidentReport;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Date;

class ResidentReportController extends Controller
{
    private $residentReport;

    function __construct()
    {
        $this->residentReport = new ResidentReport;
    }

    public function isBlocked($reportTime)
    {
        return $reportTime <= Date::now() ? false : Date::parse($reportTime)->diffForHumans();
    }

    public function getResidentReport($year)
    {
        return DataTables::of(
            $this->residentReport
                ->where('is_archive', 1)
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

    public function changeNotificationStatus($reportId) {
        $report = $this->residentReport->find($reportId);
        $report->update([
            'notification' => 0
        ]);
        //event(new Notification());

        return response()->json();
    }
}
