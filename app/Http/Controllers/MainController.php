<?php

namespace App\Http\Controllers;

use App\Models\Evacuee;
use App\Models\Disaster;
use Illuminate\Http\Request;
use App\Models\HazardReport;
use App\Models\IncidentReport;
use App\Models\EvacuationCenter;
use App\Exports\EvacueeDataExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Excel as FileFormat;

class MainController extends Controller
{
    private $evacuationCenter, $disaster, $evacuee, $hazardReport;

    public function __construct()
    {
        $this->evacuee = new Evacuee;
        $this->disaster = new Disaster;
        $this->hazardReport = new HazardReport;
        $this->evacuationCenter = new EvacuationCenter;
    }

    public function dashboard()
    {
        $disasterData = $this->fetchDisasterData();
        $onGoingDisasters = $this->disaster->where('status', "On Going")->get();
        $activeEvacuation = $this->evacuationCenter->where('status', "Active")->count();
        $totalEvacuee = array_sum(array_column($disasterData, 'totalEvacuee'));

        return view('userpage.dashboard',  compact('activeEvacuation', 'disasterData', 'totalEvacuee', 'onGoingDisasters'));
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

    public function manageEvacueeInformation(Request $request)
    {
        $disasterList = $this->disaster->where('status', 'On Going')->get();
        $evacuationList = $this->evacuationCenter->whereNotIn('status', ['Inactive', 'Archived'])->get();
        return view('userpage.evacuee.evacuee', compact('evacuationList', 'disasterList'));
    }

    public function evacuationCenterLocator()
    {
        $prefix = request()->route()->getPrefix();
        return view('userpage.evacuationCenter.evacuationCenterLocator', compact('prefix'));
    }

    public function incidentReport()
    {
        $incidentReport = IncidentReport::where('status', 'Confirmed')->where('is_archive', 0)->get();
        return view('userpage.incidentReport.incidentReport', compact('incidentReport'));
    }

    public function fetchDisasterData()
    {
        $disasterData = [];
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
}
