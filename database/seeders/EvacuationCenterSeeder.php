<?php

namespace Database\Seeders;

use App\Models\EvacuationCenter;
use Illuminate\Database\Seeder;

class EvacuationCenterSeeder extends Seeder
{
    public function run(): void
    {

        EvacuationCenter::insert([
            'name' => 'Butong Elementary School',
            'barangay_name' => 'Butong',
            'latitude' => '14.2862',
            'longitude' => '121.1338',
            'capacity' => '120',
            'status' => 'Active',
            'is_archive'=> 0,
        ]);

        EvacuationCenter::insert([
            'name' => 'Banay-Banay Elementary School',
            'barangay_name' => 'Banay-Banay',
            'latitude' => '14.2546',
            'longitude' => '121.1295',
            'capacity' => '120',
            'status' => 'Active',
            'is_archive'=> 0,
        ]);

        EvacuationCenter::insert([
            'name' => 'Marinig Elementary School',
            'barangay_name' => 'Marinig',
            'latitude' => '14.2704',
            'longitude' => '121.1539',
            'capacity' => '120',
            'status' => 'Active',
            'is_archive'=> 0,
        ]);
    }
}
