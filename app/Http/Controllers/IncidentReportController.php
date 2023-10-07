<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ReportLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\IncidentReport;
use App\Models\ActivityUserLog;
use Yajra\DataTables\DataTables;
use App\Events\NotificationEvent;
use App\Events\IncidentReportEvent;
use Illuminate\Support\Facades\Validator;

class IncidentReportController extends Controller
{
    private $reportEvent, $reportLog, $logActivity, $incidentReport;

    function __construct()
    {
        $this->reportLog      = new ReportLog;
        $this->reportEvent    = new IncidentReportEvent;
        $this->incidentReport = new IncidentReport;
        $this->logActivity    = new ActivityUserLog;
    }

    public function displayPendingIncidentReport($operation)
    {
        $pendingReport = $this->incidentReport->where('status', 'On Process')->where('is_archive', $operation == "pending" ? 0 : 1)->get();

        return DataTables::of($pendingReport)
            ->addIndexColumn()
            ->addColumn('status', '<div class="status-container"><div class="status-content bg-warning">On Process</div></div>')
            ->addColumn('action', function ($report) {
                if (!auth()->check()) {
                    return $report->user_ip == request()->ip()
                        ? '<div class="action-container"><button class="btn-table-update" id="updateIncidentReport"><i class="bi bi-pencil-square"></i>Update</button>' .
                        '<button class="btn-table-remove" id="revertIncidentReport"><i class="bi bi-arrow-counterclockwise"></i>Revert</button></div>'
                        : null;
                }

                return auth()->user()->is_disable == 0
                    ? '<div class="action-container"><button class="btn-table-submit" id="approveIncidentReport"><i class="bi bi-check-circle-fill"></i>Approve</button>' .
                    '<button class="btn-table-remove" id="declineIncidentReport"><i class="bi bi-x-circle-fill"></i>Decline</button></div>'
                    : null;
            })
            ->addColumn('photo', fn ($report) => '<div class="photo-container">
                    <div class="image-wrapper">
                        <img class="report-img" src="' . asset('reports_image/' . $report->photo) . '">
                        <div class="image-overlay">
                            <div class="overlay-text">View Photo</div>
                        </div>
                    </div>
                </div>')
            ->rawColumns(['status', 'action', 'photo'])
            ->make(true);
    }

    public function displayIncidentReport($operation)
    {
        $incidentReport = $this->incidentReport->whereNotIn('status', ['On Process'])->get();

        return DataTables::of($incidentReport)
            ->addIndexColumn()
            ->addColumn('status', fn ($report) => '<div class="status-container"><div class="status-content bg-' . match ($report->status) {
                'Approved' => 'success',
                'Declined' => 'danger'
            }
                . '">' . $report->status . '</div></div>')
            ->addColumn('action', function ($report) {
                if (auth()->user()->is_disable == 0) {
                    return $report->is_archive == 0 ?
                        '<button class="btn-table-remove" id="archiveIncidentReport"><i class="bi bi-trash3-fill"></i>Archive</button>' :
                        '<button class="btn-table-remove" id="unArchiveIncidentReport"><i class="bi bi-arrow-repeat"></i>Unarchive</button>';
                }
            })
            ->addColumn('photo', fn ($report) => '<div class="photo-container">
                    <div class="image-wrapper">
                        <img class="report-img" src="' . asset('reports_image/' . $report->photo) . '">
                        <div class="image-overlay">
                            <div class="overlay-text">View Photo</div>
                        </div>
                    </div>
                </div>')
            ->rawColumns(['status', 'action', 'photo'])
            ->make(true);
    }

    public function createIncidentReport(Request $request)
    {
        $incidentReportValidation = Validator::make($request->all(), [
            'description' => 'required',
            'location'    => 'required',
            'photo'       => 'image|mimes:jpeg|max:2048'
        ]);

        if ($incidentReportValidation->fails())
            return response(['status' => 'warning', 'message' => $incidentReportValidation->errors()->first()]);

        $resident = $this->reportLog->where('user_ip', $request->ip())->first();
        $reportPhotoPath = $request->file('photo');

        if ($reportPhotoPath) {
            $reportPhotoPath = $request->file('photo')->store();
            $request->photo->move(public_path('reports_image'), $reportPhotoPath);
        }

        $incidentReport = [
            'description'  => Str::ucFirst(trim($request->description)),
            'location'     => Str::title(trim($request->location)),
            'photo'        => $reportPhotoPath,
            'status'       => 'On Process',
            'user_ip'      => $request->ip(),
            'is_archive'   => 0,
            'report_time'  => Carbon::now()->toDayDateTimeString()
        ];

        if ($resident) {
            $residentAttempt = $resident->attempt;
            $reportTime      = $resident->report_time;

            if ($residentAttempt == 3) {
                $isBlock = $this->isBlocked($reportTime);

                if (!$isBlock) {
                    $resident->update(['attempt' => 0, 'report_time' => null]);
                    $residentAttempt = 0;
                } else {
                    return response(['status' => 'blocked', 'message' => "You have been blocked until $isBlock."]);
                }
            }

            $this->incidentReport->create($incidentReport);
            $resident->update(['attempt' => $residentAttempt + 1]);
            $attempt = $resident->attempt;
            $attempt == 3 ? $resident->update(['report_time' => Carbon::now()->addHours(3)]) : null;
            event(new IncidentReportEvent());
            event(new NotificationEvent());

            return response()->json();
        }

        $this->incidentReport->create($incidentReport);
        $this->reportLog->create([
            'user_ip' => $request->ip(),
            'attempt' => 1
        ]);
        // event(new IncidentReportEvent());
        // event(new NotificationEvent());

        return response()->json();
    }

    public function updateIncidentReport(Request $request, $reportId)
    {
        $incidentReportValidation = Validator::make($request->all(), [
            'description' => 'required',
            'location'    => 'required',
            'photo'       => 'image|mimes:jpeg|max:2048'
        ]);

        if ($incidentReportValidation->fails())
            response(['status' => 'warning', 'message' => $incidentReportValidation->errors()->first()]);

        $residentReport        = $this->incidentReport->find($reportId);
        $residentReportPhoto   = $residentReport->value('photo');
        $reportPhoto           = $request->file('photo');

        $dataToUpdate = [
            'description' => Str::ucFirst(trim($request->description)),
            'location'    => Str::title(trim($request->location))
        ];

        if ($reportPhoto) {
            $reportPhoto           = $reportPhoto->store();
            $dataToUpdate['photo'] = $reportPhoto;
            $request->photo->move(public_path('reports_image'), $reportPhoto);

            if ($residentReportPhoto) {
                $incidentPhoto = public_path('reports_image/' . $residentReportPhoto);
                if (file_exists($incidentPhoto)) unlink($incidentPhoto);
            }
        }

        $residentReport->update($dataToUpdate);
        //event(new IncidentReportEvent());

        return response()->json();
    }

    public function approveIncidentReport($reportId)
    {
        $this->reportEvent->approveStatus($reportId);
        $this->logActivity->generateLog($reportId, 'Resident Incident Report', 'approved a incident report');
        // event(new IncidentReportEvent());

        return response()->json();
    }

    public function declineIncidentReport($reportId)
    {
        $this->reportEvent->declineStatus($reportId);
        $this->logActivity->generateLog($reportId, 'Resident Incident Report', 'declined a incident report');
        // event(new IncidentReportEvent());

        return response()->json();
    }

    public function revertIncidentReport($reportId)
    {
        $reportPhotoPath = $this->incidentReport->find($reportId)->value('photo');
        $this->reportEvent->revertIncidentReport($reportId, $reportPhotoPath);
        // event(new IncidentReportEvent());

        return response()->json();
    }

    public function archiveIncidentReport($reportId, $operation)
    {
        $dangerAreaReport = $this->reportEvent->archiveDangerAreaReport($reportId, $operation);
        $$this->logActivity->generateLog($reportId, $dangerAreaReport, $operation . "d a dangerous area report");
        //event(new IncidentReportEvent());

        return response()->json();
    }

    public function updateUserAttempt()
    {
        $userIp   = request()->ip();
        $resident = $this->reportLog->where('user_ip', $userIp)->first();

        if ($resident) {
            $resident->decrement('attempt');
            $resident->attempt == 2 ? $resident->update(['report_time' => null]) : ($resident->attempt == 0 ?  $resident->delete() : null);
        }

        return response()->json();
    }

    private function isBlocked($reportTime)
    {
        return $reportTime <= Carbon::now()->toDateTimeString() ? false : Carbon::parse($reportTime)->format('F j, Y H:i:s');
    }
}
