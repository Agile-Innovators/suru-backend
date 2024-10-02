<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Country;
use Illuminate\Database\Seeder;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Country::create(['name' => 'Costa Rica', 'iso' => 'CR', 'phone_code' => '+506']);
        // Country::create(['name' => 'Panama', 'iso' => 'PA', 'phone_code' => '+507']);
        // Country::create(['name' => 'Nicaragua', 'iso' => 'NI', 'phone_code' => '+505']);
        // Country::create(['name' => 'El Salvador', 'iso' => 'SV', 'phone_code' => '+503']);
        // Country::create(['name' => 'Guatemala', 'iso' => 'GT', 'phone_code' => '+502']);
        // Country::create(['name' => 'Honduras', 'iso' => 'HN', 'phone_code' => '+504']);
        // Country::create(['name' => 'Belize', 'iso' => 'BZ', 'phone_code' => '+501']);
        // Country::create(['name' => 'Mexico', 'iso' => 'MX', 'phone_code' => '+52']);
        // Country::create(['name' => 'Colombia', 'iso' => 'CO', 'phone_code' => '+57']);
        // Country::create(['name' => 'Venezuela', 'iso' => 'VE', 'phone_code' => '+58']);
        // Country::create(['name' => 'Ecuador', 'iso' => 'EC', 'phone_code' => '+593']);
        // Country::create(['name' => 'Peru', 'iso' => 'PE', 'phone_code' => '+51']);
        // Country::create(['name' => 'Chile', 'iso' => 'CL', 'phone_code' => '+56']);
        // Country::create(['name' => 'Argentina', 'iso' => 'AR', 'phone_code' => '+54']);
        // Country::create(['name' => 'Uruguay', 'iso' => 'UY', 'phone_code' => '+598']);
        // Country::create(['name' => 'Cuba', 'iso' => 'CU', 'phone_code' => '+53']);
        // Country::create(['name' => 'Dominican Republic', 'iso' => 'DO', 'phone_code' => '+1-809']);
    }
}
