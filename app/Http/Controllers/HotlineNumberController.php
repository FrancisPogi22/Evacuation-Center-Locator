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
        $this->hotlineNumbers = new HotlineNumbers;
        $this->logActivity    = new ActivityUserLog;
    }

    public function addHotlineNumber(Request $request)
    {
        $hotlineNumberValidation = Validator::make($request->only('logo', 'label', 'number'), [
            'logo'   => 'image',
            'label'  => 'required',
            'number' => 'required|numeric'
        ]);

        if ($hotlineNumberValidation->fails())
            return response(['status' => 'warning', 'message' => $hotlineNumberValidation->errors()->first()]);

        $hotlineLogo     = $request->file('logo');
        $hotlineLogoPath = $hotlineLogo;

        if ($hotlineLogoPath) {
            $hotlineLogoPath = $hotlineLogo->store();
            $hotlineLogo->move(public_path('assets/img/'), $hotlineLogoPath);
        }

        $hotlineNumber = $this->hotlineNumbers->create([
            'label'   => Str::title(trim($request->label)),
            'number'  => trim($request->number),
            'logo'    => $hotlineLogoPath,
            'user_id' => auth()->user()->id
        ]);

        $hotlineId = $hotlineNumber->id;
        $this->logActivity->generateLog($hotlineId, $hotlineNumber->label, 'added a new hotline number');

        return response(['hotlineId' => $hotlineId, 'hotlineLogo' => $hotlineLogoPath, 'label' => $hotlineNumber->label, 'number' => $hotlineNumber->number]);
    }

    public function updateHotlineNumber(Request $request, $hotlineId)
    {
        $hotlineNumberValidation = Validator::make($request->only('logo', 'label', 'number'), [
            'logo'   => 'image',
            'label'  => 'required',
            'number' => 'required|numeric'
        ]);

        if ($hotlineNumberValidation->fails())
            return response(['status' => 'warning', 'message' => $hotlineNumberValidation->errors()->first()]);

        $hotlineNumber       = $this->hotlineNumbers->find($hotlineId);
        $hotlineLogo         = $request->file('logo');
        $hotlineNumberData   = [
            'label'   => Str::title(trim($request->label)),
            'number'  => trim($request->number),
            'user_id' => auth()->user()->id
        ];

        if ($hotlineLogo) {
            $hotlineLogoOld            = $hotlineNumber->logo;
            $hotlineLogo               = $hotlineLogo->store();
            $hotlineNumberData['logo'] = $hotlineLogo;
            $request->logo->move(public_path('assets/img/'), $hotlineLogo);

            if ($hotlineLogoOld) {
                $hotlineLogoOldPath = public_path('assets/img/' . $hotlineLogoOld);

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
            $hotlineLogoPath = public_path('assets/img/' . $hotlineLogo);

            if (file_exists($hotlineLogoPath)) unlink($hotlineLogoPath);
        }

        $this->logActivity->generateLog($hotlineId, $hotlineNumber->label, 'removed a hotline number');
        $hotlineNumber->delete();

        return response()->json();
    }
}
