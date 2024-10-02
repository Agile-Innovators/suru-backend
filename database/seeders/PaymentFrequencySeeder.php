<?php

namespace Database\Seeders;

use App\Models\PaymentFrequency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentFrequencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentFrequency::create(['name'=>'Monthly']);
        PaymentFrequency::create(['name'=>'Biweekly']);
        PaymentFrequency::create(['name'=>'Weekly']);
    }
}
