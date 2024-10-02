<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Currency::create(['code'=>'USD', 'name'=>'Dólar estadounidense']);
        Currency::create(['code'=>'CRC', 'name'=>'Colón costarricense']);
        Currency::create(['code'=>'EUR', 'name'=>'Euro']);
    }
}
