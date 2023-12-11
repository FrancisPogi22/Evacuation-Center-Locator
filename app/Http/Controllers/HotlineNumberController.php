<?php

namespace App\Http\Controllers;

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
            'logo'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'label'  => 'required',
            'number' => 'required'
        ]);

        if ($hotlineNumberValidation->fails()) return response(['status' => 'warning', 'message' => implode('<br>', $hotlineNumberValidation->errors()->all())]);

        $hotlineLogo     = $request->file('logo');
        $hotlineLogoPath = $hotlineLogo;

        if ($hotlineLogoPath) {
            $hotlineLogoPath = $hotlineLogo->store();
            $hotlineLogo->move(public_path('hotline_logo/'), $hotlineLogoPath);
        }

        $hotlineNumber = $this->hotlineNumbers->create([
            'logo'    => $hotlineLogoPath,
            'label'   => strtoupper(trim($request->label)),
            'number'  => trim($request->number)
        ]);

        $hotlineId = $hotlineNumber->id;
        $this->logActivity->generateLog("Added a new hotline number(ID - $hotlineId)");

        return response(['hotlineId' => $hotlineId, 'hotlineLogo' => $hotlineLogoPath, 'label' => $hotlineNumber->label, 'number' => $hotlineNumber->number]);
    }

    public function updateHotlineNumber(Request $request, $hotlineId)
    {
        $hotlineNumberValidation = Validator::make($request->all(), [
            'logo'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'label'  => 'required',
            'number' => 'required'
        ]);

        if ($hotlineNumberValidation->fails()) return response(['status' => 'warning', 'message' => implode('<br>', $hotlineNumberValidation->errors()->all())]);

        $hotlineLogo       = $request->file('logo');
        $hotlineNumber     = $this->hotlineNumbers->find($hotlineId);
        $hotlineNumberData = [
            'label'   => strtoupper(trim($request->label)),
            'number'  => trim($request->number)
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
        $this->logActivity->generateLog("Updated a hotline number(ID - $hotlineId)");

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

        $this->logActivity->generateLog("Removed a hotline number(ID - $hotlineId)");
        $hotlineNumber->delete();

        return response([]);
    }
}
