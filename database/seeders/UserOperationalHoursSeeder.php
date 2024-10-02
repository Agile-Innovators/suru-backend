<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserOperationalHour;

class UserOperationalHoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Operational Hours
        UserOperationalHour::create([
            'user_id' => 1, // Admin User
            'day_of_week' => 'Monday',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'is_closed' => false,
        ]);

        UserOperationalHour::create([
            'user_id' => 1, // Admin User
            'day_of_week' => 'Tuesday',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'is_closed' => false,
        ]);

        UserOperationalHour::create([
            'user_id' => 1, // Admin User
            'day_of_week' => 'Wednesday',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'is_closed' => false,
        ]);

        UserOperationalHour::create([
            'user_id' => 1, // Admin User
            'day_of_week' => 'Thursday',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'is_closed' => false,
        ]);

        UserOperationalHour::create([
            'user_id' => 1, // Admin User
            'day_of_week' => 'Friday',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'is_closed' => false,
        ]);

        UserOperationalHour::create([
            'user_id' => 1, // Admin User
            'day_of_week' => 'Saturday',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'is_closed' => false,
        ]);

        UserOperationalHour::create([
            'user_id' => 1, // Admin User
            'day_of_week' => 'Sunday',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'is_closed' => false,
        ]);

        // User Operational Hours
        UserOperationalHour::create([
            'user_id' => 2, // User
            'day_of_week' => 'Monday',
            'start_time' => '08:00:00',
            'end_time' => '16:00:00',
            'is_closed' => false,
        ]);

        UserOperationalHour::create([
            'user_id' => 2, // User
            'day_of_week' => 'Tuesday',
            'start_time' => '08:00:00',
            'end_time' => '16:00:00',
            'is_closed' => false,
        ]);

        UserOperationalHour::create([
            'user_id' => 2, // User
            'day_of_week' => 'Wednesday',
            'start_time' => '08:00:00',
            'end_time' => '16:00:00',
        ]);

        UserOperationalHour::create([
            'user_id' => 2, // User
            'day_of_week' => 'Thursday',
            'start_time' => '08:00:00',
            'end_time' => '16:00:00',
            'is_closed' => false,
        ]);

        UserOperationalHour::create([
            'user_id' => 2, // User
            'day_of_week' => 'Friday',
            'start_time' => '08:00:00',
            'end_time' => '16:00:00',
            'is_closed' => false,
        ]);

        UserOperationalHour::create([
            'user_id' => 2, // User
            'day_of_week' => 'Saturday',
            'start_time' => '08:00:00',
            'end_time' => '16:00:00',
            'is_closed' => false,
        ]);

        UserOperationalHour::create([
            'user_id' => 2, // User
            'day_of_week' => 'Sunday',
            'start_time' => '08:00:00',
            'end_time' => '16:00:00',
            'is_closed' => false,
        ]);

        // Partner Operational Hours
        UserOperationalHour::create([
            'user_id' => 3, // Partner
            'day_of_week' => 'Monday',
            'start_time' => '10:00:00',
            'end_time' => '18:00:00',
            'is_closed' => false,
        ]);

        UserOperationalHour::create([
            'user_id' => 3, // Partner
            'day_of_week' => 'Tuesday',
            'start_time' => '10:00:00',
            'end_time' => '18:00:00',
            'is_closed' => false,
        ]);

        UserOperationalHour::create([
            'user_id' => 3, // Partner
            'day_of_week' => 'Wednesday',
            'start_time' => '10:00:00',
            'end_time' => '18:00:00',
            'is_closed' => false,
        ]);

        UserOperationalHour::create([
            'user_id' => 3, // Partner
            'day_of_week' => 'Thursday',
            'start_time' => '10:00:00',
            'end_time' => '18:00:00',
            'is_closed' => false,
        ]);

        UserOperationalHour::create([
            'user_id' => 3, // Partner
            'day_of_week' => 'Friday',
            'start_time' => '10:00:00',
            'end_time' => '18:00:00',
            'is_closed' => false,
        ]);

        UserOperationalHour::create([
            'user_id' => 3, // Partner
            'day_of_week' => 'Saturday',
            'start_time' => '10:00:00',
            'end_time' => '18:00:00',
            'is_closed' => false,
        ]);

        UserOperationalHour::create([
            'user_id' => 3, // Partner
            'day_of_week' => 'Sunday',
            'start_time' => '10:00:00',
            'end_time' => '18:00:00',
            'is_closed' => false,
        ]);

    }
}
