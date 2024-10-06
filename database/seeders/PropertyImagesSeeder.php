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
            'url' => 'https://res.cloudinary.com/dvwtm566p/image/upload/v1728179511/properties/gtjmc4yhy93a9841ho1d.jpg',
            'public_id' => 'properties/gtjmc4yhy93a9841ho1d'
        ]);

        PropertyImage::create([
            'property_id' => 1,
            'url' => 'https://res.cloudinary.com/dvwtm566p/image/upload/v1728179511/properties/dgvlvqqzh5b2kz7ui1pb.jpg',
            'public_id' => 'properties/dgvlvqqzh5b2kz7ui1pb'
        ]);

        PropertyImage::create([
            'property_id' => 1,
            'url' => 'https://res.cloudinary.com/dvwtm566p/image/upload/v1728179511/properties/ahomz3xt6uxj6rylivdi.jpg',
            'public_id' => 'properties/ahomz3xt6uxj6rylivdi'
        ]);

        PropertyImage::create([
            'property_id' => 1,
            'url' => 'https://res.cloudinary.com/dvwtm566p/image/upload/v1728179511/properties/mlbadmmqdcyvz4faksh0.jpg',
            'public_id' => 'properties/mlbadmmqdcyvz4faksh0'
        ]);

        PropertyImage::create([
            'property_id' => 1,
            'url' => 'https://res.cloudinary.com/dvwtm566p/image/upload/v1728179511/properties/uxouqw3enagqssu9zhen.jpg',
            'public_id' => 'properties/uxouqw3enagqssu9zhen'
        ]);

        PropertyImage::create([
            'property_id' => 1,
            'url' => 'https://res.cloudinary.com/dvwtm566p/image/upload/v1728179511/properties/pmtalacmmu57d1b4t9md.jpg',
            'public_id' => 'properties/pmtalacmmu57d1b4t9md'
        ]);
    }
}
