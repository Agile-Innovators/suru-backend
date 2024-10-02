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

    
}
