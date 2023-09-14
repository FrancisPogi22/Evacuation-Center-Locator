<?php

namespace Database\Seeders;

use App\Models\EvacuationCenter;
use Illuminate\Database\Seeder;

class EvacuationCenterSeeder extends Seeder
{
    public function run(): void
    {

        EvacuationCenter::insert([
            'user_id' => 2,
            'name' => 'Butong Elementary School',
            'barangay_name' => 'Butong',
            'latitude' => '14.2862',
            'longitude' => '121.1338',
            'status' => 'Active',
            'is_archive'=> 0,
        ]);

        EvacuationCenter::insert([
            'user_id' => 2,
            'name' => 'Banay-Banay Elementary School',
            'barangay_name' => 'Banay-Banay',
            'latitude' => '14.2546',
            'longitude' => '121.1295',
            'status' => 'Active',
            'is_archive'=> 0,
        ]);

        EvacuationCenter::insert([
            'user_id' => 2,
            'name' => 'Marinig Elementary School',
            'barangay_name' => 'Marinig',
            'latitude' => '14.2704',
            'longitude' => '121.1539',
            'status' => 'Active',
            'is_archive'=> 0,
        ]);
    }
}
