<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PropertyTransactionType;

class PropertyTransactionTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PropertyTransactionType::create(['name' => 'Sale']);
        PropertyTransactionType::create(['name' => 'Rent']);
        PropertyTransactionType::create(['name' => 'Dual']);
    }
}
