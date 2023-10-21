<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use App\Models\Guideline;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ActivityUserLog;
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
        $guidelineValidation = Validator::make($request->only('type', 'guidelineImg'), [
            'guidelineImg' => 'image|mimes:jpeg|max:2048',
            'type'         => 'required|unique:guideline,type'
        ]);

        $guideValidation = Validator::make($request->all(), [
            'label.*'   => 'required',
            'content.*' => 'required'
        ]);

        if ($guidelineValidation->fails())
            return response(['status' => 'warning', 'message' => $guidelineValidation->errors()->first()]);
        if ($guideValidation->fails())
            return response(['status' => 'warning', 'message' => "All guide fields are required, fill them out."]);

        $userId             = auth()->user()->id;
        $guidelineImg       = $request->file('guidelineImg');
        $guidelineImagePath = $guidelineImg;

        if ($guidelineImagePath) {
            $guidelineImagePath = $guidelineImg->store();
            $guidelineImg->move(public_path('guideline_image'), $guidelineImagePath);
        }

        $guideline = $this->guideline->create([
            'type'          => Str::upper(trim($request->type)),
            'user_id'       => $userId,
            'organization'  => auth()->user()->organization,
            'guideline_img' => $guidelineImagePath
        ]);
        $this->logActivity->generateLog($guideline->id, $guideline->type, 'created a new guideline');
        $labels   = $request->label;
        $contents = $request->content;

        if ($labels && $contents) {
            $guideImages = $request->file('guidePhoto');

            foreach ($labels as $count => $label) {
                $guideData = [
                    'label'        => Str::upper(trim($label)),
                    'content'      => $contents[$count],
                    'guideline_id' => $guideline->id,
                    'user_id'      => $userId
                ];

                if (isset($guideImages[$count])) {
                    $guideImagePath     =  $guideImages[$count]->store();
                    $guideImages[$count]->move(public_path('guideline_image'), $guideImagePath);
                    $guideData['guide_photo'] = $guideImagePath;
                }

                $guide = $this->guide->create($guideData);
                $this->logActivity->generateLog($guide->id, $guide->label, 'created a new guide');
            }
        }

        return response(['guideline_id' =>  $guideline->id, 'type' => $guideline->type, 'guideline_img' => $guideline->guideline_img]);
    }

    public function updateGuideline(Request $request, $guidelineId)
    {
        $guidelineValidation = Validator::make($request->only('type', 'guidelineImg'), [
            'guidelineImg' => 'image|mimes:jpeg|max:2048',
            'type'         => 'required'
        ]);

        $guideValidation = Validator::make($request->all(), [
            'label.*'   => 'required',
            'content.*' => 'required'
        ]);

        if ($guidelineValidation->fails())
            return response(['status' => 'warning', 'message' => $guidelineValidation->errors()->first()]);
        if ($guideValidation->fails())
            return response(['status' => 'warning', 'message' => "All guide fields are required, fill them out."]);

        $userId          = auth()->user()->id;
        $guideline       = $this->guideline->find($guidelineId);
        $guidelineImg    = $request->file('guidelineImg');
        $guidelineData   = [
            'type'    => Str::upper(trim($request->type)),
            'user_id' => $userId
        ];

        if ($guidelineImg) {
            $guidelineImgOld                = $guideline->guideline_img;
            $guidelineImg                   = $guidelineImg->store();
            $guidelineData['guideline_img'] = $guidelineImg;
            $request->guidelineImg->move(public_path('guideline_image'), $guidelineImg);

            if ($guidelineImgOld) {
                $guidelineImgOldPath = public_path('guideline_image/' . $guidelineImgOld);

                if (file_exists($guidelineImgOldPath)) unlink($guidelineImgOldPath);
            }
        }

        $guideline->update($guidelineData);
        $this->logActivity->generateLog($guideline->id, $guideline->type, 'updated a guideline');

        $labels   = $request->label;
        $contents = $request->content;

        if ($labels && $contents) {
            $guidePhotos = $request->file('guidePhoto');

            foreach ($labels as $count => $label) {
                $guideData = [
                    'label'        => Str::upper(trim($label)),
                    'content'      => $contents[$count],
                    'guideline_id' => $guideline->id,
                    'user_id'      => $userId
                ];

                if (isset($guidePhotos[$count])) {
                    $guidePhotoPath     =  $guidePhotos[$count]->store();
                    $guidePhotos[$count]->move(public_path('guideline_image'), $guidePhotoPath);
                    $guideData['guide_photo'] =  $guidePhotoPath;
                }

                $guide = $this->guide->create($guideData);
                $this->logActivity->generateLog($guide->id, $guide->label, 'created a new guide');
            }
        }

        return response(['type' => $guideline->type, 'guideline_img' => $guideline->guideline_img]);
    }

    public function removeGuideline($guidelineId)
    {
        $guideline    = $this->guideline->find($guidelineId);
        $guidelineImg = $guideline->guideline_img;
        $guide        = $this->guide->where('guideline_id', $guidelineId)->get();

        foreach ($guide as $guide) {
            $this->removeGuideImage($guide->guide_photo);
        }

        if ($guidelineImg) {
            $guidelineImgPath = public_path('guideline_image/' . $guidelineImg);

            if (file_exists($guidelineImgPath)) unlink($guidelineImgPath);
        }

        $this->logActivity->generateLog($guidelineId, $guideline->type, 'removed a guideline');
        $guideline->delete();

        return response()->json();
    }

    public function updateGuide(Request $request, $guideId)
    {
        $guideValidation = Validator::make($request->only('guidePhoto', 'label', 'content'), [
            'guidePhoto' => 'image|mimes:jpeg|max:2048',
            'label'      => 'required',
            'content'    => 'required'
        ]);

        if ($guideValidation->fails())
            return response(['status' => 'warning', 'message' => $guideValidation->errors()->first()]);

        $guide       = $this->guide->find($guideId);
        $guideImg    = $request->file('guidePhoto');
        $guideData   = [
            'label'   => Str::upper(trim($request->label)),
            'content' => Str::ucfirst(trim($request->content)),
            'user_id' => auth()->user()->id
        ];

        if ($guideImg) {
            $guideImgOld              = $guide->guide_photo;
            $guideImg                 = $guideImg->store();
            $guideData['guide_photo'] = $guideImg;
            $request->guidePhoto->move(public_path('guideline_image'), $guideImg);

            if ($guideImgOld) {
                $guideImgOldPath = public_path('guideline_image/' . $guideImgOld);

                if (file_exists($guideImgOldPath)) unlink($guideImgOldPath);
            }
        }

        $guide->update($guideData);
        $this->logActivity->generateLog($guideId, $guide->label, 'updated a guide');

        return response(['label' => $guide->label, 'content' => $guide->content, 'guide_photo' => $guide->guide_photo]);
    }

    public function removeGuide($guideId)
    {
        $guide      = $this->guide->find($guideId);
        $guideImage = $guide->guide_photo;
        $this->removeGuideImage($guideImage);
        $this->logActivity->generateLog($guideId, $guide->label, 'removed a guide');
        $guide->delete();

        return response()->json();
    }

    private function removeGuideImage($guideImage)
    {
        if ($guideImage) {
            $guideImgPath = public_path('guideline_image/' . $guideImage);

            if (file_exists($guideImgPath)) unlink($guideImgPath);
        }
    }
}
