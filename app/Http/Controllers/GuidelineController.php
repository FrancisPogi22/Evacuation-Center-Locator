<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use App\Models\Guideline;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ActivityUserLog;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class GuidelineController extends Controller
{
    private $guide, $guideline, $logActivity;

    function __construct()
    {
        $this->guide       = new Guide;
        $this->guideline   = new Guideline;
        $this->logActivity = new ActivityUserLog;
    }

    public function createGuideline(Request $request)
    {
        $guidelineValidation = Validator::make($request->all(), [
            'type'      => 'required|unique:guideline,type',
            'label.*'   => 'required',
            'content.*' => 'required'
        ]);

        if ($guidelineValidation->fails())
            return response(['status' => 'warning', 'message' => $guidelineValidation->errors()->first()]);

        $userId    = auth()->user()->id;
        $guideline = $this->guideline->create([
            'type'         => Str::upper(trim($request->type)),
            'user_id'      => $userId,
            'organization' => auth()->user()->organization
        ]);
        $labels   = $request->label;
        $contents = $request->content;

        if ($labels && $contents) {
            $guidePhotos = $request->file('guidePhoto');

            foreach ($labels as $count => $label) {
                $guide = $this->guide->create([
                    'label'        => $label,
                    'content'      => $contents[$count],
                    'guideline_id' => $guideline->id,
                    'user_id'      => $userId
                ]);

                if (isset($guidePhotos[$count])) {
                    $reportPhotoPath    =  $guidePhotos[$count]->store();
                    $guidePhotos[$count]->move(public_path('guide_photo'), $reportPhotoPath);
                    $guide->guide_photo = $reportPhotoPath;
                    $guide->save();
                }
            }
        }

        $this->logActivity->generateLog($guideline->id, $guideline->type, 'created a new guideline');

        return response()->json();
    }

    public function updateGuideline(Request $request, $guidelineId)
    {
        $guidelineValidation = Validator::make($request->all(), [
            'type'      => 'required',
            'label.*'   => 'required',
            'content.*' => 'required'
        ]);

        if ($guidelineValidation->fails())
            return response(['status' => 'warning', 'message' => $guidelineValidation->errors()->first()]);

        $guideline = $this->guideline->find(Crypt::decryptString($guidelineId));
        $guideline->update([
            'type'    => Str::upper(trim($request->type)),
            'user_id' => auth()->user()->id
        ]);
        $this->logActivity->generateLog($guideline->id, $guideline->type, 'updated a guideline');

        $labels   = $request->label;
        $contents = $request->content;

        if ($labels && $contents) {
            $guidePhotos = $request->file('guidePhoto');

            foreach ($labels as $count => $label) {
                $guide = $this->guide->create([
                    'label'        => $label,
                    'content'      => $contents[$count],
                    'guideline_id' => $guideline->id,
                    'user_id'      => auth()->user()->id
                ]);

                if (isset($guidePhotos[$count])) {
                    $guidePhotoPath =  $guidePhotos[$count]->store();
                    $guidePhotos[$count]->move(public_path('guide_photo'), $guidePhotoPath);
                    $guide->guide_photo = $guidePhotoPath;
                    $guide->save();
                }
            }
        }

        return response()->json();
    }

    public function removeGuideline($guidelineId)
    {
        $guideline = $this->guideline->find(Crypt::decryptString($guidelineId));
        $this->logActivity->generateLog($guideline->id, $guideline->type, 'removed a guideline');
        $guideline->delete();

        return response()->json();
    }

    public function updateGuide(Request $request, $guideId)
    {
        $guideValidation = Validator::make($request->all(), [
            'label'   => 'required',
            'content' => 'required'
        ]);

        if ($guideValidation->fails())
            return response(['status' => 'warning', 'message' => $guideValidation->errors()->first()]);

        $guideItem = $this->guide->find($guideId);

        if ($request->hasFile('guidePhoto')) {
            $guidePhotoPath = $request->file('guidePhoto')->store();

            if ($guideItem->guide_photo) {
                $guidePhoto = public_path('guide_photo/' . $guideItem->value('guide_photo'));

                if (file_exists($guidePhoto)) unlink($guidePhoto);

                $request->guidePhoto->move(public_path('guide_photo'), $guidePhotoPath);
                $guideItem->update(['guide_photo' => $guidePhotoPath]);
            }
        }

        $guideItem->update([
            'label'   => Str::upper(trim($request->label)),
            'content' => Str::ucfirst(trim($request->content)),
            'user_id' => auth()->user()->id
        ]);
        $this->logActivity->generateLog($guideId, $guideItem->label, 'updated a guide');

        return response()->json();
    }

    public function removeGuide($guideId)
    {
        $guideItem = $this->guide->find($guideId);
        $this->logActivity->generateLog($guideId, $guideItem->label, 'removed a guide');
        $guideItem->delete();

        return response()->json();
    }
}
