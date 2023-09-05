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
        $this->disaster = new Disaster;
        $this->logActivity = new ActivityUserLog;
    }
    public function displayDisasterInformation(Request $request)
    {
        if (!$request->ajax()) return view('userpage.disaster.disaster');

        $disasterInformation = $this->disaster->where('is_archive', 0)->orderBy('id', 'desc')->get();

        return DataTables::of($disasterInformation)
            ->addIndexColumn()
            ->addColumn('status', fn ($disaster) => '<div class="status-container"><div class="status-content bg-' . match ($disaster->status) {
                'On Going' => 'success',
                'Inactive' => 'danger'
            }
                . '">' . $disaster->status . '</div></div>')
            ->addColumn('action', function ($disaster) {
                if (auth()->user()->is_disable == 1) return;

                $statusOptions = $disaster->status == 'On Going' ? '<option value="Inactive">Inactive</option>' : '<option value="On Going">On Going</option>';

                return '<div class="action-container">' .
                    '<button class="btn-table-update" id="updateDisaster"><i class="bi bi-pencil-square"></i>Update</button>' .
                    '<button class="btn-table-remove" id="archiveDisaster"><i class="bi bi-trash3-fill"></i>Archive</button>' .
                    '<select class="form-select" id="changeDisasterStatus">' .
                    '<option value="" disabled selected hidden>Change Status</option>' . $statusOptions . '</select></div>';
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

        $this->disaster->create([
            'name' => Str::of(trim($request->name))->title(),
            'status' => "On Going",
            'is_archive' => 0
        ]);
        $this->logActivity->generateLog('Creating Disaster Data');
        return response()->json();
    }

    public function updateDisasterData(Request $request, $disasterId)
    {
        $validatedDisasterValidation = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validatedDisasterValidation->fails())
            return response()->json(['status' => 'warning', 'message' => $validatedDisasterValidation->errors()->first()]);

        $this->disaster->find($disasterId)->update([
            'name' => Str::of(trim($request->name))->title()
        ]);
        $this->logActivity->generateLog('Updating Disaster Data');
        return response()->json();
    }

    public function archiveDisasterData($disasterId)
    {
        $this->disaster->find($disasterId)->update([
            'status' => 'Archived',
            'is_archive' => 1
        ]);
        $this->logActivity->generateLog('Archiving Disaster Data');
        return response()->json();
    }

    public function changeDisasterStatus(Request $request, $disasterId)
    {
        $this->disaster->find($disasterId)->update([
            'status' => $request->status
        ]);
        $this->logActivity->generateLog('Changing Disaster Status');
        return response()->json();
    }
}
