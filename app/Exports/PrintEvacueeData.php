<?php

namespace App\Exports;

use App\Models\Disaster;
use App\Models\Evacuee;
use App\Models\EvacuationCenter;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use PhpOffice\PhpSpreadsheet\Style\Style as DefaultStyle;

class PrintEvacueeData implements FromView, ShouldAutoSize, WithStyles, WithDefaultStyles
{
    use Exportable;

    private $evacuee, $evacueeData, $onGoingDisaster, $evacuationCenter, $total, $families, $individuals, $male, $female, $seniorCitizen, $minors, $infants, $pwd, $pregnant, $lactating;

    public function __construct($disasterId, $barangay)
    {
        $this->evacuee          = new Evacuee;
        $this->onGoingDisaster  = Disaster::where('id', $disasterId)->value('name');
        $this->evacuationCenter = EvacuationCenter::where('status', 'Active')->where('barangay_name', $barangay)->count();
        $this->evacueeData      = $this->evacuee
            ->selectRaw('1 as families, infants AS infants, minors AS minors, senior_citizen AS seniorCitizen, 
                pwd AS pwd, pregnant AS pregnant, lactating AS lactating, individuals AS individuals, male AS male, 
                female AS female, barangay, evacuation_center.name AS evacuationAssigned, updated_at AS dateEntry')
            ->join('evacuation_center', 'evacuee.evacuation_id', '=', 'evacuation_center.id')
            ->where('disaster_id', $disasterId)
            ->where('barangay', $barangay)
            ->get();
        $evacueeData            = $this->evacuee->where('disaster_id', $disasterId)->where('evacuee.barangay', $barangay)->selectRaw('SUM(infants) as infants, SUM(minors) as minors, 
            SUM(senior_citizen) as seniorCitizen, SUM(pwd) as pwd, SUM(pregnant) as pregnant, SUM(lactating) as lactating, SUM(individuals) as individuals, 
            SUM(male) as male, SUM(female) as female, COUNT(id) as families')->first();
        $this->infants          = $evacueeData['infants'];
        $this->minors           = $evacueeData['minors'];
        $this->seniorCitizen    = $evacueeData['seniorCitizen'];
        $this->pwd              = $evacueeData['pwd'];
        $this->pregnant         = $evacueeData['pregnant'];
        $this->lactating        = $evacueeData['lactating'];
        $this->individuals      = $evacueeData['individuals'];
        $this->male             = $evacueeData['male'];
        $this->female           = $evacueeData['female'];
        $this->families         = $evacueeData['families'];
    }

    public function view(): View
    {
        return view('userpage.evacuee.evacueeDataExcel', [
            'evacueeData'      => $this->evacueeData,
            'onGoingDisaster'  => $this->onGoingDisaster,
            'evacuationCenter' => $this->evacuationCenter,
            'infants'          => $this->infants,
            'minors'           => $this->minors,
            'seniorCitizen'    => $this->seniorCitizen,
            'pwd'              => $this->pwd,
            'pregnant'         => $this->pregnant,
            'lactating'        => $this->lactating,
            'individuals'      => $this->individuals,
            'male'             => $this->male,
            'female'           => $this->female,
            'families'         => $this->families,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow    = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $header        = 'A6:' . $highestColumn . '6';

        $mergedCells = ['A1:N1', 'A2:N2', 'A3:N3', 'A4:N4', 'A5:N5'];

        foreach ($mergedCells as $cellRange) {
            $sheet->mergeCells($cellRange);
            $sheet->getStyle($cellRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($cellRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        }

        $sheet->getStyle('A' . $highestRow . ':' . $highestColumn . $highestRow)->getFont()->setBold(true);
        $sheet->getRowDimension(5)->setRowHeight(20);

        $sheet->getStyle($header)->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle($header)->getFill()->getStartColor()->setRGB('fde047');
        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $sheet->getRowDimension(6)->setRowHeight(40);
        $sheet->getStyle('6')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A3')->getFont()->setBold(true);
        $sheet->getStyle('A6:A' . $highestRow)->getFont()->setBold(true);
        $sheet->getStyle('B6:B' . $highestRow)->getFont()->setBold(true);
        $sheet->getStyle('C6:C' . $highestRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $highestRow)->getAlignment()->setWrapText(true);
        $sheet->getCell('A6')->getStyle()->getAlignment()->setWrapText(true);

        $sheet->getStyle('A7:' . $highestColumn . $highestRow)->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A7:' . $highestColumn . $highestRow)->getFill()->getStartColor()->setRGB('fef9c3');

        for ($row = 7; $row <= $highestRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(20);
        }

        $sheet->getStyle('A' . $highestRow . ':' . $highestColumn . $highestRow)->getFill()->getStartColor()->setRGB('fde047');
    }

    public function defaultStyles(DefaultStyle $defaultStyle)
    {
        return [
            'font' => [
                'name' => 'Calibri',
                'size' => 8.5,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ]
        ];
    }
}
