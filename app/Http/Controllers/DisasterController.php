<?php

namespace App\Http\Controllers;

use App\Models\Disaster;
use App\Models\Evacuee;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ActivityUserLog;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class DisasterController extends Controller
{
    private $disaster, $evacuee, $logActivity;

    public function __construct()
    {
        $this->disaster    = new Disaster;
        $this->evacuee     = new Evacuee;
        $this->logActivity = new ActivityUserLog;
    }
    public function displayDisasterInformation($operation, $year)
    {
        $disasterInformation = $this->disaster
            ->where('is_archive', $operation == "manage" ? 0 : 1)
            ->when($year != "none", fn ($query) => $query->where('year', $year))->orderBy('id', 'desc')
            ->get();

        if ($year != "none")
            return $disasterInformation;

        return DataTables::of($disasterInformation)
            ->addIndexColumn()
            ->addColumn('status', fn ($disaster) => '<div class="status-container"><div class="status-content bg-' . match ($disaster->status) {
                'On Going' => 'success',
                'Inactive' => 'danger'
            }
                . '">' . $disaster->status . '</div></div>')
            ->addColumn('action', function ($disaster) use ($operation) {
                if (auth()->user()->is_disable == 1) return;
                $evacuees = $this->evacuee->where('disaster_id', $disaster->id)->where('status', 'Evacuated')->count();

                $updateButton  = $operation == "manage" ? '<button class="btn-table-update" id="updateDisaster"><i class="bi bi-pencil-square"></i>Update</button>' : '';
                $statusOptions = $disaster->status == 'On Going' ? '<option value="Inactive">Inactive</option>' : '<option value="On Going">On Going</option>';
                $selectStatus  = $operation == "manage" ? '<select class="form-select" id="changeDisasterStatus"><option value="" disabled selected hidden>Change Status</option>' . $statusOptions . '</select>' : '';
                $archiveButton = $operation == "manage" ?
                    ($evacuees == 0 ? '<button class="btn-table-remove" id="archiveDisaster"><i class="bi bi-box-arrow-in-down-right"></i>Archive</button>' : '') :
                    '<button class="btn-table-remove" id="unArchiveDisaster"><i class="bi bi-box-arrow-up-left"></i>Unarchive</button>';

                return '<div class="action-container">' . $updateButton . $archiveButton . $selectStatus . '</div>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function createDisasterData(Request $request)
    {
        $validatedDisasterValidation = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validatedDisasterValidation->fails())
            return response(['status' => 'warning', 'message' => $validatedDisasterValidation->errors()->first()]);

        $disasterData = $this->disaster->create([
            'name'       => Str::title(trim($request->name)),
            'year'       => date('Y'),
            'status'     => "On Going",
            'user_id'    => auth()->user()->id,
            'is_archive' => 0
        ]);
        $this->logActivity->generateLog($disasterData->id, $disasterData->name, 'added a new disaster data');

        return response()->json();
    }

    public function updateDisasterData(Request $request, $disasterId)
    {
        $validatedDisasterValidation = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validatedDisasterValidation->fails())
            return response(['status' => 'warning', 'message' => $validatedDisasterValidation->errors()->first()]);

        $disasterData = $this->disaster->find($disasterId);
        $disasterData->update([
            'name'    => Str::title(trim($request->name)),
            'user_id' => auth()->user()->id
        ]);
        $this->logActivity->generateLog($disasterId, $disasterData->name, 'updated a disaster data');

        return response()->json();
    }

    public function archiveDisasterData($disasterId, $operation)
    {
        $disasterData = $this->disaster->find($disasterId);
        $disasterData->update([
            'user_id'    => auth()->user()->id,
            'status'     => 'Inactive',
            'is_archive' =>  $operation == 'archive' ? 1 : 0
        ]);

        $this->evacuee->where('disaster_id', $disasterId)->update(['is_archive' => $archiveValue]);
        $this->logActivity->generateLog($disasterId, $disasterData->name, ($operation == "archive" ? "archived" : "unarchived") . " a disaster data");

        return response()->json();
    }

    public function changeDisasterStatus(Request $request, $disasterId)
    {
        $disasterData = $this->disaster->find($disasterId);
        $disasterData->update([
            'status'  => $request->status,
            'user_id' => auth()->user()->id
        ]);
        $this->logActivity->generateLog($disasterId, $disasterData->name, 'changed a disaster status');

        return response()->json();
    }
}
