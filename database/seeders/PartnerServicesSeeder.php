<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PartnerService;

class PartnerServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PartnerService::create([
            'partner_id' => 3,
            'business_service_id' => 1, // Residential Moving
            'price' => 100, // USD
            'price_max' => null,
        ]);

        PartnerService::create([
            'partner_id' => 3,
            'business_service_id' => 2, // Commercial Moving
            'price' => 200, // USD
            'price_max' => 150,
        ]);
    }
}
