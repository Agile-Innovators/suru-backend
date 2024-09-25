<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BusinessService;

class BusinessServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Moving Services
        BusinessService::create(['name' => 'Residential Moving', 'description' => 'Reliable residential moving service offering packing, loading, transportation, and unloading.', 'partner_category_id' => 1]);
        BusinessService::create(['name' => 'Office Moving', 'description' => 'Efficient office moving service ensuring minimal disruption to your business operations with careful packing and transportation.', 'partner_category_id' => 1]);
        BusinessService::create(['name' => 'Local Packing and Unpacking Services', 'description' => 'Professional packing and unpacking services to ensure your belongings are safely packed and efficiently unpacked at your new location.', 'partner_category_id' => 1]);
        BusinessService::create(['name' => 'Furniture Arrangement, Assembly, and Disassembly', 'description' => 'Expert service for arranging, assembling, and disassembling furniture to fit your space perfectly.', 'partner_category_id' => 1]);
        BusinessService::create(['name' => 'Cargo Handling and Transport', 'description' => 'Safe and reliable handling and transportation of cargo for local deliveries.', 'partner_category_id' => 1]);
        BusinessService::create(['name' => 'Industrial Packing, Handling, and Transportation', 'description' => 'Specialized services for the packing, handling, and transportation of industrial equipment and materials.', 'partner_category_id' => 1]);
        BusinessService::create(['name' => 'Commercial Cargo Transportation', 'description' => 'Dependable transportation services for commercial cargo, ensuring timely deliveries.', 'partner_category_id' => 1]);
        BusinessService::create(['name' => 'Specialized Transport and Handling of Equipment', 'description' => 'Expert handling and transport of specialized items such as safes, pianos, computers, ATMs, and more.', 'partner_category_id' => 1]);
        BusinessService::create(['name' => 'Local Art Handling and Transport', 'description' => 'Careful handling and transportation of artworks to ensure they arrive safely at their destination.', 'partner_category_id' => 1]);
        BusinessService::create(['name' => 'Commercial Logistics and Distribution', 'description' => 'Comprehensive logistics and distribution services tailored for commercial needs.', 'partner_category_id' => 1]);
        BusinessService::create(['name' => 'Storage and Warehousing Service', 'description' => 'Flexible storage and warehousing solutions for both temporary and permanent needs.', 'partner_category_id' => 1]);
    }
}
