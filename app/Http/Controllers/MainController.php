<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use App\Models\Evacuee;
use App\Models\Disaster;
use App\Models\Guideline;
use Illuminate\Http\Request;
use App\Models\ActivityUserLog;
use App\Models\EvacuationCenter;
use App\Exports\EvacueeDataExport;
use App\Models\HotlineNumbers;
use App\Models\ResidentReport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Excel as FileFormat;

class MainController extends Controller
{
    private $evacuationCenter, $disaster, $evacuee, $guide, $guideline, $residentReport;

    public function __construct()
    {
        $this->guide            = new Guide;
        $this->evacuee          = new Evacuee;
        $this->disaster         = new Disaster;
        $this->guideline        = new Guideline;
        $this->evacuationCenter = new EvacuationCenter;
        $this->residentReport   = new ResidentReport;
    }

    public function dashboard()
    {
        $disaster              = $this->disaster->all();
        $disasterData          = $this->fetchDisasterData();
        $onGoingDisasters      = $disaster->where('status', "On Going");
        $activeEvacuation      = $this->evacuationCenter->where('status', "Active")->count();
        $totalEvacuee          = strval($this->evacuee->where('status', "Evacuated")->sum('individuals'));
        $residentReport        = $this->residentReport->where('report_time', '>=', now()->format('Y-m-d H:i:s'))->count();

        return view('userpage.dashboard', compact('activeEvacuation', 'disasterData', 'totalEvacuee', 'onGoingDisasters', 'disaster', 'residentReport'));
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
        $generateReportValidation = Validator::make($request->all(), [
            'disaster_id' => 'required'
        ]);

        if ($generateReportValidation->fails())
            return back()->with('warning', "Disaster is not exist.");

        return Excel::download(new EvacueeDataExport($request->disaster_id), 'evacuee-data.xlsx', FileFormat::XLSX);
    }

    public function eligtasGuideline()
    {
        $guidelineData = "";

        if (!auth()->check()) {
            $guidelineData = $this->guideline->all();

            return view('userpage.guideline.eligtasGuideline', compact('guidelineData'));
        }

        $guidelineData = $this->guideline->where('organization', auth()->user()->organization)->get();

        return view('userpage.guideline.eligtasGuideline', compact('guidelineData'));
    }

    public function searchGuideline(Request $request)
    {
        $searchGuidelineValdation = Validator::make($request->all(), [
            'guideline_name' => 'required'
        ]);

        if ($searchGuidelineValdation->fails())
            return response(['warning' => $searchGuidelineValdation->errors()->first()]);

        $guideline = $this->guideline->select('id', 'type', 'guideline_img');

        if (auth()->check()) $guideline->where('organization', auth()->user()->organization);

        $guidelineData = $guideline->where('type', 'LIKE', "%{$request->guideline_name}%")->get();

        if ($guidelineData->isEmpty()) return back()->with('warning', "Sorry, we couldn't find any result.");
        return response(['guidelineData' => $guidelineData]);
    }

    public function guide($guidelineId)
    {
        $guide          = $this->guide->where('guideline_id', $guidelineId)->get();
        $guidelineLabel = $this->guideline->where('id', $guidelineId)->value('type');

        return view('userpage.guideline.guide', compact('guide', 'guidelineId', 'guidelineLabel'));
    }

    public function manageEvacueeInformation($operation)
    {
        $disasterList        = $this->disaster->where('is_archive', 0)->get();
        $archiveDisasterList = $this->disaster->where('is_archive', 1)->get();
        $yearList            = collect($archiveDisasterList->pluck('year')->unique()->toArray())->sort();
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

    public function incidentReporting()
    {
        return view('userpage.residentReport.incidentReporting');
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

        return view('userpage.userAccount.userAccounts', compact('operation'));
    }

    public function userProfile()
    {
        return view('userpage.userAccount.userProfile');
    }

    public function manageReport($operation)
    {
        $prefix = request()->route()->getPrefix();
        $reportType = ['All', 'Emergency', 'Incident', 'Flooded', 'Roadblocked'];
        $yearList = [];

        if ($operation == "archived")
            $yearList = $this->residentReport
                ->where('is_archive', 1)
                ->selectRaw('YEAR(report_time) as year')
                ->distinct()
                ->orderBy('year', 'desc')
                ->get();

        return view('userpage.residentReport.manageReport', compact('operation', 'prefix', 'yearList', 'reportType'));
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
        $hotlineNumbers = HotlineNumbers::all();

        return view('userpage.hotlineNumber.hotlineNumbers', compact('hotlineNumbers'));
    }

    public function about()
    {

        return view('userpage.about');
    }
}
