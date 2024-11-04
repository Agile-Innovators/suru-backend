<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Services\UserService;

//JWT
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

//Models
use App\Models\UserProfile;
use App\Models\PartnerProfile;
use App\Models\User;
use App\Models\UserLocation;

class AuthController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Register a user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => $request->user_type_id != 3 ? 'required|string|unique:users,username' : 'nullable|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|max:128',
            'user_type_id' => 'required|integer|in:1,2,3', // 1= Admin, 2 = Regular user, 3 = Partner

            // Conditional validations for regular users
            'lastname1' => 'nullable',
            'lastname2' => 'nullable',

            // Conditional validations for partners
            'name' => $request->user_type_id == 3 ? 'required|string' : 'nullable|max:255',
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
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'user_type_id' => $request->user_type_id,
                'name' => $request->user_type_id == 3 ? $request->name : null,
                'phone_number' => $request->user_type_id == 3 ? $request->phone_number : null,
            ]);

            if (($request->user_type_id == 1) || ($request->user_type_id == 2)) {
                UserProfile::create(['user_id' => $user->id]);
            } else if ($request->user_type_id == 3) {
                PartnerProfile::create([
                    'user_id' => $user->id,
                    'description' => $request->description,
                    'website_url' => $request->website_url,
                    'instagram_url' => $request->instagram_url,
                    'facebook_url' => $request->facebook_url,
                    'tiktok_url' => $request->tiktok_url,
                    'currency_id' => $request->currency_id,
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
                
                if ($request->image_public_id) {
                    $user->update([
                        'image_public_id' => $request->image_public_id,
                        'image_url' => cloudinary()->getUrl($request->image_public_id),
                    ]);
                }

                if ($request->city_id) {
                    UserLocation::create([
                        'user_id' => $user->id,
                        'city_id' => $request->city_id,
                        'address' => $request->address,
                    ]);
                }
            }

            // Create default operational hours
            $this->userService->createUserOperationalHours($user->id);
            $user->load('userType');
            $token = JWTAuth::fromUser($user);

            $userData = [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
                'email' => $user->email,
                'image_url' => $user->image_url ?? "https://res.cloudinary.com/dvwtm566p/image/upload/v1728158504/users/dc8aagfamyqwaspllhz8.jpg",
                'user_type' => $user->userType->name,
            ];

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

        // Intenta autenticar al usuario con JWT
        if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 404);
        }

        // Recupera el usuario autenticado
        $user = auth()->user();

        // Verifica que el usuario exista
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $userType = $user->userType->name;

        return response()->json([
            'message' => 'User logged in successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'user_type' => $userType,
                'image_url' => $user->image_url,
            ],
            'token' => $token
        ], 200);
    }

    /**
     * Logout the user
     */
    public function logout(Request $request)
    {
        try {
            // Invalidate the token
            JWTAuth::invalidate($request->token);

            return response()->json([
                'message' => 'User logged out successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error logging out user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
