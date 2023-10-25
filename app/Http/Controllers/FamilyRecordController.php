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
            ->get() : $this->familyRecord->where('id', $data)->first();

        return response($familyData);
    }

    public function recordFamilyRecord(Request $request)
    {
        $familyRecord           = $request->all();
        $familyRecordValidation = Validator::make($familyRecord, [
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

        $familyRecord['individuals'] = $familyRecord['male'] + $familyRecord['female'];
        $familyRecord['user_id']     = auth()->user()->id;
        $familyRecord                = $this->familyRecord->create($familyRecord);
        $this->logActivity->generateLog($familyRecord->id, $familyRecord->barangay, 'recorded a new family record');

        return response()->json();
    }

    public function updateFamilyRecord(Request $request)
    {
        $familyRecord           = $request->all();
        $familyRecordValidation = Validator::make($familyRecord, [
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

        $familyRecord['individuals'] = $familyRecord['male'] + $familyRecord['female'];
        $familyRecord['user_id']     = auth()->user()->id;
        Log::info($request->family_id);
        $familyRecord                = $this->familyRecord->find($request->family_id)->update($familyRecord);

        return response()->json();
    }
}
