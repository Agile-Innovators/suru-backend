<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Region;
use App\Models\Country;

class LocationController extends Controller
{
    public function getAllLocations()
    {
        $cities = City::with('region', 'region.country')->get();

        $locations = $cities->map(function ($city) {
            $region = $city->region->name;
            $country = $city->region->country->iso;

            $locationName = "{$city->name}, {$region}. {$country}";

            $value = "{$city->id}_{$city->region->id}_{$city->region->country->id}";

            return [
                'name' => $locationName,
                'value' => $value,
            ];
        });

        return response()->json($locations);
    }
}
