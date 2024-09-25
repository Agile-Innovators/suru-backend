<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PartnerCategory;

class PartnerCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PartnerCategory::create(['name' => 'Moving Service']);
        PartnerCategory::create(['name' => 'Cleaning Service']);
        PartnerCategory::create(['name' => 'Property Maintenance']);
        PartnerCategory::create(['name' => 'Legal Services']);
        PartnerCategory::create(['name' => 'Interior Design']);
        PartnerCategory::create(['name' => 'Security Services']); 
        PartnerCategory::create(['name' => 'Insurance Services']);
        PartnerCategory::create(['name' => 'Real Estate Agents']);
    }
}
