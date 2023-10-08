<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use App\Models\Evacuee;
use App\Models\Disaster;
use App\Models\Guideline;
use Illuminate\Http\Request;
use App\Models\ActivityUserLog;
use App\Models\EvacuationCenter;
use App\Events\NotificationEvent;
use App\Exports\EvacueeDataExport;
use App\Models\IncidentReport;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Excel as FileFormat;

class MainController extends Controller
{
    private $evacuationCenter, $disaster, $evacuee, $notification, $guide, $guideline, $incidentReport;

    public function __construct()
    {
        $this->guide            = new Guide;
        $this->evacuee          = new Evacuee;
        $this->disaster         = new Disaster;
        $this->guideline        = new Guideline;
        $this->notification     = new NotificationEvent;
        $this->evacuationCenter = new EvacuationCenter;
        $this->incidentReport   = new IncidentReport;
    }

    public function dashboard()
    {
        $disasterData     = $this->fetchDisasterData();
        $onGoingDisasters = $this->disaster->where('status', "On Going")->get();
        $activeEvacuation = $this->evacuationCenter->where('status', "Active")->count();
        $totalEvacuee     = $this->evacuee->where('status', "Evacuated")->sum('individuals');
        $totalEvacuee     = strval($totalEvacuee);
        $notifications    = $this->notification->notifications();
        $incidentReport   = $this->incidentReport->where('report_time', '>=', Carbon::now()->format('Y-m-d H:i:s'))->count();

        return view('userpage.dashboard', compact('activeEvacuation', 'disasterData', 'totalEvacuee', 'onGoingDisasters', 'notifications', 'incidentReport'));
    }

    public function generateExcelEvacueeData(Request $request)
    {
        $generateReportValidation = Validator::make($request->all(), [
            'disaster_id' => 'required'
        ]);

        if ($generateReportValidation->fails())
            return back()->with('warning', $generateReportValidation->errors()->first());

        return Excel::download(new EvacueeDataExport($request->disaster_id), 'evacuee-data.xlsx', FileFormat::XLSX);
    }

    public function eligtasGuideline()
    {
        $notifications = $this->notification->notifications();
        $guidelineData = "";

        if (!auth()->check()) {
            $guidelineData = $this->guideline->all();

            return view('userpage.guideline.eligtasGuideline', compact('guidelineData'));
        }

        $guidelineData = auth()->user()->organization == "CDRRMO" ? $this->guideline->where('organization', "CDRRMO")->get() :
            $this->guideline->where('organization', "CSWD")->get();

        return view('userpage.guideline.eligtasGuideline', compact('guidelineData', 'notifications'));
    }

    public function guide($guidelineId)
    {
        $notifications = $this->notification->notifications();
        $guide         = $this->guide->where('guideline_id', Crypt::decryptString($guidelineId))->get();

        return view('userpage.guideline.guide', compact('guide', 'guidelineId', 'notifications'));
    }

    public function manageEvacueeInformation($operation)
    {
        $disasterList   = $this->disaster->where('is_archive', 0)->get();
        $archiveDisasterList = $this->disaster->where('is_archive', 1)->get();
        $evacuationList = $this->evacuationCenter->whereNotIn('status', ['Inactive', 'Archived'])->get();

        return view('userpage.evacuee.evacuee', compact('evacuationList', 'disasterList', 'archiveDisasterList', 'operation'));
    }

    public function disasterInformation($operation)
    {
        return view('userpage.disaster.disaster', compact('operation'));
    }

    public function evacuationCenterLocator()
    {
        $prefix = request()->route()->getPrefix();

        return view('userpage.evacuationCenter.evacuationCenterLocator', compact('prefix'));
    }

    public function evacuationCenter($operation)
    {
        return view('userpage.evacuationCenter.manageEvacuation', compact('operation'));
    }

    public function incidentReport($operation)
    {
        $notifications = $this->notification->notifications();

        return view('userpage.incidentReport.incidentReport', compact('operation', 'notifications'));
    }

    public function userActivityLog()
    {
        $userActivityLogs = ActivityUserLog::join('user', 'activity_log.user_id', '=', 'user.id')
            ->select('activity_log.data_name', 'activity_log.activity', 'activity_log.date_time', 'user.name')
            ->get();

        return view('userpage.activityLog', compact('userActivityLogs'));
    }

    public function userAccounts($operation)
    {
        $notifications = $this->notification->notifications();

        return view('userpage.userAccount.userAccounts', compact('operation', 'notifications'));
    }

    public function userProfile()
    {
        $notifications = $this->notification->notifications();

        return view('userpage.userAccount.userProfile', compact('notifications'));
    }

    public function manageHazardReport()
    {
        $notifications = $this->notification->notifications();

        return view('userpage.hazardReport.manageHazardReport', compact('notifications'));
    }

    public function fetchDisasterData()
    {
        $disasterData     = [];
        $onGoingDisasters = $this->disaster->where('status', "On Going")->get();

        foreach ($onGoingDisasters as $disaster) {
            $totalEvacuee = 0;
            $totalEvacuee += $this->evacuee->where('disaster_id', $disaster->id)->sum('individuals');
            $result = $this->evacuee
                ->where('disaster_id', $disaster->id)
                ->selectRaw('SUM(male) as totalMale,
                    SUM(female) as totalFemale,
                    SUM(senior_citizen) as totalSeniorCitizen,
                    SUM(minors) as totalMinors,
                    SUM(infants) as totalInfants,
                    SUM(pwd) as totalPwd,
                    SUM(pregnant) as totalPregnant,
                    SUM(lactating) as totalLactating')
                ->first();

            $disasterData[] = array_merge(['disasterName' => $disaster->name, 'totalEvacuee' => $totalEvacuee], $result->toArray());
        }

        return request()->ajax() ? response()->json($disasterData) : $disasterData;
    }

    public function hotlineNumbers()
    {
        $notifications = $this->notification->notifications();

        return view('userpage.hotlineNumbers', compact('notifications'));
    }

    public function about()
    {
        $notifications = $this->notification->notifications();

        return view('userpage.about', compact('notifications'));
    }
}
