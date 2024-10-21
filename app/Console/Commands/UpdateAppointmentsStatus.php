<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use Carbon\Carbon;

class UpdateAppointmentsStatus extends Command
{
    protected $signature = 'appointments:update-status';
    protected $description = 'Update the status of appointments based on the current time';

    /**
     * Ejecuta el comando.
     */
    public function handle()
    {
        // Obtain the current date and time
        $now = Carbon::now();

        // Update appointments that are in "Pending" status and have passed to "Rejected"
        Appointment::where('status', 'Pending')
            ->where(function ($query) use ($now) {
                $query->whereDate('date', '<', $now->toDateString())
                    ->orWhere(function ($subQuery) use ($now) {
                        $subQuery->whereDate('date', $now->toDateString())
                            ->whereTime('end_time', '<', $now->toTimeString());
                    });
            })
            ->update(['status' => 'Rejected']);

        // Update appointments that are in "Scheduled" status and have passed to "Completed"
        Appointment::where('status', 'Scheduled')
            ->where(function ($query) use ($now) {
                $query->whereDate('date', '<', $now->toDateString())
                    ->orWhere(function ($subQuery) use ($now) {
                        $subQuery->whereDate('date', $now->toDateString())
                            ->whereTime('end_time', '<', $now->toTimeString());
                    });
            })
            ->update(['status' => 'Completed']);

        // Show a message indicating that the statuses have been updated successfully
        $this->info('Appointment statuses updated successfully.');
    }
}
