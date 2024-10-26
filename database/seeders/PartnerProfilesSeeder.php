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
            'instagram_url' => 'https://instagram.com/partner',
            'facebook_url' => 'https://facebook.com/partner',
            'currency_id' => 2, // CRC
            'partner_category_id' => 1, // Moving Service
        ]);
    }
}
