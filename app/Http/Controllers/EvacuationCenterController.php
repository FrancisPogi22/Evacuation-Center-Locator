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
        $this->logActivity      = new ActivityUserLog;
        $this->evacuationCenter = new EvacuationCenter;
    }

    public function getEvacuationData($operation, $type)
    {
        $evacuationCenterList = $this->evacuationCenter->where('is_archive', $type == "active" ? 0 : 1)->orderBy('name', 'asc')->get();

        return DataTables::of($evacuationCenterList)
            ->addIndexColumn()
            ->addColumn('evacuees', function ($evacuation) use ($operation) {
                return $operation == "locator" ? Evacuee::where('evacuation_id', $evacuation->id)->sum('individuals') : '';
            })->addColumn('action', function ($evacuation) use ($operation, $type) {
                if ($operation == "locator")
                    return '<button class="btn-table-primary locateEvacuationCenter"><i class="bi bi-search"></i>Locate</button>';

                if (auth()->user()->is_disable == 1) return;

                $selectOption = $archiveBtn = "";

                if ($type == "active") {
                    $statusOptions = implode('', array_map(function ($status) use ($evacuation) {
                        return $evacuation->status != $status ? '<option value="' . $status . '">' . $status . '</option>' : '';
                    }, ['Active', 'Inactive', 'Full']));
                    $archiveBtn = '<button class="btn-table-remove" id="archiveEvacuationCenter"><i class="bi bi-trash3-fill"></i>Archive</button>';
                    $selectOption =  '<select class="form-select" id="changeEvacuationStatus">' .
                        '<option value="" disabled selected hidden>Change Status</option>' . $statusOptions . '</select>';
                } else {
                    $archiveBtn = '<button class="btn-table-remove" id="unArchiveEvacuationCenter"><i class="bi bi-trash3-fill"></i>Unarchive</button>';
                }

                return '<div class="action-container">' .
                    '<button class="btn-table-update" id="updateEvacuationCenter"><i class="bi bi-pencil-square"></i>Update</button>' .
                    $archiveBtn . $selectOption . '</div>';
            })
            ->rawColumns(['evacuees', 'action'])
            ->make(true);
    }

    public function createEvacuationCenter(Request $request)
    {
        $evacuationCenterValidation = Validator::make($request->all(), [
            'name'         => 'required',
            'barangayName' => 'required',
            'latitude'     => 'required',
            'longitude'    => 'required'
        ]);

        if ($evacuationCenterValidation->fails())
            return response(['status' => 'warning', 'message' => implode('<br>', $evacuationCenterValidation->errors()->all())]);

        $evacuationCenterData =  $this->evacuationCenter->create([
            'user_id'       => auth()->user()->id,
            'name'          => Str::title(trim($request->name)),
            'barangay_name' => $request->barangayName,
            'latitude'      => $request->latitude,
            'longitude'     => $request->longitude,
            'status'        => 'Active',
            'is_archive'    => 0
        ]);
        $this->logActivity->generateLog($evacuationCenterData->id, $evacuationCenterData->name, 'added a new evacuation center');
        // event(new EvacuationCenterLocator());

        return response()->json();
    }

    public function updateEvacuationCenter(Request $request, $evacuationId)
    {
        $evacuationCenterValidation = Validator::make($request->all(), [
            'name'         => 'required',
            'barangayName' => 'required',
            'latitude'     => 'required',
            'longitude'    => 'required'
        ]);

        if ($evacuationCenterValidation->fails())
            return response(['status' => 'warning', 'message' => implode('<br>', $evacuationCenterValidation->errors()->all())]);

        $evacuationCenterData = $this->evacuationCenter->find($evacuationId);
        $evacuationCenterData->update([
            'user_id'       => auth()->user()->id,
            'name'          => Str::title(trim($request->name)),
            'barangay_name' => $request->barangayName,
            'latitude'      => $request->latitude,
            'longitude'     => $request->longitude
        ]);
        $this->logActivity->generateLog($evacuationId, $evacuationCenterData->name, 'updated a evacuation center');
        // event(new EvacuationCenterLocator());

        return response()->json();
    }

    public function archiveEvacuationCenter($evacuationId, $operation)
    {
        $evacuationCenterData = $this->evacuationCenter->find($evacuationId);
        $evacuationCenterData->update([
            'user_id'    => auth()->user()->id,
            'is_archive' => $operation == "archive" ? 1 : 0
        ]);
        $this->logActivity->generateLog($evacuationId, $evacuationCenterData->name, $operation == "archive" ? "archived evacuation center" : "unarchived evacuation center");
        // event(new EvacuationCenterLocator());

        return response()->json();
    }

    public function changeEvacuationStatus(Request $request, $evacuationId)
    {
        $evacuationCenterData = $this->evacuationCenter->find($evacuationId);
        $evacuationCenterData->update([
            'user_id' => auth()->user()->id,
            'status'  => $request->status
        ]);
        $this->logActivity->generateLog($evacuationId, $evacuationCenterData->name, 'changed a evacuation center status');
        // event(new EvacuationCenterLocator());

        return response()->json();
    }
}
