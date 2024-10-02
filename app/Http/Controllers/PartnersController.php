<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PartnerCategory;
use App\Models\PartnerService;
use App\Models\PartnerProfile;
use App\Models\User;
use App\Models\BusinessService;
use Illuminate\Support\Facades\Validator;

class PartnersController extends Controller
{
    public function getPartnersCategories()
    {
        $categories = PartnerCategory::select(
            'partner_categories.id',
            'partner_categories.name'
        )
            ->get();

        return response()->json($categories);
    }

    public function getAllPartners()
    {
        $partners = PartnerProfile::select(
            'partner_profiles.description',
            'partner_categories.name as category_name',
            'users.name as partner_name',
            'users.profile_picture as image'
        )
            ->join('users', 'partner_profiles.user_id', '=', 'users.id')
            ->join('partner_categories', 'partner_profiles.partner_category_id', '=', 'partner_categories.id')
            ->get();

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
            'users.profile_picture as image'
        )
            ->join('users', 'partner_profiles.user_id', '=', 'users.id')
            ->join('partner_categories', 'partner_profiles.partner_category_id', '=', 'partner_categories.id')
            ->where('partner_profiles.partner_category_id', $category_id)
            ->get();

        return response()->json($partners);
    }

    public function getPartnerById(string $id)
    {
        $partner = PartnerProfile::select(
            'partner_profiles.description',
            'partner_profiles.website_url',
            'partner_categories.name',
            'users.name',
            'users.email',
            'users.phone_number',
            'users.profile_picture as image',
            'partner_categories.name as category'
        )
            ->join('users', 'partner_profiles.user_id', '=', 'users.id')
            ->join('partner_categories', 'partner_profiles.partner_category_id', '=', 'partner_categories.id')
            ->where('partner_profiles.id', $id)
            ->first();

        if (!$partner) {
            return response()->json(['message' => 'Partner not found'], 404);
        }

        return response()->json($partner);
    }

    public function getPartnerServices(string $id)
    {
        $partner = PartnerProfile::find($id);

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

        return response()->json($services);
    }

    public function addServicesToPartner(Request $request, int $userId)
    {
        $request->validate([
            'services' => 'required|array',
            'services.*.id' => 'required|exists:business_services,id',
            'services.*.price' => 'required|numeric',
            'services.*.price_max' => 'nullable|numeric',
        ]);

        $partnerProfile = PartnerProfile::where('user_id', $userId)->first();

        if (!$partnerProfile) {
            return response()->json(['message' => 'Partner profile not found'], 404);
        }

        foreach ($request->services as $serviceData) {
            $existingService = PartnerService::where('partner_id', $partnerProfile->user_id)
                ->where('business_service_id', $serviceData['id'])
                ->first();

            // If the service doesn't exist, create it. Otherwise, update it.
            if (!$existingService) {
                PartnerService::create([
                    'partner_id' => $partnerProfile->user_id,
                    'business_service_id' => $serviceData['id'],
                    'price' => $serviceData['price'],
                    'price_max' => $serviceData['price_max'],
                ]);
            }else{
                $existingService->update([
                    'price' => $serviceData['price'],
                    'price_max' => $serviceData['price_max'],
                ]);
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

        return response()->json($businessService, 201);
    }
}
