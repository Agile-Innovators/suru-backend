<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Appointment;
use DateTime;

class AppointmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Appointment::create([
            'user_id' => 1,
            'owner_id' => 2,
            'property_id' => 1,
            'scheduled_at' => now(),
            'message' => "A simple message",
            'status' => 'Scheduled',
        ]);
    }
}
