<?php

namespace App\Http\Controllers;

use App\Models\Guideline;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ActivityUserLog;
use Illuminate\Support\Facades\Validator;

class GuidelineController extends Controller
{
    private $guideline, $logActivity;

    function __construct()
    {
        $this->guideline   = new Guideline;
        $this->logActivity = new ActivityUserLog;
    }

    public function createGuideline(Request $request)
    {
        $guidelineValidation = Validator::make($request->all(), [
            'type'         => 'required|unique:guideline,type',
            'coverImage'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'contentImage' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($guidelineValidation->fails()) return response(['status' => 'warning', 'message' => implode('<br>', $guidelineValidation->errors()->all())]);

        $guidelineContentImage = $request->file('contentImage');
        $guidelineCoverImage   = $request->file('coverImage');
        $contentImagePath      = $guidelineContentImage;
        $coverImagePath        = $guidelineCoverImage;

        if ($coverImagePath) {
            $coverImagePath = $guidelineCoverImage->store();
            $guidelineCoverImage->move(public_path('guideline_image'), $coverImagePath);
        }

        $contentImagePath = $guidelineContentImage->store();
        $guidelineContentImage->move(public_path('guide_image'), $contentImagePath);

        $guideline = $this->guideline->create([
            'type'          => strtoupper(trim($request->type)),
            'cover_image'   => $coverImagePath,
            'organization'  => auth()->user()->organization,
            'content_image' => $contentImagePath
        ]);

        $this->logActivity->generateLog('Created a new guideline(ID - ' . $guideline->id . ')');

        return response(['id' =>  $guideline->id, 'type' => $guideline->type, 'cover' => $guideline->cover_image, 'content' => $guideline->content_image]);
    }

    public function updateGuideline(Request $request, $guidelineId)
    {
        $guidelineValidation = Validator::make($request->all(), [
            'type'         => 'required|unique:guideline,type',
            'coverImage'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'contentImage' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($guidelineValidation->fails()) return response(['status' => 'warning', 'message' => implode('<br>', $guidelineValidation->errors()->all())]);

        $guidelineContentImage = $request->file('contentImage');
        $guidelineCoverImage   = $request->file('coverImage');
        $contentImagePath      = $guidelineContentImage;
        $coverImagePath        = $guidelineCoverImage;
        $guideline             = $this->guideline->find($guidelineId);

        if ($coverImagePath) {
            $oldCoverImage = $guideline->cover_image;
            $coverImagePath    = $guidelineCoverImage->store();
            $guidelineCoverImage->move(public_path('guideline_image'), $coverImagePath);

            if ($oldCoverImage) {
                $oldCoverPath = public_path('guideline_image/' . $oldCoverImage);

                if (file_exists($oldCoverPath)) unlink($oldCoverPath);
            }
        }

        if ($contentImagePath) {
            $oldContentImage = $guideline->content_image;
            $contentImagePath = $guidelineContentImage->store();
            $guidelineContentImage->move(public_path('guide_image'), $contentImagePath);

            if ($oldContentImage) {
                $oldContentPath = public_path('guideline_image/' . $oldContentImage);

                if (file_exists($oldContentPath)) unlink($oldContentPath);
            }
        }

        $this->logActivity->generateLog('Updated ' . lcfirst($guideline->type) . ' guideline(ID - ' . $guideline->id . ')');
        $newData = [
            'type'          => strtoupper(trim($request->type)),
            'cover_image'   => $coverImagePath,
            'content_image' => $contentImagePath
        ];

        $guideline->update($newData);

        return response(['type' => $newData['type'], 'cover' => $newData['cover_image'], 'content' => $newData['content_image']]);
    }

    public function removeGuideline($guidelineId)
    {
        $guideline    = $this->guideline->find($guidelineId);
        $guidelineCoverImage = $guideline->cover_image;

        unlink(public_path('guide_image/' . $guideline->content_image));

        if ($guidelineCoverImage) {
            $coverImagePath = public_path('guideline_image/' . $guidelineCoverImage);

            if (file_exists($coverImagePath)) unlink($coverImagePath);
        }

        $this->logActivity->generateLog('Removed ' . lcfirst($guideline->type) . ' guideline(ID - ' . $guidelineId . ')');
        $guideline->delete();

        return response([]);
    }
}
