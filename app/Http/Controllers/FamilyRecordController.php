<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FamilyRecord;
use App\Models\ActivityUserLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FamilyRecordController extends Controller
{
    private $familyRecord, $logActivity;

    public function __construct()
    {
        $this->familyRecord = new FamilyRecord;
        $this->logActivity  = new ActivityUserLog;
    }

    public function getFamilyData($data, $operation)
    {
        $familyData = $operation == 'searchData' ?
            $this->familyRecord->select('id', 'family_head', 'birth_date')
            ->where('family_head', 'LIKE', "%{$data}%")
            ->get() :
            $this->familyRecord->where('id', $data)->first();

        return response()->json($familyData);
    }

    public function recordFamilyRecord(Request $request)
    {
        $familyRecordValidation = Validator::make($request->all(), [
            'family_head'    => 'required',
            'birth_date'     => 'required',
            'barangay'       => 'required',
            'infants'        => 'required|numeric',
            'minors'         => 'required|numeric',
            'senior_citizen' => 'required|numeric',
            'pwd'            => 'required|numeric',
            'pregnant'       => 'required|numeric',
            'lactating'      => 'required|numeric',
            'male'           => 'required|numeric',
            'female'         => 'required|numeric'
        ]);

        if ($familyRecordValidation->fails())
            return response(['status' => 'warning', 'message' => implode('<br>', $familyRecordValidation->errors()->all())]);

        $familyRecord = $request->only([
            'infants', 'minors', 'senior_citizen', 'pwd', 'pregnant', 'lactating', 'male',
            'female', 'barangay', 'family_head', 'birth_date'
        ]);

        $familyRecord['individuals'] = $familyRecord['male'] + $familyRecord['female'];
        $familyRecord['user_id']     = auth()->user()->id;
        $familyRecord                = $this->familyRecord->create($familyRecord);
        $this->logActivity->generateLog($familyRecord->id, $familyRecord->barangay, 'recorded a new family record');

        return $familyRecord->id;
    }

    public function updateFamilyRecord(Request $request)
    {
        $familyRecordValidation = Validator::make($request->all(), [
            'family_head'    => 'required',
            'birth_date'     => 'required',
            'barangay'       => 'required',
            'infants'        => 'required|numeric',
            'minors'         => 'required|numeric',
            'senior_citizen' => 'required|numeric',
            'pwd'            => 'required|numeric',
            'pregnant'       => 'required|numeric',
            'lactating'      => 'required|numeric',
            'male'           => 'required|numeric',
            'female'         => 'required|numeric'
        ]);

        if ($familyRecordValidation->fails())
            return response(['status' => 'warning', 'message' => implode('<br>', $familyRecordValidation->errors()->all())]);

        $familyRecord = $request->only([
            'infants', 'minors', 'senior_citizen', 'pwd', 'pregnant', 'lactating', 'male',
            'female', 'barangay', 'family_head', 'birth_date'
        ]);

        $familyRecord['individuals'] = $familyRecord['male'] + $familyRecord['female'];
        $familyRecord['user_id']     = auth()->user()->id;
        $familyRecord                = $this->familyRecord->find($request->family_id)->update($familyRecord);

        return $request->family_id;
    }
}
