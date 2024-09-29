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
            'image_name' => 'property1_image1.jpg',
        ]);

        PropertyImage::create([
            'property_id' => 1,
            'image_name' => 'property1_image2.jpg',
        ]);

        PropertyImage::create([
            'property_id' => 1,
            'image_name' => 'property1_image3.jpg',
        ]);

        PropertyImage::create([
            'property_id' => 1,
            'image_name' => 'property1_image4.jpg',
        ]);

        PropertyImage::create([
            'property_id' => 1,
            'image_name' => 'property1_image5.jpg',
        ]);

        PropertyImage::create([
            'property_id' => 1,
            'image_name' => 'property1_image6.jpg',
        ]);
    }
}
