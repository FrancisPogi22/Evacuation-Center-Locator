<?php

namespace App\Http\Controllers;

use App\Models\Disaster;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ActivityUserLog;
use Illuminate\Support\Facades\Validator;

class DisasterController extends Controller
{
    private $disaster, $logActivity;

    function __construct()
    {
        $this->disaster = new Disaster;
        $this->logActivity = new ActivityUserLog;
    }

    // public function registerDisaster(DisasterRequest $request){

    //     $disasterData = [
    //         'disaster_type' => Str::of(trim($request->disaster_type))->title(),
    //     ];

    //     try{
    //         $this->disaster->registerDisasterObject($disasterData);
    //         Alert::success('Disaster Registered Successfully', 'Cabuyao City Disaster Risk Reduction Management Office');
    //     }catch(\Exception $e){
    //         Alert::error('Failed to Register Disaster', 'Cabuyao City Disaster Risk Reduction Management Office');
    //     }

    //     return back();
    // }

    public function getDisasterDetails($id)
    {
        if (request()->ajax()) {
            $data = Disaster::find($id);
            return response()->json(['result' => $data]);
        }
    }

    public function updateDisaster(Request $request, $disasterId)
    {
        $validatedDisaster = Validator::make($request->all(), [
            'type' => 'required',
        ]);

        if ($validatedDisaster->passes()) {
            $disasterData = [
                'type' => Str::ucfirst(trim($request->type))
            ];

            try {
                $this->disaster->find($disasterId)->update($disasterData);
                $this->logActivity->generateLog('Updating Disaster Information');

                return response()->json(['status' => 1]);
            } catch (\Exception $e) {
                return response()->json(['status' => 0]);
            }
        }

        return response()->json(['status' => 0, 'error' => $validatedDisaster->errors()->toArray()]);
    }

    public function removeDisaster($disasterId)
    {
        try {
            $this->disaster->find($disasterId)->delete();
            $this->logActivity->generateLog('Removing Disaster Information');

            return response()->json();
        } catch (\Exception $e) {
            return response()->json(['condition' => 0]);
        }
    }
}
