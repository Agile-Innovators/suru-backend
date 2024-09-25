<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PropertyCategory;

class PropertyCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PropertyCategory::create(['name' => 'House']);
        PropertyCategory::create(['name' => 'Apartment']);
        PropertyCategory::create(['name' => 'Bare Land']);
        PropertyCategory::create(['name' => 'Retail Space']);
        PropertyCategory::create(['name' => 'Studio']);
    }
}
