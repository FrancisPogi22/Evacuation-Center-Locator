<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use App\Models\Evacuee;
use App\Models\Disaster;
use App\Models\Typhoon;
use App\Models\Flashflood;
use App\Models\Guideline;
use Illuminate\Http\Request;
use App\Models\EvacuationCenter;
use Yajra\DataTables\DataTables;

class CswdController extends Controller
{
    private $disaster, $typhoon, $flashflood, $evacuee, $evacuation, $guideline, $guide;

    function __construct()
    {
        $this->disaster = new Disaster;
        $this->typhoon = new Typhoon;
        $this->flashflood = new Flashflood;
        $this->evacuee = new Evacuee;
        $this->guideline = new Guideline();
        $this->guide = new Guide();
        $this->evacuation = new EvacuationCenter;
    }
    
    public function dashboard()
    {
        $activeEvacuation = $this->evacuation->isActive();
        $inActiveEvacuation = $this->evacuation->isInactive();
        $inEvacuationCenter = $this->evacuee->countEvacueeOnEvacuation();
        $isReturned = $this->evacuee->countEvacueeReturned();
        $typhoonMaleData = $this->evacuee->countEvacuee('Typhoon', 'Male');
        $typhoonFemaleData = $this->evacuee->countEvacuee('Typhoon', 'Female');
        $floodingMaleData = $this->evacuee->countEvacuee('Flashflood', 'Male');
        $floodingFemaleData = $this->evacuee->countEvacuee('Flashflood', 'Female');
        $typhoonData = $this->evacuee->countEvacueeWithDisablities('Typhoon');
        $typhoon_4Ps = intval($typhoonData[0]->{'4Ps'});
        $typhoon_PWD = intval($typhoonData[0]->PWD);
        $typhoon_pregnant = intval($typhoonData[0]->pregnant);
        $typhoon_lactating = intval($typhoonData[0]->lactating);
        $typhoon_student = intval($typhoonData[0]->student);
        $typhoon_working = intval($typhoonData[0]->working);
        $floodingData = $this->evacuee->countEvacueeWithDisablities('Flashflood');
        $flooding_4Ps = intval($floodingData[0]->{'4Ps'});
        $flooding_PWD = intval($floodingData[0]->PWD);
        $flooding_pregnant = intval($floodingData[0]->pregnant);
        $flooding_lactating = intval($floodingData[0]->lactating);
        $flooding_student = intval($floodingData[0]->student);
        $flooding_working = intval($floodingData[0]->working);
        return view('userpage.dashboard', compact(
            'activeEvacuation',
            'inActiveEvacuation',
            'inEvacuationCenter',
            'isReturned',
            'typhoonMaleData',
            'typhoonFemaleData',
            'floodingMaleData',
            'floodingFemaleData',
            'typhoon_4Ps',
            'typhoon_PWD',
            'typhoon_pregnant',
            'typhoon_lactating',
            'typhoon_student',
            'typhoon_working',
            'flooding_4Ps',
            'flooding_PWD',
            'flooding_pregnant',
            'flooding_lactating',
            'flooding_student',
            'flooding_working'
        ));
    }

    public function manageEvacueeInformation(Request $request)
    {
        $evacuationList = $this->evacuation->retrieveAllEvacuation();
        $typhoonList =  $this->typhoon->retrieveAllActiveTyphoon();
        $flashfloodList = $this->flashflood->retrieveAllActiveFlashflood();
        $disasterList = null;
        if ($typhoonList->isNotEmpty() && $flashfloodList->isNotEmpty()) {
            $disasterList = $this->disaster->retrieveAllDisaster();
        } else if ($typhoonList->isNotEmpty() && $flashfloodList->isEmpty()) {
            $disasterList = $this->disaster->retrieveSpecificDisaster(1);
        } else if ($flashfloodList->isNotEmpty() && $typhoonList->isEmpty()) {
            $disasterList = $this->disaster->retrieveSpecificDisaster(2);
        }
        return view('userpage.evacuee.evacuee', compact('evacuationList', 'disasterList', 'typhoonList', 'flashfloodList'));
    }

    public function eligtasGuideline()
    {
        $guideline = $this->guideline->retrieveAll();
        return view('userpage.guideline.eligtasGuideline', compact('guideline'));
    }

    public function guide($guidelineId)
    {
        $guide = $this->guide->retreiveAllGuide($guidelineId);
        return view('userpage.guideline.guide', compact('guide', 'guidelineId'));
    }

    public function evacuationCenter()
    {
        $evacuationCenter = EvacuationCenter::all();
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

    public function evacuationManage()
    {
        return view('userpage.evacuationCenter.evacuation');
    }

    public function disaster(Request $request)
    {
        $disaster = $this->disaster->retrieveAllDisaster();
        if ($request->ajax()) {
            return DataTables::of($disaster)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editBtn = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" data-original-title="Edit" class="bg-slate-700 hover:bg-slate-900 py-1.5 btn-sm mr-2 text-white updateDisaster">Edit</a>';
                    $btn = $editBtn . '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" data-original-title="Remove" class="bg-red-700 hover:bg-red-900 py-1.5 btn-sm mr-2 text-white removeDisaster">Remove</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('userpage.disaster.disaster', compact('disaster'));
    }
}
