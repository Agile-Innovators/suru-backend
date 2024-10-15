<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Property;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $appointments = Appointment::all();

        if ($appointments->isEmpty()) {
            return response()->json(['message' => 'No appointments found'], 404);
        }

        return response()->json($appointments);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'owner_id' => 'required|exists:users,id',
            'user_id' => 'required|exists:users,id',
            'property_id' => 'required|exists:properties,id',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date',
            'user_message' => 'string|nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $appointment = Appointment::create($request->all());
        $appointment->status = 'Pending';
        $appointment->save();

        return response()->json($appointment, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $appointment_id)
    {
        $appointment = Appointment::find($appointment_id);

        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        return response()->json($appointment);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $appointment_id)
    {
        $appointment = Appointment::find($appointment_id);

        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'owner_id' => 'exists:users,id',
            'user_id' => 'exists:users,id',
            'property_id' => 'exists:properties,id',
            'start_datetime' => 'date',
            'end_datetime' => 'date',
            'user_message' => 'string|nullable',
            'status' => 'string|in:Pending,Scheduled,Completed,Cancelled,Rejected',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $appointment->update($request->all());

        return response()->json([
            'message' => 'Appointment updated successfully',
            'data' => $appointment,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $appointment_id)
    {
        $appointment = Appointment::find($appointment_id);

        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        $appointment->delete();

        return response()->json(['message' => 'Appointment deleted successfully']);
    }

    /**
     * Get all appointments related to a user.
     */
    public function userAppointments(string $user_id)
    {
        $appointments = Appointment::where('user_id', $user_id)
            ->orWhere('owner_id', $user_id)
            ->get();

        if ($appointments->isEmpty()) {
            return response()->json(['message' => 'No appointments found'], 404);
        }

        return response()->json($appointments);
    }

    /**
     * Get all appointments related to a property.
     */
    public function propertyAppointments(string $property_id)
    {
        $property = Property::find($property_id);

        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        $appointments = Appointment::where('property_id', $property_id)->get();

        if ($appointments->isEmpty()) {
            return response()->json(['message' => 'No appointments found'], 404);
        }

        return response()->json($appointments);
    }

    /**
     * Get all user's appointments of a specific status.
     */
    public function getUserAppointmentsByStatus(string $user_id, string $status)
    {
        $appointments = Appointment::where(function ($query) use ($user_id) {
            $query->where('user_id', $user_id)
                ->orWhere('owner_id', $user_id);
        })->where('status', $status)
            ->orderBy('start_datetime', 'asc')
            ->get();

        if ($appointments->isEmpty()) {
            return response()->json(['message' => 'No appointments found'], 404);
        }

        return response()->json($appointments);
    }

    /**
     * Cancel a specific appointment.
     */
    public function cancelAppointment(string $appointment_id)
    {
        $appointment = Appointment::find($appointment_id);

        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        // Check current status before cancelling
        if ($appointment->status === 'Cancelled') {
            return response()->json(['message' => 'Appointment is already cancelled'], 400);
        }

        $appointment->status = 'Cancelled';
        $appointment->save();

        return response()->json(['message' => 'Appointment cancelled successfully']);
    }

    /**
     * Accept a specific appointment.
     */
    public function acceptAppointment(string $appointment_id, int $user_id)
    {
        $appointment = Appointment::find($appointment_id);

        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        // Check current status before accepting
        if ($appointment->status !== 'Pending') {
            return response()->json(['message' => 'Only pending appointments can be accepted'], 400);
        }

        //Verify if there is a conflicting appointment in the same time range and property where the user is involved
        $conflictingAppointment = Appointment::where(function ($query) use ($appointment, $user_id) {
            $query->where('property_id', $appointment->property_id)
                ->where('status', 'Scheduled')
                ->where(function ($query) use ($appointment) {
                    $query->whereBetween('start_datetime', [$appointment->start_datetime, $appointment->end_datetime])
                        ->orWhereBetween('end_datetime', [$appointment->start_datetime, $appointment->end_datetime])
                        ->orWhere(function ($query) use ($appointment) {
                            $query->where('start_datetime', '<=', $appointment->start_datetime)
                                ->where('end_datetime', '>=', $appointment->end_datetime);
                        });
                })
                ->where(function ($query) use ($user_id) {
                    $query->where('owner_id', $user_id)
                        ->orWhere('user_id', $user_id);
                });
        })
            ->first();

        // If there is a conflicting appointment, return an error
        if ($conflictingAppointment) {
            return response()->json(['message' => 'There is already a scheduled appointment in this time range where you are involved.'], 409);
        }

        // Accept the appointment
        $appointment->status = 'Scheduled';
        $appointment->save();

        // Cancelled all pending appointments in the same time range and property
        Appointment::where('property_id', $appointment->property_id)
            ->where('status', 'Pending')
            ->where(function ($query) use ($appointment) {
                $query->whereBetween('start_datetime', [$appointment->start_datetime, $appointment->end_datetime])
                    ->orWhereBetween('end_datetime', [$appointment->start_datetime, $appointment->end_datetime])
                    ->orWhere(function ($query) use ($appointment) {
                        $query->where('start_datetime', '<=', $appointment->start_datetime)
                            ->where('end_datetime', '>=', $appointment->end_datetime);
                    });
            })
            ->where(function ($query) use ($user_id) {
                $query->where('owner_id', $user_id)
                    ->orWhere('user_id', $user_id);
            })
            ->update(['status' => 'Rejected']);

        return response()->json(['message' => 'Appointment accepted successfully']);
    }
}
