<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Property;

class PropertiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Property::create([
            'title' => 'Beautiful House',
            'description' => 'A beautiful house in a nice neighborhood.',
            'price' => 250000,
            'deposit_price' => 0,
            'availability_date' => '2024-10-01',
            'size_in_m2' => 150,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'floors' => 1,
            'garages' => 1,
            'pools' => 0,
            'pets_allowed' => true,
            'green_area' => true,
            'property_category_id' => 1, // House
            'property_transaction_type_id' => 1, // Sale
            'city_id' => 1, // Escazú (San José, CR)
            'currency_id' => 2, // Escazú (San José, CR)
            'user_id' => 2, // Normal User
        ]);
    }
}
