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
            'partner_categories.name',
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
            'partner_categories.name',
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

}
