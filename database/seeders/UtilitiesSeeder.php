<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Utility;

class UtilitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Utility::create(['name' => 'Electricity']);
        Utility::create(['name' => 'Water']);
        Utility::create(['name' => 'Furnished']);
        Utility::create(['name' => 'Wifi']);
        Utility::create(['name' => 'Cable TV']);
        Utility::create(['name' => 'Green Area']);
        Utility::create(['name' => 'Allowed Pets']);
        Utility::create(['name' => 'Electricity Access']);
        Utility::create(['name' => 'Water Access']);
    }
}
