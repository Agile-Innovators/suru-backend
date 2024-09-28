<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserType;
use App\Models\UserProfile;
use App\Models\PartnerProfile;
use Illuminate\Support\Facades\Validator;
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

        $validator = Validator::make($request->all(), [
            'username' => 'string|unique:users,username,' . $id,
            'email' => 'email|unique:users,email,' . $id,
            'description' => $user->user_type_id == 3 ? 'required|string' : 'nullable',
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
                'profile_picture' => 'users/default.jpg',
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

    /**
     * Login a user
     */
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

        if (!$user || !password_verify($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $user->createToken('Personal Access Token')->plainTextToken;

        return response()->json([
            'message' => 'User logged in successfully',
            'user' => $user,
            'token' => $token
        ], 200);
    }
}
