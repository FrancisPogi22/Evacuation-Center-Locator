<?php

namespace App\Http\Controllers;

use App\Events\Disaster as EventsDisaster;
use App\Models\Evacuee;
use App\Models\Disaster;
use Illuminate\Http\Request;
use App\Models\ActivityUserLog;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class DisasterController extends Controller
{
    private $disaster, $evacuee, $logActivity;

    public function __construct()
    {
        $this->evacuee     = new Evacuee;
        $this->disaster    = new Disaster;
        $this->logActivity = new ActivityUserLog;
    }

    public function displayDisasterInformation($operation, $year)
    {
        $disasterInformation = $this->disaster
            ->where('is_archive', $operation == "manage" ? 0 : 1)
            ->when($year != "none", fn ($query) => $query->where('year', $year))->orderBy('id', 'desc')
            ->get();

        return $year != "none" ? $disasterInformation :
            DataTables::of($disasterInformation)
            ->addColumn('status', fn ($disaster) => '<div class="status-container"><div class="status-content bg-' . match ($disaster->status) {
                'On Going' => 'success',
                'Inactive' => 'danger'
            }
                . '">' . $disaster->status . '</div></div>')
            ->addColumn('action', function ($disaster) use ($operation) {
                return '<div class="action-container">' .
                    ($operation == "manage" ? '<button class="btn-table-update" id="updateDisaster"><i class="bi bi-pencil-square"></i>Update</button>' .
                        ($this->evacuee->where('disaster_id', $disaster->id)->where('status', 'Evacuated')->count() == 0 ?
                            '<button class="btn-table-remove" id="archiveDisaster"><i class="bi bi-box-arrow-in-down-right"></i>Archive</button>' : '') .
                        '<select class="form-select changeDisasterStatus"><option value="" disabled selected hidden>Change Status</option>' .
                        ($disaster->status == 'On Going' ? '<option value="Inactive">Inactive</option>' : '<option value="On Going">On Going</option>') .
                        '</select>' : '<button class="btn-table-remove" id="unArchiveDisaster"><i class="bi bi-box-arrow-up-left"></i>Unarchive</button>'
                    ) . '</div>';
            })->rawColumns(['status', 'action'])->make(true);
    }

    public function createDisasterData(Request $request)
    {
        $validatedDisasterValidation = Validator::make($request->all(), ['name' => 'required']);

        if ($validatedDisasterValidation->fails()) return response(['status' => 'warning', 'message' => $validatedDisasterValidation->errors()->first()]);

        $disasterData = $this->disaster->create([
            'name'    => ucwords(trim($request->name)),
            'year'    => date('Y')
        ]);
        $this->logActivity->generateLog("Added a new disaster(ID - $disasterData->id)");
        event(new EventsDisaster());

        return response([]);
    }

    public function updateDisasterData(Request $request, $disasterId)
    {
        $validatedDisasterValidation = Validator::make($request->all(), ['name' => 'required']);

        if ($validatedDisasterValidation->fails()) return response(['status' => 'warning', 'message' => $validatedDisasterValidation->errors()->first()]);

        $this->disaster->find($disasterId)->update(['name' => ucwords(trim($request->name))]);
        $this->logActivity->generateLog("Updated info of disaster(ID - $disasterId-)");
        event(new EventsDisaster());

        return response([]);
    }

    public function archiveDisasterData($disasterId, $operation)
    {
        $archiveValue = $operation == 'archive' ? 1 : 0;
        $this->disaster->find($disasterId)->update([
            'status'     => 'Inactive',
            'is_archive' => $archiveValue
        ]);
        $this->evacuee->where('disaster_id', $disasterId)->update(['is_archive' => $archiveValue]);
        $this->logActivity->generateLog(ucwords($operation) . " Added a new disaster(ID - $disasterId)");
        event(new EventsDisaster());

        return response([]);
    }

    public function changeDisasterStatus(Request $request, $disasterId)
    {
        $this->disaster->find($disasterId)->update(['status'  => $request->status]);
        $this->logActivity->generateLog("Changed a disaster(ID - $disasterId) status to $request->status");
        event(new EventsDisaster());

        return response([]);
    }
}