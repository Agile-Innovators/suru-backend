<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PartnerProfile;

class PartnerProfilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PartnerProfile::create([
            'user_id' => 3, 
            'description' => 'This is a partner description.',
            'website_url' => 'https://partnerwebsite.com',
            'partner_category_id' => 1, // Moving Service
        ]);
    }
}
