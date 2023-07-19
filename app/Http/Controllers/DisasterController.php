<?php

namespace App\Http\Controllers;

use App\Models\Disaster;
use Illuminate\Http\Request;
use App\Models\ActivityUserLog;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class DisasterController extends Controller
{
    private $disaster, $logActivity;

    public function __construct()
    {
        $this->disaster = new Disaster;
        $this->logActivity = new ActivityUserLog;
    }
    public function displayDisasterInformation(Request $request)
    {
        if ($request->ajax()) {
            $disasterInformation = $this->disaster->where('is_archive', 0)->get();

            return DataTables::of($disasterInformation)
                ->addIndexColumn()
                ->addColumn('action', function () {
                    return '<div class="flex justify-around actionContainer"><button class="btn-table-edit updateDisaster"><i class="bi bi-pencil-square pr-2"></i>Edit</button>' .
                        '<button class="btn-table-remove removeDisaster"><i class="bi bi-trash3-fill pr-2"></i>Remove</button>' .
                        '<select class="custom-select w-44 bg-blue-500 text-white changeDisasterStatus">
                        <option value="" disabled selected hidden>Change Status</option>
                        <option value="On Going">On Going</option>
                        <option value="Inactive">Inactive</option>
                    </select></div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('userpage.disaster.disaster');
    }

    public function createDisasterData(Request $request)
    {
        $validatedDisasterData = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validatedDisasterData->passes()) {
            $this->disaster->create([
                'name' => $request->name,
                'location' => $request->location,
                'status' => "On Going",
                'is_archive' => 0
            ]);
            $this->logActivity->generateLog('Creating Disaster Data');

            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'warning', 'message' => $validatedDisasterData->errors()->first()]);
    }

    public function updateDisasterData(Request $request, $disasterId)
    {
        $validatedDisasterData = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validatedDisasterData->passes()) {
            $this->disaster->find($disasterId)->update([
                'name' => $request->name,
                'location' => $request->location
            ]);
            $this->logActivity->generateLog('Updating Disaster Data');

            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'warning', 'message' => $validatedDisasterData->errors()->first()]);
    }

    public function removeDisasterData($disasterId)
    {
        $this->disaster->find($disasterId)->update([
            'is_archive' => 1
        ]);
        $this->logActivity->generateLog('Removing Disaster Data');

        return response()->json();
    }

    public function changeDisasterStatus(Request $request, $disasterId)
    {
        $this->disaster->find($disasterId)->update([
            'status' => $request->status
        ]);
        $this->logActivity->generateLog('Change Disaster Status');

        return response()->json();
    }
}
