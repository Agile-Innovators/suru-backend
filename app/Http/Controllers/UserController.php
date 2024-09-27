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
                'user_type_id' => UserType::where('name', 'user')->first()->id,
                'profile_picture' => 'default.jpg',
            ]);

            $user->save(); 
            $user_id = $user->id;

            $userProfile = new UserProfile([
                'user_id' => $user_id,
            ]);

            $userProfile->save(); 
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

    /**
     * Register a new partner
     */
    public function registerPartner(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone_number' => 'required|string',
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
                'user_type_id' => UserType::where('name', 'partner')->first()->id,
                'profile_picture' => 'default.jpg',
                'phone_number' => $request->phone_number,
            ]);

            $user->save(); 
            $user_id = $user->id;

            $partnerProfile = new PartnerProfile([
                'user_id' => $user_id,
                'description' => $request->description,
                'website_url' => $request->website_url,
                'partner_category_id' => $request->partner_category_id,
            ]);

            $partnerProfile->save(); 
            $token = $user->createToken('Personal Access Token')->plainTextToken;

            return response()->json([
                'message' => 'Partner created successfully',
                'user' => $user,
                'token' => $token
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating partner',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
