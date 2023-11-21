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
            "name"=> "Frazier Mhon Perez",
            'email' => ('fraziermhonperez@gmail.com'),
            'password' => Hash::make('perezlangto'),
            'organization' => 'CDRRMO',
            'position' => 'President',
            'status' => 'Active',
            'is_disable' => 0,
            'is_archive' => 0
        ]);

        User::insert([
            'name'=> 'Francis Cabusas',
            'email' => ('francistengteng10@gmail.com'),
            'password' => Hash::make('francispogi'),
            'organization' => 'CSWD',
            'position' => 'Focal',
            'status' => 'Active',
            'is_disable' => 0,
            'is_archive' => 0
        ]);

        $this->call(DisasterSeeder::class);
        $this->call(EvacuationCenterSeeder::class);
    }
}
