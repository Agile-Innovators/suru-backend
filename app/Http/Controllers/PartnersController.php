<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

class PartnersController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function getPartnersCategories()
    {
        $categories = PartnerCategory::select(
            'partner_categories.id',
            'partner_categories.name'
        )
            ->get();

        if($categories->isEmpty()) {
            return response()->json(['message' => 'No categories found'], 404);
        }

        return response()->json($categories);
    }

    public function index()
    {
        $partners = PartnerProfile::select(
            'partner_profiles.description',
            'partner_categories.name as category_name',
            'users.name as partner_name',
            'users.image_url as image'
        )
            ->join('users', 'partner_profiles.user_id', '=', 'users.id')
            ->join('partner_categories', 'partner_profiles.partner_category_id', '=', 'partner_categories.id')
            ->get();

        if($partners->isEmpty()) {
            return response()->json(['message' => 'No partners found'], 404);
        }

        return response()->json($partners);
    }

    public function getPartnersByCategory(string $category_id)
    {
        $category = PartnerCategory::find($category_id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $partners = PartnerProfile::select(
            'partner_profiles.description',
            'partner_categories.name as category_name',
            'users.name as partner_name',
            'users.image_url as image'
        )
            ->join('users', 'partner_profiles.user_id', '=', 'users.id')
            ->join('partner_categories', 'partner_profiles.partner_category_id', '=', 'partner_categories.id')
            ->where('partner_profiles.partner_category_id', $category_id)
            ->get();

        if($partners->isEmpty()) {
            return response()->json(['message' => 'No partners found for this category'], 404);
        }

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
            'partner_categories.name as category_name'
        )
            ->join('partner_profiles', 'users.id', '=', 'partner_profiles.user_id')
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

        if($services->isEmpty()) {
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

    // This method is used to receive the partner request and process it 
    public function storePartnerRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string',
            'phone_number' => 'required|string|unique:users,phone_number',
            //'image' => 'required|image |mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'description' => 'required|string',

            'website_url' => 'nullable|string',
            'instagram_url' => 'nullable|string',
            'facebook_url' => 'nullable|string',
            'tiktok_url' => 'nullable|string',
            'currency_id' => 'required|exists:currencies,id',
            'partner_category_id' => 'required|exists:partner_categories,id',
            'partner_comments' => 'nullable|string',
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



}
