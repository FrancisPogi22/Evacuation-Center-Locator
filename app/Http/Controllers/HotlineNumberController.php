<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\HotlineNumbers;
use App\Models\ActivityUserLog;
use Illuminate\Support\Facades\Validator;

class HotlineNumberController extends Controller
{
    private $hotlineNumbers, $logActivity;

    public function __construct()
    {
        $this->logActivity    = new ActivityUserLog;
        $this->hotlineNumbers = new HotlineNumbers;
    }

    public function addHotlineNumber(Request $request)
    {
        $hotlineNumberValidation = Validator::make($request->all(), [
            'logo' => 'nullable|image|mimes:jpeg,png,jpg',
            'label'  => 'required',
            'number' => 'required|numeric'
        ]);

        if ($hotlineNumberValidation->fails())
            return response(['status' => 'warning', 'message' =>  implode('<br>', $hotlineNumberValidation->errors()->all())]);

        $hotlineLogo     = $request->file('logo');
        $hotlineLogoPath = $hotlineLogo;

        if ($hotlineLogoPath) {
            $hotlineLogoPath = $hotlineLogo->store();
            $hotlineLogo->move(public_path('hotline_logo/'), $hotlineLogoPath);
        }

        $hotlineNumber = $this->hotlineNumbers->create([
            'logo'    => $hotlineLogoPath,
            'label'   => Str::title(trim($request->label)),
            'number'  => trim($request->number),
            'user_id' => auth()->user()->id
        ]);

        $hotlineId = $hotlineNumber->id;
        $this->logActivity->generateLog($hotlineId, $hotlineNumber->label, 'added a new hotline number');

        return response(['hotlineId' => $hotlineId, 'hotlineLogo' => $hotlineLogoPath, 'label' => $hotlineNumber->label, 'number' => $hotlineNumber->number]);
    }

    public function updateHotlineNumber(Request $request, $hotlineId)
    {
        $hotlineNumberValidation = Validator::make($request->all(), [
            'logo' => 'nullable|image|mimes:jpeg,png,jpg',
            'label'  => 'required',
            'number' => 'required|numeric'
        ]);

        if ($hotlineNumberValidation->fails())
            return response(['status' => 'warning', 'message' => implode('<br>', $hotlineNumberValidation->errors()->all())]);

        $hotlineLogo       = $request->file('logo');
        $hotlineNumber     = $this->hotlineNumbers->find($hotlineId);
        $hotlineNumberData = [
            'label'   => Str::title(trim($request->label)),
            'number'  => trim($request->number),
            'user_id' => auth()->user()->id
        ];

        if ($hotlineLogo) {
            $hotlineLogo               = $hotlineLogo->store();
            $hotlineLogoOld            = $hotlineNumber->logo;
            $hotlineNumberData['logo'] = $hotlineLogo;
            $request->logo->move(public_path('hotline_logo/'), $hotlineLogo);

            if ($hotlineLogoOld) {
                $hotlineLogoOldPath = public_path('hotline_logo/' . $hotlineLogoOld);

                if (file_exists($hotlineLogoOldPath)) unlink($hotlineLogoOldPath);
            }
        }

        $hotlineNumber->update($hotlineNumberData);
        $this->logActivity->generateLog($hotlineId, $hotlineNumber->label, 'updated a hotline number');

        return response(['hotlineId' => $hotlineId, 'label' => $hotlineNumber->label, 'number' => $hotlineNumber->number]);
    }

    public function removeHotlineNumber($hotlineId)
    {
        $hotlineNumber = $this->hotlineNumbers->find($hotlineId);
        $hotlineLogo   = $hotlineNumber->logo;

        if ($hotlineLogo) {
            $hotlineLogoPath = public_path('hotline_logo/' . $hotlineLogo);

            if (file_exists($hotlineLogoPath)) unlink($hotlineLogoPath);
        }

        $this->logActivity->generateLog($hotlineId, $hotlineNumber->label, 'removed a hotline number');
        $hotlineNumber->delete();

        return response([]);
    }
}
