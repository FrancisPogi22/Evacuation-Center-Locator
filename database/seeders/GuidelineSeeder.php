<?php

namespace Database\Seeders;

use App\Models\Guideline;
use Illuminate\Database\Seeder;

class GuidelineSeeder extends Seeder
{
    public function run(): void
    {
        Guideline::insert([
            'type' => ('TYPHOON GUIDELINE'),
            'organization' => 'CSWD'
        ]);

        Guideline::insert([
            'type' => ('ROAD ACCIDENT GUIDELINE'),
            'organization' => 'CDRRMO'
        ]);

        Guideline::insert([
            'type' => ('EARTHQUAKE GUIDELINE'),
            'organization' => 'CSWD'
        ]);

        Guideline::insert([
            'type' => ('FLASHFLOOD guideliGUIDELINEne'),
            'organization' => 'CDRRMO'
        ]);
    }
}
