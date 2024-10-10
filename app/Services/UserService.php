<?php

namespace App\Services;

use App\Models\UserOperationalHour;
use Illuminate\Http\Request;

class UserService
{
    public function createUserOperationalHours($userId)
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        foreach ($days as $day) {
            UserOperationalHour::create([
                'user_id' => $userId,
                'day_of_week' => $day,
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
            ]);
        }
    }
}
