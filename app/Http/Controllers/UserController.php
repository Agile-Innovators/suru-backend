<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserOperationalHour;
use App\Models\UserProfile;
use App\Models\PartnerProfile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

//email provider
use Resend\Laravel\Facades\Resend;
//email template
use App\Mail\Welcome;

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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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

            //Validations for regular users
            'lastname1' => $user->user_type_id == 2 ? 'required|string' : 'nullable',
            'lastname2' => $user->user_type_id == 2 ? 'required|string' : 'nullable',

            //Validations for partners
            'description' => $user->user_type_id == 3 ? 'required|string' : 'nullable',
            'website_url' => $user->user_type_id == 3 ? 'required|string' : 'nullable'
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

            // Update profiles
            switch ($user->user_type_id) {
                case 2: // Regular user
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
                    break;

                case 3: // Partner
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
                    break;
            }

            // Updating profile picture and deleting old one if it's not the default one
            if ($request->hasFile('profile_picture')) {
                $image_to_remove = 'public/images/users/' . $user->profile_picture;

                if ($user->profile_picture != 'user_default.jpg' && Storage::exists($image_to_remove)) {
                    Storage::delete($image_to_remove);
                }

                $image = $request->file('profile_picture');
                $filename = 'user_' . $user->id . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/images/users', $filename);
                $user->update(['profile_picture' => $filename]);
            }

            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user
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
            ], 401);
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

    //Authentication Module

    /**
     * Register a user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors occurred',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = new User([
                'username' => $request->username,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'user_type_id' => $request->user_type_id,
                'profile_picture' => 'default.jpg',
            ]);

            $user->save();
            $user_id = $user->id;

            switch ($request->user_type_id) {
                case 2:
                    $userProfile = new UserProfile([
                        'user_id' => $user_id,
                    ]);
                    $userProfile->save();
                    break;

                case 3:
                    $partnerProfile = new PartnerProfile([
                        'user_id' => $user_id,
                        'description' => $request->description,
                        'website_url' => $request->website_url,
                        'partner_category_id' => $request->partner_category_id,
                    ]);

                    $partnerProfile->save();
                    break;
            }

            // Creating user operational hours per default
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            foreach ($days as $day) {
                $userOperationalHour = new UserOperationalHour([
                    'user_id' => $user_id,
                    'day_of_week' => $day,
                    'start_time' => '09:00:00',
                    'end_time' => '17:00:00',
                ]);
                $userOperationalHour->save();
            }

            $token = $user->createToken('Personal Access Token')->plainTextToken;

            // Resend::emails()->send([
            //     'from' => env('MAIL_FROM_NAME'). ' <' . env('MAIL_FROM_ADDRESS') . '>',
            //     'to' => $user->email,
            //     'subject' => 'Welcome To Suru Test',
            //     'html' => (new Welcome($user->username))->render(),
            // ]);

            return response()->json([
                'message' => 'User created successfully',
                'user' => $user,
                'token' => $token
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors occurred',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $user->createToken('Personal Access Token')->plainTextToken;

        return response()->json([
            'message' => 'User logged in successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token
        ], 200);
    }
    
}
