<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\City;

//Importar log y validator
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

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
            'properties.rent_price',
            'properties.deposit_price',
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
            'payment_frequencies.name as payment_frequency',
            'properties.user_id',
        )
            ->leftJoin('property_categories', 'property_categories.id', '=', 'properties.property_category_id')
            ->leftJoin('property_transaction_types', 'property_transaction_types.id', '=', 'properties.property_transaction_type_id')
            ->leftJoin('cities', 'cities.id', '=', 'properties.city_id')
            ->leftJoin('regions', 'regions.id', '=', 'cities.region_id')
            ->leftJoin('currencies', 'currencies.id', '=', 'properties.currency_id')
            ->leftJoin('payment_frequencies', 'payment_frequencies.id', '=', 'properties.payment_frequency_id')
            ->orderBy('properties.id', 'desc')
            ->get();


        foreach ($properties as $property) {
            $property->images = $property->propertyImages()->get();
        }

        if ($properties->isEmpty()) {
            return response()->json([
                'message' => 'There are no properties'
            ], 404);
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
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'price' => 'nullable|numeric',
            'rent_price' => 'nullable|numeric',
            'deposit_price' => 'nullable|numeric',
            'availability_date' => 'required|date',
            'size_in_m2' => 'required|numeric',
            'bedrooms' => 'nullable|numeric',
            'bathrooms' => 'nullable|numeric',
            'floors' => 'nullable|numeric',
            'garages' => 'nullable|numeric',
            'pools' => 'nullable|numeric',
            'pets_allowed' => 'boolean',
            'green_area' => 'boolean',
            'property_category_id' => 'required|exists:property_categories,id',
            'property_transaction_type_id' => 'required|exists:property_transaction_types,id',
            'city_id' => 'required|exists:cities,id',
            'payment_frequency_id' => 'nullable|exists:payment_frequencies,id',
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

        if (in_array($validateData['property_category_id'], [4, 5])) {

            unset($validateData['pets_allowed']);
            unset($validateData['green_area']);

            if (isset($validateData['utilities'])) {
                $validateData['utilities'] = array_filter($validateData['utilities'], function ($utilityId) {
                    return $utilityId !== 3;
                });
            }
        }

        try {
            $property = Property::create([
                'title' => $validateData['title'],
                'description' => $validateData['description'],
                'price' => $validateData['price'] ?? null,
                'rent_price' => $validateData['rent_price'] ?? null,
                'deposit_price' => $validateData['deposit_price'] ?? null,
                'availability_date' => $validateData['availability_date'],
                'size_in_m2' => $validateData['size_in_m2'],
                'bedrooms' => $validateData['bedrooms'] ?? null,
                'bathrooms' => $validateData['bathrooms'] ?? null,
                'floors' => $validateData['floors'] ?? null,
                'garages' => $validateData['garages'] ?? null,
                'pools' => $validateData['pools'] ?? null,
                'pets_allowed' => $validateData['pets_allowed'] ?? null,
                'green_area' => $validateData['green_area'] ?? null,
                'property_category_id' => $validateData['property_category_id'],
                'property_transaction_type_id' => $validateData['property_transaction_type_id'],
                'city_id' => $validateData['city_id'],
                'payment_frequency_id' => $validateData['payment_frequency_id'] ?? null,
                'currency_id' => $validateData['currency_id'],
                'user_id' => $validateData['user_id'],
            ]);

            if (isset($validateData['utilities'])) {
                $property->utilities()->attach($validateData['utilities']);
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    try {
                        $uploadedImage = Cloudinary::upload(
                            $image->getRealPath(),
                            ['folder' => 'properties']
                        );

                        if ($uploadedImage) {
                            $publicId = $uploadedImage->getPublicId();
                            $url = cloudinary()->getUrl($publicId);

                            PropertyImage::create([
                                'property_id' => $property->id,
                                'url' => $url,
                                'public_id' => $publicId,
                            ]);
                        } else {
                            Log::error('Error al subir imagen', ['image' => $image]);
                            return response()->json(['message' => 'Error uploading image'], 422);
                        }
                    } catch (\Exception $e) {
                        Log::error('Error al procesar imagen', ['error' => $e->getMessage()]);
                        return response()->json(['error' => 'Error uploading image', 'message' => $e->getMessage()], 500);
                    }
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
            'properties.rent_price',
            'properties.deposit_price',
            'properties.availability_date',
            'properties.size_in_m2',
            'properties.bedrooms',
            'properties.bathrooms',
            'properties.floors',
            'properties.pools',
            'properties.garages',
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
            ->where('properties.id', $id)
            ->first();

        if (!$property) {
            return response()->json([
                'message' => 'Property not found'
            ], 404);
        }

        $property->utilities = $property->utilities()->get();

        $property->images = $property->propertyImages->map(function ($image) {
            return [
                'url' => Cloudinary::getUrl($image->public_id),
                'id' => $image->id
            ];
        });

        $property->makeHidden('propertyImages');

        return response()->json([
            'property' => $property
        ]);
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
            'title' => 'required|string',
            'description' => 'required|string',
            'price' => 'nullable|numeric',
            'rent_price' => 'nullable|numeric',
            'deposit_price' => 'nullable|numeric',
            'availability_date' => 'required|date',
            'size_in_m2' => 'required|numeric',
            'bedrooms' => 'nullable|numeric',
            'bathrooms' => 'nullable|numeric',
            'floors' => 'nullable|numeric',
            'garages' => 'nullable|numeric',
            'pools' => 'nullable|numeric',
            'pets_allowed' => 'boolean',
            'green_area' => 'boolean',
            'property_category_id' => 'required|exists:property_categories,id',
            'property_transaction_type_id' => 'required|exists:property_transaction_types,id',
            'city_id' => 'required|exists:cities,id',
            'payment_frequency_id' => 'nullable|exists:payment_frequencies,id',
            'currency_id' => 'required|exists:currencies,id',
            'user_id' => 'required|exists:users,id',
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

        // Delete unused images
        $existingImages = $property->propertyImages->pluck('id')->toArray();
        $imagesToKeep = $request->input('existing_images_id', []);
        $imagesToDelete = array_diff($existingImages, $imagesToKeep);

        foreach ($imagesToDelete as $imageId) {
            $image = PropertyImage::findOrFail($imageId);
            Cloudinary::destroy($image->public_id); // Deleting images through its public_id
            $image->delete();
        }

        // Upload new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $uploadedImage = Cloudinary::upload($image->getRealPath(), [
                    'folder' => 'properties'
                ]);
                $publicId = $uploadedImage->getPublicId();

                PropertyImage::create([
                    'property_id' => $property->id,
                    'url' => Cloudinary::getUrl($publicId),
                    'public_id' => $publicId,
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
        $property = Property::with('propertyImages')->find($id);

        if (!$property) {
            return response()->json([
                'message' => 'Property not found',
            ], 404);
        }

        try {
            foreach ($property->propertyImages as $propertyImage) {
                Cloudinary::destroy($propertyImage->public_id);
                $propertyImage->delete();
            }

            $property->delete();

            return response()->json([
                'message' => 'Property deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error deleting property',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getUserProperties(string $id)
    {

        $properties = Property::select(
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
            'properties.pools',
            'properties.pets_allowed',
            'properties.green_area',
            'property_categories.name as property_category',
            'property_transaction_types.name as property_transaction',
            'cities.name as city',
            'regions.name as region',
            'currencies.code as currency_code',
            'payment_frequencies.name as payment_frequency'
        )
            ->leftJoin('property_categories', 'property_categories.id', '=', 'properties.property_category_id')
            ->leftJoin('property_transaction_types', 'property_transaction_types.id', '=', 'properties.property_transaction_type_id')
            ->leftJoin('cities', 'cities.id', '=', 'properties.city_id')
            ->leftJoin('regions', 'regions.id', '=', 'cities.region_id')
            ->leftJoin('payment_frequencies', 'payment_frequencies.id', '=', 'properties.payment_frequency_id')
            ->leftJoin('currencies', 'currencies.id', '=', 'properties.currency_id')
            ->where('properties.user_id', $id)
            ->orderBy('properties.id', 'desc')
            ->get();

        if ($properties->isEmpty()) {
            return response()->json([
                'message' => 'There are no properties'
            ], 404);
        }
        return response()->json($properties);
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
            ->join('payment_frequencies', 'payment_frequencies.id', '=', 'properties.payment_frequency_id')
            ->select(
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
                'properties.pools',
                'properties.garages',
                'properties.pets_allowed',
                'properties.green_area',
                'property_categories.name as property_category',
                'property_transaction_types.name as property_transaction',
                'cities.name as city',
                'regions.name as region',
                'currencies.code as currency_code',
                'payment_frequencies.name as payment_frequency',
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
            // Obtain property images and generate their cloudinary URLs
            $property->images = $property->propertyImages()->get()->map(function ($image) {
                return [
                    'public_id' => $image->public_id,
                    'url' => Cloudinary::url($image->public_id),
                ];
            });

            // Obtain property utilities
            $property->utilities = $property->utilities()->get();
        }

        return response()->json($properties);
    }
}
