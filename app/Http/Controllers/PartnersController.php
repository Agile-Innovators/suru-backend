<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PartnerCategory;
use App\Models\PartnerService;
use App\Models\PartnerProfile;
use App\Models\User;
use App\Models\BusinessService;

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

    
}
