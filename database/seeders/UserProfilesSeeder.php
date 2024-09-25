<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserProfile;

class UserProfilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserProfile::create([
            'user_id' => 1, 
            'lastname1' => 'Doe',
            'lastname2' => 'Smith',
        ]);
        
        UserProfile::create([
            'user_id' => 2, 
            'lastname1' => 'Doe',
            'lastname2' => 'Johnson',
        ]);
    }
}
