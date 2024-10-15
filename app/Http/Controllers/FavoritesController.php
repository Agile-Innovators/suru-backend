<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
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

    public function getFavoritesUser(string $userId){
        $favorites = Favorite::where('user_id', $userId)
        ->with('property')
        ->get()
        ->pluck('property');

        return response()->json($favorites);
    }

    public function addFavoriteProperty(Request $request){

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

    public function removeFavoriteProperty(Request $request){
        $userId = $request->input('user_id');
        $propertyId = $request->input('property_id');

        Favorite::where('user_id', $userId)
        ->where('property_id', $propertyId)
        ->delete();

        return response()->json([
            'message' => 'Propiedad eliminada de favoritos con éxito.'
        ], 200); 
    }
}
