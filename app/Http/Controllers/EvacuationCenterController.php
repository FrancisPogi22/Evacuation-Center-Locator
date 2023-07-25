<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ActivityUserLog;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\Models\EvacuationCenter;

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
        $evacuationCenterList = $this->evacuationCenter->all();

        return DataTables::of($evacuationCenterList)
            ->addIndexColumn()
<<<<<<< Updated upstream
            ->addColumn('action', function () use ($operation) {
=======
            ->addColumn('status', function ($row) {
                $color = match ($row->status) {
                    'Active' => 'green',
                    'Inactive' => 'red',
                    'Full' => 'orange'
                };

                return '<div class="flex  justify-center"><div class="bg-' . $color . '-600 status-container">' . $row->status . '</div></div>';
            })->addColumn('action', function () use ($operation) {
>>>>>>> Stashed changes
                if ($operation == "locator") {
                    return '<button class="btn-table-primary p-2 text-white locateEvacuationCenter"><i class="bi bi-search pr-2"></i>Locate</button>';
                } else {
                    if (auth()->user()->status == "Active") {
                        return '<div class="flex justify-around actionContainer"><button class="btn-table-edit mr-2 updateEvacuationCenter"><i class="bi bi-pencil-square pr-2"></i>Edit</button>' .
                            '<button class="btn-table-remove mr-2 removeEvacuationCenter"><i class="bi bi-trash3-fill pr-2"></i>Remove</button>' .
                            '<select class="custom-select w-44 bg-blue-500 text-white changeEvacuationStatus">
                                    <option value="" disabled selected hidden>Change Status</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                    <option value="Full">Full</option>
                                </select></div>';
                    }
                }

                return '<span class="text-sm">Currently Disabled.</span>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function createEvacuationCenter(Request $request)
    {
        $validateEvacuationCenter = Validator::make($request->all(), [
            'name' => 'required',
            'barangayName' => 'required',
            'latitude' => 'required',
            'longitude' => 'required'
        ]);

        if ($validateEvacuationCenter->passes()) {
            $this->evacuationCenter->create([
                'name' => Str::ucfirst($request->name),
                'barangay_name' => $request->barangayName,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'status' => 'Active'
            ]);
            $this->logActivity->generateLog('Added new evacuation center');

            return response()->json();
        }

        return response(['status' => "warning", 'message' => $validateEvacuationCenter->errors()->first()]);
    }

    public function updateEvacuationCenter(Request $request, $evacuationId)
    {
        $validateEvacuationCenter = Validator::make($request->all(), [
            'name' => 'required',
            'barangayName' => 'required',
            'latitude' => 'required',
            'longitude' => 'required'
        ]);

        if ($validateEvacuationCenter->passes()) {
            $this->evacuationCenter->find($evacuationId)->update([
                'name' => Str::ucfirst(trim($request->name)),
                'barangay_name' => $request->barangayName,
                'latitude' => trim($request->latitude),
                'longitude' => trim($request->longitude)
            ]);
            $this->logActivity->generateLog('Updating evacuation center');

            return response()->json();
        }

        return response(['status' => "warning", 'message' => $validateEvacuationCenter->errors()->first()]);
    }

    public function removeEvacuationCenter($evacuationId)
    {
        $this->evacuationCenter->find($evacuationId)->delete();
        $this->logActivity->generateLog('Removing evacuation center');

        return response()->json();
    }

    public function changeEvacuationStatus(Request $request, $evacuationId)
    {
        $this->evacuationCenter->find($evacuationId)->update([
            'status' => $request->status
        ]);
        $this->logActivity->generateLog('Changing evacuation center status');

        return response()->json();
    }
}
