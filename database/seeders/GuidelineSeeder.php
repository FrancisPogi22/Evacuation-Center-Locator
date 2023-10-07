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
            'user_id' => 2,
            'organization' => 'CSWD'
        ]);

        Guideline::insert([
            'type' => ('ROAD ACCIDENT GUIDELINE'),
            'user_id' => 1,
            'organization' => 'CDRRMO'
        ]);

        Guideline::insert([
            'type' => ('EARTHQUAKE GUIDELINE'),
            'user_id' => 2,
            'organization' => 'CSWD'
        ]);

        Guideline::insert([
            'type' => ('FLASHFLOOD GUIDELINE'),
            'user_id' => 1,
            'organization' => 'CDRRMO'
        ]);
    }
}
