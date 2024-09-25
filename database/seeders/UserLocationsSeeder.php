<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserLocation;

class UserLocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserLocation::create([
            'user_id' => 2, 
            'city_id' => 1, // Escazú, San José, Costa Rica
            'address' => '123 Partner St, City',
        ]);

        UserLocation::create([
            'user_id' => 3, 
            'city_id' => 2, // Santa Ana, San José, Costa Rica
            'address' => '123 Partner St, City',
        ]);

        UserLocation::create([
            'user_id' => 3, 
            'city_id' => 3, // Heredia, Heredia, Costa Rica
            'address' => '123 Partner St, City',
        ]);
    }
}
