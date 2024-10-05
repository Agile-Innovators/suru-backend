<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PropertyImage;

class PropertyImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PropertyImage::create([
            'property_id' => 1, 
            'public_id' => 'property1_image1'
        ]);

        PropertyImage::create([
            'property_id' => 1,
            'public_id' => 'property1_image2'
        ]);
    }
}
