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
        // Obtener todas las citas y cargar las relaciones necesarias
        $appointments = Appointment::with('property.city')->get();

        if ($appointments->isEmpty()) {
            return response()->json(['message' => 'No appointments found'], 404);
        }

        // Modificar la respuesta para incluir city_id de la propiedad
        $appointments = $appointments->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'owner_id' => $appointment->owner_id,
                'user_id' => $appointment->user_id,
                'property_id' => $appointment->property_id,
                'date' => $appointment->date,
                'start_time' => $appointment->start_time,
                'end_time' => $appointment->end_time,
                'user_message' => $appointment->user_message,
                'status' => $appointment->status,
                'property' => [
                    'id' => $appointment->property->id,
                    'title' => $appointment->property->title,
                    'city_id' => $appointment->property->city_id,
                    'city_name' => $appointment->property->city->name
                ]
            ];
        });

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
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'user_message' => 'string|nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Verify if there is a conflicting appointment in the same time range and property where the user is involved
        $conflictingAppointment = Appointment::where('property_id', $request->property_id)
            ->where('status', 'Scheduled')
            ->where(function ($query) use ($request) {
                $query->where('date', $request->date)
                    ->where(function ($query) use ($request) {
                        $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                            ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                            ->orWhere(function ($query) use ($request) {
                                $query->where('start_time', '<=', $request->start_time)
                                    ->where('end_time', '>=', $request->end_time);
                            });
                    });
            })
            ->where(function ($query) use ($request) {
                $query->where('owner_id', $request->owner_id)
                    ->orWhere('user_id', $request->user_id);
            })
            ->first();

        // If there is a conflicting appointment, return an error
        if ($conflictingAppointment) {
            return response()->json(['message' => 'There is already a scheduled appointment in this time range where you are involved.'], 409);
        }

        $appointment = Appointment::create($request->all());
        $appointment->status = 'Pending';

        if ($request->user_message == null) {
            $appointment->user_message = 'No extra comments were given';
        }

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
            'date' => 'date',
            'start_time' => 'date_format:H:i',
            'end_time' => 'date_format:H:i|after:start_time',
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
            ->with('property.city')
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

        $appointments = Appointment::where('property_id', $property_id)->with('property.city')->get();

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
            ->orderBy('date')
            ->orderBy('start_time')
            ->with('property.city')
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

        if ($appointment->status !== 'Scheduled') {
            return response()->json(['message' => 'Only scheduled appointments can be cancelled'], 400);
        }

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

        if ($appointment->status !== 'Pending') {
            return response()->json(['message' => 'Only pending appointments can be accepted'], 400);
        }

        if ($appointment->owner_id !== $user_id) {
            return response()->json(['message' => 'Only the owner can accept this appointment'], 403);
        }

        // Verify if there is a conflicting appointment in the same time range and property where the user is involved
        $conflictingAppointment = Appointment::where('property_id', $appointment->property_id)
            ->where('status', 'Scheduled')
            ->where(function ($query) use ($appointment) {
                $query->where('date', $appointment->date)
                    ->where(function ($query) use ($appointment) {
                        $query->whereBetween('start_time', [$appointment->start_time, $appointment->end_time])
                            ->orWhereBetween('end_time', [$appointment->start_time, $appointment->end_time])
                            ->orWhere(function ($query) use ($appointment) {
                                $query->where('start_time', '<=', $appointment->start_time)
                                    ->where('end_time', '>=', $appointment->end_time);
                            });
                    });
            })
            ->where(function ($query) use ($user_id) {
                $query->where('owner_id', $user_id)
                    ->orWhere('user_id', $user_id);
            })
            ->first();

        if ($conflictingAppointment) {
            return response()->json(['message' => 'There is already a scheduled appointment in this time range where you are involved.'], 409);
        }

        $appointment->status = 'Scheduled';
        $appointment->save();

        return response()->json(['message' => 'Appointment accepted successfully']);
    }

    /**
     * Reject a specific appointment.
     */
    public function rejectAppointment(string $appointment_id, int $user_id)
    {
        $appointment = Appointment::find($appointment_id);

        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        if ($appointment->owner_id !== $user_id) {
            return response()->json(['message' => 'Only the owner can reject this appointment'], 403);
        }

        if ($appointment->status !== 'Pending') {
            return response()->json(['message' => 'Only pending appointments can be rejected'], 400);
        }

        if ($appointment->status === 'Rejected') {
            return response()->json(['message' => 'Appointment is already rejected'], 400);
        }

        $appointment->status = 'Rejected';
        $appointment->save();

        return response()->json(['message' => 'Appointment rejected successfully']);
    }

    public function filterAppointments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city_id' => 'required|integer',
            'date' => 'nullable|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $city_id = $request->city_id;
        $date = $request->date;
        $start_time = $request->start_time;
        $end_time = $request->end_time;

        $appointments = Appointment::query()
            // Filter by city if present, city_id = 0 means all cities
            ->when($city_id != 0, function ($query) use ($city_id) {
                $query->whereHas('property', function ($query) use ($city_id) {
                    $query->where('city_id', $city_id);
                });
            })
            // Filter by date if present
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            // Filter by time range if both start_time and end_time are present
            ->when($start_time && $end_time, function ($query) use ($start_time, $end_time) {
                $query->where(function ($query) use ($start_time, $end_time) {
                    // Appointments may start and end within the range
                    $query->where(function ($query) use ($start_time, $end_time) {
                        $query->whereTime('start_time', '>=', $start_time)
                            ->whereTime('end_time', '<=', $end_time);
                    })->orWhere(function ($query) use ($start_time, $end_time) {
                        // Appointments may start before the range and end within the range
                        $query->whereTime('start_time', '<', $end_time)
                            ->whereTime('end_time', '>', $start_time);
                    });
                });
            })
            ->get();

        if ($appointments->isEmpty()) {
            return response()->json(['message' => 'No appointments found'], 404);
        }

        return response()->json($appointments);
    }
}
