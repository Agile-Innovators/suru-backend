<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            CountriesSeeder::class,
            RegionsSeeder::class,
            CitiesSeeder::class,
            UserTypesSeeder::class,
            UsersSeeder::class,
            UserProfilesSeeder::class,
            PaymentFrequencySeeder::class,
            CurrencySeeder::class,
            PartnerCategoriesSeeder::class,
            PartnerProfilesSeeder::class,
            UserOperationalHoursSeeder::class,
            UserLocationsSeeder::class,
            PropertyCategoriesSeeder::class,
            UtilitiesSeeder::class,
            PropertyTransactionTypesSeeder::class,
            PropertiesSeeder::class,
            PropertyUtilitiesSeeder::class,
            PropertyImagesSeeder::class,
            FavoritesSeeder::class,
            AppointmentsSeeder::class,
            BusinessServicesSeeder::class,
            PartnerServicesSeeder::class,
        ]);
        
    }
}
