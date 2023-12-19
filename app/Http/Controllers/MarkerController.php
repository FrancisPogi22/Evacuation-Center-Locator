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
        if (!request()->ajax()) return view('userpage.markers.manageMarkers');

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
            'name'        => ucwords(trim($request->name)),
            'description' => ucfirst(trim($request->description)),
            'image'       => $markerImagePath
        ]);

        $this->logActivity->generateLog("Created a new marker(ID - $marker->id)");

        return response(['id' =>  $marker->id, 'name' => $marker->name, 'description' => $marker->description, 'image' => $marker->image]);
    }

    public function updateMarker(Request $request, $markerId)
    {
        $markerValidation = Validator::make($request->all(), [
            'name'        => 'required',
            'description' => 'required',
            'image'       => 'image|mimes:jpeg,png,jpg'
        ]);

        if ($markerValidation->fails()) return response(['status' => 'warning', 'message' => implode('<br>', $markerValidation->errors()->all())]);

        $markerImage     = $request->file('image');
        $markerImagePath = $markerImage;
        $marker          = $this->marker->find($markerId);
        $markerData = [
            'name'        => ucwords(trim($request->name)),
            'description' => ucfirst(trim($request->description))
        ];

        if ($markerImage) {
            $oldMarkerImage  = $marker->image;
            $markerImagePath = $markerImage->store();
            $markerImage->move(public_path('markers/'), $markerImagePath);
            $markerData['image'] = $markerImagePath;
            if ($oldMarkerImage) {
                $oldImagePath = public_path('markers/' . $oldMarkerImage);

                if (file_exists($oldImagePath)) unlink($oldImagePath);
            }
        }

        $this->logActivity->generateLog('Updated ' . lcfirst($marker->name) . " marker(ID - $markerId)");
        $marker->update($markerData);

        return response(['name' => $marker->name, 'description' => $marker->description, 'image' => $marker->image]);
    }

    public function removeMarker($markerId)
    {
        $marker      = $this->marker->find($markerId);
        $markerImage = $marker->image;

        if ($markerImage) {
            $markerImagePath = public_path('markers/' . $markerImage);

            if (file_exists($markerImagePath)) unlink($markerImagePath);
        }

        $this->logActivity->generateLog('Removed ' . lcfirst($marker->name) . " marker(ID - $markerId)");
        $marker->delete();

        return response([]);
    }
}
