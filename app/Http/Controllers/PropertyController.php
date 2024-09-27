<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Property;
use App\Models\PropertyImage;
use Validator;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Recuperar todas las propiedades
        $properties = Property::all(); // O usa paginate() para paginación

        // Devolver las propiedades en formato JSON
        return response()->json($properties);
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
        
        // return $request;

        if ($request->hasFile('images')) {
            return response()->json(['message' => 'Images received', 'files' => $request->file('images')], 200);
        } else {
            return response()->json(['message' => 'No images found'], 400);
        }

        // dd($request->all(), $request->file('images'));
        


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
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors ocurred',
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
                foreach ($request->file('images') as $image) {
                    // Generar un nombre único para cada archivo
                    $file_name = 'property_' . uniqid() . '.' . $image->getClientOriginalExtension();

                    // Guardar la imagen en la carpeta 'public/images'
                    $path = $image->storeAs('public/images', $file_name);

                    // Crear el registro de la imagen en la tabla 'property_images'
                    PropertyImage::create([
                        'property_id' => $property->id,
                        'image_path' => $path, // Guardar la ruta completa
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
}
