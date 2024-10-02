<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Region;

class RegionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Costa Rica
        Region::create(['name' => 'San José', 'country_id' => 1]);
        Region::create(['name' => 'Alajuela', 'country_id' => 1]);
        Region::create(['name' => 'Cartago', 'country_id' => 1]);
        Region::create(['name' => 'Heredia', 'country_id' => 1]);
        Region::create(['name' => 'Guanacaste', 'country_id' => 1]);
        Region::create(['name' => 'Puntarenas', 'country_id' => 1]);
        Region::create(['name' => 'Limón', 'country_id' => 1]);

        //Panamá
        // Region::create(['name' => 'Panamá', 'country_id' => 2]);
        // Region::create(['name' => 'Chiriquí', 'country_id' => 2]);
        // Region::create(['name' => 'Veraguas', 'country_id' => 2]);
        // Region::create(['name' => 'Coclé', 'country_id' => 2]);
        // Region::create(['name' => 'Colón', 'country_id' => 2]);
        // Region::create(['name' => 'Darién', 'country_id' => 2]);
        // Region::create(['name' => 'Los Santos', 'country_id' => 2]);
        // Region::create(['name' => 'Herrera', 'country_id' => 2]);
        // Region::create(['name' => 'Bocas del Toro', 'country_id' => 2]);
        // Region::create(['name' => 'Guna Yala', 'country_id' => 2]);

        // //Nicaragua
        // Region::create(['name' => 'Managua', 'country_id' => 3]);
        // Region::create(['name' => 'León', 'country_id' => 3]);
        // Region::create(['name' => 'Granada', 'country_id' => 3]);
        // Region::create(['name' => 'Masaya', 'country_id' => 3]);
        // Region::create(['name' => 'Estelí', 'country_id' => 3]);
        // Region::create(['name' => 'Madriz', 'country_id' => 3]);
        // Region::create(['name' => 'Chinandega', 'country_id' => 3]);
        // Region::create(['name' => 'Matagalpa', 'country_id' => 3]);
        // Region::create(['name' => 'Rivas', 'country_id' => 3]);
        // Region::create(['name' => 'Jinotega', 'country_id' => 3]);

        // //El Salvador
        // Region::create(['name' => 'San Salvador', 'country_id' => 4]);
        // Region::create(['name' => 'Santa Ana', 'country_id' => 4]);
        // Region::create(['name' => 'San Miguel', 'country_id' => 4]);
        // Region::create(['name' => 'La Libertad', 'country_id' => 4]);
        // Region::create(['name' => 'Usulután', 'country_id' => 4]);
        // Region::create(['name' => 'La Paz', 'country_id' => 4]);
        // Region::create(['name' => 'Cuscatlán', 'country_id' => 4]);
        // Region::create(['name' => 'Chalatenango', 'country_id' => 4]);
        // Region::create(['name' => 'Sonsonate', 'country_id' => 4]);
        // Region::create(['name' => 'Morazán', 'country_id' => 4]);

        // //Guatemala
        // Region::create(['name' => 'Guatemala', 'country_id' => 5]);
        // Region::create(['name' => 'Alta Verapaz', 'country_id' => 5]);
        // Region::create(['name' => 'Baja Verapaz', 'country_id' => 5]);
        // Region::create(['name' => 'Chimaltenango', 'country_id' => 5]);
        // Region::create(['name' => 'Chiquimula', 'country_id' => 5]);
        // Region::create(['name' => 'Escuintla', 'country_id' => 5]);
        // Region::create(['name' => 'Huehuetenango', 'country_id' => 5]);
        // Region::create(['name' => 'Izabal', 'country_id' => 5]);
        // Region::create(['name' => 'Jalapa', 'country_id' => 5]);
        // Region::create(['name' => 'Jutiapa', 'country_id' => 5]);
        // Region::create(['name' => 'Petén', 'country_id' => 5]);
        // Region::create(['name' => 'Quetzaltenango', 'country_id' => 5]);
        // Region::create(['name' => 'Quiché', 'country_id' => 5]);
        // Region::create(['name' => 'Retalhuleu', 'country_id' => 5]);
        // Region::create(['name' => 'Sacatepéquez', 'country_id' => 5]);
        // Region::create(['name' => 'San Marcos', 'country_id' => 5]);
        // Region::create(['name' => 'Santa Rosa', 'country_id' => 5]);
        // Region::create(['name' => 'Sololá', 'country_id' => 5]);
        // Region::create(['name' => 'Suchitepéquez', 'country_id' => 5]);
        // Region::create(['name' => 'Totonicapán', 'country_id' => 5]);
        // Region::create(['name' => 'Zacapa', 'country_id' => 5]);
    }
}
