<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Mail\Welcome;
use App\Mail\Resend;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Services\UserService;

//JWT
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

//Models
use App\Models\UserProfile;
use App\Models\PartnerProfile;
use App\Models\User;


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
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'user_type_id' => 'required|integer|in:2,3', // 2 = Regular user, 3 = Partner

            // Conditional validations
            'lastname1' => 'nullable',
            'lastname2' => 'nullable',
            
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
            ], 401);
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
