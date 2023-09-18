<?php

namespace App\Http\Controllers;


use App\Models\Evacuee;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Events\ActiveEvacuees;
use App\Models\ActivityUserLog;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class EvacueeController extends Controller
{
    private $evacuee, $logActivity;

    function __construct()
    {
        $this->evacuee     = new Evacuee;
        $this->logActivity = new ActivityUserLog;
    }

    public function getEvacueeData()
    {
        $evacueeInfo = $this->evacuee->all();
        return DataTables::of($evacueeInfo)
            ->addIndexColumn()
            ->addColumn('action', function () {
                if (auth()->user()->is_disable == 1) return;

                return '<button class="btn-table-update" id="updateEvacueeBtn"><i class="bi bi-pencil-square"></i>Update</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function recordEvacueeInfo(Request $request)
    {
        $evacueeInfoValidation = Validator::make($request->all(), [
            'infants'             => 'required|numeric',
            'minors'              => 'required|numeric',
            'senior_citizen'      => 'required|numeric',
            'pwd'                 => 'required|numeric',
            'pregnant'            => 'required|numeric',
            'lactating'           => 'required|numeric',
            'families'            => 'required|numeric',
            'individuals'         => 'required|numeric',
            'male'                => 'required|numeric',
            'female'              => 'required|numeric',
            'disaster_id'         => 'required',
            'date_entry'          => 'required',
            'barangay'            => 'required|unique:evacuee,barangay',
            'evacuation_assigned' => 'required'
        ]);

        if ($evacueeInfoValidation->fails())
            return response(['status' => 'warning', 'message' => $evacueeInfoValidation->errors()->first()]);

        $evacueeInfo = $request->only([
            'infants', 'minors', 'senior_citizen', 'pwd', 'pregnant', 'lactating',
            'families', 'individuals', 'male', 'female', 'disaster_id', 'date_entry',
            'barangay', 'evacuation_assigned'
        ]);
        $evacueeInfo['user_id']    = auth()->user()->id;
        $evacueeInfo['remarks']    = Str::ucfirst(trim($request->remarks));
        $evacueeInfo['is_archive'] = 0;
        $evacueeInfo               = $this->evacuee->create($evacueeInfo);
        $this->logActivity->generateLog($evacueeInfo->id, $evacueeInfo->barangay, 'recorded a new evacuee information: ');
        // event(new ActiveEvacuees());
        
        return response()->json();
    }

    public function updateEvacueeInfo(Request $request, $evacueeId)
    {
        $evacueeInfoValidation = Validator::make($request->all(), [
            'infants'             => 'required|numeric',
            'minors'              => 'required|numeric',
            'senior_citizen'      => 'required|numeric',
            'pwd'                 => 'required|numeric',
            'pregnant'            => 'required|numeric',
            'lactating'           => 'required|numeric',
            'families'            => 'required|numeric',
            'individuals'         => 'required|numeric',
            'male'                => 'required|numeric',
            'female'              => 'required|numeric',
            'disaster_id'         => 'required',
            'date_entry'          => 'required',
            'barangay'            => 'required',
            'evacuation_assigned' => 'required'
        ]);

        if ($evacueeInfoValidation->fails())
            return response(['status' => 'warning', 'message' => $evacueeInfoValidation->errors()->first()]);

        $evacueeInfo = $request->only([
            'infants', 'minors', 'senior_citizen', 'pwd', 'pregnant', 'lactating',
            'families', 'individuals', 'male', 'female', 'disaster_id', 'date_entry',
            'barangay', 'evacuation_assigned'
        ]);
        $evacueeInfo['user_id'] = auth()->user()->id;
        $evacueeInfo['remarks'] = Str::ucfirst(trim($request->remarks));
        $evacueeInfo            = $this->evacuee->find($evacueeId)->update($evacueeInfo);
        $this->logActivity->generateLog($evacueeId, $evacueeInfo->barangay, 'updated a evacuee information');
        
        return response()->json();
    }
}
