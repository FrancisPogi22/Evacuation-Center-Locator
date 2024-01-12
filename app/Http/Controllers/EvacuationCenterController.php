<?php

namespace App\Http\Controllers;

use App\Events\EvacuationCenter as EventsEvacuationCenter;
use App\Models\Evacuee;
use Illuminate\Http\Request;
use App\Models\ActivityUserLog;
use Yajra\DataTables\DataTables;
use App\Models\EvacuationCenter;
use App\Models\FeedBack;
use Illuminate\Support\Facades\Validator;

class EvacuationCenterController extends Controller
{
    private $evacuationCenter, $logActivity, $evacuee, $feedback;

    function __construct()
    {
        $this->evacuee          = new Evacuee;
        $this->feedback         = new FeedBack;
        $this->logActivity      = new ActivityUserLog;
        $this->evacuationCenter = new EvacuationCenter;
    }

    public function getEvacuationData($operation, $type)
    {
        return DataTables::of($this->evacuationCenter->where('is_archive', $type == "active" ? 0 : 1)->orderBy('name', 'asc')->get())
            ->addColumn('evacuees', function ($evacuation) use ($operation) {
                return $operation == "locator" ? $this->evacuee->where('evacuation_id', $evacuation->id)->sum('individuals') : '';
            })->addColumn('action', function ($evacuation) use ($operation, $type) {
                $facilityBtn = '<button class="btn-table-submit checkFacilities"><i class="bi bi-building-gear"></i>Facilities</button>';

                if ($operation == "locator")
                    return '<div class="action-container"><button class="btn-table-primary locateEvacuationCenter"><i class="bi bi-search"></i>Locate</button>' . $facilityBtn .
                        (basename(trim(request()->route()->getPrefix(), '/')) == "resident" ? '<button class="btn-table-update sendFeedback"><i class="bi bi-send"></i>Send Feedback</button>' : '') . '</div>';

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
                } else
                    $archiveBtn = '<button class="btn-table-remove" id="unArchiveEvacuationCenter"><i class="bi bi-box-arrow-up-left"></i>Unarchive</button>';

                return '<div class="action-container">' . $updateBtn . $archiveBtn . $facilityBtn . $selectOption . '</div>';
            })->rawColumns(['evacuees', 'action'])->make(true);
    }

    public function addFeedback(Request $request)
    {
        $idValidation = Validator::make($request->all(), ['evacuationId' => 'required']);
        $checkboxes = ['clean_facilities', 'responsive_aid', 'safe_evacuation', 'sufficient_food_supply', 'comfortable_evacuation', 'well_managed_evacuation'];

        if ($idValidation->fails() || !collect($checkboxes)->contains(fn ($checkbox) => $request->filled($checkbox)))
            return [
                'status'  => 'warning',
                'message' => ($idValidation->fails() ? $idValidation->errors()->first() . '<br>' : '') .
                    (!collect($checkboxes)->contains(fn ($checkbox) => $request->filled($checkbox)) ? 'Please select at least one option.' : '')
            ];

        $this->feedback->create($request->all());

        return response([]);
    }

    public function createEvacuationCenter(Request $request)
    {
        $evacuationCenterValidation = Validator::make($request->all(), [
            'name'         => 'required',
            'latitude'     => 'required',
            'longitude'    => 'required',
            'barangayName' => 'required',
            'facilities' => 'required|array',
        ]);

        if ($evacuationCenterValidation->fails()) return response(['status' => 'warning', 'message' => implode('<br>', $evacuationCenterValidation->errors()->all())]);

        $evacuationCenter = $this->evacuationCenter->create([
            'name'          => ucwords(trim($request->name)),
            'latitude'      => $request->latitude,
            'longitude'     => $request->longitude,
            'facilities'    => implode(',', $request->facilities),
            'barangay_name' => $request->barangayName,
        ]);
        $this->logActivity->generateLog("Added a new evacuation center(ID - $evacuationCenter->id)");
        event(new EventsEvacuationCenter());

        return response([]);
    }

    public function updateEvacuationCenter(Request $request, $evacuationId)
    {
        $evacuationCenterValidation = Validator::make($request->all(), [
            'name'         => 'required',
            'latitude'     => 'required',
            'longitude'    => 'required',
            'barangayName' => 'required',
            'facilities' => 'required|array',
        ]);

        if ($evacuationCenterValidation->fails()) return response(['status' => 'warning', 'message' => implode('<br>', $evacuationCenterValidation->errors()->all())]);

        $this->evacuationCenter->find($evacuationId)->update([
            'name'          => ucwords(trim($request->name)),
            'latitude'      => $request->latitude,
            'longitude'     => $request->longitude,
            'facilities'    => implode(',', $request->facilities),
            'barangay_name' => $request->barangayName
        ]);
        $this->logActivity->generateLog("Updated a evacuation center(ID - $evacuationId)");
        event(new EventsEvacuationCenter());

        return response([]);
    }

    public function archiveEvacuationCenter($evacuationId, $operation)
    {
        $this->evacuationCenter->find($evacuationId)->update([
            'status'     => $operation == "archive" ? "Inactive" : "Active",
            'is_archive' => $operation == "archive" ? 1 : 0
        ]);
        $this->logActivity->generateLog(ucfirst($operation) . " evacuation center(ID - $evacuationId)");
        event(new EventsEvacuationCenter());

        return response([]);
    }

    public function changeEvacuationStatus(Request $request, $evacuationId)
    {
        $this->evacuationCenter->find($evacuationId)->update(['status'  => $request->status]);
        $this->logActivity->generateLog("Changed a evacuation center(ID - $evacuationId) status to $request->status");
        event(new EventsEvacuationCenter());

        return response([]);
    }
}
