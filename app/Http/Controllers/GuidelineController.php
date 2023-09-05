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
        $this->guide = new Guide;
        $this->guideline = new Guideline;
        $this->logActivity = new ActivityUserLog;
    }

    public function eligtasGuideline()
    {
        $guideline = "";

        if (!auth()->check()) {
            $guideline = $this->guideline->all();

            return view('userpage.guideline.eligtasGuideline', compact('guideline'));
        }

        $guideline = auth()->user()->organization == "CDRRMO" ?  $this->guideline->where('organization', "CDRRMO")->where('is_archive', 0)->get() :
            $this->guideline->where('organization', "CSWD")->where('is_archive', 0)->get();

        return view('userpage.guideline.eligtasGuideline', compact('guideline'));
    }

    public function createGuideline(Request $request)
    {
        $guidelineValidation = Validator::make($request->all(), [
            'type' => 'required|unique:guideline,type',
            'label.*' => 'required',
            'content.*' => 'required'
        ]);

        if ($guidelineValidation->fails())
            return response(['status' => 'warning', 'message' => $guidelineValidation->errors()->first()]);

        $guideline = $this->guideline->create([
            'type' => Str::upper(trim($request->type)),
            'organization' => auth()->user()->organization
        ]);
        $this->logActivity->generateLog('Creating Guideline');

        $labels = $request->label;
        $contents = $request->content;

        if ($labels && $contents) {
            $guidePhotos = $request->file('guidePhoto');

            foreach ($labels as $count => $label) {
                $guide = $this->guide->create([
                    'label' => $label,
                    'content' => $contents[$count],
                    'guideline_id' => $guideline->id
                ]);

                if (isset($guidePhotos[$count])) {
                    $reportPhotoPath =  $guidePhotos[$count]->store();
                    $guidePhotos[$count]->move(public_path('guide_photo'), $reportPhotoPath);
                    $guide->guide_photo = $reportPhotoPath;
                    $guide->save();
                }
            }
        }

        return response()->json();
    }

    public function updateGuideline(Request $request, $guidelineId)
    {
        $guidelineValidation = Validator::make($request->all(), [
            'type' => 'required',
            'label.*' => 'required',
            'content.*' => 'required'
        ]);

        if ($guidelineValidation->fails())
            return response(['status' => 'warning', 'message' => $guidelineValidation->errors()->first()]);

        $guidelineId = Crypt::decryptString($guidelineId);
        $this->guideline->find($guidelineId)->update([
            'type' => Str::upper(trim($request->type))
        ]);
        $this->logActivity->generateLog('Updating Guideline');

        $labels = $request->label;
        $contents = $request->content;

        if ($labels && $contents) {
            $guidePhotos = $request->file('guidePhoto');

            foreach ($labels as $count => $label) {
                $guide = $this->guide->create([
                    'label' => $label,
                    'content' => $contents[$count],
                    'guideline_id' => $guidelineId
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
        $this->guideline->find(Crypt::decryptString($guidelineId))->delete();
        $this->logActivity->generateLog('Removing Guideline');
        return response()->json();
    }

    public function guide($guidelineId)
    {
        $guide = $this->guide->where('guideline_id', Crypt::decryptString($guidelineId))->where('is_archive', 0)->get();
        return view('userpage.guideline.guide', compact('guide', 'guidelineId'));
    }

    public function updateGuide(Request $request, $guideId)
    {
        $guideValidation = Validator::make($request->all(), [
            'label' => 'required',
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
            'label' => Str::upper(trim($request->label)),
            'content' => Str::ucfirst(trim($request->content))
        ]);

        $this->logActivity->generateLog('Updating Guide');
        return response()->json();
    }

    public function removeGuide($guideId)
    {
        $this->guide->find($guideId)->delete();
        $this->logActivity->generateLog('Removing Guide');
        return response()->json();
    }
}
