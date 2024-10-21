<?php

use Illuminate\Support\Facades\Artisan;

return function () {
    Artisan::call('appointments:update-status');
    return response()->json([
        'message' => 'Appointment statuses updated successfully.'
    ]);
};
