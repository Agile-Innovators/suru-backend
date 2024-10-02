<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Region;
use App\Models\Country;
use App\Models\User;
use App\Models\UserLocation;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    public function getAllLocations()
    {
        $cities = City::with('region', 'region.country')->get();

        $locations = $cities->map(function ($city) {
            $region = $city->region->name;
            $country = $city->region->country->iso;
            $locationName = "{$city->name}, {$region}. {$country}";

            return [
                'name' => $locationName,
                'value' => $city->id,
            ];
        });

        return response()->json($locations);
    }

    public function associateUserWithLocation(Request $request)
    {
        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'city_id' => 'required|exists:cities,id',
            'address' => $user->user_type_id == 3 ? 'string|required' : 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }   

        $city = City::find($request->city_id);

        if (!$city) {
            return response()->json([
                'message' => 'City not found',
            ], 404);
        }

        $userLocation = UserLocation::create([
            'user_id' => $user->id,
            'city_id' => $city->id,
        ]);

        if ($user->user_type_id == 3) {
            $userLocation->address = $request->address;
            $userLocation->save();
        }

        return response()->json([
            'message' => 'User location updated successfully',
        ]);
    }

    public function unlinkUserLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'city_id' => 'required|exists:cities,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::find($request->user_id);

        $userLocation = UserLocation::where('user_id', $user->id)
            ->where('city_id', $request->city)
            ->first();

        if ($userLocation) {
            $userLocation->delete();
        }

        return response()->json([
            'message' => 'User location unlinked successfully',
        ]);
    }
    

}
