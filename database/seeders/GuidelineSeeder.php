<?php

namespace Database\Seeders;

use App\Models\Guideline;
use Illuminate\Database\Seeder;

class GuidelineSeeder extends Seeder
{
    public function run(): void
    {
        Guideline::insert([
            'type' => 'ROAD ACCIDENT',
            'user_id' => 1,
            'organization' => 'CDRRMO',
            'guideline_img' => null
        ]);

        Guideline::insert([
            'type' => 'FIRE AND EXPLOSIONS',
            'user_id' => 1,
            'organization' => 'CDRRMO',
            'guideline_img' => null
        ]);

        Guideline::insert([
            'type' => 'STRUCTURAL AND INFRASTRUCTURE FAILURES',
            'user_id' => 1,
            'organization' => 'CDRRMO',
            'guideline_img' => null
        ]);

        Guideline::insert([
            'type' => 'ENVIRONMENTAL',
            'user_id' => 1,
            'organization' => 'CDRRMO',
            'guideline_img' => null
        ]);

        Guideline::insert([
            'type' => 'MISCELLANEOUS',
            'user_id' => 1,
            'organization' => 'CDRRMO',
            'guideline_img' => null
        ]);

        Guideline::insert([
            'type' => 'CRIME',
            'user_id' => 1,
            'organization' => 'CDRRMO',
            'guideline_img' => null
        ]);

        Guideline::insert([
            'type' => 'ROAD BLOCK',
            'user_id' => 2,
            'organization' => 'CSWD',
            'guideline_img' => null
        ]);

        Guideline::insert([
            'type' => 'TYPHOON',
            'user_id' => 2,
            'organization' => 'CSWD',
            'guideline_img' => null
        ]);

        Guideline::insert([
            'type' => 'EARTHQUAKE',
            'user_id' => 2,
            'organization' => 'CSWD',
            'guideline_img' => null
        ]);

        Guideline::insert([
            'type' => 'TSUNAMI',
            'user_id' => 2,
            'organization' => 'CSWD',
            'guideline_img' => null
        ]);

        Guideline::insert([
            'type' => 'VOLCANIC ERUPTION',
            'user_id' => 2,
            'organization' => 'CSWD',
            'guideline_img' => null
        ]);

        Guideline::insert([
            'type' => 'LANDSLIDE',
            'user_id' => 2,
            'organization' => 'CSWD',
            'guideline_img' => null
        ]);

        Guideline::insert([
            'type' => 'FLOODS',
            'user_id' => 2,
            'organization' => 'CSWD',
            'guideline_img' => null
        ]);
    }
}
