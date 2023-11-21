<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::insert([
            "name"=> "president",
            'email' => ('d@gmail.com'),
            'password' => Hash::make('d'),
            'organization' => 'CDRRMO',
            'position' => 'President',
            'status' => 'Active',
            'is_disable' => 0,
            'is_archive' => 0
        ]);

        User::insert([
            'name'=> 'vice',
            'email' => ('vc@gmail.com'),
            'password' => Hash::make('vc'),
            'organization' => 'CDRRMO',
            'position' => 'Vice President',
            'status' => 'Active',
            'is_disable' => 0,
            'is_suspend' => 0,
            'is_archive' => 0
        ]);

        User::insert([
            'name'=> 'focal',
            'email' => ('c@gmail.com'),
            'password' => Hash::make('c'),
            'organization' => 'CSWD',
            'position' => 'Focal',
            'status' => 'Active',
            'is_disable' => 0,
            'is_archive' => 0
        ]);

        User::insert([
            'name'=> 'encoder',
            'email' => ('e@gmail.com'),
            'password' => Hash::make('e'),
            'organization' => 'CSWD',
            'position' => 'Encoder',
            'status' => 'Active',
            'is_disable' => 0,
            'is_suspend' => 0,
            'is_archive' => 0
        ]);

        $this->call(DisasterSeeder::class);
        $this->call(EvacuationCenterSeeder::class);
        $this->call(GuidelineSeeder::class);
    }
}
