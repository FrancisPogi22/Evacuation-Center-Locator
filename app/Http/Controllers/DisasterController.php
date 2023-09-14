<?php

namespace App\Http\Controllers;

use App\Models\Disaster;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ActivityUserLog;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class DisasterController extends Controller
{
    private $disaster, $logActivity;

    public function __construct()
    {
        $this->disaster    = new Disaster;
        $this->logActivity = new ActivityUserLog;
    }
    public function displayDisasterInformation($operation)
    {
        $isArchive = $operation == "manage" ? 0 : 1;
        $disasterInformation = $this->disaster->where('is_archive', $isArchive)->orderBy('id', 'asc')->get();

        return DataTables::of($disasterInformation)
            ->addIndexColumn()
            ->addColumn('status', fn ($disaster) => '<div class="status-container"><div class="status-content bg-' . match ($disaster->status) {
                'On Going' => 'success',
                'Inactive' => 'danger'
            }
                . '">' . $disaster->status . '</div></div>')
            ->addColumn('action', function ($disaster) use ($operation) {
                if (auth()->user()->is_disable == 1) return;

                $updateButton  = '<button class="btn-table-update" id="updateDisaster"><i class="bi bi-pencil-square"></i>Update</button>';
                $statusOptions = ($disaster->status == 'On Going') ? '<option value="Inactive">Inactive</option>' : '<option value="On Going">On Going</option>';
                $selectStatus  = "";

                if ($operation == "manage") {
                    $selectStatus  = '<select class="form-select" id="changeDisasterStatus">' .
                        '<option value="" disabled selected hidden>Change Status</option>' . $statusOptions . '</select>';
                    $archiveButton = '<button class="btn-table-remove" id="archiveDisaster"><i class="bi bi-trash3-fill"></i>Archive</button>';
                } else {
                    $archiveButton = '<button class="btn-table-remove" id="unArchiveDisaster"><i class="bi bi-arrow-repeat"></i>Unarchive</button>';
                }

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
            'name'       => Str::of(trim($request->name))->title(),
            'status'     => "On Going",
            'user_id'    => auth()->user()->id,
            'is_archive' => 0
        ]);
        $this->logActivity->generateLog($disasterData->id, 'Created New Disaster');
        return response()->json();
    }

    public function updateDisasterData(Request $request, $disasterId)
    {
        $validatedDisasterValidation = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validatedDisasterValidation->fails())
            return response(['status' => 'warning', 'message' => $validatedDisasterValidation->errors()->first()]);

        $this->disaster->find($disasterId)->update([
            'name'    => Str::of(trim($request->name))->title(),
            'user_id' => auth()->user()->id
        ]);
        $this->logActivity->generateLog($disasterId, 'Updating Disaster Data');
        return response()->json();
    }

    public function archiveDisasterData($disasterId, $operation)
    {
        $this->disaster->find($disasterId)->update([
            'user_id'    => auth()->user()->id,
            'is_archive' => $operation == "archive" ? 1 : 0
        ]);

        $this->logActivity->generateLog($disasterId, $operation == "archive" ? "Archived Disaster" : "Unarchived Disaster");
        return response()->json();
    }

    public function changeDisasterStatus(Request $request, $disasterId)
    {
        $this->disaster->find($disasterId)->update([
            'status'  => $request->status,
            'user_id' => auth()->user()->id
        ]);
        $this->logActivity->generateLog($disasterId, 'Changed Disaster Status');
        return response()->json();
    }
}
