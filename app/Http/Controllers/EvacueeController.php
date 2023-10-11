<?php

namespace App\Http\Controllers;


use App\Models\Evacuee;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Events\ActiveEvacuees;
use App\Models\ActivityUserLog;
use App\Models\FamilyRecord;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\FamilyRecordController;

class EvacueeController extends Controller
{
    private $evacuee, $logActivity, $familyRecord, $familyController;

    function __construct()
    {
        $this->evacuee     = new Evacuee;
        $this->logActivity = new ActivityUserLog;
        $this->familyRecord = new FamilyRecord();
        $this->familyController = new FamilyRecordController();
    }

    public function getEvacueeData($operation, $disasterId, $status = null)
    {
        $evacueeInfo = $this->evacuee::join('evacuation_center', 'evacuee.evacuation_id', '=', 'evacuation_center.id')
            ->select('evacuee.*', 'evacuation_center.name as evacuation_assigned')
            ->when($operation == "manage", fn ($query) => $query->where('evacuee.status', $status))
            ->when($operation == "archived", fn ($query) => $query->where('evacuee.is_archive', 1))
            ->where('evacuee.disaster_id', $disasterId)
            ->get();

        return DataTables::of($evacueeInfo)
            ->addIndexColumn()
            ->addColumn('select', function ($row) {
                return '<input type="checkbox" class="rowCheckBox" value="' . $row->id . '">';
            })
            ->addColumn('action', function () use ($operation) {
                if (auth()->user()->is_disable == 1) return;

                return $operation == 'archived' ? '' :
                    '<div class="action-container"><button class="btn-table-update" id="updateEvacueeBtn"><i class="bi bi-pencil-square"></i>Update</button></div>';
            })
            ->rawColumns(['select', 'action'])
            ->make(true);
    }

    public function recordEvacueeInfo(Request $request)
    {
        $evacueeInfoValidation = Validator::make($request->all(), [
            'barangay'       => 'required',
            'form_type'      => 'required',
            'birth_date'     => 'required',
            'family_head'    => 'required',
            'pwd'            => 'required|numeric',
            'male'           => 'required|numeric',
            'female'         => 'required|numeric',
            'minors'         => 'required|numeric',
            'infants'        => 'required|numeric',
            'pregnant'       => 'required|numeric',
            'lactating'      => 'required|numeric',
            'disaster_id'    => 'required|numeric',
            'evacuation_id'  => 'required|numeric',
            'senior_citizen' => 'required|numeric'
        ]);

        if ($evacueeInfoValidation->fails())
            return response(['status' => 'warning', 'message' => implode('<br>', $evacueeInfoValidation->errors()->all())]);

        if ($this->evacuee
            ->where([
                'family_head' => $request->family_head,
                'birth_date' => $request->birth_date,
                'disaster_id' => $request->disaster_id,
            ])
            ->exists()
        ) {
            return response(['status' => 'warning', 'message' => 'Evacuee is already recorded']);
        }

        $latestRecord = null;

        if ($request->form_type == "new") {
            $this->familyController->recordFamilyRecord($request);
            $latestRecord = $this->familyRecord->latest()->first();
        } else {
            $this->familyController->updateFamilyRecord($request);
            $latestRecord = $this->familyRecord->latest('updated_at')->first();
        }

        $evacueeInfo = $request->only([
            'infants', 'minors', 'senior_citizen', 'pwd', 'pregnant', 'lactating', 'male',
            'female', 'barangay', 'family_head', 'birth_date', 'disaster_id', 'evacuation_id'
        ]);

        $evacueeInfo['individuals'] = $evacueeInfo['male'] + $evacueeInfo['female'];
        $evacueeInfo['family_id']   = $latestRecord->id;
        $evacueeInfo['user_id']     = auth()->user()->id;
        $evacueeInfo                = $this->evacuee->create($evacueeInfo);
        $this->logActivity->generateLog($evacueeInfo->id, $evacueeInfo->barangay, 'recorded a new evacuee information');
        // event(new ActiveEvacuees());

        return response()->json();
    }

    public function updateEvacueeInfo(Request $request, $evacueeId)
    {
        $evacueeInfoValidation = Validator::make($request->all(), [
            'barangay'       => 'required',
            'birth_date'     => 'required',
            'family_head'    => 'required',
            'pwd'            => 'required|numeric',
            'male'           => 'required|numeric',
            'female'         => 'required|numeric',
            'minors'         => 'required|numeric',
            'infants'        => 'required|numeric',
            'pregnant'       => 'required|numeric',
            'lactating'      => 'required|numeric',
            'disaster_id'    => 'required|numeric',
            'evacuation_id'  => 'required|numeric',
            'senior_citizen' => 'required|numeric'
        ]);

        if ($evacueeInfoValidation->fails())
            return response(['status' => 'warning', 'message' => implode('<br>', $evacueeInfoValidation->errors()->all())]);

        $this->familyController->updateFamilyRecord($request);

        $evacueeInfo = $request->only([
            'infants', 'minors', 'senior_citizen', 'pwd', 'pregnant', 'lactating', 'male',
            'female', 'barangay', 'family_head', 'birth_date', 'disaster_id', 'evacuation_id'
        ]);

        $evacueeInfo['individuals'] = $evacueeInfo['male'] + $evacueeInfo['female'];
        $evacueeInfo['updated_at']  = date('Y-m-d H:i:s');
        $evacueeInfo['family_id']   = $this->familyRecord->latest('updated_at')->first()->id;
        $evacueeInfo['user_id']     = auth()->user()->id;
        $evacueeInfo                = $this->evacuee->find($evacueeId)->update($evacueeInfo);
        $this->logActivity->generateLog($evacueeId, '', 'updated a evacuee information');
        // event(new ActiveEvacuees());

        return response()->json();
    }

    public function updateEvacueeStatus(Request $request)
    {
        $familyIds = [];

        foreach ($request->evacueeIds as $id) {
            $evacuee = $this->evacuee->find(intval($id));
            $familyIds[] = tap($evacuee)->update(['status' => $request->status, 'updated_at' => date('Y-m-d H:i:s')])->id;
        }

        $this->logActivity->generateLog(implode(', ', $familyIds), '', 'updated evacuee status to return home');

        return response()->json();
    }
}
