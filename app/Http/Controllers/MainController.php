<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use App\Models\Evacuee;
use App\Models\Disaster;
use App\Models\Guideline;
use App\Models\HotlineNumbers;
use App\Models\ResidentReport;
use App\Models\ActivityUserLog;
use App\Models\EvacuationCenter;
use Illuminate\Http\Request;
use App\Exports\EvacueeDataExport;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
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
        $this->residentReport   = new ResidentReport;
        $this->evacuationCenter = new EvacuationCenter;
    }

    public function dashboard()
    {
        $disaster         = $this->disaster->all();
        $disasterData     = $this->fetchDisasterData();
        $totalEvacuee     = strval($this->evacuee->where('status', "Evacuated")->sum('individuals'));
        $residentReport   = $this->residentReport->whereRaw('DATE(report_time) <= CURDATE()')->count();
        $onGoingDisasters = $disaster->where('status', "On Going");
        $activeEvacuation = $this->evacuationCenter->where('status', "Active")->count();

        return view('userpage.dashboard', compact('activeEvacuation', 'disasterData', 'totalEvacuee', 'onGoingDisasters', 'disaster', 'residentReport'));
    }

    public function fetchDisasters($year)
    {
        return $this->disaster->where('year', $year)->get();
    }

    public function initDisasterData($disasterName)
    {
        $disaterData = $this->disaster->select('id', 'name', 'year')->where('name', 'LIKE', "%{$disasterName}%")->get();
        return response($disaterData);
    }

    public function generateExcelEvacueeData(Request $request)
    {
        $generateReportValidation = Validator::make($request->all(), ['disaster_id' => 'required']);
        if ($generateReportValidation->fails()) return back()->with('warning', "Disaster is not exist.");

        return Excel::download(new EvacueeDataExport($request->disaster_id), 'evacuee-data.xlsx', FileFormat::XLSX);
    }

    public function eligtasGuideline()
    {
        $guidelineData = !auth()->check() ? $this->guideline->all() : $this->guideline->where('organization', auth()->user()->organization)->get();

        return view('userpage.guideline.eligtasGuideline', compact('guidelineData'));
    }

    public function searchGuideline(Request $request)
    {
        $searchGuidelineValdation = Validator::make($request->all(), ['guideline_name' => 'required']);
        if ($searchGuidelineValdation->fails()) return response(['warning' => $searchGuidelineValdation->errors()->first()]);

        $guideline = $this->guideline->select('id', 'type', 'guideline_img');
        if (auth()->check()) $guideline->where('organization', auth()->user()->organization);

        $guidelineData = $guideline->where('type', 'LIKE', "%{$request->guideline_name}%")->get();
        if ($guidelineData->isEmpty()) return back()->with('warning', "Sorry, we couldn't find any result.");

        return response(['guidelineData' => $guidelineData]);
    }

    public function guide($guidelineId)
    {
        $guide          = $this->guide->where('guideline_id', $guidelineId)->get();
        $guidelineLabel = $this->guideline->find($guidelineId)->value('type');

        return view('userpage.guideline.guide', compact('guide', 'guidelineId', 'guidelineLabel'));
    }

    public function manageEvacueeInformation($operation)
    {
        $disasterList        = $this->disaster->where('is_archive', 0)->get();
        $archiveDisasterList = $this->disaster->where('is_archive', 1)->get();
        $yearList            = $archiveDisasterList->pluck('year')->unique()->sortByDesc('year');
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
        if (!request()->ajax()) return view('userpage.activityLog');

        return DataTables::of(ActivityUserLog::join('user', 'activity_log.user_id', '=', 'user.id')
            ->select('activity_log.*', 'user.*')->orderBy('activity_log.id', 'desc')->where('user.id', '!=', auth()->user()->id)->get())
            ->addColumn('activity', function ($userLog) {
                return $userLog->name . ' ' . $userLog->activity . ' ' . $userLog->data_name;
            })
            ->addColumn('action', function ($userLog) {
                if (auth()->user()->is_disable == 1) return;

                $actionBtn = '<div class="action-container">';

                if (auth()->user()->id != $userLog->user_id) {
                    if ($userLog->is_suspend == 0) {
                        if ($userLog->is_disable == 0)
                            $actionBtn .= '<button class="btn-table-remove" id="disableBtn" title="Disable"><i class="bi bi-x-lg"></i></button>';

                        $actionBtn .= '<button class="btn-table-update" id="suspendBtn" title="Suspend"><i class="bi bi-clock-history"></i></button>';
                    }
                }

                return '<button class="btn-table-primary" title="View" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-eye"></i></button>
                        <ul class="dropdown-menu log-dropdown">
                            <div class="log-container">
                                <p>Name: ' . $userLog->name . ' </p>
                                <p>Activity: ' . $userLog->activity . ' ' . $userLog->data_name . '</p>
                                <p>Time Issued: <span>' . $userLog->date_time . '</span></p>
                                <p>Status: <span class="log-status text-' . ($userLog->status == "Disabled" ? 'danger' : ($userLog->status == "Suspended" ? 'warning' : 'success')) . '">' . $userLog->status . '</span> </p>
                            </div>
                            <hr>
                            ' . $actionBtn . '</div>
                        </ul>';
            })->rawColumns(['activity', 'action'])->make(true);
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
        $yearList   = [];
        $prefix     = request()->route()->getPrefix();
        $reportType = ['All', 'Emergency', 'Incident', 'Flooded', 'Roadblocked'];
        if ($operation == "archived")
            $yearList = $this->residentReport->selectRaw('YEAR(report_time) as year')->where('is_archive', 1)->distinct()->orderBy('year', 'desc')->get();

        return view('userpage.residentReport.manageReport', compact('operation', 'prefix', 'yearList', 'reportType'));
    }

    public function fetchDisasterData()
    {
        $disasterData     = [];
        $onGoingDisasters = $this->disaster->join('evacuee', 'evacuee.disaster_id', '=', 'disaster.id')->where('evacuee.status', 'Evacuated')->select('disaster.*')->distinct()->get();

        foreach ($onGoingDisasters as $disaster) {
            $totalEvacuee = 0;
            $totalEvacuee += $this->evacuee->where('disaster_id', $disaster->id)->sum('individuals');
            $result = $this->evacuee->where('disaster_id', $disaster->id)
                ->where('status', "Evacuated")
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

    public function fetchReportData()
    {
        $startDate = now()->subMonth();

        $reportData = $this->residentReport
            ->selectRaw('type, DATE(report_time) as report_date, COUNT(*) as report_count')
            ->whereBetween('report_time', [$startDate, now()])
            ->groupBy(['type', 'report_date'])
            ->orderBy('report_date')
            ->get()
            ->groupBy('type')
            ->map(function ($typeData, $type) {
                return [
                    'type' => $type,
                    'data' => $typeData->map(function ($data) {
                        return [
                            'report_date' => $data->report_date,
                            'report_count' => $data->report_count,
                        ];
                    })->values(),
                ];
            })
            ->values();

        return response(['data' => $reportData, 'start_date' => $startDate]);
    }

    public function hotlineNumbers()
    {
        $hotlineNumbers = HotlineNumbers::all();

        return view('userpage.hotlineNumbers', compact('hotlineNumbers'));
    }

    public function about()
    {

        return view('userpage.about');
    }
}
