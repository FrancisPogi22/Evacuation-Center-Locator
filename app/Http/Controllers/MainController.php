<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use App\Models\Evacuee;
use App\Models\Disaster;
use App\Models\Guideline;
use Illuminate\Http\Request;
use App\Models\IncidentReport;
use App\Models\ActivityUserLog;
use App\Models\EvacuationCenter;
use App\Events\NotificationEvent;
use App\Exports\EvacueeDataExport;
use App\Models\HotlineNumbers;
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
        $disaster              = $this->disaster->all();
        $disasterData          = $this->fetchDisasterData();
        $onGoingDisasters      = $disaster->where('status', "On Going");
        $activeEvacuation      = $this->evacuationCenter->where('status', "Active")->count();
        $totalEvacuee          = strval($this->evacuee->where('status', "Evacuated")->sum('individuals'));
        $notifications         = $this->notification->notifications();
        $incidentReport        = $this->incidentReport->where('report_time', '>=', now()->format('Y-m-d H:i:s'))->count();

        return view('userpage.dashboard', compact('activeEvacuation', 'disasterData', 'totalEvacuee', 'onGoingDisasters', 'disaster', 'notifications', 'incidentReport'));
    }

    public function fetchDisasters($year)
    {
        return $this->disaster->where('year', $year)->get();
    }

    public function initDisasterData($disasterName)
    {
        $disaterData = $this->disaster->select('id', 'name', 'year')
            ->where('name', 'LIKE', "%{$disasterName}%")
            ->get();

        return response()->json($disaterData);
    }

    public function generateExcelEvacueeData(Request $request)
    {
        $generateReportValidation = Validator::make($request->only('disaster_id'), [
            'disaster_id' => 'required'
        ]);

        if ($generateReportValidation->fails())
            return back()->with('warning', "Disaster is not exist.");

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

        $guidelineData = $this->guideline->where('organization', auth()->user()->organization)->get();

        return view('userpage.guideline.eligtasGuideline', compact('guidelineData', 'notifications'));
    }

    public function searchGuideline(Request $request)
    {
        $searchGuidelineValdation = Validator::make($request->only('guideline_name'), [
            'guideline_name' => 'required'
        ]);

        if ($searchGuidelineValdation->fails())
            return back()->with('warning', $searchGuidelineValdation->errors()->first());

        $guideline = $this->guideline->select('id', 'type');

        if (auth()->check()) $guideline->where('organization', auth()->user()->organization);

        $guidelineData = $guideline->where('type', 'LIKE', "%{$request->guideline_name}%")->get();
        $notifications = $this->notification->notifications();

        if ($guidelineData->isEmpty()) return back()->with('warning', "Sorry, we couldn't find any result.");

        return view('userpage.guideline.eligtasGuideline', compact('guidelineData', 'notifications'));
    }

    public function guide($guidelineId)
    {
        $notifications  = $this->notification->notifications();
        $guidelineId    = Crypt::decryptString($guidelineId);
        $guide          = $this->guide->where('guideline_id', $guidelineId)->get();
        $guidelineLabel = $this->guideline->where('id', $guidelineId)->value('type');

        return view('userpage.guideline.guide', compact('guide', 'guidelineId', 'guidelineLabel', 'notifications'));
    }

    public function manageEvacueeInformation($operation)
    {
        $disasterList        = $this->disaster->where('is_archive', 0)->get();
        $archiveDisasterList = $this->disaster->where('is_archive', 1)->get();
        $yearList            = $archiveDisasterList->pluck('year')->unique();
        $archiveDisasterList = $archiveDisasterList->where('year', $yearList->first());
        $evacuationList      = $this->evacuationCenter->whereNotIn('status', ['Inactive', 'Archived'])->get();

        return view('userpage.evacuee.evacuee', compact('evacuationList', 'disasterList', 'yearList', 'archiveDisasterList', 'operation'));
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
        $notifications  = $this->notification->notifications();
        $hotlineNumbers = HotlineNumbers::all();

        return view('userpage.hotlineNumbers', compact('notifications', 'hotlineNumbers'));
    }

    public function about()
    {
        $notifications = $this->notification->notifications();

        return view('userpage.about', compact('notifications'));
    }
}
