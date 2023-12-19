<?php

namespace App\Http\Controllers;

use App\Models\Markers;
use Illuminate\Http\Request;
use App\Models\ActivityUserLog;
use Illuminate\Support\Facades\Validator;

class MarkerController extends Controller
{

    private $marker, $logActivity;

    function __construct()
    {
        $this->marker   = new Markers;
        $this->logActivity = new ActivityUserLog;
    }


    public function manageReportMarkers()
    {
        if (!request()->ajax()) return view('userpage.residentReport.manageMarkers');

        return response(['markerData' => $this->marker->all()]);
    }

    public function addMarker(Request $request)
    {
        $markerValidation = Validator::make($request->all(), [
            'name'        => 'required',
            'description' => 'required',
            'image'       => 'required|image|mimes:jpeg,png,jpg'
        ]);

        if ($markerValidation->fails()) return response(['status' => 'warning', 'message' => implode('<br>', $markerValidation->errors()->all())]);

        $markerImage     = $request->file('image');
        $markerImagePath = $markerImage;

        if ($markerImagePath) {
            $markerImagePath = $markerImage->store();
            $markerImage->move(public_path('markers'), $markerImagePath);
        }

        $marker = $this->marker->create([
            'name'        => strtoupper(trim($request->name)),
            'description' => trim($request->description),
            'image'       => $markerImagePath
        ]);

        $this->logActivity->generateLog('Created a new marker(ID - ' . $marker->id . ')');

        return response(['id' =>  $marker->id, 'name' => $marker->name, 'description' => $marker->description, 'image' => $marker->image]);
    }
}
