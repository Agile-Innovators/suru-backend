<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Costa Rica (CR)
        // San José
        // San José
        City::create(['name' => 'San José', 'region_id' => 1]);
        City::create(['name' => 'Escazú', 'region_id' => 1]);
        City::create(['name' => 'Desamparados', 'region_id' => 1]);
        City::create(['name' => 'Puriscal', 'region_id' => 1]);
        City::create(['name' => 'Tarrazú', 'region_id' => 1]);
        City::create(['name' => 'Aserrí', 'region_id' => 1]);
        City::create(['name' => 'Mora', 'region_id' => 1]);
        City::create(['name' => 'Goicoechea', 'region_id' => 1]);
        City::create(['name' => 'Santa Ana', 'region_id' => 1]);
        City::create(['name' => 'Alajuelita', 'region_id' => 1]);
        City::create(['name' => 'Vázquez de Coronado', 'region_id' => 1]);
        City::create(['name' => 'Acosta', 'region_id' => 1]);
        City::create(['name' => 'Tibás', 'region_id' => 1]);
        City::create(['name' => 'Moravia', 'region_id' => 1]);
        City::create(['name' => 'Montes de Oca', 'region_id' => 1]);
        City::create(['name' => 'Curridabat', 'region_id' => 1]);
        City::create(['name' => 'Pérez Zeledón', 'region_id' => 1]);
        City::create(['name' => 'León Cortés', 'region_id' => 1]);

        // Alajuela
        City::create(['name' => 'Alajuela', 'region_id' => 2]);
        City::create(['name' => 'San Ramón', 'region_id' => 2]);
        City::create(['name' => 'Grecia', 'region_id' => 2]);
        City::create(['name' => 'San Mateo', 'region_id' => 2]);
        City::create(['name' => 'Atenas', 'region_id' => 2]);
        City::create(['name' => 'Naranjo', 'region_id' => 2]);
        City::create(['name' => 'Palmares', 'region_id' => 2]);
        City::create(['name' => 'Poás', 'region_id' => 2]);
        City::create(['name' => 'Orotina', 'region_id' => 2]);
        City::create(['name' => 'San Carlos', 'region_id' => 2]);
        City::create(['name' => 'Zarcero', 'region_id' => 2]);
        City::create(['name' => 'Sarchí', 'region_id' => 2]);
        City::create(['name' => 'Upala', 'region_id' => 2]);
        City::create(['name' => 'Los Chiles', 'region_id' => 2]);
        City::create(['name' => 'Guatuso', 'region_id' => 2]);
        City::create(['name' => 'Río Cuarto', 'region_id' => 2]);

        // Cartago
        City::create(['name' => 'Cartago', 'region_id' => 3]);
        City::create(['name' => 'Paraíso', 'region_id' => 3]);
        City::create(['name' => 'La Unión', 'region_id' => 3]);
        City::create(['name' => 'Jiménez', 'region_id' => 3]);
        City::create(['name' => 'Turrialba', 'region_id' => 3]);
        City::create(['name' => 'Alvarado', 'region_id' => 3]);
        City::create(['name' => 'Oreamuno', 'region_id' => 3]);
        City::create(['name' => 'El Guarco', 'region_id' => 3]);

        // Heredia
        City::create(['name' => 'Heredia', 'region_id' => 4]);
        City::create(['name' => 'Barva', 'region_id' => 4]);
        City::create(['name' => 'Santo Domingo', 'region_id' => 4]);
        City::create(['name' => 'Santa Bárbara', 'region_id' => 4]);
        City::create(['name' => 'San Rafael', 'region_id' => 4]);
        City::create(['name' => 'San Isidro', 'region_id' => 4]);
        City::create(['name' => 'Belén', 'region_id' => 4]);
        City::create(['name' => 'Flores', 'region_id' => 4]);
        City::create(['name' => 'San Pablo', 'region_id' => 4]);
        City::create(['name' => 'Sarapiquí', 'region_id' => 4]);

        // Guanacaste
        City::create(['name' => 'Liberia', 'region_id' => 5]);
        City::create(['name' => 'Nicoya', 'region_id' => 5]);
        City::create(['name' => 'Santa Cruz', 'region_id' => 5]);
        City::create(['name' => 'Bagaces', 'region_id' => 5]);
        City::create(['name' => 'Carrillo', 'region_id' => 5]);
        City::create(['name' => 'Cañas', 'region_id' => 5]);
        City::create(['name' => 'Abangares', 'region_id' => 5]);
        City::create(['name' => 'Tilarán', 'region_id' => 5]);
        City::create(['name' => 'Nandayure', 'region_id' => 5]);
        City::create(['name' => 'La Cruz', 'region_id' => 5]);
        City::create(['name' => 'Hojancha', 'region_id' => 5]);

        // Puntarenas
        City::create(['name' => 'Puntarenas', 'region_id' => 6]);
        City::create(['name' => 'Esparza', 'region_id' => 6]);
        City::create(['name' => 'Buenos Aires', 'region_id' => 6]);
        City::create(['name' => 'Montes de Oro', 'region_id' => 6]);
        City::create(['name' => 'Osa', 'region_id' => 6]);
        City::create(['name' => 'Quepos', 'region_id' => 6]);
        City::create(['name' => 'Golfito', 'region_id' => 6]);
        City::create(['name' => 'Coto Brus', 'region_id' => 6]);
        City::create(['name' => 'Parrita', 'region_id' => 6]);
        City::create(['name' => 'Corredores', 'region_id' => 6]);
        City::create(['name' => 'Garabito', 'region_id' => 6]);

        // Limón
        City::create(['name' => 'Limón', 'region_id' => 7]);
        City::create(['name' => 'Pococí', 'region_id' => 7]);
        City::create(['name' => 'Siquirres', 'region_id' => 7]);
        City::create(['name' => 'Talamanca', 'region_id' => 7]);
        City::create(['name' => 'Matina', 'region_id' => 7]);
        City::create(['name' => 'Guácimo', 'region_id' => 7]);


        // Panamá (PA)
        // Panamá
        // City::create(['name' => 'Ciudad de Panamá', 'region_id' => 1]);
        // City::create(['name' => 'San Miguelito', 'region_id' => 1]);
        // City::create(['name' => 'Chepo', 'region_id' => 1]);

        // // Chiriquí
        // City::create(['name' => 'David', 'region_id' => 2]);
        // City::create(['name' => 'Boquete', 'region_id' => 2]);
        // City::create(['name' => 'Gualaca', 'region_id' => 2]);

        // // Veraguas
        // City::create(['name' => 'Santiago', 'region_id' => 3]);
        // City::create(['name' => 'Atalaya', 'region_id' => 3]);
        // City::create(['name' => 'La Mesa', 'region_id' => 3]);

        // // Colón
        // City::create(['name' => 'Colón', 'region_id' => 5]);
        // City::create(['name' => 'Portobelo', 'region_id' => 5]);
        // City::create(['name' => 'Santa Isabel', 'region_id' => 5]);

        // // Los Santos
        // City::create(['name' => 'Las Tablas', 'region_id' => 7]);
        // City::create(['name' => 'Guararé', 'region_id' => 7]);
        // City::create(['name' => 'Pedasí', 'region_id' => 7]);

        // // Herrera
        // City::create(['name' => 'Chitré', 'region_id' => 8]);
        // City::create(['name' => 'Parita', 'region_id' => 8]);
        // City::create(['name' => 'Ocú', 'region_id' => 8]);

        // // Bocas del Toro
        // City::create(['name' => 'Bocas del Toro', 'region_id' => 9]);
        // City::create(['name' => 'Changuinola', 'region_id' => 9]);
        // City::create(['name' => 'Almirante', 'region_id' => 9]);

        // Nicaragua (NI)
        // Managua
        // City::create(['name' => 'Tipitapa', 'region_id' => 1]);
        // City::create(['name' => 'Ciudad Sandino', 'region_id' => 1]);
        // City::create(['name' => 'Masaya', 'region_id' => 1]);

        // // León
        // City::create(['name' => 'León', 'region_id' => 2]);
        // City::create(['name' => 'Nagarote', 'region_id' => 2]);
        // City::create(['name' => 'La Paz Centro', 'region_id' => 2]);

        // // Granada
        // City::create(['name' => 'Granada', 'region_id' => 3]);
        // City::create(['name' => 'Nandaime', 'region_id' => 3]);
        // City::create(['name' => 'Diriomo', 'region_id' => 3]);

        // // Masaya
        // City::create(['name' => 'Masaya', 'region_id' => 4]);
        // City::create(['name' => 'Nindirí', 'region_id' => 4]);
        // City::create(['name' => 'Catarina', 'region_id' => 4]);

        // // Estelí
        // City::create(['name' => 'Estelí', 'region_id' => 5]);
        // City::create(['name' => 'Pueblo Nuevo', 'region_id' => 5]);
        // City::create(['name' => 'Condega', 'region_id' => 5]);

        // // Chinandega
        // City::create(['name' => 'Chinandega', 'region_id' => 7]);
        // City::create(['name' => 'El Viejo', 'region_id' => 7]);
        // City::create(['name' => 'Corinto', 'region_id' => 7]);

        // // Matagalpa
        // City::create(['name' => 'Matagalpa', 'region_id' => 8]);
        // City::create(['name' => 'Jinotega', 'region_id' => 8]);
        // City::create(['name' => 'San Ramón', 'region_id' => 8]);
    }
}
