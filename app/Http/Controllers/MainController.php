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
        $totalEvacuee     = strval($this->evacuee->where('status', "Evacuated")->sum('individuals'));
        $residentReport   = $this->residentReport->whereRaw('DATE(report_time) <= CURDATE()')->count();
        $onGoingDisasters = $disaster->where('status', "On Going");
        $activeEvacuation = $this->evacuationCenter->where('status', "Active")->count();

        return view('userpage.dashboard', compact('activeEvacuation', 'totalEvacuee', 'onGoingDisasters', 'disaster', 'residentReport'));
    }

    public function searchDisaster($year)
    {
        return $this->disaster->where('year', $year)->get();
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

        $guidelineData = $this->guideline
            ->select('id', 'type', 'guideline_img')
            ->when(auth()->check(), function ($query) {
                $query->where('organization', auth()->user()->organization);
            })
            ->where('type', 'LIKE', "%{$request->guideline_name}%")
            ->get();

        return $guidelineData->isEmpty()
            ? back()->with('warning', "Sorry, we couldn't find any result.")
            : response(['guidelineData' => $guidelineData]);
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

        $name = '';

        return DataTables::of(ActivityUserLog::join('user', 'activity_log.user_id', '=', 'user.id')
            ->select('activity_log.*', 'user.name', 'user.status')
            ->orderByDesc('activity_log.id')
            ->where('user.id', '!=', auth()->user()->id)
            ->get())
            ->addColumn('user_status', fn ($userLog) => '<div class="status-container"><div class="status-content bg-' .
                match ($userLog->status) {
                    'Active'   => 'success',
                    'Inactive' => 'warning',
                    'Archived' => 'danger'
                }
                . '">' . $userLog->status . '</div></div>')
            ->addColumn('action', function ($userLog) use (&$name) {
                $newName = $userLog->name != $name;
                $name = $userLog->name;

                return $userLog->status == 'Active' && $newName ?
                    '<div class="action-container"><button class="btn-table-remove" id="disableBtn"><i class="bi bi-person-lock"></i>Disable Account</button></div>' :
                    '';
            })->rawColumns(['user_status', 'action'])->make(1);
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

    public function fetchBarangayData()
    {
        return response()->json($this->evacuee->where('status', 'Evacuated')->selectRaw('barangay, SUM(male) as male, SUM(female) as female')->groupBy('barangay')->get());
    }

    public function fetchDisasterData()
    {
        return response()->json($this->disaster
            ->join('evacuee', 'evacuee.disaster_id', 'disaster.id')
            ->where('evacuee.status', 'Evacuated')
            ->selectRaw('disaster.name as disasterName,
                SUM(evacuee.male) as male,
                SUM(evacuee.female) as female,
                SUM(evacuee.senior_citizen) as senior_citizen,
                SUM(evacuee.minors) as minors,
                SUM(evacuee.infants) as infants,
                SUM(evacuee.pwd) as pwd,
                SUM(evacuee.pregnant) as pregnant,
                SUM(evacuee.lactating) as lactating')
            ->groupBy('disaster.id', 'disaster.name')
            ->get()
            ->toArray());
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
}
