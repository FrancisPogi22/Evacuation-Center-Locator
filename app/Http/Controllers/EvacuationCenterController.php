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
    private $evacuationCenter, $logActivity, $evacuee;

    function __construct()
    {
        $this->logActivity      = new ActivityUserLog;
        $this->evacuationCenter = new EvacuationCenter;
        $this->evacuee          = new Evacuee;
    }

    public function getEvacuationData($operation, $type)
    {
        $evacuationCenterList = $this->evacuationCenter->where('is_archive', $type == "active" ? 0 : 1)->orderBy('name', 'asc')->get();

        return DataTables::of($evacuationCenterList)
            ->addColumn('evacuees', function ($evacuation) use ($operation) {
                return $operation == "locator" ? $this->evacuee->where('evacuation_id', $evacuation->id)->sum('individuals') : '';
            })->addColumn('action', function ($evacuation) use ($operation, $type) {
                if ($operation == "locator")
                    return '<button class="btn-table-primary locateEvacuationCenter"><i class="bi bi-search"></i>Locate</button>';

                $selectOption = $updateBtn = $archiveBtn = "";

                if ($type == "active") {
                    $evacuees = $this->evacuee->where('evacuation_id', $evacuation->id)->where('status', 'Evacuated')->count();

                    $optionsArray = $evacuees > 0 ? ['Active', 'Full'] : ($evacuation->status == 'Inactive' ? ['Active', 'Inactive'] : ['Active', 'Inactive', 'Full']);

                    $statusOptions = implode('', array_map(function ($status) use ($evacuation) {
                        return $evacuation->status != $status ? '<option value="' . $status . '">' . $status . '</option>' : '';
                    }, $optionsArray));
                    $updateBtn = $operation == "manage" ? '<button class="btn-table-update" id="updateEvacuationCenter"><i class="bi bi-pencil-square"></i>Update</button>' : '';
                    $archiveBtn =  $evacuees == 0 ? '<button class="btn-table-remove" id="archiveEvacuationCenter"><i class="bi bi-box-arrow-in-down-right"></i>Archive</button>' : '';
                    $selectOption =  '<select class="form-select changeEvacuationStatus">' .
                        '<option value="" disabled selected hidden>Change Status</option>' . $statusOptions . '</select>';
                } else {
                    $updateBtn = '';
                    $archiveBtn = '<button class="btn-table-remove" id="unArchiveEvacuationCenter"><i class="bi bi-box-arrow-up-left"></i>Unarchive</button>';
                }

                return '<div class="action-container">' . $updateBtn . $archiveBtn . $selectOption . '</div>';
            })->rawColumns(['evacuees', 'action'])->make(true);
    }

    public function createEvacuationCenter(Request $request)
    {
        $evacuationCenterValidation = Validator::make($request->all(), [
            'name'         => 'required',
            'latitude'     => 'required',
            'longitude'    => 'required',
            'barangayName' => 'required'
        ]);

        if ($evacuationCenterValidation->fails()) return response(['status' => 'warning', 'message' => implode('<br>', $evacuationCenterValidation->errors()->all())]);

        $evacuationCenter =  $this->evacuationCenter->create([
            'name'          => Str::title(trim($request->name)),
            'latitude'      => $request->latitude,
            'longitude'     => $request->longitude,
            'barangay_name' => $request->barangayName
        ]);
        $this->logActivity->generateLog('Added a new evacuation center(ID - ' . $evacuationCenter->id . ')');
        event(new EvacuationCenterLocator());

        return response([]);
    }

    public function updateEvacuationCenter(Request $request, $evacuationId)
    {
        $evacuationCenterValidation = Validator::make($request->all(), [
            'name'         => 'required',
            'latitude'     => 'required',
            'longitude'    => 'required',
            'barangayName' => 'required'
        ]);

        if ($evacuationCenterValidation->fails()) return response(['status' => 'warning', 'message' => implode('<br>', $evacuationCenterValidation->errors()->all())]);

        $this->evacuationCenter->find($evacuationId)->update([
            'name'          => Str::title(trim($request->name)),
            'latitude'      => $request->latitude,
            'longitude'     => $request->longitude,
            'barangay_name' => $request->barangayName
        ]);
        $this->logActivity->generateLog('Updated a evacuation center(ID - ' . $evacuationId . ')');
        event(new EvacuationCenterLocator());

        return response([]);
    }

    public function archiveEvacuationCenter($evacuationId, $operation)
    {
        $this->evacuationCenter->find($evacuationId)->update([
            'status'     => $operation == "archive" ? "Inactive" : "Active",
            'is_archive' => $operation == "archive" ? 1 : 0
        ]);
        $this->logActivity->generateLog(Str::ucfirst($operation) . ' evacuation center(ID - ' . $evacuationId . ')');
        event(new EvacuationCenterLocator());

        return response([]);
    }

    public function changeEvacuationStatus(Request $request, $evacuationId)
    {
        $this->evacuationCenter->find($evacuationId)->update([
            'status'  => $request->status
        ]);
        $this->logActivity->generateLog('Changed a evacuation center(ID - ' . $evacuationId . ') status to ' . $request->status);
        event(new EvacuationCenterLocator());

        return response([]);
    }
}
