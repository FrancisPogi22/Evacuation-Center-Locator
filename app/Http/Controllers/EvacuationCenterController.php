<?php

namespace App\Http\Controllers;


use App\Models\Evacuee;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ActivityUserLog;
use Yajra\DataTables\DataTables;
use App\Models\EvacuationCenter;
use App\Events\EvacuationCenterLocator;
use Illuminate\Support\Facades\Validator;

class EvacuationCenterController extends Controller
{
    private $evacuationCenter, $logActivity;

    function __construct()
    {
        $this->logActivity = new ActivityUserLog;
        $this->evacuationCenter = new EvacuationCenter;
    }

    public function getEvacuationData($operation)
    {
        $evacuationCenterList = $this->evacuationCenter->orderBy('name', 'asc')->where('is_archive', 0)->get();

        return DataTables::of($evacuationCenterList)
            ->addIndexColumn()
            ->addColumn('capacity', function ($evacuation) use ($operation) {
                return $operation == "locator" ? Evacuee::where('evacuation_assigned', $evacuation->name)->sum('individuals') . '/' . $evacuation->capacity : $evacuation->capacity;
            })->addColumn('action', function ($evacuation) use ($operation) {
                if ($operation == "locator")
                    return '<button class="btn-table-primary text-white locateEvacuationCenter"><i class="bi bi-search"></i>Locate</button>';

                if (auth()->user()->is_disable == 1) return;

                $statusOptions = implode('', array_map(function ($status) use ($evacuation) {
                    return $evacuation->status != $status ? '<option value="' . $status . '">' . $status . '</option>' : '';
                }, ['Active', 'Inactive', 'Full']));

                return '<div class="action-container">' .
                    '<button class="btn-table-update" id="updateEvacuationCenter"><i class="bi bi-pencil-square"></i>Update</button>' .
                    '<button class="btn-table-remove" id="archiveEvacuationCenter"><i class="bi bi-trash3-fill"></i>Archive</button>' .
                    '<select class="form-select" id="changeEvacuationStatus">' .
                    '<option value="" disabled selected hidden>Change Status</option>' .
                    $statusOptions . '</select></div>';
            })
            ->rawColumns(['capacity', 'action'])
            ->make(true);
    }

    public function createEvacuationCenter(Request $request)
    {
        $evacuationCenterValidation = Validator::make($request->all(), [
            'name' => 'required',
            'barangayName' => 'required',
            'capacity' => 'required|numeric|min:1',
            'latitude' => 'required',
            'longitude' => 'required'
        ]);

        if ($evacuationCenterValidation->fails())
            return response(['status' => 'warning', 'message' => implode('<br>', $evacuationCenterValidation->errors()->all())]);

        $evacuationCenterData =  $this->evacuationCenter->create([
            'user_id' => auth()->user()->id,
            'name' => Str::of(trim($request->name))->title(),
            'barangay_name' => $request->barangayName,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'capacity' => trim($request->capacity),
            'status' => 'Active',
            'is_archive' => 0,
        ]);
        $this->logActivity->generateLog($evacuationCenterData->id, 'Added new evacuation center');
        // event(new EvacuationCenterLocator());
        return response()->json();
    }

    public function updateEvacuationCenter(Request $request, $evacuationId)
    {
        $evacuationCenterValidation = Validator::make($request->all(), [
            'name' => 'required',
            'barangayName' => 'required',
            'latitude' => 'required',
            'longitude' => 'required'
        ]);

        if ($evacuationCenterValidation->fails())
            return response(['status' => 'warning', 'message' => implode('<br>', $evacuationCenterValidation->errors()->all())]);

        $this->evacuationCenter->find($evacuationId)->update([
            'user_id' => auth()->user()->id,
            'name' => Str::of(trim($request->name))->title(),
            'barangay_name' => $request->barangayName,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'capacity' => trim($request->capacity)
        ]);
        $this->logActivity->generateLog($evacuationId, 'Updated evacuation center');
        // event(new EvacuationCenterLocator());
        return response()->json();
    }

    public function archiveEvacuationCenter($evacuationId)
    {
        $this->evacuationCenter->find($evacuationId)->update([
            'user_id' => auth()->user()->id,
            'status' => "Archived",
            'is_archive' => 1,
        ]);
        $this->logActivity->generateLog($evacuationId, 'Archived evacuation center');
        // event(new EvacuationCenterLocator());
        return response()->json();
    }

    public function changeEvacuationStatus(Request $request, $evacuationId)
    {
        $this->evacuationCenter->find($evacuationId)->update([
            'user_id' => auth()->user()->id,
            'status' => $request->status
        ]);
        $this->logActivity->generateLog($evacuationId, 'Changed evacuation center status');
        // event(new EvacuationCenterLocator());
        return response()->json();
    }
}
