<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Property;
use App\Models\PropertyImage;
use Validator;
use Log;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $properties = Property::with('propertyImages')->get();

        return response()->json($properties);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    //test method
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'availability_date' => 'required|date',
            'size_in_m2' => 'required|numeric',
            'bedrooms' => 'required|numeric',
            'bathrooms' => 'required|numeric',
            'floors' => 'required|numeric',
            'garages' => 'required|numeric',
            'pools' => 'required|numeric',
            'pets_allowed' => 'required|boolean',
            'green_area' => 'required|boolean',
            'property_category_id' => 'required|exists:property_categories,id',
            'property_transaction_type_id' => 'required|exists:property_transaction_types,id',
            'city_id' => 'required|exists:cities,id',
            'user_id' => 'required|exists:users,id',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',  // Valida múltiples imágenes
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors occurred',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validateData = $validator->validated();

        try {
            $property = Property::create([
                'title' => $validateData['title'],
                'description' => $validateData['description'],
                'price' => $validateData['price'],
                'availability_date' => $validateData['availability_date'],
                'size_in_m2' => $validateData['size_in_m2'],
                'bedrooms' => $validateData['bedrooms'],
                'bathrooms' => $validateData['bathrooms'],
                'floors' => $validateData['floors'],
                'garages' => $validateData['garages'],
                'pools' => $validateData['pools'],
                'pets_allowed' => $validateData['pets_allowed'],
                'green_area' => $validateData['green_area'],
                'property_category_id' => $validateData['property_category_id'],
                'property_transaction_type_id' => $validateData['property_transaction_type_id'],
                'city_id' => $validateData['city_id'],
                'user_id' => $validateData['user_id'],
            ]);

            if ($request->hasFile('images')) {
                $imageCounter = 1; 
            
                foreach ($request->file('images') as $image) {

                    $extension = $image->getClientOriginalExtension();
            
                    $file_name = 'property_' . $property->id . '_image' . $imageCounter .  '.' . $extension;
            
                    // Guardar la imagen en la carpeta 'public/images' usando storeAs()
                    $path = $image->storeAs('public/images/properties', $file_name); 
            
                    PropertyImage::create([
                        'property_id' => $property->id,
                        'image_path' => $path,  
                    ]);
            
                    $imageCounter++;
                }
            }

            return response()->json([
                'message' => 'Property created successfully',
                'property' => $property,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error creating property',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $property = Property::with('propertyImages')->find($id);

        if (!$property) {
            return response()->json([
                'message' => 'Property not found'
            ], 404);
        }

        return response()->json($property);
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

        return response()->json($request->all());

        $validator = Validator::make($request->all(), [
            'title' => 'string',
            'description' => 'string',
            'price' => 'numeric',
            'availability_date' => 'date',
            'size_in_m2' => 'numeric',
            'bedrooms' => 'numeric',
            'bathrooms' => 'numeric',
            'floors' => 'numeric',
            'garages' => 'numeric',
            'pools' => 'numeric',
            'pets_allowed' => 'boolean',
            'green_area' => 'boolean',
            'property_category_id' => 'exists:property_categories,id',
            'property_transaction_type_id' => 'exists:property_transaction_types,id',
            'city_id' => 'exists:cities,id',
            'user_id' => 'exists:users,id',
            // 'images' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors ocurred',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validatedData = $validator->validated();

        $property = Property::find($id);

        if (!$property) {
            return response()->json([
                'message' => 'Property not found'
            ], 404);
        }

        $property->update($validatedData);
        $property->refresh();

        return response()->json([
            'message' => 'Property updated successfully',
            'property' => $property,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json([
                'message' => 'Property not found',
            ], 404); // Código de estado 404 Not Found
        }

        $property->delete();

        return response()->json([
            'message' => 'Property deleted successfully',
        ], 200); // Código de estado 200 OK
    }
}
