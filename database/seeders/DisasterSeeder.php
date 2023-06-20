<?php

namespace Database\Seeders;

use App\Models\Disaster;
use Illuminate\Database\Seeder;

class DisasterSeeder extends Seeder
{
    public function run(): void
    {

        Disaster::insert([
            'type' => ('Typhoon')
        ]);

        Disaster::insert([
            'type' => ('Flooding')
        ]);
    }
}
