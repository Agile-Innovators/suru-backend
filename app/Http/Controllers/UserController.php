<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserLocation;
use App\Models\Appointment;

use App\Models\UserOperationalHour;
use App\Models\UserProfile;
use App\Models\PartnerProfile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Models\PasswordResetToken;
use Illuminate\Support\Str;
use Carbon\Carbon;

// Email
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index() {}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::select(
            'users.id',
            'users.username',
            'users.email',
            'users.name',
            'users.phone_number',
            'users.image_url',
            'users.image_public_id',
            'users.user_type_id',
            'user_types.name as user_type'
        )
            ->leftJoin('user_types', 'users.user_type_id', '=', 'user_types.id')
            ->where('users.id', $id)
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        // Obtainig profile information
        if (($user->user_type_id == 1) || $user->user_type_id == 2) { // Admin or Normal user
            $userProfile = UserProfile::select(
                'lastname1',
                'lastname2'
            )
                ->where('user_id', $id)
                ->first();

            $user->profile = $userProfile;
        } elseif ($user->user_type_id == 3) { // Partner
            $partnerProfile = PartnerProfile::select(
                'description',
                'website_url',
                'partner_category_id',
                'partner_categories.name as partner_category'
            )
                ->leftJoin('partner_categories', 'partner_profiles.partner_category_id', '=', 'partner_categories.id')
                ->where('partner_profiles.user_id', $id)
                ->first();

            $user->profile = $partnerProfile;
        }

        // Obtaining user location
        $userLocation = UserLocation::select(
            'cities.name as city',
            'cities.id as city_id',
            'user_locations.address'
        )
            ->leftJoin('cities', 'user_locations.city_id', '=', 'cities.id')
            ->where('user_locations.user_id', $id)
            ->first();

        $user->location = $userLocation;

        return response()->json(
            $user,
            200
        );
    }

    /**
     * Show the operational hours for a user
     */
    // public function showOperationalHours(string $id_user)
    // {
    //     $operationalHours = UserOperationalHour::select(
    //         'day_of_week',
    //         'start_time',
    //         'end_time',
    //         'is_closed'
    //     )
    //         ->where('user_id', $id_user)
    //         ->get()

    //     return response()->json([
    //         'message' => 'Operational hours',
    //         'operational_hours' => $operationalHours,
    //     ], 200);
    // }

    public function showOperationalHours(string $id_user)
    {
        $operationalHours = UserOperationalHour::select(
            'day_of_week',
            'start_time',
            'end_time',
            'is_closed'
        )
            ->where('user_id', $id_user)
            ->get();

        return response()->json([
            'message' => 'Operational hours',
            'operational_hours' => $operationalHours,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }
        Log::info('Request data!!!:', $request->all());

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'name' => 'required|string',
            'phone_number' => 'required|string',
            'city_id' => 'nullable|integer',
            'address' => 'nullable|string',

            // Conditional validations for regular users
            'lastname1' => 'nullable',
            'lastname2' => 'nullable',

            // Conditional validations for partners
            'name' => $request->user_type_id == 3 ? 'required|string' : 'nullable',
            'phone_number' => $request->user_type_id == 3 ? 'required|string|unique:users,phone_number' : 'nullable',
            'description' => $request->user_type_id == 3 ? 'required|string' : 'nullable',
            'website_url' => $request->user_type_id == 3 ? 'nullable|string' : 'nullable',
            'facebook_url' => $request->user_type_id == 3 ? 'nullable' : 'nullable',
            'instagram_url' => $request->user_type_id == 3 ? 'nullable' : 'nullable',
            'tiktok_url' => $request->user_type_id == 3 ? 'nullable' : 'nullable',
            'currency_id' => $request->user_type_id == 3 ? 'required|integer' : 'nullable',
            'partner_category_id' => $request->user_type_id == 3 ? 'required|integer' : 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors occurred',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $fieldsToUpdate = [];
            foreach ($request->all() as $key => $value) {
                if ($key != 'password' && $user->$key != $value) {
                    $fieldsToUpdate[$key] = $value;
                }
            }

            if (!empty($fieldsToUpdate)) {
                $user->update($fieldsToUpdate);
            }

            if ($user->user_type_id == 3) {
                $partnerProfile = PartnerProfile::where('user_id', $id)->first();
                if ($partnerProfile) {
                    $fieldsToUpdatePartner = [];
                    foreach ($request->all() as $key => $value) {
                        if ($partnerProfile->$key != $value) {
                            $fieldsToUpdatePartner[$key] = $value;
                        }
                    }
                    if (!empty($fieldsToUpdatePartner)) {
                        $partnerProfile->update($fieldsToUpdatePartner);
                    }
                }
            } else {
                $userProfile = UserProfile::where('user_id', $id)->first();
                if ($userProfile) {
                    $fieldsToUpdateProfile = [];
                    foreach ($request->all() as $key => $value) {
                        if ($userProfile->$key != $value) {
                            $fieldsToUpdateProfile[$key] = $value;
                        }
                    }
                    if (!empty($fieldsToUpdateProfile)) {
                        $userProfile->update($fieldsToUpdateProfile);
                    }
                }
            }

            // Updating user location
            if ($request->city_id !== null) {
                UserLocation::updateOrCreate(
                    ['user_id' => $user->id],
                    ['city_id' => $request->city_id, 'address' => $request->address]
                );
            }

            // Updating profile picture and deleting old one if it's not the default one
            if ($request->hasFile('image')) {
                if (($user->image_public_id != 'users/dc8aagfamyqwaspllhz8') && ($user->image_url != 'https://res.cloudinary.com/dvwtm566p/image/upload/v1728158504/users/dc8aagfamyqwaspllhz8.jpg')) {
                    Cloudinary::destroy($user->image_public_id);
                }

                $uploadedImage = Cloudinary::upload($request->image->getRealPath(), [
                    'folder' => 'users'
                ]);

                if ($uploadedImage) {
                    $publicId = $uploadedImage->getPublicId();
                    $url = cloudinary()->getUrl($publicId);

                    $user->update(['image_public_id' => $publicId]);
                    $user->update(['image_url' => $url]);
                }
            }

            $userType = $user->userType->name;
            $user->save();

            return response()->json([
                'message' => 'User updated successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username,
                    'user_type' => $userType,
                    'image_url' => $user->image_url,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    public function updatePassword(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|string|same:new_password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors occurred',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!password_verify($request->old_password, $user->password)) {
            return response()->json([
                'message' => 'Invalid old password',
            ], 400);
        }

        try {
            $user->update([
                'password' => bcrypt($request->new_password),
            ]);

            return response()->json([
                'message' => 'Password updated successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating password',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateOperationalHours(Request $request, string $userId)
    {
        $validator = Validator::make($request->all(), [
            'operational_hours' => 'required|array',
            'operational_hours.*.day_of_week' => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'operational_hours.*.start_time' => 'required|date_format:H:i',
            'operational_hours.*.end_time' => 'required|date_format:H:i|after:operational_hours.*.start_time',
            'operational_hours.*.is_closed' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors occurred',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            foreach ($request->operational_hours as $operationalHour) {
                $existingOperationalHour = UserOperationalHour::where('user_id', $userId)
                    ->where('day_of_week', $operationalHour['day_of_week'])
                    ->first();

                if ($existingOperationalHour) {
                    $existingOperationalHour->update([
                        'start_time' => $operationalHour['start_time'],
                        'end_time' => $operationalHour['end_time'],
                        'is_closed' => $operationalHour['is_closed'],
                    ]);
                } else {
                    UserOperationalHour::create([
                        'user_id' => $userId,
                        'day_of_week' => $operationalHour['day_of_week'],
                        'start_time' => $operationalHour['start_time'],
                        'end_time' => $operationalHour['end_time'],
                        'is_closed' => $operationalHour['is_closed'],
                    ]);
                }
            }

            return response()->json([
                'message' => 'Operational hours updated successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating operational hours',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Método encargado de mostrar las horar operativas disponibles de un usuario (aquellas en las que no tenga appointments en status "Scheduled")
    public function showAvailableOperationalHours(Request $request, string $userId)
{
    // Obtener las horas operacionales del usuario
    $operationalHours = UserOperationalHour::where('user_id', $userId)
        ->get();

    $availableHours = [];

    // Iterar sobre cada hora operacional para obtener las horas disponibles
    foreach ($operationalHours as $operationalHour) {
        // Filtrar las citas solo para el día de la semana y el usuario
        $appointments = Appointment::where('user_id', $userId)
            ->where('status', 'Scheduled')
            ->where('day_of_week', $operationalHour->day_of_week)
            ->get();

        // Llamar a la función para obtener los intervalos de tiempo disponibles
        $dayAvailableSlots = $this->getAvailableTimeSlots(
            $operationalHour->start_time,
            $operationalHour->end_time,
            $appointments
        );

        // Si existen horas disponibles, agregarlas a la respuesta
        if (!empty($dayAvailableSlots)) {
            $availableHours[] = [
                'day_of_week' => $operationalHour->day_of_week,
                'available_slots' => $dayAvailableSlots
            ];
        }
    }

    return response()->json([
        'message' => 'Operational hours available',
        'available_hours' => $availableHours
    ], 200);
}

private function getAvailableTimeSlots($start_time, $end_time, $appointments)
{
    $availableSlots = [];

    // Convertir las horas de inicio y fin en objetos DateTime para compararlas
    $start = \Carbon\Carbon::parse($start_time);
    $end = \Carbon\Carbon::parse($end_time);

    // Generar una lista de todas las horas en el rango operativo
    $currentTime = $start;
    while ($currentTime->lt($end)) {
        $slotStart = $currentTime->format('H:i');
        $slotEnd = $currentTime->addMinutes(30)->format('H:i'); // Asumimos intervalos de 30 minutos

        // Verificar si alguna cita ocupa este intervalo de tiempo
        $isSlotAvailable = true;
        foreach ($appointments as $appointment) {
            $appointmentStart = \Carbon\Carbon::parse($appointment->start_time);
            $appointmentEnd = \Carbon\Carbon::parse($appointment->end_time);

            // Si el intervalo de la cita se solapa con el intervalo de la hora disponible, marcar como ocupado
            if ($this->isTimeSlotOccupied($slotStart, $slotEnd, $appointmentStart, $appointmentEnd)) {
                $isSlotAvailable = false;
                break;
            }
        }

        // Si el intervalo está libre, agregarlo a la lista de horas disponibles
        if ($isSlotAvailable) {
            $availableSlots[] = ['start_time' => $slotStart, 'end_time' => $slotEnd];
        }

        // Avanzar al siguiente intervalo de 30 minutos
        $currentTime = $currentTime->addMinutes(30);
    }

    return $availableSlots;
}

private function isTimeSlotOccupied($slotStart, $slotEnd, $appointmentStart, $appointmentEnd)
{
    // Verificar si hay solapamiento entre los intervalos de tiempo
    return !(
        \Carbon\Carbon::parse($slotEnd)->lte($appointmentStart) ||
        \Carbon\Carbon::parse($slotStart)->gte($appointmentEnd)
    );
}


    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|string|same:new_password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors occurred',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        try {
            $user->update([
                'password' => bcrypt($request->new_password),
            ]);

            return response()->json([
                'message' => 'Password updated successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating password',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function sendPasswordResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        // Check if the user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Generate a unique token
        $token = Str::random(60);

        // Store the token in the database
        PasswordResetToken::updateOrCreate(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => now()]
        );

        // Build the reset password URL
        #$url = 'http://localhost:5173/reset-password?token=' . $token . '&email=' . urlencode($request->email);
        $url = 'https://suru-development-seven.vercel.app/reset-password?token=' . $token . '&email=' . urlencode($request->email);

        // Send the email with the reset link
        Mail::to($request->email)->send(new PasswordResetMail($url));

        return response()->json(['message' => 'Password reset link sent!'], 200);
    }

    public function resetForgottenPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|string|min:8',
        ]);

        $passwordReset = PasswordResetToken::where('email', $request->email)->first();

        if (!$passwordReset || $passwordReset->token !== $request->token) {
            return response()->json(['message' => 'Invalid token'], 400);
        }

        // Found the user associated with the token
        $user = User::where('email', $request->email)->first();
        $user->password = bcrypt($request->password);
        $user->save();

        // Delete the token from the database
        $passwordReset->delete();

        return response()->json(['message' => 'Password has been reset successfully!'], 200);
    }
}
