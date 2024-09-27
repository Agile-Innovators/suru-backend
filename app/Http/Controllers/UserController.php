<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserType;
use App\Models\UserProfile;
use App\Models\PartnerProfile;
use Illuminate\Support\Facades\Validator;

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
        //
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
     * Register a new normal user
     */
    public function registerUser(Request $request)
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

            $token = $user->createToken('Personal Access Token')->plainTextToken;

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
}
