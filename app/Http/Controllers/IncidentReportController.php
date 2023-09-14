<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ReportLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\IncidentReport;
use App\Models\ActivityUserLog;
use Yajra\DataTables\DataTables;
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

    public function displayPendingIncidentReport()
    {
        $pendingReport = $this->incidentReport->where('status', 'On Process')->whereNotNull('photo')->get();

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
            ->rawColumns(['id', 'status', 'action', 'photo'])
            ->make(true);
    }

    public function displayIncidentReport()
    {
        $incidentReport = $this->incidentReport->where('status', 'Approved')->where('is_archive', 0)->get();

        return DataTables::of($incidentReport)
            ->addIndexColumn()
            ->addColumn('status', fn ($report) => '<div class="status-container"><div class="status-content bg-' . match ($report->status) {
                'Approved' => 'success',
                'Declined' => 'danger'
            }
                . '">' . $report->status . '</div></div>')
            ->addColumn('action', fn () => auth()->user()->is_disable == 0 ? '<button class="btn-table-remove" id="archiveIncidentReport"><i class="bi bi-trash3-fill"></i>Archive</button>' :
                '<span class="message-text">Currently Disabled.</span>')
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
        $reportPhotoPath = $request->file('photo')->store();
        $request->photo->move(public_path('reports_image'), $reportPhotoPath);
        $incidentReport = [
            'description' => Str::ucFirst(trim($request->description)),
            'location'    => Str::of(trim($request->location))->title(),
            'photo'       => $reportPhotoPath,
            'latitude'    => null,
            'longitude'   => null,
            'status'      => 'On Process',
            'user_ip'     => $request->ip(),
            'is_archive'  => 0
        ];

        if ($resident) {
            $residentAttempt = $resident->attempt;
            $reportTime = $resident->report_time;

            if ($residentAttempt == 3) {
                $isBlock = $this->isBlocked($reportTime);

                if (!$isBlock) {
                    $resident->update(['attempt' => 0, 'report_time' => null]);
                    $residentAttempt = 0;
                } else {
                    return response(['status' => 'blocked', 'message' => "You have been blocked until  $isBlock."]);
                }
            }

            $this->incidentReport->create($incidentReport);
            $resident->update(['attempt' => $residentAttempt + 1]);
            $attempt = $resident->attempt;
            $attempt == 3 ? $resident->update(['report_time' => Carbon::now()->addHours(3)]) : null;
            //event(new IncidentReportEvent());
            return response()->json();
        }

        $this->incidentReport->create($incidentReport);
        $this->reportLog->create([
            'user_ip' => $request->ip(),
            'attempt' => 1
        ]);
        //event(new IncidentReportEvent());
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

        $residentReport = $this->incidentReport->find($reportId);
        $reportPhoto = $request->file('photo');

        $dataToUpdate = [
            'description' => Str::ucFirst(trim($request->description)),
            'location'    => Str::of(trim($request->location))->title()
        ];

        if ($reportPhoto) {
            $reportPhoto           = $reportPhoto->store();
            $request->photo->move(public_path('reports_image'), $reportPhoto);
            $dataToUpdate['photo'] = $reportPhoto;
            $image_path            = public_path('reports_image/' . $residentReport->value('photo'));
            if (file_exists($image_path)) unlink($image_path);
        }

        $residentReport->update($dataToUpdate);

        return response()->json();
    }

    public function approveIncidentReport($reportId)
    {
        $this->reportEvent->approveStatus($reportId);
        $this->logActivity->generateLog($reportId, 'Approved Incident Report');
        //event(new IncidentReportEvent());
        return response()->json();
    }

    public function declineIncidentReport($reportId)
    {
        $this->reportEvent->declineStatus($reportId);
        $this->logActivity->generateLog($reportId, 'Declined Incident Report');
        //event(new IncidentReportEvent());
        return response()->json();
    }

    public function revertIncidentReport($reportId)
    {
        $reportPhotoPath = $this->incidentReport->find($reportId)->value('photo');
        $this->reportEvent->revertIncidentReport($reportId, $reportPhotoPath);
        //event(new IncidentReportEvent());
        return response()->json();
    }

    public function archiveIncidentReport($reportId)
    {
        $this->incidentReport->find($reportId)->update([
            'is_archive' => 1,
            'status'     => 'Archived'
        ]);
        $this->logActivity->generateLog($reportId, 'Archived Incident Report');
        //event(new IncidentReportEvent());
        return response()->json();
    }

    public function displayDangerousAreasReport(Request $request)
    {
        if (!$request->ajax()) return view('userpage.evacuationCenter.dangerousAreasReport');

        $dangerousAreasReport = $this->incidentReport
            ->whereIn('status', auth()->check() ? ['On Process', 'Confirmed'] : ['On Process'])
            ->where('is_archive', 0)
            ->whereNull('photo');

        if (!auth()->check())
            $dangerousAreasReport->where('user_ip', $request->ip());

        return DataTables::of($dangerousAreasReport->get())
            ->addIndexColumn()
            ->addColumn('status', fn ($dangerousAreas) => '<div class="status-container"><div class="status-content bg-' . match ($dangerousAreas->status) {
                'Confirmed'  => 'success',
                'On Process' => 'warning'
            }
                . '">' . $dangerousAreas->status . '</div></div>')
            ->addColumn('action', function ($dangerousAreas) {
                if (!auth()->check()) {
                    return $dangerousAreas->user_ip == request()->ip() ? '<div class="action-container">' .
                        '<button class="btn-table-update" id="updateDangerousAreaReport"><i class="bi bi-trash3-fill"></i>Update</button>' .
                        '<button class="btn-table-remove" id="revertDangerousAreaReport"><i class="bi bi-arrow-counterclockwise"></i>Revert</button>' .
                        '</div>' : '';
                } elseif (auth()->user()->is_disable == 0) {
                    return '<div class="action-container">' .
                        ($dangerousAreas->status == "Confirmed"
                            ? '<button class="btn-table-remove" id="archiveDangerAreaReport"><i class="bi bi-trash3-fill"></i>Archive</button>'
                            : '<button class="btn-table-submit" id="confirmDangerAreaReport"><i class="bi bi-check-circle-fill"></i>Confirm</button>' .
                            '<button class="btn-table-remove" id="rejectDangerAreaReport"><i class="bi bi-x-circle-fill"></i>Reject</button>') .
                        '</div>';
                } else {
                    return '<span class="message-text">Currently Disabled.</span>';
                }
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function reportDangerousArea(Request $request)
    {
        $dangerousAreasReportValidation = Validator::make($request->all(), [
            'report_type' => 'required',
            'latitude'    => 'required',
            'longitude'   => 'required'
        ]);

        if ($dangerousAreasReportValidation->fails())
            return response(['status' => 'warning', 'message' => $dangerousAreasReportValidation->errors()->first()]);

        $resident = $this->reportLog->where('user_ip', $request->ip())->first();
        $dangerAreaReport = [
            'description' => Str::ucFirst(trim($request->report_type)),
            'location'    => null,
            'photo'       => null,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'status'      => 'On Process',
            'user_ip'     => $request->ip(),
            'is_archive'  => 0
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
                    return response(['status' => 'blocked', 'message' => "You have been blocked until  $isBlock."]);
                }
            }

            $this->incidentReport->create($dangerAreaReport);
            $resident->update(['attempt' => $residentAttempt + 1]);
            $attempt = $resident->attempt;
            $attempt == 3 ? $resident->update(['report_time' => Carbon::now()->addHours(3)]) : null;
            //event(new IncidentReportEvent());
            return response()->json();
        }

        $this->incidentReport->create($dangerAreaReport);
        $this->reportLog->create([
            'user_ip' => $request->ip(),
            'attempt' => 1
        ]);
        //event(new IncidentReportEvent());
        return response()->json();
    }

    public function updateDangerousAreaReport(Request $request, $reportId)
    {
        $dangerousAreasReportValidation = Validator::make($request->all(), [
            'report_type' => 'required',
            'latitude'    => 'required',
            'longitude'   => 'required'
        ]);

        if ($dangerousAreasReportValidation->fails())
            return response(['status' => 'warning', 'message' => $dangerousAreasReportValidation->errors()->first()]);

        $this->incidentReport->find($reportId)->update([
            'description' => Str::ucFirst(trim($request->report_type)),
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude
        ]);
        //event(new IncidentReportEvent());
        return response()->json();
    }

    public function revertDangerousAreaReport($reportId)
    {
        $this->reportEvent->revertDangerAreaReport($reportId);
        //event(new IncidentReportEvent());
        return response()->json();
    }

    public function confirmDangerAreaReport($dangerAreaId)
    {
        $this->reportEvent->confirmDangerAreaReport($dangerAreaId);
        $this->logActivity->generateLog($dangerAreaId, 'Confirmed Dangerous Area Report');
        //event(new IncidentReportEvent());
        return response()->json();
    }

    public function rejectDangerAreaReport($dangerAreaId)
    {
        $this->incidentReport->find($dangerAreaId)->delete();
        $this->logActivity->generateLog($dangerAreaId, 'Rejected Dangerous Area Report');
        //event(new IncidentReportEvent());
        return response()->json();
    }

    public function archiveDangerAreaReport($dangerAreaId)
    {
        $this->reportEvent->archiveDangerAreaReport($dangerAreaId);
        $this->logActivity->generateLog($dangerAreaId, 'Archived Dangerous Area Report');
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
