<?php

namespace App\Http\Controllers;

use App\Models\Evacuee;
use App\Models\Barangay;
use App\Models\Disaster;
use Illuminate\Support\Str;
use App\Models\EvacuationCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class RecordEvacueeController extends Controller{

    private $evacuee;

    function __construct(){
        $this->evacuee = new Evacuee;
    }

    public function barangayList(){
        $barangayLists = Barangay::all()->sortBy('barangay_name');

        return $barangayLists;
    }

    public function evacuationCenterList(){
        $evacuationCenterLists = EvacuationCenter::all()->sortBy('evacuation_center_name');

        return $evacuationCenterLists;
    }

    public function disasterList(){
        $disasterLists = Disaster::all()->sortBy('disaster_name');

        return $disasterLists;
    }

    public function recordEvacueeInfo(Request $request){
        
        $validatedEvacueeForm = Validator::make($request->all(), [
            'first_name' => 'required|regex:/^[a-zA-Z\s]+$/u',
            'middle_name' => 'required|regex:/^[a-zA-Z\s]+$/u',
            'last_name' => 'required|regex:/^[a-zA-Z\s]+$/u',
            'suffix' => 'required',
            'contact_number' => 'required|numeric|digits:11',
            'age' => 'required|numeric',
            'gender' => 'required',
            'address' => 'required',
            'barangay' => 'required',
            'evacuation_center' => 'required',
            'disaster' => 'required',
        ]);

        if($validatedEvacueeForm->passes()) {

            $evacueeObject = [
                'evacuee_first_name' => Str::ucfirst(trim($request->first_name)),
                'evacuee_middle_name' => Str::ucfirst(trim($request->middle_name)),
                'evacuee_last_name' => Str::ucfirst(trim($request->last_name)),
                'evacuee_suffix' => Str::ucfirst(trim($request->suffix)),
                'evacuee_contact_number' => trim($request->contact_number),
                'evacuee_age' => trim($request->age),
                'evacuee_gender' => trim($request->gender),
                'evacuee_address' => trim($request->address),
                'barangay_id' => $request->barangay,
                'disaster_id' => $request->disaster,
                'evacuation_assigned' => $request->evacuation_center,
            ];

            try{
                $this->evacuee->recordEvacueeObject($evacueeObject);
                Alert::success('Evacuee Information Recorded Successfully', 'Cabuyao City Disaster Risk Reduction Management Office');
            }catch(\Exception $e){
                Alert::error('Failed to Record Evacuee Information', 'Cabuyao City Disaster Risk Reduction Management Office');
            }
            
            return response()->json(['condition' => 1]);
        }
        
           
        return response()->json(['condition' => 0, 'error' => $validatedEvacueeForm->errors()->toArray()]);
    }
}