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
            'scheduled_at' => 'required|date',
            'message' => 'string|nullable',
            'status' => 'string|in:Scheduled,Completed,Cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $appointment = Appointment::create($request->all());

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
            'scheduled_at' => 'date',
            'message' => 'string|nullable',
            'status' => 'string|in:Scheduled,Completed,Cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $appointment->update($request->all());

        return response()->json([
            'message' => 'Appointment updated succesfully',
            'appointment' => $appointment,
        ], 201);
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
    public function userAppointments(string $user_id){
        // Appointments in which the user_id or owner_id matches the user_id passed as parameter
        $appointments = Appointment::where('user_id', $user_id)->orWhere('owner_id', $user_id)->get();

        if($appointments->isEmpty()){
            return response()->json(['message' => 'No appointments found'], 404);
        }

        return response()->json($appointments);
    }

    /**
     * Get all appointments related to a property.
     */
    public function propertyAppointments(string $property_id){
        $appointments = Appointment::where('property_id', $property_id)->get();

        $property = Property::find($property_id);

        if(!$property){
            return response()->json(['message' => 'Property not found'], 404);
        }

        if($appointments->isEmpty()){
            return response()->json(['message' => 'No appointments found'], 404);
        }

        return response()->json($appointments);
    }

    
    /**
     * Get all user's appointments of an specific status.
     */
    public function getUserAppointmentsByStatus(string $user_id, string $status){
        $appointments = Appointment::where(function($query) use ($user_id) {
            $query->where('user_id', $user_id)
              ->orWhere('owner_id', $user_id);
        })->where('status', $status)->get();

        if($appointments->isEmpty()){
            return response()->json(['message' => 'No appointments found'], 404);
        }

        return response()->json($appointments);
    }

}
