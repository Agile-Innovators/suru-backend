<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\City;


//Importar log y validator
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $properties = Property::select(
            'properties.id',
            'properties.title',
            'properties.description',
            'properties.price',
            'properties.availability_date',
            'properties.size_in_m2',
            'properties.bedrooms',
            'properties.bathrooms',
            'properties.floors',
            'properties.pools',
            'properties.pets_allowed',
            'properties.green_area',
            'property_categories.name as property_category',
            'property_transaction_types.name as property_transaction',
            'cities.name as city',
            'regions.name as region',
            'currencies.code as currency_code',
            'properties.user_id',
        )
            ->join('property_categories', 'property_categories.id', '=', 'properties.property_category_id')
            ->join('property_transaction_types', 'property_transaction_types.id', '=', 'properties.property_transaction_type_id')
            ->join('cities', 'cities.id', '=', 'properties.city_id')
            ->join('regions', 'regions.id', '=', 'cities.region_id')
            ->join('currencies', 'currencies.id', '=', 'properties.currency_id')
            ->get();

        foreach ($properties as $property) {
            $property->images = $property->propertyImages()->get();
        }


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
        //sometimes> permite que el valor sea opcional
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
            'currency_id' => 'required|exists:currencies,id',
            'user_id' => 'required|exists:users,id',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'utilities' => 'sometimes|array',
            'utilities.*' => 'integer|exists:utilities,id',
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
                'currency_id' => $validateData['currency_id'],
                'user_id' => $validateData['user_id'],
            ]);

            if (isset($validateData['utilities'])) {
                $property->utilities()->attach($validateData['utilities']);
            }

            if ($request->hasFile('images')) {

                foreach ($request->file('images') as $image) {

                    $extension = $image->getClientOriginalExtension();

                    $file_name = 'property' . $property->id . '_image' . uniqid() . '.' . $extension;

                    // Save images in storage
                    $path = $image->storeAs('public/images/properties', $file_name);

                    PropertyImage::create([
                        'property_id' => $property->id,
                        'image_name' => $file_name,
                    ]);

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
        $property = Property::select(
            'properties.id',
            'properties.title',
            'properties.description',
            'properties.price',
            'properties.availability_date',
            'properties.size_in_m2',
            'properties.bedrooms',
            'properties.bathrooms',
            'properties.floors',
            'properties.pools',
            'properties.pets_allowed',
            'properties.green_area',
            'property_categories.name as property_category',
            'property_transaction_types.name as property_transaction',
            'cities.name as city',
            'regions.name as region',
            'currencies.code as currency_code',
            'properties.user_id',
        )
            ->join('property_categories', 'property_categories.id', '=', 'properties.property_category_id')
            ->join('property_transaction_types', 'property_transaction_types.id', '=', 'properties.property_transaction_type_id')
            ->join('cities', 'cities.id', '=', 'properties.city_id')
            ->join('regions', 'regions.id', '=', 'cities.region_id')
            ->join('currencies', 'currencies.id', '=', 'properties.currency_id')
            ->where('properties.id', $id)
            ->first();

        $property->images = $property->propertyImages()->get();
        $property->utilities = $property->utilities()->get();

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
            'currency_id' => 'exists:currencies,id',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'existing_images_id' => 'array',
            'existing_images_id.*' => 'exists:property_images,id',
            'utilities' => 'sometimes|array',
            'utilities.*' => 'integer|exists:utilities,id',
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

        $existingImages = $property->propertyImages->pluck('id')->toArray();

        $imagesToKeep = $request->input('existing_images_id', []);

        //array_diff() devuelve los valores que estan presentes en el primer array pero no los que estan en el segundo
        $imagesToDelete = array_diff($existingImages, $imagesToKeep);

        foreach ($imagesToDelete as $imageId) {
            $image = PropertyImage::findOrFail($imageId);

            Storage::disk('public')->delete('images/properties/' . $image->image_name);

            $image->delete();
        }


        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {

                $extension = $image->getClientOriginalExtension();

                $file_name = 'property' . $property->id . '_image' . uniqid() . '.' . $extension;

                $path = $image->storeAs('public/images/properties', $file_name);

                Log::debug('Image path: ' . $path . ' - File name: ' . $file_name);

                PropertyImage::create([
                    'property_id' => $property->id,
                    'image_name' => $file_name,
                ]);
            }
        }

        if (isset($validatedData['utilities'])) {
            // sync() = permite añadir nuevas utilidades y elimina las que no estan en el array
            $property->utilities()->sync($validatedData['utilities']);
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
            ], 404);
        }

        $property->delete();

        return response()->json([
            'message' => 'Property deleted successfully',
        ], 200);
    }

    public function getUserProperties(string $id)
    {

        $property = Property::select(
            'properties.id',
            'properties.title',
            'properties.description',
            'properties.price',
            'properties.availability_date',
            'properties.size_in_m2',
            'properties.bedrooms',
            'properties.bathrooms',
            'properties.floors',
            'properties.pools',
            'properties.pets_allowed',
            'properties.green_area',
            'property_categories.name as property_category',
            'property_transaction_types.name as property_transaction',
            'cities.name as city',
            'regions.name as region',
            'currencies.code as currency_code',
        )
            ->join('property_categories', 'property_categories.id', '=', 'properties.property_category_id')
            ->join('property_transaction_types', 'property_transaction_types.id', '=', 'properties.property_transaction_type_id')
            ->join('cities', 'cities.id', '=', 'properties.city_id')
            ->join('regions', 'regions.id', '=', 'cities.region_id')
            ->join('currencies', 'currencies.id', '=', 'properties.currency_id')
            ->where('user_id', $id)
            ->get();

        if ($property->isEmpty()) {
            return response()->json([
                'message' => 'There are no properties'
            ], 404);
        }
        return response()->json($property);
    }

    public function filterProperty(Request $request)
    {

        $regionId = $request->query('regionId');
        $minPrice = $request->query('minPrice');
        $maxPrice = $request->query('maxPrice');
        $propertyCategoryId = $request->query('propertyCategoryId');

        if ($minPrice && $maxPrice && $minPrice > $maxPrice) {
            return response()->json(['error' => 'El precio mínimo no puede ser mayor que el precio máximo'], 400);
        }

        // $query = Property::query();

        $query = Property::query()
            ->join('property_categories', 'property_categories.id', '=', 'properties.property_category_id')
            ->join('property_transaction_types', 'property_transaction_types.id', '=', 'properties.property_transaction_type_id')
            ->join('cities', 'cities.id', '=', 'properties.city_id')
            ->join('regions', 'regions.id', '=', 'cities.region_id')
            ->join('currencies', 'currencies.id', '=', 'properties.currency_id')
            ->select(
                'properties.id',
                'properties.title',
                'properties.description',
                'properties.price',
                'properties.availability_date',
                'properties.size_in_m2',
                'properties.bedrooms',
                'properties.bathrooms',
                'properties.floors',
                'properties.pools',
                'properties.pets_allowed',
                'properties.green_area',
                'property_categories.name as property_category',
                'property_transaction_types.name as property_transaction',
                'cities.name as city',
                'regions.name as region',
                'currencies.code as currency_code',
                'properties.user_id',
            );

        if ($propertyCategoryId) {
            $query->where('property_category_id', $propertyCategoryId);
        }

        if ($regionId) {
            //pluck() = obtiene un valor especifico o una lista de valores
            $citiesId = City::where('region_id', $regionId)->pluck('id');
            $query->whereIn('city_id', $citiesId);
        }

        if ($minPrice) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }



        $properties = $query->get();

        foreach ($properties as $property) {
            $property->images = $property->propertyImages()->get();
            $property->utilities = $property->utilities()->get();
        }

        return response()->json($properties);
    }
}
