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
            'properties.garages',
            'properties.pools',
            'properties.pets_allowed',
            'properties.green_area',
            'property_categories.name as property_category',
            'property_categories.id as property_category_id',
            'property_transaction_types.name as property_transaction',
            'property_transaction_types.id as property_transaction_id',
            'cities.name as city',
            'cities.id as city_id',
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
            $property->utilities = $property->utilities()->get();
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
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
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
            'properties.payment_frequency_id',
            'property_categories.name as property_category',
            'property_categories.id as property_category_id',
            'property_transaction_types.id as property_transaction_id',
            'property_transaction_types.name as property_transaction',
            'property_transaction_types.id as property_transaction_id',
            'cities.name as city',
            'cities.id as city_id',
            'regions.name as region',
            'currencies.code as currency_code',
            'payment_frequencies.name as payment_frequency',
            'properties.user_id',
            'users.name as owner_name',
            'users.phone_number as owner_phone',
        )
            ->leftJoin('property_categories', 'property_categories.id', '=', 'properties.property_category_id')
            ->leftJoin('property_transaction_types', 'property_transaction_types.id', '=', 'properties.property_transaction_type_id')
            ->leftJoin('cities', 'cities.id', '=', 'properties.city_id')
            ->leftJoin('regions', 'regions.id', '=', 'cities.region_id')
            ->leftJoin('currencies', 'currencies.id', '=', 'properties.currency_id')
            ->leftJoin('payment_frequencies', 'payment_frequencies.id', '=', 'properties.payment_frequency_id')
            ->leftJoin('users', 'users.id', '=', 'properties.user_id')
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
     * Show 3 properties in the same city or region as the property with the given id
     */
    public function showRelatedProperties(string $property_id)
    {
        $property = Property::find($property_id);

        if (!$property) {
            return response()->json([
                'message' => 'Property not found'
            ], 404);
        }

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
            'properties.garages',
            'properties.pools',
            'properties.pets_allowed',
            'properties.green_area',
            'property_categories.name as property_category',
            'property_categories.id as property_category_id',
            'property_transaction_types.name as property_transaction',
            'property_transaction_types.id as property_transaction_id',
            'cities.name as city',
            'cities.id as city_id',
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
            ->where('properties.id', '!=', $property_id)
            ->orderByRaw('CASE WHEN cities.id = ? THEN 0 WHEN regions.id = ? THEN 1 WHEN regions.country_id = ? THEN 2 ELSE 3 END', [$property->city_id, $property->city->region_id, $property->city->region->country_id])
            ->take(3)
            ->get();

        foreach ($properties as $property) {
            $property->image = $property->propertyImages()->value('url');
            $property->utilities = $property->utilities()->get();
        }

        if ($properties->isEmpty()) {
            return response()->json([
                'message' => 'There are no related properties'
            ], 404);
        }

        return response()->json($properties);
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
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
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
                    'url' => cloudinary()->getUrl($publicId),
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
            'properties.garages',
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
        $USDtoCRC = 515;
        $CRCtoUSD = 0.0019;

        // return "local";

        //attributes always present
        $propertyCategoryId = $request->query('propertyCategoryId');
        $propertyTransactionId = $request->query('propertyTransactionId');
        $regionId = $request->query('regionId');
        $currencyId = $request->query('currencyId');
        $size = $request->query('size_in_m2');

        $minPrice = $request->query('minPrice');
        $maxPrice = $request->query('maxPrice');

        if (isset($propertyTransactionId)) {
            // if ($propertyTransactionId == 1 || $propertyTransactionId == 3) { //sale and dual transaction
            //     $minPrice = $request->query('minPrice');
            //     $maxPrice = $request->query('maxPrice');
            // }
            // return "entro aqui";
            if ($propertyTransactionId == 2 || $propertyTransactionId == 3) { // rent and dual transaction

                $depositPrice = $request->query('depositPrice');
                $rentPrice = $request->query('rentPrice');
            }
        }

        //property categories (House, apartment, studio)
        if ($propertyCategoryId == 1 || $propertyCategoryId == 2 || $propertyCategoryId == 5) {
            $qtyBedrooms = $request->query('qtyBedrooms');
            $qtyBathrooms = $request->query('qtyBathrooms');
            $qtyFloors = $request->query('qtyFloors');
            $qtyPools = $request->query('qtyPools');
            $qtyGarages = $request->query('qtyGarages');
        }

        //property category retail space
        if ($propertyCategoryId == 4) {
            $qtyBathrooms = $request->query('qtyBathrooms');
        }

        $petsAllowed = $request->query('allow_pets');
        $greenArea = $request->query('green_area');
        $utilities = $request->query('utilities');

        if ($maxPrice !== "max" && $minPrice > $maxPrice) {
            return response()->json(['error' => 'El precio mínimo no puede ser mayor que el precio máximo'], 400);
        }

        $query = Property::query()
            ->leftJoin('property_categories', 'property_categories.id', '=', 'properties.property_category_id')
            ->leftJoin('property_transaction_types', 'property_transaction_types.id', '=', 'properties.property_transaction_type_id')
            ->leftJoin('cities', 'cities.id', '=', 'properties.city_id')
            ->leftJoin('regions', 'regions.id', '=', 'cities.region_id')
            ->leftJoin('currencies', 'currencies.id', '=', 'properties.currency_id')
            ->leftJoin('payment_frequencies', 'payment_frequencies.id', '=', 'properties.payment_frequency_id')
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

        //filtrar por categoria, si es 0 no seleccionar una en especifico
        if ($propertyCategoryId != 0) {
            $query->where('property_category_id', $propertyCategoryId);
        }

        //filtrar por transaccion, si es 3 no seleccionar una en especifico
        if (isset($propertyTransactionId) && $propertyTransactionId != 3) {
            $query->where('property_transaction_type_id', $propertyTransactionId);
        }

        //filtrar por region, si es 0 no seleccionar una en especifico
        if ($regionId != 0) {
            //pluck() = obtiene un valor especifico o una lista de valores
            $citiesId = City::where('region_id', $regionId)->pluck('id');
            $query->whereIn('city_id', $citiesId);
        }

        if (isset($petsAllowed)) {
            $query->where(function ($q) use ($petsAllowed) {
                $q->where('pets_allowed', $petsAllowed)
                    ->orWhereNull('pets_allowed');
            });
        }

        if (isset($greenArea)) {
            $query->where(function ($q) use ($greenArea) {
                $q->where('green_area', $greenArea)
                    ->orWhereNull('green_area');
            });
        }

        if (isset($qtyBedrooms)) {
            $query->where(function ($q) use ($qtyBedrooms) {
                $q->where('bedrooms', '>=', $qtyBedrooms)
                    ->orWhereNull('bedrooms');
            });
        }

        if (isset($qtyBathrooms)) {
            $query->where(function ($q) use ($qtyBathrooms) {
                $q->where('bathrooms', '>=', $qtyBathrooms)
                    ->orWhereNull('bathrooms');
            });
        }

        if (isset($qtyFloors)) {
            $query->where(function ($q) use ($qtyFloors) {
                $q->where('floors', '>=', $qtyFloors)
                    ->orWhereNull('floors');
            });
        }

        if (isset($qtyPools)) {
            $query->where(function ($q) use ($qtyPools) {
                $q->where('pools', '>=', $qtyPools)
                    ->orWhereNull('pools');
            });
        }

        if (isset($qtyGarages)) {
            $query->where(function ($q) use ($qtyGarages) {
                $q->where('garages', '>=', $qtyGarages)
                    ->orWhereNull('garages');
            });
        }

        if (isset($size)) {
            $query->where('size_in_m2', '>=', $size);
        }

        if (isset($currencyId)) {

            // verify if minPrice and maxPrice exists (property transaction SALE)
            if (isset($minPrice) && isset($maxPrice)) {

                // Filter by USD (currencyId = 1)
                $query->when($currencyId == 1, function ($q) use ($CRCtoUSD, $minPrice, $maxPrice, $propertyTransactionId) {

                    // filter by minPrice and maxPrice if transaction is sell or dual (1 o 3)
                    if ($propertyTransactionId == 1 || $propertyTransactionId == 3) {
                        $q->whereRaw("
                            IFNULL(IF(properties.currency_id = 2, properties.price * ?, properties.price), 0) >= ?
                        ", [$CRCtoUSD, $minPrice]);

                        if ($maxPrice !== "max") {
                            $q->whereRaw("
                                IFNULL(IF(properties.currency_id = 2, properties.price * ?, properties.price), 0) <= ?
                            ", [$CRCtoUSD, $maxPrice]);
                        }
                    }
                });

                // Filter by CRC (currencyId = 2)
                $query->when($currencyId == 2, function ($q) use ($USDtoCRC, $minPrice, $maxPrice, $propertyTransactionId) {
                    // filter by minPrice and maxPrice if transaction is sell or dual (1 o 3)
                    if ($propertyTransactionId == 1 || $propertyTransactionId == 3) {
                        $q->whereRaw("
                            IFNULL(IF(properties.currency_id = 1, properties.price * ?, properties.price), 0) >= ?
                        ", [$USDtoCRC, $minPrice]);

                        if ($maxPrice !== "max") {
                            $q->whereRaw("
                                IFNULL(IF(properties.currency_id = 1, properties.price * ?, properties.price), 0) <= ?
                            ", [$USDtoCRC, $maxPrice]);
                        }
                    }
                });
            }

            // verify if depositPrice and rentPrice exists (property transaction RENT)
            if (isset($depositPrice) && isset($rentPrice)) {

                // Filter by USD (currencyId = 1)
                $query->when($currencyId == 1, function ($q) use ($CRCtoUSD, $depositPrice, $rentPrice, $propertyTransactionId) {

                    // filter by rentPrice and depositPrice if transaction is rent or dual (2 o 3)
                    if ($propertyTransactionId == 2 || $propertyTransactionId == 3) {
                        $q->whereRaw("
                            IFNULL(IF(properties.currency_id = 2, properties.rent_price * ?, properties.rent_price), 0) >= ?
                        ", [$CRCtoUSD, $rentPrice]);

                        $q->whereRaw("
                            IFNULL(IF(properties.currency_id = 2, properties.deposit_price * ?, properties.deposit_price), 0) >= ?
                        ", [$CRCtoUSD, $depositPrice]);
                    }
                });

                // Filter by CRC (currencyId = 2)
                $query->when($currencyId == 2, function ($q) use ($USDtoCRC, $depositPrice, $rentPrice, $propertyTransactionId) {

                    // filter by rentPrice and depositPrice if transaction is rent or dual (2 o 3)
                    if ($propertyTransactionId == 2 || $propertyTransactionId == 3) {
                        $q->whereRaw("
                            IFNULL(IF(properties.currency_id = 1, properties.rent_price * ?, properties.rent_price), 0) >= ?
                        ", [$USDtoCRC, $rentPrice]);

                        $q->whereRaw("
                            IFNULL(IF(properties.currency_id = 1, properties.deposit_price * ?, properties.deposit_price), 0) >= ?
                        ", [$USDtoCRC, $depositPrice]);
                    }
                });
            }
        }
        
        $properties = $query->get();

        //filter by utilities
        if (isset($utilities) && count($utilities) > 0) {
            $properties = $properties->filter(function ($property) use ($utilities) {
                // get ID´s utilities from property 
                $propertyUtilitiesIds = $property->utilities()
                    ->select('utilities.id')
                    ->pluck('utilities.id')
                    ->toArray();

                // Verificar si todas las utilidades enviadas en la request están presentes en la propiedad.
                return empty(array_diff($utilities, $propertyUtilitiesIds));
            });
        }

        foreach ($properties as $property) {
            // Obtain property images and generate their cloudinary URLs
            $property->images = $property->propertyImages()->get()->map(function ($image) {
                return [
                    'public_id' => $image->public_id,
                    // 'url' => Cloudinary::url($image->public_id),
                    'url' => cloudinary()->getUrl($image->public_id),
                ];
            });
            $property->utilities = $property->utilities()->get();
        }

        return response()->json($properties);
    }
}
