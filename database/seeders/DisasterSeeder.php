<?php

namespace Database\Seeders;

use App\Models\Disaster;
use Illuminate\Database\Seeder;

class DisasterSeeder extends Seeder
{
    public function run(): void
    {
        Disaster::insert([
            'name' => ('Typhoon Paeng'),
            'year' => ('2021'),
            'status' => "On Going",
            'is_archive' => 0,
            'user_id' => 2
        ]);

        Disaster::insert([
            'name' => ('Typhoon Ondoy'),
            'year' => ('2022'),
            'status' => "Inactive",
            'is_archive' => 0,
            'user_id' => 2
        ]);
    }
}
