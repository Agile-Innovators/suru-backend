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
        // Moving Services
        BusinessService::create(['name' => 'Residential Moving', 'description' => 'Reliable residential moving service offering packing, loading, transportation, and unloading.', 'partner_category_id' => 1]);
        BusinessService::create(['name' => 'Office Relocation', 'description' => 'Efficient office relocation services tailored for businesses.', 'partner_category_id' => 1]);
        BusinessService::create(['name' => 'Furniture Assembly & Disassembly', 'description' => 'Furniture assembly and disassembly services to assist with moves.', 'partner_category_id' => 1]);
        BusinessService::create(['name' => 'Long-Distance Moving', 'description' => 'Safe and efficient long-distance moving services.', 'partner_category_id' => 1]);
        BusinessService::create(['name' => 'Packing & Unpacking', 'description' => 'Professional packing and unpacking services for residential and business moves.', 'partner_category_id' => 1]);
        BusinessService::create(['name' => 'Moving Insurance', 'description' => 'Insurance services to protect your belongings during the move.', 'partner_category_id' => 1]);
        BusinessService::create(['name' => 'Storage Solutions', 'description' => 'Short and long-term storage services for your moving needs.', 'partner_category_id' => 1]);
        BusinessService::create(['name' => 'Vehicle Transport', 'description' => 'Professional vehicle transport services during moves.', 'partner_category_id' => 1]);
        BusinessService::create(['name' => 'Specialty Item Moving', 'description' => 'Safe moving services for pianos, antiques, and other specialty items.', 'partner_category_id' => 1]);
        BusinessService::create(['name' => 'Eco-Friendly Moving', 'description' => 'Environmentally conscious moving services using eco-friendly packing materials.', 'partner_category_id' => 1]);

        // Cleaning Services
        BusinessService::create(['name' => 'House Cleaning', 'description' => 'Comprehensive cleaning services for homes, including dusting, mopping, and disinfecting.', 'partner_category_id' => 2]);
        BusinessService::create(['name' => 'Office Cleaning', 'description' => 'Thorough cleaning services for office spaces and businesses.', 'partner_category_id' => 2]);
        BusinessService::create(['name' => 'Carpet & Upholstery Cleaning', 'description' => 'Specialized cleaning services for carpets, sofas, and other upholstered furniture.', 'partner_category_id' => 2]);
        BusinessService::create(['name' => 'Window Cleaning', 'description' => 'Interior and exterior window cleaning services for homes and businesses.', 'partner_category_id' => 2]);
        BusinessService::create(['name' => 'Post-Construction Cleaning', 'description' => 'Detailed cleaning services after construction or renovation projects.', 'partner_category_id' => 2]);
        BusinessService::create(['name' => 'Move-In/Move-Out Cleaning', 'description' => 'Cleaning services for homes during move-ins or move-outs.', 'partner_category_id' => 2]);
        BusinessService::create(['name' => 'Deep Cleaning', 'description' => 'Intensive cleaning service to reach deep dirt, dust, and allergens.', 'partner_category_id' => 2]);
        BusinessService::create(['name' => 'Green Cleaning', 'description' => 'Environmentally-friendly cleaning services using non-toxic products.', 'partner_category_id' => 2]);
        BusinessService::create(['name' => 'Pressure Washing', 'description' => 'Exterior cleaning services using high-pressure water for driveways and patios.', 'partner_category_id' => 2]);
        BusinessService::create(['name' => 'Airbnb Cleaning', 'description' => 'Specialized cleaning services for short-term rental properties.', 'partner_category_id' => 2]);

        // Property Maintenance
        BusinessService::create(['name' => 'Lawn Care', 'description' => 'Comprehensive lawn care services including mowing, trimming, and fertilizing.', 'partner_category_id' => 3]);
        BusinessService::create(['name' => 'HVAC Maintenance', 'description' => 'Heating, ventilation, and air conditioning system maintenance services.', 'partner_category_id' => 3]);
        BusinessService::create(['name' => 'Plumbing Services', 'description' => 'Plumbing maintenance and repair services for residential and commercial properties.', 'partner_category_id' => 3]);
        BusinessService::create(['name' => 'Electrical Repairs', 'description' => 'Electrical maintenance and repair services for homes and businesses.', 'partner_category_id' => 3]);
        BusinessService::create(['name' => 'Roof Inspection & Repair', 'description' => 'Roof maintenance services including inspections and minor repairs.', 'partner_category_id' => 3]);
        BusinessService::create(['name' => 'Handyman Services', 'description' => 'General repair services for small fixes around the house.', 'partner_category_id' => 3]);
        BusinessService::create(['name' => 'Gutter Cleaning', 'description' => 'Professional gutter cleaning services to prevent blockages and damage.', 'partner_category_id' => 3]);
        BusinessService::create(['name' => 'Tree Trimming', 'description' => 'Tree trimming and pruning services for residential properties.', 'partner_category_id' => 3]);
        BusinessService::create(['name' => 'Pool Maintenance', 'description' => 'Regular pool cleaning and maintenance services.', 'partner_category_id' => 3]);
        BusinessService::create(['name' => 'Pest Control', 'description' => 'Pest control services to eliminate and prevent infestations.', 'partner_category_id' => 3]);

        // Legal Services
        BusinessService::create(['name' => 'Legal Consultation', 'description' => 'Initial legal consultation to offer advice on various matters.', 'partner_category_id' => 4]);
        BusinessService::create(['name' => 'Contract Drafting', 'description' => 'Legal services for drafting contracts and agreements.', 'partner_category_id' => 4]);
        BusinessService::create(['name' => 'Real Estate Law', 'description' => 'Legal assistance with real estate transactions and disputes.', 'partner_category_id' => 4]);
        BusinessService::create(['name' => 'Tax Law Consulting', 'description' => 'Expert tax law consulting for businesses and individuals.', 'partner_category_id' => 4]);
        BusinessService::create(['name' => 'Immigration Law', 'description' => 'Legal services for immigration and visa-related issues.', 'partner_category_id' => 4]);
        BusinessService::create(['name' => 'Family Law', 'description' => 'Legal representation for family-related matters like divorce and custody.', 'partner_category_id' => 4]);
        BusinessService::create(['name' => 'Criminal Defense', 'description' => 'Legal defense services for criminal cases.', 'partner_category_id' => 4]);
        BusinessService::create(['name' => 'Intellectual Property', 'description' => 'Legal services to protect copyrights and trademarks.', 'partner_category_id' => 4]);
        BusinessService::create(['name' => 'Business Law', 'description' => 'Legal services for corporate governance and compliance.', 'partner_category_id' => 4]);
        BusinessService::create(['name' => 'Employment Law', 'description' => 'Legal assistance with labor and employment disputes.', 'partner_category_id' => 4]);

        // Interior Design
        BusinessService::create(['name' => 'Home Staging', 'description' => 'Professional home staging service offering interior design and decoration.', 'partner_category_id' => 5]);
        BusinessService::create(['name' => 'Residential Interior Design', 'description' => 'Interior design services for homes and apartments.', 'partner_category_id' => 5]);
        BusinessService::create(['name' => 'Commercial Interior Design', 'description' => 'Interior design services for offices and retail spaces.', 'partner_category_id' => 5]);
        BusinessService::create(['name' => 'Furniture Selection & Layout', 'description' => 'Assistance with selecting and arranging furniture.', 'partner_category_id' => 5]);
        BusinessService::create(['name' => 'Lighting Design', 'description' => 'Expert lighting design services for homes and offices.', 'partner_category_id' => 5]);
        BusinessService::create(['name' => 'Color Consultation', 'description' => 'Professional advice on choosing colors for interiors.', 'partner_category_id' => 5]);
        BusinessService::create(['name' => 'Kitchen Design', 'description' => 'Customized kitchen design and remodeling services.', 'partner_category_id' => 5]);
        BusinessService::create(['name' => 'Bathroom Design', 'description' => 'Professional design services for modern and functional bathrooms.', 'partner_category_id' => 5]);
        BusinessService::create(['name' => 'Custom Furniture Design', 'description' => 'Design and creation of bespoke furniture.', 'partner_category_id' => 5]);
        BusinessService::create(['name' => 'Outdoor Living Spaces', 'description' => 'Design services for outdoor living spaces like patios and decks.', 'partner_category_id' => 5]);

        // Security Services
        BusinessService::create(['name' => 'Home Security Systems', 'description' => 'Installation of home security systems including cameras and alarms.', 'partner_category_id' => 6]);
        BusinessService::create(['name' => 'Business Surveillance', 'description' => 'Comprehensive surveillance systems for businesses.', 'partner_category_id' => 6]);
        BusinessService::create(['name' => 'Access Control Systems', 'description' => 'Installation of access control systems to manage entry to buildings.', 'partner_category_id' => 6]);
        BusinessService::create(['name' => 'Fire Alarm Installation', 'description' => 'Installation of fire alarm systems for homes and businesses.', 'partner_category_id' => 6]);
        BusinessService::create(['name' => '24/7 Monitoring Services', 'description' => 'Round-the-clock monitoring services for residential and commercial properties.', 'partner_category_id' => 6]);
        BusinessService::create(['name' => 'Alarm Response', 'description' => 'Immediate response services to triggered alarms.', 'partner_category_id' => 6]);
        BusinessService::create(['name' => 'Security Consulting', 'description' => 'Expert consulting services to assess and improve security measures.', 'partner_category_id' => 6]);
        BusinessService::create(['name' => 'Cybersecurity Solutions', 'description' => 'Cybersecurity services for protecting digital assets.', 'partner_category_id' => 6]);
        BusinessService::create(['name' => 'Private Investigation', 'description' => 'Private investigation services for personal and corporate cases.', 'partner_category_id' => 6]);
        BusinessService::create(['name' => 'Personal Security Services', 'description' => 'Personal security and bodyguard services.', 'partner_category_id' => 6]);
    
        // Insurance Services
        BusinessService::create(['name' => 'Life Insurance', 'description' => 'Comprehensive life insurance plans to secure your family’s future.', 'partner_category_id' => 7]);
        BusinessService::create(['name' => 'Health Insurance', 'description' => 'Health insurance plans covering medical, dental, and vision care.', 'partner_category_id' => 7]);
        BusinessService::create(['name' => 'Auto Insurance', 'description' => 'Coverage for personal and commercial vehicles.', 'partner_category_id' => 7]);
        BusinessService::create(['name' => 'Home Insurance', 'description' => 'Insurance policies to protect your home from damage and theft.', 'partner_category_id' => 7]);
        BusinessService::create(['name' => 'Travel Insurance', 'description' => 'Comprehensive travel insurance plans for trips abroad.', 'partner_category_id' => 7]);
        BusinessService::create(['name' => 'Disability Insurance', 'description' => 'Income protection plans in case of illness or injury.', 'partner_category_id' => 7]);
        BusinessService::create(['name' => 'Business Insurance', 'description' => 'Commercial insurance plans for small and large businesses.', 'partner_category_id' => 7]);
        BusinessService::create(['name' => 'Pet Insurance', 'description' => 'Coverage for veterinary care for pets.', 'partner_category_id' => 7]);
        BusinessService::create(['name' => 'Flood Insurance', 'description' => 'Specialized insurance to protect against flood damage.', 'partner_category_id' => 7]);
        BusinessService::create(['name' => 'Motorcycle Insurance', 'description' => 'Insurance plans tailored for motorcycle riders.', 'partner_category_id' => 7]);

        // Real Estate Agents
        BusinessService::create(['name' => 'Residential Property Sales', 'description' => 'Helping individuals buy and sell residential properties.', 'partner_category_id' => 8]);
        BusinessService::create(['name' => 'Commercial Property Sales', 'description' => 'Assistance with buying and selling commercial properties.', 'partner_category_id' => 8]);
        BusinessService::create(['name' => 'Property Leasing', 'description' => 'Services for leasing residential and commercial properties.', 'partner_category_id' => 8]);
        BusinessService::create(['name' => 'Real Estate Valuation', 'description' => 'Professional valuation services for residential and commercial properties.', 'partner_category_id' => 8]);
        BusinessService::create(['name' => 'Real Estate Investment Consulting', 'description' => 'Consulting services for real estate investors.', 'partner_category_id' => 8]);
        BusinessService::create(['name' => 'Vacation Rentals', 'description' => 'Assistance with listing and managing vacation rental properties.', 'partner_category_id' => 8]);
        BusinessService::create(['name' => 'Foreclosure Sales', 'description' => 'Real estate services for purchasing foreclosed properties.', 'partner_category_id' => 8]);
        BusinessService::create(['name' => 'Land Sales', 'description' => 'Assistance with the sale of vacant land and plots.', 'partner_category_id' => 8]);
        BusinessService::create(['name' => 'Relocation Services', 'description' => 'Comprehensive relocation services for individuals and families moving.', 'partner_category_id' => 8]);
        BusinessService::create(['name' => 'Real Estate Auctions', 'description' => 'Managing real estate auctions for properties.', 'partner_category_id' => 8]);
    }
}
