<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use App\Models\Guideline;
use App\Models\EvacuationCenter;
use Illuminate\Support\Facades\Crypt;

class ResidentController extends Controller
{
    private $guide, $guideline, $evacuationCenter;
    
    public function __construct()
    {
        $this->guide = new Guide;
        $this->guideline = new Guideline;
        $this->evacuationCenter = new EvacuationCenter;
    }
    public function dashboard()
    {
        return view('/userpage/dashboard');
    }

    public function residentEligtasGuideline()
    {
        $guideline = $this->guideline->all();

        return view('userpage.guideline.eligtasGuideline', compact('guideline'));
    }

    public function residentEligtasGuide($guidelineId)
    {
        $guide = $this->guide->where('guideline_id', Crypt::decryptString($guidelineId))->get();

        return view('userpage.guideline.guide', compact('guide', 'guidelineId'));
    }

    public function residentEvacuationCenter()
    {
        $evacuationCenter = $this->evacuationCenter->all();

        $initialMarkers = [
            [
                'position' => [
                    'lat' => 28.625485,
                    'lng' => 79.821091
                ],
                'label' => ['color' => 'white', 'text' => 'P1'],
                'draggable' => true
            ],
            [
                'position' => [
                    'lat' => 28.625293,
                    'lng' => 79.817926
                ],
                'label' => ['color' => 'white', 'text' => 'P2'],
                'draggable' => false
            ],
            [
                'position' => [
                    'lat' => 28.625182,
                    'lng' => 79.81464
                ],
                'label' => ['color' => 'white', 'text' => 'P3'],
                'draggable' => true
            ]
        ];

        return view('userpage.evacuationCenter.evacuationCenter', ['evacuationCenter' => $evacuationCenter, 'initialMarkers' => $initialMarkers]);
    }

    public function residentReportAccident()
    {
        return view('userpage.reportAccident');
    }

    public function residentHotlineNumber()
    {
        return view('userpage.hotlineNumbers');
    }

    public function residentAbout()
    {
        return view('userpage.about');
    }
}
