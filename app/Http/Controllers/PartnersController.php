<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocationController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

use App\Models\PartnerCategory;
use App\Models\PartnerService;
use App\Models\PartnerProfile;
use App\Models\User;
use App\Models\BusinessService;
use App\Models\UserLocation;
use App\Models\PartnerRequest;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Services\UserService;

use App\Mail\SendPartnerCredentials;

class PartnersController extends Controller
{
    protected $userService;
    protected $authController;
    protected $locationController;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->authController = new AuthController($userService);
        $this->locationController = new LocationController();
    }

    public function getPartnersCategories()
    {
        $categories = PartnerCategory::select(
            'partner_categories.id',
            'partner_categories.name'
        )
            ->get();

        // Obtener la cantidad de partners por categoría
        foreach ($categories as $category) {
            $category->partner_count = PartnerProfile::where('partner_category_id', $category->id)->count();
        }


        if ($categories->isEmpty()) {
            return response()->json(['message' => 'No categories found'], 404);
        }

        return response()->json($categories);
    }

    public function index()
    {
        $partners = PartnerProfile::select(
            'partner_profiles.description',
            'partner_profiles.user_id',
            'partner_categories.name as category_name',
            'users.name as partner_name',
            'users.image_url as image',
        )
            ->join('users', 'partner_profiles.user_id', '=', 'users.id')
            ->join('partner_categories', 'partner_profiles.partner_category_id', '=', 'partner_categories.id')
            ->get();

        if ($partners->isEmpty()) {
            return response()->json(['message' => 'No partners found'], 404);
        }

        $partnerServices = PartnerService::select(
            'partner_services.partner_id',
            'partner_services.id',
            'business_services.name',
        )
            ->join('business_services', 'business_services.id', '=', 'partner_services.business_service_id')
            ->get()
            ->groupBy('partner_id');

        $partners->transform(function ($partner) use ($partnerServices) {
            // Añadir los servicios correspondientes al perfil actual
            $partner->services = $partnerServices->get($partner->user_id, collect());
            return $partner;
        });


        // $partners->services = $partnerServices;

        return response()->json($partners);
    }

    public function getPartnersByCategory(string $category_id)
    {


        if ($category_id != 0) {
            $category = PartnerCategory::find($category_id);

            if (!$category) {
                return response()->json(['message' => 'Category not found'], 404);
            }
        }

        $partners = PartnerProfile::select(
            'partner_profiles.description',
            'partner_profiles.user_id',
            'partner_categories.name as category_name',
            'users.name as partner_name',
            'users.image_url as image'
        )
            ->join('users', 'partner_profiles.user_id', '=', 'users.id')
            ->join('partner_categories', 'partner_profiles.partner_category_id', '=', 'partner_categories.id')
            ->when($category_id != 0, function ($query) use ($category_id) {
                return $query->where('partner_profiles.partner_category_id', $category_id);
            })
            ->get();

        if ($partners->isEmpty()) {
            return response()->json([]);
        }

        $partnerServices = PartnerService::select(
            'partner_services.partner_id',
            'partner_services.id',
            'business_services.name',
        )
            ->join('business_services', 'business_services.id', '=', 'partner_services.business_service_id')
            ->get()
            ->groupBy('partner_id');

        $partners->transform(function ($partner) use ($partnerServices) {
            // Añadir los servicios correspondientes al perfil actual
            $partner->services = $partnerServices->get($partner->user_id, collect());
            return $partner;
        });

        return response()->json($partners);
    }

    public function show(string $user_id)
    {
        $partner = User::select(
            'users.name',
            'users.email',
            'users.phone_number',
            'users.image_url',
            'partner_profiles.description',
            'partner_profiles.website_url',
            'partner_profiles.instagram_url',
            'partner_profiles.facebook_url',
            'partner_profiles.tiktok_url',
            'partner_profiles.currency_id',
            'partner_categories.name as category_name',
        )
            ->join('partner_profiles', 'partner_profiles.user_id', '=', 'users.id')
            ->join('partner_categories', 'partner_profiles.partner_category_id', '=', 'partner_categories.id')
            ->where('users.id', $user_id)
            ->first();

        if (!$partner) {
            return response()->json(['message' => 'Partner not found'], 404);
        }

        $locations = UserLocation::with('city.region.country')
            ->where('user_id', $user_id)
            ->get()
            ->map(function ($userLocation) {
                $city = $userLocation->city;
                $region = $city->region;
                $country = $region->country;
                $locationName = "{$city->name}, {$region->name}. {$country->iso}";
                $address = $userLocation->address;

                return [
                    'name' => $locationName,
                    'address' => $address,
                    'city_id' => $city->id,
                ];
            });

        $partner->locations = $locations;

        $partner->operational_hours = $this->userService->showOperationalHours($user_id);

        $partnerServices = PartnerService::select(
            'partner_services.id',
            'partner_services.price',
            'partner_services.price_max',
            'business_services.name',
            'business_services.description',

        )
            ->join('business_services', 'business_services.id', '=', 'partner_services.business_service_id')
            ->where('partner_id', $user_id)
            ->get();

        $services = [];
        foreach ($partnerServices as $service) {
            $services[] = $service;
        }

        $partner->services = $services;

        return response()->json($partner);
    }

    public function getPartnerServices(string $user_id)
    {
        $partner = PartnerProfile::where('user_id', $user_id)->first();

        if (!$partner) {
            return response()->json(['message' => 'Partner not found'], 404);
        }

        $services = PartnerService::select(
            'partner_services.price',
            'partner_services.price_max',
            'business_services.name',
            'business_services.description'
        )
            ->join('business_services', 'partner_services.business_service_id', '=', 'business_services.id')
            ->where('partner_services.partner_id', $partner->user_id)
            ->get();

        if ($services->isEmpty()) {
            return response()->json(['message' => 'No services found for this partner'], 404);
        }

        return response()->json($services, 201);
    }

    public function updatePartnerServices(Request $request, int $userId)
    {
        $validator = Validator::make($request->all(), [
            'services' => 'required|array',
            'services.*.id' => 'required|exists:business_services,id',
            'services.*.price' => 'required|numeric',
            'services.*.price_max' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $partnerProfile = PartnerProfile::where('user_id', $userId)->first();

        if (!$partnerProfile) {
            return response()->json(['message' => 'Partner not found'], 404);
        }

        $existingServices = PartnerService::where('partner_id', $partnerProfile->user_id)->get();
        $serviceIdsFromRequest = collect($request->services)->pluck('id')->toArray();

        foreach ($request->services as $serviceData) {
            $existingService = $existingServices->where('business_service_id', $serviceData['id'])->first();

            // If the services does not exist, create it. Otherwise, update it.
            if (!$existingService) {
                PartnerService::create([
                    'partner_id' => $partnerProfile->user_id,
                    'business_service_id' => $serviceData['id'],
                    'price' => $serviceData['price'],
                    'price_max' => $serviceData['price_max'],
                ]);
            } else {
                $existingService->update([
                    'price' => $serviceData['price'],
                    'price_max' => $serviceData['price_max'],
                ]);
            }
        }

        // Delete services that are not in the request
        foreach ($existingServices as $existingService) {
            if (!in_array($existingService->business_service_id, $serviceIdsFromRequest)) {
                $existingService->delete();
            }
        }
        return response()->json(['message' => 'Services updated successfully'], 201);
    }

    public function addBusinessService(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'partner_category_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $businessService = BusinessService::create([
            'name' => $request->name,
            'description' => $request->description,
            'partner_category_id' => $request->partner_category_id,
        ]);

        return response()->json($businessService);
    }

    public function changeCurrency(Request $request, int $partnerId)
    {
        // Validar que se pase el nuevo currency_id y que tenga un valor de 1 o 2
        $validator = Validator::make($request->all(), [
            'currency_id' => 'required|in:1,2',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $newCurrencyId = $request->currency_id;

        // Obtener el partner profile
        $partnerProfile = PartnerProfile::where('user_id', $partnerId)->first();

        if (!$partnerProfile) {
            return response()->json(['message' => 'Partner profile not found'], 404);
        }

        // Obtener el tipo de cambio de la API
        $response = Http::get('https://apis.gometa.org/tdc/tdc.json');

        if ($response->failed()) {
            return response()->json(['message' => 'Unable to fetch exchange rate'], 500);
        }

        $data = $response->json();
        $usdToCrcRate = $data['venta']; // Tasa de cambio de USD a CRC
        $crcToUsdRate = 1 / $usdToCrcRate; // Tasa de cambio de CRC a USD

        // Cambiar el currency_id
        $partnerProfile->currency_id = $newCurrencyId;
        $partnerProfile->save();

        // Actualizar precios de los partner services
        if ($newCurrencyId == 2) { // De USD a CRC
            PartnerService::where('partner_id', $partnerId)->each(function ($service) use ($usdToCrcRate) {
                $service->price *= $usdToCrcRate;
                if ($service->price_max) {
                    $service->price_max *= $usdToCrcRate;
                }
                $service->save();
            });
        } elseif ($newCurrencyId == 1) { // De CRC a USD
            PartnerService::where('partner_id', $partnerId)->each(function ($service) use ($crcToUsdRate) {
                $service->price *= $crcToUsdRate;
                if ($service->price_max) {
                    $service->price_max *= $crcToUsdRate;
                }
                $service->save();
            });
        }

        return response()->json(['message' => 'Currency updated and prices converted successfully'], 200);
    }

    public function getPartnersByStatus(string $status, int $userId)
    {
        // Verifica que el usuario sea un admin
        $user = User::find($userId);

        if ($user == null) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if (!$user || $user->user_type_id != 1) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make(['status' => $status], [
            'status' => 'required|in:Pending,Approved,Rejected',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $partnerRequests = PartnerRequest::where('status', $status)->get();

        if ($partnerRequests->isEmpty()) {
            return response()->json(['message' => 'No partner requests found'], 404);
        }

        // Reemplazar el public_id por la URL
        $partnerRequests->transform(function ($request) {
            $request->image = cloudinary()->getUrl($request->image_public_id);
            unset($request->image_public_id);
            return $request;
        });

        // Cambiar el id de la ciudad por el nombre
        $partnerRequests->transform(function ($request) {
            $location = $this->locationController->getOneLocation($request->city_id);

            if ($location->getStatusCode() === 200) {
                $locationData = $location->getData();
                $request->location_name = $locationData->name;
            } else {
                $request->location_name = 'Unknown Location';
            }
            return $request;
        });

        return response()->json($partnerRequests);
    }

    public function getPartnerRequest(int $partnerRequestId, int $userId)
    {
        $user = User::find($userId);

        if ($user == null) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if (!$user || $user->user_type_id != 1) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $partnerRequest = PartnerRequest::find($partnerRequestId);

        if (!$partnerRequest) {
            return response()->json(['message' => 'Partner request not found'], 404);
        }

        $partnerRequest->image = cloudinary()->getUrl($partnerRequest->image_public_id);
        unset($partnerRequest->image_public_id);

        $location = $this->locationController->getOneLocation($partnerRequest->city_id);
        if ($location->getStatusCode() === 200) {
            $locationData = $location->getData();
            $partnerRequest->location_name = $locationData->name;
        } else {
            $partnerRequest->location_name = 'Unknown Location';
        }

        return response()->json($partnerRequest);
    }

    public function storePartnerRequest(Request $request)
    {
        if (
            PartnerRequest::where('email', $request->email)->orWhere('phone_number', $request->phone_number)->exists() ||
            User::where('email', $request->email)->orWhere('phone_number', $request->phone_number)->exists()
        ) {
            return response()->json(['message' => 'A partner request or user already exists with this email or phone number, please be patient or contact support if you think this is an error'], 409);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|unique:users,phone_number',
            //'image' => 'required|image |mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'description' => 'required|string',

            'website_url' => 'nullable|string',
            'instagram_url' => 'nullable|string',
            'facebook_url' => 'nullable|string',
            'tiktok_url' => 'nullable|string',
            'currency_id' => 'required|exists:currencies,id',
            'city_id' => 'required|exists:cities,id',
            'address' => 'nullable|string|max:255',
            'partner_category_id' => 'required|exists:partner_categories,id',
            'partner_comments' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $partnerRequest = PartnerRequest::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'description' => $request->description,
            'website_url' => $request->website_url,
            'instagram_url' => $request->instagram_url,
            'facebook_url' => $request->facebook_url,
            'tiktok_url' => $request->tiktok_url,
            'currency_id' => $request->currency_id,
            'city_id' => $request->city_id,
            'address' => $request->address,
            'partner_category_id' => $request->partner_category_id,
        ]);

        // Upload the image if it exists
        if ($request->hasFile('image')) {
            $uploadedImage = Cloudinary::upload($request->image->getRealPath(), [
                'folder' => 'users'
            ]);

            if ($uploadedImage) {
                $publicId = $uploadedImage->getPublicId();
                $partnerRequest->update(['image_public_id' => $publicId]);
            }
        }

        return response()->json(['message' => 'Partner request created successfully'], 201);
    }

    public function respondPartnerRequest(Request $request, int $partnerRequestId, int $userId)
    {
        // Verify that the user is an admin
        $user = User::find($userId);

        if ($user == null) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if (!$user || $user->user_type_id != 1) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Verify that the partner request exists
        $partnerRequest = PartnerRequest::find($partnerRequestId);
        if (!$partnerRequest) {
            return response()->json(['message' => 'Partner request not found'], 404);
        }

        // Verify that the request has not been reviewed yet
        if ($partnerRequest->reviewd_at != null) {
            return response()->json(['message' => 'Partner request already reviewed'], 400);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Approved,Rejected',
            'admin_comments' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $partnerRequest->update([
            'status' => $request->status,
            'admin_id' => $userId,
            'admin_comments' => $request->admin_comments,
            'reviewd_at' => now(),
        ]);

        if ($request->status == 'Approved') {
            $password = bin2hex(random_bytes(8));
            $username = strtolower(str_replace(' ', '', $partnerRequest->name));

            while (User::where('username', $username)->exists()) {
                $username .= rand(1, 100);
            }

            $newRequest = [
                'username' => $username,
                'password' => $password,
                'email' => $partnerRequest->email,
                'name' => $partnerRequest->name,
                'phone_number' => $partnerRequest->phone_number,
                'description' => $partnerRequest->description,
                'website_url' => $partnerRequest->website_url,
                'facebook_url' => $partnerRequest->facebook_url,
                'instagram_url' => $partnerRequest->instagram_url,
                'tiktok_url' => $partnerRequest->tiktok_url,
                'currency_id' => $partnerRequest->currency_id,
                'city_id' => $partnerRequest->city_id,
                'address' => $partnerRequest->address,
                'partner_category_id' => $partnerRequest->partner_category_id,
                'image_public_id' => $partnerRequest->image_public_id,
                'user_type_id' => 3,
            ];

            // Send an email with the credentials to the partner
            Mail::to($partnerRequest->email)->send(new SendPartnerCredentials($partnerRequest->name, $username, $password));

            return $this->authController->register(new Request($newRequest));
        }

        return response()->json(['message' => 'Partner request updated successfully'], 201);
    }
}
