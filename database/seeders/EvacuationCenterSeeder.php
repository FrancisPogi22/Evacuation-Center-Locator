<?php

namespace Database\Seeders;

use App\Models\EvacuationCenter;
use Illuminate\Database\Seeder;

class EvacuationCenterSeeder extends Seeder
{
    public function run(): void
    {
        EvacuationCenter::insert([
            'name' => 'Niugan Elementary School',
            'barangay_name' => 'Niugan',
            'latitude' => '14.2626',
            'longitude' => '121.1227',
            'status' => 'Active',
            'is_archive'=> 0,
        ]);

        EvacuationCenter::insert([
            'name' => 'Butong Elementary School',
            'barangay_name' => 'Butong',
            'latitude' => '14.2862',
            'longitude' => '121.1338',
            'status' => 'Active',
            'is_archive'=> 0,
        ]);

        EvacuationCenter::insert([
            'name' => 'Southville 1 Elementary School',
            'barangay_name' => 'Banay-Banay',
            'latitude' => '14.2677',
            'longitude' => '121.1392',
            'status' => 'Active',
            'is_archive'=> 0,
        ]);

        EvacuationCenter::insert([
            'name' => 'Southville 1 Integrated National High School',
            'barangay_name' => 'Banay-Banay',
            'latitude' => '14.2660',
            'longitude' => '121.1410',
            'status' => 'Active',
            'is_archive'=> 0,
        ]);

        EvacuationCenter::insert([
            'name' => 'Banay-Banay Elementary School',
            'barangay_name' => 'Banay-Banay',
            'latitude' => '14.2546',
            'longitude' => '121.1295',
            'status' => 'Active',
            'is_archive'=> 0,
        ]);

        EvacuationCenter::insert([
            'name' => 'Marinig Elementary School',
            'barangay_name' => 'Marinig',
            'latitude' => '14.2704',
            'longitude' => '121.1539',
            'status' => 'Active',
            'is_archive'=> 0,
        ]);

        EvacuationCenter::insert([
            'name' => 'Bigaa Elementary School',
            'barangay_name' => 'Bigaa',
            'latitude' => '14.2878',
            'longitude' => '121.1302',
            'status' => 'Active',
            'is_archive'=> 0,
        ]);

        EvacuationCenter::insert([
            'name' => 'Cabuyao Athletes Basic School',
            'barangay_name' => 'Banay-Banay',
            'latitude' => '14.2597',
            'longitude' => '121.1377',
            'status' => 'Active',
            'is_archive'=> 0,
        ]);

        EvacuationCenter::insert([
            'name' => 'Gulod Elementary School',
            'barangay_name' => 'Gulod',
            'latitude' => '14.2556',
            'longitude' => '121.1618',
            'status' => 'Active',
            'is_archive'=> 0,
        ]);
    }
}
