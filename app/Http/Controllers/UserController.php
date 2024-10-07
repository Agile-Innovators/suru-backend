<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserOperationalHour;
use App\Models\UserProfile;
use App\Models\PartnerProfile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

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
        if ($user->user_type_id == 2) { // Normal user
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

        // Obtaining operational hours except for the id and user_id
        $operationalHours = UserOperationalHour::select(
            'day_of_week',
            'start_time',
            'end_time',
            'is_closed'
        )
            ->where('user_id', $id)
            ->get();

        $user->operational_hours = $operationalHours;

        return response()->json(
            $user,
            200
        );
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
            'user_type_id' => 'required|integer|in:2,3', // 2 = Regular user, 3 = Partner

            // Conditional validations
            'lastname1' => $request->user_type_id == 2 ? 'required|string' : 'nullable',
            'lastname2' => $request->user_type_id == 2 ? 'required|string' : 'nullable',
            'name' => $request->user_type_id == 3 ? 'required|string' : 'nullable',
            'phone_number' => $request->user_type_id == 3 ? 'required|string' : 'nullable',
            'description' => $request->user_type_id == 3 ? 'required|string' : 'nullable',
            'website_url' => $request->user_type_id == 3 ? 'required|string' : 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors occurred',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'user_type_id' => $request->user_type_id,
                'name' => $request->user_type_id == 3 ? $request->name : null,
                'phone_number' => $request->user_type_id == 3 ? $request->phone_number : null,
            ]);

            // Create profiles
            if ($request->user_type_id == 2) {
                UserProfile::create(['user_id' => $user->id]);
            } else if ($request->user_type_id == 3) {
                PartnerProfile::create([
                    'user_id' => $user->id,
                    'description' => $request->description,
                    'website_url' => $request->website_url,
                    'partner_category_id' => $request->partner_category_id,
                ]);

                // Upload an image if it exists
                if ($request->hasFile('image')) {
                    $uploadedImage = Cloudinary::upload($request->image->getRealPath(), ['folder' => 'users']);

                    if ($uploadedImage) {
                        $publicId = $uploadedImage->getPublicId();
                        $user->update([
                            'image_public_id' => $publicId,
                            'image_url' => cloudinary()->getUrl($publicId),
                        ]);
                    }
                }
            }

            // Create default operational hours
            $this->createUserOperationalHours($user->id);

            $user->load('userType');
            $token = $user->createToken('Personal Access Token')->plainTextToken;

            $userData = [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
                'email' => $user->email,
                'image_url' => $user->image_url ?? "https://res.cloudinary.com/dvwtm566p/image/upload/v1728158504/users/dc8aagfamyqwaspllhz8.jpg",
                'user_type' => $user->userType->name,
            ];

            // Welcome email
            // Resend::emails()->send([
            //     'from' => env('MAIL_FROM_NAME') . ' <' . env('MAIL_FROM_ADDRESS') . '>',
            //     'to' => $user->email,
            //     'subject' => 'Welcome To Suru Test',
            //     'html' => (new Welcome($user->username))->render(),
            // ]);

            return response()->json([
                'message' => 'User created successfully',
                'user' => $userData,
                'token' => $token
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function createUserOperationalHours($userId)
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

        $user = User::with('userType')->where('email', $request->email)->first();

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
                'username' => $user->username,
                'user_type' => $user->userType->name,
                'image_url' => $user->image_url,
            ],
            'token' => $token
        ], 200);
    }
}
