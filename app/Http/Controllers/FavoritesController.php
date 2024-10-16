<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Property;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getFavoritesUser(string $userId)
    {
        // $favorites = Favorite::where('user_id', $userId)
        //     ->with('property')
        //     ->get()
        //     ->pluck('property');


        $favoritesProperties = Property::select(
            'properties.id',
            'properties.title',
            'properties.description',
            'properties.price',
            'properties.rent_price',
            'properties.deposit_price',
            'properties.availability_date',
            'properties.size_in_m2',
            'properties.bedrooms',
            'properties.bathrooms',
            'properties.floors',
            'properties.garages',
            'properties.pools',
            'properties.pets_allowed',
            'properties.green_area',
            'property_categories.name as property_category',
            'property_transaction_types.name as property_transaction',
            'cities.name as city',
            'regions.name as region',
            'currencies.code as currency_code',
            'payment_frequencies.name as payment_frequency',
            'properties.user_id',
        )
            ->leftJoin('property_categories', 'property_categories.id', '=', 'properties.property_category_id')
            ->leftJoin('property_transaction_types', 'property_transaction_types.id', '=', 'properties.property_transaction_type_id')
            ->leftJoin('cities', 'cities.id', '=', 'properties.city_id')
            ->leftJoin('regions', 'regions.id', '=', 'cities.region_id')
            ->leftJoin('currencies', 'currencies.id', '=', 'properties.currency_id')
            ->leftJoin('payment_frequencies', 'payment_frequencies.id', '=', 'properties.payment_frequency_id')
            ->leftJoin('favorites', 'favorites.property_id', '=', 'properties.id')
            ->where('favorites.user_id', $userId)
            ->orderBy('properties.id', 'desc')
            ->get();

        return response()->json($favoritesProperties);
    }

    public function getFavoritesUserIds(string $userId)
    {
        $favoritesIds = Favorite::where('user_id', $userId)
            ->with('property')
            ->get()
            ->pluck('property.id');

        return response()->json($favoritesIds);
    }

    public function addFavoriteProperty(Request $request)
    {

        $userId = $request->input('user_id');
        $propertyId = $request->input('property_id');

        Favorite::create([
            'user_id' => $userId,
            'property_id' => $propertyId,
        ]);

        return response()->json([
            'message' => 'Propiedad añadida a favoritos con exito'
        ], 201);
    }

    public function removeFavoriteProperty(Request $request)
    {
        $userId = $request->input('user_id');
        $propertyId = $request->input('property_id');

        $deleted = Favorite::where('user_id', $userId)
            ->where('property_id', $propertyId)
            ->delete();

        if ($deleted) {
            return response()->json([
                'message' => 'Propiedad eliminada de favoritos con éxito.'
            ], 200);
        } else {
            return response()->json([
                'message' => 'No se encontró la propiedad en favoritos.'
            ], 404);
        }
    }
}
