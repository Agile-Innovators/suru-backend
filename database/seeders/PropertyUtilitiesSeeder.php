<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PropertyUtility;

class PropertyUtilitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PropertyUtility::create([
            'property_id' => 1, 
            'utility_id' => 1, // Electricity
        ]);

        PropertyUtility::create([
            'property_id' => 1, 
            'utility_id' => 2, // Water
        ]);

        PropertyUtility::create([
            'property_id' => 1, 
            'utility_id' => 3, // Furnished
        ]);

        PropertyUtility::create([
            'property_id' => 1, 
            'utility_id' => 4, // Wifi
        ]);
    }
}
